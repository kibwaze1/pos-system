@extends('layouts.app')
@section('title', 'Add Supplier')
@section('content')
<div class="card">
    <div class="card-header">Add Supplier</div>
    <div class="card-body">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-3"><label>Contact Person</label><input type="text" name="contact_person" class="form-control" required></div>
            <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control" required></div>
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control" required></textarea></div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
