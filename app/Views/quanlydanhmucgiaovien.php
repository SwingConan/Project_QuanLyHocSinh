<?php
// ======= CONTROLLER IMPORTS =======

include_once __DIR__ . '/../Controllers/cGiaoVien.php';
include_once __DIR__ . '/../Controllers/cMonHoc.php';

$pGV  = new cGiaoVien();
$pMH  = new cMonHoc();

// ======= HANDLE POST (ADD / EDIT / DELETE) =======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  // Fields chung
  $hoten     = $_POST['hoten']      ?? '';
  $ngaysinh  = $_POST['ngaysinh']   ?? '';
  $gioitinh  = $_POST['gioitinh']   ?? 'Nam';
  $cmnd      = $_POST['cmndcccd']   ?? '';
  $diachi    = $_POST['diachi']     ?? '';
  $dienthoai = $_POST['dienthoai']  ?? '';
  $email     = $_POST['email']      ?? '';
  $trinhdo   = $_POST['trinhdo']    ?? '';
  $trangthai = $_POST['trangthai']  ?? 'hoatdong';
  // MẢNG ID MÔN HỌC (từ checkbox)
  $monhoc_ids = $_POST['monhoc_ids'] ?? [];
  $_SESSION['message'] = 'DEBUG View monhoc_ids=' . htmlspecialchars(json_encode($monhoc_ids));


  if ($action === 'add') {
    // insertTeacher(..., $monhoc_ids) – tham số $mon (text) bỏ
    $kq = $pGV->insertTeacher($hoten, $ngaysinh, $gioitinh, $cmnd, $diachi, $dienthoai, $email, $trinhdo, $trangthai, $monhoc_ids);
    $_SESSION['message'] = ($kq === true) ? "Thêm giáo viên thành công" : ("Không thêm được: " . ($kq ?: "Lỗi không xác định"));
    header("Location: index.php?act=quanlydanhmucgiaovien");
    exit;
  }

  if ($action === 'edit') {
    $magv = $_POST['magv'] ?? '';
    $kq = $pGV->updateTeacher($magv, $hoten, $ngaysinh, $gioitinh, $cmnd, $diachi, $dienthoai, $email, $trinhdo, $trangthai, $monhoc_ids);
    $_SESSION['message'] = ($kq === true) ? "Cập nhật giáo viên thành công" : ("Không cập nhật được: " . ($kq ?: "Lỗi không xác định"));
    header("Location: index.php?act=quanlydanhmucgiaovien");
    exit;
  }

  if ($action === 'delete') {
    $magv = $_POST['magv_delete'] ?? '';
    $kq = $pGV->deleteTeacher($magv);
    $_SESSION['message'] = $kq ? "Đã xóa giáo viên #$magv" : "Xóa thất bại";
    header("Location: index.php?act=quanlydanhmucgiaovien");
    exit;
  }
}

// ======= HANDLE GET (SEARCH) =======
$action  = $_GET['action']  ?? '';
$keyword = $_GET['keyword'] ?? '';

// Với bản đơn giản này ta liệt kê toàn bộ + JOIN GROUP_CONCAT.
// (Bạn có thể nâng cấp search sau.)
$danhsach = $pGV->getAllTeachers();

// Lấy danh sách môn cho modal THÊM
$dsMonHoc_Add = $pMH->getAllSubjects();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Quản lý danh mục giáo viên</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-4">

    <h3 class="mb-3">Quản lý danh mục giáo viên</h3>

    <?php if (!empty($_SESSION['message'])): ?>
      <div class="alert alert-info"><?php echo $_SESSION['message'];
                                    unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <div class="d-flex gap-2 mb-3">
      <form class="d-flex gap-2" method="get" action="index.php">
        <input type="hidden" name="act" value="quanlydanhmucgiaovien">
        <input type="hidden" name="action" value="search">
        <input class="form-control" name="keyword" placeholder="Tìm tên/email/sđt/CMND..." value="<?php echo htmlspecialchars($keyword); ?>">
        <button class="btn btn-primary">Tìm kiếm</button>
      </form>
      <button class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#modalAdd">+ Thêm giáo viên</button>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-secondary">
          <tr>
            <th>Mã GV</th>
            <th>Họ tên</th>
            <th>Giới tính</th>
            <th>Ngày sinh</th>
            <th>Điện thoại</th>
            <th>Email</th>
            <th>CMND/CCCD</th>
            <th>Môn giảng dạy</th>
            <th>Trình độ</th>
            <th>Trạng thái</th>
            <th style="width:140px"></th>
          </tr>
        </thead>
        <tbody>
          <?php if ($danhsach && mysqli_num_rows($danhsach) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($danhsach)): ?>
              <?php
              // Lấy mảng ID môn của giáo viên này để tick sẵn khi Edit
              $ids = $pGV->getSubjectIdsByTeacher($row['magv']);
              $ids_json = htmlspecialchars(json_encode($ids), ENT_QUOTES, 'UTF-8');
              ?>
              <tr>
                <td><?php echo $row['magv']; ?></td>
                <td><?php echo htmlspecialchars($row['hoten']); ?></td>
                <td><?php echo htmlspecialchars($row['gioitinh']); ?></td>
                <td><?php echo htmlspecialchars($row['ngaysinh']); ?></td>
                <td><?php echo htmlspecialchars($row['dienthoai']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['cmndcccd']); ?></td>
                <td><?php echo htmlspecialchars($row['dsmon'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['trinhdo']); ?></td>
                <td>
                  <?php if ($row['trangthai'] === 'hoatdong'): ?>
                    <span class="badge text-bg-success">hoạt động</span>
                  <?php else: ?>
                    <span class="badge text-bg-secondary">không hoạt động</span>
                  <?php endif; ?>
                </td>
                <td>
                  <button class="btn btn-sm btn-warning"
                    onclick='setEditData(
                        "<?php echo $row["magv"]; ?>",
                        `<?php echo htmlspecialchars($row["hoten"]); ?>`,
                        "<?php echo $row["ngaysinh"]; ?>",
                        "<?php echo $row["gioitinh"]; ?>",
                        `<?php echo htmlspecialchars($row["cmndcccd"]); ?>`,
                        `<?php echo htmlspecialchars($row["diachi"]); ?>`,
                        `<?php echo htmlspecialchars($row["dienthoai"]); ?>`,
                        `<?php echo htmlspecialchars($row["email"]); ?>`,
                        `<?php echo htmlspecialchars($row["trinhdo"]); ?>`,
                        "<?php echo $row["trangthai"]; ?>",
                        <?php echo $ids_json; ?>
                      )'
                    data-bs-toggle="modal" data-bs-target="#modalEdit">Sửa</button>

                  <form class="d-inline" method="post" onsubmit="return confirm('Xóa giáo viên #<?php echo $row['magv']; ?>?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="magv_delete" value="<?php echo $row['magv']; ?>">
                    <button class="btn btn-sm btn-danger">Xóa</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="11" class="text-center text-muted">Không có dữ liệu</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ================= MODAL ADD ================= -->
  <div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" method="post">
        <input type="hidden" name="action" value="add">
        <div class="modal-header">
          <h5 class="modal-title">Thêm giáo viên</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">Họ tên</label>
              <input class="form-control" name="hoten" required>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label">Giới tính</label>
              <select class="form-select" name="gioitinh">
                <option>Nam</option>
                <option>Nữ</option>
                <option>Khác</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label">Ngày sinh</label>
              <input type="date" class="form-control" name="ngaysinh" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">CMND/CCCD</label>
              <input class="form-control" name="cmndcccd" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Địa chỉ</label>
              <input class="form-control" name="diachi" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Điện thoại</label>
              <input class="form-control" name="dienthoai" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Trình độ</label>
              <input class="form-control" name="trinhdo" required>
            </div>

            <!-- CHECKBOX MÔN HỌC (ADD) -->
            <div class="col-md-12 mb-2">
              <label class="form-label">Môn giảng dạy</label><br>
              <?php if ($dsMonHoc_Add && mysqli_num_rows($dsMonHoc_Add) > 0): ?>
                <?php while ($m = mysqli_fetch_assoc($dsMonHoc_Add)): ?>
                  <label class="me-3 mb-1">
                    <input type="checkbox" name="monhoc_ids[]" value="<?php echo $m['mamh']; ?>">
                    <?php echo htmlspecialchars($m['tenmh']); ?>
                  </label>
                <?php endwhile; ?>
              <?php else: ?>
                <div class="text-muted">Chưa có môn học nào</div>
              <?php endif; ?>
            </div>

            <div class="col-md-4 mb-2">
              <label class="form-label">Trạng thái</label>
              <select class="form-select" name="trangthai">
                <option value="hoatdong">hoạt động</option>
                <option value="khonghoatdong">không hoạt động</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary">Lưu</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ================= MODAL EDIT ================= -->
  <div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" method="post">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="magv" id="e_magv">
        <div class="modal-header">
          <h5 class="modal-title">Cập nhật giáo viên</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">Họ tên</label>
              <input class="form-control" id="e_hoten" name="hoten" required>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label">Giới tính</label>
              <select class="form-select" id="e_gioitinh" name="gioitinh">
                <option>Nam</option>
                <option>Nữ</option>
                <option>Khác</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label">Ngày sinh</label>
              <input type="date" class="form-control" id="e_ngaysinh" name="ngaysinh" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">CMND/CCCD</label>
              <input class="form-control" id="e_cmndcccd" name="cmndcccd" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Địa chỉ</label>
              <input class="form-control" id="e_diachi" name="diachi" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Điện thoại</label>
              <input class="form-control" id="e_dienthoai" name="dienthoai" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="e_email" name="email" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Trình độ</label>
              <input class="form-control" id="e_trinhdo" name="trinhdo" required>
            </div>

            <!-- CHECKBOX MÔN HỌC (EDIT) -->
            <div class="col-md-12 mb-2" id="e_monhoc_group">
              <label class="form-label">Môn giảng dạy</label><br>
              <?php
              // Gọi lại danh sách môn cho modal Edit
              $dsMonHoc_Edit = $pMH->getAllSubjects();
              while ($m2 = mysqli_fetch_assoc($dsMonHoc_Edit)):
              ?>
                <label class="me-3 mb-1">
                  <input type="checkbox" name="monhoc_ids[]" value="<?php echo $m2['mamh']; ?>" class="e_mh">
                  <?php echo htmlspecialchars($m2['tenmh']); ?>
                </label>
              <?php endwhile; ?>
            </div>

            <div class="col-md-4 mb-2">
              <label class="form-label">Trạng thái</label>
              <select class="form-select" id="e_trangthai" name="trangthai">
                <option value="hoatdong">hoạt động</option>
                <option value="khonghoatdong">không hoạt động</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary">Cập nhật</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    /**
     * monIds: mảng số (ID môn) để tick sẵn
     */
    function setEditData(magv, hoten, ngaysinh, gioitinh, cmnd, diachi, sdt, email, trinhdo, trangthai, monIds) {
      document.getElementById('e_magv').value = magv;
      document.getElementById('e_hoten').value = hoten;
      document.getElementById('e_ngaysinh').value = ngaysinh;
      document.getElementById('e_gioitinh').value = gioitinh;
      document.getElementById('e_cmndcccd').value = cmnd;
      document.getElementById('e_diachi').value = diachi;
      document.getElementById('e_dienthoai').value = sdt;
      document.getElementById('e_email').value = email;
      document.getElementById('e_trinhdo').value = trinhdo;
      document.getElementById('e_trangthai').value = trangthai;

      // Reset & tick checkbox môn (class .e_mh)
      const boxes = document.querySelectorAll('#e_monhoc_group .e_mh');
      boxes.forEach(b => b.checked = false);
      if (Array.isArray(monIds)) {
        const set = new Set(monIds.map(Number));
        boxes.forEach(b => {
          if (set.has(Number(b.value))) b.checked = true;
        });
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>