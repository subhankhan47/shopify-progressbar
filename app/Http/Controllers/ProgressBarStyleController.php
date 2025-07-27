<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgressBarStyleController extends Controller
{
    public function saveBarStyle(Request $request)
    {
        auth()->user()->progressBarStyle()->updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only([
                'filled_progress_color', 'bg_color',
                'message_position', 'font_color', 'font_size',
                'border_radius',
            ]) + [
                'show_products_in_bar' => $request->has('show_products_in_bar'),
            ]
        );

        return response()->json(['message' => 'Saved']);
    }

    public function saveWidgetStyle(Request $request)
    {
        auth()->user()->progressWidgetStyle()->updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only([
                'position', 'widget_shape', 'bg_color', 'width', 'height',
            ]) + [
                'open_drawer' => $request->has('open_drawer'),
            ]
        );

        return response()->json(['message' => 'Saved']);
    }

    public function saveDrawerStyle(Request $request)
    {
        auth()->user()->progressDrawerStyle()->updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only([
                'filled_progress_color', 'bg_color',
                'layout', 'message_position', 'animation', 'font_color', 'font_size',
            ]) + [
                'show_products_in_bar' => $request->has('show_products_in_bar'),
            ]
        );

        return response()->json(['message' => 'Saved']);
    }

}
