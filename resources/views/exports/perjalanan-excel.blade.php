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
        <th>KENDARAAN</th>
        <th>NO POL</th>
        <th>VOL/LTR</th>
        <th>SPEED METER</th>
        <th>KM LAMA</th>
        <th>KM BARU</th>
        <th>SELISIH</th>
        <th>HARGA/LITER</th>
        <th>JUMLAH</th>
    </tr>
    @forelse($perjalanans as $perjalanan)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $perjalanan->tanggal->format('d/m/Y') }}</td>
            <td>{{ $perjalanan->uraian ?: $perjalanan->tujuan }}</td>
            <td>{{ $perjalanan->kendaraan->merk ?? $perjalanan->kendaraan->jenis ?? '-' }}</td>
            <td>{{ $perjalanan->kendaraan->plat_nomor ?? '-' }}</td>
            <td>{{ $perjalanan->vol_liter }}</td>
            <td>{{ $perjalanan->km_baru }}</td>
            <td>{{ $perjalanan->km_lama }}</td>
            <td>{{ $perjalanan->km_baru }}</td>
            <td>{{ $perjalanan->jarak }}</td>
            <td>{{ $perjalanan->harga_per_liter }}</td>
            <td>{{ $perjalanan->jumlah_biaya }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="12">Tidak ada data perjalanan pada periode ini.</td>
        </tr>
    @endforelse
    <tr>
        <td colspan="11">TOTAL:</td>
        <td></td>
    </tr>
    <tr><td colspan="12"></td></tr>
    <tr><td colspan="12"></td></tr>
    <tr>
        <td colspan="6">Mgr. Branch Binjai</td>
        <td colspan="6">Officer 3 Business Support Branch Binjai</td>
    </tr>
    <tr><td colspan="12"></td></tr>
    <tr><td colspan="12"></td></tr>
    <tr>
        <td colspan="6"><strong>{{ config('perjalanan_report.manager_name') }}</strong></td>
        <td colspan="6"><strong>{{ config('perjalanan_report.officer_name') }}</strong></td>
    </tr>
</table>
