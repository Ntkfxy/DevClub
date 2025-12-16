<?php
session_start();
if (!isset($_SESSION['is_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการสมาชิก DevClub</title>
    <!-- Bootstrap & Styles (เหมือนเดิม) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* (Copy CSS เดิมมาใส่ที่นี่ หรือแยกเป็น style.css) */
        :root {
            --primary-color: #0d6efd;
            --secondary-bg: #f8f9fa;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--secondary-bg);
            color: #333;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background-color: #f1f4f9;
            font-weight: 600;
        }

        .badge-major {
            border-radius: 20px;
            font-weight: 400;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-code me-2"></i>DevClub Management</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-sm-block"><i class="fas fa-user-circle me-1"></i> Admin</span>
                <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h3 class="fw-bold text-secondary"><i class="fas fa-users me-2"></i>รายชื่อสมาชิกชมรม</h3>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-success shadow-sm" onclick="openModal()">
                    <i class="fas fa-plus me-1"></i> เพิ่มสมาชิกใหม่
                </button>
            </div>
        </div>

        <div class="card card-custom p-4">
            <!-- Search & Filter -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchInput" placeholder="ค้นหา..." onkeyup="renderTable()">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterMajor" onchange="renderTable()">
                        <option value="">ทั้งหมด (ทุกสาขา)</option>
                        <option value="วิทยาการคอมพิวเตอร์">วิทยาการคอมพิวเตอร์</option>
                        <option value="เทคโนโลยีสารสนเทศ">เทคโนโลยีสารสนเทศ</option>
                        <option value="วิศวกรรมซอฟต์แวร์">วิศวกรรมซอฟต์แวร์</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>อีเมล</th>
                            <th>สาขา</th>
                            <th>ปี (พ.ศ.)</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="membersTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals (Add/Edit & Delete) - Copy โครงสร้าง HTML Modals จากไฟล์เดิมมาใส่ได้เลย -->
    <!-- ... (ใส่ Modal Code ที่นี่) ... -->

    <!-- Add/Edit Modal (ย่อ) -->
    <div class="modal fade" id="memberModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">จัดการสมาชิก</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="memberForm">
                        <input type="hidden" id="memberId">
                        <div class="mb-3"><label>ชื่อ-นามสกุล</label><input type="text" id="fullName" class="form-control" required></div>
                        <div class="mb-3"><label>อีเมล</label><input type="email" id="email" class="form-control" required><small class="text-muted">ต้องเป็น รหัสนักศึกษา@webmail.npru.ac.th</small></div>
                        <div class="row">
                            <div class="col-6"><label>สาขา</label>
                                <select id="major" class="form-select" required>
                                    <option value="วิทยาการคอมพิวเตอร์">วิทยาการคอมพิวเตอร์</option>
                                    <option value="เทคโนโลยีสารสนเทศ">เทคโนโลยีสารสนเทศ</option>
                                    <option value="วิศวกรรมซอฟต์แวร์">วิศวกรรมซอฟต์แวร์</option>
                                </select>
                            </div>
                            <div class="col-6"><label>ปี (พ.ศ.)</label><input type="number" id="year" class="form-control" required></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-primary" onclick="saveMember()">บันทึก</button></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let members = [];
        let memberModalInstance = new bootstrap.Modal(document.getElementById('memberModal'));

        // Load Data on Start
        document.addEventListener('DOMContentLoaded', loadMembers);

        // Fetch from API
        function loadMembers() {
            fetch('api.php?action=fetch')
                .then(res => res.json())
                .then(data => {
                    members = data; // map field names if DB columns differ (e.g. fullname -> name)
                    // adjust mapping to match JS logic
                    members = data.map(m => ({
                        id: m.id,
                        name: m.fullname,
                        email: m.email,
                        major: m.major,
                        year: m.academic_year
                    }));
                    renderTable();
                });
        }

        function renderTable() {
            const tbody = document.getElementById('membersTableBody');
            const search = document.getElementById('searchInput').value.toLowerCase();
            const filter = document.getElementById('filterMajor').value;
            tbody.innerHTML = '';

            const filtered = members.filter(m => {
                return (m.name.toLowerCase().includes(search) || m.email.toLowerCase().includes(search)) &&
                    (filter === '' || m.major === filter);
            });

            filtered.forEach(m => {
                tbody.innerHTML += `
                    <tr>
                        <td>${m.id}</td>
                        <td>${m.name}</td>
                        <td>${m.email}</td>
                        <td><span class="badge bg-light text-dark border badge-major">${m.major}</span></td>
                        <td>${m.year}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1" onclick="editMember(${m.id})"><i class="fas fa-pen"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteMember(${m.id})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }

        function openModal() {
            document.getElementById('memberForm').reset();
            document.getElementById('memberId').value = '';
            document.getElementById('modalTitle').innerText = 'เพิ่มสมาชิก';
            memberModalInstance.show();
        }

        function editMember(id) {
            const m = members.find(x => x.id == id);
            document.getElementById('memberId').value = m.id;
            document.getElementById('fullName').value = m.name;
            document.getElementById('email').value = m.email;
            document.getElementById('major').value = m.major;
            document.getElementById('year').value = m.year;
            document.getElementById('modalTitle').innerText = 'แก้ไขสมาชิก';
            memberModalInstance.show();
        }

        function saveMember() {
            const id = document.getElementById('memberId').value;
            const data = {
                id: id,
                name: document.getElementById('fullName').value,
                email: document.getElementById('email').value,
                major: document.getElementById('major').value,
                year: document.getElementById('year').value
            };

            fetch('api.php?action=save', {
                    method: 'POST',
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        loadMembers(); // Reload data
                        memberModalInstance.hide();
                        alert('บันทึกสำเร็จ');
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + res.message);
                    }
                });
        }

        function deleteMember(id) {
            if (confirm('ยืนยันการลบ?')) {
                fetch('api.php?action=delete', {
                    method: 'POST',
                    body: JSON.stringify({
                        id: id
                    })
                }).then(() => {
                    loadMembers();
                    alert('ลบข้อมูลแล้ว');
                });
            }
        }
    </script>
</body>

</html>