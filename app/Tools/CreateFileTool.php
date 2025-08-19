<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Prism\Prism\Tool;

final class CreateFileTool extends Tool
{
    public function __construct()
    {
        $this->as('create_file')
            ->for('Creates a file in the user\'s filesystem')
            ->withStringParameter(
                name: 'path',
                description: 'Relative pathname of the file to create.',
                required: true,
            )
            ->withStringParameter(
                name: 'content',
                description: 'The content of the file.',
                required: true,
            )
            ->using($this);
    }

    public function __invoke(string $path, string $content): string
    {
        $success = Storage::put($path, $content);

        return json_encode([
            'success' => $success,
        ]);
    }
}
