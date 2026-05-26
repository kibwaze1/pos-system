@extends('layouts.app')
@section('title', 'Expenses')
@section('content')
<div class="card">
    <div class="card-header">
        Expenses
        <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm float-end">Add Expense</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="expensesTable">
            <thead>
                <tr><th>Category</th><th>Description</th><th>Amount</th><th>Date</th><th>Added By</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($expenses as $e)
                <tr>
                    <td>{{ $e->category }}</td>
                    <td>{{ $e->description }}</td>
                    <td>{{ \App\Models\Setting::get('currency_symbol', '$') }}{{ number_format($e->amount, 2) }}</td>
                    <td>{{ $e->expense_date }}</td>
                    <td>{{ $e->user->name }}</td>
                    <td>
                        <a href="{{ route('expenses.edit', $e) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $e->id }}">Delete</button>
                    </td>
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
    $('#expensesTable').DataTable();
    $('.delete-btn').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Delete expense?',
            text: "Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/expenses/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: () => location.reload()
                });
            }
        });
    });
});
</script>
@endpush
