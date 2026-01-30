<?php
// Include koneksi database
require_once 'koneksi.php';

// Cek apakah user ingin ke form input atau lihat data
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// Jika ada POST dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
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
            $message = "✨ Data berhasil disimpan!";
            $message_type = "success";
        } else {
            $message = "❌ Gagal menyimpan data";
            $message_type = "error";
        }
    } else {
        $message = "⚠️ NIM '$nim' sudah terdaftar!";
        $message_type = "error";
    }
    
    // Tampilkan form lagi dengan pesan
    $action = 'input';
}

// Jika hapus data
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM mahasiswa WHERE id=$id";
    
    if (mysqli_query($koneksi, $query)) {
        $message = "🗑️ Data berhasil dihapus!";
        $message_type = "success";
    }
    
    $action = 'data';
}

// Ambil data untuk tampilan data
if ($action == 'data') {
    $query = "SELECT * FROM mahasiswa ORDER BY id DESC";
    $result = mysqli_query($koneksi, $query);
    
    $total_query = "SELECT COUNT(*) as total FROM mahasiswa";
    $total_result = mysqli_query($koneksi, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_data = $total_row['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNIVERSITAS ICHSAN SIDENRENG RAPPANG</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif; 
        }
        
        body { 
            background: linear-gradient(135deg, #ffccd5 0%, #ffafcc 50%, #ff8fab 100%);
            min-height: 100vh; 
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* BACKGROUND ELEMENTS */
        .bg-element {
            position: fixed;
            z-index: -1;
            opacity: 0.3;
        }
        
        .circle-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, #ec4899, transparent 70%);
            border-radius: 50%;
            top: 10%;
            left: 5%;
            animation: float 6s ease-in-out infinite;
        }
        
        .circle-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #f472b6, transparent 70%);
            border-radius: 50%;
            bottom: 10%;
            right: 5%;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        .accent-1, .accent-2, .accent-3 {
            font-size: 4rem;
            color: rgba(236, 72, 153, 0.1);
            position: fixed;
            z-index: -1;
        }
        
        .accent-1 {
            top: 20%;
            left: 10%;
            animation: spin 20s linear infinite;
        }
        
        .accent-2 {
            bottom: 20%;
            right: 10%;
            animation: spin 25s linear infinite reverse;
        }
        
        .accent-3 {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 10rem;
            opacity: 0.05;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(244, 114, 182, 0.4);
            overflow: hidden;
            display: flex;
            min-height: 750px;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255, 255, 255, 0.8);
        }
        
        /* LEFT PANEL - For Input Form */
        .left-panel {
            flex: 1;
            background: linear-gradient(rgba(156, 23, 77, 0.9), rgba(236, 72, 153, 0.9)), 
                        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><path fill="white" opacity="0.1" d="M50,0 C77.614,0 100,22.386 100,50 C100,77.614 77.614,100 50,100 C22.386,100 0,77.614 0,50 C0,22.386 22.386,0 50,0 Z M50,90 C77.614,90 90,77.614 90,50 C90,22.386 77.614,10 50,10 C22.386,10 10,22.386 10,50 C10,77.614 22.386,90 50,90 Z"/></svg>');
            background-repeat: repeat;
            opacity: 0.2;
        }
        
        .university-logo {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 25px;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
            position: relative;
            z-index: 1;
        }
        
        .university-logo i {
            color: #f9a8d4;
            margin-right: 15px;
            text-shadow: 0 0 10px rgba(249, 168, 212, 0.5);
        }
        
        .left-panel h1 {
            font-size: 36px;
            margin-bottom: 20px;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .left-panel p {
            font-size: 18px;
            line-height: 1.6;
            opacity: 0.95;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        
        .features {
            list-style: none;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }
        
        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .features li:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(10px);
        }
        
        .features i {
            color: #f9a8d4;
            font-size: 18px;
            min-width: 25px;
        }
        
        /* RIGHT PANEL */
        .right-panel {
            flex: 1;
            padding: 50px;
            overflow-y: auto;
            background: linear-gradient(135deg, #fffdfe 0%, #fffafb 100%);
        }
        
        /* HOME PAGE - IMPROVED DESIGN */
        .home-container {
            text-align: center;
            padding: 40px 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .home-title {
            color: #9d174d;
            font-size: 48px;
            margin-bottom: 15px;
            font-weight: 800;
            text-shadow: 2px 2px 0px rgba(255, 255, 255, 0.8);
            position: relative;
            display: inline-block;
        }
        
        .home-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 5px;
            background: linear-gradient(90deg, #ec4899, #f472b6);
            border-radius: 5px;
        }
        
        .home-subtitle {
            color: #ec4899;
            font-size: 28px;
            margin-bottom: 40px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .developer-info {
            background: linear-gradient(45deg, #ec4899, #f472b6);
            color: white;
            border-radius: 20px;
            padding: 20px 40px;
            margin: 30px auto;
            display: inline-block;
            box-shadow: 0 15px 30px rgba(236, 72, 153, 0.4);
            border: 3px solid white;
            font-size: 18px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .developer-info::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            z-index: -1;
        }
        
        .welcome-text {
            color: #666;
            margin: 40px 0;
            font-size: 18px;
            line-height: 1.8;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            padding: 25px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(244, 114, 182, 0.1);
            border: 2px solid #fce7f3;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .action-btn {
            padding: 25px 30px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 700;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            z-index: -1;
            transition: all 0.4s ease;
        }
        
        .action-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: all 0.6s ease;
            z-index: -1;
        }
        
        .action-btn:hover::after {
            left: 100%;
        }
        
        .btn-input {
            background: linear-gradient(45deg, #ec4899, #f472b6);
            color: white;
            box-shadow: 0 15px 30px rgba(236, 72, 153, 0.4);
        }
        
        .btn-data {
            background: white;
            color: #ec4899;
            border: 3px solid #ec4899;
            box-shadow: 0 10px 20px rgba(236, 72, 153, 0.2);
        }
        
        .btn-input:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 25px 40px rgba(236, 72, 153, 0.6);
        }
        
        .btn-data:hover {
            transform: translateY(-8px) scale(1.05);
            background: #ec4899;
            color: white;
            box-shadow: 0 25px 40px rgba(236, 72, 153, 0.4);
        }
        
        /* FOOTER - HOME PAGE */
        .home-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 3px solid #fce7f3;
        }
        
        .home-footer .footer-title {
            color: #9d174d;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        /* FORM STYLES */
        .form-container {
            padding: 20px 0;
        }
        
        .form-title {
            color: #9d174d;
            font-size: 28px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 700;
        }
        
        .form-group { 
            margin-bottom: 25px; 
        }
        
        label { 
            display: block; 
            margin-bottom: 10px; 
            color: #9d174d; 
            font-weight: 600; 
            font-size: 16px;
        }
        
        .input-group { 
            position: relative; 
        }
        
        .input-group i { 
            position: absolute; 
            left: 20px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #ec4899; 
            font-size: 18px;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 16px 20px 16px 55px;
            background: #fff;
            border: 2px solid #fce7f3;
            border-radius: 15px;
            color: #333;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        select {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ec4899' stroke-width='3'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 20px center;
            background-size: 18px;
            padding-right: 50px;
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #ec4899;
            box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.2);
        }
        
        .form-help {
            color: #f472b6;
            margin-top: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-submit {
            background: linear-gradient(45deg, #ec4899, #f472b6);
            color: white;
            border: none;
            padding: 20px;
            width: 100%;
            border-radius: 15px;
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit:hover {
            background: linear-gradient(45deg, #f472b6, #ec4899);
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(236, 72, 153, 0.4);
        }
        
        .back-btn {
            display: block;
            width: 100%;
            padding: 16px;
            border-radius: 15px;
            border: 3px solid #ec4899;
            background: white;
            color: #ec4899;
            text-align: center;
            text-decoration: none;
            margin-top: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: #ec4899;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(236, 72, 153, 0.3);
        }
        
        /* DATA VIEW */
        .data-container {
            padding: 20px 0;
        }
        
        .data-title {
            color: #9d174d;
            font-size: 28px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 700;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(244, 114, 182, 0.2);
            border: 3px solid #fce7f3;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #f472b6;
            box-shadow: 0 15px 30px rgba(244, 114, 182, 0.3);
        }
        
        .stat-icon {
            font-size: 32px;
            color: #ec4899;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: #9d174d;
            margin-bottom: 10px;
        }
        
        .student-list {
            max-height: 450px;
            overflow-y: auto;
            padding-right: 15px;
        }
        
        .student-item {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            border: 3px solid #fce7f3;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .student-item:hover {
            transform: translateX(10px);
            border-color: #f9a8d4;
            box-shadow: 0 15px 30px rgba(244, 114, 182, 0.2);
        }
        
        .student-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: linear-gradient(to bottom, #ec4899, #f9a8d4);
        }
        
        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .student-name {
            font-size: 20px;
            font-weight: 700;
            color: #9d174d;
            position: relative;
            padding-left: 15px;
        }
        
        .student-name::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: #f472b6;
            border-radius: 50%;
        }
        
        .student-nim {
            background: linear-gradient(45deg, #ec4899, #f472b6);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(236, 72, 153, 0.3);
        }
        
        .student-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            color: #666;
            padding: 12px;
            background: #fce7f3;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .detail-item:hover {
            background: #f9a8d4;
            color: #9d174d;
        }
        
        .detail-item i {
            color: #ec4899;
            width: 20px;
            font-size: 16px;
        }
        
        .alamat-full {
            grid-column: 1 / -1;
            font-size: 15px;
        }
        
        .delete-btn {
            background: linear-gradient(45deg, #f43f5e, #fb7185);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .delete-btn:hover {
            background: linear-gradient(45deg, #fb7185, #f43f5e);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 20px rgba(244, 63, 94, 0.3);
        }
        
        /* MESSAGE */
        .message {
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideIn 0.5s ease;
            border-left: 5px solid;
            font-size: 16px;
        }
        
        .message-success {
            background: #fce7f3;
            border-left-color: #ec4899;
            color: #9d174d;
        }
        
        .message-error {
            background: #ffe4e6;
            border-left-color: #f43f5e;
            color: #be123c;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* FOOTER */
        .footer {
            text-align: center;
            color: #9d174d;
            padding: 25px;
            margin-top: 40px;
            font-size: 16px;
            border-top: 3px solid #fce7f3;
        }
        
        .footer-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .footer-title i {
            color: #ec4899;
            animation: heartbeat 1.5s infinite;
        }
        
        @keyframes heartbeat {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
                min-height: auto;
            }
            
            .left-panel {
                padding: 40px 30px;
            }
            
            .right-panel {
                padding: 40px 30px;
            }
            
            .home-title {
                font-size: 36px;
            }
            
            .home-subtitle {
                font-size: 24px;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .student-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- BACKGROUND ELEMENTS -->
    <div class="bg-element circle-1"></div>
    <div class="bg-element circle-2"></div>
    <div class="accent-1">🌸</div>
    <div class="accent-2">🏵️</div>
    <div class="accent-3">🎓</div>
    
    <div class="container">
        <!-- LEFT PANEL WITH IMAGE (only show for input form) -->
        <?php if ($action == 'input'): ?>
        <div class="left-panel">
            <div class="university-logo">
                <i class="fas fa-university"></i> UNIVERSITAS ICHSAN
            </div>
            <h1>SIDENRENG RAPPANG</h1>
            <p>Sistem Manajemen Data Mahasiswa yang terintegrasi untuk pengelolaan informasi akademik yang lebih efisien dan modern.</p>
            
            <ul class="features">
                <li><i class="fas fa-check-circle"></i> Input data mahasiswa dengan mudah</li>
                <li><i class="fas fa-check-circle"></i> Kelola database akademik terpusat</li>
                <li><i class="fas fa-check-circle"></i> Akses data kapan saja, di mana saja</li>
                <li><i class="fas fa-check-circle"></i> Antarmuka yang ramah pengguna</li>
            </ul>
            
            <div class="developer-info">
                <span><i class="fas fa-user-graduate"></i> Wilda Naisyah - KS1124026 - Informatika</span>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <?php if ($action == 'home'): ?>
                <!-- HOME PAGE -->
                <div class="home-container">
                    <h1 class="home-title">
                        <i class="fas fa-university"></i> UNIVERSITAS ICHSAN
                    </h1>
                    <h2 class="home-subtitle">SIDENRENG RAPPANG</h2>
                    
                    <div class="developer-info">
                        <span><i class="fas fa-user-graduate"></i> Wilda Naisyah - KS1124026 - Informatika</span>
                    </div>
                    
                    <div class="welcome-text">
                        <p>Selamat datang di <strong>Sistem Manajemen Data Mahasiswa</strong> Universitas Ichsan Sidenreng Rappang.</p>
                        <p>Sistem ini dirancang untuk memudahkan pengelolaan data mahasiswa secara digital dengan antarmuka yang intuitif dan fitur yang lengkap.</p>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="?action=input" class="action-btn btn-input">
                            <i class="fas fa-user-plus"></i> INPUT DATA MAHASISWA
                        </a>
                        <a href="?action=data" class="action-btn btn-data">
                            <i class="fas fa-database"></i> LIHAT DATA MAHASISWA
                        </a>
                    </div>
                    
                    <div class="home-footer">
                        <div class="footer-title">
                            <i class="fas fa-heart"></i> UNIVERSITAS ICHSAN SIDENRENG RAPPANG <i class="fas fa-heart"></i>
                        </div>
                        <p style="color: #666; margin-top: 10px;">Sistem Manajemen Data Mahasiswa © 2026</p>
                    </div>
                </div>
                
            <?php elseif ($action == 'input'): ?>
                <!-- FORM INPUT -->
                <div class="form-container">
                    <h2 class="form-title">
                        <i class="fas fa-user-plus"></i> Form Input Data Mahasiswa
                    </h2>
                    
                    <?php if (isset($message)): ?>
                        <div class="message message-<?php echo $message_type; ?>">
                            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                            <span><?php echo $message; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="?action=input">
                        <div class="form-group">
                            <label for="nim"><i class="fas fa-hashtag"></i> NIM</label>
                            <div class="input-group">
                                <i class="fas fa-id-card"></i>
                                <input type="text" id="nim" name="nim" placeholder="Contoh: KS1124026" required>
                            </div>
                            <span class="form-help">
                                <i class="fas fa-info-circle"></i> Format NIM: KS + 7 digit angka
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="nama"><i class="fas fa-user-circle"></i> Nama Lengkap</label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap mahasiswa" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="program_studi"><i class="fas fa-graduation-cap"></i> Program Studi</label>
                            <div class="input-group">
                                <i class="fas fa-graduation-cap"></i>
                                <select id="program_studi" name="program_studi" required>
                                    <option value="">Pilih Program Studi</option>
                                    <option value="Teknik Informatika">💻 Teknik Informatika</option>
                                    <option value="Sistem Informasi">📊 Sistem Informasi</option>
                                    <option value="Teknik Komputer">🖥️ Teknik Komputer</option>
                                    <option value="Manajemen Informatika">👩‍💼 Manajemen Informatika</option>
                                    <option value="Ilmu Komputer">🔬 Ilmu Komputer</option>
                                    <option value="Desain Komunikasi Visual">🎨 Desain Komunikasi Visual</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat"><i class="fas fa-map-marked-alt"></i> Alamat Lengkap</label>
                            <div class="input-group">
                                <i class="fas fa-map-marker-alt"></i>
                                <textarea id="alamat" name="alamat" placeholder="Masukkan alamat lengkap (jalan, kota, provinsi)" required></textarea>
                            </div>
                        </div>

                        <button type="submit" name="submit" class="btn-submit">
                            <i class="fas fa-cloud-upload-alt"></i> SIMPAN DATA MAHASISWA
                        </button>
                    </form>

                    <a href="?action=data" class="back-btn">
                        <i class="fas fa-database"></i> LIHAT DATA TERSIMPAN
                    </a>
                    
                    <a href="?action=home" class="back-btn" style="margin-top: 15px;">
                        <i class="fas fa-home"></i> KEMBALI KE HALAMAN UTAMA
                    </a>
                    
                    <div class="footer">
                        <div class="footer-title">
                            <i class="fas fa-heart"></i> UNIVERSITAS ICHSAN SIDENRENG RAPPANG <i class="fas fa-heart"></i>
                        </div>
                        <p style="margin-top: 10px;">Sistem Manajemen Data Mahasiswa © 2026</p>
                    </div>
                </div>
                
            <?php elseif ($action == 'data'): ?>
                <!-- DATA VIEW -->
                <div class="data-container">
                    <h2 class="data-title">
                        <i class="fas fa-database"></i> Data Mahasiswa Tersimpan
                    </h2>
                    
                    <?php if (isset($message)): ?>
                        <div class="message message-success">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $message; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number"><?php echo $total_data; ?></div>
                            <div style="font-size: 16px; color: #666; font-weight: 600;">Total Mahasiswa</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="stat-number">db_akademik</div>
                            <div style="font-size: 16px; color: #666; font-weight: 600;">Database</div>
                        </div>
                    </div>

                    <div class="student-list">
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
                                            <span style="font-weight: 600;">Program Studi:</span>
                                            <span><?php echo htmlspecialchars($row['program_studi']); ?></span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span style="font-weight: 600;">Ditambahkan:</span>
                                            <span><?php echo date('d M Y H:i', strtotime($row['tanggal_input'])); ?></span>
                                        </div>
                                        
                                        <div class="detail-item alamat-full">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span style="font-weight: 600;">Alamat:</span>
                                            <span><?php echo htmlspecialchars($row['alamat']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <a href="?action=data&hapus=<?php echo $row['id']; ?>" 
                                       class="delete-btn"
                                       onclick="return confirm('Hapus data mahasiswa <?php echo addslashes($row['nama']); ?>?')">
                                        <i class="fas fa-trash-alt"></i> Hapus Data
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
                                <i class="fas fa-database" style="font-size: 60px; margin-bottom: 20px; color: #f9a8d4; animation: float 3s ease-in-out infinite;"></i>
                                <h3 style="color: #ec4899; margin-bottom: 15px; font-size: 24px;">Belum ada data mahasiswa</h3>
                                <p style="font-size: 16px;">Klik tombol di bawah untuk menambahkan data pertama Anda</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <a href="?action=input" class="back-btn">
                        <i class="fas fa-user-plus"></i> TAMBAH DATA BARU
                    </a>
                    
                    <a href="?action=home" class="back-btn" style="margin-top: 15px;">
                        <i class="fas fa-home"></i> KEMBALI KE HALAMAN UTAMA
                    </a>
                    
                    <div class="footer">
                        <div class="footer-title">
                            <i class="fas fa-heart"></i> UNIVERSITAS ICHSAN SIDENRENG RAPPANG <i class="fas fa-heart"></i>
                        </div>
                        <p style="margin-top: 10px;">Sistem Manajemen Data Mahasiswa © 2026</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Validasi form
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const nim = document.getElementById('nim');
                    if (nim) {
                        const nimValue = nim.value.trim();
                        const nimPattern = /^KS\d{7}$/;
                        if (!nimPattern.test(nimValue)) {
                            e.preventDefault();
                            alert('Format NIM tidak valid! Gunakan format: KS1234567 (KS diikuti 7 digit angka)');
                            nim.focus();
                            return false;
                        }
                    }
                    return true;
                });
            }
            
            // Auto hide message after 5 seconds
            const message = document.querySelector('.message');
            if (message) {
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        message.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>
<?php
// Tutup koneksi
if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>
