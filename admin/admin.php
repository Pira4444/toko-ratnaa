<?php
// Pastikan tidak ada spasi atau baris kosong di atas tag PHP ini
ob_start();
session_start();

// 1. Tampilkan error agar tidak putih polos jika ada masalah
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../koneksi.php'; 

// 2. Proteksi Halaman - Samakan dengan kunci 'login_admin'
if (!isset($_SESSION['login_admin'])) {
    header("Location: login.php");
    exit;
}

// 3. Set Zona Waktu
date_default_timezone_set('Asia/Jakarta');

// 4. Hitung Statistik
$q_notif = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Menunggu Pembayaran'");
$pesanan_baru = ($q_notif) ? mysqli_fetch_assoc($q_notif)['total'] : 0;

$total_p = mysqli_num_rows(mysqli_query($conn, "SELECT id_produk FROM produk"));
$total_order = mysqli_num_rows(mysqli_query($conn, "SELECT id_pesanan FROM pesanan"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Toko Ana</title>
    <meta http-equiv="refresh" content="120">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4e73df; --dark: #224abe; --success: #1cc88a; }
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; }
        .sidebar { background: linear-gradient(180deg, var(--primary) 10%, var(--dark) 100%); min-height: 100vh; color: white; z-index: 100; }
        .nav-link { color: rgba(255,255,255,.8); font-weight: 500; padding: 1rem; border-radius: 10px; margin-bottom: 5px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,.15); }
        .card-stat { border-left: 4px solid var(--primary); border-radius: 10px; transition: 0.3s; }
        .card-stat:hover { transform: translateY(-5px); }
        .img-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
        .badge-notif { font-size: 10px; padding: 4px 6px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 d-none d-md-block sidebar p-3 position-fixed">
            <h5 class="text-center fw-bold mb-4 mt-2"><i class="fas fa-store me-2"></i>ANA ADMIN</h5>
            <hr>
            <nav class="nav flex-column">
                <a class="nav-link active" href="admin.php"><i class="fas fa-box me-2"></i> Produk</a>
                <a class="nav-link d-flex justify-content-between align-items-center" href="pesanan.php">
                    <span><i class="fas fa-shopping-cart me-2"></i> Pesanan</span>
                    <?php if($pesanan_baru > 0): ?>
                        <span class="badge bg-danger rounded-pill badge-notif"><?= $pesanan_baru ?></span>
                    <?php endif; ?>
                </a>
                <a class="nav-link" href="../index.php" target="_blank"><i class="fas fa-eye me-2"></i> Lihat Toko</a>
                <a class="nav-link text-danger mt-5" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </nav>
        </div>

        <div class="col-md-10 offset-md-2 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-gray-800">Dashboard</h3>
                <div class="user-info">
                    <span class="text-muted me-2 small">Admin: <b><?= $_SESSION['admin_data']['nama'] ?? 'Administrator' ?></b></span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="35">
                </div>
            </div>

            <?php if($pesanan_baru > 0): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-bell fa-shake me-3 fa-lg text-danger"></i>
                <div>
                    Ada <strong><?= $pesanan_baru ?> pesanan baru</strong> masuk.
                    <a href="pesanan.php" class="alert-link ms-2 text-decoration-none">Lihat Detail &rarr;</a>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="text-primary small fw-bold text-uppercase mb-1">Total Produk</div>
                        <div class="h5 mb-0 fw-bold"><?= $total_p ?> Item</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stat shadow-sm p-3" style="border-left-color: #1cc88a;">
                        <div class="text-success small fw-bold text-uppercase mb-1">Total Pesanan</div>
                        <div class="h5 mb-0 fw-bold"><?= $total_order ?> Order</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Manajemen Barang</h6>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#tambah">
                        <i class="fas fa-plus me-1"></i> Tambah Produk
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Foto</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $data = mysqli_query($conn, "SELECT * FROM produk ORDER BY id_produk DESC");
                            while($row = mysqli_fetch_assoc($data)){ 
                                $id_p = $row['id_produk'];
                                $q_galeri = mysqli_query($conn, "SELECT id_galeri FROM galeri_produk WHERE id_produk = $id_p");
                                $jml_v = mysqli_num_rows($q_galeri);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="position-relative d-inline-block">
                                        <img src="../img/<?= $row['foto'] ?>" class="img-thumb border" onerror="this.src='https://placehold.co/50'">
                                        <?php if($jml_v > 0): ?>
                                            <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 9px;"><?= $jml_v ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="fw-bold"><?= $row['nama_produk'] ?></td>
                                <td class="text-success fw-bold">Rp <?= number_format($row['harga']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= $row['stok'] ?> Unit</span></td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $row['id_produk'] ?>" class="btn btn-sm btn-outline-warning border-0"><i class="fas fa-edit"></i></a>
                                    <a href="proses.php?hapus=<?= $row['id_produk'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="fw-bold m-0">Tambah Produk & Varian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="small fw-bold">Pilih Kategori (Sub-Menu)</label>
                            <select name="id_kategori" class="form-select mb-2" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php 
                                $kats = mysqli_query($conn, "SELECT * FROM navbar WHERE parent_id != 0 ORDER BY nama_menu ASC");
                                while($k = mysqli_fetch_assoc($kats)){
                                    echo "<option value='{$k['id']}'>{$k['nama_menu']}</option>";
                                }
                                ?>
                            </select>

                            <label class="small fw-bold">Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-control mb-2" required>
                            <label class="small fw-bold">Harga Dasar</label>
                            <input type="number" name="harga" class="form-control mb-2" required>
                            <label class="small fw-bold">Stok</label>
                            <input type="number" name="stok" class="form-control mb-2" required>
                            <label class="small fw-bold">Foto Utama</label>
                            <input type="file" name="foto" class="form-control" required>
                        </div>
                        
                        <div class="col-md-7 border-start">
                            <label class="small fw-bold d-block mb-2">Varian (Foto | Nama | Harga)</label>
                            <div id="container-varian">
                                <div class="row g-1 mb-2 align-items-center">
                                    <div class="col-4">
                                        <input type="file" name="foto_varian[]" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" name="warna_varian[]" class="form-control form-control-sm" placeholder="Nama Warna">
                                    </div>
                                    <div class="col-3">
                                        <input type="number" name="harga_varian[]" class="form-control form-control-sm" placeholder="Harga">
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" class="btn btn-sm btn-primary btn-tambah-varian"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" name="simpan_produk" class="btn btn-primary px-4 w-100">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelector('.btn-tambah-varian').addEventListener('click', function() {
    const container = document.getElementById('container-varian');
    const newItem = document.createElement('div');
    newItem.className = 'row g-1 mb-2 align-items-center';
    newItem.innerHTML = `
        <div class="col-4">
            <input type="file" name="foto_varian[]" class="form-control form-control-sm">
        </div>
        <div class="col-4">
            <input type="text" name="warna_varian[]" class="form-control form-control-sm" placeholder="Nama Warna">
        </div>
        <div class="col-3">
            <input type="number" name="harga_varian[]" class="form-control form-control-sm" placeholder="Harga">
        </div>
        <div class="col-1 text-end">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
    `;
    container.appendChild(newItem);
}); // Menutup fungsi addEventListener
</script>
</body>
</html>