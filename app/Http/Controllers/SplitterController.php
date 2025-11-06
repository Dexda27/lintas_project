<?php

namespace App\Http\Controllers;

use App\Models\Splitter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SplitterController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Splitter::query();

        // Apply regional filter for admin region users
        if ($user->isAdminRegion()) {
            $query->where('region', $user->region);
        }

        // Apply search filter if search parameter exists
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('splitter_id', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('location', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('region', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $allSplitters = $query->get();
        $statistics = [
            'total_splitters' => $allSplitters->count(),
            'active_splitters' => $allSplitters->where('status', 'ok')->count(),
            'problem_splitters' => $allSplitters->where('status', 'not_ok')->count(),
            'total_capacity' => $allSplitters->sum('capacity'),
            'used_capacity' => $allSplitters->sum('used_capacity')
        ];

        // Get splitters with pagination
        $splitters = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('splitters.index', compact('splitters', 'statistics'));
    }

    public function create()
    {
        return view('splitters.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'splitter_id' => 'required|string|unique:splitters,splitter_id',
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

        Splitter::create([
            'splitter_id' => $request->splitter_id,
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

        return redirect()->route('splitters.index')
            ->with('success', 'Splitter created successfully.');
    }

    public function show(Splitter $splitter)
    {
        $this->checkRegionAccess($splitter);

        $statistics = [
            'total_capacity' => $splitter->capacity,
            'used_capacity' => $splitter->used_capacity,
            'available_capacity' => $splitter->available_capacity,
        ];

        return view('splitters.show', compact('splitter', 'statistics'));
    }

    public function edit(Splitter $splitter)
    {
        $this->checkRegionAccess($splitter);

        return view('splitters.edit', compact('splitter'));
    }

    public function update(Request $request, Splitter $splitter)
    {
        $this->checkRegionAccess($splitter);
        $user = Auth::user();

        $request->validate([
            'splitter_id' => ['required', 'string', Rule::unique('splitters', 'splitter_id')->ignore($splitter->id)],
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'region' => 'required|string',
            'capacity' => 'integer|min:' . $splitter->used_capacity . '|max:1000',
            'status' => 'required|in:ok,not_ok',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check region access for admin
        if ($user->isAdminRegion() && $request->region !== $user->region) {
            return back()->withErrors(['region' => 'Access denied to this region.']);
        }

        $splitter->update($request->only([
            'splitter_id',
            'name',
            'location',
            'region',
            'capacity',
            'status',
            'latitude',
            'longitude',
            'description'
        ]));

        return redirect()->route('splitters.show', $splitter)
            ->with('success', 'Splitter updated successfully.');
    }

    public function destroy(Splitter $splitter)
    {
        $this->checkRegionAccess($splitter);

        // Check if splitter has any usage
        if ($splitter->used_capacity > 0) {
            return back()->withErrors(['error' => 'Cannot delete splitter with active connections. Please remove all connections first.']);
        }

        $splitter->delete();

        return redirect()->route('splitters.index')
            ->with('success', 'Splitter deleted successfully.');
    }

    private function checkRegionAccess(Splitter $splitter)
    {
        $user = Auth::user();

        if ($user->isAdminRegion() && $splitter->region !== $user->region) {
            abort(403, 'Access denied to this region.');
        }
    }
}