<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân công GVCN</title>
    <link rel="stylesheet" href="public/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4 text-primary">Phân công Giáo viên chủ nhiệm</h2>

    <form method="POST" class="card p-4 shadow-lg">
        <div class="mb-3">
            <label class="form-label fw-bold">Chọn lớp:</label>
            <select name="maLop" class="form-select" required>
                <option value="">-- Chọn lớp --</option>
                <?php
                if ($dsLop->num_rows > 0) {
                    while ($row = $dsLop->fetch_assoc()) {
                        echo "<option value='{$row['malop']}'>{$row['tenlop']}</option>";
                    }
                } else {
                    echo "<option disabled>Không có lớp trống để phân công</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Chọn giáo viên:</label>
            <select name="maGV" class="form-select" required>
                <option value="">-- Chọn giáo viên --</option>
                <?php
                while ($gv = $dsGV->fetch_assoc()) {
                    echo "<option value='{$gv['magv']}'>{$gv['hoten']}</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" name="btnPhanCong" class="btn btn-success w-100">Phân công</button>
    </form>
</div>

<script src="public/vendor/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
