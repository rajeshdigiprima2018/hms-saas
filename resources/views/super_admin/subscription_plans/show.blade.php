@extends('layouts.app')
@section('title')
    {{ __('messages.subscription_plans.view_subscription_plan')}}
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{route('super.admin.subscription.plans.edit', $subscriptionPlan->id)}}"
                   class="btn btn-primary me-4">{{ __('messages.common.edit') }}</a>
                <a href="{{route('super.admin.subscription.plans.index')}}"
                   class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')

    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="card">
                <div class="card-body">
                    @include('super_admin.subscription_plans.show_fields')
                </div>
            </div>
        </div>
    </div>

@endsection
