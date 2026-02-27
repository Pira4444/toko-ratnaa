<?php 
ob_start();
session_start();
include '../koneksi.php'; 

if(!isset($_SESSION['login_admin'])){ header("location: login.php"); exit; }

$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id'");
$p = mysqli_fetch_assoc($query);

if(!$p) { echo "<script>alert('Data tidak ditemukan!'); window.location='admin.php';</script>"; exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Final Edit Produk - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background:#f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .varian-box { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; margin-bottom: 10px; transition: 0.3s; }
        .varian-box:hover { border-color: #4e73df; }
        .img-edit { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body class="py-4">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-primary m-0"><i class="fas fa-edit me-2"></i>Edit Produk & Varian</h4>
                    <a href="admin.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fas fa-times me-1"></i> Batal</a>
                </div>

                <form action="proses.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_produk" value="<?= $p['id_produk'] ?>">
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="fw-bold small mb-1">Nama Produk</label>
                                <input type="text" name="nama" class="form-control" value="<?= $p['nama_produk'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small mb-1">Kategori</label>
                                <select name="id_kategori" class="form-select" required>
                                    <?php 
                                    $kats = mysqli_query($conn, "SELECT * FROM navbar WHERE parent_id != 0");
                                    while($k = mysqli_fetch_assoc($kats)){
                                        $sel = ($k['id'] == $p['id_kategori']) ? "selected" : "";
                                        echo "<option value='{$k['id']}' $sel>{$k['nama_menu']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="fw-bold small mb-1">Harga Dasar</label>
                                    <input type="number" name="harga" class="form-control" value="<?= $p['harga'] ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold small mb-1">Stok</label>
                                    <input type="number" name="stok" class="form-control" value="<?= $p['stok'] ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small mb-1">Foto Utama</label><br>
                                <img src="../img/<?= $p['foto'] ?>" class="img-edit border mb-2 shadow-sm">
                                <input type="file" name="foto_utama" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="col-md-7 border-start ps-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold m-0 text-dark">Varian Saat Ini</h6>
                                <button type="button" id="btn-tambah-varian" class="btn btn-success btn-sm rounded-pill">
                                    <i class="fas fa-plus me-1"></i> Tambah Varian Baru
                                </button>
                            </div>

                            <div class="row g-2">
                                <?php 
                                $galeri = mysqli_query($conn, "SELECT * FROM galeri_produk WHERE id_produk = '$id'");
                                while($g = mysqli_fetch_assoc($galeri)){ ?>
                                    <div class="col-md-6">
                                        <div class="varian-box text-center shadow-sm">
                                            <img src="../img/<?= $g['foto_tambahan'] ?: $p['foto'] ?>" class="rounded mb-2" style="width:100%; height:80px; object-fit:cover;">
                                            <input type="hidden" name="id_galeri_lama[]" value="<?= $g['id_galeri'] ?>">
                                            <input type="text" name="warna_lama[]" class="form-control form-control-sm mb-1" value="<?= $g['warna'] ?>" placeholder="Warna">
                                            <input type="number" name="harga_lama[]" class="form-control form-control-sm mb-2" value="<?= $g['harga_varian'] ?>" placeholder="Harga">
                                            <a href="proses.php?hapus_foto=<?= $g['id_galeri'] ?>&id_produk=<?= $id ?>" class="btn btn-link btn-sm text-danger text-decoration-none p-0" onclick="return confirm('Hapus varian ini?')"><i class="fas fa-trash me-1"></i>Hapus</a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div id="container-varian-baru" class="mt-3"></div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" name="update_produk" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill">SIMPAN PERUBAHAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-tambah-varian');
    const container = document.getElementById('container-varian-baru');

    btn.onclick = function() {
        const div = document.createElement('div');
        div.className = 'varian-box bg-light border-primary border-opacity-25 shadow-sm p-3 mb-3';
        div.innerHTML = `
            <div class="d-flex justify-content-between mb-2">
                <span class="badge bg-primary">Varian Baru</span>
                <button type="button" class="btn-close small" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="small fw-bold">Foto</label>
                    <input type="file" name="foto_varian_baru[]" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Warna/Nama</label>
                    <input type="text" name="warna_varian_baru[]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Harga</label>
                    <input type="number" name="harga_varian_baru[]" class="form-control form-control-sm" required>
                </div>
            </div>
        `;
        container.appendChild(div);
    };
});
</script>
</body>
</html>