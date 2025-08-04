<?php

namespace App\Tools;

final class GetTimeTool implements ToolInterface
{
    public string $name = 'get_time';

    public string $description = 'Get the actual time from the user\'s computer.';

    public array $schema = [];

    public function execute(array $arguments): ToolResult
    {
        $time = now()->timezone('Europe/Madrid')->format('H:i:s');

        return new ToolResult(
            content: $time,
            description: 'Getting actual time',
        );
    }
}
