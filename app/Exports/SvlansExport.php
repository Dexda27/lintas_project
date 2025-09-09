<?php

namespace App\Exports;

use App\Models\Svlan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SvlansExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        $query = Svlan::query()->with('node');

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('svlan_nms', 'like', "%{$search}%")
                  ->orWhere('svlan_vpn', 'like', "%{$search}%")
                  ->orWhere('svlan_inet', 'like', "%{$search}%")
                  ->orWhereHas('node', function ($nodeQuery) use ($search) {
                      $nodeQuery->where('nama_node', 'like', "%{$search}%");
                  });
            });
        }
        
        return $query;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'NO',
            'NODE ID',
            'SVLAN-NMS',
            'SVLAN-ME',
            'SVLAN-VPN',
            'SVLAN-INET',
            'EXTRA',
            'Keterangan',
        ];
    }

    /**
    * @param mixed $svlan
    * @return array
    */
    public function map($svlan): array
    {
        static $nomor = 0;
        $nomor++;

        return [
            $nomor,
            $svlan->node->nama_node ?? $svlan->node_id,
            $svlan->svlan_nms,
            $svlan->svlan_me,
            $svlan->svlan_vpn,
            $svlan->svlan_inet,
            $svlan->extra,
            $svlan->keterangan,
        ];
    }

    /**
    * @param Worksheet $sheet
    */
    public function styles(Worksheet $sheet)
    {
        return [
            // Membuat baris pertama (header) menjadi tebal (bold)
            1    => ['font' => ['bold' => true]],

            // Membuat seluruh kolom (A sampai G) memiliki perataan tengah
            'A:G' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }
}