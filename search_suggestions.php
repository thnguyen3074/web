<?php
/**
 * AJAX Search Suggestions - Medicare
 * Trả về gợi ý tìm kiếm dạng JSON
 */

header('Content-Type: application/json');
require_once 'config.php';

// Lấy từ khóa từ GET
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if (empty($keyword) || strlen($keyword) < 2) {
    echo json_encode([]);
    exit();
}

// Escape từ khóa để bảo mật
$keyword = mysqli_real_escape_string($conn, $keyword);
$search_term = '%' . $keyword . '%';

$suggestions = [];

// Tìm kiếm trong bảng facilities (giới hạn 5 kết quả)
$sql_facilities = "SELECT facility_id, name, type FROM facilities WHERE name LIKE '$search_term' ORDER BY type, name LIMIT 5";
$result_facilities = mysqli_query($conn, $sql_facilities);
if ($result_facilities) {
    while ($row = mysqli_fetch_assoc($result_facilities)) {
        $type_text = ($row['type'] == 'hospital') ? 'Bệnh viện' : 'Phòng khám';
        $suggestions[] = [
            'id' => $row['facility_id'],
            'name' => $row['name'],
            'type' => 'facility',
            'type_text' => $type_text,
            'url' => 'FacilityDetail.php?id=' . $row['facility_id']
        ];
    }
}

// Tìm kiếm trong bảng specialties (giới hạn 5 kết quả)
$sql_specialties = "SELECT specialty_id, specialty_name FROM specialties WHERE specialty_name LIKE '$search_term' ORDER BY specialty_name LIMIT 5";
$result_specialties = mysqli_query($conn, $sql_specialties);
if ($result_specialties) {
    while ($row = mysqli_fetch_assoc($result_specialties)) {
        $suggestions[] = [
            'id' => $row['specialty_id'],
            'name' => $row['specialty_name'],
            'type' => 'specialty',
            'type_text' => 'Chuyên khoa',
            'url' => 'SpecialtyResult.php?id=' . $row['specialty_id']
        ];
    }
}

// Trả về JSON
echo json_encode($suggestions);
?>

