<?php
require_once("app/Models/mPhanCongGVCN.php");

class PhanCongController {
    private $model;

    public function __construct() {
        $this->model = new PhanCongModel();
    }

    public function hienThiTrangPhanCong() {
        $dsLop = $this->model->getDanhSachLopChuaGVCN();
        $dsGV = $this->model->getDanhSachGiaoVien();
        include("app/Views/PhanCongGVCN.php");
    }

    public function thucHienPhanCong() {
        if (isset($_POST['btnPhanCong'])) {
            $maLop = $_POST['maLop'];
            $maGV = $_POST['maGV'];

            // Kiểm tra GV đã là GVCN lớp khác chưa
            if ($this->model->kiemTraGVCN($maGV)) {
                echo "<script>alert('Giáo viên đã là GVCN lớp khác, vui lòng chọn GV khác!');</script>";
            } else {
                if ($this->model->phanCongGVCN($maLop, $maGV)) {
                    echo "<script>alert('Phân công thành công!');</script>";
                } else {
                    echo "<script>alert('Lỗi khi phân công!');</script>";
                }
            }

            $this->hienThiTrangPhanCong();
        }
    }
}
?>
