<?php
// api.php
require_once 'config.php'; // ดึง $pdo (การเชื่อมต่อ SQLite)

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'fetch') {
    // READ
    try {
        $stmt = $pdo->query("SELECT * FROM members ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
} elseif ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE / UPDATE
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'] ?? null;
    $student_id = $data['student_id'] ?? '';
    $fullname = $data['fullname'] ?? '';
    $email = $data['email'] ?? '';
    $major = $data['major'] ?? '';
    $academic_year = $data['academic_year'] ?? '';

    // Basic Server-side Validation (เพิ่มเติมได้ตามต้องการ)
    if (empty($student_id) || empty($fullname) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit;
    }

    try {
        if (empty($id)) {
            // INSERT (สร้างใหม่)
            $sql = "INSERT INTO members (student_id, fullname, email, major, academic_year) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$student_id, $fullname, $email, $major, $academic_year]);
            echo json_encode(['status' => 'success', 'message' => 'เพิ่มข้อมูลแล้ว']);
        } else {
            // UPDATE (แก้ไข)
            $sql = "UPDATE members SET student_id=?, fullname=?, email=?, major=?, academic_year=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$student_id, $fullname, $email, $major, $academic_year, $id]);
            echo json_encode(['status' => 'success', 'message' => 'แก้ไขข้อมูลแล้ว']);
        }
    } catch (PDOException $e) {
        // ดักจับ Error สำหรับ Unique Constraint (รหัสซ้ำ/อีเมลซ้ำ)
        $msg = $e->getMessage();
        if (strpos($msg, 'UNIQUE constraint failed')) {
            $msg = 'รหัสนักศึกษาหรืออีเมลซ้ำในระบบ';
        }
        echo json_encode(['status' => 'error', 'message' => $msg]);
    }
    exit;
} elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // DELETE
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบ ID ที่ต้องการลบ']);
        exit;
    }

    try {
        $sql = "DELETE FROM members WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลแล้ว']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// ถ้าไม่มี action ที่ถูกต้อง
http_response_code(400);
echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
