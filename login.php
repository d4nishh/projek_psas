<?php
session_start();
include 'config/koneksi.php';

// Satpam Pengecek Gelang Tiket
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == "sudah_login") {
    header("location: homepage.php");
    exit();
}

if (isset($_POST['login'])) {
    $role = $_POST['role']; // Nangkep role dari hidden input
    $identifier = mysqli_real_escape_string($koneksi, $_POST['identifier']);
    $password = $_POST['password'];

    if ($role == 'siswa') {
        $query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$identifier' AND password='$password'");
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_array($query);
            $_SESSION['status_login'] = "sudah_login";
            $_SESSION['role'] = "siswa";
            $_SESSION['nis'] = $data['nis'];
            $_SESSION['nama'] = $data['nama'];
            header("location: homepage.php");
        } else {
            header("location: login.php?pesan=gagal");
        }
    } else if ($role == 'guru') {
        $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE nis_guru='$identifier' AND password='$password'");
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_array($query);
            $_SESSION['status_login'] = "sudah_login";
            $_SESSION['role'] = "guru";
            $_SESSION['nis_guru'] = $data['nis_guru'];
            $_SESSION['nama'] = isset($data['nama']) ? $data['nama'] : "Bapak/Ibu Guru"; 
            header("location: homepage.php");
        } else {
            header("location: login.php?pesan=gagal");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aspirasi Siswa</title>
    
    <!-- Import Font Premium (Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Trik maksa browser baca CSS baru -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
</head>
<body>
    

    <div class="mobile-container">
        
        <div class="login-header">
            <!-- Bisa diganti pakai logo sekolah beneran nanti pakai tag <img> -->
            <div style="font-size: 32px; margin-bottom: 15px;">🏫</div>
            <h1>Masuk Akun</h1>
            <p>Silakan masuk dengan NIS Anda untuk mulai menyampaikan aspirasi.</p>
        </div>

        <!-- Form Login dalam Card Putih -->
        <div class="login-form-card">
            
            <?php 
            // Menampilkan pesan error dengan desain alert modern
            if(isset($_GET['pesan'])){
                echo '<div class="alert-error">⚠️ ';
                if($_GET['pesan'] == "gagal"){
                    echo "NIS atau Password salah.";
                } else if($_GET['pesan'] == "belum_login"){
                    echo "Kamu harus login terlebih dahulu.";
                }
                echo '</div>';
            }
            ?>

            <!-- Action dikirim ke proses_login.php di dalam folder config -->
           <form action="config/proses_login.php" method="POST" style="display: flex; flex-direction: column; gap: 20px;">

    <input type="hidden" name="role" id="inputRole" value="siswa">

    <div class="role-selector">
        <div class="role-card active" id="cardSiswa" onclick="pilihRole('siswa')">
            <span class="role-icon">🧑‍🎓</span>
            <span class="role-text">Siswa</span>
        </div>
        <div class="role-card" id="cardGuru" onclick="pilihRole('guru')">
            <span class="role-icon">👨‍🏫</span>
            <span class="role-text">Guru</span>
        </div>
    </div>

    <div class="input-group">
        <label for="nis" id="labelNis">Nomor Induk Siswa</label>
        <input type="number" id="nis" name="nis" placeholder="Contoh: 1002938" required autocomplete="off">
    </div>

    <div class="input-group">
        <label for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
    </div>

    <button type="submit" name="login" class="btn-login">Masuk ke Sistem</button>

</form>

<script>
    function pilihRole(role) {
        // Set value input hidden buat dikirim ke PHP
        document.getElementById('inputRole').value = role;

        // Bersihin class 'active' dari kedua kotak
        document.getElementById('cardSiswa').classList.remove('active');
        document.getElementById('cardGuru').classList.remove('active');

        // Logika Morphing (Ubah Teks Sesuai ID yang bener)
        if (role === 'siswa') {
            document.getElementById('cardSiswa').classList.add('active');
            // ID disamain sama HTML di atas
            document.getElementById('labelNis').innerText = 'Nomor Induk Siswa';
            document.getElementById('nis').placeholder = 'Contoh: 1002938';
        } else {
            document.getElementById('cardGuru').classList.add('active');
            // ID disamain sama HTML di atas
            document.getElementById('labelNis').innerText = 'Nomor Induk Guru';
            document.getElementById('nis').placeholder = 'Masukkan Nomor Induk Guru';
        }
    }
</script>
                
            </form>
        </div>

    </div>

</body>
</html>