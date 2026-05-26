@extends('layouts.app')
@section('title', 'Edit Customer')
@section('content')
<div class="card">
    <div class="card-header">Edit Customer</div>
    <div class="card-body">
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $customer->name }}" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="{{ $customer->email }}"></div>
            <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" required></div>
            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control">{{ $customer->address }}</textarea></div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
