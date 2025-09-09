<?php

namespace App\Http\Controllers;

use App\Models\Svlan;
use App\Models\Node;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SvlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    // Ambil parameter dari URL, berikan nilai default
    $search = $request->query('search');
    $sortField = $request->query('sort', 'id');
    $sortOrder = $request->query('order', 'asc');

    // Query dasar dengan eager loading untuk relasi node dan cvlans
    $query = Svlan::with(['cvlans', 'node']);

    // Logika untuk pencarian
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('svlan_nms', 'like', "%{$search}%")
              ->orWhere('svlan_vpn', 'like', "%{$search}%")
              ->orWhere('svlan_inet', 'like', "%{$search}%")
              // Pencarian berdasarkan nama node dari relasi
              ->orWhereHas('node', function ($nodeQuery) use ($search) {
                  $nodeQuery->where('nama_node', 'like', "%{$search}%");
              });
        });
    }
    
    // Logika untuk sorting
    // PERBAIKAN: Logika khusus untuk sorting berdasarkan nama_node
    if ($sortField === 'node_id') {
        $query->join('node', 'svlan.node_id', '=', 'node.id')
              ->orderBy('node.nama_node', $sortOrder)
              ->select('svlan.*'); // Penting agar tidak ada konflik kolom ID
    } else {
        // Sorting untuk kolom lain di tabel svlan
        $query->orderBy($sortField, $sortOrder);
    }

    $svlans = $query->paginate(5); // Angka 5 bisa disesuaikan
    
    return view('svlan.index', compact('svlans', 'sortField', 'sortOrder'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // AMBIL SEMUA DATA NODE UNTUK DITAMPILKAN DI DROPDOWN
        $nodes = Node::orderBy('nama_node')->get();
        
        // KIRIM DATA NODES KE VIEW
        return view('svlan.create', compact('nodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // PERKUAT ATURAN VALIDASI UNTUK NODE_ID
        $request->validate([
            'node_id'    => 'required|integer|exists:node,id', // Pastikan isinya angka & ada di tabel node
            'svlan_nms'  => 'required',
            'svlan_me'   => 'nullable',
            'svlan_vpn'  => 'required',
            'svlan_inet' => 'required',
            'extra'      => 'nullable',
            'keterangan' => 'nullable'
        ]);

        Svlan::create($request->all());
        return redirect()->route('svlan.index')->with('success', 'Data SVLAN berhasil ditambahkan.');
    }

    public function edit(Svlan $svlan)
    {
        // TAMBAHKAN BARIS INI UNTUK MENGAMBIL SEMUA NODE
        $nodes = Node::orderBy('nama_node')->get();

        // KIRIM DATA $nodes KE VIEW
        return view('svlan.edit', compact('svlan', 'nodes'));
    }

    public function update(Request $request, Svlan $svlan)
    {
        $request->validate([
            'node_id' => 'required',
            'svlan_nms' => 'required',
            'svlan_me' => 'nullable',
            'svlan_vpn' => 'required',
            'svlan_inet' => 'required',
            'extra' => 'nullable',
            'keterangan' => 'nullable'
        ]);

        $svlan->update($request->all());
        return redirect()->route('svlan.index')->with('success', 'Data SVLAN berhasil diperbarui.');
    }

    public function destroy(Request $request, Svlan $svlan)
    {
        $cascadeDelete = $request->input('cascade_delete') === 'true';

        if ($cascadeDelete) {
            $svlan->cvlans()->delete();
            $svlan->delete();
            $message = 'SVLAN dan semua CVLAN terkait berhasil dihapus.';
        } else {
            $svlan->delete();
            $message = 'SVLAN berhasil dihapus. CVLAN terkait kini menjadi mandiri.';
        }

        return redirect()->route('svlan.index')->with('success', $message);
    }
    
    /**
     * Export all (or filtered) SVLANs to a CSV file (without CVLAN details).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportAll(Request $request)
    {
        // Logika query untuk filtering tetap sama
        $search = $request->query('search');
        $query = Svlan::with('node'); // Eager load node

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('svlan_nms', 'like', "%{$search}%")
                  ->orWhere('svlan_vpn', 'like', "%{$search}%")
                  ->orWhere('svlan_inet', 'like', "%{$search}%")
                  ->orWhereHas('node', function ($nodeQuery) use ($search) {
                      $nodeQuery->where('nama_node', 'like', "%{$search}%");
                  });
            });
        }
        
        $svlans = $query->get();

        $fileName = 'semua_svlan_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 1. HAPUS SEMUA KOLOM CVLAN DARI HEADER
        $columns = [
            'Node ID', 'NMS', 'ME', 'VPN', 'INET', 'Extra', 'Keterangan'
        ];

        $callback = function() use($svlans, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // 2. LOOPING HANYA UNTUK SETIAP SVLAN (LOGIKA CVLAN DIHILANGKAN)
            foreach ($svlans as $svlan) {
                $row = [
                    'Node ID'      => $svlan->node->nama_node ?? $svlan->node_id,
                    'NMS'          => $svlan->svlan_nms,
                    'ME'           => $svlan->svlan_me,
                    'VPN'          => $svlan->svlan_vpn,
                    'INET'         => $svlan->svlan_inet,
                    'Extra'        => $svlan->extra,
                    'Keterangan'   => $svlan->keterangan,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}