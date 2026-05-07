# SIVALID

## Sistem Informasi Validasi Instrumen Penelitian Berbasis CodeIgniter 4
Email    : admin@sivalid.test
Password : admin123
---

## 1. Identitas Project

- **Nama Project:** SIVALID
- **Kepanjangan:** Sistem Informasi Validasi Instrumen Penelitian
- **Framework:** CodeIgniter 4
- **Database:** MySQL / MariaDB
- **Tampilan Awal:** HTML polos + CSS sederhana
- **Fokus Utama:** Validasi instrumen, revisi butir instrumen, penyebaran instrumen valid, rekap hasil, analisis otomatis, dan laporan.

---

## 2. Latar Belakang

SIVALID dikembangkan untuk membantu peneliti mengelola proses validasi instrumen penelitian secara sistematis. Dalam penelitian pengembangan, instrumen yang telah disusun secara manual perlu divalidasi terlebih dahulu oleh validator instrumen sebelum digunakan untuk menilai produk penelitian atau diberikan kepada responden.

Sistem ini dirancang untuk mengelola tiga komponen utama:

1. Kisi-kisi instrumen.
2. Instrumen.
3. Lembar validasi instrumen.

Setelah instrumen divalidasi, sistem menghitung hasil validasi secara otomatis, menampilkan kategori kelayakan, menyimpan komentar validator, dan menyediakan ruang untuk merevisi butir-butir pernyataan instrumen. Instrumen yang telah valid kemudian dapat dibagikan kepada validator produk, mahasiswa, observer, atau responden lain melalui link pengisian tanpa login.

Sesuai dokumen instrumen penelitian yang digunakan, sistem perlu mengakomodasi instrumen seperti Form Penilaian Ahli terhadap Model Pembelajaran, Panduan Pembelajaran, Materi Ajar, E-Learning, Pedoman FGD, Pedoman Observasi, dan lembar validasi instrumen masing-masing.

---

## 3. Tujuan Sistem

SIVALID bertujuan untuk:

1. Menyimpan dan mengelola kisi-kisi instrumen penelitian.
2. Menyimpan instrumen yang telah dibuat secara manual.
3. Menyediakan halaman validasi instrumen untuk validator.
4. Menghitung hasil validasi instrumen secara otomatis.
5. Menampilkan rekap skor per butir, per aspek, dan keseluruhan.
6. Menyediakan fitur revisi butir instrumen berdasarkan hasil validasi.
7. Menetapkan instrumen yang telah valid.
8. Membagikan instrumen valid kepada validator produk atau responden.
9. Menyimpan hasil pengisian validator/responden.
10. Menyusun laporan validasi dan analisis hasil pengisian.

---

## 4. Konsep Dasar Sistem

SIVALID memiliki dua lapis utama.

### 4.1 Validasi Instrumen

Tahap ini digunakan untuk menilai apakah instrumen yang sudah disusun layak digunakan.

Validator instrumen akan melihat:

1. Kisi-kisi instrumen.
2. Instrumen yang divalidasi.
3. Lembar validasi instrumen.

Output tahap ini:

1. Skor validasi instrumen.
2. Komentar per butir.
3. Komentar umum validator.
4. Kesimpulan validasi.
5. Kategori kelayakan instrumen.
6. Rekomendasi revisi.
7. Status valid atau perlu revisi.

### 4.2 Penggunaan Instrumen Valid

Tahap ini digunakan setelah instrumen dinyatakan valid.

Validator produk atau responden akan melihat:

1. Produk yang divalidasi.
2. Instrumen penilaian produk.

Output tahap ini:

1. Skor validasi produk.
2. Komentar validator produk.
3. Kesimpulan validasi produk.
4. Rekap hasil.
5. Laporan analisis.

---

## 5. Alur Besar Sistem

```text
Admin login
-> Admin input data instrumen
-> Admin input kisi-kisi instrumen
-> Admin input butir instrumen
-> Admin membuat link validasi instrumen
-> Validator instrumen membuka link tanpa login
-> Validator melihat kisi-kisi, instrumen, dan lembar validasi
-> Validator mengisi skor dan komentar
-> Sistem menghitung hasil validasi instrumen
-> Admin melihat hasil validasi
-> Admin merevisi butir instrumen jika diperlukan
-> Admin menetapkan instrumen sebagai valid
-> Admin membuat link pengisian instrumen valid
-> Validator produk/responden membuka link tanpa login
-> Validator/responden mengisi instrumen
-> Sistem menghitung hasil pengisian
-> Admin mencetak laporan
```

---

## 6. Jenis Pengguna

### 6.1 Admin / Peneliti

Admin harus login.

Hak akses admin:

1. Mengelola instrumen.
2. Mengelola kisi-kisi.
3. Mengelola butir pernyataan.
4. Membuat link validasi instrumen.
5. Melihat hasil validasi instrumen.
6. Merevisi butir instrumen.
7. Menetapkan instrumen valid.
8. Membuat link pengisian instrumen valid.
9. Melihat hasil pengisian.
10. Mencetak laporan.

### 6.2 Validator Instrumen

Validator instrumen tidak perlu login. Validator cukup membuka link khusus.

Contoh:

```text
https://domain.com/validasi-instrumen/abc123
```

Yang muncul:

1. Identitas validator.
2. Kisi-kisi instrumen.
3. Instrumen yang divalidasi.
4. Lembar validasi instrumen.
5. Komentar umum.
6. Kesimpulan validasi.
7. Tombol kirim.

### 6.3 Validator Produk

Validator produk tidak perlu login.

Contoh link:

```text
https://domain.com/validasi-produk/xyz789
```

Yang muncul:

1. Identitas validator.
2. Produk yang divalidasi.
3. Instrumen penilaian produk.
4. Komentar/saran.
5. Kesimpulan validasi produk.
6. Tombol kirim.

### 6.4 Mahasiswa / Responden

Mahasiswa atau responden tidak perlu login.

Contoh link:

```text
https://domain.com/responden/rsp123
```

Yang muncul:

1. Identitas responden.
2. Angket/instrumen respon.
3. Komentar atau tanggapan.
4. Tombol kirim.

---

## 7. Layout Sistem

Sistem menggunakan sidebar, bukan top bar sebagai menu utama.

### 7.1 Struktur Layout

```text
+------------------------------------------------------+
| SIVALID - Sistem Validasi Instrumen         Admin    |
+----------------------+-------------------------------+
| SIDEBAR              | KONTEN UTAMA                  |
|                      |                               |
| Dashboard            | Judul Halaman                 |
| Master Instrumen     | Isi tabel/form/laporan        |
| Validasi Instrumen   |                               |
| Produk Penelitian    |                               |
| Validasi Produk      |                               |
| Instrumen Valid      |                               |
| Hasil Pengisian      |                               |
| Analisis & Laporan   |                               |
| Pengaturan           |                               |
+----------------------+-------------------------------+
```

---

## 8. Struktur Menu Sidebar

- Dashboard
- Master Instrumen
  - Data Instrumen
  - Kisi-Kisi Instrumen
  - Butir Pernyataan
  - Riwayat Revisi Butir
- Validasi Instrumen
  - Link Validasi Instrumen
  - Hasil Validasi Instrumen
  - Analisis Validasi Instrumen
  - Revisi Butir Instrumen
  - Instrumen Valid
- Produk Penelitian
  - Data Produk
  - File / Link Produk
  - Hubungkan Produk dengan Instrumen
- Validasi Produk
  - Link Validasi Produk
  - Hasil Validasi Produk
  - Analisis Validasi Produk
- Instrumen Siap Disebar
  - Link Angket Responden
  - Link Observasi
  - Link FGD
  - Link Tes Kinerja
- Hasil Pengisian
  - Hasil Validator Produk
  - Hasil Responden Mahasiswa
  - Hasil Observer
  - Hasil FGD
- Analisis & Laporan
  - Rekap Skor
  - Analisis per Butir
  - Analisis per Aspek
  - Komentar dan Saran
  - Cetak Laporan
- Pengaturan
  - Profil Penelitian
  - Skala Penilaian
  - Kategori Kelayakan
  - User Admin

---

## 9. Status Instrumen

Setiap instrumen memiliki status berikut:

1. Draft
2. Dalam Validasi Instrumen
3. Perlu Revisi
4. Valid
5. Siap Disebar
6. Selesai
7. Ditutup

### 9.1 Penjelasan Status

| Status | Keterangan |
| --- | --- |
| Draft | Instrumen baru dimasukkan ke sistem. |
| Dalam Validasi Instrumen | Link validasi instrumen sudah dibuat. |
| Perlu Revisi | Hasil validasi menunjukkan butir perlu diperbaiki. |
| Valid | Instrumen sudah layak digunakan. |
| Siap Disebar | Instrumen valid dan siap dibagikan. |
| Selesai | Pengisian selesai dianalisis. |
| Ditutup | Link pengisian ditutup. |

---

## 10. Status Produk

1. Draft Produk
2. Siap Divalidasi
3. Sedang Divalidasi
4. Perlu Revisi
5. Layak
6. Selesai

---

## 11. Mode Pengisian

Sistem menggunakan mode pengisian agar satu sistem dapat melayani berbagai jenis halaman.

Mode yang tersedia:

1. `validasi_instrumen`
2. `validasi_produk`
3. `respon_mahasiswa`
4. `observasi`
5. `fgd`
6. `tes_kinerja`

### 11.1 Mode Validasi Instrumen

Yang tampil:

1. Kisi-kisi instrumen.
2. Instrumen.
3. Lembar validasi instrumen.

### 11.2 Mode Validasi Produk

Yang tampil:

1. Produk yang divalidasi.
2. Instrumen penilaian produk.

### 11.3 Mode Respon Mahasiswa

Yang tampil:

1. Identitas mahasiswa.
2. Angket respon.
3. Komentar/tanggapan.

### 11.4 Mode Observasi

Yang tampil:

1. Identitas observer.
2. Pedoman observasi.
3. Skor keterlaksanaan.
4. Catatan observer.

### 11.5 Mode FGD

Yang tampil:

1. Identitas kegiatan FGD.
2. Pertanyaan pemandu.
3. Catatan masukan.
4. Kesimpulan FGD.

---

## 12. Rumus Perhitungan Otomatis

### 12.1 Skor Total

```text
Total Skor = jumlah seluruh skor yang diberikan validator
```

### 12.2 Skor Maksimal

```text
Skor Maksimal = jumlah butir x skor tertinggi x jumlah validator
```

Contoh:

```text
Jumlah butir = 30
Skor tertinggi = 4
Jumlah validator = 2

Skor Maksimal = 30 x 4 x 2
Skor Maksimal = 240
```

### 12.3 Persentase Kelayakan

```text
Persentase = (Total Skor / Skor Maksimal) x 100
```

### 12.4 Rata-Rata Per Butir

```text
Rata-rata Butir = Total skor butir / Jumlah validator
```

### 12.5 Rata-Rata Per Aspek

```text
Rata-rata Aspek = Total skor seluruh butir dalam aspek / Jumlah skor
```

---

## 13. Kategori Validasi Instrumen

| Persentase | Kategori |
| --- | --- |
| 85% - 100% | Layak digunakan tanpa revisi. |
| 70% - 84% | Layak digunakan dengan revisi kecil. |
| 55% - 69% | Perlu revisi besar sebelum digunakan. |
| < 55% | Tidak layak digunakan. |

---

## 14. Kategori Validasi Produk

| Persentase | Kategori |
| --- | --- |
| 85% - 100% | Sangat Layak |
| 70% - 84% | Layak |
| 55% - 69% | Kurang Layak |
| < 55% | Tidak Layak |

---

## 15. Kategori Per Butir

| Rata-Rata | Kategori | Rekomendasi |
| --- | --- | --- |
| 3.26 - 4.00 | Sangat Relevan | Dipertahankan |
| 2.51 - 3.25 | Cukup Relevan | Revisi kecil |
| 1.76 - 2.50 | Kurang Relevan | Revisi besar |
| 1.00 - 1.75 | Tidak Relevan | Ganti atau hapus |

---

## 16. Struktur Database

### 16.1 Tabel `users`

Digunakan untuk login admin.

```text
id
name
email
password
role
status
created_at
updated_at
```

### 16.2 Tabel `instruments`

Menyimpan data instrumen.

```text
id
kode
judul
jenis
sasaran
deskripsi
pengantar
petunjuk
skala_min
skala_max
status
created_at
updated_at
```

Contoh `jenis`:

1. `validasi_produk`
2. `validasi_instrumen`
3. `angket_respon`
4. `observasi`
5. `fgd`
6. `tes_kinerja`

### 16.3 Tabel `instrument_aspects`

Menyimpan aspek instrumen.

```text
id
instrument_id
nama_aspek
deskripsi
urutan
created_at
updated_at
```

### 16.4 Tabel `instrument_indicators`

Menyimpan kisi-kisi atau indikator.

```text
id
instrument_id
aspect_id
indikator
urutan
created_at
updated_at
```

### 16.5 Tabel `instrument_items`

Menyimpan butir instrumen.

```text
id
instrument_id
aspect_id
indicator_id
nomor
pernyataan
tipe_butir
wajib
urutan
status
created_at
updated_at
```

Contoh `tipe_butir`:

1. `skala`
2. `komentar`
3. `isian`
4. `pilihan`
5. `catatan`

### 16.6 Tabel `instrument_revisions`

Menyimpan riwayat revisi butir.

```text
id
instrument_item_id
pernyataan_lama
pernyataan_baru
alasan_revisi
sumber_revisi
tanggal_revisi
created_at
updated_at
```

Contoh `sumber_revisi`:

1. Komentar validator
2. Keputusan peneliti
3. Revisi redaksi
4. Revisi aspek

### 16.7 Tabel `research_products`

Menyimpan produk penelitian yang akan divalidasi.

```text
id
kode
nama_produk
jenis_produk
deskripsi
file_produk
link_produk
status
created_at
updated_at
```

Contoh `jenis_produk`:

1. Buku Model
2. Buku Ajar
3. Panduan Pembelajaran
4. E-Learning
5. Rubrik
6. Template Artikel

### 16.8 Tabel `product_instruments`

Menghubungkan produk dengan instrumen.

```text
id
product_id
instrument_id
keterangan
created_at
updated_at
```

Contoh:

```text
Produk: Buku Model
Instrumen: Form Penilaian Ahli terhadap Model Pembelajaran
```

### 16.9 Tabel `instrument_links`

Menyimpan link pengisian.

```text
id
instrument_id
product_id
token
mode
judul_link
sasaran
tanggal_mulai
tanggal_selesai
status
maksimal_respon
created_at
updated_at
```

Contoh `mode`:

1. `validasi_instrumen`
2. `validasi_produk`
3. `respon_mahasiswa`
4. `observasi`
5. `fgd`
6. `tes_kinerja`

### 16.10 Tabel `respondents`

Menyimpan identitas validator atau responden.

```text
id
instrument_link_id
nama
email
bidang_keahlian
instansi
jenis_responden
nim
program_studi
semester
kelas
tanggal_isi
created_at
updated_at
```

Contoh `jenis_responden`:

1. `validator_instrumen`
2. `validator_produk`
3. `mahasiswa`
4. `dosen`
5. `observer`
6. `peserta_fgd`

### 16.11 Tabel `responses`

Menyimpan data pengisian utama.

```text
id
instrument_id
instrument_link_id
product_id
respondent_id
mode
status
komentar_umum
kesimpulan
submitted_at
created_at
updated_at
```

### 16.12 Tabel `response_answers`

Menyimpan jawaban per butir.

```text
id
response_id
instrument_item_id
skor
jawaban_teks
komentar
created_at
updated_at
```

### 16.13 Tabel `analysis_results`

Menyimpan hasil analisis otomatis.

```text
id
instrument_id
instrument_link_id
product_id
mode
jumlah_responden
jumlah_butir
total_skor
skor_maksimal
rata_rata
persentase
kategori
catatan
created_at
updated_at
```

### 16.14 Tabel `analysis_aspects`

Menyimpan analisis per aspek.

```text
id
analysis_result_id
aspect_id
total_skor
skor_maksimal
rata_rata
persentase
kategori
created_at
updated_at
```

### 16.15 Tabel `analysis_items`

Menyimpan analisis per butir.

```text
id
analysis_result_id
instrument_item_id
total_skor
rata_rata
kategori
rekomendasi
created_at
updated_at
```

---

## 17. Struktur Folder CI4

```text
app/
|-- Controllers/
|   |-- Admin/
|   |   |-- Dashboard.php
|   |   |-- Instruments.php
|   |   |-- InstrumentAspects.php
|   |   |-- InstrumentIndicators.php
|   |   |-- InstrumentItems.php
|   |   |-- InstrumentRevisions.php
|   |   |-- InstrumentLinks.php
|   |   |-- InstrumentValidation.php
|   |   |-- Products.php
|   |   |-- ProductValidation.php
|   |   |-- Responses.php
|   |   |-- Analysis.php
|   |   |-- Reports.php
|   |   `-- Settings.php
|   |-- PublicForm.php
|   `-- Auth.php
|-- Models/
|   |-- UserModel.php
|   |-- InstrumentModel.php
|   |-- InstrumentAspectModel.php
|   |-- InstrumentIndicatorModel.php
|   |-- InstrumentItemModel.php
|   |-- InstrumentRevisionModel.php
|   |-- ResearchProductModel.php
|   |-- ProductInstrumentModel.php
|   |-- InstrumentLinkModel.php
|   |-- RespondentModel.php
|   |-- ResponseModel.php
|   |-- ResponseAnswerModel.php
|   |-- AnalysisResultModel.php
|   |-- AnalysisAspectModel.php
|   `-- AnalysisItemModel.php
|-- Views/
|   |-- layouts/
|   |   |-- main.php
|   |   |-- sidebar.php
|   |   |-- topbar.php
|   |   `-- footer.php
|   |-- auth/
|   |   `-- login.php
|   |-- admin/
|   |   |-- dashboard.php
|   |   |-- instruments/
|   |   |-- aspects/
|   |   |-- indicators/
|   |   |-- items/
|   |   |-- revisions/
|   |   |-- links/
|   |   |-- products/
|   |   |-- validations/
|   |   |-- responses/
|   |   |-- analysis/
|   |   `-- reports/
|   `-- public/
|       |-- validasi_instrumen.php
|       |-- validasi_produk.php
|       |-- responden.php
|       |-- observasi.php
|       |-- fgd.php
|       `-- thanks.php
|-- Filters/
|   `-- AuthFilter.php
|-- Database/
|   |-- Migrations/
|   `-- Seeds/
`-- Helpers/
    |-- sivalid_helper.php
    `-- analysis_helper.php
```

---

## 18. Daftar Route Awal

```php
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    $routes->resource('instruments', ['controller' => 'Admin\Instruments']);
    $routes->resource('instrument-aspects', ['controller' => 'Admin\InstrumentAspects']);
    $routes->resource('instrument-indicators', ['controller' => 'Admin\InstrumentIndicators']);
    $routes->resource('instrument-items', ['controller' => 'Admin\InstrumentItems']);
    $routes->resource('instrument-revisions', ['controller' => 'Admin\InstrumentRevisions']);

    $routes->resource('products', ['controller' => 'Admin\Products']);
    $routes->resource('instrument-links', ['controller' => 'Admin\InstrumentLinks']);

    $routes->get('validasi-instrumen', 'Admin\InstrumentValidation::index');
    $routes->get('validasi-instrumen/hasil/(:num)', 'Admin\InstrumentValidation::show/$1');
    $routes->get('validasi-instrumen/analisis/(:num)', 'Admin\InstrumentValidation::analysis/$1');
    $routes->post('validasi-instrumen/tetapkan-valid/(:num)', 'Admin\InstrumentValidation::setValid/$1');

    $routes->get('validasi-produk', 'Admin\ProductValidation::index');
    $routes->get('validasi-produk/hasil/(:num)', 'Admin\ProductValidation::show/$1');
    $routes->get('validasi-produk/analisis/(:num)', 'Admin\ProductValidation::analysis/$1');

    $routes->get('responses', 'Admin\Responses::index');
    $routes->get('analysis', 'Admin\Analysis::index');
    $routes->get('reports', 'Admin\Reports::index');
    $routes->get('reports/cetak/(:num)', 'Admin\Reports::print/$1');
});

$routes->get('isi/(:segment)', 'PublicForm::show/$1');
$routes->post('isi/(:segment)', 'PublicForm::submit/$1');
$routes->get('terima-kasih', 'PublicForm::thanks');
```

---

## 19. Tahapan Pengerjaan Project

### TAHAP 1: Setup Project CI4

#### Tujuan

Membuat fondasi awal project SIVALID.

#### Output

1. Project CI4 terpasang.
2. Database terkoneksi.
3. Base URL berjalan.
4. Halaman login awal tersedia.
5. Layout polos tersedia.

#### Perintah Instalasi

```bat
cd /d C:\xampp\htdocs
composer create-project codeigniter4/appstarter sivalid
cd sivalid
php spark serve
```

#### Konfigurasi `.env`

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/sivalid/public/'

database.default.hostname = localhost
database.default.database = sivalid
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

#### Database

Buat database:

```sql
CREATE DATABASE sivalid;
```

### TAHAP 2: Layout Polos dan Login Admin

#### Tujuan

Membuat tampilan dasar sistem dengan sidebar.

#### Output

1. Login admin.
2. Session login.
3. Logout.
4. Layout utama.
5. Sidebar.
6. Topbar sederhana.
7. Dashboard awal.

#### File yang Dibuat

```text
app/Controllers/Auth.php
app/Controllers/Admin/Dashboard.php
app/Filters/AuthFilter.php
app/Views/auth/login.php
app/Views/layouts/main.php
app/Views/layouts/sidebar.php
app/Views/layouts/topbar.php
app/Views/admin/dashboard.php
```

#### Menu Awal Sidebar

1. Dashboard
2. Master Instrumen
3. Validasi Instrumen
4. Produk Penelitian
5. Validasi Produk
6. Hasil Pengisian
7. Analisis & Laporan
8. Pengaturan

### TAHAP 3: Master Data Instrumen

#### Tujuan

Membuat CRUD instrumen.

#### Output

1. Daftar instrumen.
2. Tambah instrumen.
3. Edit instrumen.
4. Hapus instrumen.
5. Detail instrumen.

#### Data yang Dikelola

1. Kode instrumen
2. Judul instrumen
3. Jenis instrumen
4. Sasaran
5. Deskripsi
6. Pengantar
7. Petunjuk
8. Skala minimal
9. Skala maksimal
10. Status

#### File

```text
app/Controllers/Admin/Instruments.php
app/Models/InstrumentModel.php
app/Views/admin/instruments/index.php
app/Views/admin/instruments/form.php
app/Views/admin/instruments/show.php
```

### TAHAP 4: Kisi-Kisi Instrumen

#### Tujuan

Membuat fitur pengelolaan kisi-kisi instrumen.

#### Output

1. Tambah aspek.
2. Tambah indikator.
3. Tampilkan kisi-kisi instrumen.
4. Urutkan aspek dan indikator.

#### Data yang Dikelola

1. Instrumen
2. Aspek
3. Indikator
4. Urutan

#### File

```text
app/Controllers/Admin/InstrumentAspects.php
app/Controllers/Admin/InstrumentIndicators.php
app/Models/InstrumentAspectModel.php
app/Models/InstrumentIndicatorModel.php
app/Views/admin/aspects/index.php
app/Views/admin/aspects/form.php
app/Views/admin/indicators/index.php
app/Views/admin/indicators/form.php
```

### TAHAP 5: Butir Pernyataan Instrumen

#### Tujuan

Membuat fitur pengelolaan butir instrumen.

#### Output

1. Tambah butir.
2. Edit butir.
3. Hapus butir.
4. Hubungkan butir dengan aspek dan indikator.
5. Tampilkan butir berdasarkan instrumen.

#### Data yang Dikelola

1. Instrumen
2. Aspek
3. Indikator
4. Nomor butir
5. Pernyataan
6. Tipe butir
7. Status butir
8. Urutan

#### File

```text
app/Controllers/Admin/InstrumentItems.php
app/Models/InstrumentItemModel.php
app/Views/admin/items/index.php
app/Views/admin/items/form.php
```

### TAHAP 6: Produk Penelitian

#### Tujuan

Menyimpan produk yang akan divalidasi.

#### Output

1. Data produk penelitian.
2. Upload file produk.
3. Simpan link produk.
4. Hubungkan produk dengan instrumen validasi.

#### Contoh Produk

1. Buku Model Pembelajaran
2. Buku Ajar / Materi Ajar
3. Panduan Pembelajaran Dosen dan Mahasiswa
4. E-Learning
5. Rubrik Penilaian
6. Template Artikel

#### File

```text
app/Controllers/Admin/Products.php
app/Models/ResearchProductModel.php
app/Models/ProductInstrumentModel.php
app/Views/admin/products/index.php
app/Views/admin/products/form.php
app/Views/admin/products/show.php
```

### TAHAP 7: Link Validasi Instrumen

#### Tujuan

Membuat link publik untuk validator instrumen.

#### Output

1. Generate token link.
2. Tentukan instrumen yang divalidasi.
3. Tentukan mode `validasi_instrumen`.
4. Link dapat dikirim ke validator.
5. Validator membuka halaman tanpa login.

#### Contoh Link

```text
http://localhost/sivalid/public/isi/abc123
```

#### Halaman Validator Instrumen Menampilkan

1. Identitas Validator
2. Kisi-Kisi Instrumen
3. Instrumen yang Divalidasi
4. Lembar Validasi Instrumen
5. Komentar Umum
6. Kesimpulan Validasi

#### File

```text
app/Controllers/Admin/InstrumentLinks.php
app/Controllers/PublicForm.php
app/Models/InstrumentLinkModel.php
app/Views/admin/links/index.php
app/Views/admin/links/form.php
app/Views/public/validasi_instrumen.php
app/Views/public/thanks.php
```

### TAHAP 8: Simpan Hasil Validasi Instrumen

#### Tujuan

Menyimpan hasil pengisian validator instrumen.

#### Output

1. Simpan identitas validator.
2. Simpan skor setiap butir.
3. Simpan komentar per butir.
4. Simpan komentar umum.
5. Simpan kesimpulan validasi.
6. Status pengisian tersimpan.

#### File

```text
app/Models/RespondentModel.php
app/Models/ResponseModel.php
app/Models/ResponseAnswerModel.php
```

### TAHAP 9: Analisis Validasi Instrumen

#### Tujuan

Menghitung hasil validasi instrumen secara otomatis.

#### Output

1. Total skor.
2. Skor maksimal.
3. Persentase kelayakan.
4. Kategori kelayakan.
5. Analisis per aspek.
6. Analisis per butir.
7. Rekomendasi revisi butir.

#### File

```text
app/Controllers/Admin/InstrumentValidation.php
app/Controllers/Admin/Analysis.php
app/Models/AnalysisResultModel.php
app/Models/AnalysisAspectModel.php
app/Models/AnalysisItemModel.php
app/Views/admin/validations/instrument_result.php
app/Views/admin/validations/instrument_analysis.php
```

### TAHAP 10: Revisi Butir Instrumen

#### Tujuan

Merevisi butir berdasarkan hasil validasi instrumen.

#### Output

1. Tampilkan butir lama.
2. Tampilkan rata-rata skor butir.
3. Tampilkan komentar validator.
4. Form revisi butir.
5. Simpan riwayat revisi.
6. Tandai butir telah direvisi.

#### File

```text
app/Controllers/Admin/InstrumentRevisions.php
app/Models/InstrumentRevisionModel.php
app/Views/admin/revisions/index.php
app/Views/admin/revisions/form.php
```

### TAHAP 11: Penetapan Instrumen Valid

#### Tujuan

Menetapkan instrumen yang telah selesai divalidasi dan direvisi sebagai instrumen valid.

#### Output

1. Tombol "Tetapkan sebagai Valid".
2. Status instrumen berubah menjadi Valid.
3. Instrumen masuk ke daftar instrumen valid.
4. Instrumen dapat digunakan untuk validasi produk atau pengisian responden.

#### Proses

```text
Hasil validasi instrumen dilihat
-> Butir yang perlu revisi diperbaiki
-> Admin klik Tetapkan Valid
-> Status instrumen = Valid
```

### TAHAP 12: Link Validasi Produk

#### Tujuan

Membuat link untuk validator produk.

#### Output

1. Pilih produk yang divalidasi.
2. Pilih instrumen valid.
3. Buat link pengisian.
4. Validator produk membuka link tanpa login.

#### Halaman Validator Produk Menampilkan

1. Identitas Validator
2. Produk yang Divalidasi
3. Pengantar
4. Petunjuk Pengisian
5. Instrumen Penilaian Produk
6. Komentar/Saran
7. Kesimpulan Validasi Produk

#### File

```text
app/Views/public/validasi_produk.php
app/Controllers/Admin/ProductValidation.php
```

### TAHAP 13: Simpan dan Analisis Validasi Produk

#### Tujuan

Menyimpan dan menghitung hasil validasi produk.

#### Output

1. Rekap skor validator produk.
2. Rekap per aspek.
3. Rekap per butir.
4. Persentase kelayakan produk.
5. Kategori produk.
6. Komentar dan saran validator.

#### Kategori

1. Sangat Layak
2. Layak
3. Kurang Layak
4. Tidak Layak

### TAHAP 14: Link Instrumen untuk Responden

#### Tujuan

Membagikan instrumen valid kepada responden mahasiswa, observer, peserta FGD, atau pengguna lain.

#### Output

1. Link angket mahasiswa.
2. Link observasi.
3. Link FGD.
4. Link tes kinerja.
5. Hasil pengisian tersimpan.

#### Mode

1. `respon_mahasiswa`
2. `observasi`
3. `fgd`
4. `tes_kinerja`

### TAHAP 15: Laporan

#### Tujuan

Membuat laporan hasil validasi dan pengisian.

#### Jenis Laporan

1. Laporan Validasi Instrumen
2. Laporan Revisi Butir Instrumen
3. Laporan Validasi Produk
4. Laporan Respon Mahasiswa
5. Laporan Observasi
6. Laporan FGD

#### Isi Laporan Validasi Instrumen

1. Identitas instrumen
2. Tujuan validasi instrumen
3. Identitas validator
4. Rekap skor per validator
5. Rekap skor per aspek
6. Rekap skor per butir
7. Komentar validator
8. Persentase kelayakan
9. Kategori kelayakan
10. Daftar revisi butir
11. Kesimpulan akhir

#### Isi Laporan Validasi Produk

1. Identitas produk
2. Identitas instrumen
3. Identitas validator
4. Rekap skor per aspek
5. Rekap skor keseluruhan
6. Komentar dan saran
7. Persentase kelayakan
8. Kategori produk
9. Kesimpulan

### TAHAP 16: Cetak HTML dan PDF

#### Tujuan

Membuat laporan dapat dicetak.

#### Output Awal

1. Cetak HTML dulu.
2. Tombol print browser.
3. Tampilan A4 sederhana.
4. Setelah stabil, baru tambah PDF.

#### File

```text
app/Views/admin/reports/print_validasi_instrumen.php
app/Views/admin/reports/print_validasi_produk.php
```

---

## 20. Prioritas Pengerjaan

Prioritas pengerjaan paling aman:

1. Setup CI4
2. Login dan layout sidebar
3. CRUD instrumen
4. CRUD kisi-kisi
5. CRUD butir instrumen
6. Generate link validasi instrumen
7. Halaman publik validasi instrumen
8. Simpan hasil validasi
9. Analisis otomatis
10. Revisi butir
11. Tetapkan instrumen valid
12. Data produk penelitian
13. Link validasi produk
14. Analisis validasi produk
15. Laporan
16. Cetak HTML/PDF

---

## 21. Definisi MVP

MVP SIVALID dinyatakan selesai jika sudah memiliki fitur berikut:

1. Admin bisa login.
2. Admin bisa membuat instrumen.
3. Admin bisa membuat kisi-kisi instrumen.
4. Admin bisa membuat butir instrumen.
5. Admin bisa membuat link validasi instrumen.
6. Validator instrumen bisa mengisi tanpa login.
7. Sistem menyimpan hasil validasi.
8. Sistem menghitung persentase kelayakan instrumen.
9. Admin bisa melihat butir yang perlu revisi.
10. Admin bisa merevisi butir.
11. Admin bisa menetapkan instrumen sebagai valid.
12. Admin bisa membuat link validasi produk.
13. Validator produk bisa mengisi tanpa login.
14. Sistem bisa menghitung hasil validasi produk.
15. Sistem bisa mencetak laporan sederhana.

---

## 22. Catatan Desain Awal

Pada tahap awal, sistem tidak perlu menggunakan template berat.

Gunakan tampilan sederhana:

1. HTML polos
2. CSS sederhana
3. Sidebar sederhana
4. Tabel standar
5. Form standar
6. Button standar
7. Tanpa tema admin dulu
8. Tanpa JavaScript kompleks
9. Tanpa dashboard grafik dulu

Fokus utama:

1. Data benar
2. Alur benar
3. Rumus benar
4. Laporan benar

---

## 23. Catatan Penting Pengembangan

1. Jangan mencampur validasi instrumen dengan validasi produk.
2. Validasi instrumen menilai alat ukur.
3. Validasi produk menilai produk penelitian.
4. Validator instrumen harus melihat kisi-kisi, instrumen, dan lembar validasi.
5. Validator produk cukup melihat produk yang divalidasi dan instrumen penilaian produk.
6. Instrumen hanya bisa disebarkan jika statusnya sudah valid.
7. Semua pengisian publik menggunakan token.
8. Validator dan responden tidak perlu login.
9. Setiap revisi butir harus tersimpan dalam riwayat.
10. Laporan harus menampilkan skor, persentase, kategori, komentar, dan kesimpulan.

---

## 24. Penutup

SIVALID dikembangkan sebagai sistem bantu penelitian untuk memastikan proses validasi instrumen dan validasi produk berjalan lebih tertib, terdokumentasi, dan dapat dianalisis secara otomatis.

Sistem ini tidak hanya berfungsi sebagai formulir online, tetapi sebagai alur kerja lengkap:

```text
Input instrumen
-> Validasi instrumen
-> Analisis hasil validasi
-> Revisi butir
-> Penetapan instrumen valid
-> Penyebaran instrumen
-> Pengumpulan data
-> Analisis
-> Laporan
```

Dengan rancangan ini, seluruh proses validasi instrumen penelitian dapat dilakukan secara lebih sistematis, transparan, dan siap digunakan dalam penelitian disertasi.

---

## 25. Catatan Analisis Setelah Tahap 1-16

Bagian ini berisi temuan awal setelah aplikasi dikembangkan sampai Tahap 16. Catatan ini menjadi daftar cek bersama sebelum masuk ke pengembangan berikutnya.

### 25.1 Bug Status Butir Setelah Revisi

Setelah butir direvisi, status butir berubah menjadi `Direvisi`. Namun form publik dan proses analisis saat ini hanya mengambil butir dengan status `Aktif`.

Dampak:

1. Butir yang sudah direvisi bisa tidak tampil pada form publik.
2. Butir yang sudah direvisi bisa tidak ikut dihitung pada analisis.
3. Hasil validasi setelah revisi berpotensi tidak lengkap.

Prioritas:

1. Pastikan butir `Direvisi` tetap dapat digunakan sebagai butir aktif.
2. Atau ubah mekanisme revisi agar status butir kembali menjadi `Aktif` setelah revisi disimpan.

Referensi: InstrumentRevisions.php (line 137), PublicForm.php (line 96).

### 25.2 Skala Penilaian Masih Tetap 1-4

Database instrumen sudah memiliki `skala_min` dan `skala_max`, tetapi form publik, validasi submit, dan perhitungan analisis masih memakai skor 1 sampai 4 secara tetap.

Dampak:

1. Instrumen dengan skala selain 1-4 belum bisa digunakan dengan benar.
2. Skor maksimal pada analisis belum mengikuti konfigurasi instrumen.

Prioritas:

1. Form publik harus menampilkan pilihan skor dari `skala_min` sampai `skala_max`.
2. Validasi submit harus mengikuti skala instrumen.
3. Rumus skor maksimal harus memakai `skala_max`.

Referensi: PublicForm.php (line 277), InstrumentValidation.php (line 129), validasi_instrumen.php (line 355).

### 25.3 Tipe Butir Belum Benar-Benar Digunakan

Admin sudah dapat memilih tipe butir seperti `skala`, `komentar`, `isian`, `pilihan`, dan `catatan`. Namun form publik masih memperlakukan semua butir sebagai butir skala yang wajib diberi skor.

Dampak:

1. Butir komentar atau isian tetap dipaksa memiliki skor.
2. Field `jawaban_teks` belum dimanfaatkan.
3. Field `wajib` belum memengaruhi validasi pengisian.

Prioritas:

1. Bedakan tampilan input berdasarkan `tipe_butir`.
2. Simpan jawaban teks untuk tipe non-skala.
3. Terapkan aturan wajib berdasarkan field `wajib`.

eferensi: form.php (line 141), PublicForm.php (line 250).

### 25.4 Mode Responden Masih Generik

Mode `observasi`, `fgd`, dan `tes_kinerja` sudah tersedia, tetapi tampilannya masih memakai template angket respon mahasiswa.

Dampak:

1. Label identitas belum sesuai dengan jenis responden.
2. Observasi belum menampilkan konteks observer dan catatan observasi secara khusus.
3. FGD belum menampilkan identitas kegiatan dan catatan masukan secara khusus.
4. Tes kinerja belum memiliki tampilan penilaian yang khas.

Prioritas:

1. Buat tampilan publik khusus untuk observasi.
2. Buat tampilan publik khusus untuk FGD.
3. Buat tampilan publik khusus untuk tes kinerja.

Referensi: observasi.php (line 1), fgd.php (line 1), tes_kinerja.php (line 1).

### 25.5 Dashboard, Hasil Pengisian, dan Pengaturan Belum Matang

Dashboard masih menampilkan angka statis dan catatan tahap awal. Menu `Hasil Pengisian` dan `Pengaturan` juga belum memiliki halaman fungsional.

Dampak:

1. Admin belum mendapat ringkasan data nyata.
2. Hasil pengisian belum punya menu khusus untuk melihat data mentah.
3. Pengaturan kategori, skala, profil penelitian, dan admin belum tersedia.

Prioritas:

1. Dashboard menampilkan jumlah instrumen, link aktif, respon masuk, dan laporan.
2. Buat halaman hasil pengisian per mode.
3. Buat pengaturan minimal untuk profil penelitian, kategori kelayakan, dan user admin.

Referensi: dashboard.php (line 10), sidebar.php (line 58).

### 25.6 Status Workflow Belum Sepenuhnya Otomatis

Status instrumen dan produk sudah tersedia, tetapi sebagian perubahan status masih manual.

Dampak:

1. Membuat link validasi instrumen belum otomatis mengubah status menjadi `Dalam Validasi Instrumen`.
2. Hasil analisis dengan rekomendasi revisi belum otomatis mengubah status menjadi `Perlu Revisi`.
3. Instrumen valid yang sudah dibuatkan link responden belum otomatis menjadi `Siap Disebar`.
4. Produk yang sedang divalidasi belum selalu berubah mengikuti alur validasi.

Prioritas:

1. Tambahkan aturan transisi status pada proses penting.
2. Tetap beri ruang admin untuk koreksi manual jika diperlukan.

### 25.7 Keamanan Input Publik Perlu Diperkuat

Form sudah menggunakan `csrf_field()`, tetapi filter CSRF global masih belum diaktifkan. Sistem juga menerima input dari link publik tanpa login.

Dampak:

1. Perlindungan submit form publik belum optimal.
2. Link publik perlu perlindungan tambahan dari spam dan pengisian berulang yang tidak diinginkan.

Prioritas:

1. Aktifkan dan uji CSRF untuk form admin dan publik.
2. Pertimbangkan batasan pengisian berdasarkan email, token, atau kuota.
3. Tambahkan validasi link dan tanggal secara lebih jelas pada halaman publik.

Referensi: Filters.php (line 77).


### 25.8 PDF Belum Ditambahkan

Tahap 16 sudah memiliki cetak HTML A4 dengan tombol print browser. PDF belum tersedia.

Dampak:

1. Admin belum bisa mengunduh laporan PDF langsung dari sistem.
2. Laporan masih bergantung pada fitur print/save PDF bawaan browser.

Prioritas:

1. Stabilkan tampilan cetak HTML terlebih dahulu.
2. Setelah stabil, tambahkan library PDF seperti mPDF atau Dompdf.
3. Buat tombol unduh PDF untuk laporan validasi instrumen dan validasi produk.
