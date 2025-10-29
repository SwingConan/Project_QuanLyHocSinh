<?php
// app/Models/mThoiKhoaBieu.php

class mThoiKhoaBieu {
    private $conn;

    public function __construct() {
        // 1. Dòng này sẽ nạp file config/ketnoi.php
        // Khi nạp vào bên trong một hàm, nó sẽ tạo ra biến $conn
        // CÓ PHẠM VI CỤC BỘ (local) bên trong hàm __construct này.
        include_once(__DIR__ . '/../../config/ketnoi.php');
        
        // 2. (SỬA LỖI)
        // Chúng ta KHÔNG dùng 'global $conn;'
        // Chúng ta gán thẳng biến $conn (vừa được tạo cục bộ) 
        // vào thuộc tính $this->conn của class.
        
        if (isset($conn)) {
            $this->conn = $conn;
        } else {
            // Nếu $conn không tồn tại, file ketnoi.php của bạn có vấn đề
            die("Lỗi nghiêm trọng: 'config/ketnoi.php' không tạo ra biến \$conn.");
        }
    }

    /**
     * Hàm trợ giúp fetch_all (tương thích PHP 5.3)
     */
    private function fetch_all_assoc($stmt) {
        $result = array();
        $stmt->store_result(); // Cần thiết
        
        // Lấy metadata (tên cột)
        $meta = $stmt->result_metadata();
        $fields = array();
        $row = array();
        
        while ($field = $meta->fetch_field()) {
            $fields[] = $field->name;
        }
        
        // Gán $row[colName] vào biến $col
        $bind_vars = array();
        for($i=0; $i < count($fields); $i++) {
             $bind_vars[$i] = &$row[$fields[$i]];
        }
        call_user_func_array(array($stmt, 'bind_result'), $bind_vars);

        // Fetch dữ liệu
        while ($stmt->fetch()) {
            $result_row = array();
            foreach($row as $key => $val){
                $result_row[$key] = $val;
            }
            $result[] = $result_row;
        }
        
        $stmt->close();
        return $result;
    }

    /**
     * Hàm trợ giúp fetch_assoc (tương thích PHP 5.3)
     */
    private function fetch_assoc($stmt) {
        $result = $this->fetch_all_assoc($stmt);
        return isset($result[0]) ? $result[0] : null;
    }


    // ========== HÀM LẤY DỮ LIỆU CHO FORM (ĐÃ SỬA) ==========

    public function getAllLop() {
        $sql = "SELECT maLop, tenLop FROM lop";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $this->fetch_all_assoc($stmt);
    }
    public function getAllMonHoc() {
        $sql = "SELECT maMon, tenMon FROM monhoc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $this->fetch_all_assoc($stmt);
    }
    public function getAllGiaoVien() {
        $sql = "SELECT maGV, tenGV FROM giaovien";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $this->fetch_all_assoc($stmt);
    }
    public function getAllPhongHoc() {
        $sql = "SELECT maPhong, tenPhong FROM phonghoc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $this->fetch_all_assoc($stmt);
    }
    public function getAllNamHoc() {
        $sql = "SELECT maNamHoc, tenNamHoc FROM namhoc ORDER BY tenNamHoc DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $this->fetch_all_assoc($stmt);
    }
    public function getAllHocKy() {
        $sql = "SELECT maHocKy, tenHocKy FROM hocky";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $this->fetch_all_assoc($stmt);
    }
    
    // ========== HÀM LOGIC NGHIỆP VỤ (ĐÃ SỬA) ==========

    /**
     * (Đã điều chỉnh) Kiểm tra GV có được phân công Lớp-Môn không
     */
    private function checkPhanCong($maGV, $maLop, $maMon) {
        $sql = "SELECT id FROM phancong 
                WHERE maGV = ? AND maLop = ? AND maMon = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $maGV, $maLop, $maMon);
        $stmt->execute();
        $stmt->store_result(); // Cần thiết
        $count = $stmt->num_rows;
        $stmt->close();
        return $count > 0;
    }

    /**
     * (Alternative Flow A2) Kiểm tra xung đột
     */
    private function checkConflict($maLop, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $maTiet_exclude = 0) {
        
        $sql = "SELECT maGV, maLop, maPhong FROM tiet_tkb 
                WHERE namHoc = ? AND hocKy = ? AND thu = ? AND tietSo = ? 
                      AND tuanApDung = ? AND trangThai = 'active'
                      AND maTiet != ? 
                      AND (maGV = ? OR maLop = ? OR maPhong = ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssiisisss", $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $maTiet_exclude, $maGV, $maLop, $maPhong);
        $stmt->execute();
        
        // Sửa lỗi get_result()
        $conflict = $this->fetch_assoc($stmt); 
        
        if ($conflict) {
            if ($conflict['maGV'] == $maGV) return "XUNG_DOT_GIAOVIEN";
            if ($conflict['maLop'] == $maLop) return "XUNG_DOT_LOP";
            if ($conflict['maPhong'] == $maPhong) return "XUNG_DOT_PHONG";
        }
        return "OK"; // Không xung đột
    }
    
    /**
     * Subflow: Thêm tiết TKB (Không đổi)
     */
    public function addTKB($maLop, $maMon, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $ghiChu) {
        if (!$this->checkPhanCong($maGV, $maLop, $maMon)) {
            return "KHONG_PHAN_CONG";
        }

        $conflict = $this->checkConflict($maLop, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, 0);
        if ($conflict != "OK") {
            return $conflict; 
        }
        
        $sql = "INSERT INTO tiet_tkb (maLop, maMon, maGV, maPhong, namHoc, hocKy, thu, tietSo, tuanApDung, ghiChu, trangThai) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssiiss", $maLop, $maMon, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $ghiChu);
        
        if ($stmt->execute()) {
            $stmt->close();
            return "THANH_CONG";
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "LOI_HE_THONG: " . $error;
        }
    }

    /**
     * Subflow: Cập nhật tiết TKB (Không đổi)
     */
    public function updateTKB($maTiet, $maLop, $maMon, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $ghiChu) {
        if (!$this->checkPhanCong($maGV, $maLop, $maMon)) {
            return "KHONG_PHAN_CONG";
        }

        $conflict = $this->checkConflict($maLop, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $maTiet);
        if ($conflict != "OK") {
            return $conflict;
        }

        $sql = "UPDATE tiet_tkb 
                SET maLop = ?, maMon = ?, maGV = ?, maPhong = ?, namHoc = ?, hocKy = ?, 
                    thu = ?, tietSo = ?, tuanApDung = ?, ghiChu = ? 
                WHERE maTiet = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssiissi", $maLop, $maMon, $maGV, $maPhong, $namHoc, $hocKy, $thu, $tietSo, $tuanApDung, $ghiChu, $maTiet);
        
        if ($stmt->execute()) {
            $stmt->close();
            return "THANH_CONG";
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "LOI_HE_THONG: " . $error;
        }
    }

    /**
     * Subflow: Xóa tiết TKB (Xóa mềm) (Không đổi)
     */
    public function deleteTKB($maTiet) {
        $sql = "UPDATE tiet_tkb SET trangThai = 'deleted' WHERE maTiet = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maTiet);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Lấy danh sách TKB (Tìm kiếm) (ĐÃ SỬA LỖI)
     * Đây là nơi gây ra lỗi "unexpected '.'" (dòng 194)
     */
    public function searchTKB($namHoc_filter, $hocKy_filter, $maLop_filter, $maGV_filter) {
        $sql = "SELECT tkb.*, l.tenLop, mh.tenMon, gv.tenGV, p.tenPhong
                FROM tiet_tkb tkb
                LEFT JOIN lop l ON tkb.maLop = l.maLop
                LEFT JOIN monhoc mh ON tkb.maMon = mh.maMon
                LEFT JOIN giaovien gv ON tkb.maGV = gv.maGV
                LEFT JOIN phonghoc p ON tkb.maPhong = p.maPhong
                WHERE tkb.trangThai = 'active'";
        
        // Sử dụng array() cho PHP 5.3
        $params = array();
        $types = "";

        // Lọc theo Năm học và Học kỳ (BẮT BUỘC)
        $sql .= " AND tkb.namHoc = ? AND tkb.hocKy = ?";
        $params[] = $namHoc_filter;
        $params[] = $hocKy_filter;
        $types .= "ss";

        if (!empty($maLop_filter)) {
            $sql .= " AND tkb.maLop = ?";
            $params[] = $maLop_filter;
            $types .= "s";
        }
        if (!empty($maGV_filter)) {
            $sql .= " AND tkb.maGV = ?";
            $params[] = $maGV_filter;
            $types .= "s";
        }

        $sql .= " ORDER BY tkb.thu ASC, tkb.tietSo ASC";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($types)) {
            
            // === SỬA LỖI PHP < 5.6 (Lỗi '...') ===
            // Thay thế cho: $stmt->bind_param($types, ...$params);

            // 1. Tạo mảng tham chiếu (bind_param yêu cầu tham chiếu)
            $bind_params = array();
            $bind_params[] = $types; // Thêm $types là phần tử đầu tiên
            
            // Tạo tham chiếu cho từng biến trong $params
            for ($i = 0; $i < count($params); $i++) {
                $bind_params[] = &$params[$i];
            }

            // 2. Gọi bind_param bằng call_user_func_array
            call_user_func_array(array($stmt, 'bind_param'), $bind_params);
            
            // === KẾT THÚC SỬA LỖI ===
        }
        
        $stmt->execute();
        
        // === SỬA LỖI PHP < 5.4 (fetch_all / get_result) ===
        return $this->fetch_all_assoc($stmt);
    }

    /**
     * Lấy chi tiết 1 tiết TKB (để Sửa) (ĐÃ SỬA LỖI)
     */
    public function getTKBDetails($maTiet) {
        $sql = "SELECT * FROM tiet_tkb WHERE maTiet = ? AND trangThai = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maTiet);
        $stmt->execute();
        
        // Sửa lỗi get_result()
        return $this->fetch_assoc($stmt); 
    }
}
?>