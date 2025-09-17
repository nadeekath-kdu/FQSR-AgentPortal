<?php
// get_total_amount.php
// Usage: ?nic=NIC_OR_PASSPORT
header('Content-Type: application/json');
require_once '../config/dbcon.php';

if (!isset($_GET['nic']) || trim($_GET['nic']) === '') {
    echo json_encode(array('success' => false, 'message' => 'NIC/Passport number required'));
    exit;
}
$nic = trim($_GET['nic']);
$conn = $con_fqsr;

$sql = "SELECT COUNT(*) as degree_count FROM appliedDegrees WHERE nic = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $nic);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$degree_count = (int)(isset($row['degree_count']) ? $row['degree_count'] : 0);
$total_amount = $degree_count * 100;

echo json_encode(array(
    'success' => true,
    'degree_count' => $degree_count,
    'total_amount' => $total_amount
));
