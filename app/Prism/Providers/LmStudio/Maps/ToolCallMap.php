<?php

declare(strict_types=1);

namespace App\Prism\Providers\LmStudio\Maps;

use Prism\Prism\ValueObjects\ToolCall;

class ToolCallMap
{
    /**
     * @param  array<int, mixed>  $toolCalls
     * @return array<int, ToolCall>
     */
    public static function map(array $toolCalls): array
    {
        return array_map(fn (array $toolCall): ToolCall => new ToolCall(
            id: $toolCall['id'],
            name: $toolCall['function']['name'],
            arguments: array_filter(json_decode((string) $toolCall['function']['arguments'], true)),
        ), $toolCalls);
    }
}
