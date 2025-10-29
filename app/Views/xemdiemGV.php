<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem/Thêm điểm học sinh</title>
    <link rel="stylesheet" href="./public/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css" />
</head>
<body class="bg-light">

<main class="container my-4">

    <?php
    // Định nghĩa an toàn: nếu hàm đã bị xóa hoặc không tồn tại, định nghĩa lại để tránh fatal error
    if (!function_exists('fix_mojibake')) {
        function fix_mojibake($s) {
            if (!is_string($s) || $s === '') return $s;
            $map = [
                'Nguy?n' => 'Nguyễn',
                'Ngo?i ng?' => 'Ngoại ngữ',
                'Ng? v?n' => 'Ngữ văn',
                'Sinh h?c' => 'Sinh học',
                'Ho?c' => 'Học'
            ];
            return strtr($s, $map);
        }
    }
    ?>

    <?php if (isset($message) && $message): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!isset($_GET['mode']) || $_GET['mode'] !== 'detail'): ?>
        <!-- DANH SÁCH HỌC SINH -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Danh sách học sinh</h4>
                <a href="index.php?act=xemdiemGV&mode=them" class="btn btn-sm btn-light">
                    + Thêm điểm
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã HS</th>
                            <th>Tên học sinh</th>
                            <th>Lớp</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (isset($dsHocSinh) && is_array($dsHocSinh) && count($dsHocSinh) > 0):
                            foreach ($dsHocSinh as $row):
                                $mahs = isset($row['mahs']) ? $row['mahs'] : '';
                                $tenhs = isset($row['tenhs']) ? $row['tenhs'] : '';
                                $lop = isset($row['lop']) ? $row['lop'] : '';
                                $mahsEncoded = urlencode($mahs);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($mahs) ?></td>
                            <td><?= htmlspecialchars($tenhs) ?></td>
                            <td><?= htmlspecialchars($lop) ?></td>
                            <td>
                                <a href="index.php?act=xemdiemGV&mode=detail&mahs=<?= $mahsEncoded ?>" 
                                   class="btn btn-sm btn-primary">
                                    Xem điểm
                                </a>
                            </td>
                        </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Không có dữ liệu học sinh
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif (isset($_GET['mode']) && $_GET['mode'] === 'them'): ?>
        <!-- FORM THÊM ĐIỂM -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Thêm điểm học sinh</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?act=xemdiemGV&mode=them">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mahs" class="form-label">Mã HS *</label>
                            <input type="text" class="form-control" id="mahs" name="mahs" required 
                                   value="<?= isset($_POST['mahs']) ? htmlspecialchars($_POST['mahs']) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tenhs" class="form-label">Tên HS *</label>
                            <input type="text" class="form-control" id="tenhs" name="tenhs" required 
                                   value="<?= isset($_POST['tenhs']) ? htmlspecialchars($_POST['tenhs']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="diem_giua_ky" class="form-label">Điểm giữa kỳ</label>
                            <input type="number" step="0.5" min="0" max="10" class="form-control" id="diem_giua_ky" name="diem_giua_ky" 
                                   value="<?= isset($_POST['diem_giua_ky']) ? htmlspecialchars($_POST['diem_giua_ky']) : '0' ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="diem_cuoi_ky" class="form-label">Điểm cuối kỳ</label>
                            <input type="number" step="0.5" min="0" max="10" class="form-control" id="diem_cuoi_ky" name="diem_cuoi_ky" 
                                   value="<?= isset($_POST['diem_cuoi_ky']) ? htmlspecialchars($_POST['diem_cuoi_ky']) : '0' ?>">
                        </div>
                        <div class="col-md-9"></div>
                        <div class="col-md-4 mb-3">
                            <label for="namhoc" class="form-label">Năm học *</label>
                            <input type="text" class="form-control" id="namhoc" name="namhoc" required placeholder="VD: 2023-2024"
                                   value="<?= isset($_POST['namhoc']) ? htmlspecialchars($_POST['namhoc']) : '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="hocky" class="form-label">Học kỳ *</label>
                            <select class="form-select" id="hocky" name="hocky" required>
                                <option value="">-- Chọn --</option>
                                <option value="HK1" <?= isset($_POST['hocky']) && $_POST['hocky'] == 'HK1' ? 'selected' : '' ?>>HK1</option>
                                <option value="HK2" <?= isset($_POST['hocky']) && $_POST['hocky'] == 'HK2' ? 'selected' : '' ?>>HK2</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="monhoc" class="form-label">Môn học *</label>
                        <input type="text" class="form-control" id="monhoc" name="monhoc" required placeholder="VD: Toán học"
                               value="<?= isset($_POST['monhoc']) ? htmlspecialchars($_POST['monhoc']) : '' ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="diem_mieng" class="form-label">Điểm miệng</label>
                            <input type="number" step="0.5" min="0" max="10" class="form-control" id="diem_mieng" name="diem_mieng" 
                                   value="<?= isset($_POST['diem_mieng']) ? htmlspecialchars($_POST['diem_mieng']) : '0' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="diem_15p" class="form-label">Điểm 15 phút</label>
                            <input type="number" step="0.5" min="0" max="10" class="form-control" id="diem_15p" name="diem_15p" 
                                   value="<?= isset($_POST['diem_15p']) ? htmlspecialchars($_POST['diem_15p']) : '0' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="diem_1tiet" class="form-label">Điểm 1 tiết</label>
                            <input type="number" step="0.5" min="0" max="10" class="form-control" id="diem_1tiet" name="diem_1tiet" 
                                   value="<?= isset($_POST['diem_1tiet']) ? htmlspecialchars($_POST['diem_1tiet']) : '0' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="diem_hocky" class="form-label">Điểm học kỳ</label>
                            <input type="number" step="0.5" min="0" max="10" class="form-control" id="diem_hocky" name="diem_hocky" 
                                   value="<?= isset($_POST['diem_hocky']) ? htmlspecialchars($_POST['diem_hocky']) : '0' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ghichu" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="ghichu" name="ghichu" rows="2"><?= isset($_POST['ghichu']) ? htmlspecialchars($_POST['ghichu']) : '' ?></textarea>
                    </div>
                    
                    <small class="text-muted d-block mb-3">
                        <strong>Công thức tính điểm TB:</strong> (Điểm miệng + Điểm 15p + Điểm 1 tiết×2 + Điểm cuối kỳ×3) ÷ 7
                    </small>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="submit" class="btn btn-success">Thêm điểm</button>
                        <a href="index.php?act=xemdiemGV" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>

    <?php else: ?>
        <!-- CHI TIẾT ĐIỂM -->
        <div class="mb-3">
            <a href="index.php?act=xemdiemGV" class="btn btn-secondary">← Quay lại</a>
        </div>

        <?php if (isset($thongTin) && $thongTin): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        Điểm của: <strong><?= isset($thongTin['tenhs']) ? htmlspecialchars(fix_mojibake($thongTin['tenhs'])) : '' ?></strong>
                        (<?= isset($thongTin['mahs']) ? htmlspecialchars($thongTin['mahs']) : '' ?>) - Lớp <?= isset($thongTin['lop']) ? htmlspecialchars($thongTin['lop']) : '' ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($dsDiem) && is_array($dsDiem) && count($dsDiem) > 0): ?>
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Môn học</th>
                                    <th>Điểm miệng</th>
                                    <th>Điểm 15p</th>
                                    <th>Điểm 1 tiết</th>
                                    <th>Điểm giữa kỳ</th>
                                    <th>Điểm cuối kỳ</th>
                                    <th>Điểm TB</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dsDiem as $diem): 
                                    $monhocRaw = isset($diem['monhoc']) ? trim($diem['monhoc']) : '';
                                    $monhoc = $monhocRaw !== '' ? htmlspecialchars(fix_mojibake($monhocRaw)) : 'Môn học (chưa rõ)';
                                    $diem_mieng = isset($diem['diem_mieng']) ? htmlspecialchars($diem['diem_mieng']) : '--';
                                    $diem_15p = isset($diem['diem_15p']) ? htmlspecialchars($diem['diem_15p']) : '--';
                                    $diem_1tiet = isset($diem['diem_1tiet']) ? htmlspecialchars($diem['diem_1tiet']) : '--';
                                    $diem_giua_ky = isset($diem['diem_giua_ky']) ? htmlspecialchars($diem['diem_giua_ky']) : '--';
                                    $diem_cuoi_ky = isset($diem['diem_cuoi_ky']) ? htmlspecialchars($diem['diem_cuoi_ky']) : '--';
                                    $diemtb = isset($diem['diemtb']) ? htmlspecialchars($diem['diemtb']) : '--';
                                ?>
                                <tr>
                                    <td><?= $monhoc ?></td>
                                    <td><?= $diem_mieng ?></td>
                                    <td><?= $diem_15p ?></td>
                                    <td><?= $diem_1tiet ?></td>
                                    <td><?= $diem_giua_ky ?></td>
                                    <td><?= $diem_cuoi_ky ?></td>
                                    <td><strong><?= $diemtb ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning text-center mb-0">
                            Chưa có dữ liệu điểm cho học sinh này
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Không tìm thấy thông tin học sinh
            </div>
        <?php endif; ?>

    <?php endif; ?>

</main>

<script src="./public/vendor/bootstrap-5.3.3-dist/js/bootstrap.min.js"></script>
</body>
</html>
