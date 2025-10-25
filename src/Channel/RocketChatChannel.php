<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Http\Client;
use Cake\Notification\AnonymousNotifiable;
use Cake\Notification\Channel\ChannelInterface;
use Cake\Notification\Exception\CouldNotSendNotification;
use Cake\Notification\Notification;
use Cake\RocketChatNotification\Message\RocketChatMessage;
use Exception;

/**
 * RocketChat Channel
 *
 * Sends notifications to RocketChat via incoming webhooks.
 */
class RocketChatChannel implements ChannelInterface
{
    /**
     * HTTP client
     *
     * @var \Cake\Http\Client
     */
    protected Client $client;

    /**
     * Channel configuration
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * Constructor
     *
     * @param array<string, mixed> $config Channel configuration
     * @param \Cake\Http\Client|null $client Optional HTTP client for testing
     */
    public function __construct(array $config = [], ?Client $client = null)
    {
        $this->config = $config + [
            'timeout' => 30,
        ];

        if (empty($this->config['webhook'])) {
            throw CouldNotSendNotification::missingCredentials('rocketchat', 'webhook');
        }

        $this->client = $client ?? new Client([
            'timeout' => $this->config['timeout'],
        ]);
    }

    /**
     * Send the notification
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Notification\AnonymousNotifiable $notifiable The notifiable entity
     * @param \Cake\Notification\Notification $notification The notification to send
     * @return mixed Response from RocketChat API
     */
    public function send(EntityInterface|AnonymousNotifiable $notifiable, Notification $notification): mixed
    {
        $message = $notification->toRocketChat($notifiable);

        if ($message === null) {
            return null;
        }

        $payload = $this->buildPayload($message, $notifiable, $notification);

        try {
            $response = $this->client->post($this->config['webhook'], json_encode($payload), [
                'type' => 'json',
            ]);

            if (!$response->isOk()) {
                throw CouldNotSendNotification::serviceRespondedWithError(
                    'rocketchat',
                    $response->getStringBody(),
                    "RocketChat API returned error: HTTP {$response->getStatusCode()}",
                );
            }

            return $response->getJson();
        } catch (CouldNotSendNotification $e) {
            throw $e;
        } catch (Exception $e) {
            throw CouldNotSendNotification::serviceRespondedWithError(
                'rocketchat',
                $e->getMessage(),
                "Failed to send RocketChat notification: {$e->getMessage()}",
            );
        }
    }

    /**
     * Build the notification payload
     *
     * @param \Cake\RocketChatNotification\Message\RocketChatMessage|array<string, mixed>|string $message Message data
     * @param \Cake\Datasource\EntityInterface|\Cake\Notification\AnonymousNotifiable $notifiable Notifiable entity
     * @param \Cake\Notification\Notification $notification Notification instance
     * @return array<string, mixed>
     */
    protected function buildPayload(
        RocketChatMessage|array|string $message,
        EntityInterface|AnonymousNotifiable $notifiable,
        Notification $notification,
    ): array {
        if (is_string($message)) {
            $payload = ['text' => $message];
        } elseif (is_array($message)) {
            $payload = $message;
        } else {
            $payload = $message->toArray();
        }

        $channel = $this->getChannel($notifiable, $notification);
        if ($channel !== null) {
            $payload['channel'] = $channel;
        }

        return $payload;
    }

    /**
     * Get the channel/recipient for the notification
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Notification\AnonymousNotifiable $notifiable Notifiable entity
     * @param \Cake\Notification\Notification $notification Notification instance
     * @return string|null
     */
    protected function getChannel(
        EntityInterface|AnonymousNotifiable $notifiable,
        Notification $notification,
    ): ?string {
        if ($notifiable instanceof AnonymousNotifiable) {
            return $notifiable->routeNotificationFor('rocketchat', $notification);
        }

        if (method_exists($notifiable, 'routeNotificationForRocketchat')) {
            return $notifiable->routeNotificationForRocketchat($notification);
        }

        if (isset($notifiable->rocketchat_channel)) {
            return $notifiable->rocketchat_channel;
        }

        return null;
    }
}
