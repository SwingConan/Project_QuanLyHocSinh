<?php
// app/Controllers/cThoiKhoaBieu.php
include_once("app/Models/mThoiKhoaBieu.php");

class cThoiKhoaBieu {
    public $model;

    public function __construct() {
        $this->model = new mThoiKhoaBieu();
    }
    
    /**
     * Hiển thị trang QL TKB chính
     */
    /**
     * Hiển thị trang QL TKB chính
     */
    public function index() {
        // Lấy năm học/học kỳ mặc định (code tương thích PHP 5.3)
        $allNamHoc = $this->model->getAllNamHoc();
        $defaultNamHoc = isset($allNamHoc[0]['maNamHoc']) ? $allNamHoc[0]['maNamHoc'] : '2023-2024';
        
        $allHocKy = $this->model->getAllHocKy();
        $defaultHocKy = isset($allHocKy[0]['maHocKy']) ? $allHocKy[0]['maHocKy'] : 'HK1';

        // Lấy bộ lọc từ URL (người xem thường lọc theo Lớp)
        $namHoc_filter = isset($_GET['namHoc']) ? $_GET['namHoc'] : $defaultNamHoc;
        $hocKy_filter = isset($_GET['hocKy']) ? $_GET['hocKy'] : $defaultHocKy;
        $maLop_filter = isset($_GET['maLop']) ? $_GET['maLop'] : '';
        $maGV_filter = isset($_GET['maGV']) ? $_GET['maGV'] : '';

        // Lấy dữ liệu cho các bộ lọc
        $danhSachNamHoc = $this->model->getAllNamHoc();
        $danhSachHocKy = $this->model->getAllHocKy();
        $danhSachLop = $this->model->getAllLop();
        $danhSachGiaoVien = $this->model->getAllGiaoVien();
        
        // 1. Lấy TKB dạng danh sách (flat list) như cũ
        $dataTKB = $this->model->searchTKB($namHoc_filter, $hocKy_filter, $maLop_filter, $maGV_filter);
        
        // 2. [CODE MỚI] Chuyển danh sách thành dạng lưới (Grid) 2 chiều
        $tkb_grid = array(); // mảng 2 chiều [thứ][tiết]
        foreach ($dataTKB as $tiet) {
            $thu = $tiet['thu'];
            $tietSo = $tiet['tietSo'];
            
            // Gán tiết học vào đúng ô [thứ][tiết]
            $tkb_grid[$thu][$tietSo] = $tiet;
        }
        
        // 3. Gọi view (View sẽ sử dụng biến $tkb_grid mới này)
        include_once("app/Views/quanlythoikhoabieu.php");
    }

    /**
     * Hiển thị form Thêm
     */
    public function showAddForm() {
        // Lấy dữ liệu cho các dropdown
        $danhSachLop = $this->model->getAllLop();
        $danhSachMonHoc = $this->model->getAllMonHoc();
        $danhSachGiaoVien = $this->model->getAllGiaoVien();
        $danhSachPhongHoc = $this->model->getAllPhongHoc();
        $danhSachNamHoc = $this->model->getAllNamHoc();
        $danhSachHocKy = $this->model->getAllHocKy();
        
        // Biến $message và $formData để xử lý lỗi
        $message = isset($_GET['error']) ? $this->getErrorMessage($_GET['error']) : '';
        
        // Đảm bảo session_start() đã được gọi trong index.php
        $formData = isset($_SESSION['old_data']) ? $_SESSION['old_data'] : array();
        if(isset($_SESSION['old_data'])) {
            unset($_SESSION['old_data']);
        }

        include_once("app/Views/themTKB.php");
    }

    /**
     * Xử lý Thêm
     */
    public function handleAddTKB() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Lấy dữ liệu (Lấy giá trị VARCHAR từ các bảng)
            $maLop = $_POST['maLop'];
            $maMon = $_POST['maMon'];
            $maGV = $_POST['maGV'];
            $maPhong = $_POST['maPhong'];
            $namHoc = $_POST['namHoc']; // VD: "2023-2024"
            $hocKy = $_POST['hocKy'];   // VD: "HK1"
            $thu = $_POST['thu'];
            $tietSo = $_POST['tietSo'];
            $tuanApDung = $_POST['tuanApDung'];
            $ghiChu = $_POST['ghiChu'];

            // 2. Kiểm tra thiếu (Alternative A1)
            if (empty($maLop) || empty($maMon) || empty($maGV) || empty($maPhong) || empty($namHoc) || empty($hocKy) || empty($thu) || empty($tietSo) || empty($tuanApDung)) {
                $_SESSION['old_data'] = $_POST; // Giữ lại dữ liệu đã nhập
                header("Location: index.php?act=quanlythoikhoabieu&action=showAddForm&error=THIEU_THONG_TIN");
                exit();
            }

            // 3. Gọi Model
            $result = $this->model->addTKB($maLop, $maMon, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $ghiChu);

            // 4. Xử lý kết quả
            if ($result == "THANH_CONG") {
                header("Location: index.php?act=quanlythoikhoabieu&namHoc=$namHoc&hocKy=$hocKy&status=add_success");
                exit();
            } else {
                $_SESSION['old_data'] = $_POST;
                header("Location: index.php?act=quanlythoikhoabieu&action=showAddForm&error=$result");
                exit();
            }
        }
    }

    /**
     * Hiển thị form Sửa
     */
    public function showUpdateForm() {
        $maTiet = $_GET['id'];
        $dataTiet = $this->model->getTKBDetails($maTiet);

        if (!$dataTiet) {
            // (Alternative A3)
            echo "<h1>Lỗi: Không tìm thấy tiết TKB.</h1>";
            echo "<a href='index.php?act=quanlythoikhoabieu'>Quay lại</a>";
            exit();
        }

        // Lấy dữ liệu cho dropdowns
        $danhSachLop = $this->model->getAllLop();
        $danhSachMonHoc = $this->model->getAllMonHoc();
        $danhSachGiaoVien = $this->model->getAllGiaoVien();
        $danhSachPhongHoc = $this->model->getAllPhongHoc();
        $danhSachNamHoc = $this->model->getAllNamHoc();
        $danhSachHocKy = $this->model->getAllHocKy();
        
        $message = isset($_GET['error']) ? $this->getErrorMessage($_GET['error']) : '';
        
        include_once("app/Views/suaTKB.php");
    }
    
    /**
     * Xử lý Cập nhật
     */
    public function handleUpdateTKB() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maTiet = $_POST['maTiet'];
            $maLop = $_POST['maLop'];
            $maMon = $_POST['maMon'];
            $maGV = $_POST['maGV'];
            $maPhong = $_POST['maPhong'];
            $namHoc = $_POST['namHoc'];
            $hocKy = $_POST['hocKy'];
            $thu = $_POST['thu'];
            $tietSo = $_POST['tietSo'];
            $tuanApDung = $_POST['tuanApDung'];
            $ghiChu = $_POST['ghiChu'];

            // Kiểm tra thiếu (A1) ... (tương tự handleAddTKB)
            if (empty($maLop) || empty($maMon) || empty($maGV) || empty($maPhong) || empty($namHoc) || empty($hocKy) || empty($thu) || empty($tietSo) || empty($tuanApDung)) {
                header("Location: index.php?act=quanlythoikhoabieu&action=showUpdateForm&id=$maTiet&error=THIEU_THONG_TIN");
                exit();
            }

            // Gọi Model
            $result = $this->model->updateTKB($maTiet, $maLop, $maMon, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $ghiChu);

            if ($result == "THANH_CONG") {
                header("Location: index.php?act=quanlythoikhoabieu&namHoc=$namHoc&hocKy=$hocKy&status=update_success");
                exit();
            } else {
                // Quay lại form Sửa với mã lỗi
                header("Location: index.php?act=quanlythoikhoabieu&action=showUpdateForm&id=$maTiet&error=$result");
                exit();
            }
        }
    }

    /**
     * Xử lý Xóa
     */
    public function handleDeleteTKB() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maTiet = $_POST['maTiet'];
            $namHoc = $_POST['namHoc']; // Để chuyển hướng về đúng trang
            $hocKy = $_POST['hocKy'];
            
            $this->model->deleteTKB($maTiet);
            
            header("Location: index.php?act=quanlythoikhoabieu&namHoc=$namHoc&hocKy=$hocKy&status=delete_success");
            exit();
        }
    }

    /**
     * Hàm trợ giúp dịch mã lỗi
     */
    private function getErrorMessage($errorCode) {
        if (strpos($errorCode, "LOI_HE_THONG:") === 0) {
            return "Thao tác thất bại do lỗi hệ thống: " . substr($errorCode, 14);
        }
        switch ($errorCode) {
            case "THIEU_THONG_TIN": return "Vui lòng nhập đầy đủ thông tin bắt buộc.";
            case "KHONG_PHAN_CONG": return "Lỗi: Giáo viên này chưa được phân công dạy Lớp-Môn này.";
            case "XUNG_DOT_GIAOVIEN": return "Lỗi: Giáo viên bị trùng lịch vào Thứ-Tiết này.";
            case "XUNG_DOT_LOP": return "Lỗi: Lớp đã có lịch vào Thứ-Tiết này.";
            case "XUNG_DOT_PHONG": return "Lỗi: Phòng đã được sử dụng vào Thứ-Tiết này.";
            default: return "Đã xảy ra lỗi không xác định.";
        }
    }
}
?>