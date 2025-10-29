<?php
include_once __DIR__ . '/../Controllers/cMonHoc.php';

$p = new cMonHoc();

// Xử lý thêm môn học
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $tenmh = $_POST['tenmh'];
    $sotiet = $_POST['sotiet'];
    $khoilop = isset($_POST['khoilop']) ? implode(",", $_POST['khoilop']) : '';
    $trangthai = $_POST['trangthai'];

    if ($p->checkDuplicateSubject($tenmh)) {
    $_SESSION['message'] = "Tên môn học đã tồn tại, vui lòng nhập tên khác!";
    header("Location: index.php?act=quanlydanhmucmonhoc");
    exit();
    }

    if ($p->insertSubject($tenmh, $sotiet, $khoilop, $trangthai)) {
        $_SESSION['message'] = "Thêm môn học thành công!";
        header("Location: index.php?act=quanlydanhmucmonhoc");
        exit();
    } else {
        $_SESSION['message'] = "Thêm thất bại!";
        header("Location: index.php?act=quanlydanhmucmonhoc");
        exit();
    }
}

// Xử lý cập nhật môn học
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $mamh = $_POST['mamh'];
    $tenmh = $_POST['tenmh'];
    $sotiet = $_POST['sotiet'];
    $khoilop = isset($_POST['khoilop']) ? implode(",", $_POST['khoilop']) : '';
    $trangthai = $_POST['trangthai'];

    if ($p->updateSubject($mamh, $tenmh, $sotiet, $khoilop, $trangthai)) {
        $_SESSION['message'] = "Cập nhật thành công!";
        header("Location: index.php?act=quanlydanhmucmonhoc");
        exit();
    } else {
        $_SESSION['message'] = "Cập nhật thất bại!";
        header("Location: index.php?act=quanlydanhmucmonhoc");
        exit();
    }
}

// Xử lý xóa môn học
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $mamh = $_GET['id'];
    if ($p->deleteSubject($mamh)) {
        $_SESSION['message'] = "Xóa môn học thành công!";
    } else {
        $_SESSION['message'] = "Xóa thất bại!";
    }
    header("Location: index.php?act=quanlydanhmucmonhoc");
    exit();
}

// Xử lý tìm kiếm
if (isset($_GET['action']) && $_GET['action'] == 'search') {
    $keyword = $_GET['keyword'] ?? '';
    $tbl = $p->getAllSubjectsBySearch($keyword);
} else {
    $tbl = $p->getAllSubjects();
}

// Chuyển dữ liệu thành mảng để hiển thị dễ hơn
$subjects = [];
if ($tbl && $tbl->num_rows > 0) {
    while ($row = $tbl->fetch_assoc()) {
        $subjects[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý danh mục môn học</title>
    <link rel="stylesheet" href="./public/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css" />
    <script src="./public/vendor/bootstrap-5.3.3-dist/js/jquery-3.7.1.min.js"></script>
    <script src="./public/vendor/bootstrap-5.3.3-dist/js/bootstrap.min.js"></script>
    
</head>

<body class="bg-light">
<main class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary mb-0">Quản lý danh mục môn học (CRUD)</h4>
    </div>

    <!-- TÌM KIẾM + NÚT THÊM -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <form class="d-flex flex-grow-1" style="max-width: 600px;" method="GET" action="index.php">
                    <input type="hidden" name="act" value="quanlydanhmucmonhoc">
                    <div class="input-group">
                        <input type="text" name="keyword" id="searchInput" class="form-control"
                            placeholder="Tìm kiếm theo mã hoặc tên môn học..." value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>">
                        <button type="submit" name="action" value="search" class="btn btn-primary">Tìm kiếm</button>
                        <button type="reset" class="btn btn-outline-secondary" onclick="window.location.href='index.php?act=quanlydanhmucmonhoc'">Làm mới</button>
                    </div>
                </form>

                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    + Thêm môn học
                </button>
            </div>
        </div>
    </div>

    <!-- DANH SÁCH -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Mã môn học</th>
                        <th>Tên môn học</th>
                        <th>Số tiết</th>
                        <th>Khối lớp</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($subjects)) : ?>
                        <?php foreach ($subjects as $r): ?>
                            <tr>
                                <td><?= $r['mamh'] ?></td>
                                <td><?= $r['tenmh'] ?></td>
                                <td><?= $r['sotiet'] ?></td>
                                <td><?= $r['khoilop'] ?></td>
                                <td>
                                    <span class="badge <?= $r['trangthai'] == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $r['trangthai'] == 'active' ? 'Hoạt động' : 'Ngưng hoạt động' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEdit"
                                        onclick="setEditData('<?= $r['mamh'] ?>','<?= addslashes($r['tenmh']) ?>','<?= $r['sotiet'] ?>','<?= $r['khoilop'] ?>','<?= $r['trangthai'] ?>')">
                                        Sửa
                                    </button>
                                    <a href="index.php?act=quanlydanhmucmonhoc&action=delete&id=<?= $r['mamh'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc muốn xóa môn học này?')">
                                       Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Không có môn học nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- MODAL THÊM -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalAddLabel">Thêm môn học</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Tên môn học</label>
                <input type="text" name="tenmh" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Số tiết</label>
                <input type="number" name="sotiet" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Khối lớp</label>
                <div class="border rounded p-2">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="khoilop[]" value="10"> Khối 10</div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="khoilop[]" value="11"> Khối 11</div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="khoilop[]" value="12"> Khối 12</div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="trangthai" class="form-select">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngưng hoạt động</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL SỬA -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalEditLabel">Cập nhật môn học</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="mamh" id="edit_mamh">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Tên môn học</label>
                <input type="text" name="tenmh" id="edit_tenmh" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Số tiết</label>
                <input type="number" name="sotiet" id="edit_sotiet" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Khối lớp</label>
                <div class="border rounded p-2">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="khoilop[]" value="10" id="edit_khoi10"> Khối 10</div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="khoilop[]" value="11" id="edit_khoi11"> Khối 11</div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="khoilop[]" value="12" id="edit_khoi12"> Khối 12</div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="trangthai" id="edit_trangthai" class="form-select">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngưng hoạt động</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-warning text-white">Cập nhật</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function setEditData(mamh, tenmh, sotiet, khoilop, trangthai) {
    document.getElementById('edit_mamh').value = mamh;
    document.getElementById('edit_tenmh').value = tenmh;
    document.getElementById('edit_sotiet').value = sotiet;
    document.getElementById('edit_trangthai').value = trangthai;

    document.getElementById('edit_khoi10').checked = false;
    document.getElementById('edit_khoi11').checked = false;
    document.getElementById('edit_khoi12').checked = false;

    const arr = khoilop.split(',');
    arr.forEach(k => {
        if (k.trim() == '10') document.getElementById('edit_khoi10').checked = true;
        if (k.trim() == '11') document.getElementById('edit_khoi11').checked = true;
        if (k.trim() == '12') document.getElementById('edit_khoi12').checked = true;
    });
}
</script>
</body>
</html>
