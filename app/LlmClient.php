<?php

namespace App;

use Exception;

final class LlmClient
{
    public function __construct(
        private string $apiKey = '',
        private string $baseUrl = 'http://localhost:1234',
    ) {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function sendMessage(Conversation $conversation, Tools $tools): LlmResponse
    {
        $url = "{$this->baseUrl}/v1/chat/completions";

        $data = json_encode([
            'model' => env('LLM_MODEL'),
            'messages' => $conversation->messages->map(fn (Message $message) => $message->toArray())->toArray(),
            'tools' => $tools->toArray(),
            'stream' => false,
        ]);

        $response = $this->sendRequest($url, $data);

        throw_if(
            $response === false,
            new Exception("Error al enviar la solicitud.\n{$data}"),
        );

        $jsonResponse = json_decode($response, true);

        throw_if(
            ! isset($jsonResponse['choices'][0]['message']['content']) && ! isset($jsonResponse['choices'][0]['message']['tool_calls']),
            new Exception("Respuesta no válida del modelo.\n{$data}\n{$response}"),
        );

        return new LlmResponse($jsonResponse);
    }

    private function sendRequest(string $url, string $data): string
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$this->apiKey,
            ],
        ]);

        $response = curl_exec($ch);

        throw_if($response === false, new Exception('Error cURL: '.curl_error($ch)));

        curl_close($ch);

        return $response;
    }
}
