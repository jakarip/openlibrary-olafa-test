@extends('layouts/layoutMaster')

@section('title', 'Delete File')

@section('vendor-style')
@endsection

@section('page-style')
<style>
</style>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0"> Delete File</h5>
        <button class="btn btn-openlib-red btn-md me-2" onclick="window.history.back()"><i class="ti ti-arrow-left ti-sm me-1"></i> Kembali</button>
    </div>
    <div class="card-body">
        <table id="table" class="table table-hover">
            <thead>
                <tr>
                    <th width="85%">Name</th>
                    <th width="5%">Extension</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>


@endsection

@section('vendor-script')
@endsection

@section('page-script')

<script>
let url = '{{ url('olafa/bebas-pustaka/delete_file') }}';
let dTable = null;


$(function() {
    var id = {{$id}};

    dTable = $('.table').DataTable({
        pageLength: 10,
        searching: false,
        buttons: [],
        lengthChange: false,
        paging: false,
        info: false,
        ajax: {
            url: url+'/dt_delete_file',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id 
            },
        },
        columns: [
            { data: 'title', name: 'title', },
            { data: 'extension', name: 'extension', },
            { data: 'action', name: 'action', class: 'text-center' },
        ],
        
    });
});

function del(id)
{
    yswal_delete.fire({
        title: "{{ __('common.message_delete_prompt_title') }}",
        text: "{{ __('common.message_delete_prompt_text') }}"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                data: { id : id },
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions)
                        $('.table').DataTable().ajax.reload();
                    }
                }
            });
        }
    })
} 



</script>
@endsection