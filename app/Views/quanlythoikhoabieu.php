<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thời Khóa Biểu</title>
    
    <link href="./public/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="./public/css/style.css">
</head>
<body>
    <div class="container-fluid mt-4"> <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2>🗓️ Quản lý Thời khóa biểu</h2>
                <!-- <p class="text-muted">Giao diện quản lý, thêm, sửa, xóa các tiết học trong hệ thống.</p> -->
            </div>
            
            <?php
            // KIỂM TRA QUYỀN: Chỉ Admin mới thấy nút "Thêm"
            if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin'):
            ?>
                <div>
                    <a href="index.php?act=quanlythoikhoabieu&action=showAddForm" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm Tiết học
                    </a>
                    </div>
            <?php endif; ?>
        </div>

        <form method="GET" class="row g-3 mb-4 p-3 border rounded bg-light">
            <input type="hidden" name="act" value="quanlythoikhoabieu">
            <input type="hidden" name="action" value="index">
            <div class="col-md-2">
                <label for="namHoc" class="form-label">Năm Học</label>
                <select id="namHoc" name="namHoc" class="form-select">
                    <?php foreach ($danhSachNamHoc as $nh): ?>
                        <option value="<?php echo $nh['maNamHoc']; ?>" <?php echo ($nh['maNamHoc'] == $namHoc_filter) ? 'selected' : ''; ?>>
                            <?php echo $nh['tenNamHoc']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="hocKy" class="form-label">Học Kỳ</label>
                <select id="hocKy" name="hocKy" class="form-select">
                    <?php foreach ($danhSachHocKy as $hk): ?>
                        <option value="<?php echo $hk['maHocKy']; ?>" <?php echo ($hk['maHocKy'] == $hocKy_filter) ? 'selected' : ''; ?>>
                            <?php echo $hk['tenHocKy']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="maLop" class="form-label">Lọc theo Lớp</label>
                <select id="maLop" name="maLop" class="form-select">
                    <option value="">-- Tất cả Lớp --</option>
                    <?php foreach ($danhSachLop as $lop): ?>
                        <option value="<?php echo $lop['maLop']; ?>" <?php echo ($lop['maLop'] == $maLop_filter) ? 'selected' : ''; ?>>
                            <?php echo $lop['tenLop']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="maGV" class="form-label">Lọc theo Giáo viên</label>
                <select id="maGV" name="maGV" class="form-select">
                    <option value="">-- Tất cả GV --</option>
                    <?php foreach ($danhSachGiaoVien as $gv): ?>
                        <option value="<?php echo $gv['maGV']; ?>" <?php echo ($gv['maGV'] == $maGV_filter) ? 'selected' : ''; ?>>
                            <?php echo $gv['tenGV']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-info w-100">
                    <i class="bi bi-filter"></i> Lọc
                </button>
            </div>
        </form>

        <div class="tkb-grid">
            <div class="row g-0">
                <div class="col col-header" style="width: 8%;">Tiết</div>
                <div class="col col-header">Thứ Hai</div>
                <div class="col col-header">Thứ Ba</div>
                <div class="col col-header">Thứ Tư</div>
                <div class="col col-header">Thứ Năm</div>
                <div class="col col-header">Thứ Sáu</div>
                <div class="col col-header">Thứ Bảy</div>
                </div>

            <?php
            // Lặp qua các Tiết (ví dụ: 10 tiết)
            for ($tiet = 1; $tiet <= 10; $tiet++):
            ?>
                <div class="row g-0">
                    <div class="col col-label" style="width: 8%;">
                        <strong>Tiết <?php echo $tiet; ?></strong>
                    </div>
                    
                    <?php
                    // Lặp qua các Thứ (từ Thứ 2 đến Thứ 7)
                    for ($thu = 2; $thu <= 7; $thu++):
                    ?>
                        <div class="col col-cell">
                            <?php
                            // KIỂM TRA XEM CÓ DATA TRONG LƯỚI KHÔNG
                            if (isset($tkb_grid[$thu][$tiet])):
                                $data = $tkb_grid[$thu][$tiet];
                            ?>
                                <div class="tkb-card">
                                    <span class="subject">
                                        <?php echo $data['tenMon']; ?> - <?php echo $data['tenLop']; ?>
                                    </span>
                                    <span class="details">
                                        <i class="bi bi-person-fill"></i> <?php echo $data['tenGV']; ?>
                                    </span>
                                    <span class="details">
                                        <i class="bi bi-geo-alt-fill"></i> <?php echo $data['tenPhong']; ?>
                                    </span>
                                    <span class="details">
                                        <i class="bi bi-calendar-week"></i> Tuần: <?php echo $data['tuanApDung']; ?>
                                    </span>
                                    
                                    <?php
                                    // NÚT ADMIN (chỉ hiện khi đăng nhập)
                                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin'):
                                    ?>
                                        <div class="admin-actions">
                                            <a href="index.php?act=quanlythoikhoabieu&action=showUpdateForm&id=<?php echo $data['maTiet']; ?>" class="btn btn-warning btn-sm" title="Sửa">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            
                                            <form action="index.php?act=quanlythoikhoabieu&action=handleDeleteTKB" method="POST" style="display: inline-block;">
                                                <input type="hidden" name="maTiet" value="<?php echo $data['maTiet']; ?>">
                                                <input type="hidden" name="namHoc" value="<?php echo $namHoc_filter; ?>">
                                                <input type="hidden" name="hocKy" value="<?php echo $hocKy_filter; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa tiết này?');" title="Xóa">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; // Hết lặp Thứ ?>
                </div>
            <?php endfor; // Hết lặp Tiết ?>
        </div>
        </div>
    
    <script src="./public/vendor/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>