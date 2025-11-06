<?php

namespace App\Http\Controllers;

use App\Models\Node;
use App\Models\Svlan;
use App\Models\Cvlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // Ambil kata kunci pencarian dan per_page dari request
    $search = $request->input('search');
    $perPage = $request->input('per_page', 10); // Default 10
    
    // Validasi per_page agar hanya menerima nilai yang diizinkan
    if (!in_array($perPage, [10, 25, 50, 100])) {
        $perPage = 10;
    }

    // Query dasar untuk mengambil data node beserta relasi svlans
    $query = Node::with('svlans')->orderBy('nama_node');

    // Jika ada input pencarian, tambahkan kondisi WHERE
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('nama_node', 'like', "%{$search}%")
              ->orWhereHas('svlans', function ($svlanQuery) use ($search) {
                  $svlanQuery->where('node_id', 'like', "%{$search}%")
                             ->orWhere('svlan_vpn', 'like', "%{$search}%")
                             ->orWhere('svlan_nms', 'like', "%{$search}%");
              });
        });
    }

    // Eksekusi query dengan pagination dinamis
    $nodes = $query->paginate($perPage);

    return view('node.index', compact('nodes'));
}


    // generate sample node_id, svlan, dan cvlan
    public function generateSampleData()
    {
        // Gunakan transaction untuk memastikan semua data berhasil dibuat atau tidak sama sekali
        DB::transaction(function () {
            // Hapus data sample lama (urutannya penting: dari anak ke induk)
            Cvlan::where('nama_pelanggan', 'LIKE', 'SAMPLE CUSTOMER%')->delete();
            Svlan::where('keterangan', 'LIKE', 'Sample Keterangan SVLAN')->delete();
            Node::where('nama_node', 'LIKE', 'SAMPLE-NODE-%')->delete();

            $nodesToCreate = [];
            $timestamp = now();

            // 1. Buat 50 Data Sample Node
            for ($i = 1; $i <= 50; $i++) {
                $nodesToCreate[] = [
                    'nama_node' => 'SAMPLE-NODE-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
            Node::insert($nodesToCreate);

            // Ambil semua Node sample yang baru dibuat
            $newNodes = Node::where('nama_node', 'LIKE', 'SAMPLE-NODE-%')->get();

            $svlansToCreate = [];

            foreach ($newNodes as $node) {
                // 2. Untuk setiap Node, buat 2-3 data SVLAN
                $svlanCount = rand(2, 3);
                for ($j = 1; $j <= $svlanCount; $j++) {
                    $svlansToCreate[] = [
                        'node_id' => $node->id,
                        'svlan_nms' => (string)rand(100, 999),
                        'svlan_me' => (string)rand(1000, 9999),
                        'svlan_vpn' => (string)rand(1000, 9999),
                        'svlan_inet' => (string)rand(1000, 9999),
                        'extra' => (string)rand(1000, 9999),
                        'keterangan' => 'Sample Keterangan SVLAN',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }
            // Masukkan semua data SVLAN sekaligus
            Svlan::insert($svlansToCreate);

            // Ambil semua SVLAN sample yang baru dibuat
            $newSvlans = Svlan::where('keterangan', 'LIKE', 'Sample Keterangan SVLAN')->get();
            
            $cvlansToCreate = [];

            foreach ($newSvlans as $svlan) {
                // 3. Untuk setiap SVLAN, buat 2-5 data CVLAN
                $cvlanCount = rand(2, 5);
                for ($k = 1; $k <= $cvlanCount; $k++) {
                    $cvlansToCreate[] = [
                        'svlan_id' => $svlan->id,
                        'node_id' => $svlan->node_id,
                        'cvlan_slot' => (string)rand(1, 10),
                        'nms' => (string)rand(100, 999),
                        'vpn' => (string)rand(100, 999),
                        'inet' => (string)rand(100, 999),
                        'no_jaringan' => (string)rand(10000, 99999),
                        'nama_pelanggan' => 'SAMPLE CUSTOMER ' . $svlan->id . '-' . $k,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }
            // Masukkan semua data CVLAN sekaligus
            Cvlan::insert($cvlansToCreate);
        });

        return redirect()->route('nodes.index')
                        ->with('success', 'Data sample untuk Node, SVLAN, dan CVLAN berhasil dibuat.');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('node.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_node' => 'required|string|max:255|unique:node,nama_node',
        ]);

        Node::create($request->all());

        return redirect()->route('nodes.index')
                         ->with('success', 'Node berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Node $node)
    {
        return view('node.edit', compact('node'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Node $node)
    {
        $request->validate([
            'nama_node' => 'required|string|max:255|unique:node,nama_node,' . $node->id,
        ]);

        $node->update($request->all());

        return redirect()->route('nodes.index')
                         ->with('success', 'Node berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Node $node)
    {
        // Untuk mencegah error jika ada SVLAN yang masih terhubung
        if ($node->svlans()->count() > 0) {
            return redirect()->route('nodes.index')
                             ->with('error', 'Node tidak bisa dihapus karena masih memiliki SVLAN terkait.');
        }

        $node->delete();

        return redirect()->route('nodes.index')
                         ->with('success', 'Node berhasil dihapus.');
    }
}