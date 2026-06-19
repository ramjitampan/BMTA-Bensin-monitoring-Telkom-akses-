<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kendaraan</title>
</head>
<body>
    <h2>Tambah Kendaraan</h2>

    <form action="{{ route('kendaraan.store') }}" method="POST">
        @csrf
        <label for="plat_nomor">Plat Nomor:</label><br>
        <input type="text" id="plat_nomor" name="plat_nomor" required><br><br>

        <label for="merk">Merk:</label><br>
        <input type="text" id="merk" name="merk"><br><br>

        <label for="jenis">Jenis:</label><br>
        <input type="text" id="jenis" name="jenis"><br><br>

        <label for="tahun">Tahun:</label><br>
        <input type="number" id="tahun" name="tahun"><br><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>