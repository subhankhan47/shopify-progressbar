@extends('shopify-app::layouts.default')

@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4 text-center">📹 Video Guide</h1>

            <!-- Video Section -->
            <div class="ratio ratio-16x9 mb-5">
                <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID"
                        title="Video Guide"
                        allowfullscreen>
                </iframe>
            </div>

            <!-- Support Section -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">Need Help? Contact Support</h4>
                    <p>
                        📞 <strong>WhatsApp Support:</strong>
                        <a href="https://wa.me/923185423031" target="_blank" class="text-primary">
                            +92 318 5423031
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

