<?php

namespace App\Console\Commands;

use App\Conversation;
use App\LlmClient;
use App\LlmResponse;
use App\Message;
use App\Tools;
use App\Tools\CreateFileTool;
use App\Tools\EditFileTool;
use App\Tools\ExecuteCommandTool;
use App\Tools\GetTimeTool;
use App\Tools\ListDirectoriesTool;
use App\Tools\ListFilesTool;
use App\Tools\ReadFileTool;
use App\Tools\SearchFileInCodebase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Concerns\Colors;

use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\textarea;

final class Agent extends Command
{
    use Colors;

    /** @var string */
    protected $signature = 'agent';

    /** @var string */
    protected $description = 'Chat with an agent.';

    public function handle(): void
    {
        $tools = new Tools;
        $tools->add(new GetTimeTool);
        $tools->add(new CreateFileTool);
        $tools->add(new ReadFileTool);
        $tools->add(new ListFilesTool);
        $tools->add(new ListDirectoriesTool);
        $tools->add(new EditFileTool);
        $tools->add(new ExecuteCommandTool);
        $tools->add(new SearchFileInCodebase);

        $client = new LlmClient;
        $conversation = new Conversation;
        $conversation->addMessage(Message::fromSystem(Storage::get('AGENT.md')));

        intro('Starting a chat with an agent.');

        while (true) {
            $message = trim(textarea(label: 'You', hint: 'Send a message to the agent.'));

            if (! $message) {
                break;
            }

            $conversation->addMessage(Message::fromUser($message));
            $agentResponse = spin(
                callback: fn () => $client->sendMessage($conversation, $tools),
                message: 'Waiting response from the agent.',
            );
            if ($agentResponse->hasMessage()) {
                $conversation->addMessage(Message::fromAssistant($agentResponse->message()));
                $this->agentOutput($agentResponse);
            }

            while (true) {
                if ($agentResponse->hasToolCalls()) {
                    $toolCalls = $agentResponse->toolCalls();

                    foreach ($toolCalls as $toolCall) {
                        $tool = $tools->findByName($toolCall['name']);
                        $toolResult = spin(
                            callback: fn () => $tool->execute($toolCall['arguments']),
                            message: "Executing tool {$tool->name}.",
                        );

                        $conversation->addMessage(Message::fromTool("Result of {$tool->name}: {$toolResult->content}"));
                        info(sprintf('Tool | %s: %s', $tool->name, $toolResult->description()));
                    }

                    $agentResponse = spin(
                        callback: fn () => $client->sendMessage($conversation, $tools),
                        message: 'Waiting response from the agent.',
                    );
                    if ($agentResponse->hasMessage()) {
                        $conversation->addMessage(Message::fromAssistant($agentResponse->message()));
                        $this->agentOutput($agentResponse);
                    }
                } else {
                    break;
                }
            }
        }

        outro('Chat ended.');
    }

    private function agentOutput(LlmResponse $response): void
    {
        if ($response->hasReasoning()) {
            note($this->gray("Reasoning: {$response->reasoning()}"));
        }

        if ($response->hasMessage()) {
            note("\033[38;5;208mAgent:\033[0m {$response->message()}");
        }
    }
}
