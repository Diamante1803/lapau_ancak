-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Bulan Mei 2026 pada 11.08
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lelang_negara`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `aksi` varchar(255) NOT NULL,
  `entitas` varchar(255) NOT NULL,
  `entitas_id` bigint(20) UNSIGNED NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `barangs`
--

CREATE TABLE `barangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perkara_id` bigint(20) UNSIGNED NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `catatan_internal` text DEFAULT NULL,
  `harga_awal` decimal(15,2) NOT NULL,
  `status` enum('available','in_auction','sold','unsold') NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `barangs`
--

INSERT INTO `barangs` (`id`, `perkara_id`, `nama_barang`, `deskripsi`, `catatan_internal`, `harga_awal`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Handphone Oppo A7', 'Layar Pecah, Hp mati, terkunci', NULL, 750000.00, 'sold', '2026-04-27 01:00:05', '2026-05-05 03:30:29'),
(3, 4, 'Sendal', 'Bolong, warna pudar', NULL, 150000.00, 'sold', '2026-04-27 02:47:58', '2026-05-13 01:19:00'),
(5, 5, 'Sepatu', 'Robek, Sol hilang', NULL, 2000000.00, 'sold', '2026-04-27 21:55:12', '2026-05-13 01:19:00'),
(6, 6, 'Mobil Grand Vitara', 'Kondisi baik, lecet pemakaian, tanpa STNK dan BPKB', NULL, 34000000.00, 'sold', '2026-05-13 04:19:10', '2026-05-19 03:00:00'),
(7, 7, 'Baju Koko', 'Baju warna putih, jenis koko, bermotif', NULL, 1000000.00, 'in_auction', '2026-05-19 02:20:37', '2026-05-19 02:55:44'),
(8, 8, 'Sendal', 'Sendal Original merk adidas, warna putih', NULL, 150000.00, 'in_auction', '2026-05-19 04:56:09', '2026-05-19 05:00:57'),
(9, 8, 'Mobil Grand Vitara', 'Mobil hidup, baret pemakaian, pajak mati, tanpa bpkb dan STNK', NULL, 25000000.00, 'in_auction', '2026-05-19 04:56:23', '2026-05-19 05:00:57'),
(10, 8, 'Baju Lebaran', 'Baju warna putih, jenis koko, bermotif', NULL, 200000.00, 'in_auction', '2026-05-19 04:56:44', '2026-05-19 05:00:57'),
(11, 8, 'Sepatu', 'Sepatu Original merk adidas, warna putih', NULL, 200000.00, 'in_auction', '2026-05-19 04:56:58', '2026-05-19 05:00:57'),
(12, 8, 'Motor Vario', 'Kondisi hidup, tanpa BPKB dan STNK', NULL, 10000000.00, 'in_auction', '2026-05-19 04:59:43', '2026-05-19 05:00:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-adminpusat|103.178.17.43', 'i:1;', 1778646960),
('laravel-cache-adminpusat|103.178.17.43:timer', 'i:1778646960;', 1778646960),
('laravel-cache-adminpusat|2001:448a:8080:fb4:e5a8:4c5e:b980:641d', 'i:1;', 1778646196),
('laravel-cache-adminpusat|2001:448a:8080:fb4:e5a8:4c5e:b980:641d:timer', 'i:1778646196;', 1778646196),
('laravel-cache-pangkalan|103.178.17.43', 'i:1;', 1778647047),
('laravel-cache-pangkalan|103.178.17.43:timer', 'i:1778647047;', 1778647047),
('laravel-cache-pangkalan|2001:448a:8080:b77:dd67:d7e6:532f:27fe', 'i:1;', 1779155683),
('laravel-cache-pangkalan|2001:448a:8080:b77:dd67:d7e6:532f:27fe:timer', 'i:1779155683;', 1779155683);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_pengajuans`
--

CREATE TABLE `dokumen_pengajuans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengajuan_lelang_id` bigint(20) UNSIGNED NOT NULL,
  `jenis` enum('sk_panitia','izin_penjualan','surat_penetapan_harga') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dokumen_pengajuans`
--

INSERT INTO `dokumen_pengajuans` (`id`, `pengajuan_lelang_id`, `jenis`, `file_path`, `created_at`, `updated_at`) VALUES
(11, 8, 'sk_panitia', 'pengajuan/sk_panitia_ckn-pangkalan_1777348378.pdf', '2026-04-27 20:52:58', '2026-04-27 20:52:58'),
(12, 8, 'izin_penjualan', 'pengajuan/izin_penjualan_ckn-pangkalan_1777348388.pdf', '2026-04-27 20:53:08', '2026-04-27 20:53:08'),
(14, 8, 'surat_penetapan_harga', 'pengajuan/surat_penetapan_harga_ckn-pangkalan_1777354988.pdf', '2026-04-27 22:43:08', '2026-04-27 22:43:08'),
(15, 10, 'sk_panitia', 'pengajuan/sk_panitia_kejaksaan-negeri-padang_1778636916.pdf', '2026-05-13 01:48:36', '2026-05-13 01:48:36'),
(18, 10, 'izin_penjualan', 'pengajuan/izin_penjualan_kejaksaan-negeri-padang_1778639905.pdf', '2026-05-13 02:38:25', '2026-05-13 02:38:25'),
(19, 10, 'surat_penetapan_harga', 'pengajuan/surat_penetapan_harga_kejaksaan-negeri-padang_1778639905.pdf', '2026-05-13 02:38:25', '2026-05-13 02:38:25'),
(20, 11, 'sk_panitia', 'pengajuan/sk_panitia_ckn-pangkalan_1779157111.pdf', '2026-05-19 02:18:31', '2026-05-19 02:18:31'),
(21, 11, 'izin_penjualan', 'pengajuan/izin_penjualan_ckn-pangkalan_1779157111.pdf', '2026-05-19 02:18:31', '2026-05-19 02:18:31'),
(22, 11, 'surat_penetapan_harga', 'pengajuan/surat_penetapan_harga_ckn-pangkalan_1779157111.pdf', '2026-05-19 02:18:31', '2026-05-19 02:18:31'),
(23, 12, 'sk_panitia', 'pengajuan/sk_panitia_kejaksaan-negeri-padang_1779166497.pdf', '2026-05-19 04:54:57', '2026-05-19 04:54:57'),
(24, 12, 'izin_penjualan', 'pengajuan/izin_penjualan_kejaksaan-negeri-padang_1779166497.pdf', '2026-05-19 04:54:57', '2026-05-19 04:54:57'),
(25, 12, 'surat_penetapan_harga', 'pengajuan/surat_penetapan_harga_kejaksaan-negeri-padang_1779166497.pdf', '2026-05-19 04:54:57', '2026-05-19 04:54:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_perkaras`
--

CREATE TABLE `dokumen_perkaras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perkara_id` bigint(20) UNSIGNED NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dokumen_perkaras`
--

INSERT INTO `dokumen_perkaras` (`id`, `perkara_id`, `nama_dokumen`, `file_path`, `created_at`, `updated_at`) VALUES
(6, 4, 'B-18', 'dokumen_perkara/b-18_primus_ckn-pangkalan_1777274287.jpeg', '2026-04-27 00:18:07', '2026-04-27 00:18:07'),
(12, 4, 'B-20', 'dokumen_perkara/b-20_primus_ckn-pangkalan_1777277603.jpeg', '2026-04-27 01:13:24', '2026-04-27 01:13:24'),
(13, 5, 'B-18', 'dokumen_perkara/b-18_adiguna2_ckn-pangkalan_1777346434.pdf', '2026-04-27 20:20:34', '2026-04-27 20:20:34'),
(14, 5, 'B-20', 'dokumen_perkara/b-20_adiguna2_ckn-pangkalan_1777346434.pdf', '2026-04-27 20:20:34', '2026-04-27 20:20:34'),
(15, 5, 'Petikan Putusan', 'dokumen_perkara/petikan-putusan_adiguna2_ckn-pangkalan_1777346434.pdf', '2026-04-27 20:20:34', '2026-04-27 20:20:34'),
(16, 5, 'BA-21', 'dokumen_perkara/ba-21_adiguna2_ckn-pangkalan_1777349114.pdf', '2026-04-27 21:05:14', '2026-04-27 21:05:14'),
(17, 6, 'P-48', 'dokumen_perkara/p-48_handri_kejaksaan-negeri-padang_1778639995.pdf', '2026-05-13 02:39:55', '2026-05-13 02:39:55'),
(18, 6, 'BA-20', 'dokumen_perkara/ba-20_handri_kejaksaan-negeri-padang_1778639995.pdf', '2026-05-13 02:39:55', '2026-05-13 02:39:55'),
(21, 7, 'B-20', 'dokumen_perkara/b-20_1779158633.pdf', '2026-05-19 02:43:53', '2026-05-19 02:43:53'),
(22, 7, 'P-48', 'dokumen_perkara/p-48_1779158879.pdf', '2026-05-19 02:47:59', '2026-05-19 02:47:59'),
(23, 8, 'B-20', 'dokumen_perkara/b-20_johanes_kejaksaan-negeri-padang_1779166539.pdf', '2026-05-19 04:55:39', '2026-05-19 04:55:39'),
(24, 8, 'P-48', 'dokumen_perkara/p-48_johanes_kejaksaan-negeri-padang_1779166539.pdf', '2026-05-19 04:55:39', '2026-05-19 04:55:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `foto_barangs`
--

CREATE TABLE `foto_barangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `foto_barangs`
--

INSERT INTO `foto_barangs` (`id`, `barang_id`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'foto_barang/PV0r3W75Biwe3h3n6jogbVIcV5dCm36ry0aVV7kl.jpg', '2026-04-27 01:29:58', '2026-04-27 01:29:58'),
(2, 1, 'foto_barang/whatsapp-image-2026-02-24-at-134207_1777282192.jpeg', '2026-04-27 02:29:52', '2026-04-27 02:29:52'),
(6, 3, 'foto_barang/sendal_1_1777349499.jpeg', '2026-04-27 21:11:39', '2026-04-27 21:11:39'),
(8, 5, 'foto_barang/sepatu_1_1777352121.jpeg', '2026-04-27 21:55:21', '2026-04-27 21:55:21'),
(9, 5, 'foto_barang/sepatu_2_1777354673.jpeg', '2026-04-27 22:37:53', '2026-04-27 22:37:53'),
(10, 6, 'foto_barang/mobil-grand-vitara_1_1778645962.jpeg', '2026-05-13 04:19:22', '2026-05-13 04:19:22'),
(13, 6, 'foto_barang/mobil-grand-vitara_2_1778646038.jpg', '2026-05-13 04:20:38', '2026-05-13 04:20:38'),
(14, 7, 'foto_barang/baju-koko_1_1779157248.jpg', '2026-05-19 02:20:48', '2026-05-19 02:20:48'),
(15, 7, 'foto_barang/baju-koko_2_1779157248.jpg', '2026-05-19 02:20:48', '2026-05-19 02:20:48'),
(16, 7, 'foto_barang/baju-koko_3_1779159220.jpg', '2026-05-19 02:53:40', '2026-05-19 02:53:40'),
(17, 7, 'foto_barang/baju-koko_4_1779159220.jpg', '2026-05-19 02:53:40', '2026-05-19 02:53:40'),
(18, 8, 'foto_barang/sendal_1_1779166643.jpg', '2026-05-19 04:57:23', '2026-05-19 04:57:23'),
(19, 8, 'foto_barang/sendal_2_1779166643.jpg', '2026-05-19 04:57:23', '2026-05-19 04:57:23'),
(20, 9, 'foto_barang/mobil-grand-vitara_1_1779166651.jpg', '2026-05-19 04:57:31', '2026-05-19 04:57:31'),
(21, 10, 'foto_barang/baju-lebarang_1_1779166665.jpg', '2026-05-19 04:57:45', '2026-05-19 04:57:45'),
(22, 10, 'foto_barang/baju-lebarang_2_1779166665.jpg', '2026-05-19 04:57:45', '2026-05-19 04:57:45'),
(23, 11, 'foto_barang/sepatu_1_1779166674.jpg', '2026-05-19 04:57:54', '2026-05-19 04:57:54'),
(24, 11, 'foto_barang/sepatu_2_1779166674.jpg', '2026-05-19 04:57:54', '2026-05-19 04:57:54'),
(25, 12, 'foto_barang/motor-vario_1_1779166798.jpg', '2026-05-19 04:59:58', '2026-05-19 04:59:58'),
(26, 12, 'foto_barang/motor-vario_2_1779166798.jpg', '2026-05-19 04:59:58', '2026-05-19 04:59:58'),
(27, 12, 'foto_barang/motor-vario_3_1779166798.jpg', '2026-05-19 04:59:58', '2026-05-19 04:59:58'),
(28, 12, 'foto_barang/motor-vario_4_1779166798.jpg', '2026-05-19 04:59:58', '2026-05-19 04:59:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_lelangs`
--

CREATE TABLE `laporan_lelangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lelang_id` bigint(20) UNSIGNED NOT NULL,
  `satker_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_bast` varchar(255) DEFAULT NULL,
  `nomor_billing` varchar(255) DEFAULT NULL,
  `file_bast` varchar(255) DEFAULT NULL,
  `file_bukti_bayar` varchar(255) DEFAULT NULL,
  `tanggal_bast` date DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `status` enum('belum_lengkap','lengkap') NOT NULL DEFAULT 'belum_lengkap',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lelangs`
--

CREATE TABLE `lelangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_mulai` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tanggal_selesai` timestamp NULL DEFAULT NULL,
  `status` enum('scheduled','active','closed','cancelled') NOT NULL DEFAULT 'scheduled',
  `harga_awal` decimal(15,2) NOT NULL,
  `harga_tertinggi` decimal(15,2) DEFAULT NULL,
  `pemenang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pemenang_urutan` tinyint(4) NOT NULL DEFAULT 1,
  `catatan_pemenang` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `lelangs`
--

INSERT INTO `lelangs` (`id`, `barang_id`, `tanggal_mulai`, `tanggal_selesai`, `status`, `harga_awal`, `harga_tertinggi`, `pemenang_id`, `pemenang_urutan`, `catatan_pemenang`, `created_at`, `updated_at`) VALUES
(5, 1, '2026-05-05 03:30:29', '2026-05-05 03:29:13', 'closed', 750000.00, 782131.00, 2, 1, NULL, '2026-04-28 06:07:35', '2026-05-05 03:30:29'),
(6, 3, '2026-05-13 01:19:00', '2026-05-06 13:31:00', 'closed', 150000.00, 160000.00, 2, 1, NULL, '2026-04-28 06:07:35', '2026-05-13 01:19:00'),
(7, 5, '2026-05-13 01:19:00', '2026-05-06 13:31:00', 'closed', 2000000.00, 2130000.00, 2, 1, NULL, '2026-04-28 06:07:35', '2026-05-13 01:19:00'),
(8, 6, '2026-05-19 03:00:00', '2026-05-19 03:00:00', 'closed', 34000000.00, 90000000.00, 3, 1, NULL, '2026-05-13 04:23:40', '2026-05-19 03:00:00'),
(9, 7, '2026-05-19 03:59:29', '2026-05-26 03:00:00', 'active', 1000000.00, 1520000.00, NULL, 1, NULL, '2026-05-19 02:55:44', '2026-05-19 03:59:29'),
(10, 8, '2026-05-19 05:02:00', '2026-05-26 04:00:00', 'active', 150000.00, NULL, NULL, 1, NULL, '2026-05-19 05:00:57', '2026-05-19 05:02:00'),
(11, 9, '2026-05-19 05:02:00', '2026-05-26 04:00:00', 'active', 25000000.00, NULL, NULL, 1, NULL, '2026-05-19 05:00:57', '2026-05-19 05:02:00'),
(12, 10, '2026-05-19 05:02:00', '2026-05-26 04:00:00', 'active', 200000.00, NULL, NULL, 1, NULL, '2026-05-19 05:00:57', '2026-05-19 05:02:00'),
(13, 11, '2026-05-19 05:02:00', '2026-05-26 04:00:00', 'active', 200000.00, NULL, NULL, 1, NULL, '2026-05-19 05:00:57', '2026-05-19 05:02:00'),
(14, 12, '2026-05-19 05:02:00', '2026-05-26 04:00:00', 'active', 10000000.00, NULL, NULL, 1, NULL, '2026-05-19 05:00:57', '2026-05-19 05:02:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2026_04_20_021033_create_satkers_table', 1),
(4, '2026_04_20_021054_create_users_table', 1),
(5, '2026_04_20_021119_create_pengajuan_lelangs_table', 1),
(6, '2026_04_20_021137_create_dokumen_pengajuans_table', 1),
(7, '2026_04_20_021154_create_perkaras_table', 1),
(8, '2026_04_20_021214_create_dokumen_perkaras_table', 1),
(9, '2026_04_20_021232_create_barangs_table', 1),
(10, '2026_04_20_021256_create_foto_barangs_table', 1),
(11, '2026_04_20_021310_create_pembelis_table', 1),
(12, '2026_04_20_021318_create_lelangs_table', 1),
(13, '2026_04_20_021334_create_penawarans_table', 1),
(14, '2026_04_20_021346_create_audit_logs_table', 1),
(15, '2026_04_20_042036_create_sessions_table', 1),
(16, '2026_04_22_035417_add_kontak_to_users_table', 2),
(17, '2026_04_22_035631_migrate_kontak_satker_to_users', 2),
(18, '2026_04_22_035815_drop_kontak_from_satkers_table', 2),
(19, '2026_04_22_050842_add_admin_user_id_to_satkers', 2),
(20, '2026_04_28_034658_update_jenis_enum_dokumen_pengajuans', 3),
(21, '2026_04_29_101506_add_magic_link_to_pembelis_table', 4),
(22, '2026_04_29_144901_add_username_to_users_table', 5),
(23, '2026_05_05_092522_change_catatan_revisi_to_json_in_pengajuan_lelangs', 6),
(24, '2026_05_05_103535_add_pemenang_notes_to_lelangs_table', 7),
(27, '2026_05_05_111706_berita_acara_serah_terimas', 8),
(28, '2026_05_05_145706_add_catatan_internal_to_barangs_table', 9),
(29, '2026_05_06_111932_drop_berita_acara_serah_terimas_table', 9),
(30, '2026_05_06_112012_create_laporan_lelangs_table', 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembelis`
--

CREATE TABLE `pembelis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_hp` varchar(255) NOT NULL,
  `magic_token` varchar(64) DEFAULT NULL,
  `token_expired_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pembelis`
--

INSERT INTO `pembelis` (`id`, `nama`, `email`, `no_hp`, `magic_token`, `token_expired_at`, `verified_at`, `created_at`, `updated_at`) VALUES
(1, 'Jodiaz Faren', 'fakediaz18@gmail.com', '0852121313', '0MiulNz1xlV64p0zokacSnsOiyGmz9lV70hqbjdWjTRJnOk51lVpD8lp8A3t01Pz', '2026-05-13 16:59:59', '2026-04-29 03:33:48', '2026-04-29 03:22:33', '2026-05-13 04:33:38'),
(2, 'Satriadiaz Faren', 'satriadiazfaren@gmail.com', '085271080395', 'Gd2kuaRQAavxcFdsDMCX4fwwKXn43RxdMbabXOeZMHRlUzeGnwhzws941KoONyiE', '2026-05-19 16:59:59', '2026-04-29 04:08:38', '2026-04-29 04:01:01', '2026-05-19 03:39:37'),
(3, 'Rahmat Al Mubarak', 'rahmatalmubarak35@gmail.com', '081977717633', 'zRhHkHPFe3euH9fqPLiC1VhxGf2eNrwl9ay8niA1DI2IHv0l5cxPVHEHUvEXKYzy', '2026-05-19 16:59:59', '2026-05-19 02:57:17', '2026-05-19 02:56:54', '2026-05-19 02:57:17'),
(4, 'Randu Fascal', 'randufascal06@gmail.com', '081264618270', 'KOEYgwlSgPFElNKhcicm2y8kkrcT00KvGRo5ZD7kOgCHmYNN3EUwWScpT2MGsGSO', '2026-05-19 16:59:59', '2026-05-19 03:39:16', '2026-05-19 03:38:45', '2026-05-19 03:39:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penawarans`
--

CREATE TABLE `penawarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lelang_id` bigint(20) UNSIGNED NOT NULL,
  `pembeli_id` bigint(20) UNSIGNED NOT NULL,
  `nilai_penawaran` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penawarans`
--

INSERT INTO `penawarans` (`id`, `lelang_id`, `pembeli_id`, `nilai_penawaran`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 2100000.00, '2026-04-29 03:55:52', '2026-04-29 03:55:52'),
(2, 7, 1, 2120000.00, '2026-04-29 03:56:17', '2026-04-29 03:56:17'),
(3, 7, 2, 2130000.00, '2026-04-29 04:08:51', '2026-04-29 04:08:51'),
(4, 6, 2, 160000.00, '2026-04-29 04:17:48', '2026-04-29 04:17:48'),
(5, 5, 2, 782131.00, '2026-04-29 05:17:10', '2026-04-29 05:17:10'),
(6, 8, 1, 40000000.00, '2026-05-13 04:34:39', '2026-05-13 04:34:39'),
(7, 8, 3, 90000000.00, '2026-05-19 02:57:34', '2026-05-19 02:57:34'),
(8, 9, 3, 1010000.00, '2026-05-19 03:37:17', '2026-05-19 03:37:17'),
(9, 9, 4, 1020000.00, '2026-05-19 03:39:30', '2026-05-19 03:39:30'),
(10, 9, 2, 1500000.00, '2026-05-19 03:40:05', '2026-05-19 03:40:05'),
(11, 9, 3, 1520000.00, '2026-05-19 03:59:29', '2026-05-19 03:59:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_lelangs`
--

CREATE TABLE `pengajuan_lelangs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `satker_id` bigint(20) UNSIGNED NOT NULL,
  `judul_pengajuan` varchar(255) NOT NULL,
  `status` enum('draft','submitted','revision','approved','rejected') NOT NULL DEFAULT 'draft',
  `catatan_revisi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`catatan_revisi`)),
  `tanggal_pengajuan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pengajuan_lelangs`
--

INSERT INTO `pengajuan_lelangs` (`id`, `satker_id`, `judul_pengajuan`, `status`, `catatan_revisi`, `tanggal_pengajuan`, `created_at`, `updated_at`) VALUES
(8, 1, 'CKN Pangkalan Januari', 'approved', NULL, '2026-04-28 01:00:21', '2026-04-26 20:51:20', '2026-04-28 01:00:21'),
(10, 3, 'Penyelesaian KN Padang Mei 2026', 'approved', NULL, '2026-05-13 04:20:59', '2026-05-13 01:48:18', '2026-05-13 04:22:40'),
(11, 1, 'CKN Pangkalan Maret', 'approved', NULL, '2026-05-19 02:54:41', '2026-05-19 02:12:46', '2026-05-19 02:55:00'),
(12, 3, 'KN Padang Juni', 'approved', NULL, '2026-05-19 05:00:12', '2026-05-19 04:54:41', '2026-05-19 05:00:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perkaras`
--

CREATE TABLE `perkaras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengajuan_lelang_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_perkara` varchar(255) NOT NULL,
  `nama_tersangka` varchar(255) NOT NULL,
  `tanggal_putusan` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `perkaras`
--

INSERT INTO `perkaras` (`id`, `pengajuan_lelang_id`, `nomor_perkara`, `nama_tersangka`, `tanggal_putusan`, `created_at`, `updated_at`) VALUES
(4, 8, '23423', 'Primus', '2026-04-02', '2026-04-27 00:18:07', '2026-04-27 22:43:21'),
(5, 8, '2121', 'Adiguna2', '2026-02-05', '2026-04-27 20:20:34', '2026-04-27 20:20:34'),
(6, 10, 'PDM/021/2/2026', 'Handri', '2026-02-10', '2026-05-13 02:39:55', '2026-05-13 02:39:55'),
(7, 11, '1231', 'Deki', '2026-04-07', '2026-05-19 02:19:18', '2026-05-19 02:19:18'),
(8, 12, 'PDM/12341/Pdg/2026', 'Johanes', '2026-05-01', '2026-05-19 04:55:39', '2026-05-19 04:55:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `satkers`
--

CREATE TABLE `satkers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_satker` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `admin_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `satkers`
--

INSERT INTO `satkers` (`id`, `nama_satker`, `alamat`, `admin_user_id`, `created_at`, `updated_at`) VALUES
(1, 'CKN Pangkalan', 'Pangkalan', NULL, NULL, NULL),
(3, 'Kejaksaan Negeri Padang', 'Jl. Contoh No. 10, Indonesia', 4, '2026-05-13 01:19:52', '2026-05-13 01:19:52'),
(4, 'Kejaksaan Negeri Padang Panjang', 'Jl. Contoh No. 20, Indonesia', 5, '2026-05-13 01:19:52', '2026-05-13 01:19:52'),
(5, 'Kejaksaan Negeri Pariaman', 'Jl. Contoh No. 30, Indonesia', 6, '2026-05-13 01:19:52', '2026-05-13 01:19:52'),
(6, 'Kejaksaan Negeri Bukittinggi', 'Jl. Contoh No. 40, Indonesia', 7, '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(7, 'Kejaksaan Negeri Payakumbuh', 'Jl. Contoh No. 50, Indonesia', 8, '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(8, 'Kejaksaan Negeri Solok', 'Jl. Contoh No. 60, Indonesia', 9, '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(9, 'Kejaksaan Negeri Pesisir Selatan', 'Jl. Contoh No. 70, Indonesia', 10, '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(10, 'Kejaksaan Negeri Pasaman', 'Jl. Contoh No. 80, Indonesia', 11, '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(11, 'Kejaksaan Negeri Pasaman Barat', 'Jl. Contoh No. 90, Indonesia', 12, '2026-05-13 01:19:54', '2026-05-13 01:19:54'),
(12, 'Kejaksaan Negeri Agam', 'Jl. Contoh No. 100, Indonesia', 13, '2026-05-13 01:19:54', '2026-05-13 01:19:54'),
(13, 'Kejaksaan Negeri Dharmasraya', 'Jl. Contoh No. 110, Indonesia', 14, '2026-05-13 01:19:54', '2026-05-13 01:19:54'),
(14, 'Kejaksaan Negeri Sijunjung', 'Jl. Contoh No. 120, Indonesia', 15, '2026-05-13 01:19:54', '2026-05-13 01:19:54'),
(15, 'Kejaksaan Negeri Solok Selatan', 'Jl. Contoh No. 130, Indonesia', 16, '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(16, 'Kejaksaan Negeri Sawahlunto', 'Jl. Contoh No. 140, Indonesia', 17, '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(17, 'Kejaksaan Negeri Tanah Datar', 'Jl. Contoh No. 150, Indonesia', 18, '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(18, 'Cabang Kejaksaan Negeri Payakumbuh di Pangkalan Kotobaru', 'Jl. Contoh No. 160, Indonesia', 19, '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(19, 'Cabang Kejaksaan Negeri Payakumbuh di Suliki', 'Jl. Contoh No. 170, Indonesia', 20, '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(20, 'Cabang Kejaksaan Negeri Solok di Alahan Panjang', 'Jl. Contoh No. 180, Indonesia', 21, '2026-05-13 01:19:56', '2026-05-13 01:19:56'),
(21, 'Cabang Kejaksaan Negeri Agam di Maninjau', 'Jl. Contoh No. 190, Indonesia', 22, '2026-05-13 01:19:56', '2026-05-13 01:19:56'),
(22, 'Cabang Kejaksaan Negeri Pasaman Barat di Air Bangis', 'Jl. Contoh No. 200, Indonesia', 23, '2026-05-13 01:19:56', '2026-05-13 01:19:56'),
(23, 'Cabang Kejaksaan Negeri Pesisir Selatan di Balai Selasa', 'Jl. Contoh No. 210, Indonesia', 24, '2026-05-13 01:19:56', '2026-05-13 01:19:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Ah0pWhnLLAZHkwXVlMVfQ6gbNGLuhZmLNoSfL5zc', 4, '2001:448a:8080:b77:dd67:d7e6:532f:27fe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSDhiN2ZhTUxrVnJCa0xwbENaQ3VkWEZnd2VKNFNMNEk5MHEwSlgzZyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY5OiJodHRwczovL2FmZmlkYXZpdC1sYXN0LWhhcm5lc3Mubmdyb2stZnJlZS5kZXYvc2F0a2VyL2xlbGFuZy8xMi9kZXRhaWwiO3M6NToicm91dGUiO3M6MjA6InNhdGtlci5sZWxhbmcuZGV0YWlsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDt9', 1779179850),
('PX2XAR3fNcwCMLwirAAWj9AURlFLzXiYNmwIRias', 2, '2001:448a:8080:b77:dd67:d7e6:532f:27fe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiMlF0bWlHVWZZN3M1M2ZVdmkyNlNsTVpmUWFHa0hJUUVhdnp0MUh6TiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY2OiJodHRwczovL2FmZmlkYXZpdC1sYXN0LWhhcm5lc3Mubmdyb2stZnJlZS5kZXYvYWRtaW4vbGVsYW5nL3NlbGVzYWkiO3M6NToicm91dGUiO3M6MjA6ImFkbWluLmxlbGFuZy5zZWxlc2FpIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjE5OiJ2ZXJpZmllZF9wZW1iZWxpX2lkIjtpOjI7czoyMToidmVyaWZpZWRfcGVtYmVsaV9uYW1hIjtzOjE2OiJTYXRyaWFkaWF6IEZhcmVuIjtzOjE2OiJ2ZXJpZmllZF9leHBpcmVkIjtzOjI1OiIyMDI2LTA1LTE5VDIzOjU5OjU5KzA3OjAwIjt9', 1779174460);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `satker_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `kontak` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin_pusat','admin_satker') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `satker_id`, `name`, `username`, `email`, `kontak`, `password`, `role`, `created_at`, `updated_at`) VALUES
(2, NULL, 'Satriadiaz', 'adminpusat', 'fakediaz18@gmail.com', NULL, '$2y$12$1H9ZKD/664kmcO9SaBfk4eD8KstXDTxBctOWOM9kY5.Ap3UJMDghG', 'admin_pusat', '2026-04-20 00:14:42', '2026-05-19 02:24:28'),
(3, 1, 'Rahmat', 'pangkalan', 'pangkalan@gmail.com', '085271080395', '$2y$12$7occ99qoGckR/CmIZlIKkuYMttrGRUVdJXYsGT/WyX48LnU3tsTc6', 'admin_satker', '2026-04-20 00:16:20', '2026-05-19 01:54:11'),
(4, 3, 'Kejaksaan Negeri Padang', 'kejaripadang', 'admin.satker1@lelang.test', '081374008877', '$2y$12$xcTDKuNZRIJrmfy1O2tA3eZ0FVdYLn.iA7oXkStiyD8qaUWmz.qCu', 'admin_satker', '2026-05-13 01:19:52', '2026-05-19 04:54:04'),
(5, NULL, 'Admin Kejaksaan Negeri Padang Panjang', NULL, 'admin.satker2@lelang.test', NULL, '$2y$12$muawiK4M5G3kQb/LyDZ8u.rv8dhgtmE12ZsTCjTQdBymLe.AHmXP2', 'admin_satker', '2026-05-13 01:19:52', '2026-05-13 01:19:52'),
(6, NULL, 'Admin Kejaksaan Negeri Pariaman', NULL, 'admin.satker3@lelang.test', NULL, '$2y$12$4YvIiWXYnMCaM65ZOJbYMOSPSU3BEz59Zot6Q7177m0wXD7wKwciW', 'admin_satker', '2026-05-13 01:19:52', '2026-05-13 01:19:52'),
(7, NULL, 'Admin Kejaksaan Negeri Bukittinggi', NULL, 'admin.satker4@lelang.test', NULL, '$2y$12$y7oPahS2sbYBEhE9r5K5VO7UW0lNc.vko2ChWAgUWlNgkUCRnwRNO', 'admin_satker', '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(8, NULL, 'Admin Kejaksaan Negeri Payakumbuh', NULL, 'admin.satker5@lelang.test', NULL, '$2y$12$kLlEIts3lWqCrHy7mqpBa.E/dWYT5pMHvveCWiARyPlwFNSPGTZpi', 'admin_satker', '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(9, NULL, 'Admin Kejaksaan Negeri Solok', NULL, 'admin.satker6@lelang.test', NULL, '$2y$12$J6IXl2AwAHBmUFuVZWJfBO7ro.irM0u/VQ7LZ20imqZLw4dRmBKGS', 'admin_satker', '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(10, NULL, 'Admin Kejaksaan Negeri Pesisir Selatan', NULL, 'admin.satker7@lelang.test', NULL, '$2y$12$2qnIZzFuHv7YD4EDO0UHSOXSitsmilPc6HjKPzvyFjqMyKRXeE6ZS', 'admin_satker', '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(11, NULL, 'Admin Kejaksaan Negeri Pasaman', NULL, 'admin.satker8@lelang.test', NULL, '$2y$12$MMBoQ4dEm68MOuSJ5.wvy.KuYhJ4hVGotDjHtzyLr/KmBCeqahwt2', 'admin_satker', '2026-05-13 01:19:53', '2026-05-13 01:19:53'),
(12, NULL, 'Admin Kejaksaan Negeri Pasaman Barat', NULL, 'admin.satker9@lelang.test', NULL, '$2y$12$u9Exffg4HYZCo1na.6THZOWcfzWyscC9pH.dbJO/v.F.sn7JfV62K', 'admin_satker', '2026-05-13 01:19:54', '2026-05-13 01:19:54'),
(13, NULL, 'Admin Kejaksaan Negeri Agam', NULL, 'admin.satker10@lelang.test', NULL, '$2y$12$pf2t67kUNYdAVo7zATfsk.DLclPDX4sWnivUazbaucyfLmk1fDkm2', 'admin_satker', '2026-05-13 01:19:54', '2026-05-13 01:19:54'),
(14, NULL, 'Admin Kejaksaan Negeri Dharmasraya', NULL, 'admin.satker11@lelang.test', NULL, '$2y$12$Bl5EQ60GYpEUSdo6RMOnKel8OjJo6AMCq96VRh6xJ28iIDTgJa6qa', 'admin_satker', '2026-05-13 01:19:54', '2026-05-13 01:19:54'),
(15, NULL, 'Admin Kejaksaan Negeri Sijunjung', NULL, 'admin.satker12@lelang.test', NULL, '$2y$12$p2CEANZfObPb37BKJ.6jr.gDM38O53at5YBlxQdJXjCjNpAU.fNXq', 'admin_satker', '2026-05-13 01:19:54', '2026-05-13 01:19:54'),
(16, NULL, 'Admin Kejaksaan Negeri Solok Selatan', NULL, 'admin.satker13@lelang.test', NULL, '$2y$12$rQKL.ANg8CFB457WBFoNvutL7aiYkVsxCEynGt6Uggqqh7kdcq0mO', 'admin_satker', '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(17, NULL, 'Admin Kejaksaan Negeri Sawahlunto', NULL, 'admin.satker14@lelang.test', NULL, '$2y$12$MXdQUAKkGycEDXedNgDseO2SIb2OwyAQcEHSl7C1/qt3Akp2dc/Nm', 'admin_satker', '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(18, NULL, 'Admin Kejaksaan Negeri Tanah Datar', NULL, 'admin.satker15@lelang.test', NULL, '$2y$12$y7Ep9xnl9.hmKz3magZ5P.VzRwPLRzFfRlD6OLM6QiU7K/pRrE3z6', 'admin_satker', '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(19, NULL, 'Admin Cabang Kejaksaan Negeri Payakumbuh di Pangkalan Kotobaru', NULL, 'admin.satker16@lelang.test', NULL, '$2y$12$.F5jXUlCe7GAo7z7gQkrJOfbGBL4JFSBtMmA.OxLFZolTUkY.mWvi', 'admin_satker', '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(20, NULL, 'Admin Cabang Kejaksaan Negeri Payakumbuh di Suliki', NULL, 'admin.satker17@lelang.test', NULL, '$2y$12$3Yumb8KfolgzuMHxiaD0LuEr.zOpQ2Oxcjy987uQ6Vsm5yfQsMBH6', 'admin_satker', '2026-05-13 01:19:55', '2026-05-13 01:19:55'),
(21, NULL, 'Admin Cabang Kejaksaan Negeri Solok di Alahan Panjang', NULL, 'admin.satker18@lelang.test', NULL, '$2y$12$NzJw7NyVf4B0dN8eQnB2cOZ6merRustHapolQCgn7PiaphATUiffW', 'admin_satker', '2026-05-13 01:19:56', '2026-05-13 01:19:56'),
(22, NULL, 'Admin Cabang Kejaksaan Negeri Agam di Maninjau', NULL, 'admin.satker19@lelang.test', NULL, '$2y$12$TJKvZlC8oPAyZx8S6pNmtOCZ8FFjqqmD/57mdWQlO44ysIWxROXF2', 'admin_satker', '2026-05-13 01:19:56', '2026-05-13 01:19:56'),
(23, NULL, 'Admin Cabang Kejaksaan Negeri Pasaman Barat di Air Bangis', NULL, 'admin.satker20@lelang.test', NULL, '$2y$12$.qRbtYXEVl0VH7QYmitN8Olsap7TMCKp1.sLjDqCNQpdQXbOZpA3.', 'admin_satker', '2026-05-13 01:19:56', '2026-05-13 01:19:56'),
(24, NULL, 'Admin Cabang Kejaksaan Negeri Pesisir Selatan di Balai Selasa', NULL, 'admin.satker21@lelang.test', NULL, '$2y$12$XzfFCI/irvTExAqsS8KepunTgGJCmX8fs.MLoat.ZlY1aB9Ob4MkW', 'admin_satker', '2026-05-13 01:19:56', '2026-05-13 01:19:56');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `barangs`
--
ALTER TABLE `barangs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barangs_perkara_id_foreign` (`perkara_id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `dokumen_pengajuans`
--
ALTER TABLE `dokumen_pengajuans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dokumen_pengajuans_pengajuan_lelang_id_foreign` (`pengajuan_lelang_id`);

--
-- Indeks untuk tabel `dokumen_perkaras`
--
ALTER TABLE `dokumen_perkaras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dokumen_perkaras_perkara_id_foreign` (`perkara_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `foto_barangs`
--
ALTER TABLE `foto_barangs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foto_barangs_barang_id_foreign` (`barang_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_lelangs`
--
ALTER TABLE `laporan_lelangs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_lelangs_lelang_id_foreign` (`lelang_id`),
  ADD KEY `laporan_lelangs_satker_id_foreign` (`satker_id`);

--
-- Indeks untuk tabel `lelangs`
--
ALTER TABLE `lelangs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lelangs_barang_id_foreign` (`barang_id`),
  ADD KEY `lelangs_pemenang_id_foreign` (`pemenang_id`),
  ADD KEY `lelangs_tanggal_mulai_index` (`tanggal_mulai`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pembelis`
--
ALTER TABLE `pembelis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pembelis_email_unique` (`email`),
  ADD KEY `pembelis_email_index` (`email`);

--
-- Indeks untuk tabel `penawarans`
--
ALTER TABLE `penawarans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penawarans_lelang_id_foreign` (`lelang_id`),
  ADD KEY `penawarans_pembeli_id_foreign` (`pembeli_id`);

--
-- Indeks untuk tabel `pengajuan_lelangs`
--
ALTER TABLE `pengajuan_lelangs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_lelangs_satker_id_foreign` (`satker_id`),
  ADD KEY `pengajuan_lelangs_status_index` (`status`);

--
-- Indeks untuk tabel `perkaras`
--
ALTER TABLE `perkaras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perkaras_pengajuan_lelang_id_foreign` (`pengajuan_lelang_id`);

--
-- Indeks untuk tabel `satkers`
--
ALTER TABLE `satkers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `satkers_admin_user_id_foreign` (`admin_user_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_satker_id_foreign` (`satker_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `barangs`
--
ALTER TABLE `barangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `dokumen_pengajuans`
--
ALTER TABLE `dokumen_pengajuans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `dokumen_perkaras`
--
ALTER TABLE `dokumen_perkaras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `foto_barangs`
--
ALTER TABLE `foto_barangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan_lelangs`
--
ALTER TABLE `laporan_lelangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lelangs`
--
ALTER TABLE `lelangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `pembelis`
--
ALTER TABLE `pembelis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `penawarans`
--
ALTER TABLE `penawarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_lelangs`
--
ALTER TABLE `pengajuan_lelangs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `perkaras`
--
ALTER TABLE `perkaras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `satkers`
--
ALTER TABLE `satkers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `barangs`
--
ALTER TABLE `barangs`
  ADD CONSTRAINT `barangs_perkara_id_foreign` FOREIGN KEY (`perkara_id`) REFERENCES `perkaras` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dokumen_pengajuans`
--
ALTER TABLE `dokumen_pengajuans`
  ADD CONSTRAINT `dokumen_pengajuans_pengajuan_lelang_id_foreign` FOREIGN KEY (`pengajuan_lelang_id`) REFERENCES `pengajuan_lelangs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dokumen_perkaras`
--
ALTER TABLE `dokumen_perkaras`
  ADD CONSTRAINT `dokumen_perkaras_perkara_id_foreign` FOREIGN KEY (`perkara_id`) REFERENCES `perkaras` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `foto_barangs`
--
ALTER TABLE `foto_barangs`
  ADD CONSTRAINT `foto_barangs_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `laporan_lelangs`
--
ALTER TABLE `laporan_lelangs`
  ADD CONSTRAINT `laporan_lelangs_lelang_id_foreign` FOREIGN KEY (`lelang_id`) REFERENCES `lelangs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_lelangs_satker_id_foreign` FOREIGN KEY (`satker_id`) REFERENCES `satkers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `lelangs`
--
ALTER TABLE `lelangs`
  ADD CONSTRAINT `lelangs_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lelangs_pemenang_id_foreign` FOREIGN KEY (`pemenang_id`) REFERENCES `pembelis` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `penawarans`
--
ALTER TABLE `penawarans`
  ADD CONSTRAINT `penawarans_lelang_id_foreign` FOREIGN KEY (`lelang_id`) REFERENCES `lelangs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penawarans_pembeli_id_foreign` FOREIGN KEY (`pembeli_id`) REFERENCES `pembelis` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengajuan_lelangs`
--
ALTER TABLE `pengajuan_lelangs`
  ADD CONSTRAINT `pengajuan_lelangs_satker_id_foreign` FOREIGN KEY (`satker_id`) REFERENCES `satkers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `perkaras`
--
ALTER TABLE `perkaras`
  ADD CONSTRAINT `perkaras_pengajuan_lelang_id_foreign` FOREIGN KEY (`pengajuan_lelang_id`) REFERENCES `pengajuan_lelangs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `satkers`
--
ALTER TABLE `satkers`
  ADD CONSTRAINT `satkers_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_satker_id_foreign` FOREIGN KEY (`satker_id`) REFERENCES `satkers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
