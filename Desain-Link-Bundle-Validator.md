# Desain Fitur: 1 Link Untuk Validasi Banyak Instrumen

## Latar Belakang
Saat ini validasi cenderung dilakukan per link atau per instrumen. Kebutuhan baru: satu validator cukup menerima satu link untuk menilai banyak instrumen sekaligus (contoh: 9 instrumen) dalam satu ruang kerja.

## Tujuan
- Mempermudah distribusi link validasi.
- Mengurangi kebingungan validator karena tidak perlu membuka banyak link.
- Memungkinkan progres bertahap dalam satu sesi kerja.
- Menyediakan rekap hasil per instrumen dan total.
- Mendukung jumlah instrumen dan jumlah validator yang fleksibel.

## Konsep Utama
Satu link publik mewakili satu "paket validasi".
Di dalam paket terdapat beberapa instrumen yang harus dinilai validator.

### Prinsip Fleksibilitas
- Jumlah instrumen per paket tidak tetap (contoh bisa 3, 4, 5, 9, atau lainnya).
- Jumlah validator per paket juga tidak tetap (bisa 1, 2, 3, atau lebih).
- Sistem harus membaca konfigurasi paket, bukan nilai hardcoded.

### Istilah
- Paket Validasi: wadah berisi banyak instrumen.
- Sesi Validator: jejak kerja satu validator pada satu paket.
- Item Penilaian: butir yang dinilai pada tiap instrumen.

## Alur Pengguna (Validator)
1. Validator membuka satu link paket.
2. Validator mengisi identitas sekali di awal.
3. Validator melihat daftar instrumen dalam paket (misal 9 instrumen).
4. Validator menilai instrumen satu per satu.
5. Validator bisa simpan progres.
6. Validator kirim final setelah semua instrumen selesai.
7. Validator mendapat halaman ringkasan hasil kirim.

## Alur Admin
1. Admin membuat paket validasi baru.
2. Admin memilih beberapa instrumen untuk dimasukkan ke paket.
3. Admin menentukan mode paket:
   - Simpan per instrumen (autosave/manual save)
   - Submit final wajib lengkap semua
4. Admin membagikan satu link paket ke validator.
5. Admin memantau progres per validator dan per instrumen.

## Skenario Multi-Validator
- Satu paket dapat dibagikan ke beberapa validator.
- Setiap validator memiliki sesi terpisah pada paket yang sama.
- Jawaban antar validator tidak saling menimpa.
- Status progres dapat dipantau per validator dan per instrumen.

## Kebutuhan Fungsional

### A. Manajemen Paket
- Buat, ubah, aktif/nonaktif paket.
- Pilih banyak instrumen dalam satu paket.
- Atur urutan instrumen.
- Atur batas waktu paket (opsional).

### B. Sesi Validator
- Identitas validator sekali input.
- Lanjutkan progres sebelumnya bila sesi belum final.
- Status tiap instrumen: Belum Mulai, Proses, Selesai.
- Validasi form agar tidak ada butir wajib yang terlewat.

### C. Submit dan Rekap
- Simpan per instrumen.
- Submit final paket.
- Ringkasan hasil total dan per instrumen.
- Kunci edit setelah submit final (opsional: kecuali admin membuka ulang).

### E. Laporan Bertingkat
- Laporan Per Validator: seluruh hasil milik satu validator pada satu paket.
- Laporan Per Instrumen: hasil satu instrumen dari semua validator.
- Laporan Per Paket: agregasi seluruh instrumen dan seluruh validator dalam paket.
- Laporan Komparatif Validator: perbandingan nilai antar validator pada instrumen yang sama.

### D. Keamanan
- Link menggunakan token unik panjang.
- Opsi token sekali pakai atau multi-akses.
- Opsi expired date/time.
- Pembatasan percobaan akses yang gagal.

## Kebutuhan Non-Fungsional
- UI sederhana: daftar instrumen di sisi kiri, form aktif di sisi kanan.
- Responsif mobile dan desktop.
- Autosave aman saat koneksi tidak stabil.
- Jejak audit untuk perubahan dan submit.

## Usulan Struktur Data (Konseptual)

### 1. validation_bundles
- id
- kode_bundle
- judul_bundle
- deskripsi
- token
- status
- starts_at
- expires_at
- created_at
- updated_at

### 2. validation_bundle_instruments
- id
- bundle_id
- instrument_id
- urutan
- created_at

### 3. validation_bundle_sessions
- id
- bundle_id
- validator_nama
- validator_email
- validator_instansi
- status_session (draft/final)
- started_at
- submitted_at
- created_at
- updated_at

### 4. validation_bundle_answers
- id
- session_id
- instrument_id
- item_id
- skor
- catatan
- created_at
- updated_at

Catatan: nama tabel dapat disesuaikan dengan standar penamaan yang sudah dipakai di proyek.

## Usulan UI
- Halaman publik paket:
  - Header: judul paket + deadline
  - Sidebar/list: semua instrumen + status progres
  - Konten utama: form instrumen aktif
  - Footer aksi: Simpan, Lanjut Instrumen Berikutnya, Submit Final

- Halaman admin monitoring:
  - Tabel progres validator
  - Persentase penyelesaian per instrumen
  - Aksi lihat detail jawaban

## Aturan Agregasi (Usulan Awal)
- Nilai akhir per instrumen = rata-rata nilai dari validator yang sudah submit final.
- Nilai akhir paket = rata-rata nilai akhir semua instrumen dalam paket.
- Nilai mentah per validator tetap disimpan untuk audit dan pelacakan keputusan.

## Aturan Bisnis yang Perlu Diputuskan
1. Apakah validator wajib menyelesaikan semua instrumen sebelum submit final? [ ya]
2. Apakah validator boleh kembali mengubah nilai setelah submit final? [ya]
3. Apakah satu link dipakai banyak validator atau satu validator satu token? [1 validator 1 token/link]
4. Apakah bobot tiap instrumen sama atau berbeda? [Kan ada yangpakai skala, ada yang kualitatif]
5. Saat laporan komparatif, apakah ditampilkan selisih nilai dan ranking validator?, [ gselisih nilai ]

## Tahapan Implementasi Disarankan
1. Fase 1: Paket + pilih instrumen + tampilan publik multi-instrumen.
2. Fase 2: Simpan progres per sesi validator.
3. Fase 3: Submit final + rekap admin.
4. Fase 4: Penguatan keamanan token + audit log lengkap.

## Risiko dan Mitigasi
- Risiko: validator bingung saat instrumen banyak.
  - Mitigasi: progress bar, penanda status, tombol next/previous.
- Risiko: data hilang saat koneksi putus.
  - Mitigasi: autosave berkala dan notifikasi status simpan.
- Risiko: token bocor.
  - Mitigasi: token panjang, expiry, dan opsi revoke oleh admin.

## Ringkasan
Kebutuhan "1 link untuk 9 instrumen" sangat layak dan cocok untuk alur kerja validasi. Pendekatan paling aman adalah membangun konsep Paket Validasi dengan sesi validator, progres bertahap, dan submit final terkontrol.
