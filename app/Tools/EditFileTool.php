<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Prism\Prism\Tool;

final class EditFileTool extends Tool
{
    public function __construct()
    {
        $this->as('edit_file')
            ->for('Edit the content of a file. Try to make partial updates, avoid editing the whole file at once')
            ->withStringParameter(
                name: 'path',
                description: 'Relative pathname of the file to edit. Can replace a fragment of text, without the need to update all the content.',
                required: true,
            )
            ->withStringParameter(
                name: 'search',
                description: 'Text to search in the file. This text will be replaced with the new content.',
                required: true,
            )
            ->withStringParameter(
                name: 'replace',
                description: 'Text to replace with the old content.',
                required: true,
            )
            ->using($this);
    }

    public function __invoke(string $path, string $search, string $replace): string
    {
        $content = Storage::get($path);
        $newContent = Str::of($content)->replace($search, $replace);

        $success = Storage::put($path, $newContent);

        return json_encode([
            'success' => $success,
        ]);
    }
}
