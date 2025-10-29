<?php
session_start();
unset($_SESSION['search']);

// Hiển thị thông báo alert (nếu có)
if (isset($_SESSION['message'])) {
    echo '<script>alert("' . $_SESSION['message'] . '")</script>';
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống quản lý trường học</title>
    <link rel="stylesheet" href="./public/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container-fluid p-0">

        <!-- HEADER -->
        <header class="bg-primary text-white py-3 shadow-sm">
            <div class="container d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Hệ thống Quản lý Trường học</h3>
                <nav>
                    <a href="index.php?act=trangchu" class="text-white me-3 text-decoration-none">Trang chủ</a>
                    <a href="index.php?act=quanlydanhmucmonhoc" class="text-white me-3 text-decoration-none">Quản lý môn học</a>
                    <a href="index.php?act=phanCongGVCN" class="text-white me-3 text-decoration-none">Phân công GVCN</a>
                    <a href="index.php?act=quanlydanhmucgiaovien" class="text-white me-3 text-decoration-none">
                        Quản lý giáo viên
                    </a>
                    <a href="index.php?act=xemdiem" class="text-white me-3 text-decoration-none">Xem điểm</a>
                    <a href="index.php?act=dangxuat" class="text-white text-decoration-none">Đăng xuất</a>
                </nav>
            </div>
        </header>

        <!-- MAIN -->
        <main class="container my-4">
            <?php
            if (isset($_GET['act'])) {
                $act = $_GET['act'];
                switch ($act) {
                    case 'trangchu':
                        echo "<h4 class='text-center text-primary'>Chào mừng đến hệ thống quản lý trường học</h4>";
                        break;

                    case 'quanlydanhmucmonhoc':
                        include "app/Views/quanlydanhmucmonhoc.php";
                        break;
                    case 'phanCongGVCN':
                        include_once "app/Controllers/cPhanCongGVCN.php";
                        $controller = new PhanCongController();

                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $controller->thucHienPhanCong();
                            } else {
                                $controller->hienThiTrangPhanCong();
                            }
                        break;

                    case 'quanlydanhmucgiaovien':
                        include "app/Views/quanlydanhmucgiaovien.php";
                    case 'xemdiem':
                        // use the controller so it prepares $dsKy / $bangdiem for the view
                        require_once __DIR__ . '/app/Controllers/cXemDiem.php';
                        $c = new cXemDiem();
                        $c->hienThiDiem();
                        break;

                    case 'dangxuat':
                        echo "<h4 class='text-center text-danger'>Bạn đã đăng xuất!</h4>";
                        break;

                    default:
                        echo "<h4 class='text-center text-secondary'>Trang không tồn tại</h4>";
                        break;
                }
            } else {
                echo "<h4 class='text-center text-primary'>Chào mừng đến hệ thống quản lý trường học</h4>";
            }
            ?>
        </main>

        <!-- FOOTER -->
        <footer class="bg-dark text-white text-center py-3">
            <small>© 2025 Sinh Viên Vippro - Quản lý Trường học | Khoa CNTT - IUH</small>
        </footer>
    </div>

    <script src="./public/vendor/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>