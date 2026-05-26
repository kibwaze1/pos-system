@extends('layouts.app')
@section('title', 'Add Expense')
@section('content')
<div class="card">
    <div class="card-header">Add Expense</div>
    <div class="card-body">
        <form action="{{ route('expenses.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="Rent">Rent</option>
                    <option value="Utilities">Utilities</option>
                    <option value="Salaries">Salaries</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Supplies">Supplies</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Expense Date</label>
                <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Expense</button>
        </form>
    </div>
</div>
@endsection
