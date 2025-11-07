<?php

namespace App\Http\Controllers;

use App\Models\Pole;
use App\Models\JointClosure;
use App\Models\Splitter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoleController extends Controller
{
    public function index(Request $request)
{
    $query = Pole::with(['jointClosures', 'splitters']);

    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // Search by Pole attributes
            $q->where('poles.pole_id', 'like', "%{$search}%")
              ->orWhere('poles.name', 'like', "%{$search}%")
              ->orWhere('poles.location', 'like', "%{$search}%")

              // Search by Joint Closure
              ->orWhereHas('jointClosures', function($jc) use ($search) {
                  $jc->where('joint_closures.closure_id', 'like', "%{$search}%")
                    ->orWhere('joint_closures.name', 'like', "%{$search}%")
                    ->orWhere('joint_closures.location', 'like', "%{$search}%");
              })

              // Search by Splitter
              ->orWhereHas('splitters', function($sp) use ($search) {
                  $sp->where('splitters.splitter_id', 'like', "%{$search}%")
                    ->orWhere('splitters.name', 'like', "%{$search}%")
                    ->orWhere('splitters.location', 'like', "%{$search}%");
              });
        });
    }

    // Filter by region
    if ($request->filled('region')) {
        $query->where('region', $request->region);
    }

    // Filter by type
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $poles = $query->latest()->paginate(10)->withQueryString();

    return view('poles.index', compact('poles'));
}

    public function create()
    {
        $regions = ['Bali', 'NTB', 'NTT'];
        $jointClosures = JointClosure::orderBy('name')->get();
        $splitters = Splitter::orderBy('name')->get();

        return view('poles.create', compact('regions', 'jointClosures', 'splitters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pole_id' => 'required|string|unique:poles,pole_id|max:255',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'type' => 'required|in:besi,beton',
            'height' => 'required|in:7,9',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',
            'status' => 'required|in:ok,not_ok',
            'joint_closures' => 'nullable|array',
            'joint_closures.*' => 'exists:joint_closures,id',
            'splitters' => 'nullable|array',
            'splitters.*' => 'exists:splitters,id',
        ]);

        DB::beginTransaction();
        try {
            // Create pole dengan pole_id dari input user
            $pole = Pole::create($validated);

            // Attach joint closures
            if ($request->has('joint_closures')) {
                $pole->jointClosures()->attach($request->joint_closures);
            }

            // Attach splitters
            if ($request->has('splitters')) {
                $pole->splitters()->attach($request->splitters);
            }

            DB::commit();

            return redirect()->route('poles.index')
                ->with('success', 'Tiang berhasil ditambahkan dengan ID: ' . $pole->pole_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menambahkan tiang: ' . $e->getMessage());
        }
    }

    public function show(Pole $pole)
    {
        $pole->load(['jointClosures', 'splitters']);
        return view('poles.show', compact('pole'));
    }

    public function edit(Pole $pole)
    {
        $regions = ['Bali', 'NTB', 'NTT'];
        $jointClosures = JointClosure::orderBy('name')->get();
        $splitters = Splitter::orderBy('name')->get();

        $pole->load(['jointClosures', 'splitters']);

        return view('poles.edit', compact('pole', 'regions', 'jointClosures', 'splitters'));
    }

    public function update(Request $request, Pole $pole)
    {
        $validated = $request->validate([
            'pole_id' => 'required|string|unique:poles,pole_id,' . $pole->id . '|max:255',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'type' => 'required|in:besi,beton',
            'height' => 'required|in:7,9',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',
            'status' => 'required|in:ok,not_ok',
            'joint_closures' => 'nullable|array',
            'joint_closures.*' => 'exists:joint_closures,id',
            'splitters' => 'nullable|array',
            'splitters.*' => 'exists:splitters,id',
        ]);

        DB::beginTransaction();
        try {
            // Update pole
            $pole->update($validated);

            // Sync joint closures
            $pole->jointClosures()->sync($request->joint_closures ?? []);

            // Sync splitters
            $pole->splitters()->sync($request->splitters ?? []);

            DB::commit();

            return redirect()->route('poles.index')
                ->with('success', 'Tiang berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui tiang: ' . $e->getMessage());
        }
    }

    public function destroy(Pole $pole)
    {
        try {
            $poleId = $pole->pole_id;
            $pole->delete();

            return redirect()->route('poles.index')
                ->with('success', "Tiang {$poleId} berhasil dihapus");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus tiang: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get joint closures by region
     */
    public function getJointClosuresByRegion(Request $request)
    {
        $region = $request->input('region');
        $jointClosures = JointClosure::where('region', $region)
            ->orderBy('name')
            ->get(['id', 'closure_id', 'name', 'location']);

        return response()->json($jointClosures);
    }

    /**
     * AJAX: Get splitters by region
     */
    public function getSplittersByRegion(Request $request)
    {
        $region = $request->input('region');
        $splitters = Splitter::where('region', $region)
            ->orderBy('name')
            ->get(['id', 'splitter_id', 'name', 'location']);

        return response()->json($splitters);
    }

    /**
     * Get Joint Closures by region
     */
    public function getJointClosures(Request $request)
    {
        $region = $request->query('region');

        if (!$region) {
            return response()->json([]);
        }

        $jointClosures = JointClosure::where('region', $region)
            ->select('id', 'closure_id', 'name', 'location', 'region')
            ->orderBy('closure_id')
            ->get();

        return response()->json($jointClosures);
    }

    /**
     * Get Splitters by region
     */
    public function getSplitters(Request $request)
    {
        $region = $request->query('region');

        if (!$region) {
            return response()->json([]);
        }

        $splitters = Splitter::where('region', $region)
            ->select('id', 'splitter_id', 'name', 'location', 'region')
            ->orderBy('splitter_id')
            ->get();

        return response()->json($splitters);
    }
}
