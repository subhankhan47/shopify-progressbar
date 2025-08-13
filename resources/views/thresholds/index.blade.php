@extends('shopify-app::layouts.default')

@section('content')
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 top-heading">Thresholds</h4>
                            <span>Here you can manage created thresholds</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a target="_blank" class="button white-btn d-none" id="activate-storefront-btn">
                                Activate on Store
                            </a>
                            @if($thresholds->count() < 1)
                                <a onclick="navigation('/thresholds/create')" class="black-btn">Add Threshold</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($thresholds->isEmpty())
                            <strong>
                                <p class="text-center text-dark">No thresholds found. Click "Add Threshold" to add one.</p>
                            </strong>
                        @else
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="text-danger">#</th>
                                    <th>Amount</th>
                                    <th>Reward Type</th>
                                    <th>Product Variant</th>
                                    <th>Priority</th>
                                    <th>Auto Add</th>
{{--                                    <th>Shipping Regions</th>--}}
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($thresholds as $threshold)
                                    <tr id="threshold-row-{{ $threshold->id }}">
                                        <td class="text-danger">{{ $loop->iteration }}</td>
                                        <td>{{ $threshold->amount }}</td>
                                        <td>{{ $threshold->reward_type }}</td>
                                        <td>
                                            @if ($threshold->reward_type == 'free_product' && $threshold->product_id)
                                                {{ $titles['ProductVariant/' . $threshold->product_id] ?? $threshold->product_id }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $threshold->priority }}</td>
                                        <td>
                                            @if ($threshold->auto_add_product)
                                                <div class="badge bg-success">Yes</div>
                                            @else
                                                <div class="badge bg-secondary">No</div>
                                            @endif
                                        </td>
{{--                                        <td>--}}
{{--                                            @if (!empty($threshold->shipping_regions))--}}
{{--                                                {{ implode(', ', $threshold->shipping_regions) }}--}}
{{--                                            @else--}}
{{--                                                <span class="text-muted">-</span>--}}
{{--                                            @endif--}}
{{--                                        </td>--}}
                                        <td>
                                            <a onclick="navigation('/thresholds/{{ $threshold->id }}/edit')" class="btn btn-sm btn-warning">Edit</a>
                                            <button type="button" class="btn btn-sm btn-danger delete-threshold" data-id="{{ $threshold->id }}">
                                                Delete
                                            </button>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.delete-threshold', function () {
            const id = $(this).data('id');
            const row = $('#threshold-row-' + id);
            if (!confirm('Delete this threshold?')) return;
            $.ajax({
                url: '/thresholds/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    row.remove();
                    showToast(app, response.message);
                },
                error: function (xhr) {
                    let message = 'Failed to delete threshold.';
                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(app, message, true);
                }
            });
        });

    </script>
@endsection
