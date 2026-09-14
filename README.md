# Courier Master Data API (Gradin Coding Test)

Backend RESTful API untuk manajemen master data Kurir/Courier. Dibangun menggunakan **Laravel 13** dengan **PHP 8.3+**. Sesuai instruksi teknis, proyek ini murni API (tanpa frontend UI) dan merespons seluruh permintaan dalam format JSON.

---

## 1. Spesifikasi & Persyaratan Sistem

- **PHP**: ^8.3
- **Framework**: Laravel 13.x
- **Database**: MySQL

---

## 2. Cara Menjalankan Project

1. **Aktifkan Servis MySQL**:
   Pastikan servis MySQL pada Laragon atau XAMPP sudah berjalan.

2. **Buat Database**:
   Buat database baru di MySQL (misalnya melalui HeidiSQL, phpMyAdmin, atau MySQL CLI):
   ```sql
   CREATE DATABASE courier_gradin;
   ```

3. **Install Dependencies & Pengaturan Environment**:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database di `.env`**:
   Buka file `.env` dan sesuaikan pengaturan koneksi database MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=courier_gradin
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan Migrasi Database**:
   Jalankan migrasi untuk membuat tabel `couriers`:
   ```bash
   php artisan migrate
   ```

6. **Jalankan Development Server**:
   ```bash
   php artisan serve
   ```
   API siap diakses pada: `http://127.0.0.1:8000/api/couriers`
   _(Atau via virtual host Laragon jika aktif, contoh: `http://courier-gradin-test.test/api/couriers`)_.

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

## 4. Dokumentasi Endpoint API

Base URL: `http://127.0.0.1:8000/api/couriers`

Semua request dan response wajib menyertakan header:

- `Accept: application/json`
- `Content-Type: application/json`

---

### A. List Kurir (Index)

Mengambil daftar kurir dengan fitur paginasi, pencarian multi-kata, filter level, dan pengurutan.

- **Method**: `GET`
- **URL**: `/api/couriers`
- **Query Parameters**:
  | Parameter | Tipe | Default | Keterangan |
  |---|---|---|---|
  | `per_page` | integer | `15` | Jumlah data per halaman (maksimal 100). |
  | `page` | integer | `1` | Nomor halaman paginasi. |
  | `sort` | string | `name` | Kolom pengurutan (`name` atau `registered_at`). |
  | `direction` | string | `asc` | Arah pengurutan (`asc` atau `desc`). |
  | `search` | string | - | Pencarian kurir. Mendukung multi-kata (contoh: `budi+agung` cocok dengan `Budiono Hadi Agung`). |
  | `level` | string | - | Filter level (1-5), pisahkan dengan koma (contoh: `2,3`). |

#### Contoh Request cURL:

1. **Default (Paginasi 15 data & urut nama ASC)**:

    ```bash
    curl -X GET "http://127.0.0.1:8000/api/couriers" \
      -H "Accept: application/json"
    ```

2. **Urutkan berdasarkan tanggal daftar terbaru (DESC)**:

    ```bash
    curl -X GET "http://127.0.0.1:8000/api/couriers?sort=registered_at&direction=desc" \
      -H "Accept: application/json"
    ```

3. **Cari Kurir (`budi agung` mencocokkan `Budiono Hadi Agung`)**:

    ```bash
    curl -X GET "http://127.0.0.1:8000/api/couriers?search=budi+agung" \
      -H "Accept: application/json"
    ```

4. **Filter hanya kurir dengan level 2 atau 3**:
    ```bash
    curl -X GET "http://127.0.0.1:8000/api/couriers?level=2,3" \
      -H "Accept: application/json"
    ```

#### Contoh Response (200 OK):

```json
{
    "current_page": 1,
    "data": [
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
    ],
    "first_page_url": "http://127.0.0.1:8000/api/couriers?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://127.0.0.1:8000/api/couriers?page=1",
    "next_page_url": null,
    "path": "http://127.0.0.1:8000/api/couriers",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

---

### B. Detail Kurir (Show)

Mengembalikan semua informasi atribut dari satu kurir berdasarkan ID.

- **Method**: `GET`
- **URL**: `/api/couriers/{id}`

#### Contoh cURL:

```bash
curl -X GET "http://127.0.0.1:8000/api/couriers/1" \
  -H "Accept: application/json"
```

#### Contoh Response (200 OK):

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

### C. Tambah Kurir Baru (Store)

Menambahkan data kurir baru ke sistem dengan validasi lengkap.

- **Method**: `POST`
- **URL**: `/api/couriers`
- **Aturan Validasi**:
    - `name`: required | string | max:255
    - `phone`: required | string | max:20 | unique:couriers,phone
    - `email`: nullable | email | max:255 | unique:couriers,email
    - `level`: required | integer | between:1,5
    - `vehicle_type`: nullable | string | max:50
    - `vehicle_plate_number`: nullable | string | max:20
    - `address`: nullable | string
    - `status`: sometimes | string | in:active,inactive (default: active)
    - `registered_at`: required | date (format: YYYY-MM-DD)

#### Contoh cURL:

```bash
curl -X POST "http://127.0.0.1:8000/api/couriers" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Budiono Hadi Agung",
    "phone": "081298745321",
    "email": "budiono.hadiagung@gmail.com",
    "level": 5,
    "vehicle_type": "motor",
    "vehicle_plate_number": "B 4821 XYZ",
    "address": "Jl. Jend. Sudirman No. 12, Jakarta",
    "status": "active",
    "registered_at": "2024-05-12"
  }'
```

#### Contoh Response (201 Created):

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

### D. Update Kurir (Update)

Memperbarui sebagian atau seluruh data kurir. Menggunakan aturan `sometimes` sehingga hanya field yang dikirim yang divalidasi dan diperbarui. Pengecekan `unique` pada `phone` dan `email` secara otomatis mengabaikan ID kurir yang bersangkutan.

- **Method**: `PUT` atau `PATCH`
- **URL**: `/api/couriers/{id}`

#### Contoh cURL:

```bash
curl -X PUT "http://127.0.0.1:8000/api/couriers/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "level": 4,
    "vehicle_type": "mobil",
    "vehicle_plate_number": "B 9999 KLA"
  }'
```

#### Contoh Response (200 OK):

```json
{
    "id": 1,
    "name": "Budiono Hadi Agung",
    "phone": "081298745321",
    "email": "budiono.hadiagung@gmail.com",
    "level": 4,
    "vehicle_type": "mobil",
    "vehicle_plate_number": "B 9999 KLA",
    "address": "Jl. Jend. Sudirman No. 12, Jakarta",
    "status": "active",
    "registered_at": "2024-05-12",
    "created_at": "2026-09-14T04:00:00.000000Z",
    "updated_at": "2026-09-14T04:10:00.000000Z"
}
```

---

### E. Hapus Kurir (Destroy)

Menghapus rekaman data kurir dari database.

- **Method**: `DELETE`
- **URL**: `/api/couriers/{id}`

#### Contoh cURL:

```bash
curl -X DELETE "http://127.0.0.1:8000/api/couriers/1" \
  -H "Accept: application/json"
```

#### Response:

- **HTTP Status**: `204 No Content` (body kosong)

---

## 5. Struktur Skema Database (`couriers`)

Tabel dibuat via migration `database/migrations/2026_09_14_034725_create_couriers_table.php`:

| Kolom                       | Tipe                       | Keterangan                                                  |
| --------------------------- | -------------------------- | ----------------------------------------------------------- |
| `id`                        | BIGINT UNSIGNED            | Primary Key (Auto Increment)                                |
| `name`                      | VARCHAR(255)               | Nama lengkap kurir (**Indexed**)                            |
| `phone`                     | VARCHAR(20)                | Nomor telepon kurir (**Unique**)                            |
| `email`                     | VARCHAR(255)               | Alamat email (**Nullable, Unique**)                         |
| `level`                     | UNSIGNED TINYINT           | Level kurir 1 s/d 5 (**Indexed**)                           |
| `vehicle_type`              | VARCHAR(50)                | Jenis kendaraan, misal: motor, mobil, pickup (**Nullable**) |
| `vehicle_plate_number`      | VARCHAR(20)                | Nomor plat kendaraan (**Nullable**)                         |
| `address`                   | TEXT                       | Alamat tempat tinggal (**Nullable**)                        |
| `status`                    | ENUM('active', 'inactive') | Status kurir (Default: `'active'`)                          |
| `registered_at`             | DATE                       | Tanggal terdaftar (**Indexed**)                             |
| `created_at` / `updated_at` | TIMESTAMP                  | Timestamp standar Eloquent                                  |
