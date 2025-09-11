<?php
$conn = new mysqli('localhost', 'root', '', 'vigazafarm_clean');
if ($conn->connect_error) { 
    echo 'Connection failed: ' . $conn->connect_error; 
} else {
    echo "=== STRUKTUR TABEL kos_penetasan ===\n";
    $result = $conn->query('DESCRIBE kos_penetasan');
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
    }
    
    echo "\n=== DATA TERBARU kos_penetasan (5 terakhir) ===\n";
    $result = $conn->query('SELECT * FROM kos_penetasan ORDER BY created_at DESC LIMIT 5');
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id_penetasan'] . ", Batch: " . $row['batch'] . ", Status: " . $row['status'] . ", Created: " . $row['created_at'] . "\n";
        }
    } else {
        echo "Tidak ada data dalam tabel kos_penetasan\n";
    }
}
?>
