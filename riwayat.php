<?php 
session_start();
include 'config/koneksi.php'; 

if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location: login.php?pesan=belum_login");
    exit();
}

$role = $_SESSION['role'];

// 1. NANGKEP PARAMETER FILTER DAN SEARCH (DARI URL)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

// 2. KONDISI UNTUK QUERY SQL
$kondisi = "";

if ($role == 'siswa') {
    $nis_user = $_SESSION['nis'];
    $kondisi = "WHERE nis='$nis_user'";
    
    // Tambahan kondisi kalau siswa nge-klik filter status
    if ($filter == 'proses') $kondisi .= " AND status='Proses'";
    elseif ($filter == 'selesai') $kondisi .= " AND status='Selesai'";
    
    // Tambahan kondisi kalau siswa nyari kata kunci
    if (!empty($search)) $kondisi .= " AND (isi LIKE '%$search%' OR kategori LIKE '%$search%')";

    $page_title = "Riwayat Pengaduan";
    $page_desc = "Daftar laporan yang pernah kamu kirimkan.";
    $nav_text = "Riwayat";
} else {
    // Role GURU
    $kondisi = "WHERE 1=1"; // Trik SQL biar bisa digabung pake AND terus
    
    if ($filter == 'proses') $kondisi .= " AND status='Proses'";
    elseif ($filter == 'selesai') $kondisi .= " AND status='Selesai'";
    
    // Guru bisa nyari berdasarkan teks laporan, kategori, atau NIS siswa
    if (!empty($search)) $kondisi .= " AND (isi LIKE '%$search%' OR kategori LIKE '%$search%' OR nis LIKE '%$search%')";

    $page_title = "Kelola Aspirasi Masuk";
    $page_desc = "Daftar seluruh laporan dari siswa yang perlu ditinjau.";
    $nav_text = "Kelola";
}

// 3. JALANKAN QUERY DINAMIS
$query = mysqli_query($koneksi, "SELECT * FROM pengaduan $kondisi ORDER BY id DESC");
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

        /* --- SEARCH BAR --- */
        .search-container {
            margin-bottom: 20px;
            width: 100%;
        }
        .search-form {
            display: flex;
            gap: 10px;
        }
        .search-input {
            flex: 1;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            background-color: #FFFFFF;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: 0.2s;
             box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .search-input:focus {
            border-color: #D32F2F;
        }
        .btn-search {
            background-color: #D32F2F;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
             box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        /* --- FILTER TABS --- */
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            overflow-x: auto;
            padding-bottom: 5px;
        }
        .tab-item {
            padding: 8px 16px;
            border-radius: 20px;
            background-color: #E5E7EB;
            color: #4B5563;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .tab-item.tab-active {
            background-color:  #D32F2F;
            color: #FFFFFF;
        }

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

        <div class="search-container">
            <form action="" method="GET" class="search-form">
                <input type="hidden" name="role" value="<?php echo $role; ?>">
                <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                
                <input type="text" name="search" class="search-input" placeholder="<?php echo ($role == 'siswa') ? 'Cari laporanku...' : 'Cari isi, kategori, atau NIS...'; ?>" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn-search">Cari</button>
            </form>
        </div>

        <div class="filter-tabs">
            <a href="riwayat.php?filter=semua" class="tab-item <?php echo ($filter == 'semua') ? 'tab-active' : ''; ?>">Semua</a>
            <a href="riwayat.php?filter=proses" class="tab-item <?php echo ($filter == 'proses') ? 'tab-active' : ''; ?>">Belum Diproses</a>
            <a href="riwayat.php?filter=selesai" class="tab-item <?php echo ($filter == 'selesai') ? 'tab-active' : ''; ?>">Selesai</a>
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