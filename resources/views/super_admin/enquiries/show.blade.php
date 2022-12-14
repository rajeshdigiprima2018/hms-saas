@extends('layouts.app')
@section('title')
    {{ __('messages.enquiries')}}
@endsection
@section('content')
    <div class="container-fluid">
        @include('flash::message')
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <h1 class="mb-0">{{__('messages.enquiry.enquiry_details')}}</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{route('super.admin.enquiry.index')}}"
                   class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="d-flex flex-column">
            <div class="card">
                <div class="card-body">
                    @include('super_admin.enquiries.show_fields')
                </div>
            </div>
        </div>
    </div>
@endsection
