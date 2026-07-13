<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PerjalananMonthlyExport implements FromView, ShouldAutoSize, WithDrawings, WithEvents, WithTitle
{
    public function __construct(
        private readonly Collection $perjalanans,
        private readonly int $bulan,
        private readonly int $tahun,
    ) {
    }

    public function view(): View
    {
        return view('exports.perjalanan-excel', [
            'perjalanans' => $this->perjalanans,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'tifNumber' => config('perjalanan_report.tif_prefix').'/'.$this->tahun,
        ]);
    }

    public function title(): string
    {
        return 'Laporan BBM';
    }

    public function drawings(): BaseDrawing|array
    {
        $logoPath = config('perjalanan_report.logo');

        if (! is_string($logoPath) || ! is_file($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo Telkom Akses');
        $drawing->setPath($logoPath);
        $drawing->setHeight(48);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);

        return $drawing;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $headerRow = 5;
                $firstDataRow = 6;
                $lastDataRow = $firstDataRow + max($this->perjalanans->count(), 1) - 1;
                $totalRow = $lastDataRow + 1;
                $footerStartRow = $totalRow + 3;

                $sheet->getPageSetup()->setOrientation('landscape');
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5)->setRight(0.3)->setBottom(0.5)->setLeft(0.3);
                $sheet->freezePane('A6');

                // Baris judul & info
                $sheet->getRowDimension(1)->setRowHeight(34);
                $sheet->getRowDimension($headerRow)->setRowHeight(32);
                $sheet->getStyle('A1:L3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1:L3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:L1')->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle('A2:L2')->getFont()->setSize(10);
                $sheet->getStyle('A3:L3')->getFont()->setSize(10);

                // Header tabel (baris 5) — merah
                $sheet->getStyle("A{$headerRow}:L{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D71920']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                // Border semua sel data + header
                $sheet->getStyle("A{$headerRow}:L{$totalRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('555555');

                // Alignment data
                $sheet->getStyle("A{$firstDataRow}:L{$lastDataRow}")
                    ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

                $sheet->getStyle("A{$firstDataRow}:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$firstDataRow}:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$firstDataRow}:J{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("K{$firstDataRow}:L{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Format angka Indonesia
                $sheet->getStyle("G{$firstDataRow}:G{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("H{$firstDataRow}:J{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("K{$firstDataRow}:K{$totalRow}")->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');
                $sheet->getStyle("L{$firstDataRow}:L{$totalRow}")->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');

                // Total (SUM)
                if ($this->perjalanans->isNotEmpty()) {
                    $sheet->setCellValue("L{$totalRow}", "=SUM(L{$firstDataRow}:L{$lastDataRow})");
                } else {
                    $sheet->setCellValue("L{$totalRow}", 0);
                }

                // Styling total row
                $sheet->getStyle("A{$totalRow}:L{$totalRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$totalRow}:L{$totalRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FCE8E8');

                // Signature area — center
                $sheet->getStyle("A{$footerStartRow}:L".($footerStartRow + 3))
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(13);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(28);
                $sheet->getColumnDimension('E')->setWidth(16);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(12);
                $sheet->getColumnDimension('J')->setWidth(10);
                $sheet->getColumnDimension('K')->setWidth(14);
                $sheet->getColumnDimension('L')->setWidth(16);
            },
        ];
    }
}
