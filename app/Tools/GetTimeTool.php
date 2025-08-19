<?php

namespace App\Tools;

use Prism\Prism\Tool;

final class GetTimeTool extends Tool
{
    public function __construct()
    {
        $this->as('get_time')
            ->for('Get the actual time from the user\'s computer')
            ->using($this);
    }

    public function __invoke(): string
    {
        return now()->timezone('Europe/Madrid')->format('H:i:s');
    }
}
