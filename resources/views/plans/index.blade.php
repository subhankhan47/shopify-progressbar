@extends('shopify-app::layouts.default')

@section('content')
    <div class="container-fluid mt-3 mb-3">
        <!-- Back Button -->
        <div class="d-flex justify-content-between mb-2">
            <div class="d-flex align-items-center">
            <span>
                <a onclick="navigation('')" class="btn btn-sm btn-light">
                    <i class="fa fa-arrow-circle-left"></i>
                </a>
            </span>
                <h6 class="mb-0 mx-1">Back</h6>
            </div>
        </div>
        <h3 class="plan-page-title text-center">Choose Your Plan</h3>
        <p class="plan-page-desc text-center">Flexible plans to match your business needs—explore now!</p>

        <div class="tab-content text-center">
            <div class="plans-wrapper">
                @foreach ($plans as $plan)
                    @if ($plan->interval == 'EVERY_30_DAYS')
                        <div class="card {{ $currentPlan == $plan->id ? 'plan-active' : '' }}">
                            <div class="card-body position-relative">
                                <div class="plan-inner">
                                    <h6 class="plan-title">{{ $plan->name }}</h6>
                                    <h3 class="plan-pricing">{!! '$' . $plan->price . ' <span class="plan-duration">/ month</span>' !!}</h3>
                                    <div class="plan-feature-wrapper">
                                        <div class="plan-features">
                                           <span>Top Progressbar</span>
                                           <span>Drawer Icon</span>
                                           <span>Drawer Progressbar (Vertical Or Horizontal)</span>
                                           <span>Free Product Gift</span>
{{--                                           <span>Free Shipping</span>--}}
                                        </div>
                                    </div>
                                    <div class="plan-btn-wrapper">
                                        <a href="{{ URL::tokenRoute('billing', ['plan' => $plan->id, 'shop' => Auth::user()->name]) }}"
                                           class="black-btn {{ $currentPlan == $plan->id ? 'button-disabled' : '' }}">{{ $currentPlan == $plan->id ? 'Currently Activated Plan' : 'Activate Plan' }}</a>
                                    </div>
                                </div>
                                <div class="plan-trial">{{ $plan->terms }}</div>
                            </div>
                        </div>
                    @else
                        <div class="card {{ $currentPlan == $plan->id ? 'plan-active' : '' }}">
                            <div class="card-body position-relative">
                                <div class="plan-inner">
                                    <h6 class="plan-title">{{ $plan->name }}</h6>
                                    <h3 class="plan-pricing">
                                        <del>$47.88</del>
                                        ${{ $plan->price }} <span class="plan-duration">/ year</span>
                                        <span class="plan-discount">
                                        (27% OFF)</span>
                                    </h3>
                                    <div class="plan-feature-wrapper">
                                        <div class="plan-features">
                                            <span>Top Progressbar</span>
                                            <span>Drawer Icon</span>
                                            <span>Drawer Progressbar (Vertical Or Horizontal)</span>
                                            <span>Free Product Gift</span>
{{--                                            <span>Free Shipping</span>--}}
                                        </div>
                                    </div>
                                    <div class="plan-btn-wrapper">
                                        <a href="{{ URL::tokenRoute('billing', ['plan' => $plan->id, 'shop' => Auth::user()->name]) }}"
                                           class="black-btn {{ $currentPlan == $plan->id ? 'button-disabled' : '' }}">{{ $currentPlan == $plan->id ? 'Currently Activated Plan' : 'Activate Plan' }}</a>
                                    </div>
                                </div>
                                <div class="plan-trial">{{ $plan->terms }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
