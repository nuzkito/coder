<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;

final class ListFilesTool implements ToolInterface
{
    public string $name = 'list_files';

    public string $description = 'List files of the filesystem.';

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
        $path = $arguments['path'] ?? './';
        $result = collect(Storage::files($path))->implode("\n");

        return new ToolResult(
            content: $result,
            description: "Listed files in `{$path}`.",
        );
    }
}
