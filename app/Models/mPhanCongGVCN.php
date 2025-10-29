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
        // Chỉ lấy các cột cần thiết theo schema hiện tại
        // Ưu tiên giáo viên đang hoạt động để phân công
        $sql = "SELECT magv, hoten FROM giaovien WHERE trangthai='hoatdong' AND (isGVCN = 0 OR isGVCN IS NULL)";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Kiểm tra giáo viên đã là GVCN lớp khác chưa
    public function kiemTraGVCN($maGV) {
        $maGV = intval($maGV);
        $sqlGV = "SELECT isGVCN FROM giaovien WHERE magv=$maGV LIMIT 1";
        $rsGV = $this->conn->query($sqlGV);
        if ($rsGV && $rsGV->num_rows > 0) {
            $row = $rsGV->fetch_assoc();
            if (isset($row['isGVCN']) && intval($row['isGVCN']) === 1) return true;
        }
        $sql = "SELECT 1 FROM lop WHERE magvcn = '$maGV' LIMIT 1";
        $result = $this->conn->query($sql);
        return $result && $result->num_rows > 0;
    }

    // Thực hiện phân công
    public function phanCongGVCN($maLop, $maGV) {
        $maGV = intval($maGV);
        $maLop = $this->conn->real_escape_string($maLop);

        try {
            $this->conn->begin_transaction();

            // Chi cap nhat neu lop chua co GVCN
            $sql1 = "UPDATE lop SET magvcn = '$maGV' WHERE malop = '$maLop' AND (magvcn IS NULL OR magvcn = '')";
            $this->conn->query($sql1);
            if ($this->conn->affected_rows <= 0) {
                $this->conn->rollback();
                return false;
            }

            // Danh dau giao vien la GVCN
            $sql2 = "UPDATE giaovien SET isGVCN = 1 WHERE magv = $maGV";
            if (!$this->conn->query($sql2)) {
                $this->conn->rollback();
                return false;
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>
