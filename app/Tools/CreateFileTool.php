<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;

final class CreateFileTool implements ToolInterface
{
    public string $name = 'create_file';

    public string $description = 'Creates a file in the user\'s filesystem.';

    public array $schema = [
        'type' => 'object',
        'properties' => [
            'path' => [
                'type' => 'string',
                'description' => 'Relative pathname of the file to create.',
            ],
            'content' => [
                'type' => 'string',
                'description' => 'The content of the file.',
            ],
        ],
        'required' => ['path', 'content'],
    ];

    public function execute(array $arguments): ToolResult
    {
        $success = Storage::put($arguments['path'], $arguments['content']);

        return new ToolResult(
            content: $success ? "File at {$arguments['path']} has been created." : "There was a problem creating {$arguments['path']}.",
            description: $success ? "Created file `{$arguments['path']}`." : "Error creating file `{$arguments['path']}`.",
        );
    }
}
