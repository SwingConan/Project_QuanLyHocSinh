
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

        <header class="bg-primary text-white py-3 shadow-sm">
            <div class="container d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Hệ thống Quản lý Trường học</h3>
                <nav>
                    <a href="index.php?act=trangchu" class="text-white me-3 text-decoration-none">Trang chủ</a>
                    
                    <a href="index.php?act=quanlythoikhoabieu" class="text-white me-3 text-decoration-none">Thời Khóa Biểu</a>
                    
                    <a href="index.php?act=quanlydanhmucmonhoc" class="text-white me-3 text-decoration-none">Quản lý môn học</a>
                    <a href="index.php?act=phanCongGVCN" class="text-white me-3 text-decoration-none">Phân công GVCN</a>
                    <a href="index.php?act=quanlydanhmucgiaovien" class="text-white me-3 text-decoration-none">
                        Quản lý giáo viên
                    </a>
                    <a href="index.php?act=xemdiem" class="text-white me-3 text-decoration-none">Xem điểm</a>
                    
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="index.php?act=dangxuat" class="btn btn-outline-light btn-sm">Đăng xuất (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light btn-sm">Đăng nhập</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>

        <main class="container my-4">
            <?php
            // Không cần 'isset' nữa vì chúng ta đã xử lý ở trên
            switch ($act) {
                case 'trangchu':
                    // Nếu là admin thì chào admin, nếu không thì chào khách
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
                         echo "<h4 class='text-center text-primary'>Chào mừng Admin " . htmlspecialchars($_SESSION['username']) . " đến hệ thống quản lý.</h4>";
                    } else {
                         echo "<h4 class='text-center text-primary'>Chào mừng đến hệ thống quản lý trường học</h4>";
                    }
                    break;

                // == CÁC CASE CỦA ADMIN (đã được bảo vệ) ==
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
                    break; 
                case 'xemdiem':
                    require_once __DIR__ . '/app/Controllers/cXemDiem.php';
                    $c = new cXemDiem();
                    $c->hienThiDiem();
                    break;

                // == CASE ĐĂNG XUẤT (ĐÃ SỬA) ==
                case 'dangxuat':
                    session_unset();    // Xóa tất cả biến session
                    session_destroy();  // Hủy session
                    header('Location: login.php'); // Chuyển về trang đăng nhập
                    exit();
                    break;
                    
                // == CASE TKB (ĐÃ CẬP NHẬT LOGIC) ==
                case 'quanlythoikhoabieu':
                    
                    include_once "app/Controllers/cThoiKhoaBieu.php";
                    $controller = new cThoiKhoaBieu();
                    $action = isset($_GET['action']) ? $_GET['action'] : 'index';
                    
                    // BẢO VỆ: Nếu hành động KHÁC 'index' (như thêm, sửa, xóa)
                    // thì phải kiểm tra Admin
                    if ($action != 'index') {
                        if (!isset($_SESSION['role']) || $_SESSION['role'] == 'Admin') {
                            echo "<h4 class='text-center text-danger'>Bạn không có quyền thực hiện hành động này. Vui lòng đăng nhập Admin.</h4>";
                            break; // Dừng lại
                        }
                    }

                    // Nếu là 'index' (công khai) hoặc là Admin (đã vượt qua)
                    switch ($action) {
                        case 'index': $controller->index(); break;
                        case 'showAddForm': $controller->showAddForm(); break;
                        case 'handleAddTKB': $controller->handleAddTKB(); break;
                        case 'showUpdateForm': $controller->showUpdateForm(); break;
                        case 'handleUpdateTKB': $controller->handleUpdateTKB(); break;
                        case 'handleDeleteTKB': $controller->handleDeleteTKB(); break;
                        default: $controller->index(); break;
                    }
                    break;
                    
                default:
                    echo "<h4 class='text-center text-secondary'>Trang không tồn tại</h4>";
                    break;
            }
            ?>
        </main>

        <footer class="bg-dark text-white text-center py-3">
            <small>© 2025 Sinh Viên Vippro - Quản lý Trường học | Khoa CNTT - IUH</small>
        </footer>
    </div>

    <script src="./public/vendor/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>