<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $messages = Message::where('user_id', $user->id)->orWhere('receiver_id', $user->id)->get();
        $users = User::where('id', '!=', $user->id)->get();
        return view('chat', [
            'messages' => $messages,
            'user' => $user,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required',
                'receiver_id' => 'required',
            ]);
    
            $message = new Message;
            $message->user_id = Auth::user()->id;
            $message->receiver_id = $request->receiver_id;
            $message->message = $request->message;
            $message->save();
    
            broadcast(new MessageSent($message))->toOthers(); 
            return back();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    public function getMessages($userId) {
    $user = Auth::user();
    $messages = Message::where(function ($query) use ($userId, $user) {
        $query->where('user_id', $user->id)
              ->where('receiver_id', $userId);
    })->orWhere(function ($query) use ($userId, $user) {
        $query->where('user_id', $userId)
              ->where('receiver_id', $user->id);
    })->orderBy('created_at', 'asc')->get();

    return response()->json($messages);

     }

}
