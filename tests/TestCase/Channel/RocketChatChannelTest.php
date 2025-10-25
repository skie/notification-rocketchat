<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification\Test\TestCase\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Http\Client;
use Cake\Http\Client\Response;
use Cake\Notification\Exception\CouldNotSendNotification;
use Cake\RocketChatNotification\Channel\RocketChatChannel;
use Cake\TestSuite\TestCase;
use Exception;

/**
 * RocketChatChannel Test
 */
class RocketChatChannelTest extends TestCase
{
    /**
     * Test constructor throws exception without webhook
     *
     * @return void
     */
    public function testConstructorThrowsExceptionWithoutWebhook(): void
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('missing required credential: webhook');

        new RocketChatChannel([]);
    }

    /**
     * Test send with RocketChatMessage
     *
     * @return void
     */
    public function testSendWithRocketChatMessage(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockResponse = $this->createMock(Response::class);

        $mockResponse->method('isOk')->willReturn(true);
        $mockResponse->method('getJson')->willReturn(['success' => true]);

        $mockClient->expects($this->once())
            ->method('post')
            ->with(
                'https://example.com/webhook',
                $this->isType('string'),
                ['type' => 'json'],
            )
            ->willReturn($mockResponse);

        $channel = new RocketChatChannel([
            'webhook' => 'https://example.com/webhook',
        ], $mockClient);

        $notifiable = $this->createMock(EntityInterface::class);
        $notification = new TestRocketChatMessageNotification();

        $result = $channel->send($notifiable, $notification);

        $this->assertEquals(['success' => true], $result);
    }

    /**
     * Test send with string message
     *
     * @return void
     */
    public function testSendWithStringMessage(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockResponse = $this->createMock(Response::class);

        $mockResponse->method('isOk')->willReturn(true);
        $mockResponse->method('getJson')->willReturn(['success' => true]);

        $mockClient->expects($this->once())
            ->method('post')
            ->willReturn($mockResponse);

        $channel = new RocketChatChannel([
            'webhook' => 'https://example.com/webhook',
        ], $mockClient);

        $notifiable = $this->createMock(EntityInterface::class);
        $notification = new TestStringNotification();

        $result = $channel->send($notifiable, $notification);

        $this->assertEquals(['success' => true], $result);
    }

    /**
     * Test send with null message
     *
     * @return void
     */
    public function testSendWithNullMessage(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->never())->method('post');

        $channel = new RocketChatChannel([
            'webhook' => 'https://example.com/webhook',
        ], $mockClient);

        $notifiable = $this->createMock(EntityInterface::class);
        $notification = new TestRocketChatNotification();

        $result = $channel->send($notifiable, $notification);

        $this->assertNull($result);
    }

    /**
     * Test send throws exception on HTTP error
     *
     * @return void
     */
    public function testSendThrowsExceptionOnHttpError(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockResponse = $this->createMock(Response::class);

        $mockResponse->method('isOk')->willReturn(false);
        $mockResponse->method('getStatusCode')->willReturn(500);
        $mockResponse->method('getStringBody')->willReturn('Internal Server Error');

        $mockClient->method('post')->willReturn($mockResponse);

        $channel = new RocketChatChannel([
            'webhook' => 'https://example.com/webhook',
        ], $mockClient);

        $notifiable = $this->createMock(EntityInterface::class);
        $notification = new TestRocketChatMessageNotification();

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('HTTP 500');

        $channel->send($notifiable, $notification);
    }

    /**
     * Test send handles exception
     *
     * @return void
     */
    public function testSendHandlesException(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient->method('post')->willThrowException(new Exception('Network error'));

        $channel = new RocketChatChannel([
            'webhook' => 'https://example.com/webhook',
        ], $mockClient);

        $notifiable = $this->createMock(EntityInterface::class);
        $notification = new TestRocketChatMessageNotification();

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Network error');

        $channel->send($notifiable, $notification);
    }
}
