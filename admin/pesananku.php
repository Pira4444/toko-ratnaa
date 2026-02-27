<?php
include '../koneksi.php';
session_start();
if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }

$id_user = $_SESSION['user']['id_user'] ?? $_SESSION['user']['id'];
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY id_pesanan DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pesanan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3>Pesanan Saya</h3>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td>#<?= $r['id_pesanan'] ?></td>
                    <td><?= $r['tgl_pesan'] ?></td>
                    <td>
                        <?php if($r['status'] == 'Menunggu Ongkir'): ?>
                            <span class="text-muted">Menghitung Ongkir...</span>
                        <?php else: ?>
                            <strong>Rp <?= number_format($r['total_bayar']) ?></strong>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge <?= ($r['status'] == 'Menunggu Ongkir') ? 'bg-warning' : 'bg-primary' ?>">
                            <?= $r['status'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if($r['status'] == 'Menunggu Ongkir'): ?>
                            <small class="text-muted">Tunggu admin input ongkir</small>
                        <?php elseif($r['status'] == 'Menunggu Pembayaran'): ?>
                            <a href="bayar.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-sm btn-success">Upload Bukti Transfer</a>
                        <?php else: ?>
                            <span class="text-success small">Sudah Diproses</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>