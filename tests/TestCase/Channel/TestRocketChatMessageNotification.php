<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification\Test\TestCase\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Notification\AnonymousNotifiable;
use Cake\Notification\Notification;
use Cake\RocketChatNotification\Message\RocketChatMessage;

/**
 * Test Notification with RocketChatMessage
 */
class TestRocketChatMessageNotification extends Notification
{
    /**
     * @inheritDoc
     */
    public function via(EntityInterface|AnonymousNotifiable $notifiable): array
    {
        return ['rocketchat'];
    }

    /**
     * @inheritDoc
     */
    public function toRocketChat(EntityInterface|AnonymousNotifiable $notifiable): RocketChatMessage
    {
        return RocketChatMessage::create()
            ->text('Test message')
            ->emoji(':test:');
    }
}
