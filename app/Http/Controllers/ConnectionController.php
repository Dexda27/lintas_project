<?php

namespace App\Http\Controllers;

use App\Models\CoreConnection;
use App\Models\FiberCore;
use App\Models\JointClosure;
use App\Models\Cable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConnectionController extends Controller
{
    /**
     * Store single connection
     */
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

            // ✅ NEW: Check if this SPECIFIC connection already exists (bidirectional)
            $existingConnection = CoreConnection::where(function($query) use ($request) {
                $query->where('core_a_id', $request->source_core_id)
                      ->where('core_b_id', $request->target_core_id);
            })->orWhere(function($query) use ($request) {
                $query->where('core_a_id', $request->target_core_id)
                      ->where('core_b_id', $request->source_core_id);
            })->first();

            if ($existingConnection) {
                return response()->json([
                    'success' => false,
                    'message' => 'Connection already exists between these cores.'
                ], 422);
            }

            // ❌ REMOVED: isConnected() validation
            // Now cores can have multiple connections

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

            // Create connection
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
            Log::error('Connection creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create connection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store multiple connections at once (BULK)
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'connections' => 'required|array|min:1',
            'connections.*.source_core_id' => 'required|integer|exists:fiber_cores,id',
            'connections.*.target_core_id' => 'required|integer|exists:fiber_cores,id',
            'connections.*.joint_closure_id' => 'required|integer|exists:joint_closures,id',
            'connections.*.connection_type' => 'nullable|string|in:splice,connector',
            'connections.*.connection_loss' => 'nullable|numeric|min:0|max:10',
            'connections.*.notes' => 'nullable|string|max:500',
        ]);

        Log::info('Validation passed', ['total_connections' => count($request->connections)]);

        $created = [];
        $skipped = [];
        $errors = [];
        $connectionNumber = 0;

        Log::info('Starting loop through connections', ['count' => count($request->connections)]);

        DB::beginTransaction();

        try {
            foreach ($request->connections as $index => $connectionData) {
                $connectionNumber++;

                Log::info("========== Processing connection #{$connectionNumber} ==========", [
                    'data' => $connectionData
                ]);

                // Load entities
                $sourceCore = FiberCore::with('cable')->find($connectionData['source_core_id']);
                $targetCore = FiberCore::with('cable')->find($connectionData['target_core_id']);
                $closure = JointClosure::find($connectionData['joint_closure_id']);

                if (!$sourceCore || !$targetCore || !$closure) {
                    $msg = "Connection #{$connectionNumber}: Invalid entity IDs";
                    $errors[] = $msg;
                    Log::error($msg);
                    continue;
                }

                Log::info("Connection #{$connectionNumber} entities loaded", [
                    'source_core' => [
                        'id' => $sourceCore->id,
                        'cable' => $sourceCore->cable->name,
                        'cable_id' => $sourceCore->cable_id,
                        'region' => $sourceCore->cable->region
                    ],
                    'target_core' => [
                        'id' => $targetCore->id,
                        'cable' => $targetCore->cable->name,
                        'cable_id' => $targetCore->cable_id,
                        'region' => $targetCore->cable->region
                    ],
                    'closure' => [
                        'id' => $closure->id,
                        'name' => $closure->name,
                        'region' => $closure->region,
                        'used_capacity' => $closure->used_capacity,
                        'capacity' => $closure->capacity
                    ]
                ]);

                // Validation 1: Check if connecting to itself
                if ($sourceCore->id === $targetCore->id) {
                    $msg = "Connection #{$connectionNumber}: Cannot connect a core to itself";
                    $errors[] = $msg;
                    Log::warning($msg, ['core_id' => $sourceCore->id]);
                    continue;
                }
                Log::info("Connection #{$connectionNumber}: ✅ Not connecting to itself");

                // Validation 2: Check region access
                if (Auth::user()->role === 'admin_region') {
                    $userRegion = Auth::user()->region;
                    if ($sourceCore->cable->region !== $userRegion ||
                        $targetCore->cable->region !== $userRegion ||
                        $closure->region !== $userRegion) {
                        $msg = "Connection #{$connectionNumber}: Access denied - Region mismatch";
                        $errors[] = $msg;
                        Log::warning($msg, [
                            'user_region' => $userRegion,
                            'source_region' => $sourceCore->cable->region,
                            'target_region' => $targetCore->cable->region,
                            'closure_region' => $closure->region
                        ]);
                        continue;
                    }
                }
                Log::info("Connection #{$connectionNumber}: ✅ Region access validated");

                // Validation 3: Check if cores are from same cable
                if ($sourceCore->cable_id === $targetCore->cable_id) {
                    $msg = "Connection #{$connectionNumber}: Cannot connect cores from same cable";
                    $errors[] = $msg;
                    Log::warning($msg, [
                        'cable_id' => $sourceCore->cable_id,
                        'cable_name' => $sourceCore->cable->name
                    ]);
                    continue;
                }
                Log::info("Connection #{$connectionNumber}: ✅ Cores from different cables");

                // Validation 4: Check closure capacity
                if ($closure->used_capacity >= $closure->capacity) {
                    $msg = "Connection #{$connectionNumber}: Joint closure at maximum capacity";
                    $errors[] = $msg;
                    Log::warning($msg, [
                        'closure_id' => $closure->id,
                        'used' => $closure->used_capacity,
                        'capacity' => $closure->capacity
                    ]);
                    continue;
                }
                Log::info("Connection #{$connectionNumber}: ✅ Closure has available capacity");

                // ✅ UPDATED VALIDATION 5: Check for EXACT duplicate (same cores + same closure)
                $existingConnection = CoreConnection::where(function($query) use ($sourceCore, $targetCore) {
                    $query->where(function($q) use ($sourceCore, $targetCore) {
                        $q->where('core_a_id', $sourceCore->id)
                          ->where('core_b_id', $targetCore->id);
                    })->orWhere(function($q) use ($sourceCore, $targetCore) {
                        $q->where('core_a_id', $targetCore->id)
                          ->where('core_b_id', $sourceCore->id);
                    });
                })
                ->where('closure_id', $closure->id) // ✅ MUST be same closure
                ->first();

                if ($existingConnection) {
                    $msg = "Connection #{$connectionNumber}: Duplicate - Connection already exists between these cores via this closure";
                    $skipped[] = [
                        'connection_number' => $connectionNumber,
                        'reason' => $msg,
                        'existing_connection_id' => $existingConnection->id,
                        'source_core_id' => $sourceCore->id,
                        'target_core_id' => $targetCore->id,
                        'closure_id' => $closure->id
                    ];
                    Log::info($msg, [
                        'existing_connection_id' => $existingConnection->id,
                        'core_a_id' => $existingConnection->core_a_id,
                        'core_b_id' => $existingConnection->core_b_id,
                        'closure_id' => $existingConnection->closure_id
                    ]);
                    continue;
                }

                Log::info("Connection #{$connectionNumber}: ✅ No duplicate found (different closure or cores)");

                // All validations passed - Create connection
                Log::info("Connection #{$connectionNumber}: All validations passed, creating connection...");

                $connection = CoreConnection::create([
                    'closure_id' => $closure->id,
                    'core_a_id' => $sourceCore->id,
                    'core_b_id' => $targetCore->id,
                    'connection_type' => $connectionData['connection_type'] ?? 'splice',
                    'splice_loss' => $connectionData['connection_loss'] ?? null,
                    'description' => $connectionData['notes'] ?? null,
                ]);

                Log::info("Connection #{$connectionNumber} CREATED successfully", [
                    'connection_id' => $connection->id,
                    'closure_id' => $closure->id,
                    'core_a_id' => $sourceCore->id,
                    'core_b_id' => $targetCore->id
                ]);

                // Update core usage
                $sourceCore->update(['usage' => 'active']);
                $targetCore->update(['usage' => 'active']);
                Log::info("Connection #{$connectionNumber}: Core usage updated to active");

                // Update closure capacity
                $closure->increment('used_capacity');
                Log::info("Connection #{$connectionNumber}: Closure capacity incremented");

                $created[] = [
                    'connection_number' => $connectionNumber,
                    'connection_id' => $connection->id,
                    'source_core' => [
                        'id' => $sourceCore->id,
                        'cable' => $sourceCore->cable->name,
                        'tube' => $sourceCore->tube_number,
                        'core' => $sourceCore->core_number
                    ],
                    'target_core' => [
                        'id' => $targetCore->id,
                        'cable' => $targetCore->cable->name,
                        'tube' => $targetCore->tube_number,
                        'core' => $targetCore->core_number
                    ],
                    'joint_closure' => $closure->name ?? "JC {$closure->id}",
                    'type' => $connection->connection_type,
                    'loss' => $connection->splice_loss
                ];

                Log::info("Connection #{$connectionNumber} FULLY PROCESSED ✅");
            }

            DB::commit();

            Log::info("========== Loop completed ==========", [
                'total_requested' => $connectionNumber,
                'created' => count($created),
                'skipped' => count($skipped),
                'errors' => count($errors)
            ]);

            $message = $this->buildSummaryMessage($created, $skipped, $errors);

            Log::info('Bulk store completed successfully', [
                'created_count' => count($created)
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'created' => $created,
                    'skipped' => $skipped,
                    'errors' => $errors,
                    'summary' => [
                        'total_requested' => $connectionNumber,
                        'created_count' => count($created),
                        'skipped_count' => count($skipped),
                        'error_count' => count($errors)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Bulk connection creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create connections: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build summary message for bulk operation
     */
    private function buildSummaryMessage($created, $skipped, $errors)
    {
        $parts = [];

        if (count($created) > 0) {
            $parts[] = count($created) . " connection(s) created successfully";
        }

        if (count($skipped) > 0) {
            $parts[] = count($skipped) . " connection(s) skipped";
        }

        if (count($errors) > 0) {
            $parts[] = count($errors) . " connection(s) failed";
        }

        return implode(' | ', $parts);
    }

    /**
     * Delete connection
     */
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
            $coreA = $connection->coreA;
            $coreB = $connection->coreB;

            // Delete connection first
            $connection->delete();

            // Update core usage to inactive if no other connections exist
            $this->updateCoreUsageAfterDisconnect($coreA);
            $this->updateCoreUsageAfterDisconnect($coreB);

            // Update closure used capacity
            if ($closure->used_capacity > 0) {
                $closure->decrement('used_capacity');
            }

            DB::commit();

            Log::info('Connection deleted', [
                'connection_id' => $connection->id,
                'core_a_id' => $coreA->id,
                'core_b_id' => $coreB->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Connection deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Connection deletion failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete connection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update core usage after disconnect
     * Only set to inactive if core has no other connections
     */
    private function updateCoreUsageAfterDisconnect($core)
    {
        if (!$core) return;

        // Check if core has other connections
        $hasOtherConnections = CoreConnection::where('core_a_id', $core->id)
                                            ->orWhere('core_b_id', $core->id)
                                            ->exists();

        // If no connections, set to inactive
        if (!$hasOtherConnections) {
            $core->update(['usage' => 'inactive']);
        }
    }

    /**
     * Get all connections for a specific core
     */
    public function getCoreConnections($coreId)
    {
        try {
            $core = FiberCore::with('cable')->findOrFail($coreId);

            $user = Auth::user();
            if ($user->isAdminRegion() && $core->cable->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $connections = CoreConnection::where('core_a_id', $coreId)
                                        ->orWhere('core_b_id', $coreId)
                                        ->with(['coreA.cable', 'coreB.cable', 'closure'])
                                        ->get();

            return response()->json([
                'success' => true,
                'data' => $connections,
                'count' => $connections->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching core connections', [
                'core_id' => $coreId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch connections: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== HELPER METHODS ==========

    public function getJointClosures()
    {
        try {
            $user = Auth::user();
            $query = JointClosure::select('id', 'name', 'location', 'closure_id', 'capacity', 'used_capacity');

            if ($user->isAdminRegion()) {
                $query->where('region', $user->region);
            }

            $closures = $query->where('status', 'ok')
                ->whereRaw('used_capacity < capacity')
                ->orderBy('name')
                ->get()
                ->map(function ($closure) {
                    $closure->available_capacity = $closure->capacity - $closure->used_capacity;
                    return $closure;
                });

            return response()->json($closures);
        } catch (\Exception $e) {
            Log::error('Error loading joint closures: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load joint closures'], 500);
        }
    }

    public function getCablesByJointClosure($closureId)
    {
        try {
            $user = Auth::user();
            $closure = JointClosure::findOrFail($closureId);

            if ($user->isAdminRegion() && $closure->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $cables = Cable::select('id', 'name', 'cable_id')
                ->where('region', $closure->region)
                ->where('status', 'ok')
                ->orderBy('name')
                ->get();

            return response()->json($cables);
        } catch (\Exception $e) {
            Log::error('Error loading cables for JC: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load cables'], 500);
        }
    }

    public function getAvailableCores($cableId, $tubeNumber = null)
    {
        try {
            $user = Auth::user();
            $cable = Cable::findOrFail($cableId);

            if ($user->isAdminRegion() && $cable->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            // ✅ UPDATED: Remove "not connected" filter - show all OK cores
            $query = FiberCore::select('id', 'core_number', 'tube_number', 'status')
                ->where('cable_id', $cableId)
                ->where('status', 'ok');
            // REMOVED: ->whereDoesntHave('connectionA')
            // REMOVED: ->whereDoesntHave('connectionB')

            if ($tubeNumber) {
                $query->where('tube_number', $tubeNumber);
            }

            $cores = $query->orderBy('tube_number')
                ->orderBy('core_number')
                ->get();

            return response()->json($cores);
        } catch (\Exception $e) {
            Log::error('Error loading available cores: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load cores'], 500);
        }
    }

    public function getCablesByRegion($regionId)
    {
        try {
            $user = Auth::user();

            if ($user->isAdminRegion() && $regionId !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            // ✅ UPDATED: Show cables with OK cores (regardless of connection status)
            $cables = Cable::select('id', 'name', 'cable_id', 'source_site', 'destination_site')
                ->where('region', $regionId)
                ->where('status', 'ok')
                ->whereHas('fiberCores', function ($query) {
                    $query->where('status', 'ok');
                    // REMOVED: ->whereDoesntHave('connectionA')
                    // REMOVED: ->whereDoesntHave('connectionB')
                })
                ->orderBy('name')
                ->get();

            return response()->json($cables);
        } catch (\Exception $e) {
            Log::error('Error loading cables: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load cables'], 500);
        }
    }

    public function getTubesByCable($cableId)
    {
        try {
            $user = Auth::user();
            $cable = Cable::findOrFail($cableId);

            if ($user->isAdminRegion() && $cable->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            // ✅ UPDATED: Show tubes with OK cores (regardless of connection status)
            $availableTubes = FiberCore::where('cable_id', $cableId)
                ->where('status', 'ok')
                // REMOVED: ->whereDoesntHave('connectionA')
                // REMOVED: ->whereDoesntHave('connectionB')
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
            Log::error('Error loading cable tubes: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load tubes'], 500);
        }
    }

    public function getCoresByTube($cableId, $tubeNumber)
    {
        try {
            $user = Auth::user();
            $cable = Cable::findOrFail($cableId);

            if ($user->isAdminRegion() && $cable->region !== $user->region) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            // ✅ UPDATED: Show all OK cores (regardless of connection status)
            $cores = FiberCore::select('id', 'core_number', 'tube_number', 'status', 'attenuation')
                ->where('cable_id', $cableId)
                ->where('tube_number', $tubeNumber)
                ->where('status', 'ok')
                // REMOVED: ->whereDoesntHave('connectionA')
                // REMOVED: ->whereDoesntHave('connectionB')
                ->orderBy('core_number')
                ->get();

            return response()->json($cores);
        } catch (\Exception $e) {
            Log::error('Error loading cores: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load cores'], 500);
        }
    }
}
