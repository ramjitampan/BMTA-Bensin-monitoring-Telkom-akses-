<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai</title>
</head>
<body>
    <h2>Data Pegawai</h2>
    <a href="{{ route('pegawai.create') }}">Tambah Pegawai</a>
    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Divisi</th>
            <th>No HP</th>
            <th>Action</th>
        </tr>

        @foreach ($pegawais as $pegawai)
        <tr>
            <td>{{ $pegawai->id }}</td>
            <td>{{ $pegawai->nama }}</td>
            <td>{{ $pegawai->jabatan }}</td>
            <td>{{ $pegawai->divisi }}</td>
            <td>{{ $pegawai->no_hp }}</td>
            <td>
                <a href="{{ route('pegawai.edit', $pegawai->id) }}">Edit</a>
                |
                <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST" style="display:inline;">
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