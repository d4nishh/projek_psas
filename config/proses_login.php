<?php
session_start();

// Panggil file koneksi (karena proses_login.php dan koneksi.php sama-sama di dalam folder config, gausah pake folder lagi)
include 'koneksi.php'; 

// Nangkep data dari form login.php
$role = $_POST['role'];
$nis_input = $_POST['nis']; // Nangkep dari name="nis"
$password = $_POST['password'];

if ($role == 'siswa') {
    
    // Logika Siswa
    $query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$nis_input' AND password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_array($query);
        $_SESSION['status_login'] = "sudah_login";
        $_SESSION['role'] = "siswa";
        $_SESSION['nis'] = $data['nis'];
        $_SESSION['nama'] = $data['nama'];
        
        // Sukses? Keluar dari folder config (../), lempar ke homepage.php
        header("location: ../homepage.php");
    } else {
        // Gagal? Keluar dari folder config (../), balik ke login.php bawa pesan
        header("location: ../login.php?pesan=gagal");
    }

} else if ($role == 'guru') {
    
    // Logika Guru (Admin)
    $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE nis_guru='$nis_input' AND password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_array($query);
        $_SESSION['status_login'] = "sudah_login";
        $_SESSION['role'] = "guru";
        $_SESSION['nis_guru'] = $data['nis_guru'];
        $_SESSION['nama'] = $data['nama'];
        
        // Sukses? Keluar dari folder config (../), lempar ke homepage.php
        header("location: ../homepage.php");
    } else {
        header("location: ../login.php?pesan=gagal");
    }
}
?>