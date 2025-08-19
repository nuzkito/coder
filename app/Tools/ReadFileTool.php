<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Prism\Prism\Tool;

final class ReadFileTool extends Tool
{
    public function __construct()
    {
        $this->as('read_file')
            ->for('Reads the content of a file')
            ->withStringParameter(
                name: 'path',
                description: 'The relative path of the file to read. The tool does not support absolute paths.',
                required: true,
            )
            ->using($this);
    }

    public function __invoke(string $path): string
    {
        if (Storage::exists($path)) {
            return json_encode([
                'content' => Storage::get($path),
                'description' => "Read file `{$path}`.",
            ]);
        }

        return json_encode([
            'content' => '',
            'description' => "File `{$path}` not found.",
        ]);
    }
}
