<?php
declare(strict_types=1);

namespace Cake\RocketChatNotification\Message;

/**
 * RocketChat Attachment
 *
 * Represents a RocketChat message attachment with rich formatting.
 */
class RocketChatAttachment
{
    /**
     * Attachment data
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Constructor
     *
     * @param array<string, mixed> $data Initial attachment data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Set attachment title
     *
     * @param string $title Title text
     * @param string|null $link Optional title link
     * @return static
     */
    public function title(string $title, ?string $link = null): static
    {
        $this->data['title'] = $title;

        if ($link !== null) {
            $this->data['title_link'] = $link;
        }

        return $this;
    }

    /**
     * Set attachment text
     *
     * @param string $text Attachment text
     * @return static
     */
    public function text(string $text): static
    {
        $this->data['text'] = $text;

        return $this;
    }

    /**
     * Set attachment color
     *
     * @param string $color Color (e.g., 'good', 'warning', 'danger', or hex '#00FF00')
     * @return static
     */
    public function color(string $color): static
    {
        $this->data['color'] = $color;

        return $this;
    }

    /**
     * Add a field to the attachment
     *
     * @param string $title Field title
     * @param string $value Field value
     * @param bool $short Whether field should be short (displayed in columns)
     * @return static
     */
    public function field(string $title, string $value, bool $short = false): static
    {
        if (!isset($this->data['fields'])) {
            $this->data['fields'] = [];
        }

        $this->data['fields'][] = [
            'title' => $title,
            'value' => $value,
            'short' => $short,
        ];

        return $this;
    }

    /**
     * Set thumbnail image URL
     *
     * @param string $url Image URL
     * @return static
     */
    public function thumb(string $url): static
    {
        $this->data['thumb_url'] = $url;

        return $this;
    }

    /**
     * Set image URL
     *
     * @param string $url Image URL
     * @return static
     */
    public function image(string $url): static
    {
        $this->data['image_url'] = $url;

        return $this;
    }

    /**
     * Set author information
     *
     * @param string $name Author name
     * @param string|null $link Author link
     * @param string|null $icon Author icon URL
     * @return static
     */
    public function author(string $name, ?string $link = null, ?string $icon = null): static
    {
        $this->data['author_name'] = $name;

        if ($link !== null) {
            $this->data['author_link'] = $link;
        }

        if ($icon !== null) {
            $this->data['author_icon'] = $icon;
        }

        return $this;
    }

    /**
     * Set timestamp
     *
     * @param int $timestamp Unix timestamp
     * @return static
     */
    public function timestamp(int $timestamp): static
    {
        $this->data['ts'] = $timestamp;

        return $this;
    }

    /**
     * Convert attachment to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
