<?php

namespace App\Http\Controllers;

use App\Models\Cable;
use App\Models\FiberCore;
use App\Models\JointClosure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Cable::query();
        if ($user->isAdminRegion()) {
            $query->where('region', $user->region);
        }

        $cables = $query->get();

        $totalCores = 0;
        $activeCores = 0;
        $inactiveCores = 0;
        $problemCores = 0;

        foreach ($cables as $cable) {
            $totalCores += $cable->total_cores;
            $activeCores += $cable->active_cores_count;
            $inactiveCores += $cable->inactive_cores_count;
            $problemCores += $cable->problem_cores_count;
        }

        $regionalData = [];
        if ($user->isSuperAdmin()) {
            $regionalData = Cable::select('region')
                ->selectRaw('COUNT(*) as total_cables')
                ->selectRaw('SUM(total_cores) as total_cores')
                ->groupBy('region')
                ->get();
        }

        return view('dashboard.index', compact(
            'cables',
            'totalCores',
            'activeCores',
            'inactiveCores',
            'problemCores',
            'regionalData'
        ));
    }
}