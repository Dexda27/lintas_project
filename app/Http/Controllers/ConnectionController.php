<?php

namespace App\Http\Controllers;

use App\Models\CoreConnection;
use App\Models\FiberCore;
use App\Models\JointClosure;
use App\Models\Cable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConnectionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'source_core_id' => 'required|exists:fiber_cores,id',
            'target_core_id' => 'required|exists:fiber_cores,id|different:source_core_id',
            'joint_closure_id' => 'required|exists:joint_closures,id',
            'connection_type' => 'required|in:splice,patch,direct',
            'connection_loss' => 'nullable|numeric|min:0|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $sourceCore = FiberCore::with('cable')->findOrFail($request->source_core_id);
            $targetCore = FiberCore::with('cable')->findOrFail($request->target_core_id);
            $closure = JointClosure::findOrFail($request->joint_closure_id);

            // Check region access
            $user = Auth::user();
            if ($user->isAdminRegion()) {
                if (
                    $sourceCore->cable->region !== $user->region ||
                    $targetCore->cable->region !== $user->region ||
                    $closure->region !== $user->region
                ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied to this region.'
                    ], 403);
                }
            }

            // Validate cores are from different cables
            if ($sourceCore->cable_id === $targetCore->cable_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot connect cores from the same cable.'
                ], 422);
            }

            // Validate cores are not already connected
            if ($sourceCore->isConnected() || $targetCore->isConnected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or both cores are already connected.'
                ], 422);
            }

            // Validate closure has available capacity
            if ($closure->used_capacity >= $closure->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joint closure has reached maximum capacity.'
                ], 422);
            }

            // Validate cores belong to the same region as closure
            if ($sourceCore->cable->region !== $closure->region || $targetCore->cable->region !== $closure->region) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cores must belong to the same region as the closure.'
                ], 422);
            }

            // Create connection - using splice_loss field for backward compatibility
            CoreConnection::create([
                'closure_id' => $closure->id,
                'core_a_id' => $request->source_core_id,
                'core_b_id' => $request->target_core_id,
                'splice_loss' => $request->connection_loss,
                'description' => $request->notes,
            ]);

            // Update core usage to active
            $sourceCore->update(['usage' => 'active']);
            $targetCore->update(['usage' => 'active']);

            // Update closure used capacity
            $closure->increment('used_capacity');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Connection created successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create connection: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(CoreConnection $connection)
    {
        $closure = $connection->closure;

        // Check region access
        $user = Auth::user();
        if ($user->isAdminRegion() && $closure->region !== $user->region) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied to this region.'
            ], 403);
        }

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
                'message' => 'Connection deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete connection: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods for AJAX calls
    public function getJointClosures()
    {
        try {
            $user = Auth::user();
            $query = JointClosure::select('id', 'name', 'location', 'closure_id', 'capacity', 'used_capacity');

            if ($user->isAdminRegion()) {
                $query->where('region', $user->region);
            }

            $closures = $query->where('status', 'ok')
                ->whereRaw('used_capacity < capacity') // Only show closures with available capacity
                ->orderBy('name')
                ->get()
                ->map(function ($closure) {
                    $closure->available_capacity = $closure->capacity - $closure->used_capacity;
                    return $closure;
                });

            return response()->json($closures);
        } catch (\Exception $e) {
            \Log::error('Error loading joint closures: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load joint closures'], 500);
        }
    }

    public function getCablesByJointClosure($closureId)
    {
        try {
            $user = Auth::user();
            $closure = JointClosure::findOrFail($closureId);

            // Check region access
            if ($user->isAdminRegion() && $closure->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            // Get all cables in the same region as the closure
            $cables = Cable::select('id', 'name', 'cable_id')
                ->where('region', $closure->region)
                ->where('status', 'ok')
                ->orderBy('name')
                ->get();

            return response()->json($cables);
        } catch (\Exception $e) {
            \Log::error('Error loading cables for JC: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load cables'], 500);
        }
    }

    public function getAvailableCores($cableId, $tubeNumber = null)
    {
        try {
            $user = Auth::user();
            $cable = Cable::findOrFail($cableId);

            // Check region access
            if ($user->isAdminRegion() && $cable->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $query = FiberCore::select('id', 'core_number', 'tube_number', 'status')
                ->where('cable_id', $cableId)
                ->where('status', 'ok')
                ->whereDoesntHave('connectionA')
                ->whereDoesntHave('connectionB');

            if ($tubeNumber) {
                $query->where('tube_number', $tubeNumber);
            }

            $cores = $query->orderBy('tube_number')
                ->orderBy('core_number')
                ->get();

            return response()->json($cores);
        } catch (\Exception $e) {
            \Log::error('Error loading available cores: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load cores'], 500);
        }
    }

    public function getCablesByRegion($regionId)
    {
        try {
            $user = Auth::user();

            // Check region access
            if ($user->isAdminRegion() && $regionId !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $cables = Cable::select('id', 'name', 'cable_id', 'source_site', 'destination_site')
                ->where('region', $regionId)
                ->where('status', 'ok')
                ->whereHas('fiberCores', function ($query) {
                    $query->where('status', 'ok')
                        ->whereDoesntHave('connectionA')
                        ->whereDoesntHave('connectionB');
                })
                ->orderBy('name')
                ->get();

            return response()->json($cables);
        } catch (\Exception $e) {
            \Log::error('Error loading cables: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load cables'], 500);
        }
    }

    public function getTubesByCable($cableId)
    {
        try {
            $user = Auth::user();
            $cable = Cable::findOrFail($cableId);

            // Check region access
            if ($user->isAdminRegion() && $cable->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            // Get tubes that have available cores
            $availableTubes = FiberCore::where('cable_id', $cableId)
                ->where('status', 'ok')
                ->whereDoesntHave('connectionA')
                ->whereDoesntHave('connectionB')
                ->distinct()
                ->pluck('tube_number')
                ->sort()
                ->values();

            return response()->json([
                'total_tubes' => $cable->total_tubes,
                'available_tubes' => $availableTubes,
                'cable_name' => $cable->name,
                'cable_id' => $cable->cable_id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading cable tubes: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load tubes'], 500);
        }
    }

    public function getCoresByTube($cableId, $tubeNumber)
    {
        try {
            $user = Auth::user();
            $cable = Cable::findOrFail($cableId);

            // Check region access
            if ($user->isAdminRegion() && $cable->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $cores = FiberCore::select('id', 'core_number', 'tube_number', 'status', 'attenuation')
                ->where('cable_id', $cableId)
                ->where('tube_number', $tubeNumber)
                ->where('status', 'ok')
                ->whereDoesntHave('connectionA')
                ->whereDoesntHave('connectionB')
                ->orderBy('core_number')
                ->get();

            return response()->json($cores);
        } catch (\Exception $e) {
            \Log::error('Error loading cores: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load cores'], 500);
        }
    }
}
