@extends('layouts/layoutMaster')

@section('title', 'Delete Document / File')

@section('vendor-style')
@endsection

@section('page-style')
<style>
</style>
@endsection

@section('content')

<div class="card">

    <div class="card-header">
        <h4 class="card-title">Delete Document / File</h4>
    </div>

    <div class="card-body">
        <div class="col-lg mb-4">
            <label for="TypeaheadBasic" class="form-label">Search username / nim / nama lengkap mahasiswa :</label>
            <input id="TypeaheadBasic" class="form-control typeahead" type="text" autocomplete="off" placeholder="Ketik disini..." />
        </div>

        <table class="dt-scrollableTable table" id="tabel">
            <thead>
                <tr>
                    <th width="1%">{{ __('common.action') }}</th>
                    <th width="15%">Nama</th> 
                    <th width="30%">Judul</th> 
                    <th width="10%">Status</th> 
                    <th width="10%">Jumlah File</th> 
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


$(document).ready(function() {
    let ajaxRequest;

    $('#TypeaheadBasic').typeahead(
        {
            hint: false,
            highlight: true,
            minLength: 3,
            
        },
        {
            name: 'members',
            source: function(query, syncResults, asyncResults) {
            // Synchronous suggestions (if any)
            syncResults([]);

            // Clear the previous timeout if it exists
            if (ajaxRequest) {
                clearTimeout(ajaxRequest);
            }

            // Set a new timeout
            ajaxRequest = setTimeout(function() {
                    // Asynchronous suggestions
                    $.ajax({
                        url: url + '/autodata',
                        type: 'POST',
                        data: { q: query },
                        dataType: 'json',
                        success: function(data) {
                            asyncResults($.map(data, function(item) {
                                return item;
                            }));
                        },
                    });
                }, 500); 
            },
            display: function(item) {
                return item.name;
            },
        }
    ).on('typeahead:select', function(event, item) {
        // Destroy the existing DataTable instance
        if ($.fn.DataTable.isDataTable('.table')) {
            $('.table').DataTable().clear().destroy();
        }
        $('.table').DataTable({
            searching: false,
            buttons: [],
            lengthChange: false,
            paging: false,
            info: false,
            ajax: {
                url: url+'/dt',
                type: 'post',
                data: {
                    selectedItem: item // Kirim data item yang dipilih
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            columns: [
                { data: 'action', name: 'action', class: 'text-center' },
                { data: 'master_data_fullname', name: 'master_data_fullname', },
                { data: 'title', name: 'title', },
                { data: 'name', name: 'name', },
                { data: 'jml', name: 'jml', },
            ],
            
        });
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
                url: url+'/delete_document',
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