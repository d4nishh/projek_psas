<?php
session_start();
include 'config/koneksi.php';

// Satpam Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location: login.php");
    exit();
}

$role = $_SESSION['role'];
// Tentukan tabel dan kolom berdasarkan role yang lagi login
if ($role == 'siswa') {
    $tabel = 'siswa';
    $kolom_id = 'nis';
    $identifier = $_SESSION['nis'];
} else {
    $tabel = 'admin';
    $kolom_id = 'nis_guru';
    $identifier = $_SESSION['nis_guru'];
}

$pesan = "";

// Jika tombol Simpan ditekan
if (isset($_POST['ubah_password'])) {
    $pass_lama = $_POST['password_lama'];
    $pass_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // 1. Cek dulu password lama di database
    $query = mysqli_query($koneksi, "SELECT password FROM $tabel WHERE $kolom_id='$identifier'");
    $data = mysqli_fetch_array($query);

    if ($data['password'] == $pass_lama) {
        // 2. Kalau password lama bener, cek konfirmasi password baru
        if ($pass_baru == $konfirmasi) {
            // 3. Kalau cocok, Update ke database!
            $update = mysqli_query($koneksi, "UPDATE $tabel SET password='$pass_baru' WHERE $kolom_id='$identifier'");
            if ($update) {
                $pesan = "<div class='alert-sukses'>Password berhasil diperbarui!</div>";
            } else {
                $pesan = "<div class='alert-gagal'>Sistem error, gagal mengubah password.</div>";
            }
        } else {
            $pesan = "<div class='alert-gagal'>Konfirmasi password baru tidak cocok!</div>";
        }
    } else {
        $pesan = "<div class='alert-gagal'>Password lama yang kamu masukkan salah!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password - Aspirasi Siswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #FFDADA 0%, transparent 55%),
                        linear-gradient(135deg, #FDFBFB 0%, #F4F6F9 100%) !important; 
            min-height: 100vh;
        }

        .app-layout {
            width: 100%; max-width: 500px; margin: 0 auto; padding: 30px 20px;
        }

        /* Top Bar & Back Button */
        .top-bar { display: flex; align-items: center; margin-bottom: 30px; gap: 15px; }
        .back-btn {
            display: flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; background: #FFFFFF; border-radius: 12px;
            color: #1F2937; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .page-title { font-size: 18px; font-weight: 700; color: #1F2937; }

        /* Form Card */
        .form-card {
            background: #FFFFFF; border-radius: 20px; padding: 24px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03); border: 1px solid rgba(255,255,255,0.8);
        }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: #4B5563; margin-bottom: 8px; }
        .input-group input {
            width: 100%; padding: 14px 16px; border-radius: 12px;
            border: 1px solid #E5E7EB; background: #F9FAFB;
            font-size: 14px; font-family: inherit; transition: 0.2s;
        }
        .input-group input:focus { border-color: #D32F2F; outline: none; background: #FFFFFF; }

        .btn-submit {
            width: 100%; background: #1F2937; color: white; padding: 14px;
            border: none; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; margin-top: 10px;
        }
        .btn-submit:hover { background: #111827; }

        /* Alert Messages */
        .alert-gagal { background-color: #FEF2F2; color: #DC2626; padding: 12px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 20px; text-align: center; border: 1px solid #FECACA; }
        .alert-sukses { background-color: #F0FDF4; color: #16A34A; padding: 12px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 20px; text-align: center; border: 1px solid #BBF7D0; }
    </style>
</head>
<body>

    <div class="app-layout">
        <div class="top-bar">
            <a href="profil.php" class="back-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div class="page-title">Keamanan Akun</div>
        </div>

        <div class="form-card">
            <?php echo $pesan; // Menampilkan pesan sukses/gagal di sini ?>

            <form action="" method="POST">
                <div class="input-group">
                    <label>Kata Sandi Saat Ini</label>
                    <input type="password" name="password_lama" placeholder="Masukkan kata sandi lama..." required>
                </div>

                <div class="input-group">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="password_baru" placeholder="Minimal 6 karakter" required>
                </div>

                <div class="input-group">
                    <label>Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="konfirmasi_password" placeholder="Ketik ulang kata sandi baru..." required>
                </div>

                <button type="submit" name="ubah_password" class="btn-submit">Perbarui Kata Sandi</button>
            </form>
        </div>
    </div>

</body>
</html>