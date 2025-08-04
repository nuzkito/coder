<?php

namespace App\Tools;

interface ToolInterface
{
    public string $name { get; }

    public string $description { get; }

    public array $schema { get; }

    public function execute(array $arguments): ToolResult;
}
