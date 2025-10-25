<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification\Test\TestCase\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Notification\AnonymousNotifiable;
use Cake\Notification\Notification;

/**
 * Test Notification with string message
 */
class TestStringNotification extends Notification
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
    public function toRocketChat(EntityInterface|AnonymousNotifiable $notifiable): string
    {
        return 'Simple string message';
    }
}
