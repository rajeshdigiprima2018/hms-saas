@if(!$row->is_super_admin_default)
<div class="d-flex align-items-center">
    <a href="{{ route('admins.edit',$row->id)}}" title="{{__('messages.common.edit') }}"
       class="btn px-1 text-primary fs-3 ps-0">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <a href="javascript:void(0)" title="{{__('messages.common.delete')}}" data-id="{{ $row->id }}"
       class="admin-delete-btn btn px-2 text-danger fs-3 ps-0">
        <i class="fa-solid fa-trash"></i>
    </a>
</div>
@endif
