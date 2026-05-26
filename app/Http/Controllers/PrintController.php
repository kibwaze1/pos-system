<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Setting;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function directPrint(Sale $sale)
    {
        if (Setting::get('printer_enabled') != '1') {
            return response()->json(['error' => 'Direct printing is disabled in settings'], 400);
        }

        try {
            $printer = $this->getPrinter();
            if (!$printer) {
                return response()->json(['error' => 'Printer not configured properly'], 500);
            }

            $sale->load('items.product', 'user');

            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(Setting::get('store_name', 'POS System') . "\n");
            $printer->text(Setting::get('store_address', '') . "\n");
            $printer->text("Tel: " . Setting::get('store_phone', '') . "\n");
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Invoice: {$sale->invoice_no}\n");
            $printer->text("Date: {$sale->created_at->format('Y-m-d H:i:s')}\n");
            $printer->text("Cashier: {$sale->user->name}\n");
            $printer->text("--------------------------------\n");
            $printer->text(str_pad("Item", 20) . str_pad("Qty", 5) . str_pad("Price", 8) . str_pad("Total", 8) . "\n");
            $printer->text("--------------------------------\n");
            foreach ($sale->items as $item) {
                $name = substr($item->product->name, 0, 18);
                $line = str_pad($name, 20) . str_pad($item->quantity, 5) . str_pad(number_format($item->price, 2), 8) . str_pad(number_format($item->total, 2), 8);
                $printer->text($line . "\n");
            }
            $printer->text("--------------------------------\n");
            $printer->text(str_pad("Subtotal:", 33) . number_format($sale->subtotal, 2) . "\n");
            if ($sale->discount > 0) $printer->text(str_pad("Discount:", 33) . number_format($sale->discount, 2) . "\n");
            if ($sale->tax > 0) $printer->text(str_pad("Tax:", 33) . number_format($sale->tax, 2) . "\n");
            $printer->text(str_pad("TOTAL:", 33) . number_format($sale->total, 2) . "\n");
            $printer->text(str_pad("Paid:", 33) . number_format($sale->paid, 2) . "\n");
            $printer->text(str_pad("Change:", 33) . number_format($sale->change, 2) . "\n");
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(Setting::get('receipt_footer', 'Thank you!') . "\n");
            $printer->feed(2);
            $printer->cut();
            $printer->close();

            return response()->json(['success' => 'Receipt printed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getPrinter()
    {
        $type = Setting::get('printer_connection_type');
        if (!$type) return null;

        switch ($type) {
            case 'network':
                $ip = Setting::get('printer_network_ip', '127.0.0.1');
                $port = Setting::get('printer_network_port', 9100);
                $connector = new NetworkPrintConnector($ip, $port);
                break;
            case 'windows':
                $share = Setting::get('printer_windows_share');
                if (!$share) return null;
                $connector = new WindowsPrintConnector($share);
                break;
            case 'usb':
                $path = Setting::get('printer_usb_path');
                if (!$path) return null;
                $connector = new FilePrintConnector($path);
                break;
            default:
                return null;
        }
        return new Printer($connector);
    }
}
