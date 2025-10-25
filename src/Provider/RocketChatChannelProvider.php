<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification\Provider;

use Cake\Core\Configure;
use Cake\Notification\Extension\ChannelProviderInterface;
use Cake\Notification\Registry\ChannelRegistry;
use Cake\RocketChatNotification\Channel\RocketChatChannel;

/**
 * RocketChat Channel Provider
 *
 * Registers the RocketChat channel with the notification system.
 */
class RocketChatChannelProvider implements ChannelProviderInterface
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return ['rocketchat'];
    }

    /**
     * @inheritDoc
     */
    public function register(ChannelRegistry $registry): void
    {
        $config = array_merge(
            $this->getDefaultConfig(),
            (array)Configure::read('Notification.channels.rocketchat', []),
        );

        $registry->load('rocketchat', [
            'className' => RocketChatChannel::class,
        ] + $config);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultConfig(): array
    {
        $webhookUrl = getenv('ROCKETCHAT_WEBHOOK_URL');

        return [
            'webhook' => $webhookUrl !== false ? $webhookUrl : null,
            'timeout' => 30,
            'verify' => true,
        ];
    }
}
