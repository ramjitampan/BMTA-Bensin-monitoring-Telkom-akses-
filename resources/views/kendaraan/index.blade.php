<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kendaraan</title>
</head>
<body>
    <h2>Data Kendaraan</h2>
    <a href="{{ route('kendaraan.create') }}">Tambah Kendaraan</a>
    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>Plat Nomor</th>
            <th>Merk</th>
            <th>Jenis</th>
            <th>Tahun</th>
            <th>Action</th>
        </tr>

        @foreach ($kendaraans as $kendaraan)
        <tr>
            <td>{{ $kendaraan->plat_nomor }}</td>
            <td>{{ $kendaraan->merk }}</td>
            <td>{{ $kendaraan->jenis }}</td>
            <td>{{ $kendaraan->tahun }}</td>
            <td>
                <a href="{{ route('kendaraan.edit', $kendaraan->id) }}">Edit</a>
                |
                <form action="{{ route('kendaraan.destroy', $kendaraan->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

</body>
</html>