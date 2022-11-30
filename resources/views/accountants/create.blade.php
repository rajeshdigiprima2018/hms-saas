@extends('layouts.app')
@section('title')
    {{__('messages.accountant.new_accountant')}}
@endsection
@section('page_css')
{{--    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">--}}
@endsection
@section('header_toolbar')
        <div class="container-fluid">
            <div class="d-md-flex align-items-center justify-content-between mb-7">
                <h1 class="mb-0">@yield('title')</h1>
                <a href="{{ route('accountants.index') }}"
                   class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="row">
                <div class="col-12">
                    @include('layouts.errors')
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'accountants.store', 'files' => 'true', 'id' => 'createAccountantForm']) }}

                    @include('accountants.fields')

                    {{ Form::close() }}
                </div>
            </div>
            {{ Form::hidden('utilScript', asset('assets/js/int-tel/js/utils.min.js'), ['class' => 'utilScript']) }}
            {{ Form::hidden('isEdit', false, ['class' => 'isEdit']) }}
            {{ Form::hidden('defaultAvatarImageUrl', asset('assets/img/avatar.png'), ['id' => 'defaultAccountantAvatarImageUrl']) }}
        </div>
    </div>
@endsection
{{--let utilsScript = "{{asset('assets/js/int-tel/js/utils.min.js')}}";--}}
{{--let isEdit = false;--}}
{{--let defaultAvatarImageUrl = "{{ asset('assets/img/avatar.png') }}";--}}
{{--    <script src="{{ mix('assets/js/accountants/create-edit.js') }}"></script>--}}
{{--    <script src="{{ mix('assets/js/custom/add-edit-profile-picture.js') }}"></script>--}}
{{--    <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>--}}


