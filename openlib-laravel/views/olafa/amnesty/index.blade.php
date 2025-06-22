@extends('layouts/layoutMaster')

@section('title', 'Amnesty Denda untuk Bebas Pustaka')

@section('vendor-style')
@endsection

@section('page-style')
<style>
.highcharts-credits,
.highcharts-button {
    display: none;
}
</style>
<style>
    .select2-container {
        z-index: 9999;
    }
</style>
@endsection

@section('content')

<div class="card">

    <div class="card-datatable table-responsive" >
        <table class="dt-scrollableTable table" id="table">
            <thead>
                <tr>
                    <th width="1%">{{ __('common.action') }}</th>
                    <th width="10%">Username</th>
                    <th width="10%">Fullname</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="frmbox" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Tambah Amnesty Denda Untuk Bebas Pustaka</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm" class="form-validate" >
                @csrf
                <input type="hidden" id="hiddenInputField" name="username_id">
                <div class="modal-body">

                    <div class="card mb-3">

                        <div class="card-header">
                            <h6>Amnesty Denda</h6>
                        </div>

                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label for="TypeaheadBasic" class="col-md-3 col-form-label">Username</label>
                                <div class="col-md-9">
                                    <input id="TypeaheadBasic" class="form-control" type="text" autocomplete="off" placeholder="Cari Username..." name="username"  />
                                    <small class="text-secondary">Ketik Minimal 3 Huruf</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('vendor-script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')

<script>

let dTable = null;
let url = '{{ url('olafa/amnesty') }}';

$(function() {
    dTable = $('.table').DataTable({
        pageLength: 10,
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        },
        columns: [
            { data: 'action', name: 'action', class: 'text-center' },
            { data: 'master_data_user', name: 'master_data_user', },
            { data: 'master_data_fullname', name: 'master_data_fullname', },
        ],

    });

    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Amnesty
        </button>`)
});

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
    );

    $('#TypeaheadBasic').bind('typeahead:select', function(ev, suggestion) {
        // Update the hidden input field with the selected suggestion's id
        $('#hiddenInputField').val(suggestion.id);
    });


});

function add() {
    $('#frmbox').modal('show');
}

function save()
{
    if($("#frm").valid())
    {
        let formData = new FormData();
        formData.append('username_id', $('#hiddenInputField').val());

        $.ajax({
            url: url+'/save',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#frmbox').modal('hide'); // Tutup modal jika berhasil menyimpan
                    dTable.draw();
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                } else if (data.status === 'error') {
                    // Menampilkan alert ketika ada error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });

                    // Menutup modal walaupun ada error
                    $('#frmbox').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                // Menangani error pada saat request
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data.'
                });

                // Menutup modal walaupun ada error
                $('#frmbox').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        $('#frmbox').modal('hide');
    }
}

function del(id)
{
    yswal_delete.fire({
        title: "{{ __('common.message_delete_prompt_title') }}",
        text: "{{ __('common.message_delete_prompt_text') }}"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url + '/delete',
                data: { id: id, _token: '{{ csrf_token() }}' }, // Pastikan untuk menyertakan token CSRF
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        dTable.draw();
                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions);
                    } else {
                        // Jika ada error, tampilkan alert
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: e.message // Menampilkan pesan error dari server
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Menangani kesalahan jika AJAX gagal
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: 'Gagal menghapus data. Silakan coba lagi.'
                    });
                }
            });
        }
    });
}
</script>
@endsection
