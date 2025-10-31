@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product</h1>
    <a href="{{ route('product.create') }}" class="btn btn-success">Add Product</a>
    <div class="row">
    </div>
</div>
@endsection