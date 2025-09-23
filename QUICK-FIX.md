# 🚨 QUICK FIX: Login/Register Tidak Berfungsi di cPanel

## ⚡ Solusi Cepat (5 Menit)

### 1. Periksa .env File
```bash
# Pastikan konfigurasi ini benar:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=base64:xxxxx

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Session
SESSION_DRIVER=database
SESSION_DOMAIN=.yourdomain.com
SESSION_SECURE_COOKIE=true
```

### 2. Jalankan Commands
```bash
php artisan key:generate
php artisan config:clear
php artisan cache:clear
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
```

### 3. Set Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 4. Test Database
```bash
php artisan tinker
# Run: DB::connection()->getPdo();
# Should return PDO object
```

## 🔍 Debugging Steps

### Check Error Logs
```bash
tail -50 storage/logs/laravel.log
```

### Test Session
```bash
php artisan tinker
session(['test' => 'working']);
echo session('test');
```

### Verify Database Tables
```bash
php artisan tinker
echo \App\Models\User::count();
```

## 🎯 Common Issues & Fixes

| Issue | Fix |
|-------|-----|
| 500 Error | Check permissions: `chmod -R 755 storage bootstrap/cache` |
| CSRF Token | Clear config: `php artisan config:clear` |
| Session Issues | Set SESSION_DOMAIN=.yourdomain.com |
| Database Error | Verify credentials in .env |
| Login Fails | Run `php artisan migrate --force` |

## 📞 Emergency Checklist

- [ ] .env configured correctly
- [ ] Database credentials valid
- [ ] APP_KEY generated
- [ ] Migrations run
- [ ] Permissions set (755)
- [ ] Config cached
- [ ] HTTPS working
- [ ] Session domain correct

**If still failing, enable debug:**
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Then check `storage/logs/laravel.log` for detailed errors.
