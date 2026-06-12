<?php
session_start();
include 'config/koneksi.php';

// 1. SATPAM LOGIN
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] != "sudah_login") {
    header("location: login.php");
    exit();
}

$role = $_SESSION['role'];
$id_laporan = $_GET['id'];

// 2. AMBIL DATA LAPORAN DARI DATABASE
$query = mysqli_query($koneksi, "SELECT * FROM pengaduan WHERE id='$id_laporan'");
$data = mysqli_fetch_array($query);

// 3. KEAMANAN: Cegah Siswa Ngintip Laporan Orang Lain
if ($role == 'siswa' && $data['nis'] != $_SESSION['nis']) {
    echo "<script>alert('Akses Ditolak! Ini bukan laporanmu.'); window.location='riwayat.php';</script>";
    exit();
}

// 4. LOGIKA EKSEKUSI GURU (Menyimpan Tanggapan)
if (isset($_POST['simpan_tanggapan']) && $role == 'guru') {
    $status_baru = $_POST['status_baru'];
    // Pake fungsi escape biar kalau guru ngetik tanda kutip (') gak bikin error database
    $tanggapan = mysqli_real_escape_string($koneksi, $_POST['tanggapan']);

    $update = mysqli_query($koneksi, "UPDATE pengaduan SET status='$status_baru', tanggapan='$tanggapan' WHERE id='$id_laporan'");

    if ($update) {
        // Refresh halaman biar datanya terupdate
        echo "<script>window.location='detail_riwayat.php?id=$id_laporan';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan tanggapan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - Aspirasi Siswa</title>
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
            padding: 30px 20px 80px 20px;
        }

        /* --- Header & Tombol Kembali --- */
        .top-bar { display: flex; align-items: center; margin-bottom: 25px; gap: 15px; }
        .back-btn {
            display: flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; background: #FFFFFF; border-radius: 12px;
            color: #1F2937; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .page-title { font-size: 18px; font-weight: 700; color: #1F2937; }

        /* --- Card Detail Utama --- */
        .detail-card {
            background-color: #FFFFFF; border-radius: 20px; padding: 24px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03); border: 1px solid rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
        }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #F3F4F6; }
        .report-meta span { display: block; }
        .report-id { font-size: 13px; font-weight: 700; color: #1F2937; margin-bottom: 4px; }
        .report-date { font-size: 12px; color: #9CA3AF; }

        /* Badge Status */
        .status-badge { padding: 8px 16px; border-radius: 12px; font-size: 12px; font-weight: 700; }
        .status-proses { background-color: #FFFBEB; color: #D97706; }
        .status-selesai { background-color: #F0FDF4; color: #16A34A; }
        .status-ditolak { background-color: #FEF2F2; color: #DC2626; }

        /* Isi Laporan */
        .content-group { margin-bottom: 20px; }
        .label-title { font-size: 11px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block; }
        .content-text { font-size: 15px; color: #1F2937; line-height: 1.6; }
        
        /* Foto Bukti */
        .foto-bukti { width: 100%; border-radius: 12px; margin-top: 10px; object-fit: cover; border: 1px solid #E5E7EB; }
        .no-foto { padding: 15px; background: #F9FAFB; border-radius: 12px; text-align: center; color: #9CA3AF; font-size: 13px; border: 1px dashed #D1D5DB; }

        /* --- Kotak Tanggapan (Bila sudah diisi) --- */
        .feedback-box {
            background-color: #F0FDF4; border: 1px solid #BBF7D0;
            border-radius: 16px; padding: 20px; margin-top: 20px;
        }
        .feedback-box.ditolak { background-color: #FEF2F2; border-color: #FECACA; }
        .feedback-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #1F2937; margin-bottom: 8px; }
        .feedback-text { font-size: 14px; color: #4B5563; line-height: 1.5; }

        /* --- Form Eksekusi Guru --- */
        .admin-action-card {
            background-color: #1F2937; border-radius: 20px; padding: 24px; color: #FFFFFF;
        }
        .admin-action-card h3 { font-size: 16px; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        
        .radio-group { display: flex; gap: 15px; margin-bottom: 15px; }
        .radio-label { 
            flex: 1; padding: 12px; background: rgba(255,255,255,0.1); border-radius: 12px; 
            text-align: center; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; border: 1px solid transparent;
        }
        .radio-label:hover { background: rgba(255,255,255,0.2); }
        .radio-input { display: none; }
        /* Efek kalau radio dipilih */
        .radio-input[value="Selesai"]:checked + .radio-label { background: #16A34A; border-color: #4ADE80; }
        .radio-input[value="Ditolak"]:checked + .radio-label { background: #DC2626; border-color: #F87171; }

        .textarea-tanggapan {
            width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px; padding: 15px; color: white; font-family: inherit; font-size: 13px;
            resize: vertical; min-height: 100px; margin-bottom: 15px;
        }
        .textarea-tanggapan:focus { outline: none; border-color: #D32F2F; }
        .textarea-tanggapan::placeholder { color: #9CA3AF; }

        .btn-submit-action {
            width: 100%; background: #D32F2F; color: white; padding: 14px;
            border: none; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s;
        }
        .btn-submit-action:hover { background: #B91C1C; }

    </style>
</head>
<body>

    <div class="app-layout">
        
        <div class="top-bar">
            <a href="riwayat.php" class="back-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div class="page-title">Detail Aspirasi</div>
        </div>

        <?php 
        // Set warna badge status
        $status_class = "";
        if($data['status'] == 'Proses') $status_class = "status-proses";
        elseif($data['status'] == 'Selesai') $status_class = "status-selesai";
        else $status_class = "status-ditolak";
        ?>

        <div class="detail-card">
            <div class="card-header">
                <div class="report-meta">
                    <span class="report-id">#LAPOR-<?php echo $data['id']; ?></span>
                    <span class="report-date"><?php echo date('d M Y - H:i', strtotime($data['tanggal'])); ?></span>
                </div>
                <div class="status-badge <?php echo $status_class; ?>">
                    <?php echo $data['status']; ?>
                </div>
            </div>

            <?php if ($role == 'guru') { ?>
                <div class="content-group">
                    <span class="label-title">Identitas Pelapor</span>
                    <div class="content-text" style="font-weight: 600;">NIS: <?php echo $data['nis']; ?></div>
                </div>
            <?php } ?>

            <div class="content-group">
                <span class="label-title">Kategori Laporan</span>
                <div class="content-text"><?php echo $data['kategori']; ?></div>
            </div>

            <div class="content-group">
                <span class="label-title">Isi Laporan</span>
                <div class="content-text"><?php echo nl2br($data['isi']); ?></div>
            </div>

            <div class="content-group">
                <span class="label-title">Lampiran Bukti</span>
                <?php if (!empty($data['foto'])) { ?>
                    <img src="assets/img/laporan/<?php echo $data['foto']; ?>" alt="Bukti Laporan" class="foto-bukti">
                <?php } else { ?>
                    <div class="no-foto">Tidak ada lampiran foto yang dikirim.</div>
                <?php } ?>
            </div>

            <?php if ($data['status'] != 'Proses' && !empty($data['tanggapan'])) { 
                $feedback_bg = ($data['status'] == 'Ditolak') ? 'ditolak' : '';
            ?>
                <div class="feedback-box <?php echo $feedback_bg; ?>">
                    <div class="feedback-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Tanggapan Sekolah
                    </div>
                    <div class="feedback-text">
                        <?php echo nl2br($data['tanggapan']); ?>
                    </div>
                </div>
            <?php } ?>

        </div>

        <?php if ($role == 'guru' && $data['status'] == 'Proses') { ?>
            <div class="admin-action-card">
                <h3>Tindak Lanjut Laporan</h3>
                <form action="" method="POST">
                    
                    <div class="radio-group">
                        <input type="radio" name="status_baru" id="statSelesai" value="Selesai" class="radio-input" required>
                        <label for="statSelesai" class="radio-label">Terima / Selesai</label>

                        <input type="radio" name="status_baru" id="statDitolak" value="Ditolak" class="radio-input" required>
                        <label for="statDitolak" class="radio-label">Tolak Laporan</label>
                    </div>

                    <textarea name="tanggapan" class="textarea-tanggapan" placeholder="Tuliskan tanggapan atau alasan kepada siswa..." required></textarea>

                    <button type="submit" name="simpan_tanggapan" class="btn-submit-action">Simpan Keputusan</button>
                </form>
            </div>
        <?php } ?>

    </div>

</body>
</html>