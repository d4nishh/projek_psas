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
           <div style="margin-bottom: 5px;">
            <img src="assets/css/img/logo-telkom-schools.png" alt="Telkom Schools" style="height: 40px; width: auto; object-fit: contain;">
            </div>
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

<div class="input-group"> <label style="display: block; font-weight: bold; margin-bottom: 8px;">Kata Sandi</label>
    
    <div style="position: relative;">
        <input type="password" id="password_input" name="password" placeholder="Masukkan Kata Sandi" style="width: 100%; padding: 14px 45px 14px 16px; border-radius: 12px; border: 1px solid #E5E7EB; background: #F9FAFB; font-size: 14px; box-sizing: border-box;" required>
        
        <span id="toggle_password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #9CA3AF; display: flex; align-items: center;">
            <svg id="eye_icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </span>
    </div>
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
    <script>
    const togglePassword = document.getElementById('toggle_password');
    const passwordInput = document.getElementById('password_input');
    const eyeIcon = document.getElementById('eye_icon');

    togglePassword.addEventListener('click', function () {
        // Cek tipe input saat ini (password atau text)
        const isPassword = passwordInput.getAttribute('type') === 'password';
        
        // Ubah tipe inputnya
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        
        // Ubah gambar ikon matanya (terbuka vs dicoret)
        if (isPassword) {
            // SVG Mata Dicoret (Sembunyikan)
            eyeIcon.innerHTML = '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>';
        } else {
            // SVG Mata Terbuka (Lihat)
            eyeIcon.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    });
</script>
</body>
</html>