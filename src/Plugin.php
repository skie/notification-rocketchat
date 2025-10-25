<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\RocketChatNotification\Provider\RocketChatChannelProvider;

/**
 * RocketChat Plugin for Notification
 */
class Plugin extends BasePlugin
{
    /**
     * Load all the plugin configuration and bootstrap logic.
     *
     * The host application is provided as an argument. This allows you to load
     * additional plugin dependencies, or attach events.
     *
     * @param \Cake\Core\PluginApplicationInterface<\Cake\Core\BasePlugin> $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        EventManager::instance()->on(
            'Notification.Registry.discover',
            function ($event): void {
                $registry = $event->getSubject();
                (new RocketChatChannelProvider())->register($registry);
            },
        );
    }
}
