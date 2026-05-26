<?php

namespace App\Exports;

use App\Models\SaleItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        return SaleItem::with(['product', 'sale'])
            ->whereHas('sale', function($q) {
                $q->whereBetween('created_at', [$this->start, $this->end])
                  ->where('status', 'completed');
            })
            ->orderBy('sale_id', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Quantity',
            'Unit Price',
            'Total',
            'Sale Date',
            'Invoice No',
            'Currency'
        ];
    }

    public function map($item): array
    {
        $currency = \App\Models\Setting::get('currency_symbol', '$');
        return [
            $item->product->name,
            $item->quantity,
            $currency . ' ' . number_format($item->price, 2),
            $currency . ' ' . number_format($item->total, 2),
            $item->sale->created_at->format('Y-m-d H:i:s'),
            $item->sale->invoice_no,
            $currency,
        ];
    }
}
