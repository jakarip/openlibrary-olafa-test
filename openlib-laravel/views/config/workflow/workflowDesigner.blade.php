@extends('layouts/layoutMaster')

@section('title', __('config.workflow_designer.page.title'))

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
            z-index: 1;
        }

        .card {
            z-index: 0;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table border-top" id="table">
                <thead>
                    <tr>
                        <th width="10%">{{ __('common.action') }}</th>
                        <th>{{ __('config.workflow_designer.input.name') }}</th>
                        <th>{{ __('config.workflow_designer.input.description') }}</th>
                        <th>{{ __('config.workflow_designer.input.start_state') }}</th>
                        <th>{{ __('config.workflow_designer.input.final_state') }}</th>
                        <th>{{ __('common.updated_by') }}</th>
                        <th>{{ __('common.updated_at') }}</th>
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
                    <div class="modal-title"><i class="ti ti-forms me-2"></i>
                        {{ __('config.workflow_designer.form.title') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="frm" class="form-validate">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <!-- Card untuk Task Details -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6>Workflow</h6>
                            </div>
                            <div class="card-body">

                                <div class="form-group row mb-4">
                                    <label
                                        class="col-md-3 col-form-label">{{ __('config.workflow_task.input.name') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="inp[name]" id="name"
                                            data-rule-required="true">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label
                                        class="col-md-3 col-form-label">{{ __('config.classification.input.description') }}</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="inp[description]" id="description" rows="4"
                                            data-rule-required="true"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h6>Members</h6>
                            </div>
                            <div class="card-body">
                                <ul class="">
                                    @foreach($members as $member)
                                        <li class="list-group-item d-flex align-items-center py-0 px-1">
                                            <div class="flex-grow-1">
                                                <input class="form-check-input" style="transform: scale(0.8);" type="checkbox"
                                                    id="member_id_{{ $member->id }}" value="member_id_{{ $member->id }}"
                                                    onclick="toggleActive('member', {{ $member->id }})">
                                                <small>{{ $member->name }}</small>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h6>Document Types</h6>
                            </div>
                            <div class="card-body">
                                <label for="select2Doc" class="form-label">Document Types</label>
                                <select id="select2Doc" class="select2 form-select" multiple>
                                    @foreach ($documents as $document)
                                        <option value="{{$document->id}}">{{$document->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h6>File Types</h6>
                            </div>
                            <div class="card-body">
                                {{-- <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label">Available File Types</label>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="associated_members"><strong>Associated</strong></label>
                                                <select id="associated_members" name="associated_members[]"
                                                    class="form-control" multiple style="height: 200px;">
                                                </select>
                                            </div>


                                            <div class="col-md-2 d-flex flex-column align-items-center">
                                                <button type="button" class="btn btn-sm btn-primary mb-2"
                                                    onclick="moveToAssociated()" style="font-size: 0.8rem;">
                                                    <<< /button>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            onclick="moveToUnassociated()"
                                                            style="font-size: 0.8rem;">>></button>
                                            </div>

                                            <div class="col-md-5">
                                                <label for="unassociated_members"><strong>Unassociated</strong></label>
                                                <select id="unassociated_members" class="form-control" multiple
                                                    style="height: 200px;">
                                                    @foreach ($files as $file)
                                                    <option value="{{$file->id}}">{{$file->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Jenis anggota yang diperbolehkan untuk meminjam
                                            jenis katalog ini</small>
                                    </div>
                                </div> --}}

                                <label for="select2File" class="form-label">Available File Types</label>
                                <select id="select2File" class="select2 form-select" multiple>
                                    @foreach ($files as $file)
                                        <option value="{{$file->id}}">{{$file->name}}</option>
                                    @endforeach
                                </select>

                                {{-- <ul class="list-group list-group-flush">
                                    @foreach($files as $file)
                                    <li class="list-group-item d-flex align-items-center py-0 px-1">

                                        <div class="flex-grow-1">
                                            <small>{{ $file->name }}</small>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul> --}}
                            </div>
                        </div>

                    </form>

                    <!-- Card untuk Timestampable -->
                    <div class="card timestampable-section">
                        <div class="card-header">
                            <h6>Timestampable</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-2">
                                <label class="col-md-3 col-form-label">{{ __('common.updated_by') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="updated_by" id="updated_by" readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-md-3 col-form-label">{{ __('common.updated_at') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="updated_at" id="updated_at" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        onclick="save()">{{ __('common.save') }}</button>
                </div>
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
        let url = '{{ url('config/workflow-designer') }}';
        let selectedMembers = [];

        $(function () {
            dTable = $('.table').DataTable({
                ajax: {
                    url: url + '/dt',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'name', name: 'name', orderable: false, searchable: false },
                    { data: 'description', name: 'description', orderable: true, searchable: true },
                    { data: 'start_state_id', name: 'start_state_id', orderable: false, searchable: false },
                    { data: 'final_state_id', name: 'final_state_id', orderable: false, searchable: false },
                    { data: 'updated_by', name: 'updated_by', orderable: false, searchable: false },
                    {
                        data: 'updated_at', name: 'updated_at', orderable: false, searchable: false,
                        render: function (data) {
                            return formatDate(data); // Panggil fungsi formatDate
                        }
                    },
                ]
            });
            @if(auth()->can('config-workflow-designer.create'))
            $('.dtb').append('<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.workflow_designer.form.add_text') }}</button>');
            @endif
        });

        function add() {
            _reset();
            $(".timestampable-section").addClass('d-none'); // Sembunyikan timestamp
            $("#frmbox").modal('show');
        }

        function edit(id) {
            $.ajax({
                url: `${url}/get/${id}`,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    if (response) {
                        _reset();

                        // Isi nilai di input form
                        $('#id').val(response.id);
                        $('#name').val(response.name);
                        $('#description').val(response.description);

                        // Tampilkan bagian timestamp jika ada
                        $(".timestampable-section").removeClass('d-none');
                        $('#updated_by').val(response.updated_by || '');
                        $('#updated_at').val(formatDate(response.updated_at));

                        $("#frmbox").modal('show');
                    } else {
                        toastr.error("{{ __('common.message_error_title') }}", "{{ __('common.message_failed_load_data') }}");
                    }
                },
                error: function (xhr) {
                    console.error("Terjadi kesalahan:", xhr);
                }
            });
        }

        function save() {
            if ($("#frm").valid()) {
                const formData = new FormData();

                formData.append('id', $('#id').val());
                formData.append('name', $('#name').val());
                formData.append('description', $('#description').val());

                // Ambil data member dan pastikan dikonversi ke angka
                formData.append('members', JSON.stringify(selectedMembers.map(Number)));

                // Ambil data files dan documents
                formData.append('documents', JSON.stringify(getSelect2Values('#select2Doc')));
                formData.append('filesType', JSON.stringify(getSelect2Values('#select2File')));

                $.ajax({
                    url: url + '/save',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.status == 'success') {
                            $('#frmbox').modal('hide');
                            dTable.draw();
                            toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                        }
                    }
                });
            }
        }

        function del(id) {
            yswal_delete.fire({
                title: "{{ __('common.message_delete_prompt_title') }}",
                text: "{{ __('common.message_delete_prompt_text') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url + '/delete',
                        data: { id: id },
                        type: 'delete',
                        dataType: 'json',
                        success: function (e) {
                            if (e.status == 'success') {
                                dTable.draw();
                                toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions)
                            }
                        }
                    });
                }
            })
        }

        // function moveToAssociated() {
        //     $('#unassociated_members option:selected').each(function() {
        //         $(this).remove().appendTo('#associated_members');
        //     });
        // }

        // function moveToUnassociated() {
        //     $('#associated_members option:selected').each(function() {
        //         $(this).remove().appendTo('#unassociated_members');
        //     });
        // }

        function toggleActive(type, memberId) {
            const checkbox = document.getElementById(`member_id_${memberId}`);

            if (checkbox.checked) {
                if (!selectedMembers.includes(memberId)) {
                    selectedMembers.push(memberId); // Pastikan ID disimpan sebagai angka
                }
            } else {
                selectedMembers = selectedMembers.filter(id => id !== memberId);
            }
        }

        // Ambil dan konversi nilai dari select2 menjadi array angka (int)
        function getSelect2Values(selector) {
            return $(selector).val().map(Number);
        }

        function getCheckedValues(prefix) {
            return $(`input[id^="${prefix}"]:checked`).map(function () {
                return parseInt(this.id.replace(prefix, ''), 10);
            }).get();
        }

        function formatDate(dateString) {
            // Cek jika data kosong atau tanggal invalid
            if (!dateString || dateString === '-000001-11-30T00:00:00.000000Z') {
                return ''; // Mengembalikan string kosong untuk data tidak valid
            }

            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return ''; // Jika tanggal tidak valid, kembalikan string kosong
            }

            const day = String(date.getDate()).padStart(2, '0'); // Tambahkan nol di depan hari jika perlu
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
            const year = date.getFullYear();
            return `${day}-${month}-${year}`; // Format DD-MM-YYYY
        }

        // Reset function to clear inputs and select fields
        function _reset() {
            $('#frm')[0].reset(); // Reset form fields
            $('.select2').val(null).trigger('change'); // Reset Select2 fields
            $('#updated_by').val(''); // Clear updated_by field
            $('#updated_at').val(''); // Clear updated_at field
        }

    </script>
@endsection