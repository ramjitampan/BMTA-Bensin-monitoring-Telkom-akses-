<table>
    <tr>
        <th colspan="12">{{ config('perjalanan_report.title') }}</th>
    </tr>
    <tr>
        <th colspan="12">Nomor: {{ $tifNumber }}</th>
    </tr>
    <tr>
        <th colspan="12">Periode: {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</th>
    </tr>
    <tr><td colspan="12"></td></tr>
    <tr>
        <th>NO</th>
        <th>TANGGAL</th>
        <th>URAIAN</th>
        <th>TUJUAN</th>
        <th>KENDARAAN</th>
        <th>NO POLISI</th>
        <th>VOLUME (L)</th>
        <th>KM LAMA</th>
        <th>KM BARU</th>
        <th>SELISIH</th>
        <th>HARGA/LITER</th>
        <th>JUMLAH</th>
    </tr>
    @forelse($perjalanans as $perjalanan)
        <tr>
            <td style="text-align:center">{{ $loop->iteration }}</td>
            <td style="text-align:center">{{ $perjalanan->tanggal->format('d/m/Y') }}</td>
            <td>{{ $perjalanan->uraian ?: $perjalanan->tujuan }}</td>
            <td>{{ $perjalanan->tujuan }}</td>
            <td style="text-align:center">{{ $perjalanan->kendaraan->merk ?? $perjalanan->kendaraan->jenis ?? '-' }}</td>
            <td style="text-align:center">{{ $perjalanan->kendaraan->plat_nomor ?? '-' }}</td>
            <td style="text-align:center">{{ number_format($perjalanan->vol_liter, 2, ',', '.') }}</td>
            <td style="text-align:center">{{ number_format($perjalanan->km_lama, 0, ',', '.') }}</td>
            <td style="text-align:center">{{ number_format($perjalanan->km_baru, 0, ',', '.') }}</td>
            <td style="text-align:center">{{ number_format($perjalanan->jarak, 0, ',', '.') }}</td>
            <td style="text-align:right">Rp {{ number_format($perjalanan->harga_per_liter, 0, ',', '.') }}</td>
            <td style="text-align:right">Rp {{ number_format($perjalanan->jumlah_biaya, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="12" style="text-align:center">Tidak ada data perjalanan pada periode ini.</td>
        </tr>
    @endforelse
    <tr>
        <td colspan="11" style="text-align:right;font-weight:bold">TOTAL:</td>
        <td style="text-align:right;font-weight:bold"></td>
    </tr>
    <tr><td colspan="12"></td></tr>
    <tr><td colspan="12"></td></tr>
    <tr>
        <td colspan="6" style="text-align:center">Mgr. Branch Binjai</td>
        <td colspan="6" style="text-align:center">Officer 3 Business Support Branch Binjai</td>
    </tr>
    <tr><td colspan="12"></td></tr>
    <tr><td colspan="12"></td></tr>
    <tr>
        <td colspan="6" style="text-align:center"><strong>{{ config('perjalanan_report.manager_name') }}</strong></td>
        <td colspan="6" style="text-align:center"><strong>{{ config('perjalanan_report.officer_name') }}</strong></td>
    </tr>
</table>
