<?php
// 1. Paksa tampilkan error agar tidak layar putih
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();
include '../koneksi.php';

// 2. Cek Session (Samakan dengan login_admin)
if (!isset($_SESSION['login_admin'])) {
    die("Akses ditolak! Anda belum login sebagai admin.");
}

if (isset($_GET['id'])) {
    // Sanitasi ID
    $id_pesanan = mysqli_real_escape_string($conn, $_GET['id']);

    // 3. Ambil data foto (Cek apakah kolomnya benar 'bukti_transfer')
    // Jika nama kolom di tabel kamu berbeda, ganti 'bukti_transfer' di bawah ini
    $query_foto = mysqli_query($conn, "SELECT bukti_transfer FROM pesanan WHERE id_pesanan = '$id_pesanan'");
    
    if (!$query_foto) {
        die("Error Query Foto: " . mysqli_error($conn));
    }

    $data_foto = mysqli_fetch_assoc($query_foto);

    // 4. Hapus File fisik jika ada
    if ($data_foto && !empty($data_foto['bukti_transfer'])) {
        $path = "../img/bukti/" . $data_foto['bukti_transfer'];
        if (file_exists($path)) {
            if (!unlink($path)) {
                // Log jika gagal hapus file, tapi biarkan proses database lanjut
                error_log("Gagal menghapus file: " . $path);
            }
        }
    }

    // 5. Hapus dari Database
    $delete = mysqli_query($conn, "DELETE FROM pesanan WHERE id_pesanan = '$id_pesanan'");

    if ($delete) {
        echo "<script>
                alert('Pesanan #$id_pesanan berhasil dihapus!');
                window.location.href='pesanan.php';
              </script>";
    } else {
        die("Gagal menghapus data di database: " . mysqli_error($conn));
    }

} else {
    header("Location: pesanan.php");
    exit;
}