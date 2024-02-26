<span class="form-switch mt-1">
    <input class="form-check-input updateStatusBtn" type="checkbox" id="flexSwitchCheckChecked" data-id="{{$model->id}}"
           data-status="{{$model->status}}"
    {{ $model->status === 1 ? "checked" : "" }}
    >
</span>