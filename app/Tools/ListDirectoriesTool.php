<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Prism\Prism\Tool;

final class ListDirectoriesTool extends Tool
{
    public function __construct()
    {
        $this->as('list_directories')
            ->for('List directories of the filesystem')
            ->withStringParameter(
                name: 'path',
                description: 'Relative pathname of the directory to read. If path is not defined, it will use the actual working directory.',
            )
            ->using($this);
    }

    public function __invoke(?string $path): string
    {
        return collect(Storage::disk('current')->directories($path ?? null))->implode("\n");
    }
}
