<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\StorageAttributes;

final class SearchFileInCodebase implements ToolInterface
{
    public string $name = 'search_file_in_codebase';

    public string $description = 'Search a file in the codebase. It return the paths of possible coincidences.';

    public array $schema = [
        'type' => 'object',
        'properties' => [
            'name' => [
                'type' => 'string',
                'description' => 'Name of file to search.',
            ],
        ],
        'required' => ['name'],
    ];

    public function execute(array $arguments): ToolResult
    {
        $paths = Storage::listContents(location: '.', deep: true)
            ->filter(fn (StorageAttributes $attributes) => Str::of($attributes->path())->afterLast('/')->contains($arguments['name']))
            ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
            ->sortByPath()
            ->map(fn (StorageAttributes $attributes) => $attributes->path())
            ->toArray();

        $result = collect($paths)->implode(PHP_EOL);

        return new ToolResult(
            content: $result,
            description: "Searched for file `{$arguments['name']}`.",
        );
    }
}
