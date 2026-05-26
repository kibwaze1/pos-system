@extends('layouts.app')
@section('title', 'Inventory History')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>Inventory Movement Log</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="historyTable">
            <thead>
                <tr><th>Product</th><th>Type</th><th>Quantity Change</th><th>Reason</th><th>User</th><th>Date</th></tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->product->name ?? 'Deleted Product' }}</td>
                    <td>{{ ucfirst($log->type) }}</td>
                    <td>
                        @if($log->quantity > 0)
                            <span class="text-success">+{{ $log->quantity }}</span>
                        @elseif($log->quantity < 0)
                            <span class="text-danger">{{ $log->quantity }}</span>
                        @else
                            {{ $log->quantity }}
                        @endif
                    </td>
                    <td>{{ $log->reason }}</td>
                    <td>{{ $log->user->name }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $logs->links() }}
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#historyTable').DataTable();
    });
</script>
@endpush
