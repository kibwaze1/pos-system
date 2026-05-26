@extends('layouts.app')
@section('title', 'Edit Expense')
@section('content')
<div class="card">
    <div class="card-header">Edit Expense</div>
    <div class="card-body">
        <form action="{{ route('expenses.update', $expense) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="Rent" {{ $expense->category=='Rent'?'selected':'' }}>Rent</option>
                    <option value="Utilities" {{ $expense->category=='Utilities'?'selected':'' }}>Utilities</option>
                    <option value="Salaries" {{ $expense->category=='Salaries'?'selected':'' }}>Salaries</option>
                    <option value="Marketing" {{ $expense->category=='Marketing'?'selected':'' }}>Marketing</option>
                    <option value="Supplies" {{ $expense->category=='Supplies'?'selected':'' }}>Supplies</option>
                    <option value="Other" {{ $expense->category=='Other'?'selected':'' }}>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" required>{{ $expense->description }}</textarea>
            </div>
            <div class="mb-3">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="{{ $expense->amount }}" required>
            </div>
            <div class="mb-3">
                <label>Expense Date</label>
                <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Expense</button>
        </form>
    </div>
</div>
@endsection
