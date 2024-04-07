<?php

namespace App\Services;

use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;

class MessageService
{
    public function __construct(protected MessageRepository $messageRepository)
    {
    }

    public function storeMessage(array $data)
    {
        $response= $this->messageRepository->create($data);
        return response([new MessageResource($response)]);
    }
}
