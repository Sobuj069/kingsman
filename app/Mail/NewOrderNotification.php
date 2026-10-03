<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $items;

    /**
     * Create a new message instance.
     *
     * @param array $order
     * @param array $items
     */
    public function __construct(array $order, array $items = [])
    {
        $this->order = $order;
        $this->items = $items;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $orderId = $this->order['order_id'] ?? ($this->order['invoice_no'] ?? 'New Order');
        $customerName = $this->order['customer_name'] ?? 'Customer';
        $grandTotal = number_format((float)($this->order['grand_total'] ?? 0));

        $fromAddress = config('mail.from.address') ?: 'noreply@kingsmen.com.bd';
        $fromName = config('mail.from.name') ?: 'Kingsman Store';

        return $this->from($fromAddress, $fromName)
                    ->subject("🚨 New Order #{$orderId} from {$customerName} (৳{$grandTotal}) - Kingsman")
                    ->view('emails.new-order-notification');
    }
}
