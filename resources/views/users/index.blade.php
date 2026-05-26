@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="card">
    <div class="card-header">
        System Users
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm float-end">Add User</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="usersTable">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Created At</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->role->display_name }}</td>
                    <td>{{ $u->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $u->id }}" data-name="{{ $u->name }}">Delete</button>
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
    $('#usersTable').DataTable();
    $('.delete-btn').click(function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        Swal.fire({
            title: 'Delete user: ' + name + '?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/users/${id}`,
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
