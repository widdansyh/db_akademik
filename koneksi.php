<?php
// Koneksi ke database db_akademik
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_akademik';

// Buat koneksi
$koneksi = mysqli_connect($host, $username, $password);

// Cek koneksi ke MySQL
if (!$koneksi) {
    die("Koneksi ke MySQL gagal");
}

// Coba pilih database yang sudah ada
if (!mysqli_select_db($koneksi, $database)) {
    // Jika database tidak ada, buat baru
    $create_db = "CREATE DATABASE IF NOT EXISTS $database 
                  CHARACTER SET utf8mb4 
                  COLLATE utf8mb4_general_ci";
    
    if (mysqli_query($koneksi, $create_db)) {
        // Pilih database setelah dibuat
        mysqli_select_db($koneksi, $database);
    } else {
        die("Gagal membuat database");
    }
}

// Set charset
mysqli_set_charset($koneksi, "utf8mb4");

// Buat tabel mahasiswa jika belum ada
$create_table = "CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(50) NOT NULL UNIQUE,
    nama VARCHAR(255) NOT NULL,
    program_studi VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    tanggal_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!mysqli_query($koneksi, $create_table)) {
    die("Gagal membuat tabel");
}
?>
