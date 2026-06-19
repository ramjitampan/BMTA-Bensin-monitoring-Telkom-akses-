<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pegawai</title>
</head>
<body>
    <h2>Edit Pegawai</h2>
    <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="nama">Nama:</label><br>
        <input type="text" id="nama" name="nama" value="{{ $pegawai->nama }}" required><br><br>

        <label for="jabatan">Jabatan:</label><br>
        <input type="text" id="jabatan" name="jabatan" value="{{ $pegawai->jabatan }}"><br><br>

        <label for="divisi">Divisi:</label><br>
        <input type="text" id="divisi" name="divisi" value="{{ $pegawai->divisi }}"><br><br>

        <label for="no_hp">No HP:</label><br>
        <input type="text" id="no_hp" name="no_hp" value="{{ $pegawai->no_hp }}"><br><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>