<?php 
ob_start();
session_start();
include '../koneksi.php'; 

// Samakan kuncinya dengan file login & admin (login_admin)
if (!isset($_SESSION['login_admin'])) { 
    header("location: login.php"); 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Order Management - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
        .card-main { border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .table thead { background: #f8f9fa; border-bottom: 2px solid #edf2f7; }
        .table thead th { font-weight: 600; color: #4a5568; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 15px; }
        
        /* Status Badges */
        .badge-status { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.7rem; display: inline-block; }
        .status-menunggu { background: #fff8e1; color: #b7791f; }
        .status-bayar { background: #e6fffa; color: #2c7a7b; }
        .status-kirim { background: #ebf8ff; color: #2b6cb0; }
        .status-selesai { background: #f0fff4; color: #2f855a; }

        .bukti-preview { 
            width: 45px; height: 45px; object-fit: cover; border-radius: 10px; 
            cursor: pointer; border: 2px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: 0.2s;
        }
        .bukti-preview:hover { transform: scale(1.1); }

        /* Unified Form Styling */
        .action-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; min-width: 320px; }
        .form-control-custom { font-size: 0.85rem; border-radius: 6px; border: 1px solid #e2e8f0; padding: 5px 10px; }
        .form-control-custom:focus { border-color: #3182ce; box-shadow: none; }
        
        .btn-update { padding: 5px 15px; font-weight: 600; font-size: 0.8rem; border-radius: 6px; }
        
        .customer-info h6 { font-size: 0.9rem; font-weight: 700; margin-bottom: 2px; color: #2d3748; }
        .customer-info p { font-size: 0.75rem; color: #718096; margin-bottom: 0; }
        
        .wa-link { color: #38a169; text-decoration: none; font-weight: 600; font-size: 0.8rem; }
        .wa-link:hover { color: #2f855a; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Manajemen Pesanan</h3>
            <p class="text-muted small">Kelola status, ongkir, dan pengiriman pelanggan Anda.</p>
        </div>
        <a href="admin.php" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
            <i class="fas fa-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="card card-main shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Order / Tgl</th>
                        <th>Pelanggan</th>
                        <th>Pembayaran</th>
                        <th class="text-center">Bukti</th>
                        <th>Proses Pesanan (Ongkir, Resi, Status)</th>
                        <th class="text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY id_pesanan DESC");
                    while($r = mysqli_fetch_assoc($q)){ 
                        // Penentuan class warna status
                        $status_class = '';
                        if($r['status'] == 'Menunggu Ongkir' || $r['status'] == 'Menunggu Pembayaran') $status_class = 'status-menunggu';
                        elseif($r['status'] == 'Sudah Bayar') $status_class = 'status-bayar';
                        elseif($r['status'] == 'Dikirim') $status_class = 'status-kirim';
                        elseif($r['status'] == 'Selesai') $status_class = 'status-selesai';
                    ?>
                    <tr>
                        <td class="ps-4">
                            <span class="text-dark fw-bold">#ORD-<?= $r['id_pesanan'] ?></span><br>
                            <small class="text-muted"><?= date('d M Y', strtotime($r['tgl_pesan'])) ?></small>
                        </td>
                        <td>
                            <div class="customer-info">
                                <h6><?= $r['nama_pembeli'] ?></h6>
                                <p class="text-truncate" style="max-width: 180px;" title="<?= $r['alamat'] ?>"><?= $r['alamat'] ?></p>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $r['telepon']) ?>" target="_blank" class="wa-link">
                                    <i class="fab fa-whatsapp me-1"></i>Chat WhatsApp
                                </a>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">Rp <?= number_format($r['total_bayar']) ?></span><br>
                            <span class="badge bg-light text-dark border small mt-1"><?= $r['metode_pembayaran'] ?></span>
                        </td>
                        <td class="text-center">
                            <?php if(!empty($r['bukti_transfer'])): ?>
                                <img src="../img/bukti/<?= $r['bukti_transfer'] ?>" class="bukti-preview" data-bs-toggle="modal" data-bs-target="#modalBukti<?= $r['id_pesanan'] ?>">
                                
                                <div class="modal fade" id="modalBukti<?= $r['id_pesanan'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                            <div class="modal-header border-0">
                                                <h6 class="fw-bold m-0">Bukti Transfer: <?= $r['nama_pembeli'] ?></h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-0 text-center bg-light">
                                                <img src="../img/bukti/<?= $r['bukti_transfer'] ?>" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-card shadow-sm">
                                <form action="proses_order_bersama.php" method="POST">
                                    <input type="hidden" name="id_pesanan" value="<?= $r['id_pesanan'] ?>">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="small fw-bold text-muted mb-1">Ongkos Kirim</label>
                                            <input type="number" name="ongkir" class="form-control form-control-custom" placeholder="Rp" value="<?= $r['ongkir'] ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small fw-bold text-muted mb-1">Nomor Resi</label>
                                            <input type="text" name="no_resi" class="form-control form-control-custom" placeholder="Resi Kurir" value="<?= $r['no_resi'] ?>">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="small fw-bold text-muted mb-1">Status Pesanan</label>
                                            <div class="d-flex gap-1">
                                                <select name="status" class="form-select form-select-sm form-control-custom flex-grow-1">
                                                    <option value="Menunggu Ongkir" <?= $r['status'] == 'Menunggu Ongkir' ? 'selected' : '' ?>>Wait Ongkir</option>
                                                    <option value="Menunggu Pembayaran" <?= $r['status'] == 'Menunggu Pembayaran' ? 'selected' : '' ?>>Wait Bayar</option>
                                                    <option value="Sudah Bayar" <?= $r['status'] == 'Sudah Bayar' ? 'selected' : '' ?>>Lunas / Proses</option>
                                                    <option value="Dikirim" <?= $r['status'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                                    <option value="Selesai" <?= $r['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                                </select>
                                                <button type="submit" name="update_all" class="btn btn-primary btn-update" title="Simpan Perubahan">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="mt-2 px-1">
                                <span class="badge-status <?= $status_class ?>"><i class="fas fa-circle me-1 small" style="font-size: 8px;"></i> <?= $r['status'] ?></span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="cetak_struk.php?id=<?= $r['id_pesanan'] ?>" target="_blank" class="btn btn-light btn-sm rounded-3 border" title="Cetak Struk">
                                    <i class="fas fa-print text-dark"></i>
                                </a>
                                <a href="hapus_pesanan.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-light btn-sm rounded-3 border text-danger" onclick="return confirm('Hapus data?')" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>