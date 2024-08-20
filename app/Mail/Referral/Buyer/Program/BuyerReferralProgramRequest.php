<?php

namespace App\Mail\Referral\Buyer\Program;

use App\Models\BuyerReferralProgram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BuyerReferralProgramRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $program;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(BuyerReferralProgram $program)
    {
        $this->program = $program;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Buyer Referral Program Request',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.referral.buyer.program.request',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
