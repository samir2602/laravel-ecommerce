@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Permission</h1>    
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addpermissionmodal">
        Add Permission
    </button>
    <div class="row">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Permission Name</th>
                    <th width="100px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addpermissionmodal" tabindex="-1" aria-labelledby="addpermissionmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addpermissionmodalLabel">Add Permission</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="add-permission-form" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Permission Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('permission_name') }}">
                <span class="text-danger error-text name_error"></span>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <span type="button" class="btn btn-primary add-permission">Add Permission</span>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editpermissionmodal" tabindex="-1" aria-labelledby="editaddpermissionmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editpermissionmodalLabel">Add Permission</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-permission-form">
            @csrf
            <input type="hidden" class="form-control" id="edit_permission_id" name="id" value="">
            <div class="mb-3">
                <label for="name" class="form-label">Permission Name</label>
                <input type="text" class="form-control" id="edit_permission_name" name="name" value="">
                <span class="text-danger error-text name_error"></span>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <span type="button" class="btn btn-primary update-permission">Update Permission</span>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers : {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')}
    });

    $(document).ready(function(){
        $(function(){
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('permission.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });

        $('.add-permission').click(function(){
            var form = $('#add-permission-form')[0];
            const formData = new FormData(form); // Create FormData object from the form   
            let url = "{{ route('permission.store') }}";
            
            $('.error-text').text(''); // clear old errors
            $.ajax({
                url : url,
                type : "post",
                data : formData,
                contentType : false,
                processData : false,
                success: function(res){
                    $('#addpermissionmodal').modal('hide');
                    $('.data-table').DataTable().ajax.reload();
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
            let url = "{{ route('permission.edit', ':id') }}".replace(':id', id);                        
            $.get(url, function(res){                
                $('#edit_permission_id').val(res.permission.id);
                $('#edit_permission_name').val(res.permission.name);
                $('#editpermissionmodal').modal('show');
            });
        });

        $('.update-permission').click(function(){
            var form = $('#edit-permission-form')[0];
            var id = $('#edit_permission_id').val();
            const formData = new FormData(form); // Create FormData object from the form
            formData.append('_method', 'PUT');
            let url = "{{ route('permission.update', ':id') }}".replace(':id', id);
            
            $('.error-text').text(''); // clear old errors
            $.ajax({
                url : url,
                type : "post",
                data : formData,
                contentType : false,
                processData : false,
                success: function(res){
                    if(res.permission){
                        $('#editpermissionmodal').modal('hide');
                        $('.data-table').DataTable().ajax.reload();
                    }
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
            if(!confirm('Are you sure you want to delete this permission?')) return;
            let id =  $(this).data('id');            
           
            $.ajax({
                url: '/admin/permission/'+id,
                type : "DELETE",
                success: function(res){
                    if(res.success){
                        $('.data-table').DataTable().ajax.reload();
                    }
                }
            });
        });
    });
</script>
@endpush