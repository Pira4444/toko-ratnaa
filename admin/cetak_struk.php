<?php 
include '../koneksi.php'; 
session_start();
if(!isset($_SESSION['login'])){ header("location: login.php"); exit; }

// Ambil ID dari URL
$id = isset($_GET['id']) ? $_GET['id'] : '';
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_pesanan = '$id'");
$d = mysqli_fetch_assoc($query);

if(!$d) { 
    echo "<script>alert('Data tidak ditemukan!'); window.location='manajemen_pesanan.php';</script>"; 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pesanan #<?= $d['id_pesanan'] ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; color: #000; background: #fff; margin: 0; padding: 20px; font-size: 13px; }
        .struk-container { max-width: 400px; margin: auto; border: 1px solid #eee; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 18px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .line { border-top: 1px dashed #000; margin: 10px 0; }
        .item-row { margin-bottom: 10px; line-height: 1.4; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; }
        .resi-box { border: 1px solid #000; padding: 8px; text-align: center; margin: 10px 0; font-weight: bold; background: #f9f9f9; }
        
        @media print {
            .no-print { display: none; }
            .struk-container { border: none; padding: 0; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="text-align:center; margin-bottom: 20px;">
    <button onclick="window.print()" style="padding: 10px 20px; cursor:pointer; font-weight:bold;">CETAK STRUK</button>
    <a href="pesanan.php" style="margin-left:10px; text-decoration:none; color:blue;"> Kembali ke Pesanan</a>
</div>

<div class="struk-container">
    <div class="header">
        <h2>TOKO ANA</h2>
        <p>Jl. Ahmad Yani No. 29, Subang<br>WhatsApp: +62895-0196-5182</p>
    </div>

    <div class="info-row">
        <span>No. Order:</span>
        <span style="font-weight:bold;">#ORD-<?= $d['id_pesanan'] ?></span>
    </div>
    <div class="info-row">
        <span>Tanggal:</span>
        <span><?= date('d/m/Y H:i', strtotime($d['tgl_pesan'])) ?></span>
    </div>
    <div class="info-row">
        <span>Pelanggan:</span>
        <span><?= $d['nama_pembeli'] ?></span>
    </div>
    <div class="info-row">
        <span>Metode:</span>
        <span><?= $d['metode_pembayaran'] ?></span>
    </div>

    <div class="line"></div>

    <?php if(!empty($d['no_resi'])): ?>
    <div class="resi-box">
        NO. RESI PENGIRIMAN:<br>
        <span style="font-size: 16px; letter-spacing: 1px;"><?= $d['no_resi'] ?></span>
    </div>
    <?php endif; ?>

    <div class="item-row">
    <div style="font-weight: bold; margin-bottom: 5px; text-transform: uppercase; font-size: 11px;">
        Tujuan Pengiriman:
    </div>
    
    <div style="line-height: 1.6; font-size: 13px;">
        <div style="margin-bottom: 2px;">
            <strong><?= $d['nama_pembeli'] ?></strong> (<?= $d['telepon'] ?>)
        </div>
        
        <div style="border-left: 2px solid #000; padding-left: 8px;">
            <?= nl2br($d['alamat']) ?>
        </div>
    </div>
</div>

    <div class="line"></div>

    <div class="info-row">
        <span>Total Produk:</span>
        <span>Rp <?= number_format($d['total_bayar'] - $d['ongkir']) ?></span>
    </div>
    <div class="info-row">
        <span>Ongkos Kirim:</span>
        <span>Rp <?= number_format($d['ongkir']) ?></span>
    </div>
    <div class="info-row" style="font-weight: bold; font-size: 15px; margin-top: 5px;">
        <span>TOTAL BAYAR:</span>
        <span>Rp <?= number_format($d['total_bayar']) ?></span>
    </div>

    <div class="line"></div>

    <div class="info-row">
        <span>Status Pesanan:</span>
        <span style="text-transform: uppercase; font-weight: bold;"><?= $d['status'] ?></span>
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja di Toko Ana!<br>Semoga barangnya bermanfaat dan awet.</p>
        <p style="font-size: 9px; color: #666;"><?= date('d-m-Y H:i:s') ?></p>
    </div>
</div>

</body>
</html>