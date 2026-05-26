@extends('layouts.app')
@section('title', 'Add Customer')
@section('content')
<div class="card">
    <div class="card-header">Add Customer</div>
    <div class="card-body">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
            <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" required></div>
            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control"></textarea></div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
