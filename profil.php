<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: index.php");

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Profil</title>
    <style>
        /* CSS GLOBAL */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f3fbf2; display: flex; height: 100vh; }
        .sidebar { width: 250px; background: #fff; color: #555; padding: 20px; border-right: 1px solid #e0e0e0; display:flex; flex-direction:column; }
        .menu a { display: block; padding: 12px; color: #666; text-decoration: none; margin-bottom: 5px; border-radius: 8px; font-weight: 500; }
        .menu a:hover, .menu a.active { background: #e8f5e9; color: #2e7d32; font-weight: bold; }
        .main-content { flex: 1; padding: 30px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; }

        /* PROFILE CARD STYLE */
        .profile-card { 
            background: white; padding: 50px; border-radius: 12px; text-align: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; width: 400px; margin-top: 50px;
        }
        .avatar { 
            width: 100px; height: 100px; background: #e8f5e9; color: #2ecc71; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            font-size: 3rem; margin: 0 auto 20px; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { color: #333; margin: 0; }
        p { color: #666; margin-top: 5px; }
        
        .role-badge { 
            display: inline-block; background: #dcebdc; color: #2e7d32; 
            padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; margin: 10px 0;
        }
        
        /* SIDEBAR UPDATED */
        .sidebar { width: 250px; background: #fff; color: #555; padding: 20px; border-right: 1px solid #e0e0e0; display:flex; flex-direction:column; }
        .menu { flex: 1; }
        .menu a { display: block; padding: 12px; color: #666; text-decoration: none; margin-bottom: 5px; border-radius: 8px; font-weight: 500; }
        .menu a:hover, .menu a.active { background: #e8f5e9; color: #2e7d32; font-weight: bold; }
        .btn-logout-side { display: block; padding: 12px; color: #e74c3c; text-decoration: none; border-radius: 8px; font-weight: bold; border: 1px solid #fadbd8; text-align: center; margin-top: auto; }
        .btn-logout-side:hover { background: #fadbd8; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="display:flex; gap:10px; align-items:center; margin-bottom:40px;">
            <img src="logo.png" width="35"> <span style="font-size:1.4rem; font-weight:bold; color:#2ecc71;">PestGuard</span>
        </div>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <?php if($_SESSION['role'] != 'guest'): ?>
                <a href="input-data.php">Input Data</a>
                <a href="area.php">Manajemen Area</a>
            <?php endif; ?>
            <a href="laporan.php">Laporan</a>
            <a href="profil.php" class="active">Profil</a>
        </div>
        <a href="logout.php" class="btn-logout-side">Keluar</a>
    </div>

    <div class="main-content">
        <div style="width:100%; max-width:400px; margin-bottom:20px; color:#666;">Home / <span style="color:#5e72e4;">Profil</span></div>

        <div class="profile-card">
            <div class="avatar">👤</div>
            <h2><?php echo $_SESSION['user']; ?></h2>
            <div class="role-badge"><?php echo strtoupper($_SESSION['role']); ?></div>
            
            <p style="color:#27ae60;">● Online</p>
        </div>
    </div>
</body>
</html>