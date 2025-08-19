<?php

declare(strict_types=1);

namespace App\Prism\Providers\LmStudio\Maps;

use Prism\Prism\Tool;

class ToolMap
{
    /**
     * @param  array<int, Tool>  $tools
     * @return array<int, mixed>
     */
    public static function map(array $tools): array
    {
        return array_map(fn (Tool $tool): array => array_filter([
            'type' => 'function',
            'function' => array_filter([
                'name' => $tool->name(),
                'description' => $tool->description(),
                'parameters' => $tool->parameters() === [] ? null : [
                    'type' => 'object',
                    'properties' => $tool->parametersAsArray(),
                    'required' => $tool->requiredParameters(),
                ],
            ]),
            'strict' => $tool->providerOptions('strict'),
        ]), $tools);
    }
}
