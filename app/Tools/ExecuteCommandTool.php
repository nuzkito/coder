<?php

namespace App\Tools;

use Illuminate\Support\Facades\Process;
use Prism\Prism\Tool;

final class ExecuteCommandTool extends Tool
{
    public function __construct()
    {
        $this->as('execute_command')
            ->for('Executes a command line process in the user\'s system')
            ->withStringParameter(
                name: 'command',
                description: 'The command to execute in the system. Some commands may return their output in ANSI format, which includes color codes and formatting. When this happens, you should use appropriate flags to disable ANSI formatting and return plain text output when possible.',
                required: true,
            )
            ->using($this);
    }

    public function __invoke(string $command): string
    {
        $processResult = Process::run($command);

        return mb_convert_encoding($processResult->output(), 'UTF-8', 'ISO-8859-1');
    }
}
