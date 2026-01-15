<?php
// api.php - สำหรับรับข้อมูลจาก PerfectPanel
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// รับข้อมูลจาก POST request
$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input) {
    // อ่านข้อมูลเดิม
    $filename = 'data.json';
    $currentData = [];
    
    if (file_exists($filename)) {
        $currentData = json_decode(file_get_contents($filename), true);
        if (!is_array($currentData)) {
            $currentData = [];
        }
    }
    
    // เพิ่มข้อมูลใหม่
    $newEntry = [
        'user_id' => $input['user_id'] ?? 'unknown_' . time(),
        'username' => $input['username'] ?? 'Anonymous',
        'achievement_id' => $input['achievement_id'] ?? 'unknown',
        'achievement_name' => $input['achievement_name'] ?? 'Unknown Achievement',
        'bonus' => $input['bonus'] ?? 0,
        'icon' => $input['icon'] ?? '🏆',
        'unlocked_at' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'timestamp' => time()
    ];
    
    // ใส่ข้อมูลใหม่ไปข้างหน้า (ล่าสุดอยู่ด้านบน)
    array_unshift($currentData, $newEntry);
    
    // เก็บเฉพาะ 500 รายการล่าสุด
    $currentData = array_slice($currentData, 0, 500);
    
    // บันทึกลงไฟล์
    file_put_contents($filename, json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // ตอบกลับ
    echo json_encode([
        'success' => true,
        'message' => 'Achievement saved successfully!',
        'data' => $newEntry,
        'total_records' => count($currentData)
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // แสดงข้อมูลทั้งหมด (สำหรับทดสอบ)
    $filename = 'data.json';
    
    if (file_exists($filename)) {
        $data = json_decode(file_get_contents($filename), true);
        echo json_encode([
            'success' => true,
            'count' => count($data),
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No data yet',
            'data' => []
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method or empty data'
    ]);
}
?>
