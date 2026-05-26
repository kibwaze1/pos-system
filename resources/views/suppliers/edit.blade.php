@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('content')
<div class="card">
    <div class="card-header">Edit Supplier</div>
    <div class="card-body">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required></div>
            <div class="mb-3"><label>Contact Person</label><input type="text" name="contact_person" class="form-control" value="{{ $supplier->contact_person }}" required></div>
            <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="{{ $supplier->email }}"></div>
            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control" required>{{ $supplier->address }}</textarea></div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
