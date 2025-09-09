<?php

namespace App\Http\Controllers;

use App\Models\Cvlan;
use App\Models\Svlan;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvlanController extends Controller
{
    /**
     * Menampilkan daftar CVLAN untuk SVLAN tertentu.
     */
    public function index($svlan_id, Request $request)
    {
        $svlan = Svlan::with('node')->findOrFail($svlan_id);
        
        $search = $request->query('search');
        $sortField = $request->query('sort', 'cvlan_slot');
        $sortOrder = $request->query('order', 'asc');
        $koneksiFilter = $request->query('koneksi_filter');

        $cvlansQuery = $svlan->cvlans();

        if ($koneksiFilter) {
        if ($koneksiFilter === 'metro') {
            $cvlansQuery->whereNotNull('metro');
        } elseif ($koneksiFilter === 'vpn') {
            $cvlansQuery->whereNotNull('vpn');
        } elseif ($koneksiFilter === 'inet') {
            $cvlansQuery->whereNotNull('inet');
        } elseif ($koneksiFilter === 'nms') {
            $cvlansQuery->whereNotNull('nms');
        } elseif ($koneksiFilter === 'extra') {
            $cvlansQuery->whereNotNull('extra');
        }
    }

    // 2. Terapkan PENCARIAN setelah filter
    if ($search) {
        $cvlansQuery->where(function ($query) use ($search) {
            // Selalu cari di semua kolom ini, tidak peduli apa filternya
            $query->where('no_jaringan', 'like', "%{$search}%")
                ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                ->orWhere('nms', 'like', "%{$search}%")
                ->orWhere('metro', 'like', "%{$search}%")
                ->orWhere('vpn', 'like', "%{$search}%")
                ->orWhere('inet', 'like', "%{$search}%")
                ->orWhere('extra', 'like', "%{$search}%");
        });
    }
        
        $cvlans = $cvlansQuery->orderBy($sortField, $sortOrder)->get();

        return view('cvlan.index', compact('svlan', 'cvlans', 'koneksiFilter'));
    }

    /**
     * Menampilkan formulir untuk membuat CVLAN baru untuk SVLAN tertentu.
     */
    public function create($svlan_id)
    {
        $svlan = Svlan::findOrFail($svlan_id);
        return view('cvlan.create', compact('svlan'));
    }

    /**
     * Menyimpan CVLAN baru untuk SVLAN tertentu.
     */
    // Method store() di dalam CvlanController.php
    public function store($svlan_id, Request $request)
    {
        $svlan = Svlan::findOrFail($svlan_id);

        $validated = $request->validate([
            'cvlan_slot'       => 'nullable|string|max:255',
            'connection_type'  => 'nullable|in:nms,metro,vpn,inet,extra',
            'connection_value' => 'nullable|integer|max:9999',
            'no_jaringan'      => 'nullable|string|max:255',
            'nama_pelanggan'   => 'nullable|string|max:255',
        ]);
        
        $dataToCreate = [
            'svlan_id'        => $svlan->id,
            'node_id'         => $svlan->node_id,
            'no_jaringan'     => $validated['no_jaringan'],
            'nama_pelanggan'  => $validated['nama_pelanggan'],
            'cvlan_slot'      => null,
            'nms'             => null,
            'metro'           => null,
            'vpn'             => null,
            'inet'            => null,
            'extra'            => null,
        ];

        $connectionType = $validated['connection_type'] ?? null;
        $connectionValue = $validated['connection_value'] ?? null;

        if ($connectionType && $connectionValue) {
            if ($connectionType === 'nms') { // <-- TAMBAHKAN BLOK INI
                $dataToCreate['nms'] = $connectionValue;
            } elseif ($connectionType === 'metro') {
                $dataToCreate['metro'] = $connectionValue;
            } elseif ($connectionType === 'vpn') {
                $dataToCreate['vpn'] = $connectionValue;
            } elseif ($connectionType === 'inet') {
                $dataToCreate['inet'] = $connectionValue;
            } elseif ($connectionType === 'extra') {
                $dataToCreate['extra'] = $connectionValue;
            }
            
        }

        $svlan->cvlans()->create($dataToCreate);

        $originFilter = $request->input('koneksi_filter_origin');

        // Gunakan nilai filter saat melakukan redirect
        return redirect()->route('cvlan.index', [
            'svlan_id' => $svlan->id, 
            'koneksi_filter' => $originFilter
        ])->with('success', 'CVLAN berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir untuk mengedit CVLAN tertentu.
     */
    public function edit($svlan_id, $id)
    {
        $cvlan = Cvlan::findOrFail($id);
        if ($cvlan->svlan_id != $svlan_id) {
             return redirect()->route('cvlan.index', $svlan_id)->with('error', 'CVLAN tidak ditemukan dalam SVLAN ini.');
        }

        $svlan = Svlan::findOrFail($svlan_id);
        $allSvlan = Svlan::with('node')->get();
        $nodes = Node::all();
        
        return view('cvlan.edit', compact('cvlan', 'svlan', 'allSvlan', 'nodes'));
    }

    /**
     * Memperbarui CVLAN tertentu.
     */
    public function update($svlan_id, $id, Request $request)
    {
        $cvlan = Cvlan::findOrFail($id);
        if ($cvlan->svlan_id != $svlan_id && $request->input('is_standalone') != '1') {
            return back()->with('error', 'Akses tidak sah.');
        }

        $isStandalone = $request->input('is_standalone') == '1';

        // Aturan validasi yang umum untuk kedua kondisi
        $commonRules = [
            'no_jaringan'    => 'nullable|string|max:255',
            'nama_pelanggan' => 'nullable|string|max:255',
            'origin'         => 'nullable|string',
            'koneksi_filter_origin' => 'nullable|string'
        ];

        // Aturan validasi spesifik berdasarkan status
        if ($isStandalone) {
            $specificRules = [
                'node_id'    => 'required|exists:node,id',
                'cvlan_slot' => 'required|string|max:255' // WAJIB jika standalone
            ];
        } else {
            $specificRules = [
                'svlan_id'         => 'required|exists:svlan,id',
                'connection_type'  => 'nullable|in:nms,metro,vpn,inet,extra',
                'connection_value' => 'nullable|integer|max:9999',
                'cvlan_slot'       => 'nullable|string|max:255' // Boleh kosong jika terhubung
            ];
        }

        $validated = $request->validate(array_merge($commonRules, $specificRules));

        // --- Logika Penyimpanan Data ---
        $cvlan->no_jaringan = $validated['no_jaringan'];
        $cvlan->nama_pelanggan = $validated['nama_pelanggan'];

        if ($isStandalone) {
            $cvlan->svlan_id = null;
            $cvlan->node_id = $validated['node_id'];
            $cvlan->cvlan_slot = $validated['cvlan_slot']; // Simpan nilainya
            // Reset koneksi karena sekarang dikelola di level CVLAN mandiri (jika ada)
            $cvlan->nms = null; $cvlan->metro = null; $cvlan->vpn = null; $cvlan->inet = null; $cvlan->extra = null;
        } else {
            $newSvlan = Svlan::find($validated['svlan_id']);
            $cvlan->svlan_id = $newSvlan->id;
            $cvlan->node_id = $newSvlan->node_id;
            
            // Atur cvlan_slot menjadi NULL jika terhubung ke SVLAN
            $cvlan->cvlan_slot = null; 
            
            // Reset dan atur koneksi baru
            $cvlan->nms = null; $cvlan->metro = null; $cvlan->vpn = null; $cvlan->inet = null; $cvlan->extra = null;
            $connectionType = $validated['connection_type'] ?? null;
            $connectionValue = $validated['connection_value'] ?? null;
            if ($connectionType && $connectionValue) {
                $cvlan->{$connectionType} = $connectionValue;
            }
        }
        
        $cvlan->save();
        
        // Logika redirect
        $origin = $request->input('origin');
        $originFilter = $request->input('koneksi_filter_origin');

        if ($isStandalone || $origin === 'all') {
            return redirect()->route('cvlan.all')->with('success', 'CVLAN berhasil diperbarui.');
        }

        return redirect()->route('cvlan.index', [
            'svlan_id' => $request->input('svlan_id'),
            'koneksi_filter' => $originFilter
        ])->with('success', 'CVLAN berhasil diperbarui!');
    }

    /**
     * Menghapus CVLAN tertentu.
     */
    public function destroy($svlan_id, $id, Request $request) // Tambahkan Request $request
    {
        $cvlan = Cvlan::findOrFail($id);
        $cvlan->delete();

        // Ambil nilai filter asal dari hidden input
        $originFilter = $request->input('koneksi_filter_origin');

        // Gunakan nilai filter saat melakukan redirect
        return redirect()->route('cvlan.index', [
            'svlan_id' => $svlan_id,
            'koneksi_filter' => $originFilter
        ])->with('success', 'CVLAN berhasil dihapus.');
    }
    
//-------------------------------------------------------------------------------------------------------

    /**
     * Menampilkan daftar semua CVLAN.
     */
    public function all(Request $request)
    {
        $search = $request->query('search');
        $sortField = $request->query('sort', 'id'); 
        $sortOrder = $request->query('order', 'asc');
        $koneksiFilter = $request->query('koneksi_filter');

        $query = Cvlan::with(['svlan.node', 'node']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cvlan_slot', 'like', "%{$search}%")
                ->orWhere('no_jaringan', 'like', "%{$search}%")
                ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                ->orWhere('nms', 'like', "%{$search}%")
                ->orWhere('metro', 'like', "%{$search}%")
                ->orWhere('vpn', 'like', "%{$search}%")
                ->orWhere('inet', 'like', "%{$search}%")
                ->orWhere('extra', 'like', "%{$search}%")
                ->orWhereHas('node', function ($subQuery) use ($search) {
                    $subQuery->where('nama_node', 'like', "%{$search}%");
                })
                ->orWhereHas('svlan', function ($subQuery) use ($search) {
                    $subQuery->where('svlan_nms', 'like', "%{$search}%");
                });
            });
        }

        if ($koneksiFilter) {
            if ($koneksiFilter === 'mandiri') {
                $query->whereNull('svlan_id');
            } elseif ($koneksiFilter === 'metro') {
                $query->whereNotNull('metro');
            } elseif ($koneksiFilter === 'vpn') {
                $query->whereNotNull('vpn');
            } elseif ($koneksiFilter === 'inet') {
                $query->whereNotNull('inet');
            } elseif ($koneksiFilter === 'nms') {
                $query->whereNotNull('nms');
            } elseif ($koneksiFilter === 'extra') {
                $query->whereNotNull('extra');
            }
        }
        if ($sortField === 'node_id') {
            // Perbaikan nama tabel: gunakan 'node' (bukan 'nodes') sesuai migrasi
            $query->leftJoin('node as node_direct', 'cvlans.node_id', '=', 'node_direct.id')
                ->leftJoin('svlan', 'cvlans.svlan_id', '=', 'svlan.id')
                ->leftJoin('node as node_via_svlan', 'svlan.node_id', '=', 'node_via_svlan.id')
                ->orderBy(DB::raw('COALESCE(node_direct.nama_node, node_via_svlan.nama_node)'), $sortOrder)
                ->select('cvlans.*');
        } else {
            $query->orderBy($sortField, $sortOrder);
        }
        
        $cvlans = $query->paginate(15);
        
        return view('cvlan.all', compact('cvlans', 'sortField', 'sortOrder', 'koneksiFilter'));
    }

    /**
     * Menampilkan formulir untuk membuat CVLAN mandiri baru.
     */
    public function createall()
    {
        $svlans = Svlan::with('node')->get();
        $nodes = Node::all();
        return view('cvlan.createall', compact('svlans', 'nodes'));
    }

    /**
     * Menyimpan CVLAN baru (mandiri atau terikat SVLAN).
     */
    public function storeall(Request $request)
    {
        // Validasi data tetap sama
        if ($request->input('is_standalone') == '1') {
            $validated = $request->validate([
                'node_id'         => 'required|exists:node,id',
                'cvlan_slot'      => 'nullable|string|max:255',
                'no_jaringan'     => 'nullable|string|max:255',
                'nama_pelanggan'  => 'nullable|string|max:255',
            ]);
        } else { // Jika terhubung ke SVLAN
            $validated = $request->validate([
                'svlan_id'         => 'required|exists:svlan,id',
                'cvlan_slot'       => 'nullable|string|max:255',
                'connection_type'  => 'nullable|in:nms,metro,vpn,inet,extra',
                'connection_value' => 'nullable|integer|max:9999',
                'no_jaringan'      => 'nullable|string|max:255',
                'nama_pelanggan'   => 'nullable|string|max:255',
            ]);
        }

        // PERBAIKAN: Buat objek baru dan set properti satu per satu
        $cvlan = new Cvlan();
        $cvlan->cvlan_slot = $validated['cvlan_slot'];
        $cvlan->no_jaringan = $validated['no_jaringan'] ?? null;
        $cvlan->nama_pelanggan = $validated['nama_pelanggan'] ?? null;

        if ($request->input('is_standalone') == '1') {
            $cvlan->svlan_id = null;
            $cvlan->node_id = $validated['node_id'];
            $cvlan->nms = null;
            $cvlan->metro = null;
            $cvlan->vpn = null;
            $cvlan->inet = null;
            $cvlan->extra = null;
        } else {
            $svlan = Svlan::find($validated['svlan_id']);
            $cvlan->svlan_id = $svlan->id;
            $cvlan->node_id = $svlan->node_id;

            $connectionType = $validated['connection_type'] ?? null;
            $connectionValue = $validated['connection_value'] ?? null;
            
            $cvlan->nms = null;
            $cvlan->metro = null;
            $cvlan->vpn = null;
            $cvlan->inet = null;
            $cvlan->extra = null;

            if ($connectionType && $connectionValue) {
                if ($connectionType === 'nms') {
                    $cvlan->nms = $connectionValue;
                } elseif ($connectionType === 'metro') {
                $cvlan->metro = $connectionValue;
                } elseif ($connectionType === 'vpn') {
                    $cvlan->vpn = $connectionValue;
                } elseif ($connectionType === 'inet') {
                    $cvlan->inet = $connectionValue;
                } elseif ($connectionType === 'extra') {
                    $cvlan->extra = $connectionValue;
                }
            }
        }

        $cvlan->save(); // Simpan ke database

        return redirect()->route('cvlan.all')->with('success', 'CVLAN berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir untuk mengedit CVLAN mandiri.
     */
    public function editall($id)
    {
        $cvlan = Cvlan::with(['svlan', 'node'])->findOrFail($id);
        $svlans = Svlan::with('node')->get();
        $nodes = Node::all();
        return view('cvlan.editall', compact('cvlan', 'svlans', 'nodes'));
    }

    /**
     * Memperbarui CVLAN (mandiri atau terikat SVLAN).
     */
    public function updateAll(Request $request, $id)
    {
        $cvlan = Cvlan::findOrFail($id);
        $isStandalone = $request->input('is_standalone') == '1';

        $commonRules = [
            'no_jaringan'    => 'nullable|string|max:255',
            'nama_pelanggan' => 'nullable|string|max:255',
            'origin'         => 'nullable|string',
        ];

        if ($isStandalone) {
            $specificRules = [
                'node_id'    => 'required|exists:node,id',
                'cvlan_slot' => 'required|string|max:255' // WAJIB jika standalone
            ];
        } else {
            $specificRules = [
                'svlan_id'         => 'required|exists:svlan,id',
                'connection_type'  => 'nullable|in:nms,metro,vpn,inet,extra',
                'connection_value' => 'nullable|integer|max:9999',
                'cvlan_slot'       => 'nullable|string|max:255' // Boleh kosong jika terhubung
            ];
        }
        
        $validated = $request->validate(array_merge($commonRules, $specificRules));

        // --- Logika Penyimpanan Data ---
        $cvlan->no_jaringan = $validated['no_jaringan'];
        $cvlan->nama_pelanggan = $validated['nama_pelanggan'];

        if ($isStandalone) {
            $cvlan->svlan_id = null;
            $cvlan->node_id = $validated['node_id'];
            $cvlan->cvlan_slot = $validated['cvlan_slot']; // Simpan nilainya
            $cvlan->nms = null; $cvlan->metro = null; $cvlan->vpn = null; $cvlan->inet = null; $cvlan->extra = null;
        } else {
            $newSvlan = Svlan::find($validated['svlan_id']);
            $cvlan->svlan_id = $newSvlan->id;
            $cvlan->node_id = $newSvlan->node_id;
            
            // Atur cvlan_slot menjadi NULL jika terhubung ke SVLAN
            $cvlan->cvlan_slot = null;
            
            // Reset dan atur koneksi baru
            $cvlan->nms = null; $cvlan->metro = null; $cvlan->vpn = null; $cvlan->inet = null; $cvlan->extra = null;
            $connectionType = $validated['connection_type'] ?? null;
            $connectionValue = $validated['connection_value'] ?? null;
            if ($connectionType && $connectionValue) {
                $cvlan->{$connectionType} = $connectionValue;
            }
        }
        
        $cvlan->save();
        
        return redirect()->route('cvlan.all')->with('success', 'CVLAN berhasil diperbarui.');
    }

    /**
     * Menghapus CVLAN.
     */
    public function destroyAll($id)
    {
        $cvlan = Cvlan::findOrFail($id);
        $cvlan->delete();
        return redirect()->route('cvlan.all')->with('success', 'CVLAN berhasil dihapus!');
    }
    
    
    /**
     * Export data CVLAN (semua atau terfilter) ke file CSV.
     */
    public function exportAllCsv(Request $request)
    {
        // Logika query untuk mendapatkan data CVLAN (sama seperti di method all())
        $search = $request->query('search');
        $koneksiFilter = $request->query('koneksi_filter');
        $query = Cvlan::with(['svlan.node', 'node']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cvlan_slot', 'like', "%{$search}%")
                  ->orWhere('no_jaringan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhereHas('node', function ($nodeQuery) use ($search) {
                      $nodeQuery->where('nama_node', 'like', "%{$search}%");
                  })
                  ->orWhereHas('svlan.node', function ($nodeQuery) use ($search) {
                      $nodeQuery->where('nama_node', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($koneksiFilter) {
            if ($koneksiFilter === 'mandiri') {
                $query->whereNull('svlan_id');
            } elseif (in_array($koneksiFilter, ['nms', 'metro', 'vpn', 'inet', 'extra'])) {
                $query->whereNotNull($koneksiFilter);
            }
        }
        
        $cvlans = $query->get();

        $fileName = 'semua_cvlan_export_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        // ======================= AWAL PERUBAHAN =======================

        // 1. Ubah header CSV agar sesuai dengan tampilan tabel
        $columns = [
            'Node', 'Status', 'CVLAN Slot', 'Koneksi', 'No Jaringan', 'Nama Pelanggan'
        ];

        $callback = function() use($cvlans, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($cvlans as $cvlan) {
                
                // 2. Buat logika PHP untuk meniru tampilan di Blade
                $node = $cvlan->svlan->node->nama_node ?? $cvlan->node->nama_node ?? 'N/A';
                
                // Logika untuk kolom STATUS
                $status = 'Mandiri';
                if ($cvlan->svlan) {
                    $statusDetail = '';
                    if ($cvlan->nms !== null) {
                        $statusDetail = "SVLAN-NMS: " . $cvlan->svlan->svlan_nms;
                    } elseif ($cvlan->metro !== null) {
                        $statusDetail = "SVLAN-Metro: " . $cvlan->svlan->svlan_me;
                    } elseif ($cvlan->vpn !== null) {
                        $statusDetail = "SVLAN-VPN: " . $cvlan->svlan->svlan_vpn;
                    } elseif ($cvlan->inet !== null) {
                        $statusDetail = "SVLAN-INET: " . $cvlan->svlan->svlan_inet;
                    } elseif ($cvlan->extra !== null) {
                        $statusDetail = "SVLAN-Extra: " . $cvlan->svlan->extra;
                    } else {
                        $statusDetail = "SVLAN: " . $cvlan->svlan->svlan_nms;
                    }
                    $status = "Terhubung (" . $statusDetail . ")";
                }

                // Logika untuk kolom KONEKSI
                $koneksi = '-';
                if ($cvlan->nms !== null) {
                    $koneksi = "NMS: " . $cvlan->nms;
                } elseif ($cvlan->metro !== null) {
                    $koneksi = "Metro: " . $cvlan->metro;
                } elseif ($cvlan->vpn !== null) {
                    $koneksi = "VPN: " . $cvlan->vpn;
                } elseif ($cvlan->inet !== null) {
                    $koneksi = "INET: " . $cvlan->inet;
                } elseif ($cvlan->extra !== null) {
                    $koneksi = "Extra: " . $cvlan->extra;
                }

                // 3. Susun baris CSV sesuai format baru
                $row = [
                    'Node'           => $node,
                    'Status'         => $status,
                    'CVLAN Slot'     => $cvlan->cvlan_slot,
                    'Koneksi'        => $koneksi,
                    'No Jaringan'    => $cvlan->no_jaringan ?? 'N/A',
                    'Nama Pelanggan' => $cvlan->nama_pelanggan ?? 'N/A',
                ];

                fputcsv($file, $row);
            }
            fclose($file);
        };
        // ======================== AKHIR PERUBAHAN ========================

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Export data CVLAN untuk SVLAN tertentu ke CSV.
     */
    public function exportCsvForSvlan($svlan_id, Request $request)
    {
        $svlan = Svlan::with('node')->findOrFail($svlan_id);

        $search = $request->query('search');
        $koneksiFilter = $request->query('koneksi_filter');

        $query = $svlan->cvlans()->with(['svlan.node', 'node']);

        if ($koneksiFilter) {
            if (in_array($koneksiFilter, ['nms', 'metro', 'vpn', 'inet', 'extra'])) {
                $query->whereNotNull($koneksiFilter);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cvlan_slot', 'like', "%{$search}%")
                  ->orWhere('no_jaringan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('nms', 'like', "%{$search}%")
                  ->orWhere('metro', 'like', "%{$search}%")
                  ->orWhere('vpn', 'like', "%{$search}%")
                  ->orWhere('inet', 'like', "%{$search}%")
                  ->orWhere('extra', 'like', "%{$search}%");
            });
        }

        $cvlans = $query->orderBy('id', 'asc')->get();

        $fileName = 'cvlan_svlan_' . $svlan->id . '_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Node', 'Status', 'CVLAN Slot', 'Koneksi', 'No Jaringan', 'Nama Pelanggan'];

        $callback = function () use ($cvlans, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($cvlans as $cvlan) {
                $node = $cvlan->svlan->node->nama_node ?? $cvlan->node->nama_node ?? 'N/A';

                $status = 'Mandiri';
                if ($cvlan->svlan) {
                    $statusDetail = '';
                    if ($cvlan->nms !== null) {
                        $statusDetail = 'SVLAN-NMS: ' . ($cvlan->svlan->svlan_nms ?? '');
                    } elseif ($cvlan->metro !== null) {
                        $statusDetail = 'SVLAN-Metro: ' . ($cvlan->svlan->svlan_me ?? '');
                    } elseif ($cvlan->vpn !== null) {
                        $statusDetail = 'SVLAN-VPN: ' . ($cvlan->svlan->svlan_vpn ?? '');
                    } elseif ($cvlan->inet !== null) {
                        $statusDetail = 'SVLAN-INET: ' . ($cvlan->svlan->svlan_inet ?? '');
                    } elseif ($cvlan->extra !== null) {
                        $statusDetail = 'SVLAN-Extra: ' . ($cvlan->svlan->extra ?? '');
                    } else {
                        $statusDetail = 'SVLAN: ' . ($cvlan->svlan->svlan_nms ?? '');
                    }
                    $status = 'Terhubung (' . $statusDetail . ')';
                }

                $koneksi = '-';
                if ($cvlan->nms !== null) {
                    $koneksi = 'NMS: ' . $cvlan->nms;
                } elseif ($cvlan->metro !== null) {
                    $koneksi = 'Metro: ' . $cvlan->metro;
                } elseif ($cvlan->vpn !== null) {
                    $koneksi = 'VPN: ' . $cvlan->vpn;
                } elseif ($cvlan->inet !== null) {
                    $koneksi = 'INET: ' . $cvlan->inet;
                } elseif ($cvlan->extra !== null) {
                    $koneksi = 'Extra: ' . $cvlan->extra;
                }

                $row = [
                    'Node' => $node,
                    'Status' => $status,
                    'CVLAN Slot' => $cvlan->cvlan_slot,
                    'Koneksi' => $koneksi,
                    'No Jaringan' => $cvlan->no_jaringan ?? 'N/A',
                    'Nama Pelanggan' => $cvlan->nama_pelanggan ?? 'N/A',
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
