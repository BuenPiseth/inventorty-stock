@extends('layouts.app')

@section('title', 'Product List')

@section('content')
<h1>Product List</h1>

<a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Add New Product</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td>{{ $product->quantity }}</td>
            <td>{{ $product->unit }}</td>
            <td>{{ $product->status }}</td>
            <td>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>

                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" 
                    onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center">No Products Found</td></tr>
    @endforelse
    </tbody>
</table>

{{ $products->links() }}
@endsection
