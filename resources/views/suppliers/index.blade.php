@extends('layouts.app')
@section('title', 'Suppliers')
@section('content')
<div class="card">
    <div class="card-header">
        Suppliers
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm float-end">Add Supplier</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="suppliersTable">
            <thead>
                <tr><th>Name</th><th>Contact Person</th><th>Phone</th><th>Email</th><th>Balance</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($suppliers as $s)
                <tr>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->contact_person }}</td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->email }}</td>
                    <td>${{ number_format($s->balance,2) }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $s->id }}">Delete</button>
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
    $('#suppliersTable').DataTable();
    $('.delete-btn').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Delete supplier?',
            text: "Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/suppliers/${id}`,
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
