<label class="form-check form-switch form-check-custom form-check-solid form-switch-sm justify-content-center">
    <input name="status" data-id="{{$row->id}}" class="form-check-input superUserStatus cursor-pointer" type="checkbox"
           value="1" {{$row->status == 0 ? '' : 'checked'}} >
    <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
</label>
