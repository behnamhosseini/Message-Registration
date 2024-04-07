<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function store(StoreMessageRequest $request)
    {
        return $this->messageService->storeMessage($request->validated());
    }
}
