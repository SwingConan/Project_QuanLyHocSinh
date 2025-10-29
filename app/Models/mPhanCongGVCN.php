<?php
require_once("config/ketnoi.php");

class PhanCongModel {
    private $conn;

    public function __construct() {
        $db = new clsKetNoi();
        $this->conn = $db->moKetNoi();
    }

    // Lấy danh sách lớp chưa có GVCN
    public function getDanhSachLopChuaGVCN() {
        $sql = "SELECT * FROM lop WHERE magvcn IS NULL OR magvcn = ''";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Lấy danh sách giáo viên
    public function getDanhSachGiaoVien() {
        $sql = "SELECT * FROM giaovien";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Kiểm tra giáo viên đã là GVCN lớp khác chưa
    public function kiemTraGVCN($maGV) {
        $sql = "SELECT * FROM lop WHERE magvcn = '$maGV'";
        $result = $this->conn->query($sql);
        return $result->num_rows > 0;
    }

    // Thực hiện phân công
    public function phanCongGVCN($maLop, $maGV) {
        $sql = "UPDATE lop SET magvcn = '$maGV' WHERE malop = '$maLop'";
        return $this->conn->query($sql);
    }
}
?>
