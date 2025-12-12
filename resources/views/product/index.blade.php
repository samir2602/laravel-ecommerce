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

<!-- Modal -->
<div class="modal fade" id="editproductmodal" tabindex="-1" aria-labelledby="editproductmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editproductmodalLabel">Edit Product</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-product-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="form-control" id="edit_product_id" value="">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="edit_product_name" name="name" value="">
                <span class="text-danger error-text name_error"></span>
            </div>
            <div class="mb-3">
                <label for="product_price" class="form-label">Product Price</label>
                <input type="text" class="form-control" id="edit_product_price" name="price" value="">
                <span class="text-danger error-text price_error"></span>
            </div>
            <div class="mb-3">
                <label for="product_image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="edit_product_image" name="image">
                <span class="text-danger error-text image_error"></span>
                <div class="mt-3">
                    <img src="" id="edit_product_image_div" style="width: 100px; height:auto;">
                </div>
            </div>            
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <span type="button" class="btn btn-primary update-product">Save changes</span>
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
                        $('.data-table').DataTable().ajax.reload();
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

        $('.update-product').on('click', function(){
            $('.error-text').text(''); // clear old errors
            var form = $('#edit-product-form')[0];
            var eid = $('#edit_product_id').val();
            const formData = new FormData(form); // Create FormData object from the form
            formData.append('_method', 'PUT');
            let url = "{{ route('product.update', ':id') }}".replace(':id', eid);

            $.ajax({
                url: url,
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res){
                    if(res.product){
                        $('#editproductmodal').modal('hide');
                        $('.data-table').DataTable().ajax.reload();
                    } else {                        
                    }
                    $('#edit-product-form')[0].reset();                    
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

        $(document).on('click', '.edit', function(){
        let id = $(this).data('id');
        let url = "{{ route('product.edit', ':id') }}".replace(':id', id);
            $.get(url, function(res){                
                $('#edit_product_id').val(res.product.id);
                $('#edit_product_name').val(res.product.name);
                $('#edit_product_price').val(res.product.price);
                $('#edit_product_image_div').prop('src',res.product.image);
                $('#editproductmodal').modal('show');                
            });
        });

        $(document).on('change', '.status-switch', function(){
            let id = $(this).data('id');
            let status = ($(this).prop('checked')) ? 1 : 0;
            let url = "{{ route('product_status') }}";
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id : id,
                    status : status,
                },
                success: function(res){
                    // alert('status update');
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

        $(document).on('click', '.delete_btn', function(){
            if(!confirm('Are you sure')) return;
            let id = $(this).data('id');            
            $.ajax({
                url: '/admin/product/'+id,
                type: 'DELETE',
                success: function(res){
                    if(res.success){
                        alert('Record Delete');
                        location.reload();
                    }
                }
            })
        });
    });
</script>
@endpush

