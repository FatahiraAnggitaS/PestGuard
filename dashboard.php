<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) header("Location: index.php");

// 1. DATA KARTU ATAS (Lengkap dengan Aman)
$total   = $conn->query("SELECT COUNT(*) as c FROM areas")->fetch_assoc()['c'];
$merah   = $conn->query("SELECT COUNT(*) as c FROM areas WHERE status='Bahaya'")->fetch_assoc()['c'];
$kuning  = $conn->query("SELECT COUNT(*) as c FROM areas WHERE status='Waspada'")->fetch_assoc()['c'];
$aman    = $conn->query("SELECT COUNT(*) as c FROM areas WHERE status='Aman'")->fetch_assoc()['c'];

// 2. DATA TABEL (Ambil 20 data agar fitur scroll berguna)
$recent  = $conn->query("SELECT * FROM areas ORDER BY tanggal DESC LIMIT 20");

// 3. DATA CHART
$statusRes = $conn->query("SELECT status, COUNT(*) as c FROM areas GROUP BY status");
$statLabels = []; $statData = [];
while($r = $statusRes->fetch_assoc()) { $statLabels[]=$r['status']; $statData[]=$r['c']; }

$hamaRes = $conn->query("SELECT hama, COUNT(*) as c FROM areas GROUP BY hama");
$hamaLabels = []; $hamaData = [];
while($r = $hamaRes->fetch_assoc()) { $hamaLabels[]=$r['hama']; $hamaData[]=$r['c']; }

// 4. MAP DATA
$mapData = [];
$allMaps = $conn->query("SELECT * FROM areas");
while($row = $allMaps->fetch_assoc()) { $mapData[] = $row; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard - PestGuard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f3fbf2; display: flex; height: 100vh; overflow: hidden; }
        
        /* SIDEBAR */
        .sidebar { width: 250px; background: #fff; color: #555; padding: 20px; border-right: 1px solid #e0e0e0; display:flex; flex-direction:column; flex-shrink: 0; }
        .menu { flex: 1; }
        .menu a { display: block; padding: 12px; color: #666; text-decoration: none; margin-bottom: 5px; border-radius: 8px; font-weight: 500; }
        .menu a:hover, .menu a.active { background: #e8f5e9; color: #2e7d32; font-weight: bold; }
        .btn-logout-side { display: block; padding: 12px; color: #e74c3c; text-decoration: none; border-radius: 8px; font-weight: bold; border: 1px solid #fadbd8; text-align: center; margin-top: auto; }
        .btn-logout-side:hover { background: #fadbd8; }

        /* MAIN CONTENT */
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }

        /* KARTU ATAS (4 Kolom) */
        .cards-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .card { border-radius: 12px; padding: 20px; text-align: center; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .card h1 { font-size: 2.5rem; margin: 0; }
        .card p { margin-top: 5px; font-size: 0.9rem; font-weight: 600; opacity: 0.9; }
        
        .card-blue   { background: #5D9CEC; }
        .card-red    { background: #f09494; }
        .card-yellow { background: #ebd57d; }
        .card-green  { background: #95b787; } /* Warna Hijau Aman */
        
        /* LAYOUT KONTEN */
        .content-row { display: grid; grid-template-columns: 6fr 4fr; gap: 20px; margin-bottom: 30px; }
        .box { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid #eee; display: flex; flex-direction: column;}
        .box-title { font-weight: bold; margin-bottom: 15px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        
        /* TABLE SCROLL */
        .table-scroll-container { max-height: 300px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #dcebdc; padding: 12px; text-align: left; font-size: 0.9rem; color: #333; font-weight: 600; position: sticky; top: 0; z-index: 1; }
        td { padding: 12px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
        .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        
        /* MAP */
        #miniMap { width: 100%; height: 300px; border-radius: 8px; z-index: 0; }

        /* CHARTS */
        .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .chart-wrapper { position: relative; height: 250px; width: 100%; display: flex; justify-content: center; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="display:flex; gap:10px; align-items:center; margin-bottom:40px;">
            <img src="logo.png" width="35"> <span style="font-size:1.4rem; font-weight:bold; color:#2ecc71;">PestGuard</span>
        </div>
        <div class="menu">
            <a href="dashboard.php" class="active">Dashboard</a>
            <?php if($_SESSION['role'] != 'guest'): ?>
                <a href="input-data.php">Input Data</a>
                <a href="area.php">Manajemen Area</a>
            <?php endif; ?>
            <a href="laporan.php">Laporan</a>
            <a href="profil.php">Profil</a>
        </div>
        <a href="logout.php" class="btn-logout-side">Keluar</a>
    </div>

    <div class="main-content">
        <div style="margin-bottom:20px; color:#666;">Home / <span style="color:#5e72e4;">Dashboard</span></div>

        <!-- 1. KARTU ATAS (4 Kolom: Total, Bahaya, Waspada, Aman) -->
        <div class="cards-container">
            <div class="card card-blue"><h1><?php echo $total; ?></h1><p>Total Area</p></div>
            <div class="card card-red"><h1><?php echo $merah; ?></h1><p>Bahaya</p></div>
            <div class="card card-yellow"><h1><?php echo $kuning; ?></h1><p>Waspada</p></div>
            <div class="card card-green"><h1><?php echo $aman; ?></h1><p>Aman</p></div>
        </div>

        <!-- 2. TABEL SCROLL & PETA -->
        <div class="content-row">
            <div class="box">
                <div class="box-title">Status Hama Terkini</div>
                <div class="table-scroll-container">
                    <table>
                        <thead><tr><th>Area</th><th>Hama</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php while($row = $recent->fetch_assoc()): 
                                $color = ($row['status']=='Bahaya')?'#e74c3c':(($row['status']=='Waspada')?'#f1c40f':'#2ecc71');
                            ?>
                            <tr>
                                <td><?php echo $row['nama_area']; ?></td>
                                <td><?php echo $row['hama']; ?></td>
                                <td><span class="dot" style="background:<?php echo $color; ?>"></span><?php echo $row['status']; ?></td>
                                <td><a href="input-data.php?focus=<?php echo $row['id']; ?>" style="color:#3498db; text-decoration:none;">🔍 Detail</a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="box">
                <div class="box-title">Peta Sebaran</div>
                <div id="miniMap"></div>
            </div>
        </div>

        <!-- 3. GRAFIK -->
        <div class="charts-row">
            <div class="box">
                <div class="box-title">Statistik Jenis Hama</div>
                <div class="chart-wrapper">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
            <div class="box">
                <div class="box-title">Persentase Status</div>
                <div class="chart-wrapper">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- 1. CHART: BAR (Jenis Hama - Warna Warni) ---
        const barLabels = <?php echo json_encode($hamaLabels); ?>;
        const barData = <?php echo json_encode($hamaData); ?>;
        const vibrantColors = ['#3498db', '#9b59b6', '#e67e22', '#1abc9c', '#34495e', '#f1c40f', '#e74c3c', '#2ecc71', '#e84393', '#fd79a8'];

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{ label: 'Jumlah', data: barData, backgroundColor: vibrantColors }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // --- 2. CHART: PIE (Status - Warna Semantik) ---
        const pieLabels = <?php echo json_encode($statLabels); ?>;
        const pieData = <?php echo json_encode($statData); ?>;
        const pieColors = pieLabels.map(label => {
            if (label === 'Aman') return '#2ecc71';
            if (label === 'Waspada') return '#f1c40f';
            if (label === 'Bahaya') return '#e74c3c';
            return '#ccc';
        });

        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{ data: pieData, backgroundColor: pieColors }]
            }, 
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });

        // --- 3. MAP (POIs HIDDEN) ---
        var map = L.map('miniMap', { zoomControl: false, attributionControl: false });
        
        // Perhatikan bagian '&apistyle=s.t:2|p.v:off' <-- Ini yang menyembunyikan icon tempat
        L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&apistyle=s.t:2|p.v:off', {
            maxZoom: 20, 
            subdomains: ['mt0','mt1','mt2','mt3']
        }).addTo(map);

        var mapData = <?php echo json_encode($mapData); ?>;
        var featureGroup = L.featureGroup().addTo(map);

        mapData.forEach(item => {
            if(item.geometry) {
                var layer = L.geoJSON(JSON.parse(item.geometry), {
                    style: { color: item.warna, fillColor: item.warna, fillOpacity: 0.6, weight: 1 }
                }).bindPopup(`<b>${item.nama_area}</b><br>${item.status}`);
                layer.addTo(featureGroup);
            }
        });

        if(mapData.length > 0) {
            map.fitBounds(featureGroup.getBounds(), { padding: [20, 20] });
        } else {
            map.setView([-7.805, 110.364], 13);
        }
    </script>
</body>
</html>