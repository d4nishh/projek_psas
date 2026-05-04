<?php
// Mulai session untuk ngecek apakah user udah login atau belum
session_start();

// Kalau udah login, nggak usah ke halaman login lagi, langsung tendang ke Home
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == "sudah_login") {
    header("location: homepage.php");
    exit();
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
                
                <div class="input-group">
                    <label for="nis">Nomor Induk Siswa</label>
                    <input type="number" id="nis" name="nis" placeholder="Contoh: 1002938" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
                </div>

                <button type="submit" class="btn-login">Masuk ke Sistem</button>
                
            </form>
        </div>

    </div>

</body>
</html>