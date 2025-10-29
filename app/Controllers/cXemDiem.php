<?php
require_once __DIR__ . '/../Models/mXemDiem.php';

class cXemDiem
{
    public function hienThiDiem()
    {
        // start session only if it hasn't been started yet (index.php already starts it)
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $maHS = $_SESSION['mahs'] ?? null;

        // Dev/test helper: if there's no session mahs, allow overriding via GET for testing
        // e.g. visit index.php?act=xemdiem&mahs=HS001 to test without changing index.php
        if (!$maHS && isset($_GET['mahs'])) {
            $maHS = $_GET['mahs'];
        }

        if (!$maHS) {
            echo "<p>Vui lòng đăng nhập trước khi xem điểm.</p>";
            return;
        }

        $p = new mXemDiem();

        // ✅ Lấy danh sách kỳ học TRƯỚC
        $dsKy = $p->layDanhSachKyHoc($maHS);

        // ✅ Sau đó mới kiểm tra xem người dùng đã chọn kỳ chưa
        // use lowercase variable name to match the view's expectations
        $bangdiem = null;
        if (isset($_POST['kyhoc']) && isset($_POST['namhoc'])) {
            $kyhoc = $_POST['kyhoc'];
            $namhoc = $_POST['namhoc'];
            $bangdiem = $p->layBangDiemTheoKy($maHS, $kyhoc, $namhoc);
        }

        // include the correct view file (was referencing vXemDiem.php which doesn't exist)
        include __DIR__ . '/../Views/xemdiem.php';
    }
}
