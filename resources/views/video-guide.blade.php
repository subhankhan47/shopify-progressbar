@extends('shopify-app::layouts.default')

@section('content')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mb-4 text-center">📹 Video Guide</h1>

                <!-- Video Section -->
                <div class="mb-5">
                    <video class="w-100 rounded shadow-sm" controls>
                        <source src="{{ asset('videos/guideVideo.mp4') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <!-- Support Section -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3">Need Help? Contact Support</h4>
                        <p>
                            📞 <strong>WhatsApp Support:</strong>
                            <a href="https://wa.me/447366278042" target="_blank" class="text-primary">
                                +447366278042
                            </a>
                        </p>
                        <p>
                            📧 <strong>Email Support:</strong>
                            <a href="mailto:support@sfaddons.com" class="text-primary">
                                support@sfaddons.com
                            </a>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
