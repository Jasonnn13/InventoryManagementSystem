<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Penjualan;

class OverduePaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
        public function __construct($penjualan)
    {
        $this->penjualan = $penjualan;
    }

    public function build()
    {
        return $this->to('sniperblack185@gmail.com')
                    ->subject('Overdue Payment Notification')
                    ->view('overdue')
                    ->with('penjualan', $this->penjualan);
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Overdue Payment Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'overdue',
        );
    }


}
