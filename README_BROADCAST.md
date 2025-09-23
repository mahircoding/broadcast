# WhatsApp Broadcast System

Sistem broadcast WhatsApp menggunakan API dari WaboxApp.com dengan fitur manajemen kontak dan upload CSV.

## Fitur Utama

- ✅ **Manajemen Kontak**
  - Tambah kontak manual
  - Upload kontak dari file CSV
  - Edit dan hapus kontak
  - Kelompokkan kontak (grup)
  - Export kontak ke CSV

- ✅ **Broadcast WhatsApp**
  - Kirim pesan ke kontak individual
  - Kirim pesan ke grup kontak
  - Kirim pesan ke semua kontak
  - Riwayat broadcast lengkap
  - Status delivery tracking

- ✅ **Dashboard & Laporan**
  - Statistik kontak dan broadcast
  - Riwayat broadcast dengan detail
  - Test koneksi API
  - Status pengiriman real-time

## Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- Laravel 11.x
- SQLite/MySQL/PostgreSQL
- Akun WaboxApp.com yang aktif

## Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd broadcast
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=sqlite
# Atau gunakan MySQL/PostgreSQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=wa_broadcast
# DB_USERNAME=root
# DB_PASSWORD=
```

### 5. Konfigurasi WaboxApp

Daftar di [WaboxApp.com](https://waboxapp.com) dan dapatkan:
- API Token
- UID (User ID)

Tambahkan konfigurasi di file `.env`:

```env
WABOX_API_URL=https://www.waboxapp.com/api
WABOX_TOKEN=your_token_here
WABOX_UID=your_uid_here
```

### 6. Migrasi Database

```bash
php artisan migrate
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

## Panduan Penggunaan

### Manajemen Kontak

#### Tambah Kontak Manual
1. Login ke sistem
2. Klik menu "Kontak" → "Tambah Kontak"
3. Isi form dengan data kontak
4. Klik "Simpan Kontak"

#### Upload Kontak CSV
1. Persiapkan file CSV dengan format:
   ```
   Nama,Nomor HP,Email,Grup,Catatan
   John Doe,08123456789,john@email.com,Keluarga,Teman dekat
   Jane Smith,+6281234567890,jane@email.com,Kerja,Rekan bisnis
   ```

2. Klik menu "Kontak" → "Upload CSV"
3. Pilih file CSV dan upload
4. Review hasil import

### Kirim Broadcast

#### Broadcast ke Kontak Terseleksi
1. Klik menu "Broadcast"
2. Tulis pesan di text area
3. Pilih "Pilih Individu"
4. Centang kontak yang ingin menerima pesan
5. Klik "Kirim Broadcast"

#### Broadcast ke Grup
1. Klik menu "Broadcast"
2. Tulis pesan di text area
3. Pilih "Pilih Grup"
4. Pilih grup dari dropdown
5. Klik "Kirim Broadcast"

#### Broadcast ke Semua Kontak
1. Klik menu "Broadcast"
2. Tulis pesan di text area
3. Pilih "Semua Kontak"
4. Klik "Kirim Broadcast"

### Melihat Riwayat

1. Klik menu "Riwayat"
2. Lihat daftar broadcast yang pernah dikirim
3. Klik "Detail" untuk melihat laporan lengkap

## Format File CSV

File CSV untuk import kontak harus mengikuti format berikut:

| Kolom | Wajib | Keterangan |
|-------|-------|------------|
| Nama | Ya | Nama lengkap kontak |
| Nomor HP | Ya | Format: 08xxx atau +62xxx |
| Email | Tidak | Email valid (opsional) |
| Grup | Tidak | Nama grup/kategori |
| Catatan | Tidak | Catatan tambahan |

### Contoh File CSV

```csv
Nama,Nomor HP,Email,Grup,Catatan
Ahmad Wijaya,08123456789,ahmad@email.com,Keluarga,Kakak
Siti Nurhaliza,+6281234567890,siti@email.com,Kerja,Manager
Budi Santoso,08987654321,,Teman,Teman SMA
Maria Gonzales,+6287654321098,maria@email.com,Klien,Klien VIP
```

## API WaboxApp

Sistem ini terintegrasi dengan API WaboxApp.com. Fitur yang didukung:

- ✅ Send Message (Kirim Pesan)
- ✅ Check Status (Cek Status)
- ✅ Get Account Info (Info Akun)

### Rate Limiting

Untuk menghindari rate limiting dari WaboxApp:
- Delay 1 detik antar pengiriman pesan
- Maksimal 1000 pesan per hari (sesuai paket)
- Timeout 30 detik per request

## Troubleshooting

### Pesan Gagal Terkirim

1. **Cek koneksi API**: Gunakan tombol "Test Koneksi" di dashboard
2. **Validasi nomor HP**: Pastikan format nomor benar (+62xxx)
3. **Cek quota**: Pastikan quota WaboxApp masih tersedia
4. **Cek status kontak**: Pastikan kontak aktif di WhatsApp

### CSV Import Gagal

1. **Format file**: Pastikan file berformat CSV dengan encoding UTF-8
2. **Struktur kolom**: Sesuaikan urutan kolom dengan template
3. **Data duplikat**: Sistem akan skip nomor HP yang sudah ada
4. **Email invalid**: Baris dengan email invalid akan di-skip

### Error Database

```bash
# Reset migrasi jika diperlukan
php artisan migrate:fresh

# Backup database SQLite
cp database/database.sqlite database/backup.sqlite
```

## Security

- ✅ Authentication required untuk semua fitur
- ✅ CSRF protection pada semua form
- ✅ Input validation dan sanitization
- ✅ Rate limiting pada API calls
- ✅ Secure file upload handling

## Kontribusi

1. Fork repository
2. Buat feature branch
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

Sistem ini dibuat untuk tujuan edukasi dan komersial dengan lisensi MIT.

## Support

Untuk pertanyaan dan support, silakan buat issue di repository ini atau hubungi developer.

---

**Powered by Laravel & WaboxApp API**
