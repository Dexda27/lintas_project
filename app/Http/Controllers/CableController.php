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
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Cable::query();

        if ($user->isAdminRegion()) {
            $query->where('region', $user->region);
        }
        // Search filter (tambahkan cable_id)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('cable_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('source_site', 'like', "%{$search}%")
                    ->orWhere('destination_site', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%");
            });
        }

        $cables = $query->orderBy('created_at', 'desc')->paginate(5);

        return view('cables.index', compact('cables'));
    }

    public function create()
    {
        $user = Auth::user();

        // Jika ingin membatasi region untuk admin region
        $regions = $user->isAdminRegion() ? [$user->region] : Cable::select('region')->distinct()->pluck('region');

        // Jika ingin menampilkan daftar site
        // $sites = Site::orderBy('name')->get();

        return view('cables.create', compact('regions'));
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
            'core_structure' => 'nullable|string', // Added for core structure data
        ]);

        // Check region access for admin
        if ($user->isAdminRegion() && $request->region !== $user->region) {
            return back()->withErrors(['region' => 'Access denied to this region.']);
        }

        DB::beginTransaction();
        try {
            // Calculate cores per tube (base calculation)
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

            // Generate fiber cores with sequential numbering
            $this->generateSequentialFiberCores($cable, $request->core_structure);

            DB::commit();

            return redirect()->route('cables.index')
                ->with('success', 'Cable created successfully with ' . $request->total_cores . ' fiber cores (sequential numbering).');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create cable: ' . $e->getMessage()]);
        }
    }

    public function show(Cable $cable)
    {
        $this->checkRegionAccess($cable);

        $cable->load(['fiberCores']);

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

    // Also update the cores method to load relationships properly:
    public function cores(Cable $cable)
    {
        $this->checkRegionAccess($cable);

        // Load both connectionA and connectionB with their related cores and cables
        $coresByTube = $cable->fiberCores()
            ->with([
                'connectionA.coreB.cable',
                'connectionB.coreA.cable'
            ])
            ->get()
            ->groupBy('tube_number');

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

    /**
     * Generate fiber cores with sequential numbering across all tubes
     * Each core gets a unique sequential number from 1 to total_cores
     */
    private function generateSequentialFiberCores(Cable $cable, $coreStructureJson = null)
    {
        $coresData = [];
        $sequentialCoreNumber = 1; // This will be the unique sequential number across all tubes

        // Parse core structure if provided
        $coreStructure = null;
        if ($coreStructureJson) {
            $coreStructure = json_decode($coreStructureJson, true);
        }

        // Calculate distribution
        $baseCoresPerTube = intval($cable->total_cores / $cable->total_tubes);
        $extraCores = $cable->total_cores % $cable->total_tubes;

        for ($tubeNumber = 1; $tubeNumber <= $cable->total_tubes; $tubeNumber++) {
            // Determine how many cores this tube should have
            $coresInThisTube = $baseCoresPerTube + ($tubeNumber <= $extraCores ? 1 : 0);

            // Generate cores for this tube
            for ($coreInTube = 1; $coreInTube <= $coresInThisTube; $coreInTube++) {
                if ($sequentialCoreNumber > $cable->total_cores) {
                    break 2; // Exit both loops
                }

                $coresData[] = [
                    'cable_id' => $cable->id,
                    'tube_number' => $tubeNumber,
                    'core_number' => $sequentialCoreNumber, // Sequential number across all tubes
                    'status' => 'ok',
                    'usage' => 'inactive',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $sequentialCoreNumber++;
            }
        }

        // Insert all cores at once for better performance
        if (!empty($coresData)) {
            FiberCore::insert($coresData);
        }
    }

    /**
     * Alternative method: Generate cores with tube-based numbering but store sequential reference
     * This maintains the old tube-core structure but adds sequential numbering
     */
    private function generateFiberCoresWithTubeReference(Cable $cable)
    {
        $coresData = [];
        $sequentialNumber = 1;

        // Calculate distribution
        $baseCoresPerTube = intval($cable->total_cores / $cable->total_tubes);
        $extraCores = $cable->total_cores % $cable->total_tubes;

        for ($tubeNumber = 1; $tubeNumber <= $cable->total_tubes; $tubeNumber++) {
            $coresInThisTube = $baseCoresPerTube + ($tubeNumber <= $extraCores ? 1 : 0);

            for ($coreInTube = 1; $coreInTube <= $coresInThisTube; $coreInTube++) {
                if ($sequentialNumber > $cable->total_cores) {
                    break 2;
                }

                $coresData[] = [
                    'cable_id' => $cable->id,
                    'tube_number' => $tubeNumber,
                    'core_number' => $sequentialNumber, // Using sequential instead of tube-based
                    'status' => 'ok',
                    'usage' => 'inactive',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $sequentialNumber++;
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

    // Add these methods to your existing CableController.php class

    public function getTubes(Cable $cable)
    {
        $this->checkRegionAccess($cable);

        return response()->json([
            'total_tubes' => $cable->total_tubes,
            'cable_name' => $cable->name
        ]);
    }

    public function getAvailableCores(Cable $cable, $tubeNumber)
    {
        $this->checkRegionAccess($cable);

        $cores = $cable->fiberCores()
            ->select('id', 'core_number', 'tube_number', 'status')
            ->where('tube_number', $tubeNumber)
            ->where('status', 'ok')
            ->whereDoesntHave('connectionA')
            ->whereDoesntHave('connectionB')
            ->orderBy('core_number')
            ->get();

        return response()->json($cores);
    }
}
