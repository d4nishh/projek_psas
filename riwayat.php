<?php 
session_start();
include 'config/koneksi.php'; // Sesuaikan lokasi koneksi lu

// Satpam Pengecek Login
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location: login.php?pesan=belum_login");
    exit();
}

$role = $_SESSION['role'];

// LOGIKA BINGLON: Beda Role, Beda Query & Beda Judul
if ($role == 'siswa') {
    $nis_user = $_SESSION['nis'];
    $query = mysqli_query($koneksi, "SELECT * FROM pengaduan WHERE nis='$nis_user' ORDER BY id DESC");
    
    $page_title = "Riwayat Pengaduan";
    $page_desc = "Daftar laporan yang pernah kamu kirimkan.";
    $nav_text = "Riwayat";
} else {
    // Guru ngeliat SEMUA laporan
    $query = mysqli_query($koneksi, "SELECT * FROM pengaduan ORDER BY id DESC");
    
    $page_title = "Kelola Aspirasi Masuk";
    $page_desc = "Daftar seluruh laporan dari siswa yang perlu ditinjau.";
    $nav_text = "Kelola";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Aspirasi Siswa</title>
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
            width: 100%; max-width: 420px; margin: 0 auto;
            padding: 40px 20px 120px 20px; /* Padding bawah digedein buat ruang footer */
        }

        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: #1F2937; margin-bottom: 8px; }
        .page-header p { font-size: 14px; color: #6B7280; }

        /* Card Riwayat */
        .history-card {
            background-color: #FFFFFF; border-radius: 20px; padding: 20px;
            margin-bottom: 16px;  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);s
            border: 1px solid rgba(255, 255, 255, 0.8);
            display: flex; flex-direction: column; gap: 12px;
        }
        .card-top { display: flex; justify-content: space-between; align-items: center; }
        .report-id { font-size: 12px; font-weight: 700; color: #9CA3AF; }
        
        .status-badge { padding: 6px 12px; border-radius: 10px; font-size: 11px; font-weight: 700; text-transform: capitalize; }
        .status-proses { background-color: #FFFBEB; color: #D97706; }
        .status-selesai { background-color: #F0FDF4; color: #16A34A; }
        .status-ditolak { background-color: #FEF2F2; color: #DC2626; }

        .card-body { display: flex; gap: 15px; align-items: center; }
        .category-icon {
            width: 45px; height: 45px; background-color: #F3F4F6; border-radius: 12px;
            display: flex; justify-content: center; align-items: center; color: #4B5563;
        }
        .report-content h3 {
            font-size: 15px; font-weight: 700; color: #1F2937; margin-bottom: 4px;
            display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
        }
        .report-content p { font-size: 12px; color: #6B7280; margin-bottom: 4px; }
        
        /* Tambahan buat Guru: Info Pelapor */
        .reporter-info { font-size: 11px; font-weight: 600; color: #D32F2F; background: #FEF2F2; padding: 4px 8px; border-radius: 6px; display: inline-block; }

        .card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #F3F4F6; margin-top: 5px; }
        .report-date { font-size: 11px; color: #9CA3AF; }
        .btn-detail {
            background-color: #F3F4F6; color: #4B5563; padding: 6px 14px;
            border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; transition: 0.2s;
        }
        .btn-detail:hover { background-color: #E5E7EB; }

        /* Footer Kapsul */
        .bottom-nav {
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 400px; background-color: #FFFFFF;
            display: flex; justify-content: space-around; align-items: center;
            padding: 15px 10px; border-radius: 30px; border: 1px solid #F3F4F6;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); z-index: 100;
        }
        .nav-item { display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; color: #9CA3AF; }
        .nav-item.active-nav { color: #D32F2F; }
        .nav-text { font-size: 11px; font-weight: 600; }
        .empty-state { text-align: center; padding: 50px 20px; color: #9CA3AF; }
    </style>
</head>
<body>

    <div class="app-layout">
        
        <div class="page-header">
            <h1><?php echo $page_title; ?></h1>
            <p><?php echo $page_desc; ?></p>
        </div>

        <?php 
        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_array($query)) {
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
                        <?php if ($role == 'guru') { ?>
                            <div class="reporter-info">NIS Pelapor: <?php echo $data['nis']; ?></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="report-date">
                        Dilaporkan: <?php echo date('d M Y', strtotime($data['tanggal'])); ?>
                    </span>
                    <a href="detail_riwayat.php?id=<?php echo $data['id']; ?>" class="btn-detail">Tinjau Detail</a>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="empty-state">Belum ada laporan.</div>';
        }
        ?>

    </div>

    <nav class="bottom-nav">
        <a href="homepage.php" class="nav-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="nav-text">Home</span>
        </a>
        <a href="riwayat.php" class="nav-item active-nav">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span class="nav-text"><?php echo $nav_text; ?></span>
        </a>
        <a href="profil.php" class="nav-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span class="nav-text">Profil</span>
        </a>
    </nav>

</body>
</html>