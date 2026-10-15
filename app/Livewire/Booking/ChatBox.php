<?php

namespace App\Livewire\Booking;

use App\Models\Booking;
use App\Models\Message;
use Livewire\Component;

class ChatBox extends Component
{
    public Booking $booking;
    public string  $newMessage = '';

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
        $this->markRead();
    }

    public function sendMessage()
    {
        $this->validate(['newMessage' => 'required|string|max:1000']);

        $receiverId = auth()->id() === $this->booking->student_id
            ? $this->booking->tutor_id
            : $this->booking->student_id;

        Message::create([
            'booking_id'  => $this->booking->id,
            'sender_id'   => auth()->id(),
            'receiver_id' => $receiverId,
            'body'        => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->dispatch('message-sent');
    }

    public function markRead()
    {
        Message::where('booking_id', $this->booking->id)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.booking.chat-box', [
            'messages' => Message::where('booking_id', $this->booking->id)
                ->with('sender')
                ->oldest()
                ->get(),
        ]);
    }
}
