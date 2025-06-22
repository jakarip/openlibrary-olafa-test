@extends('layouts/layoutMaster')

@section('title', 'Generate E-Proceeding')

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
    <div class="card-header sticky-element bg-label-secondary d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Generate E-Proceeding</h5>
        <div class="action-btns">
            <button type="button" class="btn btn-primary waves-effect waves-light me-3" onclick="">Generate</button>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="form-group row mb-4">
            <label class="col-md-2 col-form-label">Pilih Edisi E-Proceeding</label>
            <div class="col-md-12">
                <select name="eprocEdition" id="eprocEdition" class="select2 form-select">
                    @foreach($eprcocEditions as $edition)
                        <option value="{{ $edition->eproc_edition_id }}">{{ $edition->nama }}</option>
                    @endforeach
                </select>
            </div>                            
        </div>                

        <div class="form-group row mb-4">
            <label class="col-md-2 col-form-label">Pilih List E-Proceeding</label>
            <div class="col-md-12">
                <select name="eprocList" id="eprocList" class="select2 form-select">
                    @foreach($eprocLists as $list)
                        <option value="{{ $list->list_id }}">{{ $list->list_name }}</option>
                    @endforeach
                </select>
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
let url = '{{ url('olafa/e-proceeding-generate') }}';


</script>
@endsection