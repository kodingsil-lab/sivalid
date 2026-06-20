# Rencana Multi-User SIVALID

Tujuan: setiap user/admin memiliki data sendiri, sehingga Master Instrumen, validasi, link responden, dan hasil tidak bercampur antar user.

## Tahap 1 - Audit dan Aturan Akses

- Status: selesai.
- Tetapkan role:
  - `superadmin`: dapat melihat/mengelola semua data.
  - `admin`: hanya melihat/mengelola data miliknya.
- Gunakan `session('user_id')` sebagai pemilik data.
- Tentukan data global:
  - Logo, favicon, nama aplikasi.
  - Jenis instrumen dan jenis produk, untuk tahap awal tetap global.
- Keputusan implementasi awal:
  - Manajemen user dan backup hanya untuk `superadmin`.
  - Akun pertama dipastikan menjadi `superadmin`.
  - Data belum dipisahkan pada tahap ini; pemisahan mulai dikerjakan pada Tahap 2.

## Tahap 2 - Migrasi Database

- Status: selesai.

Tambah kolom `user_id` pada tabel utama:

- `instruments`
- `research_products`
- `instrument_links`
- `validation_bundles`
- `manual_valid_instruments`
- `respondents`
- `responses`

Opsional untuk query lebih mudah:

- `instrument_aspects`
- `instrument_indicators`
- `instrument_items`

Data lama diisi ke user admin utama agar tidak hilang.

Catatan implementasi:

- Kolom `user_id` dibuat nullable dulu agar fitur lama tetap berjalan selama transisi.
- Semua data lama di-backfill ke superadmin aktif pertama, atau user pertama jika belum ada superadmin.
- Kolom opsional pada `instrument_aspects`, `instrument_indicators`, dan `instrument_items` ikut ditambahkan untuk memudahkan query Tahap 3.
- Pada Tahap 3, setiap proses create/update mulai wajib mengisi `user_id` dari `session('user_id')`.

## Tahap 3 - Model dan Helper Ownership

- Status: selesai.
- Buat helper/method untuk mengambil `current_user_id`.
- Buat helper untuk cek `is_superadmin`.
- Tambahkan filter reusable:
  - Jika `superadmin`, boleh semua data.
  - Jika `admin`, wajib `where user_id = session('user_id')`.

Catatan implementasi:

- Helper ownership tersedia di `app/Helpers/access_helper.php`.
- Base controller memiliki method ringkas untuk `currentUserId`, `isSuperadmin`, `applyOwnerScope`, `withOwner`, dan `ownsRow`.
- Model utama yang memiliki kolom `user_id` sudah memakai trait `BelongsToUser`.
- Tahap ini menyiapkan reusable filter; penerapan detail ke list/create/edit/delete dilakukan bertahap mulai Tahap 4.

## Tahap 4 - Master Instrumen

- Status: selesai.
- Saat tambah instrumen, simpan `user_id`.
- List instrumen hanya tampil milik user login.
- Detail/edit/delete wajib cek pemilik data.
- Kode instrumen dibuat unik per user, bukan unik global.
- Generator kode berikutnya dihitung per user.

Catatan implementasi:

- List Master Instrumen memakai ownership scope; `superadmin` tetap bisa melihat semua data.
- Tambah instrumen otomatis mengisi `user_id` dari session login.
- Detail, edit, update, delete, move, dan reorder sudah cek data sesuai ownership.
- Validasi kode instrumen berubah dari unique global menjadi unique per pemilik data.
- Migrasi database mengganti unique index `kode` menjadi unique gabungan `user_id + kode`.

## Tahap 5 - Data Turunan Instrumen

- Status: selesai.

Terapkan ownership pada:

- Kisi-kisi/aspek.
- Indikator.
- Butir instrumen.

Validasi akses dilakukan lewat `instrument_id`, lalu cek pemilik instrumen.

Catatan implementasi:

- List aspek, indikator, dan butir sudah memakai ownership scope.
- Create/update/delete aspek, indikator, dan butir wajib melewati instrumen yang boleh diakses user login.
- Import Excel kisi-kisi dan butir ikut mengisi `user_id` sesuai pemilik instrumen.
- Pilihan instrumen, aspek, dan indikator pada form sudah dibatasi sesuai ownership.
- `superadmin` tetap bisa mengelola semua data; data turunan yang dibuat untuk instrumen user lain mengikuti `user_id` pemilik instrumen tersebut.

## Tahap 6 - Validasi Instrumen

- Status: selesai.

Pisahkan data per user untuk:

- Paket validasi instrumen.
- Instrumen dalam paket.
- Link/token validasi.
- Hasil validasi.
- Instrumen valid.

Semua list dan detail hanya menampilkan data milik user login, kecuali `superadmin`.

Catatan implementasi:

- List dan detail paket validasi memakai ownership dari `validation_bundles.user_id`.
- Saat membuat paket validasi, `user_id` otomatis tersimpan dari session login.
- Instrumen yang bisa dipilih ke paket hanya instrumen yang boleh diakses user login.
- Detail, edit, hapus, duplikat, revoke token, aktivasi token, monitor sesi, dan detail sesi wajib melewati paket yang boleh diakses.
- Hasil validasi difilter lewat pemilik paket validasi.
- Detail/export hasil validasi hanya bisa dibuka jika session berasal dari paket milik user login.
- Daftar Instrumen Valid difilter per user, dan penambahan dari master mengisi `user_id` sesuai pemilik instrumen.
- Halaman publik token tetap tidak memakai ownership session login, karena aksesnya berdasarkan token.

## Tahap 7 - Link Responden dan Hasil Pengisian

- Status: selesai.

- Link responden dibuat dengan `user_id`.
- List link responden difilter per user.
- Hasil pengisian difilter berdasarkan pemilik link/instrumen.
- Halaman publik tetap bisa diakses lewat token tanpa login.

Catatan implementasi:

- Link Responden difilter memakai `instrument_links.user_id`.
- Saat membuat atau mengubah link responden, `user_id` mengikuti pemilik instrumen yang dipilih.
- Instrumen yang bisa dipilih pada Link Responden hanya instrumen valid milik user login.
- Edit dan hapus Link Responden wajib melewati ownership link.
- Hasil Pengisian, detail, delete, dan export difilter memakai `responses.user_id`.
- Dropdown filter Hasil Pengisian untuk instrumen, link, dan produk ikut dibatasi per user.
- Saat responden mengisi lewat halaman publik, data `respondents.user_id` dan `responses.user_id` mengikuti pemilik link.
- Halaman publik tetap bisa diakses lewat token tanpa login dan tidak memakai session admin.

## Tahap 8 - Dashboard

- Status: selesai.

Ubah statistik dashboard agar menghitung data milik user login:

- Total instrumen.
- Instrumen valid.
- Link aktif.
- Respon masuk.
- Riwayat respon terbaru.

`superadmin` boleh melihat total semua data atau diberi filter user.

Catatan implementasi:

- Totala instrumen memakai `instruments.user_id`.
- Instrumen valid memakai `manual_valid_instruments.user_id`.
- Link aktif memakai `instrument_links.user_id`.
- Respon masuk dan ringkasan mode memakai `responses.user_id`.
- Riwayat respon terbaru memakai `responses.user_id`.
- Untuk tahap ini `superadmin` melihat total semua data; filter per user dapat ditambahkan nanti jika dibutuhkan.

## Tahap 9 - Pengaturan User

- Menu user admin tetap di Pengaturan.
- Tambah role `superadmin` dan `admin` bila belum dibedakan.
- Pastikan admin biasa tidak bisa menghapus/mengubah superadmin.
- Admin biasa tidak boleh melihat daftar user jika tidak diperlukan.

## Tahap 10 - Testing

Skenario wajib:

- User A membuat instrumen, User B tidak melihatnya.
- User A membuat paket validasi, User B tidak melihatnya.
- User A membuat link responden, User B tidak melihatnya.
- Hasil pengisian User A tidak muncul di User B.
- Public token tetap berjalan.
- Superadmin bisa melihat semua data.

## Urutan Eksekusi Disarankan

1. Tambah migrasi `user_id`.
2. Isi data lama ke admin utama.
3. Update Master Instrumen.
4. Update kisi-kisi, indikator, butir.
5. Update Paket Validasi.
6. Update Link Responden.
7. Update Hasil dan Dashboard.
8. Update menu User dan role.
9. Testing penuh dengan dua akun admin.
