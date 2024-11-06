<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderReceiptUploaded extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->markdown('mails.orders.receipt-uploaded');

        $receipt = $this->order->receipt;
        if ($receipt) {
            if (is_array($receipt)) {
                foreach ($receipt as $slip) {
                    $mail->attachFromStorageDisk('public', $slip);
                }
            } else {
                $mail->attachFromStorageDisk('public', $this->order->receipt);
            }
        }

        return $mail;
    }
}
