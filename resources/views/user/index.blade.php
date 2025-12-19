@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Users</h1>    
    <div class="row">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th width="100px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editusermodal" tabindex="-1" aria-labelledby="editusermodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editusermodalLabel">Edit User</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-user-form">
            @csrf
            <input type="hidden" id="edit_user_id">
            <div class="mb-3">
                <label for="name" class="form-label">User Name</label>
                <input type="text" class="form-control" id="editusername" name="name">
                <span class="text-danger error-text name_error"></span>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">User Email</label>
                <input type="email" class="form-control" id="edituseremail" name="email">
                <span class="text-danger error-text email_error"></span>
            </div>    
            <div class="mb-3">
                @foreach($role as $data)
                <div class="col-lg-6 form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="role[]" id="edit_user_{{ $data->id }}" value="{{ $data->name }}">
                    <label class="form-check-label" for="edit_user_{{ $data->id }}">{{ $data->name }}</label>
                </div>
                @endforeach
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <span type="button" class="btn btn-primary update-user">Edit User</span>
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
            processing : true,
            serverSide : true,
            ajax : "{{ route('user.index') }}",
            columns : [
                {data : 'id', name : 'id'},
                {data : 'name', name : 'name'},
                {data : 'email', name : 'email'},
                {data : 'action', name : 'action', orderable : false, searchable : false},
            ]
        });
    });

    $(document).on('click', '.edit', function(){
        let id = $(this).data('id');
        $('.error-text').text('');
        let url = "{{ route('user.edit', ':id') }}".replace(':id', id);
        $.get(url, function(data){
            $('#edit_user_id').val(data.user.id);
            $('#editusername').val(data.user.name);
            $('#edituseremail').val(data.user.email);
            $('#editusermodal').modal('show');
        });
    });

    $('.update-user').on('click', function(){
      $('error-text').text('');
      var form = $('#edit-user-form')[0];
      var id = $('#edit_user_id').val();
      const formData = new FormData(form);
      formData.append('_method', 'PUT');
      let url = "{{ route('user.update', ':id') }}".replace(":id", id);
      $.ajax({
        url : url,
        type : "POST",
        data : formData,
        processData: false,
        contentType: false,
        success: function(res){
          if(res.success){
            $('#editusermodal').modal('hide');
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
})
</script>
@endpush