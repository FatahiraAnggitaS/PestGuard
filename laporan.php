<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) header("Location: index.php");

$where = "";
if (isset($_GET['filter']) && $_GET['filter'] != 'Semua') {
    $s = $_GET['filter'];
    $where = "WHERE status = '$s'";
}
$result = $conn->query("SELECT * FROM areas $where ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan</title>
    <style>
        /* CSS GLOBAL */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f3fbf2; display: flex; height: 100vh; }
        .sidebar { width: 250px; background: #fff; color: #555; padding: 20px; border-right: 1px solid #e0e0e0; display:flex; flex-direction:column; }
        .menu a { display: block; padding: 12px; color: #666; text-decoration: none; margin-bottom: 5px; border-radius: 8px; font-weight: 500; }
        .menu a:hover, .menu a.active { background: #e8f5e9; color: #2e7d32; font-weight: bold; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }

        /* TEMA BOX */
        .box { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid #eee; margin-bottom: 20px; }
        
        /* TEMA TABEL */
        table { width: 100%; border-collapse: collapse; }
        th { background: #dcebdc; padding: 12px; text-align: left; border-bottom: 2px solid #c8dec8; font-size: 0.9rem; color: #333; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; color: #555; }
        
        /* CONTROLS */
        .controls { display: flex; gap: 10px; align-items: center; }
        select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; }
        .btn-filter { background: #2ecc71; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-export { background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; margin-left: auto; font-weight: bold; }
        
        /* SIDEBAR UPDATED */
        .sidebar { width: 250px; background: #fff; color: #555; padding: 20px; border-right: 1px solid #e0e0e0; display:flex; flex-direction:column; }
        .menu { flex: 1; }
        .menu a { display: block; padding: 12px; color: #666; text-decoration: none; margin-bottom: 5px; border-radius: 8px; font-weight: 500; }
        .menu a:hover, .menu a.active { background: #e8f5e9; color: #2e7d32; font-weight: bold; }
        .btn-logout-side { display: block; padding: 12px; color: #e74c3c; text-decoration: none; border-radius: 8px; font-weight: bold; border: 1px solid #fadbd8; text-align: center; margin-top: auto; }
        .btn-logout-side:hover { background: #fadbd8; }

        @media print { .sidebar, .controls { display: none; } body { background: white; } .main-content { margin:0; padding:0; } .box { border: none; shadow: none; } }
    </style>
    <script>
        function exportToExcel() {
            var table = document.getElementById('reportTable');
            var rows = table.querySelectorAll('tr');
            var csv = [];
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll('td, th');
                for (var j = 0; j < cols.length; j++) {
                    row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"'); // Escape quotes for CSV
                }
                csv.push(row.join(','));
            }
            var csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'laporan.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
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
            <a href="laporan.php" class="active">Laporan</a>
            <a href="profil.php">Profil</a>
        </div>
        <a href="logout.php" class="btn-logout-side">Keluar</a>
    </div>

    <div class="main-content">
        <div style="margin-bottom:20px; color:#666;">Home / <span style="color:#5e72e4;">Laporan</span></div>

        <div class="box">
            <form class="controls" method="GET">
                <label>Filter Status:</label>
                <select name="filter">
                    <option value="Semua">Semua</option>
                    <option value="Aman">Aman</option>
                    <option value="Waspada">Waspada</option>
                    <option value="Bahaya">Bahaya</option>
                </select>
                <button type="submit" class="btn-filter">Terapkan</button>
                <button type="button" class="btn-export" onclick="exportToExcel()">📊 Ekspor Excel</button>
            </form>
        </div>

        <div class="box">
            <table id="reportTable">
                <thead><tr><th>Tanggal</th><th>Area</th><th>Hama</th><th>Jumlah</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if($result->num_rows == 0): ?>
                        <tr><td colspan="5" style="text-align:center;">Data tidak ditemukan.</td></tr>
                    <?php else: ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td><strong><?php echo $row['nama_area']; ?></strong></td>
                            <td><?php echo $row['hama']; ?></td>
                            <td><?php echo $row['jumlah']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>