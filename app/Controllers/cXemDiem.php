<?php
require_once __DIR__ . '/../Models/mXemDiem.php';

class cXemDiem
{
    public function hienThiDiem()
    {
        // ✅ Không cần session, gán trực tiếp mã học sinh
        $maHS = 'HS001';

        // Khởi tạo model
        $p = new mXemDiem();

        // Lấy danh sách kỳ học
        $dsKy = $p->layDanhSachKyHoc($maHS);

        // Lấy bảng điểm nếu người dùng chọn kỳ/năm
        $bangdiem = null;
        if (isset($_POST['kyhoc']) && isset($_POST['namhoc'])) {
            $kyhoc = $_POST['kyhoc'];
            $namhoc = $_POST['namhoc'];
            $bangdiem = $p->layBangDiemTheoKy($maHS, $kyhoc, $namhoc);
        }

        // Load view
        include __DIR__ . '/../Views/xemdiem.php';
    }
}
