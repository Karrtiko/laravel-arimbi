# Panduan Deployment Arimbi Store (Production) 🚀

Dokumen ini berisi langkah-langkah untuk menaikkan aplikasi ke Production dan cara melakukan update (revisi) tanpa merusak data yang sudah ada.

---

## 🛑 Prinsip Penting (Jangan Dilanggar)
1.  **DATABASE JANGAN DI-GIT**: File database (`database.sqlite`) sudah di-ignore. Ini penting agar **data transaksi/produk di Production TIDAK TERTIMPA** oleh database local kamu saat update.
2.  **JANGAN `migrate:fresh` DI PRODUCTION**: Perintah `php artisan migrate:fresh` akan **MENGHAPUS SEMUA DATA** dan membuat ulang database dari nol. Jangan pernah jalankan ini di server production kecuali kamu memang ingin reset total.
3.  **Media Uploads**: Gambar produk yang di-upload user tersimpan di folders `storage/app/public/*`. Folder ini juga di-ignore agar tidak konflik. Pastikan backup folder ini secara berkala manual jika perlu.

---

## 1️⃣ Persiapan Awal (First Time Setup)

Lakukan langkah ini **HANYA SEKALI** saat pertama kali menaruh aplikasi di server/hosting.

1.  **Clone Repository**
    ```bash
    git clone https://github.com/username/arimbi-store.git
    cd arimbi-store
    ```

2.  **Setup Environment**
    Copy file `.env.example` ke `.env`:
    ```bash
    cp .env.example .env
    ```
    Edit `.env` sesuaikan dengan config server (kalau SQLite, pastikan `DB_CONNECTION=sqlite`).

3.  **Install Dependencies**
    ```bash
    composer install --optimize-autoloader --no-dev
    ```

4.  **Generate Key & Storage Link**
    ```bash
    php artisan key:generate
    php artisan storage:link
    ```

5.  **Setup Database Awal**
    Karena ini pertama kali, kita butuh file database kosong dan struktur tabelnya.
    ```bash
    # Buat file database kosong (jika belum ada)
    # Di Windows: type nul > database/database.sqlite
    # Di Linux: touch database/database.sqlite

    # Jalankan migrasi dan seeder awal (HANYA PERTAMA KALI)
    php artisan migrate --seed
    ```
    *Note: Option `--seed` akan memasukkan data awal seperti user Admin default dan Settings default.*

6.  **Build Frontend Assets**
    ```bash
    npm install
    npm run build
    ```

7.  **Optimasi**
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

---

## 🔄 Cara Update (Revisi Fitur)

Lakukan langkah ini setiap kali kamu **selesai revisi fitur** di local dan ingin **mengupdate aplikasi di server** (Production).

### Langkah-langkah:

1.  **Tarik Kode Terbaru**
    ```bash
    git pull origin main
    ```

2.  **Update Dependencies (Jika ada penambahan library)**
    ```bash
    composer install --no-dev
    ```

3.  **Update Database (MIGRATE SAJA)**
    ⚠️ **PENTING**: Gunakan perintah `migrate` saja. Ini akan menambahkan tabel/kolom baru tanpa menghapus data lama.
    ```bash
    php artisan migrate --force
    ```
    *(Flag `--force` dibutuhkan di production mode)*.

4.  **Update Frontend (Jika ada perubahan tampilan/CSS/JS)**
    ```bash
    npm run build
    ```

5.  **Clear Cache (Wajib setelah update)**
    ```bash
    php artisan optimize:clear
    # Atau satu per satu:
    # php artisan config:clear
    # php artisan cache:clear
    # php artisan view:clear
    ```

### Ringkasan Update (Copy-Paste)
```bash
git pull
composer install --no-dev
php artisan migrate --force
npm run build
php artisan optimize:clear
```

---

## ❓ FAQ & Troubleshooting

### Q: Saya nambah kolom baru di tabel Produk di local, gimana cara biar naik ke Prod tanpa hilang data?
**A:** Pastikan kamu membuat **Migration baru** di local (`php artisan make:migration add_new_column_to_products`). Jangan edit file migration lama.
Saat di Production kamu jalankan `php artisan migrate`, Laravel akan mendeteksi migration baru tersebut dan menjalankannya (menambah kolom) tanpa menyentuh data yang sudah ada di kolom lain.

### Q: Gambar produk di Production kok hilang/broken link?
**A:**
1. Cek apakah folder `storage` sudah di-link ke `public`? Coba jalankan `php artisan storage:unlink` lalu `php artisan storage:link`.
2. Pastikan permission folder `storage` dan `bootstrap/cache` writable (775/777).

### Q: Saya mau ubah text di Home, harus coding?
**A:** Tidak perlu. Sekarang sudah ada menu **Admin > General Settings**. Ganti text/gambar dari sana, langsung berubah di web utama.

---


---

## 🐳 Opsi 2: Deployment dengan Docker (Lebih Stabil)

Jika kamu menggunakan VPS (DigitalOcean/EC2/dll), cara ini lebih disarankan karena environment (PHP, Nginx) sudah terisolasi dan konsisten.

### 1. Persiapan Awal
1.  **Clone Repo & Setup Env** (Sama seperti langkah manual 1-2).
2.  **Pastikan Database Ada**
    Docker butuh file database fisik untuk di-mount.
    ```bash
    # Linux/Mac
    touch database/database.sqlite
    
    # Windows
    type nul > database/database.sqlite
    ```

### 2. Jalankan Container
Saya sudah buatkan `docker-compose.yml` siap pakai.
```bash
docker-compose up -d --build
```
Aplikasi akan aktif di Port 80.

### 3. Setup Data Awal (Sekali Saja)
Masuk ke container untuk migrasi data:
```bash
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```

### 4. Cara Update (Revisi Fitur)
Setiap ada perubahan code dari git:
```bash
# 1. Tarik Code
git pull origin main

# 2. Restart Container (Code akan ter-update karena bind mount)
# Migrasi database akan jalan otomatis lewat entrypoint script.
docker-compose restart app

# Opsional: Jika mengubah config Dockerfile
# docker-compose up -d --build
```

---

## ❓ FAQ & Troubleshooting

### Q: Mana yang lebih aman, Manual atau Docker?
**A:** Keduanya aman. **Manual** cocok untuk Shared Hosting (cPanel) dimana kita gak bisa install Docker. **Docker** cocok untuk VPS karena lebih rapi dan gampang dipindah-pindah.

### Q: Database saya aman di Docker?
**A:** **Sangat Aman**. Di `docker-compose.yml` saya sudah set mapping:
`- ./database/database.sqlite:/var/www/database/database.sqlite`.
Artinya file database asli ada di HIT (server host) kamu, bukan di dalam container. Jika container dihapus, data **TETAP ADA**.

---

