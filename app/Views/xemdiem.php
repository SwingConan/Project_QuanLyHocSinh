<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xem điểm học sinh</title>
    <link rel="stylesheet" href="./public/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="./public/vendor/bootstrap-5.3.3-dist/js/jquery-3.7.1.min.js"></script>
    <script src="./public/vendor/bootstrap-5.3.3-dist/js/bootstrap.min.js"></script>
</head>
<body class="bg-light">
<main class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary mb-0">📘 Xem điểm học sinh</h4>
    </div>

    <form method="POST" class="mb-3">
    <label for="kyhoc">Chọn kỳ học</label>
    <select name="kyhoc" id="kyhoc" class="form-select" required>
        <option value="">-- Chọn kỳ --</option>
        <?php
        $namhoc = date('Y')-1 . '-' . date('Y');  // 2024-2025
        $hk_list = ["HK1", "HK2", "HK3"];
        
        // Hiển thị tất cả HK1, HK2, HK3 cho năm học hiện tại
        foreach ($hk_list as $hk) {
            echo "<option value='{$hk}' data-namhoc='{$namhoc}'>{$hk}-{$namhoc}</option>";
        }
        ?>
    </select>

    <!-- hidden input to carry namhoc since options hold it in data-namhoc -->
    <input type="hidden" name="namhoc" id="namhoc" value="">

    <!-- submit button to request the selected semester's scores -->
    <div class="mt-2">
        <button type="submit" class="btn btn-primary">Xem điểm</button>
    </div>
</form>

<script>
    // ensure the hidden namhoc input is kept in sync with the selected option
    (function(){
        const kySelect = document.getElementById('kyhoc');
        const namInput = document.getElementById('namhoc');

        function syncNamHoc(){
            const opt = kySelect.options[kySelect.selectedIndex];
            if(opt && opt.dataset && opt.dataset.namhoc){
                namInput.value = opt.dataset.namhoc;
            } else {
                namInput.value = '';
            }
        }

        // set initial value
        if (kySelect) {
            syncNamHoc();
            kySelect.addEventListener('change', syncNamHoc);
        }
    })();
</script>

    <!-- BẢNG ĐIỂM -->
    <?php if (isset($bangdiem)) { ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if ($bangdiem->num_rows > 0) { ?>
            <table class="table table-hover align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Môn học</th>
                        <th>Miệng</th>
                        <th>15 phút</th>
                        <th>1 tiết</th>
                        <th>Giữa kỳ</th>
                        <th>Cuối kỳ</th>
                        <th>TB môn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $bangdiem->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $r['mon'] ?></td>
                        <td><?= $r['diemMieng'] ?></td>
                        <td><?= $r['diem15p'] ?></td>
                        <td><?= $r['diem1Tiet'] ?></td>
                        <td><?= $r['giuaky'] ?></td>
                        <td><?= $r['cuoiky'] ?></td>
                        <td class="fw-bold"><?= $r['tbmon'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
                <div class="alert alert-warning">Chưa có điểm cho kỳ học này.</div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

</main>
</body>
</html>
