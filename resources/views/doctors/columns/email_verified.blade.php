<div class="text-center">
    <label class="form-check form-switch form-check-custom form-check-solid form-switch-sm justify-content-center">
        <input name="status" data-id="{{$row->user->id}}" class="form-check-input is-verified" type="checkbox" value="1"
                {{$row->user->email_verified_at == null
                         ? ''
                         : 'checked disabled'}}>
        <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
    </label>
</div>
