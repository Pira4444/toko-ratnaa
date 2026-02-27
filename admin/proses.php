<?php
include '../koneksi.php';
session_start();

// Menampilkan error secara paksa jika ada masalah
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================
// --- A. LOGIKA PESANAN ---
// ==========================================

// 1. Hapus Pesanan
if(isset($_GET['hapus_pesanan'])){
    $id = $_GET['hapus_pesanan'];

    $cek_p = mysqli_query($conn, "SELECT bukti_transfer FROM pesanan WHERE id_pesanan = '$id'");
    $data_p = mysqli_fetch_assoc($cek_p);

    if($data_p){
        // Hapus bukti transfer jika ada (folder bukti ada di ../img/bukti/)
        if(!empty($data_p['bukti_transfer']) && file_exists("../img/bukti/".$data_p['bukti_transfer'])){
            unlink("../img/bukti/".$data_p['bukti_transfer']);
        }
        
        mysqli_query($conn, "DELETE FROM detail_pesanan WHERE id_pesanan = '$id'");
        mysqli_query($conn, "DELETE FROM pesanan WHERE id_pesanan = '$id'");
        
        echo "<script>alert('Pesanan Berhasil Dihapus!'); window.location='pesanan.php';</script>";
    }
    exit();
}

// 2. Update Status Pesanan & Resi
if(isset($_POST['update_status'])){
    $id_pesanan = $_POST['id_pesanan'];
    $status_baru = $_POST['status_baru'];
    $no_resi = mysqli_real_escape_string($conn, $_POST['no_resi']);

    $query = mysqli_query($conn, "UPDATE pesanan SET 
                status = '$status_baru', 
                no_resi = '$no_resi' 
                WHERE id_pesanan = '$id_pesanan'");

    if($query){
        echo "<script>alert('Status & Resi berhasil diperbarui!'); window.location='pesanan.php';</script>";
    }
    exit();
}


// ==========================================
// --- B. LOGIKA PRODUK ---
// ==========================================

// 1. Simpan Produk Baru
if(isset($_POST['simpan_produk'])){
    $id_kategori = $_POST['id_kategori']; 
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];
    
    $foto_nama = $_FILES['foto']['name'];
    $foto_baru = date('YmdHis')."_".$foto_nama;
    
    if(move_uploaded_file($_FILES['foto']['tmp_name'], '../img/'.$foto_baru)){
        $sql_produk = "INSERT INTO produk (id_kategori, nama_produk, harga, stok, foto) VALUES ('$id_kategori', '$nama', '$harga', '$stok', '$foto_baru')";
        
        if(mysqli_query($conn, $sql_produk)){
            $id_p = mysqli_insert_id($conn);

            // Simpan Varian jika ada
            if(isset($_POST['warna_varian'])){
                foreach($_POST['warna_varian'] as $key => $warna){
                    $warna_clean = mysqli_real_escape_string($conn, $warna);
                    $h_var = $_POST['harga_varian'][$key];
                    $nm_v = "";

                    if(!empty($_FILES['foto_varian']['name'][$key])){
                        $nm_v = date('YmdHis')."_v_".$_FILES['foto_varian']['name'][$key];
                        move_uploaded_file($_FILES['foto_varian']['tmp_name'][$key], '../img/'.$nm_v);
                    }
                    mysqli_query($conn, "INSERT INTO galeri_produk (id_produk, warna, harga_varian, foto_tambahan) VALUES ('$id_p', '$warna_clean', '$h_var', '$nm_v')");
                }
            }
            echo "<script>alert('Produk Berhasil Disimpan!'); window.location='admin.php';</script>";
        }
    }
    exit();
}

// 2. Update Produk & Varian (Final untuk Edit.php)
if(isset($_POST['update_produk'])){
    $id_produk = $_POST['id_produk'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Update Foto Utama jika diganti
    if ($_FILES['foto_utama']['name'] != "") {
        $foto = date('YmdHis')."_".$_FILES['foto_utama']['name'];
        move_uploaded_file($_FILES['foto_utama']['tmp_name'], "../img/" . $foto);
        mysqli_query($conn, "UPDATE produk SET nama_produk='$nama', id_kategori='$id_kategori', harga='$harga', stok='$stok', foto='$foto' WHERE id_produk='$id_produk'");
    } else {
        mysqli_query($conn, "UPDATE produk SET nama_produk='$nama', id_kategori='$id_kategori', harga='$harga', stok='$stok' WHERE id_produk='$id_produk'");
    }

    // Update Varian Lama
    if (isset($_POST['id_galeri_lama'])) {
        foreach ($_POST['id_galeri_lama'] as $key => $id_g) {
            $w_lama = mysqli_real_escape_string($conn, $_POST['warna_lama'][$key]);
            $h_lama = $_POST['harga_lama'][$key];
            mysqli_query($conn, "UPDATE galeri_produk SET warna='$w_lama', harga_varian='$h_lama' WHERE id_galeri='$id_g'");
        }
    }

    // Tambah Varian Baru
    if (isset($_POST['warna_varian_baru'])) {
        foreach ($_POST['warna_varian_baru'] as $key => $warna_baru) {
            $warna_baru_clean = mysqli_real_escape_string($conn, $warna_baru);
            $harga_baru = $_POST['harga_varian_baru'][$key];
            $foto_v = "";
            if ($_FILES['foto_varian_baru']['name'][$key] != "") {
                $foto_v = date('YmdHis')."_v_".$_FILES['foto_varian_baru']['name'][$key];
                move_uploaded_file($_FILES['foto_varian_baru']['tmp_name'][$key], "../img/" . $foto_v);
            }
            mysqli_query($conn, "INSERT INTO galeri_produk (id_produk, foto_tambahan, warna, harga_varian) VALUES ('$id_produk', '$foto_v', '$warna_baru_clean', '$harga_baru')");
        }
    }

    echo "<script>alert('Update Berhasil!'); window.location='admin.php';</script>";
    exit();
}

// 3. Hapus Satu Foto Varian
if(isset($_GET['hapus_foto'])){
    $id_gal = $_GET['hapus_foto'];
    $id_p   = $_GET['id_produk'];

    $cari = mysqli_query($conn, "SELECT foto_tambahan FROM galeri_produk WHERE id_galeri = '$id_gal'");
    $data = mysqli_fetch_assoc($cari);
    
    if($data && !empty($data['foto_tambahan']) && file_exists("../img/".$data['foto_tambahan'])){
        unlink("../img/".$data['foto_tambahan']);
    }
    mysqli_query($conn, "DELETE FROM galeri_produk WHERE id_galeri = '$id_gal'");
    header("location: edit.php?id=$id_p");
    exit();
}

// 4. Hapus Total Produk
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];

    // Hapus semua foto varian
    $res = mysqli_query($conn, "SELECT foto_tambahan FROM galeri_produk WHERE id_produk='$id'");
    while($g = mysqli_fetch_assoc($res)){ 
        if(!empty($g['foto_tambahan']) && file_exists("../img/".$g['foto_tambahan'])){
            unlink("../img/".$g['foto_tambahan']);
        }
    }
    mysqli_query($conn, "DELETE FROM galeri_produk WHERE id_produk='$id'");

    // Hapus foto utama
    $p_query = mysqli_query($conn, "SELECT foto FROM produk WHERE id_produk='$id'");
    $p = mysqli_fetch_assoc($p_query);
    if($p && !empty($p['foto']) && file_exists("../img/".$p['foto'])){
        unlink("../img/".$p['foto']);
    }

    mysqli_query($conn, "DELETE FROM produk WHERE id_produk='$id'");
    header("location: admin.php");
    exit();
}