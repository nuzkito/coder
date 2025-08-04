<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;

final class ReadFileTool implements ToolInterface
{
    public string $name = 'read_file';

    public string $description = 'Reads the content of a file.';

    public array $schema = [
        'type' => 'object',
        'properties' => [
            'path' => [
                'type' => 'string',
                'description' => 'The relative path of the file to read. The tool does not support absolute paths.',
            ],
        ],
        'required' => ['path'],
    ];

    public function execute(array $arguments): ToolResult
    {
        if (Storage::exists($arguments['path'])) {
            return new ToolResult(
                content: Storage::get($arguments['path']),
                description: "Read file `{$arguments['path']}`.",
            );
        }

        return new ToolResult(
            content: '',
            description: "File `{$arguments['path']}` not found.",
        );
    }
}
