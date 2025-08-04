<?php

namespace App;

use App\Tools\ToolInterface;
use Illuminate\Support\Collection;
use stdClass;

final readonly class Tools
{
    private Collection $tools;

    public function __construct()
    {
        $this->tools = new Collection;
    }

    public function add(ToolInterface $tool): void
    {
        $this->tools->push($tool);
    }

    public function findByName(string $name): ToolInterface
    {
        return $this->tools->first(fn (ToolInterface $tool) => $tool->name === $name);
    }

    public function toArray(): array
    {
        return $this->tools->map(fn (ToolInterface $tool) => [
            'type' => 'function',
            'function' => [
                'name' => $tool->name,
                'description' => $tool->description,
                'parameters' => $tool->schema === [] ? ['type' => 'object', 'properties' => new stdClass] : $tool->schema,
            ],
        ])->toArray();
    }
}
