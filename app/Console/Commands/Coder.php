<?php

namespace App\Console\Commands;

use App\Tools\CreateFileTool;
use App\Tools\EditFileTool;
use App\Tools\ExecuteCommandTool;
use App\Tools\GetTimeTool;
use App\Tools\ListDirectoriesTool;
use App\Tools\ListFilesTool;
use App\Tools\ReadFileTool;
use App\Tools\SearchFileInCodebase;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Concerns\Colors;
use Prism\Prism\Prism;
use Prism\Prism\Text\Response;
use Prism\Prism\Text\Step;
use Prism\Prism\ValueObjects\Messages\UserMessage;

use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\textarea;

final class Coder extends Command
{
    use Colors;

    /** @var string */
    protected $signature = 'coder';

    /** @var string */
    protected $description = 'Chat with a LLM.';

    public function handle(): void
    {
        intro('Starting a chat with a LLM.');

        $messages = collect();

        while (true) {
            $message = trim(textarea(label: 'You', hint: 'Send a message to the LLM.'));

            if (! $message) {
                break;
            }

            $response = spin(
                callback: fn () => $this->sendMessage(new UserMessage($message), $messages),
                message: 'Waiting response from the LLM.',
            );

            $messages = $response->messages;

            foreach ($response->steps as $step) {
                if ($step->toolCalls) {
                    $this->toolOutput($step);
                }

                if ($step->text) {
                    $this->llmOutput($step);
                }
            }
        }

        outro('Chat ended.');
    }

    private function sendMessage(UserMessage $message, Collection $messages): Response
    {
        return Prism::text()
            ->using(provider: 'lmstudio', model: env('LLM_MODEL'))
            ->withMaxSteps(100)
            ->withSystemPrompt(Storage::disk('app')->get('AGENTS.md'))
            ->withMessages([...$messages, $message])
            ->withTools([
                new GetTimeTool,
                new CreateFileTool,
                new ReadFileTool,
                new ListFilesTool,
                new ListDirectoriesTool,
                new EditFileTool,
                new ExecuteCommandTool,
                new SearchFileInCodebase,
            ])
            ->asText();
    }

    private function toolOutput(Step $step)
    {
        $toolCall = $step->toolCalls[0];
        info(sprintf('[Tool] %s(%s)', $toolCall->name, json_encode($toolCall->arguments())));

        $toolResult = $step->toolResults[0];
        info(sprintf('[Tool Result] %s -> %s', $toolResult->toolName, $toolResult->result));
    }

    private function llmOutput(Step $step): void
    {
        $reasoning = collect($step->messages)->last()->additionalContent['reasoning'] ?? null;

        if ($reasoning) {
            note($this->gray("Reasoning: {$reasoning}"));
        }

        if ($step->text) {
            note("\033[38;5;208mLLM:\033[0m {$step->text}");
        }
    }
}
