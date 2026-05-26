@extends('layouts.app')
@section('title', 'Add User')
@section('content')
<div class="card">
    <div class="card-header">Add New User</div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label>Role</label><select name="role_id" class="form-control">@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->display_name }}</option>@endforeach</select></div>

            <div class="mb-3">
                <label>Module Permissions (for cashiers)</label>
                <div class="row">
                    @foreach($modules as $key => $label)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="permissions[{{ $key }}]" class="form-check-input" value="1">
                            <label class="form-check-label">{{ $label }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <small class="text-muted">Admins automatically have all permissions.</small>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
