<?php 
session_start();

// 1. Satpam Penjaga Halaman
if ($_SESSION['status_login'] != "sudah_login") {
    header("location: login.php?pesan=belum_login");
    exit();
}

// 2. Panggil Koneksi Database buat ngitung statistik
include 'config/koneksi.php';

$nis_user = $_SESSION['nis'];

// Hitung total laporan
$query_total = mysqli_query($koneksi, "SELECT * FROM pengaduan WHERE nis='$nis_user'");
$total_laporan = mysqli_num_rows($query_total);

// Hitung laporan diproses (Berdasarkan status 'proses')
$query_proses = mysqli_query($koneksi, "SELECT * FROM pengaduan WHERE nis='$nis_user' AND status='proses'");
$total_proses = mysqli_num_rows($query_proses);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Aspirasi Siswa</title>
    
    <!-- Font Premium -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <!-- CSS Khusus Homepage (Biar ga melayang di tengah layar) -->
    <style>
        /* Override body dari style.css biar posisinya nempel di atas */
        body {
            align-items: flex-start !important; 
            background-color: #F8F9FA !important; /* Warna dasar layar aplikasi */
            padding-top: 0;
        }

        /* Container baru khusus buat layout full-screen */
        .app-layout {
            width: 100%;
            max-width: 420px;
            min-height: 100vh;
            margin: 0 auto;
            padding: 40px 24px;
            display: flex;
            flex-direction: column;
            gap: 30px; /* Jarak antar bagian */
        }

        /* --- STYLE HEADER ALA DIGITS --- */
        .header-app {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .greeting-small {
            color: #9CA3AF;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 2px;
        }
        .user-name {
            color: #D32F2F; /* Aksent merah elegan */
            font-size: 20px;
            font-weight: 700;
        }
        .bell-icon {
            font-size: 24px;
            color: #9CA3AF;
        }

        /* --- STYLE CARD STATISTIK (Ini kotak yang lu maksud) --- */
        .card-stats {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #F3F4F6;
        }
        .card-stats-title {
            font-size: 15px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 16px;
        }
        .stats-row {
            display: flex;
            gap: 15px;
        }
        .stat-item {
            flex: 1;
            background-color: #F9FAFB;
            border-radius: 14px;
            padding: 16px;
            border: 1px solid #F3F4F6;
        }
        .stat-angka {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
        }
        .stat-label {
            font-size: 12px;
            color: #6B7280;
            font-weight: 500;
            margin-top: 4px;
        }

        /* --- STYLE MENU GRID (Kotak-kotak icon) --- */
        .menu-section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 15px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .menu-box {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 24px 10px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            text-decoration: none;
            border: 1px solid #F3F4F6;
            transition: 0.2s ease;
        }
        .menu-box:hover {
            transform: translateY(-3px);
            border-color: #D32F2F;
        }
        .menu-icon {
            font-size: 36px;
            margin-bottom: 12px;
        }
        .menu-text {
            color: #1F2937;
            font-size: 13px;
            font-weight: 600;
        }

        /* Tombol Logout */
        .btn-logout-app {
            margin-top: 20px;
            text-align: center;
            color: #DC2626;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: block;
        }
        /* --- STYLE BANNER INFORMASI (OPSI B) --- */
        .info-banner {
            background: linear-gradient(135deg, #F9FAFB, #F3F4F6);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 80px; /* Spacer agar tidak tertutup footer */
            border: 1px dashed #E5E7EB;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .banner-icon {
            font-size: 28px;
            background-color: #FFFFFF;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .banner-text h3 {
            font-size: 14px;
            color: #1F2937;
            margin-bottom: 4px;
        }
        .banner-text p {
            font-size: 12px;
            color: #6B7280;
            line-height: 1.4;
        }

        /* --- STYLE BOTTOM NAVIGATION (FOOTER KAPSUL MELAYANG) --- */
        .bottom-nav {
            position: fixed;
            bottom: 20px; /* Diangkat dikit dari dasar layar biar melayang */
            left: 50%;
            transform: translateX(-50%);
            width: 90%; /* Biar ada jarak dari kiri-kanan pinggir layar */
            max-width: 400px;
            background-color: #FFFFFF;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 15px 10px; /* Padding disesuaikan biar proporsional */
            
            /* INI KUNCINYA: Bikin ujungnya bulat mulus (rounded) */
            border-radius: 30px; 
            
            border: 1px solid #F3F4F6;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); /* Shadow dibikin lebih halus */
            z-index: 100;
        }
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: #9CA3AF; /* Warna abu-abu redup untuk menu tidak aktif */
            transition: 0.2s ease;
        }
        /* State aktif (sedang di homepage) */
        .nav-item.active {
            color: #D32F2F; /* Merah elegan */
        }
        .nav-icon {
            font-size: 22px;
        }
        .nav-text {
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Pakai class app-layout, BUKAN mobile-container -->
    <div class="app-layout">
        
        <!-- 1. HEADER SECTON -->
        <div class="header-app">
            <div>
                <p class="greeting-small">Selamat Datang,</p>
                <!-- Menampilkan nama siswa dari session -->
                <h1 class="user-name"><?php echo $_SESSION['nama']; ?> 👋</h1>
            </div>
            <div class="bell-icon">🔔</div>
        </div>

        <!-- 2. CARD STATISTIK -->
        <div class="card-stats">
            <h2 class="card-stats-title">Ringkasan Laporanmu</h2>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-angka"><?php echo $total_laporan; ?></div>
                    <div class="stat-label">Total Laporan</div>
                </div>
                <!-- Card Diproses dengan warna aksen kuning -->
                <div class="stat-item" style="background-color: #FFFBEB; border-color: #FEF3C7;">
                    <div class="stat-angka" style="color: #D97706;"><?php echo $total_proses; ?></div>
                    <div class="stat-label" style="color: #B45309;">Sedang Diproses</div>
                </div>
            </div>
        </div>

        <!-- 3. MENU UTAMA (GRID KOTAK) -->
        <div>
            <h2 class="menu-section-title">Eksplor <span style="border-bottom: 2px solid #D32F2F;">Sekarang!</span></h2>
            <div class="menu-grid">
                <a href="form.php" class="menu-box">
                    <div class="menu-icon">📝</div>
                    <div class="menu-text">Buat Laporan</div>
                </a>
                
                <a href="riwayat.php" class="menu-box">
                    <div class="menu-icon">🕒</div>
                    <div class="menu-text">Cek Riwayat</div>
                </a>
            </div>
        </div>
        <!-- 4. BANNER EDUKASI / INFORMASI -->
        <div class="info-banner">
            <div class="banner-icon">💡</div>
            <div class="banner-text">
                <h3>Sampaikan dengan Baik</h3>
                <p>Gunakan bahasa yang sopan dan jelas saat menulis laporan agar mudah ditindaklanjuti.</p>
            </div>
        </div>

    </div> <!-- Ini adalah penutup div class="app-layout" yang sudah ada sebelumnya -->

    <!-- ==============================================
         5. BOTTOM NAVIGATION (FOOTER)
         ============================================== -->
    <nav class="bottom-nav">
        <!-- Menu Home (Aktif/Merah) -->
        <a href="homepage.php" class="nav-item active">
            <div class="nav-icon">
                <!-- SVG Icon Home -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <span class="nav-text">Home</span>
        </a>

        <!-- Menu Riwayat (Abu-abu) -->
        <a href="riwayat.php" class="nav-item">
            <div class="nav-icon">
                <!-- SVG Icon Clock/Riwayat -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span class="nav-text">Riwayat</span>
        </a>

        <!-- Menu Profil (Abu-abu) -->
        <a href="profil.php" class="nav-item">
            <div class="nav-icon">
                <!-- SVG Icon User -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <span class="nav-text">Profil</span>
        </a>
    </nav>

    </div>

</body>
</html>