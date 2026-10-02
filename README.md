# SIM Pembayaran SMK (Laravel 11 + MySQL)

Sistem informasi pengolahan uang SPP, uang ujian (UTS, UAS, dll), daftar ulang kenaikan kelas, dan uang praktek
untuk Sekolah Menengah Kejuruan. Pengguna: **Administrator** dan **Kepala Sekolah**.

## Cara memasang

1. Buat project Laravel baru, lalu masuk ke foldernya:

   ```bash
   composer create-project laravel/laravel sim-pembayaran
   cd sim-pembayaran
   ```

2. Salin isi folder ini ke dalam project (timpa file yang sudah ada):
   `app/`, `bootstrap/app.php`, `database/`, `public/css/`, `resources/views/`, `routes/web.php`.

3. Hapus file view bawaan yang tidak dipakai: `resources/views/welcome.blade.php` (opsional).

4. Atur `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sim_pembayaran
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Buat database `sim_pembayaran` di phpMyAdmin terlebih dahulu.

5. Jalankan:

   ```bash
   php artisan migrate:fresh --seed
   php artisan serve
   ```

6. Buka http://127.0.0.1:8000

## Akun contoh

| Peran | Email | Password |
|---|---|---|
| Administrator | admin@sekolah.test | password |
| Kepala sekolah | kepsek@sekolah.test | password |

Ganti password sebelum dipakai sungguhan. Data siswa dan tagihan contoh ada di `DatabaseSeeder.php`
(bagian "Data contoh"), hapus bagian itu jika tidak diperlukan.

## Hak akses

| Halaman | Administrator | Kepala sekolah |
|---|---|---|
| Dashboard | ya | ya |
| Data siswa dan status pembayaran | ya | ya (lihat saja) |
| Penunggakan | ya | ya (lihat saja) |
| Tambah/edit/hapus siswa, master data, buat tagihan, catat pembayaran | ya | tidak |

## Struktur file CSS

| File | Dipakai oleh |
|---|---|
| `public/css/base.css` | semua halaman (sidebar, tabel, form, tombol, label status) |
| `public/css/login.css` | `auth/login.blade.php` |
| `public/css/dashboard.css` | `dashboard.blade.php` |
| `public/css/siswa-index.css` | `siswa/index.blade.php` |
| `public/css/siswa-show.css` | `siswa/show.blade.php` |
| `public/css/siswa-form.css` | `siswa/form.blade.php` |
| `public/css/tunggakan.css` | `tunggakan/index.blade.php` |
| `public/css/tagihan.css` | `tagihan/generate.blade.php` |
| `public/css/master.css` | `master/index.blade.php` |

## Catatan

- Tunggakan = tagihan yang belum lunas dan sudah lewat jatuh tempo (lihat `scopeTunggakan` di `app/Models/Tagihan.php`).
- Pembayaran bisa dicicil. Status tagihan berubah otomatis: belum bayar, cicilan, lunas.
- Tagihan dibuat massal dari menu "Buat tagihan". Tagihan yang sama (siswa, jenis, tahun ajaran, periode) tidak dibuat dua kali.

## Kode Warna Logo
Biru = #0C3BBE
Kuning = #F3C010
