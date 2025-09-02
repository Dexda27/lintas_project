<?php
// app/Http/Controllers/JointClosureController.php

namespace App\Http\Controllers;

use App\Models\JointClosure;
use App\Models\CoreConnection;
use App\Models\FiberCore;
use App\Models\Cable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JointClosureController extends Controller
{




    public function index(Request $request)
    {
        $user = Auth::user();
        $query = JointClosure::query();

        // Apply regional filter for admin region users
        if ($user->isAdminRegion()) {
            $query->where('region', $user->region);
        }

        // Apply search filter if search parameter exists
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('closure_id', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('location', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('region', 'LIKE', '%' . $searchTerm . '%');
            });
        }


        $allClosures = $query->get();
        $statistics = [
            'total_closures' => $allClosures->count(),
            'active_closures' => $allClosures->where('status', 'ok')->count(),
            'problem_closures' => $allClosures->where('status', 'not_ok')->count(),
            'total_connections' => $allClosures->sum('used_capacity')
        ];



        // Get closures with core connections count and pagination
        $closures = $query->withCount('coreConnections')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('closures.index', compact('closures', 'statistics'));
    }

    public function create()
    {
        return view('closures.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'closure_id' => 'required|string|unique:joint_closures,closure_id',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'region' => 'required|string',
            'capacity' => 'required|integer|min:1|max:1000',
            'status' => 'required|in:ok,not_ok',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check region access for admin
        if ($user->isAdminRegion() && $request->region !== $user->region) {
            return back()->withErrors(['region' => 'Access denied to this region.']);
        }

        $closure = JointClosure::create([
            'closure_id' => $request->closure_id,
            'name' => $request->name,
            'location' => $request->location,
            'region' => $request->region,
            'capacity' => $request->capacity,
            'used_capacity' => 0,
            'status' => $request->status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
        ]);

        return redirect()->route('closures.index')
            ->with('success', 'Joint closure created successfully.');
    }

    public function show(JointClosure $closure)
    {
        $this->checkRegionAccess($closure);

        $closure->load([
            'coreConnections.coreA.cable',
            'coreConnections.coreB.cable'
        ]);

        $statistics = [
            'total_capacity' => $closure->capacity,
            'used_capacity' => $closure->used_capacity,
            'available_capacity' => $closure->available_capacity,
            'active_connections' => $closure->coreConnections->count(),
        ];

        $user = Auth::user();

        // Ambil core yang belum terkoneksi (Available Cores)
        $availableCores = \App\Models\FiberCore::with('cable')
            ->where('status', 'ok')
            ->whereDoesntHave('connectionA')
            ->whereDoesntHave('connectionB')
            ->whereHas('cable', function ($q) use ($user, $closure) {
                if ($user->isAdminRegion()) {
                    $q->where('region', $user->region);
                }
                $q->where('region', $closure->region);
            })
            ->orderBy('cable_id')
            ->get()
            ->groupBy('cable_id');

        return view('closures.show', compact('closure', 'statistics', 'availableCores'));
    }

    public function edit(JointClosure $closure)
    {
        $this->checkRegionAccess($closure);

        return view('closures.edit', compact('closure'));
    }

    public function update(Request $request, JointClosure $closure)
    {
        $this->checkRegionAccess($closure);
        $user = Auth::user();

        $request->validate([
            'closure_id' => ['required', 'string', Rule::unique('joint_closures', 'closure_id')->ignore($closure->id)],
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'region' => 'required|string',
            'capacity' => 'integer|min:' . $closure->used_capacity . '|max:1000',
            'status' => 'required|in:ok,not_ok',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check region access for admin
        if ($user->isAdminRegion() && $request->region !== $user->region) {
            return back()->withErrors(['region' => 'Access denied to this region.']);
        }

        $closure->update($request->only([
            'closure_id',
            'name',
            'location',
            'region',
            'capacity',
            'status',
            'latitude',
            'longitude',
            'description'
        ]));

        return redirect()->route('closures.connections', $closure)
            ->with('success', 'Joint closure updated successfully.');
    }

    public function destroy(JointClosure $closure)
    {
        $this->checkRegionAccess($closure);

        if ($closure->coreConnections()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete closure with active connections. Please disconnect all cores first.']);
        }

        $closure->delete();

        return redirect()->route('closures.index')
            ->with('success', 'Joint closure deleted successfully.');
    }

    public function connections(JointClosure $closure)
    {
        $this->checkRegionAccess($closure);

        $closure->load([
            'coreConnections.coreA.cable',
            'coreConnections.coreB.cable'
        ]);

        // Get available cables in the same region as closure
        $user = Auth::user();
        $availableCables = Cable::select('id', 'name', 'cable_id')
            ->where('region', $closure->region)
            ->where('status', 'ok')
            ->whereHas('fiberCores', function($query) {
                $query->where('status', 'ok')
                    ->whereDoesntHave('connectionA')
                    ->whereDoesntHave('connectionB');
            })
            ->orderBy('name')
            ->get();

        return view('closures.connections', compact('closure', 'availableCables'));
    }

    public function connectCores(Request $request, JointClosure $closure)
    {
        $this->checkRegionAccess($closure);

        $request->validate([
            'core_a_id' => 'required|exists:fiber_cores,id',
            'core_b_id' => 'required|exists:fiber_cores,id|different:core_a_id',
            'splice_loss' => 'nullable|numeric|min:0|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $coreA = FiberCore::with('cable')->findOrFail($request->core_a_id);
            $coreB = FiberCore::with('cable')->findOrFail($request->core_b_id);

            // Validate cores are from different cables
            if ($coreA->cable_id === $coreB->cable_id) {
                return back()->withErrors(['error' => 'Cannot connect cores from the same cable.']);
            }

            // Validate cores are not already connected
            if ($coreA->isConnected() || $coreB->isConnected()) {
                return back()->withErrors(['error' => 'One or both cores are already connected.']);
            }

            // Validate closure has available capacity
            if ($closure->used_capacity >= $closure->capacity) {
                return back()->withErrors(['error' => 'Joint closure has reached maximum capacity.']);
            }

            // Validate cores belong to the same region as closure
            if ($coreA->cable->region !== $closure->region || $coreB->cable->region !== $closure->region) {
                return back()->withErrors(['error' => 'Cores must belong to the same region as the closure.']);
            }

            // Create connection
            CoreConnection::create([
                'closure_id' => $closure->id,
                'core_a_id' => $request->core_a_id,
                'core_b_id' => $request->core_b_id,
                'splice_loss' => $request->splice_loss,
                'description' => $request->description,
            ]);

            // Update core usage to active
            $coreA->update(['usage' => 'active']);
            $coreB->update(['usage' => 'active']);

            // Update closure used capacity
            $closure->increment('used_capacity');

            DB::commit();

            return redirect()->route('closures.connections', $closure)
                ->with('success', 'Cores connected successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to connect cores: ' . $e->getMessage()]);
        }
    }

    public function disconnectCores(CoreConnection $connection)
    {
        $closure = $connection->closure;
        $this->checkRegionAccess($closure);

        DB::beginTransaction();
        try {
            // Update core usage to inactive
            $connection->coreA->update(['usage' => 'inactive']);
            $connection->coreB->update(['usage' => 'inactive']);

            // Update closure used capacity
            $closure->decrement('used_capacity');

            // Delete connection
            $connection->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cores disconnected successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect cores: ' . $e->getMessage()
            ], 500);
        }
    }

    private function checkRegionAccess(JointClosure $closure)
    {
        $user = Auth::user();

        if ($user->isAdminRegion() && $closure->region !== $user->region) {
            abort(403, 'Access denied to this region.');
        }
    }
}
