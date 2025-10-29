<?php
require_once __DIR__ . '/../Models/mMonHoc.php';


class cMonHoc {
    public function getAllSubjects() {
        $p = new mMonHoc();
        $tbl = $p->selectAllSubjects();
        return $tbl;
    }

    public function getAllSubjectsBySearch($search) {
        $p = new mMonHoc();
        $tbl = $p->selectAllSubjectsBySearch($search);
        return $tbl;
    }

    public function getSubjectById($id) {
        $p = new mMonHoc();
        $tbl = $p->selectSubjectById($id);
        return $tbl;
    }

    public function insertSubject($tenmh, $sotiet, $khoilop, $trangthai) {
        $p = new mMonHoc();
        $tbl = $p->insertSubject($tenmh, $sotiet, $khoilop, $trangthai);
        return $tbl;
    }

    public function updateSubject($mamh, $tenmh, $sotiet, $khoilop, $trangthai) {
        $p = new mMonHoc();
        $tbl = $p->updateSubject($mamh, $tenmh, $sotiet, $khoilop, $trangthai);
        return $tbl;
    }

    public function deleteSubject($mamh) {
        $p = new mMonHoc();
        $tbl = $p->deleteSubject($mamh);
        return $tbl;
    }

    public function checkDuplicateSubject($tenmh, $exclude_id = null) {
        $p = new clsKetNoi();
        $conn = $p->moKetNoi();
        $tenmh = mysqli_real_escape_string($conn, $tenmh);

        if ($exclude_id) {
            // Khi sửa, loại trừ môn học hiện tại ra khỏi kiểm tra
            $sql = "SELECT * FROM monhoc WHERE tenmh = '$tenmh' AND mamh != '$exclude_id'";
        } else {
            // Khi thêm
            $sql = "SELECT * FROM monhoc WHERE tenmh = '$tenmh'";
        }

        $result = $conn->query($sql);
        return ($result && $result->num_rows > 0);
    }
}
?>
