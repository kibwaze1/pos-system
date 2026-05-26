@extends('layouts.app')
@section('title', 'Customers')
@section('content')
<div class="card">
    <div class="card-header">
        Customers
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm float-end">Add Customer</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="customersTable">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Balance</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($customers as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>${{ number_format($c->balance,2) }}</td>
                    <td>
                        <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $c->id }}">Delete</button>
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
    $('#customersTable').DataTable();
    $('.delete-btn').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Delete customer?',
            text: "Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customers/${id}`,
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
