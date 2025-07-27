@php
    $isEdit = isset($threshold);
    $rewardType = old('reward_type', $threshold->reward_type ?? '');
@endphp

<div class="mb-3">
    <label for="amount" class="form-label">Amount</label>
    <input type="number" step="0.01" class="form-control" name="amount" id="amount"
           value="{{ old('amount', $threshold->amount ?? '') }}" placeholder="Enter amount" required>
</div>

<div class="mb-3">
    <label for="reward_type" class="form-label">Reward Type</label>
    <select name="reward_type" id="reward_type" class="form-select" required>
        <option value="">-- Select Reward Type --</option>
        <option value="free_shipping" {{ $rewardType === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
        <option value="free_product" {{ $rewardType === 'free_product' ? 'selected' : '' }}>Free Product</option>
    </select>
</div>

{{-- Product Select (Free Product only) --}}
<div class="mb-3" id="product_select_wrapper" style="display: none;">
    <label for="product_id" class="form-label">Select Product</label>
    <select name="product_id" id="product_id" class="form-select" style="width: 100%;">
        @php
            $selectedProductId = old('product_id', $threshold->product_id ?? null);
        @endphp

        @if($selectedProductId)
            <option value="{{ $selectedProductId }}" selected>
                {{ $title ?? 'Product #' . $selectedProductId }}
            </option>
        @endif
    </select>
</div>


{{-- Auto Add Product --}}
<div class="form-check form-switch mb-3" id="auto_add_wrapper" style="display: none;">
    <input type="checkbox" class="form-check-input" name="auto_add_product" id="auto_add_product"
        {{ old('auto_add_product', $threshold->auto_add_product ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="auto_add_product">Auto Add Product</label>
</div>

{{-- Shipping Regions (Free Shipping only) --}}
<div class="mb-3" id="shipping_regions_wrapper" style="display: none;">
    <label for="shipping_regions" class="form-label">Shipping Regions</label>
    <select name="shipping_regions[]" id="shipping_regions" class="form-select" multiple="multiple" style="width: 100%;">
        @php
            $selectedRegions = old('shipping_regions', $threshold->shipping_regions ?? []);
        @endphp

        @foreach ($selectedRegions as $region)
            <option value="{{ $region }}" selected>{{ $region }}</option>
        @endforeach
    </select>
</div>


{{-- Priority --}}
<div class="mb-3">
    <label for="priority" class="form-label">Priority</label>
    <input type="number" class="form-control" name="priority" id="priority"
           value="{{ old('priority', $threshold->priority ?? 0) }}" required>
</div>

    <script>
        $(document).ready(function () {
            function toggleRewardTypeFields() {
                var value = $('#reward_type').val();

                if (value === 'free_product') {
                    $('#product_select_wrapper').show();
                    $('#auto_add_wrapper').show();
                    $('#shipping_regions_wrapper').hide();
                } else if (value === 'free_shipping') {
                    $('#product_select_wrapper').hide();
                    $('#auto_add_wrapper').hide();
                    $('#shipping_regions_wrapper').show();
                } else {
                    $('#product_select_wrapper').hide();
                    $('#auto_add_wrapper').hide();
                    $('#shipping_regions_wrapper').hide();
                }
            }

            // Bind change event
            $('#reward_type').on('change', toggleRewardTypeFields);

            // Initial call
            toggleRewardTypeFields();


            // Init Select2 for product search
            $('#product_id').select2({
                placeholder: 'Search product...',
                ajax: {
                    url: "/search-products",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term || ''
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                allowClear: true
            });

            let isSubmitting = false;

            $('form').on('submit', function (e) {
                e.preventDefault();

                if (isSubmitting) return;
                isSubmitting = true;

                const form = $(this);
                const submitBtn = form.find('[type="submit"]');
                submitBtn.prop('disabled', true).text('Submitting...');
                const formData = new FormData(this);
                const method = form.find('input[name="_method"]').val() || 'POST';
                const url = form.attr('action');
                const autoAddCheckbox = document.getElementById('auto_add_product');

                formData.set('auto_add_product', autoAddCheckbox?.checked ? 1 : 0);

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        showToast(app, response.message);
                        navigation('/thresholds');
                    },
                    error: function (xhr) {
                        let message = 'Something went wrong.';
                        if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        showToast(app, message, true);
                        submitBtn.prop('disabled', false).text('submit');
                    },
                    complete: function () {
                        isSubmitting = false;
                    }
                });
            });

        });
    </script>
<script>
    $.getJSON("/data/countries.json", function(data) {
        const countries = data.map(country => ({
            id: country.code,
            text: country.name
        }));

        $('#shipping_regions').select2({
            data: countries,
            placeholder: 'Select shipping regions',
            width: 'resolve',
            multiple: true
        });
    });
</script>


