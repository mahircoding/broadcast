# 📘 Panduan Deployment Laravel ke cPanel

## 🚨 Masalah Umum Login/Register di cPanel

Jika aplikasi tidak bisa login atau register setelah upload ke cPanel, kemungkinan penyebabnya:

### 1. **Konfigurasi Environment (.env)**
- APP_ENV harus `production`
- APP_DEBUG harus `false`
- APP_URL harus sesuai domain (https://yourdomain.com)
- APP_KEY harus di-generate

### 2. **Database Configuration**
- DB_HOST biasanya `localhost`
- DB_DATABASE, DB_USERNAME, DB_PASSWORD sesuai cPanel
- Pastikan migrasi sudah dijalankan

### 3. **Session & Cookie Issues**
- SESSION_DOMAIN harus sesuai domain
- SESSION_SECURE_COOKIE harus `true` untuk HTTPS
- SESSION_DRIVER gunakan `database` untuk reliability

---

## 🛠️ Langkah-langkah Deployment

### **Step 1: Persiapan File**

1. **Upload semua file Laravel ke folder `public_html`**
   ```
   public_html/
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── database/
   ├── resources/
   ├── routes/
   ├── storage/
   ├── vendor/
   ├── artisan
   ├── composer.json
   └── ...
   ```

2. **Copy file .env.cpanel ke .env**
   ```bash
   cp .env.cpanel .env
   ```

### **Step 2: Konfigurasi .env**

Edit file `.env` dengan informasi cPanel Anda:

```env
APP_NAME="WhatsApp Broadcast"
APP_ENV=production
APP_KEY=base64:GENERATE_THIS_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database cPanel
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpanel_database_name
DB_USERNAME=cpanel_database_user
DB_PASSWORD=cpanel_database_password

# Session untuk HTTPS
SESSION_DRIVER=database
SESSION_DOMAIN=.yourdomain.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
```

### **Step 3: Setup Database**

1. **Buat database di cPanel MySQL Databases**
2. **Buat user dan assign ke database**
3. **Update kredensial di .env**

### **Step 4: Jalankan Deployment Script**

```bash
chmod +x deploy-cpanel.sh
./deploy-cpanel.sh
```

Atau jalankan manual:

```bash
# Generate key
php artisan key:generate

# Clear cache
php artisan config:clear
php artisan cache:clear

# Migrate database
php artisan migrate --force
php artisan db:seed --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Storage link
php artisan storage:link
```

### **Step 5: Set Permissions**

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

---

## 🔧 Troubleshooting Login/Register Issues

### **Issue 1: 500 Internal Server Error**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### **Issue 2: Database Connection Failed**
- Verifikasi kredensial database di .env
- Pastikan database sudah dibuat di cPanel
- Test koneksi: `php artisan tinker` → `DB::connection()->getPdo();`

### **Issue 3: Session/Cookie Issues**
- Pastikan SESSION_DOMAIN benar (gunakan `.yourdomain.com`)
- Set SESSION_SECURE_COOKIE=true untuk HTTPS
- Clear browser cookies dan coba lagi

### **Issue 4: CSRF Token Mismatch**
- Pastikan APP_URL sesuai dengan domain
- Clear cache: `php artisan config:clear`
- Pastikan session berfungsi dengan baik

### **Issue 5: Routing Issues**
- Pastikan mod_rewrite aktif di cPanel
- Check .htaccess file di public folder
- Pastikan document root mengarah ke folder public

---

## 📋 Checklist Post-Deployment

- [ ] ✅ .env file dikonfigurasi dengan benar
- [ ] ✅ Database connection berfungsi
- [ ] ✅ Migrasi berhasil dijalankan
- [ ] ✅ Storage permissions sudah benar (755)
- [ ] ✅ APP_KEY sudah di-generate
- [ ] ✅ Config sudah di-cache
- [ ] ✅ Session configuration untuk HTTPS
- [ ] ✅ Test login dengan user admin
- [ ] ✅ Test registration user baru
- [ ] ✅ Check error logs jika ada masalah

---

## 🆘 Bantuan Debug

### **Aktifkan Debug Mode Sementara**
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### **Check Error Logs**
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# cPanel error logs (di cPanel → Error Logs)
```

### **Test Database Connection**
```bash
php artisan tinker
DB::connection()->getPdo();
```

### **Test Session**
```bash
php artisan tinker
session(['test' => 'value']);
session('test');
```

---

## 🎯 Tips Optimasi Production

1. **Gunakan database session** (lebih reliable di shared hosting)
2. **Set proper cache headers**
3. **Compress static assets**
4. **Monitor error logs regularly**
5. **Backup database secara berkala**

---

**💡 Jika masih mengalami masalah, periksa:**
1. Error logs di storage/logs/laravel.log
2. Error logs di cPanel
3. Browser developer tools untuk error JavaScript
4. Network tab untuk failed requests
