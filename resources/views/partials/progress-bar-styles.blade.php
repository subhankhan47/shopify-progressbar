<div class="row g-3">
    {{-- Progress Bar Style --}}
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Progress Bar Style</div>
            <div class="card-body">
                <form id="bar-style-form" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Filled Progress Color</label>
                        <input type="text" class="form-control color-picker" name="filled_progress_color" value="{{ $progressBarStyle?->filled_progress_color ?? '#4caf50' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Background Color</label>
                        <input type="text" class="form-control color-picker" name="bg_color" value="{{ $progressBarStyle?->bg_color ?? '#f1f1f1' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message Position</label>
                        <select class="form-select" name="message_position">
                            <option value="top" {{ ($progressBarStyle?->message_position ?? 'bottom') === 'top' ? 'selected' : '' }}>Top</option>
                            <option value="bottom" {{ ($progressBarStyle?->message_position ?? 'bottom') === 'bottom' ? 'selected' : '' }}>Bottom</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Font Color</label>
                        <input type="text" class="form-control color-picker" name="font_color" value="{{ $progressBarStyle?->font_color ?? '#000000' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Font Size</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="font_size" value="{{ $progressBarStyle?->font_size ?? 14 }}">
                            <span class="input-group-text">px</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Border Radius</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="border_radius" value="{{ $progressBarStyle?->border_radius ?? 5 }}">
                            <span class="input-group-text">px</span>
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show_products_in_bar" id="productsInBarCheck" {{ ($progressBarStyle?->show_products_in_bar ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="productsInBarCheck">Show Products in Bar</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Widget Style --}}
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Widget Style</div>
            <div class="card-body">
                <form id="widget-style-form" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label>Position</label>
                        <select class="form-select" name="position">
                            <option value="">Select Position</option>
                            <option value="center-left" {{ ($progressWidgetStyle?->position ?? 'center-right') === 'center-left' ? 'selected' : '' }}>Center Left</option>
                            <option value="center-right" {{ ($progressWidgetStyle?->position ?? 'center-right') === 'center-right' ? 'selected' : '' }}>Center Right</option>
                            <option value="bottom-left" {{ ($progressWidgetStyle?->position ?? 'center-right') === 'bottom-left' ? 'selected' : '' }}>Bottom Left</option>
                            <option value="bottom-right" {{ ($progressWidgetStyle?->position ?? 'center-right') === 'bottom-right' ? 'selected' : '' }}>Bottom Right</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Shape</label>
                        <select class="form-select" name="widget_shape">
                            <option value="bottom-left" {{ ($progressWidgetStyle?->widget_shape ?? 'rounded') === 'rounded' ? 'selected' : '' }}>Rounded</option>
                            <option value="bottom-right" {{ ($progressWidgetStyle?->widget_shape ?? 'rounded') === 'square' ? 'selected' : '' }}>Square</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Background Color</label>
                        <input type="text" class="form-control color-picker" name="bg_color" value="{{ $progressWidgetStyle?->bg_color ?? '#ffffff' }}">
                    </div>

                    <div class="mb-3">
                        <label>Width</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="width" value="{{ $progressWidgetStyle?->width ?? 60 }}">
                            <span class="input-group-text">px</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Height</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="height" value="{{ $progressWidgetStyle?->height ?? 60 }}">
                            <span class="input-group-text">px</span>
                        </div>
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="open_drawer" id="OpenDrawerCheck" {{ ($progressWidgetStyle?->open_drawer ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="open_drawer">Open Drawer</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Drawer Style --}}
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Drawer Style</div>
            <div class="card-body">
                <form id="drawer-style-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Filled Progress Color</label>
                        <input type="text" class="form-control color-picker" name="filled_progress_color" value="{{ $progressDrawerStyle?->filled_progress_color ?? '#4caf50' }}">
                    </div>

                    <div class="mb-3">
                        <label>Background Color</label>
                        <input type="text" class="form-control color-picker" name="bg_color" value="{{ $progressDrawerStyle?->bg_color ?? '#ffffff' }}">
                    </div>

                    <div class="mb-3">
                        <label>Layout</label>
                        <select class="form-select" name="layout">
                            <option value="horizontal" {{ ($progressDrawerStyle?->layout ?? 'vertical') === 'horizontal' ? 'selected' : '' }}>Horizontal</option>
                            <option value="vertical" {{ ($progressDrawerStyle?->layout ?? 'vertical') === 'vertical' ? 'selected' : '' }}>Vertical</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Animation</label>
                        <select class="form-select" name="animation">
                            <option value="slide" {{ ($progressDrawerStyle?->animation ?? 'slide') === 'slide' ? 'selected' : '' }}>Slide</option>
                            <option value="fade" {{ ($progressDrawerStyle?->animation ?? 'slide') === 'fade' ? 'selected' : '' }}>Fade</option>
                            <option value="bounce" {{ ($progressDrawerStyle?->animation ?? 'slide') === 'bounce' ? 'selected' : '' }}>Bounce</option>
                            <option value="none" {{ ($progressDrawerStyle?->animation ?? 'slide') === 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Message Position</label>
                        <select class="form-select" name="message_position">
                            <option value="top" {{ ($progressDrawerStyle?->message_position ?? 'top') === 'top' ? 'selected' : '' }}>Top</option>
                            <option value="bottom" {{ ($progressDrawerStyle?->message_position ?? 'top') === 'bottom' ? 'selected' : '' }}>Bottom</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Font Color</label>
                        <input type="text" class="form-control color-picker" name="font_color" value="{{ $progressDrawerStyle?->font_color ?? '#000000' }}">
                    </div>

                    <div class="mb-3">
                        <label>Font Size</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="font_size" value="{{ $progressDrawerStyle?->font_size ?? 14 }}">
                            <span class="input-group-text">px</span>
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" name="show_products_in_bar" id="show_products_in_bar" {{ ($progressDrawerStyle?->show_products_in_bar ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_products_in_bar">Show Products</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- Color Picker + Auto Save Script --}}
<script>
    function handleAutoSave(formId, url) {
        $(document).on('change', `${formId} :input`, function () {
            const form = $(formId)[0];
            const formData = new FormData(form);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: () =>  showToast(app, `${formId} Saved Successfully`),
                error: xhr => console.error('Save error:', xhr.responseText)
            });
        });
    }

    $(function () {
        $('.color-picker').minicolors({ theme: 'bootstrap' });

        handleAutoSave('#bar-style-form', '/progress-bar/styles/save-bar');
        handleAutoSave('#widget-style-form', '/progress-bar/styles/save-widget');
        handleAutoSave('#drawer-style-form', '/progress-bar/styles/save-drawer');
    });
</script>
