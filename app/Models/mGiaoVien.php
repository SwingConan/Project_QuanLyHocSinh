<?php
if (!class_exists('mGiaoVien')) {
    include_once __DIR__ . '/../../config/ketnoi.php';

    class mGiaoVien
    {
        private $conn;
        public function __construct()
        {
            $p = new clsKetNoi();
            $this->conn = $p->moKetNoi();
            mysqli_set_charset($this->conn, "utf8mb4");
        }
        private function q($sql)
        {
            return mysqli_query($this->conn, $sql);
        }

        // ===== LIST + JOIN tên môn
        public function selectAllTeachersWithSubjects()
        {
            $sql = "SELECT g.*, GROUP_CONCAT(m.tenmh ORDER BY m.tenmh SEPARATOR ', ') AS dsmon
                    FROM giaovien g
                    LEFT JOIN giaovien_monhoc gm ON g.magv = gm.magv
                    LEFT JOIN monhoc m ON gm.mamh = m.mamh
                    GROUP BY g.magv
                    ORDER BY g.magv DESC";
            return $this->q($sql);
        }
        public function searchTeachersWithSubjects($kw)
        {
            $kw = mysqli_real_escape_string($this->conn, $kw);
            $sql = "SELECT g.*, GROUP_CONCAT(m.tenmh ORDER BY m.tenmh SEPARATOR ', ') AS dsmon
            FROM giaovien g
            LEFT JOIN giaovien_monhoc gm ON g.magv = gm.magv
            LEFT JOIN monhoc m ON gm.mamh = m.mamh
            WHERE g.hoten LIKE '%$kw%'
               OR g.email LIKE '%$kw%'
               OR g.dienthoai LIKE '%$kw%'
               OR g.cmndcccd LIKE '%$kw%'
               OR m.tenmh LIKE '%$kw%'
            GROUP BY g.magv
            ORDER BY g.magv DESC";
            return $this->q($sql);
        }

        public function getSubjectIdsByTeacher($magv)
        {
            $magv = intval($magv);
            $res = $this->q("SELECT mamh FROM giaovien_monhoc WHERE magv=$magv");
            $ids = [];
            if ($res) while ($r = mysqli_fetch_assoc($res)) $ids[] = (int)$r['mamh'];
            return $ids;
        }
        private function replaceTeacherSubjects($magv, $monhoc_ids)
        {
            $magv = intval($magv);
            if (!$this->q("DELETE FROM giaovien_monhoc WHERE magv=$magv")) return mysqli_error($this->conn);
            if (is_array($monhoc_ids)) {
                foreach ($monhoc_ids as $mamh) {
                    $mamh = intval($mamh);
                    if (!$this->q("INSERT INTO giaovien_monhoc(magv,mamh) VALUES ($magv,$mamh)"))
                        return mysqli_error($this->conn);
                }
            }
            return true;
        }
        // ===== trùng lặp
        public function checkDuplicate($email, $phone, $cmnd, $excludeId = null)
        {
            $email = mysqli_real_escape_string($this->conn, $email);
            $phone = mysqli_real_escape_string($this->conn, $phone);
            $cmnd  = mysqli_real_escape_string($this->conn, $cmnd);
            $cond  = $excludeId ? "AND magv<>" . intval($excludeId) : "";
            $sql = "SELECT email,dienthoai,cmndcccd FROM giaovien
                  WHERE (email='$email' OR dienthoai='$phone' OR cmndcccd='$cmnd') $cond LIMIT 1";
            $res = $this->q($sql);
            if ($res && mysqli_num_rows($res) > 0) {
                $r = mysqli_fetch_assoc($res);
                if ($r['email'] === $email) return "Email đã tồn tại";
                if ($r['dienthoai'] === $phone) return "Số điện thoại đã tồn tại";
                if ($r['cmndcccd'] === $cmnd) return "CMND/CCCD đã tồn tại";
            }
            return null;
        }

        // ===== INSERT
        public function insertTeacher($hoten, $ngaysinh, $gioitinh, $cmnd, $diachi, $dienthoai, $email, $trinhdo, $trangthai, $monhoc_ids = [])
        {
            if ($dup = $this->checkDuplicate($email, $dienthoai, $cmnd)) return $dup;

            $hoten = mysqli_real_escape_string($this->conn, $hoten);
            $ngaysinh = mysqli_real_escape_string($this->conn, $ngaysinh);
            $gioitinh = mysqli_real_escape_string($this->conn, $gioitinh);
            $cmnd = mysqli_real_escape_string($this->conn, $cmnd);
            $diachi = mysqli_real_escape_string($this->conn, $diachi);
            $dienthoai = mysqli_real_escape_string($this->conn, $dienthoai);
            $email = mysqli_real_escape_string($this->conn, $email);
            $trinhdo = mysqli_real_escape_string($this->conn, $trinhdo);
            $trangthai = mysqli_real_escape_string($this->conn, $trangthai);

            $this->q("START TRANSACTION");
            $sql = "INSERT INTO giaovien(hoten,ngaysinh,gioitinh,cmndcccd,diachi,dienthoai,email,trinhdo,trangthai)
                  VALUES('$hoten','$ngaysinh','$gioitinh','$cmnd','$diachi','$dienthoai','$email','$trinhdo','$trangthai')";
            if (!$this->q($sql)) {
                $e = mysqli_error($this->conn);
                $this->q("ROLLBACK");
                return $e;
            }

            $newId = mysqli_insert_id($this->conn);
            $rel = $this->replaceTeacherSubjects($newId, $monhoc_ids);
            if ($rel !== true) {
                $this->q("ROLLBACK");
                return $rel;
            }

            $this->q("COMMIT");
            return true;
        }

        // ===== UPDATE
        public function updateTeacher($magv, $hoten, $ngaysinh, $gioitinh, $cmnd, $diachi, $dienthoai, $email, $trinhdo, $trangthai, $monhoc_ids = [])
        {
            $magv = intval($magv);
            if ($dup = $this->checkDuplicate($email, $dienthoai, $cmnd, $magv)) return $dup;

            $hoten = mysqli_real_escape_string($this->conn, $hoten);
            $ngaysinh = mysqli_real_escape_string($this->conn, $ngaysinh);
            $gioitinh = mysqli_real_escape_string($this->conn, $gioitinh);
            $cmnd = mysqli_real_escape_string($this->conn, $cmnd);
            $diachi = mysqli_real_escape_string($this->conn, $diachi);
            $dienthoai = mysqli_real_escape_string($this->conn, $dienthoai);
            $email = mysqli_real_escape_string($this->conn, $email);
            $trinhdo = mysqli_real_escape_string($this->conn, $trinhdo);
            $trangthai = mysqli_real_escape_string($this->conn, $trangthai);

            $this->q("START TRANSACTION");
            $sql = "UPDATE giaovien SET
                    hoten='$hoten', ngaysinh='$ngaysinh', gioitinh='$gioitinh',
                    cmndcccd='$cmnd', diachi='$diachi', dienthoai='$dienthoai',
                    email='$email', trinhdo='$trinhdo', trangthai='$trangthai'
                  WHERE magv=$magv";
            if (!$this->q($sql)) {
                $e = mysqli_error($this->conn);
                $this->q("ROLLBACK");
                return $e;
            }

            $rel = $this->replaceTeacherSubjects($magv, $monhoc_ids);
            if ($rel !== true) {
                $this->q("ROLLBACK");
                return $rel;
            }

            $this->q("COMMIT");
            return true;
        }

        public function deleteTeacher($magv)
        {
            $magv = intval($magv);
            return $this->q("DELETE FROM giaovien WHERE magv=$magv");
        }
    }
}
