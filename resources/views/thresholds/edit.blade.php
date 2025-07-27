@extends('shopify-app::layouts.default')

@section('content')
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center">
                        <span>
                            <a onclick="navigation('/thresholds')" class="btn btn-light">
                                <i class="fa fa-arrow-circle-left"></i>
                            </a>
                        </span>
                        <h4 class="mb-0 top-heading">Edit Threshold</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('thresholds.update', $threshold->id) }}" enctype="multipart/form-data">
                            @csrf
                            @include('thresholds.form', ['threshold' => $threshold])
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="black-btn">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
