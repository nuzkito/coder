<?php

namespace App;

final readonly class LlmResponse
{
    public function __construct(
        private array $response,
    ) {}

    public function hasMessage(): bool
    {
        return isset($this->response['choices'][0]['message']['content']);
    }

    public function message(): string
    {
        return str($this->rawMessage())->before('<tool_call>')->trim();
    }

    public function hasToolCalls(): bool
    {
        return $this->hasToolCallsInResponse() || $this->hasToolCallsInMessage();
    }

    public function toolCalls(): array
    {
        return [...$this->toolCallsFromMessage(), ...$this->toolCallsFromResponse()];
    }

    private function rawMessage(): string
    {
        return $this->response['choices'][0]['message']['content'] ?? '';
    }

    private function hasToolCallsInResponse(): bool
    {
        return isset($this->response['choices'][0]['message']['tool_calls']);
    }

    private function hasToolCallsInMessage(): bool
    {
        return $this->hasMessage() && str($this->rawMessage())->contains(['<tool_call>', '</tool_call>']);
    }

    private function toolCallsFromMessage(): array
    {
        preg_match_all('/(?:^\s*<tool_call>\s*)+({.*})(?:\s*<\/tool_call>)+/m', $this->rawMessage(), $matches);

        $results = [];
        foreach ($matches[1] as $json) {
            $results[] = json_decode($json, true);
        }

        return $results;
    }

    private function toolCallsFromResponse(): array
    {
        return collect($this->response['choices'][0]['message']['tool_calls'] ?? [])
            ->map(fn (array $toolCall) => [
                'name' => $toolCall['function']['name'],
                'arguments' => json_decode($toolCall['function']['arguments'], true),
            ])
            ->toArray();
    }
}
