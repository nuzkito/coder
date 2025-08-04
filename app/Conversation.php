<?php

namespace App;

use Illuminate\Support\Collection;

final readonly class Conversation
{
    public Collection $messages;

    public function __construct()
    {
        $this->messages = new Collection;
    }

    public function addMessage(Message $message): void
    {
        $this->messages->push($message);
    }
}
