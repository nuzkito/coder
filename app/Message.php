<?php

namespace App;

final readonly class Message
{
    public function __construct(
        private string $role,
        private string $content,
    ) {}

    public static function fromUser(string $content): static
    {
        return new self('user', $content);
    }

    public static function fromTool(string $content): static
    {
        return new static('tool', $content);
    }

    public static function fromSystem(string $content): static
    {
        return new static('system', $content);
    }

    public static function fromAssistant(string $content): static
    {
        return new static('assistant', $content);
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'content' => $this->content,
        ];
    }
}
