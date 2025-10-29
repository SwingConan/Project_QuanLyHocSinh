<?php
require_once __DIR__ . '/../../config/ketnoi.php';

class mXemDiemGV {
    private function query($sql) {
        $p = new clsKetNoi();
        $conn = $p->moKetNoi();
        $result = $conn->query($sql);
        $rows = ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
        if ($result) $result->free();
        $conn->close();
        return $rows;
    }
    
    public function layDanhSachHocSinh() {
        return $this->query("SELECT DISTINCT mahs, tenhs, lop FROM bangdiem 
                            WHERE mahs IS NOT NULL AND mahs != '' ORDER BY mahs ASC");
    }

    public function layDiemTheoHocSinh($mahs) {
        $p = new clsKetNoi();
        $conn = $p->moKetNoi();
        $mahs = mysqli_real_escape_string($conn, $mahs);
        $rows = $this->query("SELECT * FROM bangdiem WHERE mahs = '$mahs' ORDER BY monhoc ASC, id DESC");
        $conn->close();
        
        $seen = $result = [];
        foreach ($rows as $row) {
            if (!isset($seen[$row['monhoc']])) {
                $result[] = $row;
                $seen[$row['monhoc']] = true;
            }
        }
        return $result;
    }

    public function layThongTinHocSinh($mahs) {
        $p = new clsKetNoi();
        $conn = $p->moKetNoi();
        $mahs = mysqli_real_escape_string($conn, $mahs);
        $rows = $this->query("SELECT DISTINCT mahs, tenhs, lop FROM bangdiem WHERE mahs = '$mahs' LIMIT 1");
        $conn->close();
        return isset($rows[0]) ? $rows[0] : null;
    }

    public function themDiemHocSinh($data) {
        $p = new clsKetNoi();
        $conn = $p->moKetNoi();
        
        foreach ($data as &$val) {
            $val = mysqli_real_escape_string($conn, $val);
        }
        // Tính điểm trung bình sử dụng điểm cuối kỳ làm trọng số (giữ tương thích với công thức cũ)
        $tb = round(($data['diem_mieng'] + $data['diem_15p'] + $data['diem_1tiet']*2 + $data['diem_cuoi_ky']*3) / 7, 1);

        // Insert theo schema mới: diem_giua_ky, diem_cuoi_ky, diemtb
        $sql = "INSERT INTO bangdiem (mahs, tenhs, lop, namhoc, hocky, monhoc, diem_mieng, diem_15p, diem_1tiet, diem_giua_ky, diem_cuoi_ky, diemtb, ghichu)
                VALUES ('{$data['mahs']}', '{$data['tenhs']}', '{$data['lop']}', '{$data['namhoc']}', '{$data['hocky']}', '{$data['monhoc']}', 
                {$data['diem_mieng']}, {$data['diem_15p']}, {$data['diem_1tiet']}, {$data['diem_giua_ky']}, {$data['diem_cuoi_ky']}, $tb, '{$data['ghichu']}')";
        
        $result = $conn->query($sql);
        $conn->close();
        return $result;
    }
}
?>
