<?php
session_start();

// 1. Panggil Koneksi Database (Karena koneksi lu ada di folder config)
include 'config/koneksi.php';

// Cek apakah user beneran udah login
if ($_SESSION['status_login'] != "sudah_login") {
    header("location: login.php");
    exit();
}

// 2. Nangkep Data dari Form
$nis = $_POST['nis'];
$kategori = $_POST['kategori'];
$isi = $_POST['isi_laporan']; // Sesuai dengan atribut name="isi_laporan" di form.php
$status = 'Proses'; // Default awal kalau laporan baru masuk pasti "Proses"

// 3. Urusan Upload Foto
$nama_foto = ""; // Default kosong kalau user ga mau upload foto

// Cek apakah ada file foto yang diupload
if ($_FILES['foto_laporan']['name'] != '') {
    $foto_awal = $_FILES['foto_laporan']['name'];
    $tmp_file = $_FILES['foto_laporan']['tmp_name'];
    
    // Rename nama fotonya dikasih angka waktu biar ga bentrok kalau ada yg namanya sama
    $nama_foto = time() . '_' . $foto_awal;
    
    // Tentukan alamat lemari simpannya
    $alamat_simpan = "assets/img/laporan/" . $nama_foto;
    
    // Pindahin file fotonya ke folder
    move_uploaded_file($tmp_file, $alamat_simpan);
}

// 4. Masukin Datanya ke Brankas (MySQL)
// Catatan: kolom 'kelas' kita kosongin ('') karena di form ga ada, dan 'tanggal' otomatis diisi MySQL
$query = "INSERT INTO pengaduan (nis, kelas, kategori, isi, status, foto) 
          VALUES ('$nis', '', '$kategori', '$isi', '$status', '$nama_foto')";

$simpan = mysqli_query($koneksi, $query);

// 5. Lempar ke Halaman Riwayat kalau sukses!
if ($simpan) {
    header("location: riwayat.php");
} else {
    // Kalau gagal, paksa PHP ngasih tau errornya apa
    echo "Gagal ngirim laporan boss: " . mysqli_error($koneksi);
}
?>