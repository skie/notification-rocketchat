<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification\Message;

use Closure;

/**
 * RocketChat Message
 *
 * Fluent API for building RocketChat webhook messages.
 *
 * Example:
 * ```
 * RocketChatMessage::create()
 *     ->text('Hello from CakePHP!')
 *     ->emoji(':wave:')
 *     ->attachment(function ($attachment) {
 *         $attachment->title('Deployment Complete')
 *             ->text('Version 2.1.0 deployed successfully')
 *             ->color('good')
 *             ->field('Environment', 'Production', true);
 *     });
 * ```
 */
class RocketChatMessage
{
    /**
     * Message text
     *
     * @var string
     */
    protected string $text = '';

    /**
     * Username to display
     *
     * @var string|null
     */
    protected ?string $username = null;

    /**
     * Avatar URL
     *
     * @var string|null
     */
    protected ?string $avatar = null;

    /**
     * Emoji to use
     *
     * @var string|null
     */
    protected ?string $emoji = null;

    /**
     * Message attachments
     *
     * @var array<\Cake\RocketChatNotification\Message\RocketChatAttachment>
     */
    protected array $attachments = [];

    /**
     * Create new message instance
     *
     * @return static
     */
    public static function create(): static
    {
        return new static(); // @phpstan-ignore-line
    }

    /**
     * Set message text
     *
     * @param string $text Message text
     * @return static
     */
    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Set username to display
     *
     * @param string $username Username
     * @return static
     */
    public function username(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set avatar URL
     *
     * @param string $url Avatar URL
     * @return static
     */
    public function avatar(string $url): static
    {
        $this->avatar = $url;

        return $this;
    }

    /**
     * Set emoji
     *
     * @param string $emoji Emoji code (e.g., ':smile:')
     * @return static
     */
    public function emoji(string $emoji): static
    {
        $this->emoji = $emoji;

        return $this;
    }

    /**
     * Add attachment
     *
     * @param \Closure|array<string, mixed> $callback Closure to configure attachment or array of attachment data
     * @return static
     */
    public function attachment(Closure|array $callback): static
    {
        if (is_array($callback)) {
            $this->attachments[] = new RocketChatAttachment($callback);
        } else {
            $attachment = new RocketChatAttachment();
            $callback($attachment);
            $this->attachments[] = $attachment;
        }

        return $this;
    }

    /**
     * Convert message to array for webhook
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'text' => $this->text,
        ];

        if ($this->username !== null) {
            $data['username'] = $this->username;
        }

        if ($this->avatar !== null) {
            $data['avatar'] = $this->avatar;
        }

        if ($this->emoji !== null) {
            $data['emoji'] = $this->emoji;
        }

        if (!empty($this->attachments)) {
            $data['attachments'] = array_map(
                fn($attachment) => $attachment->toArray(),
                $this->attachments,
            );
        }

        return $data;
    }

    /**
     * Get message text
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get username
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Get avatar URL
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Get emoji
     *
     * @return string|null
     */
    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    /**
     * Get attachments
     *
     * @return array<\Cake\RocketChatNotification\Message\RocketChatAttachment>
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
