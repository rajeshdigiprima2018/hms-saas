<label class="form-check form-switch form-check-custom form-check-solid form-switch-sm">
    <input name="status" data-id="{{$row->id}}" class="form-check-input nurseStatus" type="checkbox"
           value="1" {{$row->user->status == 0 ? '' : 'checked'}} >
    <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
</label>
