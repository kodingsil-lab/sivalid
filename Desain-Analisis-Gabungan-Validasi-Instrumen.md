# Desain Analisis Gabungan Validasi Instrumen

Dokumen ini menjelaskan rancangan alur analisis gabungan untuk validasi instrumen yang diisi oleh beberapa validator melalui paket validasi. Fokus utamanya adalah menggabungkan hasil penilaian beberapa validator untuk setiap instrumen, menghitung kelayakan instrumen, mengekspor laporan, memandu revisi butir, dan menetapkan instrumen final/valid.

## 1. Latar Belakang

Saat ini paket validasi instrumen dapat dibuat untuk validator berbeda. Contoh:

- `validator1`
- `validator2`
- `validator3`

Masing-masing validator mengisi paket yang memuat instrumen yang sama, misalnya:

- `INS-001`
- `INS-002`
- `INS-003`

Data jawaban validator tersimpan otomatis melalui tabel bundle:

- `validation_bundles`
- `validation_bundle_instruments`
- `validation_bundle_sessions`
- `validation_bundle_answers`
- `validation_bundle_instrument_progress`

Kebutuhan berikutnya adalah melakukan analisis gabungan dari beberapa paket validator agar peneliti dapat menentukan apakah setiap instrumen sudah valid, perlu revisi, atau belum layak digunakan.

## 2. Tujuan Modul

Modul Analisis Gabungan Validasi Instrumen bertujuan untuk:

1. Menggabungkan jawaban beberapa validator untuk instrumen yang sama.
2. Menghitung validitas butir skala menggunakan Aiken's V.
3. Menyajikan rekap komentar validator untuk dasar revisi.
4. Membedakan analisis instrumen skala dan non-skala.
5. Mengekspor laporan analisis gabungan ke PDF.
6. Menghubungkan hasil analisis dengan revisi butir.
7. Menetapkan instrumen sebagai final/valid setelah revisi selesai.
8. Mengubah status instrumen di Master Instrumen dan menampilkannya di menu Instrumen Valid.

## 3. Posisi Menu

Struktur menu yang disarankan:

```text
Validasi Instrumen
- Paket Validasi Instrumen
- Analisis Gabungan
- Revisi Butir
- Instrumen Valid
```

Alternatif tahap awal:

```text
Validasi Instrumen
- Paket Validasi Instrumen
  - tombol Proses Analisis Gabungan
- Revisi Butir
- Instrumen Valid
```

Rekomendasi: buat menu khusus **Analisis Gabungan** agar riwayat analisis lebih mudah ditemukan dan tidak bercampur dengan daftar paket.

## 4. Alur Kerja Utama

### 4.1 Membuat Paket untuk Validator

Admin membuat atau menduplikasi paket validasi untuk beberapa validator.

Contoh:

```text
Paket Validator 1 -> token validator1
Paket Validator 2 -> token validator2
Paket Validator 3 -> token validator3
```

Setiap paket berisi instrumen yang sama:

```text
INS-001
INS-002
INS-003
...
```

### 4.2 Validator Mengisi Paket

Validator membuka link publik:

```text
/paket/validator1
/paket/validator2
/paket/validator3
```

Validator mengisi:

- skor butir
- komentar per butir
- komentar umum
- kesimpulan

Data tersimpan otomatis ke backend admin tanpa perlu tombol Submit Final.

### 4.3 Admin Membuka Analisis Gabungan

Admin masuk ke:

```text
Validasi Instrumen -> Analisis Gabungan
```

Admin memilih paket validator yang akan digabungkan.

Contoh:

```text
[x] Paket Validator 1
[x] Paket Validator 2
[x] Paket Validator 3
```

Sistem memeriksa apakah paket yang dipilih memiliki instrumen yang cocok.

### 4.4 Sistem Mengelompokkan Data

Sistem mengelompokkan jawaban berdasarkan `instrument_id` atau kode instrumen.

Contoh hasil grouping:

```text
INS-001
- jawaban validator 1
- jawaban validator 2
- jawaban validator 3

INS-002
- jawaban validator 1
- jawaban validator 2
- jawaban validator 3
```

### 4.5 Sistem Menentukan Jenis Analisis

Setiap instrumen dianalisis berdasarkan jenis dan tipe butirnya.

Instrumen skala:

- Angket
- Rubrik skala
- Instrumen dengan butir bertipe `skala`

Analisis: Aiken's V.

Instrumen non-skala:

- Pedoman wawancara
- Pedoman observasi
- FGD
- Pedoman non-skala lain

Analisis: rekap kualitatif, bukan Aiken's V, kecuali instrumen tersebut tetap memiliki butir skala.

## 5. Analisis Aiken's V

### 5.1 Kapan Digunakan

Aiken's V digunakan untuk instrumen yang memiliki skor skala dari validator.

Contoh skala:

```text
1 = Tidak relevan
2 = Kurang relevan
3 = Relevan
4 = Sangat relevan
```

### 5.2 Rumus

```text
V = Σs / [n(c - 1)]
s = r - lo
```

Keterangan:

- `r` = skor dari validator
- `lo` = skor terendah skala
- `c` = jumlah kategori skala
- `n` = jumlah validator
- `Σs` = jumlah seluruh nilai `s`

### 5.3 Contoh Hitung

Skala 1 sampai 4.

```text
lo = 1
c = 4
n = 3 validator
```

Skor butir dari 3 validator:

```text
Validator 1 = 4
Validator 2 = 3
Validator 3 = 4
```

Maka:

```text
s1 = 4 - 1 = 3
s2 = 3 - 1 = 2
s3 = 4 - 1 = 3
Σs = 8

V = 8 / [3(4 - 1)]
V = 8 / 9
V = 0.89
```

Butir tersebut dapat dikategorikan valid jika batas valid adalah `V >= 0.80`.

### 5.4 Ambang Kategori

Ambang awal yang disarankan:

```text
V >= 0.80      = Valid / dipertahankan
0.60 - 0.79    = Perlu revisi kecil
0.40 - 0.59    = Perlu revisi besar
V < 0.40       = Tidak valid / ganti atau hapus
```

Ambang ini sebaiknya dibuat dapat diatur di menu Settings agar fleksibel mengikuti kebijakan penelitian.

## 6. Output Analisis Skala

Untuk instrumen skala, hasil analisis gabungan memuat:

1. Ringkasan instrumen.
2. Daftar validator.
3. Jumlah validator.
4. Jumlah butir skala.
5. Skala minimum dan maksimum.
6. Nilai Aiken's V per butir.
7. Rata-rata Aiken's V per aspek.
8. Rata-rata Aiken's V instrumen.
9. Kategori validitas.
10. Rekomendasi tiap butir.
11. Komentar validator tiap butir.
12. Kesimpulan validator.

Contoh tabel butir:

| No | Aspek | Butir | Skor V1 | Skor V2 | Skor V3 | Aiken's V | Kategori | Rekomendasi |
|---:|---|---|---:|---:|---:|---:|---|---|
| 1 | Pendahuluan | Latar belakang jelas | 4 | 3 | 4 | 0.89 | Valid | Dipertahankan |
| 2 | Pendahuluan | Urgensi model kuat | 3 | 3 | 3 | 0.67 | Revisi kecil | Perbaiki redaksi |

## 7. Analisis Instrumen Non-Skala

Instrumen non-skala tidak dianalisis dengan Aiken's V jika tidak memiliki skor numerik.

Contoh:

- pedoman wawancara
- pedoman observasi
- panduan FGD
- catatan kualitatif

Output yang disarankan:

1. Status kelengkapan penilaian.
2. Rekap komentar per butir.
3. Rekap komentar umum.
4. Kesimpulan validator.
5. Rekomendasi manual:
   - layak digunakan
   - revisi kecil
   - revisi besar
   - tidak layak

Jika instrumen non-skala tetap memiliki butir dengan skor, maka butir skala dapat dihitung Aiken's V, sedangkan butir teks direkap secara kualitatif.

## 8. Analisis Instrumen Checklist Setuju / Tidak Setuju

Beberapa instrumen validasi tidak memakai skala 1-4, tetapi memakai pilihan:

```text
Setuju (S)
Tidak Setuju (TS)
```

Contoh instrumen:

- pedoman observasi
- pedoman wawancara
- lembar validasi instrumen non-angket
- checklist kelayakan aspek

Untuk bentuk seperti ini, analisis yang lebih tepat adalah **persentase kesepakatan validator** atau **proporsi persetujuan**, bukan rata-rata skor skala 1-4.

### 8.1 Data yang Digunakan

Setiap validator memilih salah satu:

```text
S  = setuju bahwa butir/aspek layak atau sesuai
TS = tidak setuju bahwa butir/aspek layak atau sesuai
```

Komentar tetap wajib direkap, terutama ketika validator memilih `TS`.

### 8.2 Rumus Persentase Kesepakatan

```text
Persentase Setuju = (Jumlah validator yang memilih S / Jumlah seluruh validator) x 100%
```

Contoh 3 validator:

```text
Validator 1 = S
Validator 2 = S
Validator 3 = TS

Persentase Setuju = 2 / 3 x 100%
Persentase Setuju = 66.67%
```

### 8.3 Kategori Rekomendasi

Ambang awal yang disarankan:

```text
100%          = Layak / dipertahankan
66.67% - 99%  = Layak dengan revisi kecil
33.33% - 66%  = Perlu revisi besar
< 33.33%      = Tidak layak / ganti atau hapus
```

Untuk 3 validator, hasil biasanya akan jatuh pada pola:

```text
3 S, 0 TS = 100%
2 S, 1 TS = 66.67%
1 S, 2 TS = 33.33%
0 S, 3 TS = 0%
```

Kategori final perlu dikonfirmasi dengan kebijakan penelitian. Jika ingin lebih ketat, maka butir hanya dianggap layak jika semua validator memilih `S`.

### 8.4 Output Analisis Checklist

Tabel analisis checklist disarankan berisi:

| No | Aspek | Fokus/Butir | V1 | V2 | V3 | % Setuju | Kategori | Rekomendasi |
|---:|---|---|---|---|---|---:|---|---|
| 1 | Keterlaksanaan Sintaks | Fokus pengamatan pelaksanaan sintaks | S | S | TS | 66.67% | Revisi kecil | Perjelas fokus pengamatan |

Komentar validator ditampilkan di bawah tabel atau pada kolom detail.

### 8.5 Perbedaan dengan Aiken's V

Analisis checklist S/TS berbeda dari Aiken's V skala 1-4.

```text
Skala 1-4          -> Aiken's V
Checklist S/TS     -> Persentase kesepakatan / proporsi setuju
Teks terbuka       -> Rekap kualitatif
```

Jika suatu instrumen memiliki campuran butir skala dan checklist, sistem perlu menganalisis berdasarkan tipe butir masing-masing.

## 9. Ekspor Laporan Analisis

Setiap hasil analisis gabungan harus dapat diekspor ke PDF.

Tombol yang disarankan:

```text
Preview PDF
Unduh PDF
```

Isi laporan:

1. Sampul atau header laporan.
2. Profil penelitian:
   - nama peneliti
   - NIM
   - program studi
   - perguruan tinggi
   - judul penelitian
3. Daftar validator.
4. Daftar paket yang digabungkan.
5. Instrumen yang dianalisis.
6. Metode analisis:
   - Aiken's V untuk skala
   - rekap kualitatif untuk non-skala
7. Tabel hasil per butir.
8. Tabel hasil per aspek.
9. Ringkasan hasil instrumen.
10. Komentar validator.
11. Rekomendasi revisi.
12. Kesimpulan.

## 10. Revisi Butir Instrumen

Setelah analisis selesai, butir yang perlu revisi masuk ke alur Revisi Butir.

Alur revisi:

1. Admin membuka hasil analisis gabungan.
2. Sistem menandai butir yang rekomendasinya revisi.
3. Admin klik **Buat Revisi** pada butir tertentu.
4. Admin mengisi:
   - redaksi sebelum revisi
   - redaksi setelah revisi
   - alasan revisi
   - sumber revisi
5. Riwayat revisi tersimpan.
6. Master butir dapat diperbarui dengan redaksi final.

Menu yang digunakan:

```text
Validasi Instrumen -> Revisi Butir
```

## 11. Penetapan Instrumen Final / Valid

Setelah revisi selesai, admin dapat menetapkan instrumen sebagai valid.

Tombol yang disarankan pada hasil analisis:

```text
Tetapkan Instrumen Valid
```

Efek sistem:

1. Status instrumen di Master Instrumen berubah menjadi `Valid` atau `Siap Disebar`.
2. Instrumen muncul di menu:

```text
Validasi Instrumen -> Instrumen Valid
```

3. Riwayat analisis dan revisi tetap tersimpan sebagai bukti proses.

## 12. Dampak ke Master Instrumen

Setelah instrumen ditetapkan valid:

- kolom status di Master Instrumen berubah
- instrumen tidak lagi dianggap dalam proses validasi
- instrumen dapat digunakan untuk tahap berikutnya
- instrumen muncul di daftar Instrumen Valid

Status yang disarankan:

```text
Draft
Siap Validasi
Dalam Validasi Instrumen
Perlu Revisi
Valid
Siap Disebar
```

## 13. Rancangan Data Tambahan

Saat ini sistem sudah punya tabel analisis lama:

- `analysis_results`
- `analysis_aspects`
- `analysis_items`

Untuk analisis gabungan bundle, ada dua pilihan.

### Opsi A: Pakai Tabel Analisis Lama

Tambahkan kolom agar bisa membedakan analisis dari link lama dan bundle:

```text
analysis_results
- source_type: link / bundle_group
- bundle_group_id
- instrument_id
- mode
```

Kelebihan:

- lebih sedikit tabel baru
- laporan lama bisa lebih mudah dipakai ulang

Kekurangan:

- struktur lama awalnya dirancang untuk `instrument_link_id`
- perlu hati-hati agar analisis link lama tidak rusak

### Opsi B: Buat Tabel Baru

Tabel baru yang disarankan:

```text
bundle_analysis_groups
- id
- title
- instrument_id
- selected_bundle_ids
- validator_count
- analysis_method
- status
- created_at
- updated_at

bundle_analysis_items
- id
- analysis_group_id
- instrument_item_id
- scores_json
- aiken_v
- category
- recommendation
- comments_json
- created_at
- updated_at

bundle_analysis_aspects
- id
- analysis_group_id
- aspect_id
- average_aiken_v
- category
- created_at
- updated_at
```

Kelebihan:

- lebih jelas untuk analisis gabungan
- tidak mengganggu modul analisis lama
- lebih mudah dikembangkan untuk PDF dan revisi

Kekurangan:

- perlu membuat migrasi dan model baru

Rekomendasi: gunakan **Opsi B** agar modul bundle analysis lebih bersih.

## 14. Validasi Sebelum Proses Analisis

Sebelum analisis diproses, sistem harus memeriksa:

1. Minimal 2 validator dipilih.
2. Paket yang dipilih memiliki instrumen yang sama.
3. Instrumen memiliki butir aktif.
4. Butir skala memiliki skor dari validator.
5. Skala minimum dan maksimum tersedia.
6. Jika ada validator belum lengkap, sistem memberi peringatan.

Opsi kebijakan:

- izinkan analisis walaupun ada butir belum lengkap
- atau wajib semua validator selesai

Rekomendasi awal: izinkan analisis dengan catatan jumlah validator per butir ditampilkan, tetapi beri warning jika data tidak lengkap.

## 15. Desain Halaman

### 14.1 Daftar Analisis Gabungan

Kolom:

- No
- Judul Analisis
- Instrumen
- Jumlah Validator
- Metode
- Status
- Dibuat
- Aksi

Aksi:

- Detail
- Proses Ulang
- Preview PDF
- Unduh PDF

### 14.2 Form Proses Analisis Gabungan

Field:

- nama analisis
- instrumen target
- pilihan paket validator
- metode analisis
- ambang validitas

### 14.3 Detail Hasil Analisis

Bagian:

- ringkasan analisis
- daftar validator
- tabel butir
- tabel aspek
- komentar validator
- rekomendasi revisi
- aksi ekspor laporan
- aksi tetapkan valid

## 16. Tahapan Pengerjaan

Tahap pengerjaan yang disarankan:

### Tahap 1: Fondasi Data

1. Buat migrasi tabel analisis gabungan.
2. Buat model analisis gabungan.
3. Buat service perhitungan Aiken's V.

### Tahap 2: UI Admin

1. Tambah menu Analisis Gabungan.
2. Buat halaman daftar analisis.
3. Buat form proses analisis.
4. Buat halaman detail hasil analisis.

### Tahap 3: Perhitungan

1. Ambil data dari paket yang dipilih.
2. Kelompokkan berdasarkan instrumen.
3. Hitung Aiken's V per butir.
4. Hitung rata-rata aspek.
5. Hitung ringkasan instrumen.
6. Simpan hasil analisis.

### Tahap 4: Laporan PDF

1. Buat view laporan analisis.
2. Buat preview PDF.
3. Buat download PDF.

### Tahap 5: Revisi Butir

1. Hubungkan hasil analisis ke menu revisi.
2. Tambahkan tombol Buat Revisi pada butir yang perlu revisi.
3. Simpan riwayat revisi.
4. Update redaksi butir final bila disetujui.

### Tahap 6: Penetapan Valid

1. Tambahkan tombol Tetapkan Instrumen Valid.
2. Update status instrumen di Master Instrumen.
3. Tampilkan instrumen di menu Instrumen Valid.
4. Catat audit log.

## 17. Pertanyaan Konfirmasi

Beberapa hal yang perlu dikonfirmasi sebelum implementasi:

1. Ambang Aiken's V yang digunakan:
   - apakah `0.80` untuk valid?
   - apakah kategori revisi mengikuti rentang `0.60 - 0.79`?
2. Apakah semua validator wajib selesai sebelum analisis?
3. Apakah analisis gabungan selalu memilih beberapa paket, atau nanti satu paket boleh berisi banyak validator?
4. Apakah instrumen non-skala cukup direkap kualitatif, atau tetap perlu status valid/tidak valid manual?
5. Untuk checklist Setuju/Tidak Setuju, apakah butir dianggap layak hanya jika semua validator memilih Setuju?
6. Apakah laporan PDF memakai format resmi kampus atau format sederhana sistem dulu?
7. Apakah setelah Tetapkan Valid, butir hasil revisi otomatis mengganti redaksi master butir?

## 18. Rekomendasi Keputusan Awal

Rekomendasi awal untuk pengembangan:

1. Buat menu **Analisis Gabungan**.
2. Gunakan analisis berbasis pilihan beberapa paket.
3. Gunakan Aiken's V untuk instrumen skala.
4. Gunakan persentase kesepakatan untuk instrumen checklist Setuju/Tidak Setuju.
5. Gunakan rekap kualitatif untuk instrumen non-skala berbasis teks.
6. Ambang default Aiken's V:

```text
V >= 0.80      = Valid
0.60 - 0.79    = Revisi kecil
0.40 - 0.59    = Revisi besar
V < 0.40       = Tidak valid
```

7. Hasil analisis dapat diekspor PDF.
8. Revisi butir dilakukan setelah analisis.
9. Instrumen masuk ke menu Instrumen Valid setelah admin menetapkan valid.
