<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display conversations list.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ensure role is loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Get all users that current user can message
        if ($user->isDoctor()) {
            $usersQuery = User::with('role')
                ->whereHas('role', function($q) {
                    $q->where('name', 'registrar');
                })
                ->select('id', 'name', 'surname', 'email');
        } elseif ($user->isRegistrar()) {
            $usersQuery = User::with('role')
                ->whereHas('role', function($q) {
                    $q->where('name', 'doctor');
                })
                ->select('id', 'name', 'surname', 'email');
            
            // Axtarış (yalnız registrar üçün)
            if ($request->filled('search')) {
                $search = $request->search;
                $usersQuery->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('surname', 'LIKE', "%{$search}%");
                });
            }
        } else {
            $users = collect();
            $conversations = collect();
            return view('messages.index', compact('conversations'));
        }

        $users = $usersQuery->get();

        // Optimize: Get all last messages and unread counts in fewer queries
        $userIds = $users->pluck('id');

        // Get last messages
        $lastMessages = Message::whereIn('sender_id', $userIds->push($user->id))
            ->whereIn('receiver_id', $userIds->push($user->id))
            ->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->select('id', 'sender_id', 'receiver_id', 'message', 'created_at')
            ->get()
            ->groupBy(function($message) use ($user) {
                return $message->sender_id == $user->id
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(fn($messages) => $messages->sortByDesc('created_at')->first());

        // Get unread counts
        $unreadCounts = Message::where('receiver_id', $user->id)
            ->whereIn('sender_id', $userIds)
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->selectRaw('sender_id, COUNT(*) as count')
            ->pluck('count', 'sender_id');

        // Build conversations
        $conversations = $users->map(function($otherUser) use ($lastMessages, $unreadCounts) {
            return [
                'user' => $otherUser,
                'last_message' => $lastMessages->get($otherUser->id),
                'unread_count' => $unreadCounts->get($otherUser->id, 0),
            ];
        });

        // Registrar üçün: Yalnız oxunmamış mesajı olanları göstər (əgər 'all' parametri yoxdursa)
        if ($user->isRegistrar() && !$request->filled('search') && !$request->has('all')) {
            $conversations = $conversations->filter(function($conversation) {
                return $conversation['unread_count'] > 0;
            });
        }

        // Oxunmamış mesaj sayına görə sırala
        $conversations = $conversations->sortByDesc('unread_count')
            ->sortByDesc('last_message.created_at');

        return view('messages.index', compact('conversations'));
    }

    /**
     * Display conversation with specific user.
     */
    public function show($userId)
    {
        $user = Auth::user();
        $otherUser = User::select('id', 'name', 'surname')->findOrFail($userId);

        // Get all messages between these two users - optimized
        $messages = Message::where(function($q) use ($user, $otherUser) {
            $q->where('sender_id', $user->id)->where('receiver_id', $otherUser->id);
        })->orWhere(function($q) use ($user, $otherUser) {
            $q->where('sender_id', $otherUser->id)->where('receiver_id', $user->id);
        })
        ->select('id', 'sender_id', 'receiver_id', 'message', 'is_read', 'created_at')
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark received messages as read
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('messages.show', compact('otherUser', 'messages'));
    }

    /**
     * Store a new message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Mesaj göndərildi');
    }

    /**
     * Get unread messages count.
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadMessagesCount();
        return response()->json(['count' => $count]);
    }
}
