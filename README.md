# Courier Master Data API (Gradin Coding Test)

Backend RESTful API untuk manajemen master data Kurir/Courier. Dibangun menggunakan **Laravel 13** dengan **PHP 8.3+**. Proyek ini murni API (tanpa frontend UI) dan merespons seluruh permintaan dalam format JSON.

---

## 1. Spesifikasi & Persyaratan Sistem

- **PHP**: ^8.3
- **Framework**: Laravel 13.x
- **Database**: MySQL

---

## 2. Cara Menjalankan Project

1. **Aktifkan Servis MySQL**:
   Pastikan MySQL pada Laragon atau XAMPP sudah berjalan.

2. **Buat Database**:
   Buat database baru di MySQL (misalnya melalui HeidiSQL, phpMyAdmin, atau MySQL CLI):
   ```sql
   CREATE DATABASE courier_gradin;
   ```

3. **Install Dependencies & Pengaturan Environment**:
   Jalankan perintah berikut di terminal proyek:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database di `.env`**:
   Buka file `.env` dan pastikan pengaturan koneksi database MySQL sudah sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=courier_gradin
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan Migrasi & Seeder Database**:
   Jalankan migrasi tabel sekaligus memasukkan 5 data master kurir (termasuk **Budiono Hadi Agung**):
   ```bash
   php artisan migrate --seed
   ```
   *(Atau jika sudah pernah migrasi sebelumnya, jalankan: `php artisan db:seed`)*.

6. **Jalankan Development Server**:
   ```bash
   php artisan serve
   ```
   Server aktif dan siap diakses pada: `http://127.0.0.1:8000`

---

## 3. Cara Menjalankan Automated Tests

Test suite berada di `tests/Feature/CourierApiTest.php`. Pengujian dijalankan secara terisolasi via in-memory database (`:memory:`) dengan trait `RefreshDatabase`, sehingga data MySQL Anda dijamin aman dan tidak akan terpengaruh saat pengujian dijalankan.

Jalankan pengujian menggunakan Composer atau Artisan:

```bash
# Menggunakan script composer bawaan
composer test

# Atau menggunakan Artisan test runner
php artisan test

# Atau menjalankan spesifik test kurir
php artisan test --filter=CourierApiTest
```

### Cakupan Skenario Test:
- `test_index_paginated_and_sorted_by_name_by_default`: Memastikan paginasi dan urutan default berdasarkan nama kurir (ASC).
- `test_index_sorted_by_name_return_all_five_couriers`: Memastikan seluruh data kurir berhasil diambil saat paginasi diperbesar.
- `test_index_can_sort_by_registered_at`: Memastikan sorting dapat di-override menggunakan tanggal pendaftaran (`registered_at`).
- `test_index_search_matches_every_word_on_budiono`: Memastikan query pencarian `?search=budi+agung` dapat mencocokkan nama **Budiono Hadi Agung**.
- `test_index_filters_by_levels`: Memastikan filter level kurir (`?level=2,3`) berfungsi tepat.
- `test_show_returns_budiono_hadi_agung`: Memastikan detail kurir mengembalikan seluruh data atribut.
- `test_store_validates_and_persists`: Memastikan kurir baru berhasil divalidasi dan tersimpan di database.
- `test_store_rejects_invalid_input`: Memastikan validasi menolak input yang tidak valid (misal: level di luar 1-5 atau nama kosong).
- `test_update_validates_and_persists_within_five`: Memastikan data kurir berhasil diperbarui.
- `test_update_rejects_duplicate_phone`: Memastikan validasi nomor telepon unik tetap berjalan saat update.
- `test_destroy_removes_record`: Memastikan data kurir berhasil dihapus dari database.

---

## 4. Cara Akses & Uji API di Browser (Address Bar)

Karena endpoint API mengembalikan JSON secara otomatis, Anda bisa langsung mengetikkan URL berikut di address bar browser Anda (Chrome, Edge, Firefox, dll).

---

### A. Lihat Semua Kurir (Index & Pagination)
- **Ketik di URL Browser**:
  ```text
  http://127.0.0.1:8000/api/couriers
  ```
  *Secara default menampilkan data yang dipaginasi dan diurutkan berdasarkan nama kurir secara alfabetis (ASC).*

- **Contoh Response JSON (200 OK)**:
  ```json
  {
      "current_page": 1,
      "data": [
          {
              "id": 3,
              "name": "Agus Santoso",
              "phone": "082145987302",
              "email": "agus.santoso77@yahoo.co.id",
              "level": 3,
              "vehicle_type": "mobil",
              "vehicle_plate_number": "B 9821 ANB",
              "address": "Jl. Pahlawan No. 45, Surabaya",
              "status": "active",
              "registered_at": "2024-11-19"
          },
          {
              "id": 1,
              "name": "Budiono Hadi Agung",
              "phone": "081298745321",
              "email": "budiono.hadiagung@gmail.com",
              "level": 5,
              "vehicle_type": "motor",
              "vehicle_plate_number": "B 4821 XYZ",
              "address": "Jl. Jend. Sudirman No. 12, Jakarta",
              "status": "active",
              "registered_at": "2024-05-12"
          }
      ],
      "first_page_url": "http://127.0.0.1:8000/api/couriers?page=1",
      "from": 1,
      "last_page": 1,
      "per_page": 15,
      "total": 5
  }
  ```

---

### B. Urutkan Berdasarkan Tanggal Pendaftaran
- **Ketik di URL Browser**:
  ```text
  http://127.0.0.1:8000/api/couriers?sort=registered_at&direction=desc
  ```
  *Mengurutkan kurir dari yang paling baru didaftarkan.*

---

### C. Cari Kurir (Multi-Kata Cocok dengan Budiono Hadi Agung)
- **Ketik di URL Browser**:
  ```text
  http://127.0.0.1:8000/api/couriers?search=budi+agung
  ```
  *Pencarian multi-kata `budi agung` akan mencocokkan kurir bernama **Budiono Hadi Agung**.*

---

### D. Filter Level Tertentu Saja (Level 2 dan 3)
- **Ketik di URL Browser**:
  ```text
  http://127.0.0.1:8000/api/couriers?level=2,3
  ```
  *Hanya menampilkan kurir dengan level 2 atau level 3.*

---

### E. Lihat Detail Kurir Berdasarkan ID (Show)
- **Ketik di URL Browser**:
  ```text
  http://127.0.0.1:8000/api/couriers/1
  ```
  *Mengembalikan seluruh atribut data dari kurir ID 1 (Budiono Hadi Agung).*

- **Contoh Response JSON (200 OK)**:
  ```json
  {
      "id": 1,
      "name": "Budiono Hadi Agung",
      "phone": "081298745321",
      "email": "budiono.hadiagung@gmail.com",
      "level": 5,
      "vehicle_type": "motor",
      "vehicle_plate_number": "B 4821 XYZ",
      "address": "Jl. Jend. Sudirman No. 12, Jakarta",
      "status": "active",
      "registered_at": "2024-05-12",
      "created_at": "2026-09-14T04:00:00.000000Z",
      "updated_at": "2026-09-14T04:00:00.000000Z"
  }
  ```

---

## 5. Pengujian Operasi Tambah, Ubah, & Hapus (API Client)

Untuk operasi HTTP `POST`, `PUT`, dan `DELETE`, Anda dapat menggunakan tools seperti **Postman**, **Thunder Client** (ekstensi VS Code), atau **Insomnia**:

### A. Tambah Kurir Baru (Store)
- **Method**: `POST`
- **URL**: `http://127.0.0.1:8000/api/couriers`
- **Headers**:
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body (JSON)**:
  ```json
  {
      "name": "Budi Santoso",
      "phone": "081211223344",
      "email": "budi.santoso@example.com",
      "level": 3,
      "vehicle_type": "motor",
      "vehicle_plate_number": "B 1234 ABC",
      "address": "Jl. Thamrin No. 10, Jakarta",
      "status": "active",
      "registered_at": "2026-09-14"
  }
  ```
- **Response**: `201 Created`

---

### B. Update Kurir (Update)
- **Method**: `PUT` atau `PATCH`
- **URL**: `http://127.0.0.1:8000/api/couriers/1`
- **Headers**:
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body (JSON)** *(mendukung partial update)*:
  ```json
  {
      "level": 4,
      "vehicle_plate_number": "B 9999 XYZ"
  }
  ```
- **Response**: `200 OK`

---

### C. Hapus Kurir (Destroy)
- **Method**: `DELETE`
- **URL**: `http://127.0.0.1:8000/api/couriers/1`
- **Headers**:
  - `Accept: application/json`
- **Response**: `204 No Content` (body kosong)

---

## 6. Struktur Skema Database (`couriers`)

Tabel dibuat via migration `database/migrations/2026_09_14_034725_create_couriers_table.php`:

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key (Auto Increment) |
| `name` | VARCHAR(255) | Nama lengkap kurir (**Indexed**) |
| `phone` | VARCHAR(20) | Nomor telepon kurir (**Unique**) |
| `email` | VARCHAR(255) | Alamat email (**Nullable, Unique**) |
| `level` | UNSIGNED TINYINT | Level kurir 1 s/d 5 (**Indexed**) |
| `vehicle_type` | VARCHAR(50) | Jenis kendaraan: motor, mobil, pickup (**Nullable**) |
| `vehicle_plate_number` | VARCHAR(20) | Nomor plat kendaraan (**Nullable**) |
| `address` | TEXT | Alamat tempat tinggal (**Nullable**) |
| `status` | ENUM('active', 'inactive') | Status kurir (Default: `'active'`) |
| `registered_at` | DATE | Tanggal terdaftar (**Indexed**) |
| `created_at` / `updated_at` | TIMESTAMP | Timestamp standar Eloquent |
