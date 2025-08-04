<?php

namespace App\Tools;

use Illuminate\Support\Facades\Process;

final class ExecuteCommandTool implements ToolInterface
{
    public string $name = 'execute_command';

    public string $description = 'Executes a command line process in the user\'s system. Some commands may return their output in ANSI format, which includes color codes and formatting. When this happens, you should use appropriate flags to disable ANSI formatting and return plain text output when possible.';

    public array $schema = [
        'type' => 'object',
        'properties' => [
            'command' => [
                'type' => 'string',
                'description' => 'The command to execute in the system.',
            ],
        ],
        'required' => ['command'],
    ];

    public function execute(array $arguments): ToolResult
    {
        $processResult = Process::run($arguments['command']);

        $result = mb_convert_encoding($processResult->output(), 'UTF-8', 'ISO-8859-1');

        return new ToolResult(
            content: $result,
            description: "Executed `{$processResult->command()}`",
        );
    }
}
