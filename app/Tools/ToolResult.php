<?php

namespace App\Tools;

final readonly class ToolResult
{
    public function __construct(
        public string $content,
        public string $description,
    ) {}

    public function description(): string
    {
        return $this->description;
    }
}
