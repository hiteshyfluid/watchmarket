<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeId = (int) $request->query('conversation');

        $conversations = Conversation::query()
            ->with([
                'advert:id,title,price,main_image',
                'buyer:id,first_name,last_name',
                'seller:id,first_name,last_name',
                'latestMessage',
            ])
            ->where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        $activeConversation = $conversations->firstWhere('id', $activeId);
        if (!$activeConversation) {
            $activeConversation = $conversations->first();
        }

        return view('messages.index', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
        ]);
    }

    public function sendFromEnquiry(Request $request, Advert $advert): JsonResponse
    {
        $user = $request->user();
        abort_if($advert->user_id === $user->id, 422, 'You cannot enquire on your own advert.');
        abort_if($advert->status !== Advert::STATUS_ACTIVE, 422, 'This advert is not active.');

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = Conversation::firstOrCreate(
            [
                'advert_id' => $advert->id,
                'buyer_id' => $user->id,
                'seller_id' => $advert->user_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'message' => trim((string) $data['message']),
        ]);

        $conversation->update(['last_message_at' => $message->created_at ?? now()]);

        return response()->json([
            'ok' => true,
            'conversation_id' => $conversation->id,
            'redirect_url' => route('messages.index', ['conversation' => $conversation->id]),
        ]);
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->isParticipant($conversation, $user->id), 403);

        $messages = ConversationMessage::query()
            ->with('sender:id,first_name,last_name')
            ->where('conversation_id', $conversation->id)
            ->orderBy('id')
            ->get()
            ->map(function (ConversationMessage $message) use ($user) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender?->name ?? 'User',
                    'message' => $message->message,
                    'is_mine' => $message->sender_id === $user->id,
                    'time' => optional($message->created_at)->format('H:i'),
                    'created_at' => optional($message->created_at)?->toISOString(),
                ];
            });

        ConversationMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);

        return response()->json([
            'ok' => true,
            'messages' => $messages,
        ]);
    }

    public function storeMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->isParticipant($conversation, $user->id), 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'message' => trim((string) $data['message']),
        ]);

        $conversation->update(['last_message_at' => $message->created_at ?? now()]);

        return response()->json([
            'ok' => true,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'is_mine' => true,
                'time' => optional($message->created_at)->format('H:i'),
                'created_at' => optional($message->created_at)?->toISOString(),
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->with([
                'advert:id,title,price,main_image',
                'buyer:id,first_name,last_name',
                'seller:id,first_name,last_name',
                'latestMessage',
            ])
            ->where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (Conversation $conversation) use ($user) {
                $other = $conversation->buyer_id === $user->id
                    ? $conversation->seller
                    : $conversation->buyer;

                return [
                    'id' => $conversation->id,
                    'title' => $conversation->advert?->title ?? 'Watch Listing',
                    'price' => $conversation->advert?->price,
                    'advert_image' => $conversation->advert?->mainImageUrl(),
                    'other_name' => $other?->name ?? 'User',
                    'last_message' => $conversation->latestMessage?->message ?? '',
                    'last_time' => optional($conversation->latestMessage?->created_at)?->diffForHumans(),
                ];
            });

        return response()->json([
            'ok' => true,
            'conversations' => $conversations,
        ]);
    }

    private function isParticipant(Conversation $conversation, int $userId): bool
    {
        return $conversation->buyer_id === $userId || $conversation->seller_id === $userId;
    }
}
