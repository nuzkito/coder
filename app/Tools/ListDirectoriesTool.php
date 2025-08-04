<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;

final class ListDirectoriesTool implements ToolInterface
{
    public string $name = 'list_directories';

    public string $description = 'List directories of the filesystem.';

    public array $schema = [
        'type' => 'object',
        'properties' => [
            'path' => [
                'type' => 'string',
                'description' => 'Relative pathname of the directory to read. If path is not defined, it will use the actual working directory.',
            ],
        ],
    ];

    public function execute(array $arguments): ToolResult
    {
        $result = collect(Storage::directories($arguments['path'] ?? null))->implode("\n");

        return new ToolResult(
            content: $result,
            description: "Listed directories in `{$arguments['path']}`.",
        );
    }
}
