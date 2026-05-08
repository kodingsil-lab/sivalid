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
|   |   |-- RespondentLinks.php
|   |   |-- ReportPdf.php
|   |   |-- Reports.php
|   |   |-- Settings.php
|   |   `-- SubmissionResults.php
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
|   |   |-- respondent_links/
|   |   |-- validations/
|   |   |-- submissions/
|   |   |-- settings/
|   |   `-- reports/
|   `-- public/
|       |-- validasi_instrumen.php
|       |-- validasi_produk.php
|       |-- respon_mahasiswa.php
|       |-- observasi.php
|       |-- fgd.php
|       |-- tes_kinerja.php
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

Bagian ini berisi temuan awal setelah aplikasi dikembangkan sampai Tahap 16. Setelah perbaikan 25.1 sampai 25.8 diterapkan, bagian ini dipertahankan sebagai riwayat masalah awal, bukan backlog aktif. Pekerjaan lanjutan dipindahkan ke bagian 26.

### 25.1 Bug Status Butir Setelah Revisi

Status: **Selesai diterapkan**.

Ringkasan implementasi:

1. `Direvisi` tetap dipertahankan sebagai jejak revisi, tetapi ikut diperlakukan sebagai butir yang dapat digunakan.
2. Query form publik dan analisis mengambil status `Aktif` dan `Direvisi`.
3. File utama yang diperbarui:
   - `app/Models/InstrumentItemModel.php`
   - `app/Controllers/PublicForm.php`
   - `app/Controllers/Admin/InstrumentValidation.php`
   - `app/Controllers/Admin/ProductValidation.php`

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

Status: **Selesai diterapkan**.

Ringkasan implementasi:

1. Form publik membaca rentang skor dari `instruments.skala_min` sampai `instruments.skala_max`.
2. Submit publik memvalidasi skor sesuai skala instrumen.
3. Analisis instrumen dan produk memakai `skala_max` untuk skor maksimal dan kategori adaptif.
4. File utama yang diperbarui:
   - `app/Controllers/PublicForm.php`
   - `app/Controllers/Admin/InstrumentValidation.php`
   - `app/Controllers/Admin/ProductValidation.php`
   - `app/Views/public/validasi_instrumen.php`
   - `app/Views/public/validasi_produk.php`
   - `app/Views/public/respon_mahasiswa.php`
   - `app/Views/public/observasi.php`
   - `app/Views/public/fgd.php`
   - `app/Views/public/tes_kinerja.php`

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

Status: **Selesai diterapkan**.

Ringkasan implementasi:

1. `tipe_butir = skala` memakai input skor dan disimpan ke kolom `skor`.
2. `tipe_butir` non-skala memakai input teks dan disimpan ke `jawaban_teks`.
3. Field `wajib` dipakai untuk menentukan input wajib atau opsional.
4. Analisis skor hanya menghitung butir `skala`.
5. File utama yang diperbarui:
   - `app/Controllers/PublicForm.php`
   - `app/Controllers/Admin/InstrumentValidation.php`
   - `app/Controllers/Admin/ProductValidation.php`
   - `app/Views/public/validasi_instrumen.php`
   - `app/Views/public/validasi_produk.php`
   - `app/Views/public/respon_mahasiswa.php`
   - `app/Views/public/observasi.php`
   - `app/Views/public/fgd.php`
   - `app/Views/public/tes_kinerja.php`

Admin sudah dapat memilih tipe butir seperti `skala`, `komentar`, `isian`, `pilihan`, dan `catatan`. Namun form publik masih memperlakukan semua butir sebagai butir skala yang wajib diberi skor.

Dampak:

1. Butir komentar atau isian tetap dipaksa memiliki skor.
2. Field `jawaban_teks` belum dimanfaatkan.
3. Field `wajib` belum memengaruhi validasi pengisian.

Prioritas:

1. Bedakan tampilan input berdasarkan `tipe_butir`.
2. Simpan jawaban teks untuk tipe non-skala.
3. Terapkan aturan wajib berdasarkan field `wajib`.

Referensi: form.php (line 141), PublicForm.php (line 250).

### 25.4 Mode Responden Masih Generik

Status: **Selesai diterapkan**.

Ringkasan implementasi:

1. View publik `observasi`, `fgd`, dan `tes_kinerja` sudah berdiri sendiri.
2. Setiap mode memakai istilah identitas, petunjuk, tabel, dan komentar umum sesuai konteks.
3. Semua mode tetap mendukung skala dinamis, `tipe_butir`, dan `wajib`.
4. File utama yang diperbarui:
   - `app/Views/public/observasi.php`
   - `app/Views/public/fgd.php`
   - `app/Views/public/tes_kinerja.php`
   - `app/Controllers/PublicForm.php`

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

Status: **Selesai diterapkan**.

Ringkasan implementasi:

1. Dashboard memakai data nyata dari instrumen, produk, link, response, dan laporan analisis.
2. Menu Hasil Pengisian tersedia untuk melihat data mentah dan detail jawaban.
3. Menu Pengaturan minimal tersedia untuk profil penelitian dan kategori kelayakan.
4. Tabel `settings` dan `SettingModel` ditambahkan.
5. File utama yang diperbarui/ditambahkan:
   - `app/Controllers/Admin/Dashboard.php`
   - `app/Views/admin/dashboard.php`
   - `app/Controllers/Admin/SubmissionResults.php`
   - `app/Views/admin/submissions/index.php`
   - `app/Views/admin/submissions/show.php`
   - `app/Controllers/Admin/Settings.php`
   - `app/Models/SettingModel.php`
   - `app/Views/admin/settings/index.php`
   - `app/Database/Migrations/2026-05-08-000016_CreateSettingsTable.php`
   - `app/Config/Routes.php`
   - `app/Views/layouts/sidebar.php`

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

Status: **Selesai diterapkan**.

Ringkasan implementasi:

1. `WorkflowStatusService` dibuat untuk memusatkan transisi status instrumen dan produk.
2. Link validasi instrumen, analisis instrumen, revisi butir, penetapan valid, link responden, link validasi produk, dan analisis produk sudah memanggil service workflow.
3. Admin tetap dapat mengoreksi status manual dari form master.
4. File utama yang diperbarui/ditambahkan:
   - `app/Libraries/WorkflowStatusService.php`
   - `app/Controllers/Admin/InstrumentLinks.php`
   - `app/Controllers/Admin/InstrumentValidation.php`
   - `app/Controllers/Admin/InstrumentRevisions.php`
   - `app/Controllers/Admin/RespondentLinks.php`
   - `app/Controllers/Admin/ProductValidation.php`
   - `app/Views/admin/instruments/form.php`
   - `app/Views/admin/products/form.php`

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

Status: **Selesai diterapkan**.

Ringkasan implementasi:

1. CSRF global diaktifkan.
2. Form publik memakai honeypot `website`.
3. Link publik divalidasi berdasarkan status, tanggal mulai, tanggal selesai, dan kuota.
4. Submit ganda dicegah berdasarkan email dan NIM untuk mode yang relevan.
5. Penguatan lanjutan berupa token lebih panjang, index, dan transaksi dicatat di 26.6.
6. File utama yang diperbarui:
   - `.env`
   - `app/Config/Filters.php`
   - `app/Models/RespondentModel.php`
   - `app/Controllers/PublicForm.php`
   - `app/Views/public/validasi_instrumen.php`
   - `app/Views/public/validasi_produk.php`
   - `app/Views/public/respon_mahasiswa.php`
   - `app/Views/public/observasi.php`
   - `app/Views/public/fgd.php`
   - `app/Views/public/tes_kinerja.php`
   - `app/Views/public/thanks.php`

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

Status: **Selesai diterapkan**.

Ringkasan implementasi:

1. Dompdf ditambahkan sebagai library PDF.
2. `PdfService` dibuat untuk render dan preview PDF.
3. `ReportPdf` dibuat untuk PDF validasi instrumen dan validasi produk.
4. Route preview dan unduh PDF ditambahkan.
5. View print laporan dibuat aman untuk mode HTML print dan mode PDF.
6. Tombol Preview PDF dan Unduh PDF ditambahkan pada laporan.
7. File utama yang diperbarui/ditambahkan:
   - `composer.json`
   - `composer.lock`
   - `app/Libraries/PdfService.php`
   - `app/Controllers/Admin/ReportPdf.php`
   - `app/Config/Routes.php`
   - `app/Views/admin/reports/print_validasi_instrumen.php`
   - `app/Views/admin/reports/print_validasi_produk.php`
   - `app/Views/admin/reports/validasi_instrumen.php`
   - `app/Views/admin/reports/validasi_produk.php`
   - `app/Views/admin/reports/index.php`

Tahap 16 sudah memiliki cetak HTML A4 dengan tombol print browser. PDF belum tersedia.

Dampak:

1. Admin belum bisa mengunduh laporan PDF langsung dari sistem.
2. Laporan masih bergantung pada fitur print/save PDF bawaan browser.

Prioritas:

1. Stabilkan tampilan cetak HTML terlebih dahulu.
2. Setelah stabil, tambahkan library PDF seperti mPDF atau Dompdf.
3. Buat tombol unduh PDF untuk laporan validasi instrumen dan validasi produk.

---

## 26. Analisis Ulang Setelah Perbaikan 25.1-25.8

Bagian ini adalah pembaruan setelah perbaikan 25.1 sampai 25.8 mulai diterapkan pada aplikasi. Catatan 25.1 sampai 25.8 di atas tetap dipertahankan sebagai riwayat masalah awal, sedangkan bagian 26 ini menjadi daftar kerja berikutnya.

### Rekap Status Bagian 26

Status sampai pembaruan terakhir:

| Bagian | Status | Catatan |
| --- | --- | --- |
| 26.1 Ringkasan status perbaikan terakhir | Selesai diverifikasi | Implementasi 25.1 sampai 25.8 sudah dicek tersedia. |
| 26.2 Laporan pengisian mengikuti `tipe_butir` | Selesai diterapkan tahap awal | Laporan generik sudah memisahkan skor skala dan jawaban teks non-skala. |
| 26.3 Laporan tes kinerja admin | Selesai diterapkan tahap awal | Route, controller, view, dan tombol laporan tes kinerja sudah tersedia. |
| 26.4 Kategori kelayakan dari pengaturan | Selesai diterapkan tahap awal | Analisis instrumen dan produk sudah membaca threshold dari `settings`. |
| 26.5 Sinkronisasi `Panduan.md` lama | Selesai diterapkan tahap awal | Bagian 25.1 sampai 25.8 sudah diberi label selesai dan bagian 26 menjadi backlog aktif. |
| 26.6 Penguatan keamanan dan integritas data | Selesai diterapkan tahap awal | Token diperpanjang, index ditambahkan, submit publik dikunci dengan transaksi. |
| 26.7 Pagination, filter, dan export data mentah | Selesai diterapkan tahap awal | Pagination, filter lanjutan, dan export CSV sudah tersedia. |
| 26.8 Uji PDF end-to-end | Belum dikerjakan | Perlu uji manual dari browser dengan data nyata. |
| 26.9 Rapikan route/controller `Analysis` | Selesai diterapkan tahap awal | Route `admin/analysis` dan controller `Analysis` dihapus karena alur analisis sudah ditangani menu Laporan dan Validasi. |
| 26.10 Normalisasi status produk legacy | Belum dikerjakan | Perlu migration normalisasi status produk lama. |
| 26.11 User admin, audit log, backup | Belum dikerjakan | Masuk pengembangan lanjutan setelah alur utama stabil. |

Prioritas aktif setelah pembaruan ini:

```text
26.8 Uji PDF nyata
-> 26.10 Normalisasi status produk legacy
-> 26.11 User admin, audit log, backup
```

### 26.1 Ringkasan Status Perbaikan Terakhir

Status pengerjaan: **Selesai diverifikasi**.

Catatan:

Bagian ini bukan pekerjaan kode baru, tetapi ringkasan kondisi setelah tahap 25.1 sampai 25.8. Hasil pengecekan menunjukkan referensi implementasi utama sudah tersedia di aplikasi.

Status umum:

1. 25.1 status butir `Direvisi` sudah diperlakukan sebagai butir yang dapat digunakan.
2. 25.2 skala penilaian sudah membaca `skala_min` dan `skala_max`.
3. 25.3 `tipe_butir` dan `wajib` sudah dipakai pada form publik dan submit.
4. 25.4 tampilan publik `observasi`, `fgd`, dan `tes_kinerja` sudah dibuat khusus.
5. 25.5 dashboard, hasil pengisian, dan pengaturan minimal sudah tersedia.
6. 25.6 workflow status instrumen dan produk sudah dibuat otomatis melalui service.
7. 25.7 CSRF global, honeypot, validasi link, kuota, email ganda, dan NIM ganda sudah ditambahkan.
8. 25.8 PDF laporan validasi instrumen dan validasi produk sudah ditambahkan memakai Dompdf.

Referensi implementasi:

1. `app/Config/Filters.php` line 77: CSRF global aktif.
2. `app/Controllers/PublicForm.php` line 210: honeypot `website`.
3. `app/Controllers/PublicForm.php` line 258: cegah email ganda.
4. `app/Controllers/PublicForm.php` line 268: cegah NIM ganda.
5. `app/Controllers/PublicForm.php` line 476: validasi link publik.
6. `app/Libraries/WorkflowStatusService.php` line 8: service workflow status.
7. `app/Libraries/PdfService.php` line 8: service PDF Dompdf.
8. `app/Controllers/Admin/ReportPdf.php` line 15: controller PDF laporan.
9. `app/Config/Routes.php` line 58: route PDF validasi instrumen.
10. `app/Config/Routes.php` line 60: route PDF validasi produk.

Checklist verifikasi 26.1:

[x] CSRF global aktif.  
[x] Honeypot form publik tersedia.  
[x] Cegah pengisian ganda berbasis email tersedia.  
[x] Cegah pengisian ganda berbasis NIM tersedia.  
[x] Validasi link publik tersedia.  
[x] Workflow status service tersedia.  
[x] PDF service tersedia.  
[x] Controller PDF laporan tersedia.  
[x] Route PDF validasi instrumen tersedia.  
[x] Route PDF validasi produk tersedia.  

Keputusan:

26.1 dinyatakan selesai. Pekerjaan berikutnya dimulai dari 26.2 karena 26.2 adalah pekerjaan kode pertama yang berdampak pada akurasi laporan.

---

### 26.2 Prioritas Utama: Laporan Pengisian Responden Harus Mengikuti `tipe_butir`

Status pengerjaan: **Selesai diterapkan tahap awal**.

Masalah:

Perbaikan 25.3 membuat butir tidak selalu berupa skor. Ada butir `komentar`, `isian`, `pilihan`, dan `catatan` yang disimpan ke `jawaban_teks`. Namun laporan pengisian responden masih merekap jawaban seolah semua butir adalah skor.

Dampak:

1. Butir non-skala dapat ikut dihitung sebagai jawaban skor kosong atau nol.
2. `jumlah_jawaban` pada laporan responden bisa tidak akurat.
3. Rata-rata skor pada laporan `respon_mahasiswa`, `observasi`, dan `fgd` bisa bias.
4. Jawaban teks dari butir non-skala belum dipisahkan sebagai data kualitatif.

Yang harus dikerjakan:

1. Ubah `getGenericSummary()` agar hanya menghitung jawaban dari butir `tipe_butir = skala`.
2. Ubah `getGenericItemSummary()` agar hanya merekap butir skala.
3. Tambahkan method baru, misalnya `getGenericTextAnswers()`, untuk menampilkan jawaban `jawaban_teks` dari butir non-skala.
4. Perbarui view laporan responden agar punya dua bagian:
   - Rekap skor butir skala.
   - Rekap jawaban teks/catatan untuk butir non-skala.
5. Pastikan laporan `observasi` dan `fgd` tidak hanya memakai istilah angket mahasiswa.

Referensi PHP:

1. `app/Controllers/Admin/Reports.php` line 251: `genericPengisianReport()`.
2. `app/Controllers/Admin/Reports.php` line 263: pemanggilan `getGenericSummary()`.
3. `app/Controllers/Admin/Reports.php` line 264: pemanggilan `getGenericItemSummary()`.
4. `app/Controllers/Admin/Reports.php` line 309: method `getGenericSummary()`.
5. `app/Controllers/Admin/Reports.php` line 349: method `getGenericItemSummary()`.
6. `app/Controllers/Admin/Reports.php` line 356: `COUNT(response_answers.id)` masih menghitung semua jawaban.
7. `app/Controllers/Admin/Reports.php` line 358: `AVG(response_answers.skor)` masih berbasis skor tanpa filter tipe butir.
8. `app/Views/admin/reports/respon_mahasiswa.php` line 40: jumlah jawaban ditampilkan dari summary.
9. `app/Views/admin/reports/respon_mahasiswa.php` line 103: jumlah jawaban per butir ditampilkan.
10. `app/Views/admin/reports/observasi.php` line 1: masih memanggil template laporan respon mahasiswa.
11. `app/Views/admin/reports/fgd.php` line 1: masih memanggil template laporan respon mahasiswa.

Checklist 26.2:

[x] Summary laporan responden hanya menghitung butir skala.  
[x] Rekap per butir hanya menghitung butir skala.  
[x] Jawaban teks butir non-skala tampil di laporan.  
[x] Laporan observasi memakai istilah observasi.  
[x] Laporan FGD memakai istilah FGD.  
[x] Data skor dan data kualitatif tidak tercampur.  

Catatan implementasi:

1. `Reports::getGenericSummary()` sudah difilter ke `instrument_items.tipe_butir = skala`.
2. `Reports::getGenericItemSummary()` sudah menghitung `COUNT(response_answers.skor)` agar hanya skor yang valid dihitung.
3. `Reports::getGenericTextAnswers()` ditambahkan untuk mengambil jawaban teks dari butir non-skala.
4. `respon_mahasiswa.php` menampilkan bagian `Jawaban Teks dan Catatan`.
5. `observasi.php` dan `fgd.php` masih memakai layout laporan bersama, tetapi sekarang mengirim label khusus sesuai konteks.

---

### 26.3 Laporan Tes Kinerja Admin Belum Tersedia

Status pengerjaan: **Selesai diterapkan tahap awal**.

Masalah:

Form publik `tes_kinerja` sudah tersedia, data hasil pengisian juga sudah masuk ke menu Hasil Pengisian, tetapi menu Laporan belum memiliki halaman laporan khusus untuk mode `tes_kinerja`.

Dampak:

1. Admin bisa menerima data tes kinerja, tetapi belum bisa melihat laporan ringkas dari menu Laporan.
2. Pada daftar laporan responden, mode `tes_kinerja` masih ditandai belum tersedia.

Yang harus dikerjakan:

1. Tambahkan route laporan tes kinerja.
2. Tambahkan method `tesKinerja()` di `Reports.php`.
3. Buat view `app/Views/admin/reports/tes_kinerja.php`.
4. Tambahkan tombol laporan untuk mode `tes_kinerja` di index laporan.
5. Gunakan istilah khusus:
   - Identitas peserta/mahasiswa.
   - Penilai.
   - Rubrik/kriteria kinerja.
   - Catatan penilai.
6. Pastikan laporan tes kinerja juga mengikuti aturan 26.2, yaitu skor hanya dari butir skala dan jawaban teks ditampilkan terpisah.

Referensi PHP:

1. `app/Controllers/PublicForm.php` line 168: public view `tes_kinerja` sudah dipanggil.
2. `app/Controllers/PublicForm.php` line 225: mode `tes_kinerja` sudah boleh submit.
3. `app/Controllers/Admin/SubmissionResults.php` line 20: mode `tes_kinerja` sudah masuk hasil pengisian.
4. `app/Controllers/Admin/Reports.php` line 65: mode `tes_kinerja` sudah masuk daftar link laporan.
5. `app/Config/Routes.php` line 53: route laporan respon mahasiswa ada.
6. `app/Config/Routes.php` line 54: route laporan observasi ada.
7. `app/Config/Routes.php` line 55: route laporan FGD ada.
8. `app/Views/admin/reports/index.php` line 150: mode selain respon mahasiswa, observasi, dan FGD masih tampil `Belum tersedia`.

Checklist 26.3:

[x] Route `admin/reports/tes-kinerja/(:num)` dibuat.  
[x] Method `Reports::tesKinerja()` dibuat.  
[x] View `admin/reports/tes_kinerja.php` dibuat.  
[x] Tombol laporan tes kinerja tampil di index laporan.  
[x] Laporan tes kinerja menampilkan skor dan catatan penilai dengan benar.  

Catatan implementasi:

1. Route laporan tes kinerja ditambahkan pada group admin.
2. `Reports::tesKinerja()` memakai `genericPengisianReport()` dengan mode `tes_kinerja`.
3. View `tes_kinerja.php` memakai layout laporan responden yang sudah diperbaiki pada 26.2.
4. Label laporan disesuaikan menjadi peserta/mahasiswa, penilai, rubrik/kriteria kinerja, dan catatan penilaian.
5. Karena memakai alur 26.2, skor tetap hanya dihitung dari butir skala dan jawaban teks non-skala tampil terpisah.

---

### 26.4 Kategori Kelayakan di Pengaturan Belum Dipakai oleh Analisis

Status pengerjaan: **Selesai diterapkan tahap awal**.

Masalah:

Menu Pengaturan sudah menyimpan kategori kelayakan, tetapi proses analisis masih memakai angka tetap `85`, `70`, dan `55` di controller.

Dampak:

1. Admin dapat mengubah kategori pada menu Pengaturan, tetapi hasil analisis belum berubah mengikuti pengaturan tersebut.
2. Ada risiko perbedaan antara konfigurasi yang terlihat di UI dan perhitungan yang benar-benar dipakai sistem.

Yang harus dikerjakan:

1. Buat helper/service kategori, misalnya `CategorySettingService`.
2. Service membaca nilai:
   - `kategori_sangat_layak_min`
   - `kategori_layak_min`
   - `kategori_kurang_layak_min`
   - `kategori_tidak_layak_min`
3. Ubah `InstrumentValidation.php` agar kategori keseluruhan, aspek, dan butir membaca threshold dari settings.
4. Ubah `ProductValidation.php` agar kategori keseluruhan, aspek, dan butir membaca threshold dari settings.
5. Tambahkan fallback jika setting belum ada:
   - Sangat layak minimal 85
   - Layak minimal 70
   - Kurang layak minimal 55
   - Tidak layak minimal 0

Referensi PHP:

1. `app/Controllers/Admin/Settings.php` line 66: method `saveCategory()`.
2. `app/Models/SettingModel.php` line 49: method `getGroupValues()`.
3. `app/Views/admin/settings/index.php` line 112: default sangat layak 85.
4. `app/Views/admin/settings/index.php` line 125: default layak 70.
5. `app/Views/admin/settings/index.php` line 140: default kurang layak 55.
6. `app/Controllers/Admin/InstrumentValidation.php` line 452: kategori validasi instrumen masih hardcoded.
7. `app/Controllers/Admin/ProductValidation.php` line 619: kategori validasi produk masih hardcoded.

Checklist 26.4:

[x] Service/helper kategori dibuat.  
[x] Analisis instrumen membaca threshold dari settings.  
[x] Analisis produk membaca threshold dari settings.  
[x] Fallback threshold tetap tersedia.  
[x] Hasil analisis berubah sesuai pengaturan admin.  

Catatan implementasi:

1. `CategorySettingService` dibuat untuk membaca threshold dari tabel `settings`.
2. Service membaca grup `category` melalui `SettingModel::getGroupValues()`.
3. Fallback tetap tersedia:
   - Sangat layak minimal 85
   - Layak minimal 70
   - Kurang layak minimal 55
   - Tidak layak minimal 0
4. Threshold dinormalisasi agar tetap berada pada rentang 0 sampai 100.
5. `InstrumentValidation.php` memakai service kategori untuk:
   - kategori hasil validasi instrumen,
   - kategori aspek,
   - kategori butir,
   - rekomendasi butir.
6. `ProductValidation.php` memakai service kategori untuk:
   - kategori hasil validasi produk,
   - kategori aspek,
   - kategori butir,
   - rekomendasi butir.

---

### 26.5 Sinkronisasi `Panduan.md` dengan Implementasi Terbaru

Status pengerjaan: **Selesai diterapkan tahap awal**.

Masalah:

Catatan 25.1 sampai 25.8 masih berbentuk daftar masalah awal. Setelah perbaikan dilakukan, dokumen perlu diberi status agar tidak membingungkan saat dicek ulang.

Dampak:

1. Pengembang bisa mengira 25.1 sampai 25.8 masih belum dikerjakan.
2. Catatan pengembangan tidak mencerminkan kondisi aplikasi terbaru.
3. Prioritas berikutnya bisa tercampur dengan prioritas lama.

Yang harus dikerjakan:

1. Tambahkan label `Status: Selesai diterapkan` pada 25.1 sampai 25.8.
2. Tambahkan ringkasan file yang sudah diubah pada tiap subbagian.
3. Pindahkan sisa pekerjaan ke bagian 26.
4. Pastikan bagian 25 tetap menjadi riwayat, bukan backlog aktif.

Referensi dokumen:

1. `Panduan.md` line 1489: awal bagian 25.1.
2. `Panduan.md` line 1518: 25.2 skala penilaian.
3. `Panduan.md` line 1553: 25.3 tipe butir.
4. `Panduan.md` line 1590: 25.4 mode responden.
5. `Panduan.md` line 1622: 25.5 dashboard dan pengaturan.
6. `Panduan.md` line 1661: 25.6 workflow status.
7. `Panduan.md` line 1694: 25.7 keamanan input publik.
8. `Panduan.md` line 1734: 25.8 PDF.

Checklist 26.5:

[x] Subbagian 25.1 sampai 25.8 diberi status selesai.  
[x] Sisa pekerjaan dipindah ke bagian 26.  
[x] Referensi file dan line diperbarui.  

Catatan implementasi:

1. Bagian 25 sekarang menjadi riwayat temuan awal setelah Tahap 1 sampai 16.
2. Subbagian 25.1 sampai 25.8 sudah diberi label `Status: Selesai diterapkan`.
3. Tiap subbagian 25.1 sampai 25.8 sudah diberi ringkasan implementasi dan file utama yang berubah.
4. Pekerjaan aktif berikutnya dipusatkan di bagian 26, terutama 26.8 sampai 26.11.
5. Referensi line pada bagian 26 tetap bersifat panduan cepat dan dapat bergeser jika file terus diperbarui.

---

### 26.6 Penguatan Keamanan dan Integritas Data Tahap Lanjut

Status pengerjaan: **Selesai diterapkan tahap awal**.

Masalah:

Perbaikan 25.7 sudah menambahkan CSRF, honeypot, kuota, dan pencegahan email/NIM ganda pada level aplikasi. Namun proteksi tersebut belum diperkuat pada level database dan race condition.

Dampak:

1. Dua submit sangat berdekatan masih berpotensi melewati kuota sebelum data tersimpan.
2. Pencegahan email/NIM ganda masih bergantung pada pengecekan aplikasi.
3. Query hasil pengisian akan makin berat jika data bertambah banyak.
4. Token link publik masih 16 karakter hex dari `random_bytes(8)`.

Yang harus dikerjakan:

1. Perpanjang token publik menjadi `bin2hex(random_bytes(16))`.
2. Tambahkan index database untuk kolom yang sering dipakai:
   - `responses.instrument_link_id`
   - `responses.mode`
   - `response_answers.response_id`
   - `response_answers.instrument_item_id`
   - `respondents.instrument_link_id`
   - `respondents.email`
   - `respondents.nim`
3. Pertimbangkan unique constraint terkontrol untuk kombinasi link + email dan link + NIM.
4. Bungkus validasi kuota dan penyimpanan response dalam transaksi yang lebih ketat.
5. Pertimbangkan penyimpanan `ip_address` dan `user_agent` jika nanti diperlukan untuk audit.

Referensi PHP:

1. `app/Controllers/Admin/InstrumentLinks.php` line 207: token link validasi instrumen masih `random_bytes(8)`.
2. `app/Controllers/Admin/RespondentLinks.php` line 286: token link responden masih `random_bytes(8)`.
3. `app/Controllers/Admin/ProductValidation.php` line 578: token link validasi produk masih `random_bytes(8)`.
4. `app/Controllers/PublicForm.php` line 518: validasi `maksimal_respon`.
5. `app/Models/RespondentModel.php` line 30: cek email ganda.
6. `app/Models/RespondentModel.php` line 43: cek NIM ganda.
7. `app/Database/Migrations/2026-05-07-000008_CreateInstrumentLinksTable.php` line 76: token sudah unique.
8. `app/Database/Migrations/2026-05-07-000009_CreateRespondentsTable.php` line 82: FK respondent ke link sudah ada.

Checklist 26.6:

[x] Token publik diperpanjang menjadi 32 karakter hex.  
[x] Index query utama ditambahkan lewat migration baru.  
[x] Proteksi submit ganda diperkuat.  
[ ] Kuota link diuji dengan beberapa submit berdekatan.  

Catatan implementasi:

1. Token baru pada link validasi instrumen, link responden, dan link validasi produk sudah memakai `bin2hex(random_bytes(16))`.
2. Migration `AddSubmissionIntegrityIndexes` dibuat dan sudah dijalankan.
3. Index yang ditambahkan:
   - `idx_responses_link_mode` pada `responses (instrument_link_id, mode)`.
   - `idx_response_answers_response_item` pada `response_answers (response_id, instrument_item_id)`.
   - `idx_respondents_link_email` pada `respondents (instrument_link_id, email)`.
   - `idx_respondents_link_nim` pada `respondents (instrument_link_id, nim)`.
4. Submit publik sekarang mengunci row `instrument_links` dengan `FOR UPDATE` saat transaksi penyimpanan.
5. Di dalam transaksi, sistem mengecek ulang:
   - status link,
   - tanggal mulai,
   - tanggal selesai,
   - kuota maksimal respon,
   - email ganda,
   - NIM ganda untuk `respon_mahasiswa` dan `tes_kinerja`.
6. Unique constraint kombinasi link + email/NIM belum diterapkan karena email dan NIM masih opsional. Untuk tahap ini, penguatan dilakukan lewat index, lock transaksi, dan pengecekan ulang di dalam transaksi.
7. Uji manual beberapa submit berdekatan masih perlu dilakukan dari browser atau alat load test sederhana.

---

### 26.7 Pagination, Filter Lanjutan, dan Export Data Mentah

Status pengerjaan: **Selesai diterapkan tahap awal**.

Masalah:

Menu Hasil Pengisian sudah berfungsi, tetapi masih mengambil semua data dengan `findAll()`. Jika data responden banyak, halaman dapat menjadi berat. Selain itu, admin belum bisa export data mentah.

Dampak:

1. Halaman hasil pengisian akan lambat jika response bertambah.
2. Admin sulit mengolah data lanjutan di Excel/SPSS/JASP/R.
3. Filter baru hanya berdasarkan mode, belum berdasarkan instrumen, link, produk, dan tanggal.

Yang harus dikerjakan:

1. Tambahkan pagination pada Hasil Pengisian.
2. Tambahkan filter:
   - Mode
   - Instrumen
   - Link pengisian
   - Produk
   - Tanggal submit
3. Tambahkan export CSV atau Excel untuk data mentah.
4. Export harus memuat:
   - Identitas responden.
   - Mode.
   - Instrumen.
   - Nomor butir.
   - Tipe butir.
   - Skor.
   - Jawaban teks.
   - Komentar.
   - Komentar umum.

Referensi PHP:

1. `app/Controllers/Admin/SubmissionResults.php` line 31: filter mode.
2. `app/Controllers/Admin/SubmissionResults.php` line 72: detail jawaban memakai `findAll()`.
3. `app/Controllers/Admin/SubmissionResults.php` line 105: method `getResponses()`.
4. `app/Controllers/Admin/SubmissionResults.php` line 135: list hasil pengisian memakai `findAll()`.
5. `app/Config/Routes.php` line 63: route index submissions.
6. `app/Config/Routes.php` line 64: route detail submissions.

Checklist 26.7:

[x] Pagination hasil pengisian dibuat.  
[x] Filter instrumen/link/tanggal dibuat.  
[x] Export CSV data mentah dibuat.  
[x] Export memisahkan skor dan jawaban teks.  

Catatan implementasi:

1. Menu Hasil Pengisian sekarang memakai pagination dengan 20 data per halaman.
2. Filter yang tersedia:
   - Mode.
   - Instrumen.
   - Link pengisian.
   - Produk.
   - Tanggal submit dari.
   - Tanggal submit sampai.
3. Route export CSV ditambahkan pada `admin/submissions/export`.
4. Export CSV memakai filter yang sama dengan halaman Hasil Pengisian.
5. Export CSV memuat:
   - Identitas responden.
   - Mode.
   - Status response.
   - Waktu submit.
   - Instrumen.
   - Produk.
   - Link pengisian.
   - Aspek.
   - Nomor butir.
   - Tipe butir.
   - Wajib.
   - Pernyataan.
   - Skor.
   - Jawaban teks.
   - Komentar butir.
   - Komentar umum.
   - Kesimpulan.
6. Skor dan jawaban teks berada pada kolom terpisah agar data mentah aman untuk analisis lanjutan.

---

### 26.8 Uji PDF End-to-End dan Penyesuaian Layout

Masalah:

Fitur PDF sudah ditambahkan, tetapi perlu uji manual dengan data nyata. Dompdf tidak selalu sama dengan browser dalam membaca CSS.

Dampak:

1. Tabel panjang bisa melebar keluar halaman.
2. Beberapa styling cetak bisa terlihat berbeda di PDF.
3. PDF bisa perlu orientasi landscape untuk laporan tertentu.

Yang harus dikerjakan:

1. Buka laporan validasi instrumen dengan data nyata.
2. Klik `Preview PDF`.
3. Klik `Unduh PDF`.
4. Ulangi untuk validasi produk.
5. Periksa:
   - Kop laporan.
   - Tabel responden.
   - Analisis aspek.
   - Analisis butir.
   - Komentar.
   - Riwayat revisi.
6. Jika tabel melebar, kecilkan font tabel atau gunakan landscape pada PDF tertentu.

Referensi PHP:

1. `app/Libraries/PdfService.php` line 10: method `render()`.
2. `app/Libraries/PdfService.php` line 17: method `preview()`.
3. `app/Controllers/Admin/ReportPdf.php` line 37: PDF validasi instrumen.
4. `app/Controllers/Admin/ReportPdf.php` line 52: preview PDF validasi instrumen.
5. `app/Controllers/Admin/ReportPdf.php` line 66: PDF validasi produk.
6. `app/Controllers/Admin/ReportPdf.php` line 80: preview PDF validasi produk.
7. `app/Views/admin/reports/print_validasi_instrumen.php` line 23: background khusus PDF.
8. `app/Views/admin/reports/print_validasi_produk.php` line 23: background khusus PDF.
9. `app/Views/admin/reports/validasi_instrumen.php` line 258: tombol preview PDF.
10. `app/Views/admin/reports/validasi_produk.php` line 239: tombol preview PDF.

Checklist 26.8:

[ ] Preview PDF validasi instrumen diuji.  
[ ] Unduh PDF validasi instrumen diuji.  
[ ] Preview PDF validasi produk diuji.  
[ ] Unduh PDF validasi produk diuji.  
[ ] Layout tabel PDF aman.  
[ ] Jika perlu, orientasi landscape ditambahkan.  

---

### 26.9 Rapikan Route dan Controller `Analysis`

Status pengerjaan: **Selesai diterapkan tahap awal**.

Keputusan:

Route `admin/analysis` tidak dipertahankan. Alur analisis sudah jelas melalui:

1. Menu Validasi Instrumen untuk proses dan hasil analisis instrumen.
2. Menu Validasi Produk untuk proses dan hasil analisis produk.
3. Menu Laporan untuk membuka laporan analisis dan laporan pengisian.

Masalah:

Masih ada route `admin/analysis`, tetapi controller `Analysis` hanya menampilkan ulang view hasil validasi instrumen dengan `links` kosong. Menu sidebar juga tidak lagi memakai route ini secara langsung.

Dampak:

1. Route `admin/analysis` berpotensi membingungkan.
2. Modul analisis dan laporan bisa terasa duplikatif dengan menu Laporan dan Validasi.

Yang harus dikerjakan:

1. Putuskan apakah route `admin/analysis` masih diperlukan.
2. Jika tidak diperlukan, hapus route dan controller.
3. Jika masih diperlukan, ubah menjadi dashboard analisis yang jelas:
   - Analisis validasi instrumen.
   - Analisis validasi produk.
   - Analisis respon mahasiswa.
   - Analisis observasi.
   - Analisis FGD.
   - Analisis tes kinerja.

Referensi PHP:

1. `app/Config/Routes.php` line 71: route `admin/analysis` sebelum perbaikan.
2. `app/Controllers/Admin/Analysis.php` line 8: controller `Analysis` sebelum perbaikan.
3. `app/Controllers/Admin/Analysis.php` line 36: sebelum perbaikan memakai view `admin/validations/instrument_result`.
4. `app/Controllers/Admin/Analysis.php` line 38: sebelum perbaikan `links` dikirim sebagai array kosong.

Checklist 26.9:

[x] Keputusan route `admin/analysis` dibuat.  
[x] Route dan controller yang tidak dipakai dihapus.  
[x] View khusus analisis tidak dibuat karena route diputuskan dihapus.  

Catatan implementasi:

1. Route `$routes->get('analysis', 'Admin\Analysis::index')` dihapus dari `app/Config/Routes.php`.
2. File `app/Controllers/Admin/Analysis.php` dihapus.
3. View khusus analisis tidak dibuat karena keputusan akhirnya adalah menghapus route duplikatif, bukan mempertahankannya.
4. Menu sidebar tetap aman karena tidak lagi mengarah ke `admin/analysis`.
5. Daftar struktur folder dan route awal pada dokumen ini diperbarui agar tidak lagi menyebut `Analysis.php` sebagai controller aktif.

---

### 26.10 Normalisasi Status Produk Legacy

Masalah:

Workflow status produk baru memakai status seperti `Draft`, `Aktif`, `Dalam Validasi Produk`, `Perlu Revisi`, dan `Layak`. Namun migration awal produk masih memberi default `Draft Produk`. Service workflow sudah menoleransi status lama, tetapi database baru masih akan memakai default lama.

Dampak:

1. Status produk baru dan lama bisa bercampur.
2. Filter dan laporan status produk bisa kurang konsisten.
3. Admin bisa melihat status lama yang tidak ada di opsi utama.

Yang harus dikerjakan:

1. Buat migration normalisasi status produk.
2. Ubah default status produk dari `Draft Produk` menjadi `Draft` untuk instalasi berikutnya.
3. Update data lama:
   - `Draft Produk` -> `Draft`
   - `Siap Divalidasi` -> `Aktif`
   - `Sedang Divalidasi` -> `Dalam Validasi Produk`
4. Pastikan form produk tetap bisa menampilkan status lama jika ada data legacy.

Referensi PHP:

1. `app/Database/Migrations/2026-05-07-000006_CreateResearchProductsTable.php` line 47: default status masih `Draft Produk`.
2. `app/Libraries/WorkflowStatusService.php` line 92: service masih menoleransi `Draft Produk`.
3. `app/Libraries/WorkflowStatusService.php` line 107: validasi produk masih menoleransi status legacy.
4. `app/Libraries/WorkflowStatusService.php` line 121: revisi produk masih menoleransi status legacy.
5. `app/Views/admin/products/form.php` line 131: opsi status produk baru.

Checklist 26.10:

[x] Migration normalisasi status produk dibuat.  
[x] Default status produk baru menjadi `Draft`.  
[x] Status legacy pada data lama diperbarui.  
[x] Workflow status tetap aman untuk data lama.  

---

### 26.11 Pengembangan Lanjutan: User Admin, Audit Log, dan Backup

Bagian ini bukan prioritas paling dekat, tetapi penting sebelum aplikasi dipakai lebih luas.

Yang perlu ditambahkan:

1. Manajemen user admin:
   - Tambah admin.
   - Ubah password.
   - Nonaktifkan admin.
2. Audit log:
   - Login admin.
   - Buat/edit/hapus instrumen.
   - Buat link publik.
   - Submit publik.
   - Proses analisis.
   - Revisi butir.
   - Hapus hasil pengisian.
3. Backup dan restore:
   - Export database.
   - Export file produk.
   - Dokumentasi backup manual.

Referensi PHP:

1. `app/Views/admin/settings/index.php` line 167: user admin masih berupa catatan sementara.
2. `app/Controllers/Admin/SubmissionResults.php` line 83: hasil pengisian sudah bisa dihapus, tetapi belum ada audit log.
3. `app/Controllers/Admin/InstrumentValidation.php` line 406: penetapan instrumen valid perlu audit log.

Checklist 26.11:

[x] Modul user admin dirancang.  
[x] Audit log database dirancang.  
[x] Backup data dirancang.  

---

## 27. Urutan Kerja yang Disarankan

Urutan paling aman setelah pembaruan terakhir:

1. Kerjakan 26.8: uji PDF end-to-end dengan data nyata.
2. Kerjakan 26.10: normalisasi status produk legacy.
3. Kerjakan 26.11: user admin, audit log, dan backup.

Pekerjaan yang sudah selesai tahap awal:

1. 26.1 ringkasan status perbaikan terakhir.
2. 26.2 laporan pengisian responden mengikuti `tipe_butir`.
3. 26.3 laporan tes kinerja admin.
4. 26.4 kategori kelayakan membaca pengaturan.
5. 26.5 sinkronisasi dokumentasi lama.
6. 26.6 penguatan keamanan dan integritas data.
7. 26.7 pagination, filter lanjutan, dan export data mentah.
8. 26.9 rapikan route dan controller `Analysis`.

Prioritas teknis terdekat:

```text
26.8 Uji PDF nyata
-> 26.10 Normalisasi status produk legacy
-> 26.11 User admin, audit log, backup
```

Kesimpulan:

SIVALID sudah melewati tahap fondasi utama dan beberapa penguatan tahap 26 sudah selesai diterapkan. Bagian berikutnya lebih banyak berupa uji manual PDF, normalisasi data lama, dan penguatan operasional sebelum aplikasi dipakai lebih luas.


⚠️ Temuan & Rekomendasi
1. CLASS ALERT — Tidak Konsisten
Masalah: Mayoritas view admin masih memakai alert-error (bukan nama class Tabler). Hanya settings/index.php yang sudah pakai alert-danger.

File	Class yang dipakai	Seharusnya
instruments/form.php	alert-error	alert-danger
admin_users/form.php	alert-error	alert-danger
aspects/index.php, aspects/form.php	alert-error	alert-danger
respondent_links/index.php, form.php	alert-error	alert-danger
links/index.php, form.php	alert-error	alert-danger
items/index.php	alert-error	alert-danger
indicators/index.php	alert-error	alert-danger
submissions/index.php	alert-error	alert-danger
validations/product_result.php, instrument_result.php	alert-error	alert-danger
Dampak: alert-error tidak ada di Tabler, hanya ada di sivalid.css. Warna muncul tapi tidak dismissible dan tidak pakai ikon Tabler.

Rekomendasi: Ganti semua alert-error → alert-danger di file-file di atas.

2. PAGE HEADER — Tidak Konsisten
Masalah: settings/index.php sudah pakai struktur Tabler <div class="page-header d-print-none"> lengkap dengan subtitle. Semua halaman lain masih pakai <h1 class="page-title"> mentah tanpa wrapper.

File	Pola saat ini
settings/index.php	<div class="page-header"> + subtitle ✅
instruments/form.php, products/form.php	<h1 class="page-title"> + breadcrumb manual
instruments/index.php, products/index.php, submissions/index.php	<h1 class="page-title mb-1"> dalam wrapper sendiri
admin_users/index.php, links/index.php, dll.	<h1 class="page-title"> mentah tanpa wrapper
Rekomendasi: Standarisasi semua menggunakan pola:


<div class="page-header d-print-none mb-3">  <div class="row align-items-center">    <div class="col">      <h2 class="page-title">Judul</h2>    </div>  </div></div>
3. CARD — Tidak Konsisten
Masalah: File yang sudah di-redesign (settings, instruments, products, submissions, reports) menggunakan <div class="card"> <div class="card-header"> <div class="card-body"> (Tabler native). File yang belum di-redesign hanya menggunakan <div class="card"> tanpa card-header/card-body.

File	Pola card
settings/index.php	card + card-header + card-body ✅
instruments/form.php, products/form.php	card + judul inline <h3>
admin_users/form.php, aspects/form.php	<div class="card"> polos
respondent_links/index.php, form.php	<div class="card"> polos
items/form.php, aspects/index.php, indicators/index.php	<div class="card"> polos
validations/*.php	<div class="card"> polos
Rekomendasi: Tambah card-header + card-body pada file-file di atas.

4. FORM LABEL — Tidak Konsisten
Masalah: settings/index.php sudah menggunakan class="form-label" (Tabler). Semua file form lain memakai class kosong atau label via .form-row label dari sivalid.css.

File	Label pattern
settings/index.php	<label class="form-label"> ✅
instruments/form.php, products/form.php	<label> tanpa class (via .form-row label)
admin_users/form.php, links/form.php, items/form.php	<label> tanpa class
respondent_links/form.php	<label> tanpa class
Dampak: Secara visual aman karena sivalid.css menangani .form-row label. Namun tidak konsisten jika Tabler utilities di-override.

Rekomendasi: Tambahkan class="form-label" pada semua <label> dalam form untuk konsistensi.

5. BADGE STATUS — Dua Sistem Berdampingan
Masalah: Dua sistem badge berbeda dipakai secara bersamaan:

Sistem A (sivalid.css): badge badge-status-success, badge-valid, badge-warning, badge-danger — dipakai di dashboard.php, instruments/index.php, instruments/show.php, submissions/index.php
Sistem B (Tabler native): badge bg-success-lt text-success — hanya dipakai di settings/index.php
Dampak: Tidak ada masalah fungsional, tapi inkonsisten visual. Sistem A sudah ter-define di sivalid.css dan bekerja dengan baik.

Rekomendasi: Pilih satu sistem. Sistem A sudah lebih matang — gunakan konsisten. Ubah settings/index.php agar pakai badge-status-* alih-alih Tabler native, atau sebaliknya standarisasi semua ke Tabler native.

6. PUBLIC FORM — Dua Halaman Belum Di-redesign
Masalah: Dua public form masih menggunakan CSS inline lama (bukan token pub-*):

File	Status
validasi_instrumen.php	✅ Sudah redesign
observasi.php	✅ Sudah redesign
fgd.php	✅ Sudah redesign
tes_kinerja.php	✅ Sudah redesign
validasi_produk.php	⚠️ Masih CSS inline lama (Arial, .container, .muted)
respon_mahasiswa.php	⚠️ Masih CSS inline lama (minified CSS, .btn custom tanpa Tabler)
thanks.php	⚠️ Masih CSS inline lama (Arial, tanpa Tabler)
Dampak pada submit: Form validasi_produk.php dan respon_mahasiswa.php masih bisa submit karena csrf_field() dan honeypot hadir. Tidak ada breaking change fungsional.

7. TABEL — Tidak Konsisten
Masalah:

respondent_links/index.php menggunakan <table> mentah tanpa class
aspects/index.php, items/index.php, indicators/index.php menggunakan <table> mentah
File yang sudah redesign (instruments/index.php, products/index.php, submissions/index.php) sudah pakai <table class="table table-vcenter"> Tabler
Rekomendasi: Tambah class="table table-vcenter table-hover" pada semua tabel di file-file yang belum.

8. TOMBOL — Satu Inkonsistensi Minor
Masalah: Hanya settings/index.php yang memakai btn-outline-secondary (Tabler native). Semua file lain pakai btn-light untuk tombol sekunder. Ini bukan masalah kritis karena btn-light sudah di-define di sivalid.css, tapi inkonsisten.

Prioritas Perbaikan
Prioritas	Item	File yang Terdampak
Tinggi	alert-error → alert-danger	10+ file admin
Tinggi	Public form validasi_produk.php + respon_mahasiswa.php + thanks.php	3 file
Sedang	Standarisasi card dengan card-header/card-body	8+ file admin
Sedang	Standarisasi sistem badge (pilih satu)	settings/index.php vs file lainnya
Sedang	Tambah class="table table-vcenter" di tabel yang belum	respondent_links, aspects, items, indicators
Rendah	Standarisasi page-header wrapper	Semua halaman admin
Rendah	Tambah class="form-label" pada semua label	Semua form admin
