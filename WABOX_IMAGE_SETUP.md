# Setup Gambar untuk WaboxApp Broadcast

## Masalah
Error: `Invalid image 'url' parameter: URL not found`

## Penyebab
WaboxApp API tidak dapat mengakses gambar yang disimpan di server localhost (127.0.0.1:8000) karena URL tersebut tidak dapat diakses dari internet.

## Solusi

### 1. Untuk Development (Testing)

#### Opsi A: Menggunakan ngrok
```bash
# Install ngrok terlebih dahulu
brew install ngrok

# Jalankan Laravel server
php artisan serve

# Di terminal lain, buat tunnel publik
ngrok http 8000

# Update APP_URL di .env dengan URL ngrok
APP_URL=https://abc123.ngrok.io
```

#### Opsi B: Menggunakan localtunnel
```bash
# Install localtunnel
npm install -g localtunnel

# Jalankan Laravel server
php artisan serve

# Di terminal lain, buat tunnel publik
lt --port 8000

# Update APP_URL di .env dengan URL yang diberikan
APP_URL=https://abc123.loca.lt
```

### 2. Untuk Production

#### Deploy ke Server Publik
- Deploy aplikasi ke server yang dapat diakses dari internet
- Set APP_URL di .env ke domain/IP publik Anda
```bash
APP_URL=https://yourdomain.com
```

### 3. Alternatif: Upload ke Cloud Storage

Jika tidak ingin menggunakan tunnel, Anda bisa mengubah kode untuk upload gambar ke cloud storage seperti:
- AWS S3
- Google Cloud Storage  
- Cloudinary
- ImgBB

## Cara Test

1. Set APP_URL ke URL publik
2. Restart server Laravel
3. Upload gambar dan kirim broadcast
4. Periksa apakah gambar dapat diakses di: `{APP_URL}/storage/broadcast-images/{filename}`

## Catatan Keamanan

- Jangan commit URL ngrok/localtunnel ke repository
- Gunakan environment variables untuk URL production
- Pastikan direktori storage/app/public/broadcast-images memiliki permission yang benar