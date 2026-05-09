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
    <title>Buat Laporan - Aspirasi Siswa</title>
    
    <!-- Font Premium -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <style>
       /* 1. Background Full Gradien (Sama kayak Homepage tapi sedikit lebih tajam) */
       /* 1. Background Full Gradien (Efek Radiate/Glow Merah dari Kanan Atas) */
        body {
            align-items: flex-start !important; 
            
            /* KUNCINYA DI SINI: Kita tumpuk 2 layer background! */
            background: 
                /* Layer 1: Pendaran (radiate) merah super halus dari pojok kanan atas */
                radial-gradient(circle at top right, #FFDADA 0%, transparent 55%),
                /* Layer 2: Warna dasar putih tulang ke abu-abu aplikasi */
                linear-gradient(135deg, #FDFBFB 0%, #F4F6F9 100%) !important; 
            
            padding-top: 0;
            min-height: 100vh;
        }

        /* 2. Layout Tanpa "Penjara" (Lebar menyesuaikan, tanpa background putih) */
        .app-layout {
            width: 100%;
            max-width: 500px; /* Sedikit dilebarin biar lega kalau di laptop */
            margin: 0 auto;
            padding: 20px 24px 60px 24px; /* Padding luar */
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* 3. Header Transparan (Menyatu dengan background gradien) */
        .top-nav-bar {
            display: flex;
            align-items: center;
            padding-top: 10px;
            /* Background putih dan shadow dihapus */
            background-color: transparent; 
        }
        .back-btn {
            color: #1F2937;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.6); /* Transparan dikit */
            backdrop-filter: blur(4px); /* Efek blur ala iOS */
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: 0.2s;
        }
        .back-btn:hover { background-color: rgba(255, 255, 255, 0.9); }
        .page-title {
            flex: 1;
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            color: #1F2937;
            margin-right: 40px; 
        }

        /* 4. Form Card (Ini yang ngebungkus input-inputnya aja) */
        .form-card-container {
            background-color: #FFFFFF;
            border-radius: 24px; /* Lengkung di SEMUA sudut */
            padding: 30px 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); /* Shadow super halus */
            display: flex;
            flex-direction: column;
            gap: 24px;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        /* --- ICONIC INPUT GROUP --- */
        .iconic-input-row {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        .icon-box {
            width: 44px;
            height: 44px;
            background-color: #F3F4F6;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #4B5563;
            flex-shrink: 0;
        }
        .input-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .input-content label {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
        }
        .input-content span.sub-label {
            font-size: 11px;
            font-weight: 500;
            color: #9CA3AF;
        }
        
        /* Styling Input Box */
        .custom-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            font-size: 14px;
            color: #1F2937;
            background-color: #FAFAFA;
            transition: 0.3s ease;
            outline: none;
            font-family: inherit;
        }
        .custom-input:focus {
            border-color: #D32F2F;
            background-color: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.08);
        }
        textarea.custom-input {
            resize: none;
            min-height: 120px;
        }

        /* Read-only state untuk Nama/NIS */
        .custom-input:read-only {
            background-color: #F9FAFB;
            color: #6B7280;
            cursor: not-allowed;
            border-color: #F3F4F6;
        }

        /* --- UPLOAD FOTO AREA --- */
       .upload-area {
            border: 2px dashed #D1D5DB;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            background-color: #FAFAFA;
            cursor: pointer;
            /* Ini kuncinya: bikin transisinya mulus selama 0.3 detik */
            transition: all 0.3s ease; 
            position: relative;
        }
        
        /* Ini efek saat kursor nyentuh kotaknya */
        .upload-area:hover {
            background-color: #FEF2F2; /* Berubah jadi merah super pudar */
            border-color: #D32F2F; /* Garisnya jadi merah tegas */
            border-style: solid; /* Garis putus-putus (dashed) berubah jadi garis nyambung (solid) */
            transform: translateY(-2px); /* Kotaknya sedikit terangkat ke atas */
        }
        /* Input aslinya disembunyikan, ditimpa area klik */
        .upload-area input[type="file"] {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .upload-icon {
            color: #1F2937;
            margin-bottom: 8px;
        }
        .upload-text {
            font-size: 13px;
            font-weight: 600;
            color: #D32F2F;
        }
        .upload-subtext {
            font-size: 11px;
            color: #9CA3AF;
            margin-top: 4px;
        }

        /* --- BUTTON & WARNING --- */
        .btn-submit-app {
            background-color: #D32F2F;
            color: white;
            padding: 16px;
            border-radius: 12px;
            border: none;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: 0.2s;
            width: 100%;
            margin-top: 10px;
        }
        .btn-submit-app:hover {
            background-color: #B71C1C;
            transform: translateY(-2px);
        }
        
        .warning-banner {
            background-color: #FEF2F2;
            border: 1px solid #FEE2E2;
            padding: 12px 16px;
            border-radius: 12px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .warning-banner p {
            font-size: 11px;
            color: #991B1B;
            line-height: 1.5;
            margin: 0;
        }
    </style>
</head>
<body>

    <div class="app-layout">
        
        <!-- TOP NAV BAR -->
        <div class="top-nav-bar">
            <a href="homepage.php" class="back-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div class="page-title">Buat Pengaduan</div>
        </div>

        <!-- HEADER TITLE -->
        <div class="form-header">
            <h1>Sampaikan Laporanmu</h1>
            <p>Jelaskan masalah yang kamu alami, agar kami bisa segera menindaklanjuti.</p>
        </div>

        <!-- MAIN FORM CARD -->
        <!-- PENTING: enctype="multipart/form-data" wajib ada biar foto bisa dikirim -->
        <form action="proses_aduan.php" method="post" enctype="multipart/form-data" class="form-card-container">
            
            <!-- 1. IDENTITAS (Otomatis dari Session) -->
            <div class="iconic-input-row">
                <div class="icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="input-content">
                    <label>Identitas Pelapor <span class="sub-label">(Otomatis)</span></label>
                    <input type="text" class="custom-input" value="<?php echo $_SESSION['nama']; ?> (NIS: <?php echo $_SESSION['nis']; ?>)" readonly>
                    <!-- Input hidden untuk dikirim ke proses_aduan.php -->
                    <input type="hidden" name="nis" value="<?php echo $_SESSION['nis']; ?>">
                </div>
            </div>

            <!-- 2. KATEGORI LAPORAN -->
            <div class="iconic-input-row">
                <div class="icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="input-content">
                    <label>Kategori Pengaduan</label>
                    <select name="kategori" class="custom-input" required>
                        <option value="" disabled selected>Pilih kategori...</option>
                        <option value="Fasilitas">Fasilitas Sekolah (AC, Meja, dll)</option>
                        <option value="Kebersihan">Kebersihan Lingkungan</option>
                        <option value="Keamanan">Keamanan & Ketertiban</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <!-- 3. ISI LAPORAN -->
            <div class="iconic-input-row">
                <div class="icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="input-content">
                    <label>Isi Laporan / Keluhan</label>
                    <textarea name="isi_laporan" class="custom-input" placeholder="Tuliskan laporan kamu di sini secara detail..." required></textarea>
                </div>
            </div>

            <!-- 4. UPLOAD FOTO -->
            <div class="iconic-input-row">
                <div class="icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                </div>
                <div class="input-content">
                    <label>Upload Foto <span class="sub-label">(Opsional)</span></label>
                    <div class="upload-area">
                        <!-- Input type file yang tak terlihat tapi bisa diklik -->
                        <input type="file" name="foto_laporan" accept="image/png, image/jpeg, image/jpg">
                        <div class="upload-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div class="upload-text">Pilih Foto dari Galeri</div>
                        <div class="upload-subtext">Format PNG, JPG maks. 5MB</div>
                    </div>
                </div>
            </div>

            <!-- TOMBOL SUBMIT -->
            <button type="submit" class="btn-submit-app">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Kirim Pengaduan
            </button>

            <!-- WARNING BANNER -->
            <div class="warning-banner">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#991B1B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <p>Pastikan laporan yang kamu kirim sesuai fakta dan tidak mengandung unsur hoaks atau kata-kata kasar.</p>
            </div>

        </form>

    </div>

</body>
</html>