<?php

namespace App\Exports;

use App\Models\Cvlan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CvlansExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;
    protected $koneksiFilter;

    // Terima parameter filter dari controller
    public function __construct($search, $koneksiFilter)
    {
        $this->search = $search;
        $this->koneksiFilter = $koneksiFilter;
    }

    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        // Logika query ini disalin dari method all() di controller Anda
        // agar data yang diekspor sama persis dengan yang ditampilkan.
        $query = Cvlan::query()->with(['svlan.node', 'node']);

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('cvlan_slot', 'like', "%{$search}%")
                  ->orWhere('no_jaringan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  // ... tambahkan pencarian lain jika perlu
                  ->orWhereHas('node', function ($subQuery) use ($search) {
                      $subQuery->where('nama_node', 'like', "%{$search}%");
                  });
            });
        }

        if ($this->koneksiFilter) {
            if ($this->koneksiFilter === 'mandiri') {
                $query->whereNull('svlan_id');
            } elseif (in_array($this->koneksiFilter, ['nms', 'metro', 'vpn', 'inet', 'extra'])) {
                $query->whereNotNull($this->koneksiFilter);
            }
        }

        return $query->orderBy('id', 'asc');
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // Definisikan header kolom Anda
        return [
            'NO',
            'Node',
            'Status',
            'CVLAN',
            'Vlan',
            'Network',
            'Customer'
        ];
    }

    /**
    * @param mixed $cvlan
    * @return array
    */
    public function map($cvlan): array
    {
        // Inisialisasi nomor urut
        static $nomor = 0;
        $nomor++;

        // Logika untuk menampilkan data, disalin dari export CSV Anda
        $node = $cvlan->svlan->node->nama_node ?? $cvlan->node->nama_node ?? 'N/A';

        $status = 'Mandiri';
        if ($cvlan->svlan) {
            $statusDetail = '';
            if ($cvlan->nms !== null) $statusDetail = "SVLAN-NMS: " . $cvlan->svlan->svlan_nms;
            elseif ($cvlan->metro !== null) $statusDetail = "SVLAN-Metro: " . $cvlan->svlan->svlan_me;
            elseif ($cvlan->vpn !== null) $statusDetail = "SVLAN-VPN: " . $cvlan->svlan->svlan_vpn;
            elseif ($cvlan->inet !== null) $statusDetail = "SVLAN-INET: " . $cvlan->svlan->svlan_inet;
            elseif ($cvlan->extra !== null) $statusDetail = "SVLAN-EXTRA: " . $cvlan->svlan->extra;
            // ... tambahkan logika lain jika perlu
            $status = "Connected (" . $statusDetail . ")";
        }

        $koneksi = '-';
        if ($cvlan->nms !== null) $koneksi = "NMS: " . $cvlan->nms;
        elseif ($cvlan->metro !== null) $koneksi = "Metro: " . $cvlan->metro;
        elseif ($cvlan->vpn !== null) $koneksi = "VPN: " . $cvlan->vpn;
        elseif ($cvlan->inet !== null) $koneksi = "INET: " . $cvlan->inet;
        elseif ($cvlan->extra !== null) $koneksi = "EXTRA: " . $cvlan->extra;
        // ... tambahkan logika lain jika perlu

        return [
            $nomor,
            $node,
            $status,
            $cvlan->cvlan_slot ?? '-', // Beri nilai default jika null
            $koneksi,
            $cvlan->no_jaringan ?? '-',
            $cvlan->nama_pelanggan ?? '-',
        ];
    }

    /**
    * @param Worksheet $sheet
    */
    public function styles(Worksheet $sheet)
    {
        return [
            // Membuat baris pertama (header) menjadi tebal
            1    => ['font' => ['bold' => true]],

            // Membuat semua kolom dari A sampai G memiliki perataan tengah
            'A:G' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }
}
