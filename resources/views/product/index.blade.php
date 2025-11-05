@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product</h1>    
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addproductmodal">
        Add Product
    </button>
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

<!-- Modal -->
<div class="modal fade" id="addproductmodal" tabindex="-1" aria-labelledby="addproductmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addproductmodalLabel">Add Product</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="add-product-form" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="name" value="{{ old('name') }}">
                <span class="text-danger error-text name_error"></span>
            </div>
            <div class="mb-3">
                <label for="product_price" class="form-label">Product Price</label>
                <input type="text" class="form-control" id="product_price" name="price" value="{{ old('price') }}">
                <span class="text-danger error-text price_error"></span>
            </div>
            <div class="mb-3">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="product_image" name="image">
                <span class="text-danger error-text image_error"></span>
            </div>            
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <span type="button" class="btn btn-primary add-product">Save changes</span>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    $(document).ready(function() {
        $(function () {                
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('product.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'price', name: 'price'},
                    {data: 'upc', name: 'upc'},
                    {data: 'status', name: 'status'},
                    {data: 'image', name: 'image'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });                
        });

        $('.add-product').on('click', function(){
            $('.error-text').text(''); // clear old errors
            var form = $('#add-product-form')[0];
            const formData = new FormData(form); // Create FormData object from the form
            let url = "{{ route('product.store') }}";

            $.ajax({
                url: url,
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res){
                    if(res.product.id){
                        $('#addproductmodal').modal('hide');                        
                    } else {                        
                    }
                    $('#add-product-form')[0].reset();                    
                },
                error: function(err){
                    if(err.status === 422){
                        let errors = err.responseJSON.errors;
                        $.each(errors, function(key, value){
                            $('.'+key+'_error').text(value[0]);
                        });
                    }
                }
            });
        });
    });
</script>
@endpush

