@extends('layouts.layoutMaster')

@section('title', 'List Dokumen')

@section('vendor-style')
@endsection

@section('page-style')
<style>
</style>
@endsection

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('alert'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('alert') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<div class="card">
    <!--Search Form -->
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="workflow" class="form-label">{{ __('documents.workflow') }}:</label>
                            <select id="workflow" class="select2 form-select form-select-md">
                                <option value="">{{ __('documents.all') }}</option>
                                @foreach($workflows as $workflow)
                                    <option value="{{ $workflow->id }}">{{ $workflow->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="type" class="form-label">{{ __('documents.library_type') }}:</label>
                            <select id="type" class="select2 form-select form-select-md">
                                <option value="">{{ __('documents.all_document_types') }}</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="status" class="form-label">{{ __('documents.status') }}:</label>
                            <select id="status" class="select2 form-select form-select-md">
                                <option value="">{{ __('documents.all') }}</option>
                                @foreach($statuses as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="dates_acceptance_option" class="form-label">{{ __('documents.created_date') }}:</label>
                            <select id="dates_acceptance_option" class="select2 form-select form-select-md">
                                <option value="all">{{ __('documents.all_created_dates') }}</option>
                                <option value="date">{{ __('documents.created_date_range') }}</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 d-none" id="dates_acceptance_div">
                            <label for="dates_acceptance" class="form-label">{{ __('documents.created_date_range') }}:</label>
                            <input type="text" id="dates_acceptance" class="form-control"/>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-check">
                                <input type="checkbox" name="attribute" id="attribute" value="1" class="form-check-input">
                                <label for="attribute" class="form-check-label">
                                    <strong>{{ __('documents.i_can_edit') }}</strong>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-check">
                                <input type="checkbox" name="onlyforme" id="onlyforme" value="1" class="form-check-input">
                                <label for="onlyforme" class="form-check-label">
                                    <strong>{{ __('documents.only_for_me') }}</strong>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-end col-auto">
                            <button type="button" id="filter" class="btn btn-openlib-red">{{ __('documents.filter') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <hr class="mt-0">

    <div class="card-datatable text-wrap">
        <table class="table table-bordered table-striped no-footer dataTable dt-select-no-highlight" id="table">
            <thead>
                <tr>
                    <th class="text-center" width="10%">{{ __('documents.creator') }}</th>
                    <th class="text-center" width="10%">{{ __('documents.workflow') }}</th>
                    <th class="text-center" width="25%">{{ __('documents.title') }}</th>
                    <th class="text-center" width="15%">{{ __('documents.subject') }}</th>
                    <th class="text-center" width="10%">{{ __('documents.type') }}</th>
                    <th class="text-center" width="15%">{{ __('documents.state') }}</th>
                    <th class="text-center" width="10%">{{ __('documents.action') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('document') }}';

$(function() {
    dTable = $('#table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url + '/Json',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.workflow = $('#workflow').val();
                d.type = $('#type').val();
                d.status = $('#status').val();
                d.attribute = $('#attribute:checked').val() || '';
                d.onlyforme = $('#onlyforme:checked').val() || '';
                d.dates_acceptance_option = $('#dates_acceptance_option').val();
                d.dates_acceptance = $('#dates_acceptance').val();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown); 
                console.error('Response:', jqXHR.responseText); 
            },
        },
        columns: [
            { data: 'creator', name: 'creator', orderable: true, searchable: true },
            { data: 'workflow', name: 'workflow', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'subject', name: 'subject', orderable: true, searchable: true },
            { data: 'type', name: 'type', orderable: true, searchable: true },
            { data: 'state', name: 'state', orderable: true, searchable: true },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        responsive: false,
        scrollX: true,
    });
    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="window.location.href='{{ route('dokumen.add') }}'">
    <i class="ti ti-file-plus ti-sm me-1"></i> {{ __('documents.add_data') }}</button>`);
});

$('#filter').on('click', function(event) {
    event.preventDefault();
    $('#loading').show(); 
    dTable.ajax.reload(function() {
        $('#loading').hide(); 
    }, false);
});

$(document).ready(function() {
    $('#dates_acceptance_option').on('change', function() {
        var datesAcceptanceDiv = $('#dates_acceptance_div');
        if ($(this).val() === 'date') {
            datesAcceptanceDiv.removeClass('d-none');
        } else {
            datesAcceptanceDiv.addClass('d-none');
        }
    });

    $('#dates_acceptance').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY'
        },
        showDropdowns: true,
        opens: 'left',
        applyClass: 'bg-primary-600',
        cancelClass: 'btn-light'
    });

    $('#workflow').on('change', function() {
        $.ajax({
            url: url + '/getknowledgetype',
            type: "POST",
            data: {
                'id': $(this).val(),
                '_token': "{{ csrf_token() }}"
            },
            dataType: "JSON",
            beforeSend: function() {
                $('#loading').show();
            },
            complete: function() {
                $('#loading').hide();
            },
            success: function(response) {
                $("#type").html('<option value="">Semua Jenis Pustaka</option>');
                if (response.knowledge_types && response.knowledge_types.length > 0) {
                    $.each(response.knowledge_types, function(index, value) {
                        $("#type").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
                $('#type').select2();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                console.error("Response:", jqXHR.responseText);
            }
        });
    });
});
</script>
@endsection