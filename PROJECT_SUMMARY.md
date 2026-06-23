# Ringkasan Proyek: Sistem Lelang Negara (Elaborasi Detail)

Proyek ini adalah platform manajemen lelang barang berbasis Laravel, didesain untuk memfasilitasi proses lelang barang secara terstruktur dan transparan, khususnya untuk entitas pemerintah atau badan hukum yang menangani barang sitaan/negara.
## 👥 Peran Pengguna (Roles)

Sistem ini memiliki hierarki peran pengguna yang jelas untuk memisahkan tanggung jawab dan akses:

1.  **Admin Pusat**: 
    *   **Verifikasi Pengajuan Lelang**: Menerima dan meninjau pengajuan lelang dari berbagai Satuan Kerja (Satker). Admin Pusat memiliki wewenang untuk `menyetujui`, `menolak`, atau meminta `revisi` dengan memberikan catatan rinci dalam format JSON.
    *   **Manajemen Pengguna & Satker**: Mengelola daftar pengguna (Admin Pusat, Admin Satker) dan data Satker. Ini termasuk pembuatan, pengeditan, dan penghapusan akun pengguna serta informasi Satker. Admin Pusat juga bertanggung jawab menetapkan `admin_user_id` untuk setiap Satker.
    *   **Pemantauan & Audit Log**: Akses penuh ke `AuditLogs` untuk memantau seluruh aktivitas pengguna dan perubahan data dalam sistem, memastikan transparansi dan akuntabilitas.
    *   **Manajemen Laporan Lelang Nasional**: Mengumpulkan dan mengelola data laporan lelang dari seluruh Satker, memungkinkan agregasi data untuk analisis dan pelaporan tingkat nasional.
2.  **Admin Satker (Satuan Kerja)**:
    *   **Input Data Perkara & Barang**: Bertanggung jawab untuk memasukkan informasi detail mengenai perkara hukum (`Perkara`) dan barang-barang yang akan dilelang (`Barang`), termasuk deskripsi, harga awal, dan catatan internal.
    *   **Pengajuan Permohonan Lelang**: Membuat `PengajuanLelang` yang mengacu pada barang-barang dari suatu perkara. Proses ini melibatkan pengunggahan `DokumenPengajuan` yang relevan.
    *   **Pengelolaan Dokumen**: Mengelola `DokumenPengajuan` untuk permohonan lelang dan `DokumenPerkara` yang berkaitan dengan perkara hukum asal barang.
    *   **Manajemen Foto Barang**: Mengunggah `FotoBarang` untuk setiap barang yang akan dilelang, memberikan visualisasi yang jelas bagi calon pembeli.
3.  **Pembeli**:
    *   **Penawaran Lelang (Bidding)**: Melakukan penawaran pada barang-barang yang sedang dalam periode lelang aktif. Setiap penawaran dicatat dalam `Penawaran` yang mencakup nilai penawaran dan waktu.
    *   **Akses Magic Link**: Sistem otentikasi khusus menggunakan `Magic Link`. Ini memungkinkan pembeli untuk masuk tanpa kata sandi tradisional, yang meningkatkan keamanan dan kemudahan penggunaan. Setiap tautan memiliki `magic_token` dan `token_expired_at` untuk validasi.
## 🛠 Fitur Utama

Detail fungsionalitas inti sistem:

*   **Manajemen Satker**:
    *   Pencatatan `nama_satker`, `alamat`, dan penunjukan `admin_user_id` yang terhubung ke model `User`.
    *   Hubungan `hasMany` dengan `User` (pengguna di bawah Satker) dan `PengajuanLelang`.
*   **Manajemen Perkara & Barang**:
    *   **Perkara**: Mencatat `nomor_perkara`, `nama_tersangka`, dan `tanggal_putusan`. Terhubung ke `PengajuanLelang` dan `DokumenPerkara`.
    *   **Barang**: Detail `nama_barang`, `deskripsi`, `catatan_internal` (opsional), `harga_awal`, dan `status`. Memiliki relasi `hasMany` dengan `FotoBarang` dan `hasOne` dengan `Lelang`.
*   **Pengajuan Lelang (Workflow Verifikasi)**:
    *   Status pengajuan meliputi `Draft`, `Diajukan`, `Disetujui`, `Revisi`.
    *   Fungsi `catatan_revisi` yang disimpan sebagai JSON untuk memberikan feedback terstruktur.
    *   Setiap pengajuan memiliki `satker_id` dan `tanggal_pengajuan`.
*   **Sistem Lelang & Penawaran**:
    *   **Lelang**: Menampilkan `barang_id`, `tanggal_mulai`, `tanggal_selesai`, `status`, `harga_awal`, `harga_tertinggi`, `pemenang_id`, `pemenang_urutan`, `catatan_pemenang`.
    *   **Penawaran**: Mencatat `lelang_id`, `pembeli_id`, `nilai_penawaran`, dan `waktu_penawaran`.
    *   Fungsi `penawaranTertinggi()` pada model `Lelang` untuk menemukan penawaran tertinggi secara efisien.
*   **Manajemen Dokumen**:
    *   **Dokumen Pengajuan**: Terkait dengan `PengajuanLelang`, mencatat `jenis` dokumen dan `file_path`.
    *   **Dokumen Perkara**: Terkait dengan `Perkara`, mencatat `nama_dokumen` dan `file_path`.
*   **Audit Trail**:
    *   Model `AuditLogs` mencatat `user_id`, `event`, `auditable_type`, `auditable_id`, `description`, `aksi`, `entitas`, `entitas_id`, dan `deskripsi`. Ini memberikan jejak lengkap dari semua tindakan penting dalam sistem.
*   **Pelaporan**:
    *   Model `LaporanLelang` mencakup detail `lelang_id`, `satker_id`, `nomor_bast`, `nomor_billing`, `file_bast`, `file_bukti_bayar`, `tanggal_bast`, `tanggal_bayar`, `status`, dan `catatan`.
    *   Fungsi `isLengkap()` untuk memverifikasi kelengkapan laporan.
## 🔄 Alur Kerja (Workflow)

Alur kerja lelang yang terstandardisasi:

1.  **Persiapan Data (Admin Satker)**:
    *   Admin Satker menginput data **Satker** (jika belum ada), **Perkara**, dan **Barang** ke dalam sistem.
    *   Untuk setiap barang, `FotoBarang` diunggah dan `DokumenPerkara` terkait dimasukkan.
2.  **Pengajuan Lelang (Admin Satker)**:
    *   Admin Satker membuat **Pengajuan Lelang**, mengasosiasikannya dengan barang-barang dari suatu perkara, dan melampirkan `DokumenPengajuan` yang relevan. Status awal adalah `Draft`.
    *   Setelah lengkap, pengajuan diubah statusnya menjadi `Diajukan` untuk ditinjau oleh Admin Pusat.
3.  **Verifikasi (Admin Pusat)**:
    *   Admin Pusat meninjau `PengajuanLelang`.
    *   Berdasarkan tinjauan, Admin Pusat dapat:
        *   Mengubah status menjadi `Disetujui` jika semua persyaratan terpenuhi.
        *   Mengubah status menjadi `Revisi` dan mengisi `catatan_revisi` (JSON) untuk Admin Satker.
4.  **Pelaksanaan Lelang (Sistem Otomatis / Admin Pusat)**:
    *   Setelah pengajuan `Disetujui`, sistem membuat entitas `Lelang` untuk barang-barang yang bersangkutan.
    *   Lelang dibuka untuk publik/pembeli terdaftar pada `tanggal_mulai` yang ditentukan.
5.  **Penawaran (Pembeli)**:
    *   **Pembeli** yang telah terdaftar (dan mungkin terverifikasi melalui Magic Link) dapat melihat barang lelang aktif dan memberikan **Penawaran**.
    *   Setiap penawaran dicatat, dan sistem secara otomatis memperbarui `harga_tertinggi` pada entitas `Lelang`.
6.  **Penyelesaian Lelang (Admin Pusat / Admin Satker)**:
    *   Setelah `tanggal_selesai` lelang, Admin Pusat (atau Satker yang diberi wewenang) dapat menetapkan pemenang lelang (`pemenang_id` pada model `Lelang`). `catatan_pemenang` juga dapat ditambahkan.
    *   Sistem kemudian mencatat hasil lelang dalam `LaporanLelang`, termasuk detail seperti `nomor_bast` (Berita Acara Serah Terima) dan `nomor_billing`.
    *   Proses diakhiri dengan pengunggahan `file_bast` dan `file_bukti_bayar`.
## 🗄 Struktur Data Utama (Models)

Penjelasan lebih lanjut tentang setiap model dan relasinya:

*   `User`:
    *   `fillable`: `name`, `username`, `email`, `kontak`, `password`, `role`, `satker_id`.
    *   `hidden`: `password`, `remember_token`.
    *   **Relasi**: `belongsTo` ke `Satker`, `hasMany` ke `AuditLogs`.
    *   **Helper**: `isAdminPusat()`, `isAdminSatker()`.
*   `Satker`:
    *   `fillable`: `nama_satker`, `alamat`, `admin_user_id`.
    *   **Relasi**: `hasMany` ke `User`, `belongsTo` ke `User` (melalui `admin_user_id`), `hasMany` ke `PengajuanLelang`.
*   `Barang`:
    *   `fillable`: `perkara_id`, `nama_barang`, `deskripsi`, `catatan_internal`, `harga_awal`, `status`.
    *   `casts`: `harga_awal` ke `decimal:2`.
    *   **Relasi**: `belongsTo` ke `Perkara`, `hasMany` ke `FotoBarang`, `hasOne` ke `Lelang`.
*   `Perkara`:
    *   `fillable`: `pengajuan_lelang_id`, `nomor_perkara`, `nama_tersangka`, `tanggal_putusan`.
    *   `casts`: `tanggal_putusan` ke `date`.
    *   **Relasi**: `belongsTo` ke `PengajuanLelang`, `hasMany` ke `DokumenPerkara`, `hasMany` ke `Barang`.
*   `PengajuanLelang`:
    *   `fillable`: `satker_id`, `judul_pengajuan`, `status`, `catatan_revisi`, `tanggal_pengajuan`.
    *   `casts`: `catatan_revisi` ke `array` (JSON), `tanggal_pengajuan` ke `datetime`.
    *   **Relasi**: `belongsTo` ke `Satker`, `hasMany` ke `DokumenPengajuan`, `hasMany` ke `Perkara`.
*   `Lelang`:
    *   `fillable`: `barang_id`, `tanggal_mulai`, `tanggal_selesai`, `status`, `harga_awal`, `harga_tertinggi`, `pemenang_id`, `pemenang_urutan`, `catatan_pemenang`.
    *   `casts`: `tanggal_mulai`, `tanggal_selesai` ke `datetime`, `harga_awal`, `harga_tertinggi` ke `decimal:2`.
    *   **Relasi**: `belongsTo` ke `Barang`, `hasMany` ke `Penawaran`, `belongsTo` ke `Pembeli` (sebagai pemenang), `hasOne` ke `LaporanLelang`.
    *   **Helper**: `penawaranTertinggi()`.
*   `Penawaran`:
    *   `fillable`: `lelang_id`, `pembeli_id`, `nilai_penawaran`, `waktu_penawaran`.
    *   **Relasi**: `belongsTo` ke `Lelang`, `belongsTo` ke `Pembeli`.
*   `AuditLogs`:
    *   `fillable`: `user_id`, `event`, `auditable_type`, `auditable_id`, `description`, `aksi`, `entitas`, `entitas_id`, `deskripsi`.
    *   **Relasi**: `belongsTo` ke `User`.
*   `Pembeli`:
    *   `fillable`: `nama`, `email`, `no_hp`, `verified_at`, `magic_token`, `token_expired_at`.
    *   `casts`: `verified_at`, `token_expired_at` ke `datetime`.
    *   **Relasi**: `hasMany` ke `Penawaran`.
    *   **Helper**: `isVerifiedToday()`.
*   `DokumenPengajuan`:
    *   `fillable`: `pengajuan_lelang_id`, `jenis`, `file_path`.
    *   **Relasi**: `belongsTo` ke `PengajuanLelang`.
*   `DokumenPerkara`:
    *   `fillable`: `perkara_id`, `nama_dokumen`, `file_path`.
    *   **Relasi**: `belongsTo` ke `Perkara`.
*   `FotoBarang`:
    *   `fillable`: `barang_id`, `file_path`.
    *   **Relasi**: `belongsTo` ke `Barang`.
*   `LaporanLelang`:
    *   `fillable`: `lelang_id`, `satker_id`, `nomor_bast`, `nomor_billing`, `file_bast`, `file_bukti_bayar`, `tanggal_bast`, `tanggal_bayar`, `status`, `catatan`.
    *   `casts`: `tanggal_bast`, `tanggal_bayar` ke `date`.
    *   **Relasi**: `belongsTo` ke `Lelang`, `belongsTo` ke `Satker`.
    *   **Helper**: `isLengkap()`.

### 💾 Manajemen Cache
Sistem menggunakan Laravel Cache untuk meningkatkan performa pada halaman publik yang memiliki trafik tinggi. Caching diterapkan pada:
1.  **`public_index_stats`**: Menyimpan statistik agregat (jumlah lelang aktif, dll) untuk dashboard utama.
2.  **`public_lelangs_aktif`**: Menyimpan daftar barang yang sedang dalam masa lelang live.
3.  **`public_lelangs_mendatang`**: Menyimpan daftar barang yang sudah dijadwalkan (scheduled).

**Mekanisme Invalidation (Pembersihan Otomatis):**
Sistem secara otomatis menghapus ketiga cache di atas (Cache Bursting) setiap kali terjadi perubahan status atau data penting pada lelang melalui `LelangController`. Trigger pembersihan dilakukan saat:
*   Menjadwalkan lelang baru.
*   Mengaktifkan lelang secara manual.
*   Menutup lelang (secara manual atau sistem).
*   Membatalkan lelang (scheduled/active).
*   Menghapus penawaran tertinggi oleh Admin.
*   Pengajuan lelang ulang (unsold items).

## 🚀 Teknologi yang Digunakan

*   **Framework**: Laravel 11.x. Ini menunjukkan penggunaan fitur-fitur modern Laravel seperti routing, Eloquent ORM, migrasi, dan middleware.
*   **Frontend**: Tailwind CSS dan Vite. Kombinasi ini menawarkan pengembangan UI yang cepat dan efisien dengan utilitas CSS dan bundling aset yang modern.
*   **Database**: MySQL/PostgreSQL (relasional). Pilihan database relasional yang kuat untuk menjaga integritas data dan mendukung transaksi kompleks.
*   **Fitur Tambahan**:
    *   **Laravel Breeze/Fortify**: Digunakan untuk sistem otentikasi dasar (login, register, reset password), yang kemudian disesuaikan untuk peran `admin_pusat` dan `admin_satker`.
    *   **Magic Link untuk Pembeli**: Implementasi otentikasi tanpa kata sandir untuk pembeli, meningkatkan pengalaman pengguna dan mengurangi gesekan. Ini melibatkan tabel `pembelis` dengan kolom `magic_token` dan `token_expired_at`.
