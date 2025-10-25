<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification\Test\TestCase\Message;

use Cake\RocketChatNotification\Message\RocketChatMessage;
use Cake\TestSuite\TestCase;

/**
 * RocketChatMessage Test
 */
class RocketChatMessageTest extends TestCase
{
    /**
     * Test basic text message
     *
     * @return void
     */
    public function testBasicTextMessage(): void
    {
        $message = RocketChatMessage::create()
            ->text('Hello World');

        $data = $message->toArray();

        $this->assertEquals('Hello World', $data['text']);
        $this->assertArrayNotHasKey('username', $data);
        $this->assertArrayNotHasKey('emoji', $data);
    }

    /**
     * Test message with username
     *
     * @return void
     */
    public function testMessageWithUsername(): void
    {
        $message = RocketChatMessage::create()
            ->text('Hello')
            ->username('CakePHP Bot');

        $data = $message->toArray();

        $this->assertEquals('CakePHP Bot', $data['username']);
    }

    /**
     * Test message with avatar
     *
     * @return void
     */
    public function testMessageWithAvatar(): void
    {
        $message = RocketChatMessage::create()
            ->text('Hello')
            ->avatar('https://example.com/avatar.png');

        $data = $message->toArray();

        $this->assertEquals('https://example.com/avatar.png', $data['avatar']);
    }

    /**
     * Test message with emoji
     *
     * @return void
     */
    public function testMessageWithEmoji(): void
    {
        $message = RocketChatMessage::create()
            ->text('Success!')
            ->emoji(':white_check_mark:');

        $data = $message->toArray();

        $this->assertEquals(':white_check_mark:', $data['emoji']);
    }

    /**
     * Test message with attachment
     *
     * @return void
     */
    public function testMessageWithAttachment(): void
    {
        $message = RocketChatMessage::create()
            ->text('Deployment')
            ->attachment(function ($attachment): void {
                $attachment->title('Production Deploy')
                    ->text('Version 2.1.0')
                    ->color('good');
            });

        $data = $message->toArray();

        $this->assertArrayHasKey('attachments', $data);
        $this->assertCount(1, $data['attachments']);
        $this->assertEquals('Production Deploy', $data['attachments'][0]['title']);
        $this->assertEquals('Version 2.1.0', $data['attachments'][0]['text']);
        $this->assertEquals('good', $data['attachments'][0]['color']);
    }

    /**
     * Test message with multiple attachments
     *
     * @return void
     */
    public function testMessageWithMultipleAttachments(): void
    {
        $message = RocketChatMessage::create()
            ->text('Updates')
            ->attachment(function ($attachment): void {
                $attachment->title('First Update');
            })
            ->attachment(function ($attachment): void {
                $attachment->title('Second Update');
            });

        $data = $message->toArray();

        $this->assertCount(2, $data['attachments']);
        $this->assertEquals('First Update', $data['attachments'][0]['title']);
        $this->assertEquals('Second Update', $data['attachments'][1]['title']);
    }

    /**
     * Test attachment with fields
     *
     * @return void
     */
    public function testAttachmentWithFields(): void
    {
        $message = RocketChatMessage::create()
            ->text('Server Status')
            ->attachment(function ($attachment): void {
                $attachment->title('Status Report')
                    ->field('CPU', '45%', true)
                    ->field('Memory', '2.5GB', true)
                    ->field('Status', 'Healthy', false);
            });

        $data = $message->toArray();
        $fields = $data['attachments'][0]['fields'];

        $this->assertCount(3, $fields);
        $this->assertEquals('CPU', $fields[0]['title']);
        $this->assertEquals('45%', $fields[0]['value']);
        $this->assertTrue($fields[0]['short']);
        $this->assertFalse($fields[2]['short']);
    }

    /**
     * Test attachment with image
     *
     * @return void
     */
    public function testAttachmentWithImage(): void
    {
        $message = RocketChatMessage::create()
            ->text('Chart')
            ->attachment(function ($attachment): void {
                $attachment->title('Performance Graph')
                    ->image('https://example.com/chart.png');
            });

        $data = $message->toArray();

        $this->assertEquals('https://example.com/chart.png', $data['attachments'][0]['image_url']);
    }

    /**
     * Test attachment with author
     *
     * @return void
     */
    public function testAttachmentWithAuthor(): void
    {
        $message = RocketChatMessage::create()
            ->text('Update')
            ->attachment(function ($attachment): void {
                $attachment->title('Feature Release')
                    ->author('John Doe', 'https://example.com/john', 'https://example.com/avatar.jpg');
            });

        $data = $message->toArray();
        $attachment = $data['attachments'][0];

        $this->assertEquals('John Doe', $attachment['author_name']);
        $this->assertEquals('https://example.com/john', $attachment['author_link']);
        $this->assertEquals('https://example.com/avatar.jpg', $attachment['author_icon']);
    }

    /**
     * Test getters
     *
     * @return void
     */
    public function testGetters(): void
    {
        $message = RocketChatMessage::create()
            ->text('Test')
            ->username('Bot')
            ->avatar('https://example.com/avatar.png')
            ->emoji(':smile:');

        $this->assertEquals('Test', $message->getText());
        $this->assertEquals('Bot', $message->getUsername());
        $this->assertEquals('https://example.com/avatar.png', $message->getAvatar());
        $this->assertEquals(':smile:', $message->getEmoji());
        $this->assertIsArray($message->getAttachments());
    }
}
