@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="card">
    <div class="card-header">Edit User: {{ $user->name }}</div>
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $user->name }}" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="{{ $user->email }}" required></div>
            <div class="mb-3"><label>Password (leave blank to keep)</label><input type="password" name="password" class="form-control"></div>
            <div class="mb-3"><label>Role</label>
                <select name="role_id" class="form-control">
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ $user->role_id == $r->id ? 'selected' : '' }}>{{ $r->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Module Permissions (for cashiers)</label>
                <div class="row">
                    @foreach($modules as $key => $label)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="permissions[{{ $key }}]" class="form-check-input" value="1" {{ (isset($user->permissions[$key]) && $user->permissions[$key]) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $label }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <small class="text-muted">Admins automatically have all permissions.</small>
            </div>

            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
</div>
@endsection
