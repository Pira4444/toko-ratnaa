<?php
include '../koneksi.php';
session_start();

if(isset($_POST['update_all'])){
    $id = $_POST['id_pesanan'];
    $ongkir_baru = (int)$_POST['ongkir'];
    $resi = mysqli_real_escape_string($conn, $_POST['no_resi']);
    $status = $_POST['status'];

    // 1. Ambil data pesanan yang sekarang (untuk tahu total_bayar dan ongkir lama)
    $query_lama = mysqli_query($conn, "SELECT total_bayar, ongkir FROM pesanan WHERE id_pesanan = '$id'");
    $data_lama = mysqli_fetch_assoc($query_lama);

    $total_bayar_lama = $data_lama['total_bayar'];
    $ongkir_lama = $data_lama['ongkir'];

    // 2. Hitung harga produk murni (Tanpa Ongkir)
    $harga_produk_saja = $total_bayar_lama - $ongkir_lama;

    // 3. Hitung Total Bayar Baru (Harga Produk + Ongkir Baru)
    $total_final_baru = $harga_produk_saja + $ongkir_baru;

    // 4. Update ke Database
    $update = mysqli_query($conn, "UPDATE pesanan SET 
                ongkir = '$ongkir_baru', 
                no_resi = '$resi', 
                status = '$status',
                total_bayar = '$total_final_baru' 
                WHERE id_pesanan = '$id'");

    if($update){
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location='pesanan.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>