@extends('layouts.app')
@section('title')
    {{ __('messages.visitor.new') }}
@endsection
@section('page_css')
{{--    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">--}}
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-7">
            <h1 class="mb-0">@yield('title')</h1>
            <a href="{{ route('visitors.index') }}"
               class="btn btn-outline-primary">{{ __('messages.common.back') }}
            </a>
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
                    {{ Form::hidden('visitorUrl', route('visitors.index'), ['class' => 'visitorUrl']) }}
                    {{ Form::hidden('downloadDocumentUrl', url('visitor-download'), ['class' => 'downloadDocumentUrl']) }}
                    {{ Form::hidden('documentError', __('messages.expense.document_error'), ['id' => 'documentError']) }}
                    {{ Form::hidden('utilsScript', asset('assets/js/int-tel/js/utils.min.js'), ['class' => 'utilsScript']) }}
                    {{ Form::hidden('isEdit', false, ['class' => 'isEdit']) }}
                    {{ Form::hidden('defaultDocumentImageUrl', asset('assets/img/default_image.jpg'), ['class' => 'defaultDocumentImageUrl']) }}
                    {{ Form::open(['route' => 'visitors.store','id' => 'createVisitorForm','files' => true]) }}

                    @include('visitors.fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
{{--let visitorUrl = "{{ route('visitors.index') }}";--}}
{{--let downloadDocumentUrl = "{{ url('visitor-download') }}";--}}
{{--let documentError = "{{__('messages.expense.document_error')}}";--}}
{{--let utilsScript = "{{asset('assets/js/int-tel/js/utils.min.js')}}";--}}
{{--let isEdit = false;--}}
{{--let defaultDocumentImageUrl = "{{ asset('assets/img/default_image.jpg') }}";--}}
{{--    <script src="{{ mix('assets/js/visitors/create-edit.js') }}"></script>--}}
{{--    <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>--}}

