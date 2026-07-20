# Handover Brief — Sistem Monitoring BBM
**Versi:** v1.0.0-rc1
**Tanggal:** 20 Juli 2026
**Developer:** Ramzy Junfaris Hamonangan (Mahasiswa Magang)
**Supervisor:** Adelina Jojorita SIB
**Perusahaan:** PT Telkom Akses

---

## 1. Tentang aplikasi

Sistem Monitoring BBM adalah aplikasi web internal berbasis Laravel untuk
memantau konsumsi bahan bakar kendaraan dinas. Fitur utama meliputi:

- Manajemen data pegawai, kendaraan, dan perjalanan
- Monitoring konsumsi BBM dengan efisiensi per kendaraan
- Fraud detection otomatis (5 indikator anomali)
- Export laporan Excel berformat branded Telkom
- REST API untuk integrasi sistem lain
- Dashboard statistik dengan cache

---

## 2. Repositori

| | |
|---|---|
| **URL** | https://github.com/ramjitampan/BMTA-Bensin-monitoring-Telkom-akses- |
| **Branch utama** | `main` |
| **Tag release** | `v1.0.0-rc1` |
| **Akses** | Public |

Untuk mengambil versi yang sudah diaudit:

```bash
git clone https://github.com/ramjitampan/BMTA-Bensin-monitoring-Telkom-akses-
cd BMTA-Bensin-monitoring-Telkom-akses-
git checkout v1.0.0-rc1
```

---

## 3. Tech stack

| Komponen | Versi |
|---|---|
| PHP | 8.3 |
| Laravel | 13 |
| Database | MySQL 8+ |
| CSS Build | Tailwind v4 via Vite |
| Excel Export | Maatwebsite Excel |
| Web Server | Nginx (direkomendasikan) |

---

## 4. Perintah wajib setelah deploy

Jalankan perintah berikut secara berurutan setelah aplikasi ditempatkan di server:

```bash
# 1. Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 2. Konfigurasi environment
cp .env.example .env
php artisan key:generate
# Edit .env: isi DB_*, APP_URL, dan variabel lainnya

# 3. Database
php artisan migrate --force
php artisan db:seed          # opsional — untuk data demo awal

# 4. Storage
php artisan storage:link

# 5. Optimasi production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Queue worker (jalankan via Supervisor)
php artisan queue:work --daemon
```

Detail lengkap ada di `DEPLOYMENT.md` di dalam repositori.

---

## 5. Variabel environment yang perlu diisi tim IT

Buka `.env` dan isi nilai berikut sesuai environment perusahaan:

```env
APP_URL=https://domain-internal-perusahaan.com

DB_HOST=127.0.0.1
DB_DATABASE=nama_database
DB_USERNAME=username_db
DB_PASSWORD=password_db

MANAGER_NAME="Nama Manager Penandatangan"
OFFICER_NAME="Nama Pejabat Penandatangan"
TIF_PREFIX="TIF"

MAIL_*           # jika notifikasi email diaktifkan
QUEUE_CONNECTION=database
```

---

## 6. Catatan keamanan untuk tim Cybersecurity

### API tanpa autentikasi middleware
Seluruh endpoint REST API (`/api/perjalanan`, `/api/pegawai`, `/api/kendaraan`)
saat ini tidak memiliki middleware autentikasi di level aplikasi. Ini adalah
keputusan yang disengaja agar tim IT dapat menentukan kebijakan akses sesuai
infrastruktur perusahaan.

**Rekomendasi:** Restrict akses ke endpoint `/api/*` di level Nginx hanya untuk
IP internal atau jaringan VPN perusahaan:

```nginx
location /api/ {
    allow 10.0.0.0/8;      # sesuaikan dengan range IP internal
    allow 172.16.0.0/12;
    deny all;
    try_files $uri $uri/ /index.php?$query_string;
}
```

Jika autentikasi token per-request diperlukan di kemudian hari, Laravel Sanctum
sudah tersedia sebagai dependency dan dapat diaktifkan tanpa mengubah business
logic yang ada.

### Proteksi yang sudah aktif di level aplikasi
- CSRF protection — aktif di semua form
- XSS protection — via Blade auto-escaping
- Input validation — via Laravel Form Request di semua endpoint
- Soft delete — data tidak terhapus permanen, aman untuk audit trail
- Session-based auth — untuk akses web (login/logout sudah berjalan)

---

## 7. Kontak developer

Untuk pertanyaan teknis terkait source code, business logic, atau fraud
detection algorithm:

**Ramzy Junfaris Hamonangan**
Mahasiswa Magang — PT Telkom Akses
Supervisor: Adelina Jojorita SIB

---

*Dokumen ini dibuat sebagai bagian dari proses handover akhir magang.
Source code telah melalui spring cleaning audit dan production test
sebelum diserahkan. Tag `v1.0.0-rc1` adalah snapshot final yang
direkomendasikan untuk deployment.*
