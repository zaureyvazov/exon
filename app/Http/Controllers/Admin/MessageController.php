<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display all messages (admin monitoring).
     */
    public function index(Request $request)
    {
        $query = Message::with(['sender', 'receiver']);

        // Filter by sender
        if ($request->has('sender_id') && $request->sender_id) {
            $query->where('sender_id', $request->sender_id);
        }

        // Filter by receiver
        if ($request->has('receiver_id') && $request->receiver_id) {
            $query->where('receiver_id', $request->receiver_id);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $messages = $query->latest()->paginate(20);

        // Get all doctors and registrars for filter
        $doctorRole = Role::where('name', 'doctor')->first();
        $registrarRole = Role::where('name', 'registrar')->first();

        $doctors = User::where('role_id', $doctorRole->id)->get();
        $registrars = User::where('role_id', $registrarRole->id)->get();

        $stats = [
            'total_messages' => Message::count(),
            'today_messages' => Message::whereDate('created_at', today())->count(),
            'unread_messages' => Message::unread()->count(),
        ];

        return view('admin.messages.index', compact('messages', 'doctors', 'registrars', 'stats'));
    }

    /**
     * Display conversation between two users.
     */
    public function show($senderId, $receiverId)
    {
        $sender = User::findOrFail($senderId);
        $receiver = User::findOrFail($receiverId);

        $messages = Message::where(function($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();

        return view('admin.messages.show', compact('sender', 'receiver', 'messages'));
    }
}
