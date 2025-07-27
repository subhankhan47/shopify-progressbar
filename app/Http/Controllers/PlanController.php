<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index()
    {
        $currentPlan = auth()->user()->plan->id ?? '';
        $plans = DB::table('plans')->get();
        return view('plans.index', compact('currentPlan', 'plans'));
    }
}
