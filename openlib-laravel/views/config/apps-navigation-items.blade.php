@foreach($items as $item)
<li class="dd-item" data-id="{!! $item->id !!}">
    <div class="pull-right item_actions">
        <div class="btn btn-sm btn-danger float-end" onclick="del('{!! $item->id !!}')">
            <i class="ti ti-trash me-1"></i> {{ __('common.delete') }}
        </div>
    </div>

    <div class="dd-handle">{!! $item->title !!}</div>

    @if($item->hasChildren())
        <ol class="dd-list">
            @include('config.apps-navigation-items', ['items' => $item->children()])
        </ol>
    @endif
</li>
@endforeach