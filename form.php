<?php 
session_start();

// Jika belum login, tendang balik ke halaman login
if ($_SESSION['status_login'] != "sudah_login") {
    header("location: login.php?pesan=belum_login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Laporan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="mobile-container">
        <div class="card-accent"></div> 

        <div class="login-header" style="padding-bottom: 20px;">
            <h1>Buat Laporan 📝</h1>
            <p>Sampaikan keluhanmu. Data kamu aman bersama kami.</p>
        </div>

        <form class="login-form" action="config/proses_aduan.php" method="POST">
            
            <div class="input-group">
                <label>Nama Pelapor</label>
                <input type="text" name="nama" value="<?php echo $_SESSION['nama']; ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
            </div>

            <div class="input-group">
                <label>NIS</label>
                <input type="text" name="nis" value="<?php echo $_SESSION['nis']; ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
            </div>
            <div class="input-group">
                <label for="kategori">Kategori Laporan</label>
                <select name="kategori" id="kategori" required style="width: 100%; padding: 14px 16px; border: 1px solid var(--abu-border); border-radius: var(--rounded-md); font-size: 15px; outline: none;">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Fasilitas">Kerusakan Fasilitas</option>
                    <option value="Bullying">Tindakan Bullying</option>
                    <option value="Kebersihan">Kebersihan Lingkungan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="input-group">
                <label for="isi">Isi Laporan / Keluhan</label>
                <textarea name="isi" id="isi" rows="4" placeholder="Ceritakan detail kejadian atau keluhanmu di sini..." required style="width: 100%; padding: 14px 16px; border: 1px solid var(--abu-border); border-radius: var(--rounded-md); font-size: 15px; outline: none; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <button type="submit" class="btn-login" style="margin-top: 15px;">Kirim Laporan Sekarang</button>
            
        </form>

        <a href="homepage.php" class="back-link">← Kembali ke Home</a>

    </div>

</body>
</html>