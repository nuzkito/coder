<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class EditFileTool implements ToolInterface
{
    public string $name = 'edit_file';

    public string $description = 'Edit the content of a file. Try to make partial updates, avoid editing the whole file at once.';

    public array $schema = [
        'type' => 'object',
        'properties' => [
            'path' => [
                'type' => 'string',
                'description' => 'Relative pathname of the file to edit. Can replace a fragment of text, without the need to update all the content.',
            ],
            'old_content' => [
                'type' => 'string',
                'description' => 'Text to search in the file. This text will be replaced with the new content.',
            ],
            'new_content' => [
                'type' => 'string',
                'description' => 'Text to replace with the old content.',
            ],
        ],
        'required' => ['path', 'old_content', 'new_content'],
    ];

    public function execute(array $arguments): ToolResult
    {
        $oldContent = Storage::get($arguments['path']);
        $newContent = Str::of($oldContent)->replace($arguments['old_content'], $arguments['new_content']);

        $success = Storage::put($arguments['path'], $newContent);

        return new ToolResult(
            content: $success ? "File at {$arguments['path']} has been updated." : "There was a problem editing {$arguments['path']}.",
            description: $success ? "Updated file `{$arguments['path']}`." : "Error updating file `{$arguments['path']}`.",
        );
    }
}
