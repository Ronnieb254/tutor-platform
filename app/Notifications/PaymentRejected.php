<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification
{
    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Not Verified – Action Required')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Your payment submission (code: ' . $this->payment->mpesa_code . ') could not be verified.')
            ->line('Reason: ' . ($this->payment->admin_note ?? 'No reason provided.'))
            ->line('Amount: KSh ' . number_format($this->payment->amount, 2))
            ->line('Please re-submit with the correct M-Pesa code, or contact support.')
            ->action('Book Again', url('/tutors'))
            ->line('Paybill: 542542  |  Account: 313126');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'payment_rejected',
            'payment_id' => $this->payment->id,
            'message'    => 'Your payment could not be verified. Reason: ' . $this->payment->admin_note,
            'mpesa_code' => $this->payment->mpesa_code,
            'amount'     => $this->payment->amount,
        ];
    }
}
