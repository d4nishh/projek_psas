<?php 
session_start();

// Satpam Penjaga Halaman
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
    <title>Profil - Aspirasi Siswa</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <style>
        body {
            align-items: flex-start !important; 
            /* Konsisten dengan efek radiate di form.php */
            background: radial-gradient(circle at top right, #FFDADA 0%, transparent 55%),
                        linear-gradient(135deg, #FDFBFB 0%, #F4F6F9 100%) !important; 
            padding-top: 0;
            min-height: 100vh;
        }

        .app-layout {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            padding: 40px 24px 120px 24px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        /* --- IDENTITY CARD --- */
        .profile-header-card {
            background-color: #1F2937; /* Dark theme untuk kartu identitas agar kontras */
            padding: 24px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .avatar-circle {
            width: 60px;
            height: 60px;
            background-color: #D32F2F;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
            border: 3px solid rgba(255, 255, 255, 0.1);
        }
        .user-info h3 {
            color: white;
            font-size: 18px;
            margin-bottom: 4px;
        }
        .user-info p {
            color: #9CA3AF;
            font-size: 13px;
        }

        /* --- MENU STYLING --- */
        .menu-group-label {
            font-size: 13px;
            font-weight: 700;
            color: #6B7280;
            margin-bottom: 12px;
            margin-left: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .menu-container {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .menu-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 12px;
            text-decoration: none;
            border-radius: 14px;
            transition: 0.2s ease;
        }
        .menu-item:hover {
            background-color: #F9FAFB;
        }
        .menu-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .icon-wrap {
            width: 36px;
            height: 36px;
            background-color: #F3F4F6;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #4B5563;
        }
        .menu-item span {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }
        .chevron {
            color: #D1D5DB;
        }

        /* --- LOGOUT BUTTON --- */
        .btn-logout-full {
            background-color: #D32F2F;
            color: white;
            padding: 16px;
            border-radius: 16px;
            text-align: center;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.2);
        }
        .btn-logout-full:hover {
            background-color: #B71C1C;
            transform: translateY(-2px);
        }

        /* Nav Footer override biar link icon Profile warnanya merah */
        .nav-item.active-profile {
            color: #D32F2F;
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
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); /* Shadow dibikin lebih halus */
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

    <div class="app-layout">
        
        <h2 style="text-align: center; font-size: 18px; color: #1F2937; margin-bottom: 10px;">Profil Saya</h2>

        <div class="profile-header-card">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?>
            </div>
            <div class="user-info">
                <h3><?php echo $_SESSION['nama']; ?></h3>
                <p>Siswa X PPLG 3 • NIS <?php echo $_SESSION['nis']; ?></p>
            </div>
        </div>

        <div>
            <p class="menu-group-label">Keamanan Akun</p>
            <div class="menu-container">
                <a href="ubah_password.php" class="menu-item">
                    <div class="menu-item-left">
                        <div class="icon-wrap">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <span>Ubah Password</span>
                    </div>
                    <svg class="chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
        </div>

        <div>
            <p class="menu-group-label">Informasi</p>
            <div class="menu-container">
                <a href="tentang.php" class="menu-item">
                    <div class="menu-item-left">
                        <div class="icon-wrap">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </div>
                        <span>Tentang Aplikasi</span>
                    </div>
                    <svg class="chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
        </div>

        <a href="logout.php" class="btn-logout-full">Keluar dari Akun</a>

    </div>

    <nav class="bottom-nav">
        <a href="homepage.php" class="nav-item">
            <div class="nav-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <span class="nav-text">Home</span>
        </a>
        <a href="riwayat.php" class="nav-item">
            <div class="nav-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span class="nav-text">Riwayat</span>
        </a>
        <a href="profil.php" class="nav-item active-profile">
            <div class="nav-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <span class="nav-text">Profil</span>
        </a>
    </nav>

</body>
</html>