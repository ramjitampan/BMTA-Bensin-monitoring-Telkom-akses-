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

                $sheet->getRowDimension(1)->setRowHeight(34);
                $sheet->getRowDimension($headerRow)->setRowHeight(32);
                $sheet->getStyle('A1:L3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1:L3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:L1')->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle("A{$headerRow}:L{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D71920']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle("A{$headerRow}:L{$totalRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('555555');
                $sheet->getStyle("A{$firstDataRow}:L{$lastDataRow}")->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle("A{$firstDataRow}:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$firstDataRow}:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$firstDataRow}:J{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F{$firstDataRow}:F{$lastDataRow}")->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle("G{$firstDataRow}:J{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("K{$firstDataRow}:L{$totalRow}")->getNumberFormat()->setFormatCode('[$Rp-421] #,##0');

                if ($this->perjalanans->isNotEmpty()) {
                    $sheet->setCellValue("L{$totalRow}", "=SUM(L{$firstDataRow}:L{$lastDataRow})");
                } else {
                    $sheet->setCellValue("L{$totalRow}", 0);
                }

                $sheet->getStyle("A{$totalRow}:L{$totalRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$totalRow}:L{$totalRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FCE8E8');
                $sheet->getStyle("A{$footerStartRow}:L".($footerStartRow + 3))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getColumnDimension('A')->setWidth(7);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(32);
                $sheet->getColumnDimension('D')->setWidth(19);
                $sheet->getColumnDimension('E')->setWidth(15);
                foreach (range('F', 'L') as $column) {
                    $sheet->getColumnDimension($column)->setWidth(14);
                }
            },
        ];
    }
}
