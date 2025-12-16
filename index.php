<?php
session_start();
// ตรวจสอบการ Login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-bg: #f8f9fa;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--secondary-bg);
            color: #333;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .badge-major {
            border-radius: 4px;
            font-weight: 500;
        }

        /* Toast Positioning */
        #toastContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1080;
            /* สูงกว่า Modal (1050) */
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-code me-2"></i>DevClub Management</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-sm-block"><i class="fas fa-user-circle me-1"></i> Admin</span>
                <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h3 class="fw-bold text-primary"><i class="fas fa-users me-2"></i>รายชื่อสมาชิกชมรม</h3>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-success shadow-sm" onclick="openModal()">
                    <i class="fas fa-plus me-1"></i> เพิ่มสมาชิกใหม่
                </button>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchInput" placeholder="ค้นหา (ชื่อ, อีเมล)..." onkeyup="renderTable()">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterMajor" onchange="renderTable()">
                        <option value="">ทั้งหมด (ทุกสาขา)</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>รหัสนักศึกษา</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>อีเมล</th>
                            <th>สาขา</th>
                            <th>ปี (พ.ศ.)</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="membersTableBody">
                        <tr>
                            <td colspan="7" class="text-center text-muted">กำลังโหลดข้อมูล...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="memberModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">จัดการสมาชิก</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="memberForm">
                        <input type="hidden" id="memberId">

                        <div class="mb-3">
                            <label for="studentId" class="form-label">รหัสนักศึกษา (10 หลัก)</label>
                            <input type="number" id="studentId" class="form-control" required>
                            <div class="form-text text-danger d-none" id="studentIdError">รหัสนักศึกษาต้องเป็นตัวเลข 10 หลัก</div>
                        </div>

                        <div class="mb-3">
                            <label for="fullName" class="form-label">ชื่อ-นามสกุล</label>
                            <input type="text" id="fullName" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">อีเมล</label>
                            <input type="email" id="email" class="form-control" required>
                            <div class="form-text text-danger d-none" id="emailError">รูปแบบอีเมลไม่ถูกต้อง</div>
                            <small class="text-muted">เช่น 6642590052@webmail.npru.ac.th</small>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="major" class="form-label">สาขา</label>
                                <select id="major" class="form-select" required>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="year" class="form-label">ปีการศึกษา (พ.ศ.)</label>
                                <input type="number" id="year" class="form-control" required min="2500" max="3000">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="saveMember()">
                        <i class="fas fa-save me-1"></i> บันทึก
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let members = [];
        let majors = new Set(); // สำหรับเก็บรายการสาขาที่ไม่ซ้ำกัน
        let memberModalInstance = new bootstrap.Modal(document.getElementById('memberModal'));
        const MAJOR_SELECT = document.getElementById('major');

        // Load Data on Start
        document.addEventListener('DOMContentLoaded', loadMembers);

        // --- Utility Functions for Toasts ---
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            let icon = '';
            let bgClass = '';

            if (type === 'success') {
                icon = '<i class="fas fa-check-circle me-2"></i>';
                bgClass = 'bg-success text-white';
            } else {
                icon = '<i class="fas fa-exclamation-triangle me-2"></i>';
                bgClass = 'bg-danger text-white';
            }

            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${icon} ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', toastHtml);

            const toastEl = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastEl, {
                delay: 5000
            });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        }


        // --- CRUD & Render Functions ---

        // Fetch from API
        function loadMembers() {
            fetch('api.php?action=fetch')
                .then(res => res.json())
                .then(data => {
                    members = data.map(m => ({
                        id: m.id,
                        student_id: m.student_id, // ใช้ student_id
                        name: m.fullname,
                        email: m.email,
                        major: m.major,
                        year: m.academic_year
                    }));

                    // 1. สร้างรายการสาขาที่ไม่ซ้ำกัน (สำหรับ Filter และ Modal Select)
                    majors.clear();
                    members.forEach(m => majors.add(m.major));

                    // 2. Render Select Options
                    renderMajorOptions();

                    // 3. Render Table
                    renderTable();
                })
                .catch(error => {
                    showToast('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message, 'error');
                });
        }

        // สร้าง Options สำหรับ Dropdown (Filter และ Modal)
        function renderMajorOptions() {
            const filterSelect = document.getElementById('filterMajor');
            const currentFilterValue = filterSelect.value;
            const currentModalMajorValue = MAJOR_SELECT.value;

            // 1. สำหรับ Filter
            filterSelect.innerHTML = '<option value="">ทั้งหมด (ทุกสาขา)</option>';
            majors.forEach(major => {
                const option = document.createElement('option');
                option.value = major;
                option.textContent = major;
                if (major === currentFilterValue) {
                    option.selected = true;
                }
                filterSelect.appendChild(option);
            });

            // 2. สำหรับ Modal Select (ให้มีเฉพาะสาขาที่มีอยู่จริง)
            MAJOR_SELECT.innerHTML = '';
            majors.forEach(major => {
                const option = document.createElement('option');
                option.value = major;
                option.textContent = major;
                if (major === currentModalMajorValue) {
                    option.selected = true;
                }
                MAJOR_SELECT.appendChild(option);
            });
        }


        function renderTable() {
            const tbody = document.getElementById('membersTableBody');
            const search = document.getElementById('searchInput').value.toLowerCase();
            const filter = document.getElementById('filterMajor').value;
            tbody.innerHTML = '';

            if (members.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">ไม่พบข้อมูลสมาชิก</td></tr>';
                return;
            }

            const filtered = members.filter(m => {
                const searchMatch = (
                    m.name.toLowerCase().includes(search) ||
                    m.email.toLowerCase().includes(search) ||
                    String(m.student_id).includes(search) // ค้นหาจากรหัสนักศึกษาได้ด้วย
                );
                const filterMatch = (filter === '' || m.major === filter);
                return searchMatch && filterMatch;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">ไม่พบสมาชิกที่ตรงกับเงื่อนไขการค้นหา</td></tr>';
                return;
            }

            filtered.forEach(m => {
                // ใช้สี badge ที่แตกต่างกันตามสาขาเพื่อความสวยงาม
                let badgeClass = 'bg-secondary';
                if (m.major.includes('คอมพิวเตอร์')) badgeClass = 'bg-primary';
                else if (m.major.includes('ซอฟต์แวร์')) badgeClass = 'bg-success';
                else if (m.major.includes('สารสนเทศ')) badgeClass = 'bg-info';

                tbody.innerHTML += `
                    <tr>
                        <td>${m.id}</td>
                        <td>${m.student_id}</td>
                        <td>${m.name}</td>
                        <td>${m.email}</td>
                        <td><span class="badge ${badgeClass} badge-major">${m.major}</span></td>
                        <td>${m.year}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1" title="แก้ไข" onclick="editMember(${m.id})"><i class="fas fa-pen"></i></button>
                            <button class="btn btn-danger btn-sm" title="ลบ" onclick="deleteMember(${m.id})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }

        function openModal() {
            document.getElementById('memberForm').reset();
            document.getElementById('memberId').value = '';
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus me-1"></i> เพิ่มสมาชิก';

            // Clear errors
            document.getElementById('studentIdError').classList.add('d-none');
            document.getElementById('emailError').classList.add('d-none');

            // ให้ Major Select มีตัวเลือกทั้งหมด (หากมีสาขาใหม่ที่ไม่เคยถูกเพิ่ม)
            renderMajorOptions();

            memberModalInstance.show();
        }

        function editMember(id) {
            const m = members.find(x => x.id == id);
            if (!m) return;

            document.getElementById('memberId').value = m.id;
            document.getElementById('studentId').value = m.student_id; // ตั้งค่า student_id
            document.getElementById('fullName').value = m.name;
            document.getElementById('email').value = m.email;
            document.getElementById('major').value = m.major;
            document.getElementById('year').value = m.year;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-pen me-1"></i> แก้ไขสมาชิก';

            // Clear errors
            document.getElementById('studentIdError').classList.add('d-none');
            document.getElementById('emailError').classList.add('d-none');

            memberModalInstance.show();
        }

        function validateForm(data) {
            let isValid = true;

            // 1. ตรวจสอบ รหัสนักศึกษา (10 หลัก)
            const studentIdInput = document.getElementById('studentId');
            const studentIdError = document.getElementById('studentIdError');
            if (!/^\d{10}$/.test(data.student_id)) {
                studentIdError.classList.remove('d-none');
                studentIdInput.classList.add('is-invalid');
                isValid = false;
            } else {
                studentIdError.classList.add('d-none');
                studentIdInput.classList.remove('is-invalid');
            }

            // 2. ตรวจสอบ อีเมล
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
                emailError.classList.remove('d-none');
                emailInput.classList.add('is-invalid');
                isValid = false;
            } else {
                emailError.classList.add('d-none');
                emailInput.classList.remove('is-invalid');
            }

            // ตรวจสอบปี (4 หลัก)
            const yearInput = document.getElementById('year');
            if (String(data.year).length !== 4) {
                yearInput.classList.add('is-invalid');
                isValid = false;
            } else {
                yearInput.classList.remove('is-invalid');
            }

            return isValid;
        }


        function saveMember() {
            const id = document.getElementById('memberId').value;
            const data = {
                id: id,
                student_id: document.getElementById('studentId').value, // ดึงค่า student_id
                fullname: document.getElementById('fullName').value, // ใช้ fullname ให้ตรงกับ DB
                email: document.getElementById('email').value,
                major: document.getElementById('major').value,
                academic_year: document.getElementById('year').value // ใช้ academic_year ให้ตรงกับ DB
            };

            // Client-side Validation
            if (!validateForm(data)) {
                showToast('กรุณากรอกข้อมูลให้ถูกต้องตามที่กำหนด.', 'error');
                return;
            }


            fetch('api.php?action=save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        loadMembers(); // Reload data
                        memberModalInstance.hide();
                        showToast('บันทึกข้อมูลสมาชิกสำเร็จ', 'success');
                    } else {
                        // ข้อผิดพลาดจาก Server (เช่น รหัสนักศึกษา/อีเมลซ้ำ)
                        showToast('บันทึกไม่สำเร็จ: ' + res.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('เกิดข้อผิดพลาดในการส่งข้อมูล: ' + error.message, 'error');
                });
        }

        function deleteMember(id) {
            if (confirm('ยืนยันการลบสมาชิก ID: ' + id + ' หรือไม่?')) {
                fetch('api.php?action=delete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: id
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            loadMembers();
                            showToast('ลบข้อมูลสมาชิก ID: ' + id + ' สำเร็จ', 'success');
                        } else {
                            showToast('ลบข้อมูลไม่สำเร็จ: ' + res.message, 'error');
                        }
                    })
                    .catch(error => {
                        showToast('เกิดข้อผิดพลาดในการลบข้อมูล: ' + error.message, 'error');
                    });
            }
        }
    </script>
</body>

</html>