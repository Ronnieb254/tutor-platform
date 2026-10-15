<?php

namespace App\Livewire\Booking;

use App\Models\Booking;
use App\Services\JitsiService;
use Livewire\Component;

class JoinSession extends Component
{
    public Booking $booking;
    public string  $enteredCode  = '';
    public bool    $codeVerified = false;
    public string  $codeError    = '';
    public ?string $jitsiToken   = null;
    public string  $jitsiDomain  = '';
    public string  $roomName     = '';

    public function mount(Booking $booking)
    {
        $this->booking     = $booking;
        $this->jitsiDomain = config('jitsi.domain', 'meet.jit.si');
    }

    public function verifyCode(JitsiService $jitsi)
    {
        $user       = auth()->user();
        $isTutor    = $user->id === $this->booking->tutor_id;
        $isStudent  = $user->id === $this->booking->student_id;
        $enteredCode = strtoupper(trim($this->enteredCode));

        $validCode = $isTutor
            ? $this->booking->session_code_tutor
            : $this->booking->session_code_student;

        if ($enteredCode !== strtoupper($validCode)) {
            $this->codeError = 'Invalid access code. Please check your email and try again.';
            return;
        }

        $this->codeVerified = true;
        $this->codeError    = '';
        $this->roomName     = $jitsi->generateRoomName($this->booking);
        $this->jitsiToken   = $jitsi->generateToken($user, $this->roomName, $isTutor);

        // Mark joined
        if ($isTutor)   $this->booking->update(['tutor_joined'   => true]);
        if ($isStudent) $this->booking->update(['student_joined' => true]);

        $this->dispatch('launch-jitsi', [
            'domain'    => $this->jitsiDomain,
            'room'      => $this->roomName,
            'jwt'       => $this->jitsiToken,
            'userName'  => $user->name,
            'userEmail' => $user->email,
            'userAvatar'=> $user->avatar_url,
            'isModerator' => $isTutor,
        ]);
    }

    public function canJoin(): bool
    {
        $diff = now()->diffInMinutes($this->booking->scheduled_at, false);
        return $this->booking->status === 'confirmed' && $diff >= -120 && $diff <= 15;
    }

    public function render()
    {
        return view('livewire.booking.join-session');
    }
}
