<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pegawai</title>
</head>
<body>
    <h2>Tambah Pegawai</h2>

    <form action="{{ route('pegawai.store') }}" method="POST">
        @csrf
        <label for="nama">Nama:</label><br>
        <input type="text" id="nama" name="nama" required><br><br>

        <label for="jabatan">Jabatan:</label><br>
        <input type="text" id="jabatan" name="jabatan"><br><br>

        <label for="divisi">Divisi:</label><br>
        <input type="text" id="divisi" name="divisi"><br><br>

        <label for="no_hp">No HP:</label><br>
        <input type="text" id="no_hp" name="no_hp"><br><br>

        <button type="submit">Simpan</button>
</form>
</body>
</html>