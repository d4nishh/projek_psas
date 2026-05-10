<?php 
// 1. DUA BARIS MAGIC INI BUAT MUNCULIN ERROR (JANGAN DIHAPUS DULU)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// 2. PERBAIKAN ALAMAT KONEKSI (Kalau di dalam folder config)
include 'config/koneksi.php'; 

if ($_SESSION['status_login'] != "sudah_login") {
    header("location: login.php?pesan=belum_login");
    exit();
}

$nis_user = $_SESSION['nis'];
$query = mysqli_query($koneksi, "SELECT * FROM pengaduan WHERE nis='$nis_user' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengaduan - Aspirasi Siswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* 1. TEMA UTAMA (Radiate Background) */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            background: radial-gradient(circle at top right, #FFDADA 0%, transparent 55%),
                        linear-gradient(135deg, #FDFBFB 0%, #F4F6F9 100%) !important; 
            min-height: 100vh;
        }

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
        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
            margin: 0 0 8px 0;
        }

        .page-header p {
            font-size: 14px;
            color: #6B7280;
            margin: 0;
        }

        /* 2. CARD RIWAYAT (Acuan Figma) */
        .history-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: 0.3s;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-id {
            font-size: 12px;
            font-weight: 700;
            color: #9CA3AF;
            letter-spacing: 0.5px;
        }

        /* 3. LOGIKA WARNA STATUS (Kuning, Hijau, Merah) */
        .status-badge {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-proses { background-color: #FFFBEB; color: #D97706; } /* Kuning */
        .status-selesai { background-color: #F0FDF4; color: #16A34A; } /* Hijau */
        .status-ditolak { background-color: #FEF2F2; color: #DC2626; } /* Merah */

        .card-body {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .category-icon {
            width: 45px;
            height: 45px;
            background-color: #F3F4F6;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #4B5563;
        }

        .report-content h3 {
            font-size: 15px;
            font-weight: 700;
            color: #1F2937;
            margin: 0 0 4px 0;
            /* Potong teks kalau kepanjangan */
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .report-content p {
            font-size: 12px;
            color: #6B7280;
            margin: 0;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid #F3F4F6;
            margin-top: 5px;
        }

        .report-date {
            font-size: 11px;
            color: #9CA3AF;
        }

        .btn-detail {
            background-color: #F3F4F6;
            color: #4B5563;
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-detail:hover {
            background-color: #E5E7EB;
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
        
        <div class="page-header">
            <h1>Riwayat Pengaduan</h1>
            <p>Daftar laporan yang pernah kamu kirimkan.</p>
        </div>

        <?php 
        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_array($query)) {
                // Tentukan class warna status
                $status_class = "";
                if($data['status'] == 'Proses') $status_class = "status-proses";
                elseif($data['status'] == 'Selesai') $status_class = "status-selesai";
                else $status_class = "status-ditolak";
        ?>
            <div class="history-card">
                <div class="card-top">
                    <span class="report-id">#LAPOR-<?php echo $data['id']; ?></span>
                    <span class="status-badge <?php echo $status_class; ?>">
                        <?php echo $data['status']; ?>
                    </span>
                </div>
                
                <div class="card-body">
                    <div class="category-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div class="report-content">
                        <h3><?php echo $data['isi']; ?></h3>
                        <p><?php echo $data['kategori']; ?></p>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="report-date">
                        Dilaporkan: <?php echo date('d M Y', strtotime($data['tanggal'])); ?>
                    </span>
                    <a href="detail_riwayat.php?id=<?php echo $data['id']; ?>" class="btn-detail">Lihat Detail</a>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="empty-state">Belum ada laporan yang dikirim.</div>';
        }
        ?>

    </div>

    <nav class="bottom-nav">
        <!-- Menu Home (Aktif/Merah) -->
        <a href="homepage.php" class="nav-item">
            <div class="nav-icon">
                <!-- SVG Icon Home -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <span class="nav-text">Home</span>
        </a>

        <!-- Menu Riwayat (Abu-abu) -->
        <a href="riwayat.php" class="nav-item active">
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

</body>
</html>