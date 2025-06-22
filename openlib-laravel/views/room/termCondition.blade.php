@extends('layouts/layoutMaster')

@section('title', __('rooms.termcondition_title'))

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
@endsection

@section('page-style')
@endsection

@section('content')
<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="10%">{{ __('common.action') }}</th>
                    <th width="5%">{{ __('rooms.termcondition_sequence') }}</th>
                    <th width="25%">{{ __('rooms.termcondition_information') }} <i class="fi fi-id"></i></th>
                    <th width="25%">{{ __('rooms.termcondition_information') }} <i class="fi fi-us"></i></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('rooms.termcondition_title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('rooms.termcondition_inputindonesia') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <div class="card-body p-0">
                                <div id="full-editor" style="height: 200px;"></div>
                                <input type="hidden" name="inp[information]" id="information" data-rule-required="true">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('rooms.termcondition_inputenglish') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <div class="card-body p-0">
                                <div id="full-editor-en" style="height: 200px;"></div>
                                <input type="hidden" name="inp[information_en]" id="information_en" data-rule-required="true">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('rooms.termcondition_sequence') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="inp[term_sequence]" id="term_sequence" data-rule-required="true">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('room/syaratketentuan') }}';
// let fullEditor, fullEditorEn;
let fullEditor = null, fullEditorEn = null;

$(function() {
    dTable = $('.table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
            { data: 'term_sequence', name: 'term_sequence', orderable: true, searchable: true },
            { data: 'information', name: 'information', orderable: true, searchable: true },
            { data: 'information_en', name: 'information_en', orderable: true, searchable: true }
        ]
    });

    @if(auth()->can('room-termcondition.create'))
        $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('rooms.termcondition_add') }}</button>`);
    @endif

    $('#frmbox').on('shown.bs.modal', function () {
        initQuillEditors();
    });

    // Inisialisasi Quill Editor
    const fullToolbar = [
        [
            { font: [] },
            { size: [] }
        ],
        ['bold', 'italic', 'underline', 'strike'],
        [
            { color: [] },
            { background: [] }
        ],
        [
            { script: 'super' },
            { script: 'sub' }
        ],
        [
            { header: '1' },
            { header: '2' },
            'blockquote',
            'code-block'
        ],
        [
            { list: 'ordered' },
            { list: 'bullet' },
            { indent: '-1' },
            { indent: '+1' }
        ],
        [{ direction: 'rtl' }],
        ['link', 'image', 'video', 'formula'],
        ['clean']
    ];

    fullEditor = new Quill('#full-editor', {
        bounds: '#full-editor',
        placeholder: '{{ __('rooms.termcondition_typesomething') }}',
        modules: {
            formula: true,
            toolbar: fullToolbar
        },
        theme: 'snow'
    });

    fullEditorEn = new Quill('#full-editor-en', {
        bounds: '#full-editor-en',
        placeholder: '{{ __('rooms.termcondition_typesomething') }}',
        modules: {
            formula: true,
            toolbar: fullToolbar
        },
        theme: 'snow'
    });
});

// Reset form dan editor
function _reset() {
    $('#frm')[0].reset();
    $('#id').val('');
    fullEditor.root.innerHTML = '';
    fullEditorEn.root.innerHTML = '';
    $('#information').val('');
    $('#information_en').val('');
}

function add() {
    _reset();
    $("#frmbox").modal('show');
}

function edit(id) {
    $.ajax({
        url: url+'/get/'+id,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset();
            $('#id').val(e.id);
            $('#term_sequence').val(e.term_sequence);

            // Set editor content
            fullEditor.root.innerHTML = e.information ?? '';
            fullEditorEn.root.innerHTML = e.information_en ?? '';

            // Set hidden input (optional, will be overwritten on save)
            $('#information').val(e.information ?? '');
            $('#information_en').val(e.information_en ?? '');

            $('#frmbox').modal('show');
        }
    });
}

function save()
{
    // Ambil data dari Quill editor dan set ke input hidden
    $('#information').val(fullEditor.root.innerHTML);
    $('#information_en').val(fullEditorEn.root.innerHTML);

    if($("#frm").valid())
    {
        let formData = new FormData($('#frm')[0]);

        $.ajax({
            url: url+'/save',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status == 'success'){
                    $('#frmbox').modal('hide');
                    dTable.draw();
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions)
                }
            }
        });
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
                url: url+'/delete',
                data: { id : id },
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        dTable.draw();
                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions)
                    }
                }
            });
        }
    })
}
</script>
@endsection
