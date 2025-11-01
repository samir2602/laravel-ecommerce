@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product</h1>
    <a href="{{ route('product.create') }}" class="btn btn-success">Add Product</a>
    <div class="row">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>UPC</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th width="100px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

