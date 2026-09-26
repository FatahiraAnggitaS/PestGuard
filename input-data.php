<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['role'] == 'guest') header("Location: dashboard.php");

// --- 1. SIMPAN DATA VIA AJAX (MULTI HAMA) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if($input) {
        // Proses Data Hama (Array -> String gabungan & Total Jumlah)
        $pestList = $input['pests']; // Array object
        
        $namesArray = [];
        $totalCount = 0;

        foreach($pestList as $p) {
            // Bersihkan input dan masukkan ke array
            $cleanName = trim($p['name']);
            if(!empty($cleanName)){
                $namesArray[] = $cleanName;
                $totalCount += intval($p['count']);
            }
        }

        // Gabungkan nama hama dengan koma (Contoh: "Wereng, Tikus")
        $hamaString = implode(", ", $namesArray);

        $stmt = $conn->prepare("INSERT INTO areas (nama_area, hama, jumlah, status, warna, geometry, tanggal) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $geoJSON = json_encode($input['geometry']); 
        
        // Parameter: nama, hama(string), jumlah(total), status, warna, geojson
        $stmt->bind_param("ssisss", $input['areaName'], $hamaString, $totalCount, $input['status'], $input['color'], $geoJSON);
        
        echo $stmt->execute() ? json_encode(["status" => "success"]) : json_encode(["status" => "error"]);
        exit;
    }
}

// --- 2. AMBIL DATA UNTUK PETA & DATALIST ---
$geoData = [];
$existingPests = []; // Array untuk menampung nama hama unik

$res = $conn->query("SELECT * FROM areas");
while($row = $res->fetch_assoc()) {
    $geoData[] = $row;
    
    // Logika mengambil nama hama unik dari database
    // Jika data database isinya "Wereng, Tikus", kita pecah jadi dua
    $pests = explode(",", $row['hama']);
    foreach($pests as $p) {
        $cleanP = trim($p);
        if(!empty($cleanP) && !in_array($cleanP, $existingPests)) {
            $existingPests[] = $cleanP;
        }
    }
}
sort($existingPests); // Urutkan abjad
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Data</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        /* --- RESET CSS --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f3fbf2; display: flex; height: 100vh; overflow: hidden; }
        
        /* SIDEBAR */
        .sidebar { width: 250px; background: #fff; color: #555; padding: 20px; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column; z-index: 1000; flex-shrink: 0; }
        .menu { flex: 1; }
        .menu a { display: block; padding: 12px; color: #666; text-decoration: none; margin-bottom: 5px; border-radius: 8px; font-weight: 500; }
        .menu a:hover, .menu a.active { background: #e8f5e9; color: #2e7d32; font-weight: bold; }
        .btn-logout-side { display: block; padding: 12px; color: #e74c3c; text-decoration: none; border-radius: 8px; font-weight: bold; border: 1px solid #fadbd8; text-align: center; margin-top: auto; }
        .btn-logout-side:hover { background: #fadbd8; }

        /* MAP */
        .map-container { flex: 1; position: relative; height: 100%; }
        #map { width: 100%; height: 100%; }
        
        /* MODAL */
        #inputModal { 
            display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); 
            background:white; padding:25px; z-index:9999; width:400px; /* Lebar ditambah */
            border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid #eee;
            max-height: 90vh; overflow-y: auto; /* Agar bisa scroll kalau hama banyak */
        }
        
        input, select { width:100%; margin-bottom:12px; padding:10px; border:1px solid #ddd; border-radius:8px; box-sizing:border-box; }
        .btn-save { width:100%; padding:10px; background:#27ae60; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:bold; margin-top: 10px;}
        .btn-cancel { width:100%; padding:10px; background:#e74c3c; color:white; border:none; border-radius:8px; cursor:pointer; margin-top:5px; }
        
        /* MULTI PEST STYLES */
        .pest-row { display: flex; gap: 5px; align-items: center; margin-bottom: 5px; }
        .pest-row input[type="text"] { flex: 2; margin-bottom: 0; }
        .pest-row input[type="number"] { flex: 1; margin-bottom: 0; }
        .btn-add-pest { background: #3498db; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 0.8rem; margin-bottom: 10px; }
        .btn-remove-pest { background: #fadbd8; color: #c0392b; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        
        .map-label { color: white; font-weight: bold; text-shadow: 1px 1px 2px black; text-align: center; font-size: 12px; width: auto !important; }
    </style>
</head>
<body>
    
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div style="display:flex; gap:10px; align-items:center; margin-bottom:40px;">
            <img src="logo.png" width="35"> <span style="font-size:1.4rem; font-weight:bold; color:#2ecc71;">PestGuard</span>
        </div>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="input-data.php" class="active">Input Data</a>
            <a href="area.php">Manajemen Area</a>
            <a href="laporan.php">Laporan</a>
            <a href="profil.php">Profil</a>
        </div>
        <a href="logout.php" class="btn-logout-side">Keluar</a>
    </div>

    <!-- MAP CONTAINER -->
    <div class="map-container">
        <div id="map"></div>
        
        <!-- FORM MODAL -->
        <div id="inputModal">
            <h3 style="margin-top:0; color:#333; margin-bottom: 15px;">Detail Area Baru</h3>
            
            <label style="font-size:0.9rem; font-weight:bold; color:#555;">Nama Area</label>
            <input type="text" id="areaName" placeholder="Contoh: Sawah Blok A">
            
            <label style="font-size:0.9rem; font-weight:bold; color:#555;">Daftar Hama & Jumlah</label>
            <div id="pestContainer">
                <!-- Baris Pertama Default -->
                <div class="pest-row">
                    <input list="pestOptions" class="pest-name" placeholder="Nama Hama">
                    <input type="number" class="pest-count" placeholder="Jml" min="0">
                    <!-- Tombol hapus disembunyikan untuk baris pertama -->
                    <button type="button" class="btn-remove-pest" style="visibility:hidden">×</button>
                </div>
            </div>
            
            <button type="button" class="btn-add-pest" onclick="addPestRow()">+ Tambah Hama Lain</button>

            <!-- DATALIST: Opsi dari Database -->
            <datalist id="pestOptions">
                <?php foreach($existingPests as $p): ?>
                    <option value="<?php echo htmlspecialchars($p); ?>">
                <?php endforeach; ?>
                <!-- Default options jika database kosong -->
                <option value="Wereng Coklat">
                <option value="Tikus Sawah">
                <option value="Ulat Grayak">
                <option value="Walang Sangit">
            </datalist>

            <label style="font-size:0.9rem; font-weight:bold; color:#555; margin-top:10px; display:block;">Status Area</label>
            <select id="statusLevel">
                <option value="Aman">🟢 Aman (Hijau)</option>
                <option value="Waspada">🟡 Waspada (Kuning)</option>
                <option value="Bahaya">🔴 Bahaya (Merah)</option>
            </select>
            
            <button onclick="saveData()" class="btn-save">Simpan Database</button>
            <button onclick="location.reload()" class="btn-cancel">Batal</button>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        var map = L.map('map').setView([-7.805, 110.364], 16);
        L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{maxZoom:20,subdomains:['mt0','mt1','mt2','mt3']}).addTo(map);
        L.Control.geocoder({defaultMarkGeocode:false, position:'topleft'}).on('markgeocode', function(e){ map.fitBounds(L.polygon([e.geocode.bbox.getSouthEast(), e.geocode.bbox.getNorthEast(), e.geocode.bbox.getNorthWest(), e.geocode.bbox.getSouthWest()]).getBounds()); }).addTo(map);

        var drawnItems = new L.FeatureGroup().addTo(map);
        new L.Control.Draw({ edit:{featureGroup:drawnItems}, draw:{circle:false,marker:false,polyline:false,circlemarker:false} }).addTo(map);
        var currentLayer=null, dbData=<?php echo json_encode($geoData); ?>;

        const urlParams = new URLSearchParams(window.location.search);
        const focusId = urlParams.get('focus');

        dbData.forEach(item => {
            if(item.geometry) {
                var l = L.geoJSON(JSON.parse(item.geometry),{style:{color:item.warna,fillColor:item.warna,fillOpacity:0.5}}).bindPopup(`<b>${item.nama_area}</b><br>Status: ${item.status}<br>Hama: ${item.hama} (${item.jumlah})`);
                drawnItems.addLayer(l);
                l.eachLayer(function(x){ L.marker(x.getBounds().getCenter(),{icon:L.divIcon({className:'map-label',html:item.nama_area,iconSize:[100,20]})}).addTo(map); });
                if(focusId && item.id==focusId) setTimeout(()=>{ map.fitBounds(l.getBounds(),{padding:[100,100]}); l.openPopup(); },500);
            }
        });

        map.on(L.Draw.Event.CREATED, function(e){ currentLayer=e.layer; document.getElementById('inputModal').style.display='block'; });

        // --- FUNGSI TAMBAH BARIS HAMA ---
        function addPestRow() {
            var container = document.getElementById('pestContainer');
            var div = document.createElement('div');
            div.className = 'pest-row';
            div.innerHTML = `
                <input list="pestOptions" class="pest-name" placeholder="Nama Hama">
                <input type="number" class="pest-count" placeholder="Jml" min="0">
                <button type="button" class="btn-remove-pest" onclick="this.parentElement.remove()">×</button>
            `;
            container.appendChild(div);
        }

        // --- FUNGSI SIMPAN DATA (MULTI INPUT) ---
        function saveData(){
            var areaName = document.getElementById('areaName').value;
            var status = document.getElementById('statusLevel').value;
            var color = (status==='Bahaya')?'#e74c3c':((status==='Waspada')?'#f1c40f':'#2ecc71');

            // Ambil semua data hama dari baris-baris input
            var pestNames = document.querySelectorAll('.pest-name');
            var pestCounts = document.querySelectorAll('.pest-count');
            var pestsData = [];
            var valid = false;

            for(var i=0; i<pestNames.length; i++) {
                var name = pestNames[i].value;
                var count = pestCounts[i].value;
                if(name && count) {
                    pestsData.push({ name: name, count: count });
                    valid = true;
                }
            }

            if(!areaName || !valid) { alert("Nama Area dan minimal 1 Hama/Jumlah harus diisi!"); return; }

            var payload = {
                areaName: areaName,
                pests: pestsData, // Array Hama
                status: status,
                color: color,
                geometry: currentLayer.toGeoJSON().geometry
            };

            fetch('input-data.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify(payload)
            })
            .then(r=>r.json())
            .then(d=>{
                if(d.status==='success'){
                    alert("Data Berhasil Disimpan!");
                    location.reload();
                } else {
                    alert("Gagal menyimpan data.");
                }
            });
        }
    </script>
</body>
</html>