<?php

namespace App\Listeners;

use App\Models\MpesaTransaction;
use App\Models\Sale;
use App\Models\Product;
use App\Models\InventoryLog;
use Ghostscypher\Mpesa\Events\PaymentSuccessful;

class MarkSaleAsPaid
{
    public function handle(PaymentSuccessful $event): void
    {
        $transaction = MpesaTransaction::where('checkout_request_id', $event->payload->checkoutRequestId)->first();
        if (!$transaction) return;

        $sale = Sale::find($transaction->sale_id);
        if (!$sale || $sale->status !== 'pending') return;

        $sale->update([
            'status' => 'completed',
            'paid' => $event->payload->amount,
            'change' => 0,
        ]);

        // Reduce stock and log inventory
        foreach ($sale->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('stock_quantity', $item->quantity);
                InventoryLog::create([
                    'product_id' => $item->product_id,
                    'type' => 'stock_out',
                    'quantity' => $item->quantity,
                    'reason' => 'M-Pesa sale - ' . $sale->invoice_no,
                    'user_id' => $sale->user_id,
                ]);
            }
        }

        $transaction->update(['status' => 'completed']);
    }
}
