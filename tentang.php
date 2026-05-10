<?php 
session_start();
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
    <title>Tentang Aplikasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            background: radial-gradient(circle at top right, #FFDADA 0%, transparent 55%),
                        linear-gradient(135deg, #FDFBFB 0%, #F4F6F9 100%) !important; 
            min-height: 100vh;
        }

        .app-layout {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Top Nav Bar mirip form laporan */
        .top-nav-bar {
            display: flex;
            align-items: center;
            padding-top: 10px;
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
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.2s;
        }
        .back-btn:hover { background-color: rgba(255, 255, 255, 0.9); }
        .page-title {
            flex: 1;
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            color: #1F2937;
            margin-right: 40px; 
        }

        /* Card Utama */
        .info-card {
            background-color: #FFFFFF;
            border-radius: 24px;
            padding: 40px 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.8);

        }

        .app-logo-placeholder {
            width: 80px;
            height: 80px;
            background-color: #FEF2F2;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            color: #D32F2F;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(211, 47, 47, 0.1);
        }

        .info-card h2 {
            margin: 0 0 8px 0;
            font-size: 22px;
            color: #1F2937;
        }

        .info-card p.version {
            margin: 0 0 30px 0;
            font-size: 14px;
            color: #9CA3AF;
            font-weight: 500;
        }

        /* Area Tim Developer */
        .dev-team-section {
            width: 90%;
            background-color: #F9FAFB;
            border-radius: 16px;
            padding: 20px;
            text-align: left;
            border: 1px solid #F3F4F6;
            box-shadow: 0 2px 10px rgba(0,0,0,0.10);
        }

        .dev-team-section h3 {
            font-size: 13px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
        }
         .dev-team-section h4 {
            font-size: 13px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 15px 0;
        }

        .team-member {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .team-member:last-child {
            margin-bottom: 0;
        }

        .member-avatar {
            width: 36px;
            height: 36px;
            background-color: #E5E7EB;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            color: #4B5563;
            font-size: 14px;
        }

        .member-info h4 {
            margin: 0;
            font-size: 14px;
            color: #1F2937;
        }
        .member-info p {
            margin: 2px 0 0 0;
            font-size: 12px;
            color: #9CA3AF;
        }
    </style>
</head>
<body>

    <div class="app-layout">
        
        <div class="top-nav-bar">
            <a href="profil.php" class="back-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div class="page-title">Tentang Web</div>
        </div>

        <div class="info-card">
            <div class="app-logo-placeholder">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            
            <h2>Aspirasi Siswa</h2>
            <p class="version">Versi 1.0.0 (Beta)</p>

            <div class="dev-team-section">
                <h3>Tim Developer (Kelompok 8)</h3>
                <h4>Kelas X PPLG 3</h4>
                
                <div class="team-member">
                    <div class="member-avatar">D</div>
                    <div class="member-info">
                        <h4>Danish Attar Waradana</h4>
                        <p>Hacker & Hustler</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-avatar">O</div>
                    <div class="member-info">
                        <h4>Ogi Revianto</h4>
                        <p>Hacker & Hustler</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-avatar">F</div>
                    <div class="member-info">
                        <h4>Findy Azka Zafira</h4>
                        <p>Hipster</p>
                    </div>
                </div>
                 <div class="team-member">
                    <div class="member-avatar">J</div>
                    <div class="member-info">
                        <h4>Julyan Debby Geysha Al Safira</h4>
                        <p>Hipster</p>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>
</html>