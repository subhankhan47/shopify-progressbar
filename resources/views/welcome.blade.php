@extends('shopify-app::layouts.default')

@section('content')
    <div class="container my-5">
        <div class="card shadow rounded">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">Progress Bar Configuration</h4>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs custom-tabs" id="settingsTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="bar-settings-tab" data-bs-toggle="tab" href="#bar-settings" role="tab">Progress Bar Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="styles-tab" data-bs-toggle="tab" href="#styles" role="tab">Bar Styles</a>
                    </li>
                </ul>

                <div class="tab-content pt-4" id="settingsTabContent">
                    <div class="tab-pane fade show active" id="bar-settings" role="tabpanel">
                        @include('partials.progress-bar-settings')
                    </div>
                    <div class="tab-pane fade" id="styles" role="tabpanel">
                        @include('partials.progress-bar-styles')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
