<?php

namespace App\Http\Controllers;

use App\Models\ProgressBarSetting;
use App\Models\ProgressBarStyle;
use App\Models\ProgressDrawerStyle;
use App\Models\ProgressWidgetStyle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Illuminate\Foundation\Testing\Concerns\json;

class ProgressBarSettingController extends Controller
{
    public function index()
    {
        if (!auth()->user()->plan){
            return \Redirect::tokenRedirect('plans.index');
        }
        $user = Auth::user();
        $user->load(['progressBarStyle', 'progressWidgetStyle', 'progressDrawerStyle', 'progressBarSetting',]);
        $progressBarStyle = $user->progressBarStyle ?? new ProgressBarStyle(['user_id' => $user->id]);
        $progressWidgetStyle = $user->progressWidgetStyle ?? new ProgressWidgetStyle(['user_id' => $user->id]);
        $progressDrawerStyle = $user->progressDrawerStyle ?? new ProgressDrawerStyle(['user_id' => $user->id]);
        $settings = $user->progressBarSetting ?? new ProgressBarSetting(['user_id' => $user->id]);

        return view('welcome', compact(
            'settings',
            'progressBarStyle',
            'progressWidgetStyle',
            'progressDrawerStyle'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->except('_token');
        ProgressBarSetting::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );
        return response()->json(['success' => true, 'message' => 'Progress Bar Settings updated successfully!']);
    }

    public function settings(Request $request)
    {
        $shop = $request->get('shop');
        if (!$shop) {
            return response()->json(['error' => 'Missing shop parameter'], 400);
        }

        $user = User::with(['progressBarSetting', 'progressBarStyle',
            'progressWidgetStyle', 'progressDrawerStyle', 'thresholds',
        ])->where('name', $shop)->first();
        if (!$user) {
            return response()->json(['error' => 'Shop not found'], 404);
        }
        return response()->json([
            'settings' => $user->progressBarSetting,
            'progressbar_style' => $user->progressBarStyle,
            'widget_style' => $user->progressWidgetStyle,
            'drawer_style' => $user->progressDrawerStyle,
            'thresholds' => $user->thresholds()->orderBy('priority')->get(),
        ]);
    }
}
