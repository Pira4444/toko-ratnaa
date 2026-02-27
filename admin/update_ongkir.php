<?php
include '../koneksi.php';

if(isset($_POST['proses_ongkir'])){
    $id = $_POST['id_pesanan'];
    $ongkir_baru = (int)$_POST['ongkir'];

    // 1. Ambil total_bayar lama (yang masih harga produk saja)
    $query = mysqli_query($conn, "SELECT total_bayar FROM pesanan WHERE id_pesanan = '$id'");
    $data = mysqli_fetch_assoc($query);
    
    // 2. Hitung total baru (Produk + Ongkir)
    $total_baru = $data['total_bayar'] + $ongkir_baru;

    // 3. Update database
    $update = mysqli_query($conn, "UPDATE pesanan SET 
        ongkir = '$ongkir_baru', 
        total_bayar = '$total_baru', 
        status = 'Menunggu Pembayaran' 
        WHERE id_pesanan = '$id'");

    if($update){
        echo "<script>alert('Ongkir berhasil diupdate!'); window.location='pesanan.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($conn);
    }
}
?>