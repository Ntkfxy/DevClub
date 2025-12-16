<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? '';

// 1. GET ALL MEMBERS
if ($action === 'fetch') {
    $stmt = $conn->query("SELECT * FROM members ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// รับข้อมูล JSON ที่ส่งมาจาก JS
$data = json_decode(file_get_contents("php://input"), true);

// 2. CREATE / UPDATE
if ($action === 'save') {
    $id = $data['id'] ?? null;
    $name = $data['name'];
    $email = $data['email'];
    $major = $data['major'];
    $year = $data['year'];

    // Strict Email Validation (Server-side)
    if (!preg_match('/^\d+@webmail\.npru\.ac\.th$/', $email)) {
        echo json_encode(['status' => 'error', 'message' => 'รูปแบบอีเมลไม่ถูกต้อง']);
        exit;
    }

    if ($id) {
        // Update
        $sql = "UPDATE members SET fullname=?, email=?, major=?, academic_year=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$name, $email, $major, $year, $id]);
    } else {
        // Insert
        $sql = "INSERT INTO members (fullname, email, major, academic_year) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$name, $email, $major, $year]);
    }

    echo json_encode(['status' => $result ? 'success' : 'error']);
    exit;
}

// 3. DELETE
if ($action === 'delete') {
    $id = $data['id'];
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $result = $stmt->execute([$id]);
    echo json_encode(['status' => $result ? 'success' : 'error']);
    exit;
}
