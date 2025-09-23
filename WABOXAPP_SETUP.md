# Panduan Konfigurasi WaboxApp API

## Langkah-langkah Setup WaboxApp

### 1. Registrasi Akun WaboxApp

1. Kunjungi [https://waboxapp.com](https://waboxapp.com)
2. Klik "Register" dan buat akun baru
3. Verifikasi email Anda
4. Login ke dashboard WaboxApp

### 2. Setup WhatsApp Device

1. Di dashboard WaboxApp, klik "Add Device"
2. Pilih metode koneksi (WhatsApp Web recommended)
3. Scan QR Code dengan WhatsApp di ponsel Anda
4. Tunggu hingga device status menjadi "Connected"

### 3. Dapatkan API Credentials

1. Buka menu "API" di dashboard WaboxApp
2. Copy nilai **Token** dan **UID**
3. Pastikan API status "Active"

### 4. Konfigurasi di Laravel

Edit file `.env` di project Laravel:

```env
WABOX_API_URL=https://www.waboxapp.com/api
WABOX_TOKEN=your_token_from_waboxapp
WABOX_UID=your_uid_from_waboxapp
```

### 5. Test Koneksi

1. Jalankan Laravel server: `php artisan serve`
2. Login ke aplikasi
3. Buka Dashboard
4. Klik tombol "Test Koneksi WaboxApp"
5. Pastikan status berhasil

## Format Nomor WhatsApp

WaboxApp menggunakan format internasional:

### ✅ Format yang Benar
- `+6281234567890` (dengan kode negara +62)
- `+6287654321098`

### ❌ Format yang Salah
- `081234567890` (tanpa kode negara)
- `62-81234567890` (dengan strip)
- `0821 3456 7890` (dengan spasi)

**Catatan**: Sistem akan otomatis mengkonversi nomor Indonesia dari format `08xxx` menjadi `+62xxx`.

## Batasan API WaboxApp

### Rate Limiting
- Maksimal 1 request per detik
- Delay 1 detik antar pengiriman pesan
- Timeout 30 detik per request

### Quota Paket
| Paket | Pesan/Hari | Pesan/Bulan | Harga |
|-------|------------|-------------|-------|
| Free | 100 | 500 | Gratis |
| Basic | 1,000 | 10,000 | $10/bulan |
| Pro | 5,000 | 50,000 | $25/bulan |
| Enterprise | 20,000 | 200,000 | $75/bulan |

### Device Status
- **Connected**: Device siap mengirim pesan
- **Disconnected**: Perlu scan ulang QR Code
- **Expired**: Perlu perpanjang subscription

## API Endpoints yang Digunakan

### 1. Send Message
```
POST https://www.waboxapp.com/api/send/chat
Parameters:
- token: API Token
- uid: User ID
- to: Nomor WhatsApp tujuan (+62xxx)
- text: Isi pesan
```

### 2. Check Status
```
GET https://www.waboxapp.com/api/status
Parameters:
- token: API Token
- uid: User ID
```

## Troubleshooting

### Error: "Device not connected"
**Solusi**:
1. Buka dashboard WaboxApp
2. Scan ulang QR Code WhatsApp Web
3. Pastikan ponsel terhubung internet
4. Test koneksi di Laravel

### Error: "Invalid token or UID"
**Solusi**:
1. Cek file `.env` untuk token dan UID
2. Copy ulang dari dashboard WaboxApp
3. Restart Laravel server
4. Clear cache: `php artisan config:clear`

### Error: "Quota exceeded"
**Solusi**:
1. Cek usage di dashboard WaboxApp
2. Upgrade paket jika perlu
3. Tunggu reset quota harian/bulanan

### Error: "Invalid phone number format"
**Solusi**:
1. Pastikan nomor menggunakan format +62xxx
2. Cek validasi di ContactController
3. Update format nomor di database

### Pesan tidak terkirim
**Solusi**:
1. Cek status device di WaboxApp
2. Pastikan nomor tujuan aktif di WhatsApp
3. Cek log error di `storage/logs/laravel.log`
4. Test dengan nomor sendiri terlebih dahulu

## Monitoring & Logging

### Log File
Cek file log Laravel untuk error detail:
```bash
tail -f storage/logs/laravel.log
```

### Database Logs
Sistem menyimpan log broadcast di tabel `broadcast_logs`:
```sql
SELECT * FROM broadcast_logs ORDER BY created_at DESC LIMIT 10;
```

### WaboxApp Dashboard
Monitor penggunaan API di dashboard WaboxApp:
- Total pesan terkirim
- Success rate
- Error logs
- Quota remaining

## Best Practices

### 1. Validasi Nomor
```php
// Selalu validasi format nomor sebelum kirim
$phone = preg_replace('/[^0-9+]/', '', $phone);
if (!str_starts_with($phone, '+')) {
    $phone = '+62' . ltrim($phone, '0');
}
```

### 2. Handle Rate Limiting
```php
// Delay antar pengiriman
sleep(1);
```

### 3. Error Handling
```php
try {
    $result = $waboxService->sendMessage($phone, $message);
} catch (\Exception $e) {
    Log::error('Broadcast failed: ' . $e->getMessage());
}
```

### 4. Backup Kontak
```php
// Export kontak secara berkala
php artisan contacts:export --format=csv
```

## Security

### 1. Environment Variables
```env
# Jangan commit credentials ke repository
WABOX_TOKEN=your_secret_token
WABOX_UID=your_secret_uid
```

### 2. API Rate Limiting
Implementasi rate limiting di controller untuk mencegah abuse.

### 3. Input Validation
Selalu validasi input user sebelum kirim ke API WaboxApp.

---

**Update terakhir**: September 2025
**Versi WaboxApp API**: v3
**Kompatibilitas**: Laravel 11.x
