<?php

namespace App\Http\Controllers;

use App\Models\Cable;
use App\Models\Site;
use App\Models\FiberCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CableController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Cable::query(); // Removed with(['sourceSite', 'destinationSite'])

        if ($user->isAdminRegion()) {
            $query->where('region', $user->region);
        }

        $cables = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('cables.index', compact('cables'));
    }

    public function create()
    {
        // Removed sites query since we don't need it anymore
        return view('cables.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'cable_id' => 'required|string|unique:cables,cable_id',
            'name' => 'required|string|max:255',
            'region' => 'required|string',
            'total_tubes' => 'required|integer|min:1|max:500',
            'total_cores' => 'required|integer|min:1|max:10000',
            'status' => 'required|in:ok,not_ok',
            'usage' => 'required|in:active,inactive',
            'otdr_length' => 'nullable|numeric|min:0',
            'source_site' => 'required|string|max:255',
            'destination_site' => 'required|string|max:255|different:source_site',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check region access for admin
        if ($user->isAdminRegion() && $request->region !== $user->region) {
            return back()->withErrors(['region' => 'Access denied to this region.']);
        }

        DB::beginTransaction();
        try {
            // Calculate cores per tube
            $coresPerTube = intval($request->total_cores / $request->total_tubes);

            $cable = Cable::create([
                'cable_id' => $request->cable_id,
                'name' => $request->name,
                'region' => $request->region,
                'total_tubes' => $request->total_tubes,
                'total_cores' => $request->total_cores,
                'cores_per_tube' => $coresPerTube,
                'status' => $request->status,
                'usage' => $request->usage,
                'otdr_length' => $request->otdr_length,
                'source_site' => $request->source_site,
                'destination_site' => $request->destination_site,
                'description' => $request->description,
            ]);

            // Auto-generate fiber cores
            $this->generateFiberCores($cable);

            DB::commit();

            return redirect()->route('cables.index')
                ->with('success', 'Cable created successfully with ' . $request->total_cores . ' fiber cores.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create cable: ' . $e->getMessage()]);
        }
    }

    public function show(Cable $cable)
    {
        $this->checkRegionAccess($cable);

        $cable->load(['fiberCores']); // Removed sourceSite, destinationSite

        $coresByTube = $cable->fiberCores->groupBy('tube_number');
        $statistics = [
            'total_cores' => $cable->total_cores,
            'active_cores' => $cable->fiberCores->where('usage', 'active')->count(),
            'inactive_cores' => $cable->fiberCores->where('usage', 'inactive')->count(),
            'problem_cores' => $cable->fiberCores->where('status', 'not_ok')->count(),
        ];

        return view('cables.show', compact('cable', 'coresByTube', 'statistics'));
    }

    public function edit(Cable $cable)
    {
        $this->checkRegionAccess($cable);

        // Removed sites query since we don't need it anymore
        return view('cables.edit', compact('cable'));
    }

    public function update(Request $request, Cable $cable)
    {
        $this->checkRegionAccess($cable);
        $user = Auth::user();

        $request->validate([
            'cable_id' => ['required', 'string', Rule::unique('cables', 'cable_id')->ignore($cable->id)],
            'name' => 'required|string|max:255',
            'region' => 'required|string',
            'status' => 'required|in:ok,not_ok',
            'usage' => 'required|in:active,inactive',
            'otdr_length' => 'nullable|numeric|min:0',
            'source_site' => 'required|string|max:255',
            'destination_site' => 'required|string|max:255|different:source_site',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check region access for admin
        if ($user->isAdminRegion() && $request->region !== $user->region) {
            return back()->withErrors(['region' => 'Access denied to this region.']);
        }

        $cable->update($request->only([
            'cable_id',
            'name',
            'region',
            'status',
            'usage',
            'otdr_length',
            'source_site',
            'destination_site',
            'description'
        ]));

        return redirect()->route('cables.show', $cable)
            ->with('success', 'Cable updated successfully.');
    }

    public function destroy(Cable $cable)
    {
        $this->checkRegionAccess($cable);

        // Check if any cores are connected
        $connectedCores = $cable->fiberCores()
            ->whereHas('connectionA')
            ->orWhereHas('connectionB')
            ->count();

        if ($connectedCores > 0) {
            return back()->withErrors(['error' => 'Cannot delete cable with connected cores. Please disconnect all cores first.']);
        }

        DB::beginTransaction();
        try {
            // Delete all fiber cores first
            $cable->fiberCores()->delete();

            // Delete the cable
            $cable->delete();

            DB::commit();

            return redirect()->route('cables.index')
                ->with('success', 'Cable deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete cable: ' . $e->getMessage()]);
        }
    }

    public function cores(Cable $cable)
    {
        $this->checkRegionAccess($cable);

        // Removed sourceSite, destinationSite from load
        $coresByTube = $cable->fiberCores()->with(['connectionA.coreB.cable', 'connectionB.coreA.cable'])->get()->groupBy('tube_number');

        return view('cables.cores', compact('cable', 'coresByTube'));
    }

    public function updateCore(Request $request, FiberCore $core)
    {
        $this->checkRegionAccess($core->cable);

        $request->validate([
            'status' => 'required|in:ok,not_ok',
            'usage' => 'required|in:active,inactive',
            'attenuation' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $core->update($request->only(['status', 'usage', 'attenuation', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Core updated successfully.'
        ]);
    }

    private function generateFiberCores(Cable $cable)
    {
        $coresData = [];
        $coreNumber = 1;

        for ($tube = 1; $tube <= $cable->total_tubes; $tube++) {
            for ($core = 1; $core <= $cable->cores_per_tube; $core++) {
                if ($coreNumber > $cable->total_cores)
                    break 2;

                $coresData[] = [
                    'cable_id' => $cable->id,
                    'tube_number' => $tube,
                    'core_number' => $core,
                    'status' => 'ok',
                    'usage' => 'inactive',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $coreNumber++;
            }
        }

        // Handle remaining cores if total_cores is not evenly divisible
        if ($coreNumber <= $cable->total_cores) {
            $remainingCores = $cable->total_cores - ($coreNumber - 1);
            $lastTube = $cable->total_tubes;
            $startCore = $cable->cores_per_tube + 1;

            for ($i = 0; $i < $remainingCores; $i++) {
                $coresData[] = [
                    'cable_id' => $cable->id,
                    'tube_number' => $lastTube,
                    'core_number' => $startCore + $i,
                    'status' => 'ok',
                    'usage' => 'inactive',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        FiberCore::insert($coresData);
    }

    private function checkRegionAccess(Cable $cable)
    {
        $user = Auth::user();

        if ($user->isAdminRegion() && $cable->region !== $user->region) {
            abort(403, 'Access denied to this region.');
        }
    }
}