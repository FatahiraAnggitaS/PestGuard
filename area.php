<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['role'] == 'guest') header("Location: dashboard.php");

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM areas WHERE id=$id");
    header("Location: area.php");
}
$result = $conn->query("SELECT * FROM areas ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen Area</title>
    <style>
        /* CSS GLOBAL (Sama di setiap halaman) */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f3fbf2; display: flex; height: 100vh; }
        .sidebar { width: 250px; background: #fff; color: #555; padding: 20px; border-right: 1px solid #e0e0e0; display:flex; flex-direction:column; }
        .menu a { display: block; padding: 12px; color: #666; text-decoration: none; margin-bottom: 5px; border-radius: 8px; font-weight: 500; }
        .menu a:hover, .menu a.active { background: #e8f5e9; color: #2e7d32; font-weight: bold; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }

        /* TEMA BOX */
        .box { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid #eee; }
        .box-title { font-weight: bold; margin-bottom: 20px; color: #333; font-size: 1.1rem; }

        /* TEMA TABEL (Sama dengan Dashboard) */
        table { width: 100%; border-collapse: collapse; }
        th { background: #dcebdc; padding: 12px; text-align: left; border-bottom: 2px solid #c8dec8; font-size: 0.9rem; color: #333; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; color: #555; }
        
        .btn-del { background: #fceceb; color: #e74c3c; padding: 6px 12px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.85rem; border: 1px solid #fadbd8; }
        .btn-del:hover { background: #e74c3c; color: white; }

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
                <a href="area.php" class="active">Manajemen Area</a>
            <?php endif; ?>
            <a href="laporan.php">Laporan</a>
            <a href="profil.php">Profil</a>
        </div>
        <a href="logout.php" class="btn-logout-side">Keluar</a>
    </div>

    <div class="main-content">
        <div style="margin-bottom:20px; color:#666;">Home / <span style="color:#5e72e4;">Manajemen Area</span></div>

        <div class="box">
            <div class="box-title">Daftar Semua Area</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Area</th>
                        <th>Hama</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal Input</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $row['nama_area']; ?></strong></td>
                        <td><?php echo $row['hama']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td>
                            <a href="area.php?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Hapus Data Permanen?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>