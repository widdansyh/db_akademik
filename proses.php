<?php
// Start session untuk message
session_start();

// Include koneksi database
require_once 'koneksi.php';

// Proses simpan data jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] == 'save') {
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $program_studi = mysqli_real_escape_string($koneksi, $_POST['program_studi']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    // Cek apakah NIM sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nim='$nim'");
    if (mysqli_num_rows($cek) == 0) {
        // Simpan data
        $query = "INSERT INTO mahasiswa (nim, nama, program_studi, alamat) 
                  VALUES ('$nim', '$nama', '$program_studi', '$alamat')";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['message'] = "Data berhasil disimpan!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menyimpan data";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "NIM '$nim' sudah terdaftar!";
        $_SESSION['message_type'] = "error";
    }
    
    // Redirect kembali ke index.php
    header('Location: index.php');
    exit();
}

// Proses hapus data jika ada GET hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM mahasiswa WHERE id=$id";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['message'] = "Data berhasil dihapus!";
        $_SESSION['message_type'] = "success";
    }
    
    // Redirect ke halaman yang sama
    header('Location: proses.php');
    exit();
}

// Ambil data mahasiswa untuk ditampilkan
$query = "SELECT * FROM mahasiswa ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);

// Hitung total data
$total_query = "SELECT COUNT(*) as total FROM mahasiswa";
$total_result = mysqli_query($koneksi, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Segoe UI', Arial, sans-serif; 
        }
        
        body { 
            background: #fce7f3; 
            padding: 20px;
        }
        
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
        }
        
        .header { 
            text-align: center; 
            color: #9d174d; 
            padding: 30px 0; 
            margin-bottom: 20px; 
        }
        
        .header h1 { 
            font-size: 24px; 
            margin-bottom: 10px; 
        }
        
        .card { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(244, 114, 182, 0.2); 
            overflow: hidden; 
            margin-bottom: 30px;
        }
        
        .card-header { 
            background: linear-gradient(45deg, #ec4899, #f472b6); 
            padding: 20px; 
            color: white; 
        }
        
        .card-header h2 { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            font-size: 18px; 
        }
        
        .card-body { 
            padding: 30px; 
        }
        
        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(244, 114, 182, 0.1);
            border: 2px solid #fce7f3;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #fce7f3;
            border-left: 4px solid #ec4899;
            color: #9d174d;
        }
        
        .student-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 2px solid #fce7f3;
            position: relative;
        }
        
        .student-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: #ec4899;
        }
        
        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .student-name {
            font-size: 16px;
            font-weight: 600;
            color: #9d174d;
        }
        
        .student-nim {
            background: #ec4899;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .student-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #666;
            padding: 8px;
            background: #fce7f3;
            border-radius: 8px;
        }
        
        .detail-item i {
            color: #ec4899;
            width: 16px;
        }
        
        .alamat-full {
            grid-column: 1 / -1;
            font-size: 13px;
        }
        
        .delete-btn {
            background: #f43f5e;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }
        
        .nav-btn {
            display: block;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 2px solid #ec4899;
            background: white;
            color: #ec4899;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        
        .nav-btn:hover {
            background: #ec4899;
            color: white;
        }
        
        .footer {
            text-align: center;
            color: #9d174d;
            padding: 20px;
            margin-top: 30px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-university"></i> UNIVERSITAS ICHSAN SIDENRENG RAPPANG</h1>
            <p>Data Mahasiswa Tersimpan</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $_SESSION['message']; ?></span>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-database"></i> Data Mahasiswa Tersimpan</h2>
            </div>
            <div class="card-body">
                <div class="stats">
                    <div class="stat-card">
                        <div style="font-size: 20px; color: #ec4899; margin-bottom: 5px;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div style="font-size: 18px; font-weight: bold; color: #9d174d;"><?php echo $total_data; ?></div>
                        <div style="font-size: 13px; color: #666;">Total Mahasiswa</div>
                    </div>
                </div>

                <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="student-item">
                                <div class="student-header">
                                    <div class="student-name"><?php echo htmlspecialchars($row['nama']); ?></div>
                                    <div class="student-nim"><?php echo htmlspecialchars($row['nim']); ?></div>
                                </div>
                                
                                <div class="student-details">
                                    <div class="detail-item">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>Program Studi:</span>
                                        <span style="font-weight: 500;"><?php echo htmlspecialchars($row['program_studi']); ?></span>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>Ditambahkan:</span>
                                        <span><?php echo date('d M Y H:i', strtotime($row['tanggal_input'])); ?></span>
                                    </div>
                                    
                                    <div class="detail-item alamat-full">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Alamat:</span>
                                        <span><?php echo htmlspecialchars($row['alamat']); ?></span>
                                    </div>
                                </div>
                                
                                <a href="?hapus=<?php echo $row['id']; ?>" 
                                   class="delete-btn"
                                   onclick="return confirm('Hapus data mahasiswa <?php echo addslashes($row['nama']); ?>?')">
                                    <i class="fas fa-trash-alt"></i> Hapus Data
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                            <i class="fas fa-database" style="font-size: 40px; margin-bottom: 15px; color: #f9a8d4;"></i>
                            <h3 style="color: #ec4899; margin-bottom: 10px;">Belum ada data mahasiswa</h3>
                            <p>Klik tombol di bawah untuk menambahkan data</p>
                        </div>
                    <?php endif; ?>
                </div>

                <a href="index.php" class="nav-btn">
                    <i class="fas fa-arrow-left"></i> KEMBALI KE FORM INPUT
                </a>
            </div>
        </div>

        <div class="footer">
            <p><i class="fas fa-heart"></i> UNIVERSITAS ICHSAN SIDENRENG RAPPANG <i class="fas fa-heart"></i></p>
            <p style="margin-top: 10px;">Sistem Manajemen Data Mahasiswa</p>
        </div>
    </div>
</body>
</html>
<?php
// Tutup koneksi
if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>
