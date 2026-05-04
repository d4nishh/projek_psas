<?php
// 1. Wajib panggil session untuk menyalakan fitur "satpam" (harus paling atas)
session_start();

// 2. Hubungkan ke database 
// (Karena koneksi.php ada di folder yang sama, cukup panggil nama filenya)
include 'koneksi.php';

// 3. Tangkap data dari form login.php
// Pastikan namanya sesuai dengan atribut name="..." di input HTML kamu
$nis = $_POST['nis'];
$password = $_POST['password'];

// 4. Cek kecocokan data ke database
// (Kita asumsikan nama tabelmu 'siswa' dan variabel koneksimu '$koneksi' atau '$conn')
// Ganti $koneksi menjadi $conn jika di file koneksi.php kamu pakai $conn
$query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$nis' AND password='$password'");

// 5. Hitung apakah ada data yang cocok
$cek = mysqli_num_rows($query);

if ($cek > 0) {
    // Kalau NIS dan Password BENAR
    $data = mysqli_fetch_assoc($query);
    
    // Berikan "gelang" session agar sistem ingat siapa yang login
    $_SESSION['status_login'] = "sudah_login";
    $_SESSION['nis'] = $data['nis']; 
    $_SESSION['nama'] = $data['nama'];
    
    // Arahkan ke homepage 
    // (Pakai ../ karena kita sedang di dalam folder config, jadi harus mundur 1 langkah)
    header("location: ../homepage.php");
    exit();

} else {
    // Kalau NIS atau Password SALAH
    // Tendang balik ke halaman login dan kirim pesan error 'gagal'
    header("location: ../login.php?pesan=gagal");
    exit();
}
?>