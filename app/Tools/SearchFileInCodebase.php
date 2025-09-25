<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\StorageAttributes;
use Prism\Prism\Tool;

final class SearchFileInCodebase extends Tool
{
    public function __construct()
    {
        $this->as('search_file_in_codebase')
            ->for('Search a file in the codebase. It return the paths of possible coincidences')
            ->withStringParameter(
                name: 'name',
                description: 'Name of file to search.',
                required: true,
            )
            ->using($this);
    }

    public function __invoke(string $name): string
    {
        $paths = Storage::disk('current')->listContents(location: '.', deep: true)
            ->filter(fn (StorageAttributes $attributes) => Str::of($attributes->path())->afterLast('/')->contains($name))
            ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
            ->sortByPath()
            ->map(fn (StorageAttributes $attributes) => $attributes->path())
            ->toArray();

        return collect($paths)->implode(PHP_EOL);
    }
}
