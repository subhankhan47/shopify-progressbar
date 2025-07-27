<form id="progressBarSettingsForm" method="POST" class="p-4 shadow-sm rounded bg-white border">
    @csrf
    @method('PUT')
    <div class="row gy-4">
        <!-- Left Column -->
        <div class="col-md-6">
            <div class="border p-3 rounded bg-light-subtle">
                <h6 class="text-muted mb-3">General Settings</h6>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="top_bar_enabled" name="top_bar_enabled" {{ $settings->top_bar_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="top_bar_enabled">Enable Top Bar</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="sticky_widget_enabled" name="sticky_widget_enabled" {{ $settings->sticky_widget_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="sticky_widget_enabled">Enable Sticky Widget</label>
                </div>

                <div class="mb-3">
                    <label for="custom_message" class="form-label">Custom Message</label>
                    <input type="text" class="form-control" id="custom_message" name="custom_message" value="{{ $settings->custom_message }}">
                </div>

                <div class="mb-3">
                    <label for="completion_message" class="form-label">Completion Message</label>
                    <input type="text" class="form-control" id="completion_message" name="completion_message" value="{{ $settings->completion_message }}">
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <div class="border p-3 rounded bg-light-subtle">
                <h6 class="text-muted mb-3">Display & Animation</h6>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="home_page_show" name="home_page_show" {{ $settings->home_page_show ? 'checked' : '' }}>
                    <label class="form-check-label" for="home_page_show">Show on Homepage</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="collection_page_show" name="collection_page_show" {{ $settings->collection_page_show ? 'checked' : '' }}>
                    <label class="form-check-label" for="collection_page_show">Show on Collection Page</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="product_page_show" name="product_page_show" {{ $settings->product_page_show ? 'checked' : '' }}>
                    <label class="form-check-label" for="product_page_show">Show on Product Page</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="animation_enabled" name="animation_enabled" {{ $settings->animation_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="animation_enabled">Enable Animation</label>
                </div>

                <div class="mb-3">
                    <label for="animation_style" class="form-label">Animation Style</label>
                    <select class="form-select" id="animation_style" name="animation_style">
                        <option value="fade" {{ $settings->animation_style === 'fade' ? 'selected' : '' }}>Fade</option>
                        <option value="slide" {{ $settings->animation_style === 'slide' ? 'selected' : '' }}>Slide</option>
                        <option value="bounce" {{ $settings->animation_style === 'bounce' ? 'selected' : '' }}>Bounce</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 text-end">
        <button type="submit" class="black-btn">Save Settings</button>
    </div>
</form>


<script>
    $(document).ready(function () {
        $('#progressBarSettingsForm').on('submit', function (e) {
            e.preventDefault();
            const form = new FormData(this);
            const fields = ['top_bar_enabled', 'sticky_widget_enabled', 'home_page_show', 'collection_page_show', 'product_page_show', 'animation_enabled'];

            fields.forEach(field => {
                form.set(field, document.getElementById(field)?.checked ? 1 : 0);
            });

            $.ajax({
                url: "/progress-bar-settings/update",
                method: "POST",
                data: form,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (response) {
                    if (response.success) {
                        showToast(app, response.message);
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    showToast(app, 'An error occurred while saving settings.', true);
                }
            });
        });
    });
</script>
