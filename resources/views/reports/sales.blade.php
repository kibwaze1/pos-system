@extends('layouts.app')
@section('title', 'Sales Report - Products Sold')
@section('content')
<div class="card">
    <div class="card-header">Sales Report – Products Sold</div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4">
                <label>Start Date</label>
                <input type="date" name="start" class="form-control" value="{{ $start }}">
            </div>
            <div class="col-md-4">
                <label>End Date</label>
                <input type="date" name="end" class="form-control" value="{{ $end }}">
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">Filter</button>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <a href="{{ route('reports.export-sales-pdf', ['start' => $start, 'end' => $end]) }}" class="btn btn-danger form-control">Download PDF</a>
            </div>
        </form>

        <h4>Total Sales: {{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($total, 2) }}</h4>

        <table class="table table-bordered" id="salesReportTable">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Sale Date</th>
                    <th>Invoice No</th>
                </tr>
            </thead>
            <tbody>
                @foreach($saleItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->price, 2) }}</td>
                    <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($item->total, 2) }}</td>
                    <td>{{ $item->sale->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $item->sale->invoice_no }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#salesReportTable').DataTable();
    });
</script>
@endpush
