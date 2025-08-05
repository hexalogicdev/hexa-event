<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingConfirmationMail extends Mailable
{
  use Queueable, SerializesModels;
  public $booking;
  public $filePath;
  /**
   * Create a new message instance.
   */
  public function __construct($booking, string $filePath)
  {
    $this->booking = $booking;
    $this->filePath = $filePath;
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Dome Carlow Booking Confirmation' . ($this->booking->created_at)->format('d-m-y'),
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'dome_carlow.bookings.booking_email',
      with: [
        'booking' => $this->booking,
      ],
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array
  {
    $createdAt = $this->booking->created_at->format('d-m-y'); // Format as YYYY-MM-DD
    $fileName = "DomeCarlow_Booking_{$createdAt}.pdf";
    return [
      Attachment::fromPath($this->filePath)
        ->as($fileName)
        ->withMime('application/pdf'),
    ];
  }
}
