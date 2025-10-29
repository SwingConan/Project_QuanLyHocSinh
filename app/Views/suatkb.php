<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật Thời Khóa Biểu</title>
    <link href="./public/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4" style="max-width: 800px;">
        <h2>Cập nhật tiết Thời Khóa Biểu</h2>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="index.php?act=quanlythoikhoabieu&action=handleUpdateTKB" method="POST">
            <input type="hidden" name="maTiet" value="<?php echo $dataTiet['maTiet']; ?>">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="namHoc" class="form-label">Năm Học (*)</label>
                    <select id="namHoc" name="namHoc" class="form-select">
                        <?php foreach ($danhSachNamHoc as $nh): ?>
                            <option value="<?php echo $nh['maNamHoc']; ?>" <?php echo ($nh['maNamHoc'] == $dataTiet['namHoc']) ? 'selected' : ''; ?>>
                                <?php echo $nh['tenNamHoc']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="hocKy" class="form-label">Học kỳ (*)</label>
                    <select id="hocKy" name="hocKy" class="form-select">
                        <?php foreach ($danhSachHocKy as $hk): ?>
                            <option value="<?php echo $hk['maHocKy']; ?>" <?php echo ($hk['maHocKy'] == $dataTiet['hocKy']) ? 'selected' : ''; ?>>
                                <?php echo $hk['tenHocKy']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="maLop" class="form-label">Lớp (*)</label>
                    <select id="maLop" name="maLop" class="form-select">
                        <?php foreach ($danhSachLop as $lop): ?>
                            <option value="<?php echo $lop['maLop']; ?>" <?php echo ($lop['maLop'] == $dataTiet['maLop']) ? 'selected' : ''; ?>>
                                <?php echo $lop['tenLop']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="col-md-6">
                    <label for="maMon" class="form-label">Môn Học (*)</label>
                    <select id="maMon" name="maMon" class="form-select">
                        <?php foreach ($danhSachMonHoc as $mh): ?>
                            <option value="<?php echo $mh['maMon']; ?>" <?php echo ($mh['maMon'] == $dataTiet['maMon']) ? 'selected' : ''; ?>>
                                <?php echo $mh['tenMon']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="maGV" class="form-label">Giáo Viên (*)</label>
                    <select id="maGV" name="maGV" class="form-select">
                        <?php foreach ($danhSachGiaoVien as $gv): ?>
                            <option value="<?php echo $gv['maGV']; ?>" <?php echo ($gv['maGV'] == $dataTiet['maGV']) ? 'selected' : ''; ?>>
                                <?php echo $gv['tenGV']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="maPhong" class="form-label">Phòng Học (*)</label>
                    <select id="maPhong" name="maPhong" class="form-select">
                        <?php foreach ($danhSachPhongHoc as $ph): ?>
                            <option value="<?php echo $ph['maPhong']; ?>" <?php echo ($ph['maPhong'] == $dataTiet['maPhong']) ? 'selected' : ''; ?>>
                                <?php echo $ph['tenPhong']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="thu" class="form-label">Thứ (*)</label>
                    <select id="thu" name="thu" class="form-select">
                        <?php for ($i = 2; $i <= 8; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $dataTiet['thu']) ? 'selected' : ''; ?>>
                            <?php echo ($i == 8) ? "Chủ Nhật" : "Thứ $i"; ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                 <div class="col-md-3">
                    <label for="tietSo" class="form-label">Tiết số (*)</label>
                    <select id="tietSo" name="tietSo" class="form-select">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $dataTiet['tietSo']) ? 'selected' : ''; ?>>
                            Tiết <?php echo $i; ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="tuanApDung" class="form-label">Tuần áp dụng (*)</label>
                    <input type="text" class="form-control" id="tuanApDung" name="tuanApDung" 
                           placeholder="Ví dụ: 8-12, 15" 
                           value="<?php echo htmlspecialchars($dataTiet['tuanApDung']); ?>">
                </div>
                <div class="col-md-12">
                    <label for="ghiChu" class="form-label">Ghi chú (Tùy chọn)</label>
                    <textarea class="form-control" id="ghiChu" name="ghiChu" rows="3"><?php echo htmlspecialchars($dataTiet['ghiChu']); ?></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success mt-4">Cập nhật</button>
            <a href="index.php?act=quanlythoikhoabieu&namHoc=<?php echo $dataTiet['namHoc']; ?>&hocKy=<?php echo $dataTiet['hocKy']; ?>" class="btn btn-secondary mt-4">Hủy</a>
        </form>
    </div>
</body>
</html>