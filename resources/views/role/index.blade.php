@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Roles</h1>    
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addrolemodal">
        Add Role
    </button>
    <div class="row">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Role Name</th>
                    <th>Permissions</th>
                    <th width="100px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addrolemodal" tabindex="-1" aria-labelledby="addrolemodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addrolemodalLabel">Add Role</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="add-role-form">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Role Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                <span class="text-danger error-text name_error"></span>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Permissions</label>
                @foreach($permissions as $permission)
                <div class="col-lg-6 form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->name }}">
                    <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                </div>
                @endforeach
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <span type="button" class="btn btn-primary add-role">Add Role</span>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editrolemodal" tabindex="-1" aria-labelledby="editrolemodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editrolemodalLabel">Edit Role</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-role-form">
            @csrf
            <input type="hidden" id="edit_role_id">
            <div class="mb-3">
                <label for="name" class="form-label">Role Name</label>
                <input type="text" class="form-control" id="editrolename" name="name" value="{{ old('name') }}">
                <span class="text-danger error-text name_error"></span>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Permissions</label>
                @foreach($permissions as $permission)
                <div class="col-lg-6 form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="permissions[]" id="edit_permission_{{ $permission->id }}" value="{{ $permission->name }}">
                    <label class="form-check-label" for="edit_permission_{{ $permission->id }}">{{ $permission->name }}</label>
                </div>
                @endforeach
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <span type="button" class="btn btn-primary update-role">Edit Role</span>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $(('meta[name="csrf-token"]')).attr('content')
    }
  });

  $(document).ready(function(){
    $(function(){
      var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('role.index') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'permissions', name: 'permissions'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
      });
    });

    $('.add-role').on("click", function(){
      $('.error-text').text('');
      var form = $("#add-role-form")[0];
      const formData = new FormData(form);
      url = "{{ route('role.store') }}";
      $.ajax({
        url : url,
        type : "POST",
        data : formData,
        processData: false,
        contentType: false,
        success : function(res){
          if(res.success){
            alert("Asdfsdf");
          }
        },
        error: function(err){
          if(err.status === 422){            
            let errors = err.responseJSON.errors;
            $.each(errors, function(key, val){
              $('.'+key+'_error').text(val[0]);
            });
          }  
        }
      });
    });

    $('.update-role').on('click', function(){
      $('error-text').text('');
      var form = $('#edit-role-form')[0];
      var id = $('#edit_role_id').val();
      const formData = new FormData(form);
      formData.append('_method', 'PUT');
      let url = "{{ route('role.update', ':id') }}".replace(":id", id);
      $.ajax({
        url : url,
        type : "POST",
        data : formData,
        processData: false,
        contentType: false,
        success: function(res){
          if(res.success){
            $('#editrolemodal').modal('hide');
            $('.data-table').DataTable().ajax.reload();
          }
        },
        error: function(err){
          if(err.status == 422){
            let errors = err.responseJSON.errors;
            $.each(errors, function(key, val){
              $('.'+key+'_error').text(val[0]);
            });
          }
        }
      });
    });

    $(document).on('click', '.edit', function(){
      $('.form-check-input').prop('checked', false);
      let id = $(this).data('id');
      let url = "{{ route('role.edit', ':id') }}".replace(":id", id);
      $.get(url, function(res){
        $('#edit_role_id').val(res.role.id);
        $('#editrolename').val(res.role.name);
        $.each(res.role.permission, function(key, val){
          $('#edit_permission_'+val).prop('checked', true)
        });
        $('#editrolemodal').modal('show');
      });
    });

    $(document).on('click', '.delete_btn', function(){
      if(!confirm("Are you sure you want to delete this role!")) return;
      let id = $(this).data('id');
      $.ajax({
        url : "/admin/role/"+id,
        type : "DELETE",
        success : function(res){
          if(res.success){
            $('.data-table').DataTable().ajax.reload();
          }
        } 
      })
    });

  });
</script>
@endpush