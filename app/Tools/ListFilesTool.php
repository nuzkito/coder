<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Prism\Prism\Tool;

final class ListFilesTool extends Tool
{
    public function __construct()
    {
        $this->as('list_files')
            ->for('List files of the filesystem')
            ->withStringParameter(
                name: 'path',
                description: 'Relative pathname of the directory to read. If path is not defined, it will use the actual working directory.',
            )
            ->using($this);
    }

    public function __invoke(?string $path = '.'): string
    {
        return collect(Storage::disk('current')->files($path))->implode("\n");
    }
}
