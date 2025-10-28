<?php
require_once __DIR__ . '/../../config/ketnoi.php';


class mMonHoc
{
    public function selectAllSubjects()
    {
        $p = new clsKetNoi();
        $str = "SELECT * FROM monhoc ORDER BY mamh ASC";
        $conn = $p->moKetNoi();
        $ketqua = $conn->query($str);
        return $ketqua;
    }

    public function selectAllSubjectsBySearch($search)
    {
        $p = new clsKetNoi();
        $str = "SELECT * FROM monhoc WHERE mamh LIKE '%" . $search . "%' OR tenmh LIKE '%" . $search . "%'";
        $conn = $p->moKetNoi();
        $ketqua = $conn->query($str);
        return $ketqua;
    }

    public function selectSubjectById($id)
    {
        $p = new clsKetNoi();
        $str = "SELECT * FROM monhoc WHERE mamh = '" . $id . "'";
        $conn = $p->moKetNoi();
        $ketqua = $conn->query($str);
        return $ketqua;
    }

    public function insertSubject($tenmh, $sotiet, $khoilop, $trangthai)
    {
        $p = new clsKetNoi();
        $str = "INSERT INTO monhoc (tenmh, sotiet, khoilop, trangthai) VALUES ('" . $tenmh . "', '" . $sotiet . "', '" . $khoilop . "', '" . $trangthai . "')";
        $conn = $p->moKetNoi();
        $ketqua = $conn->query($str);
        return $ketqua;
    }

    public function updateSubject($mamh, $tenmh, $sotiet, $khoilop, $trangthai)
    {
        $p = new clsKetNoi();
        $str = "UPDATE monhoc SET tenmh = '" . $tenmh . "', sotiet = '" . $sotiet . "', khoilop = '" . $khoilop . "', trangthai = '" . $trangthai . "' WHERE mamh = '" . $mamh . "'";
        $conn = $p->moKetNoi();
        $ketqua = $conn->query($str);
        return $ketqua;
    }

    public function deleteSubject($mamh)
    {
        $p = new clsKetNoi();
        $str = "DELETE FROM monhoc WHERE mamh = '" . $mamh . "'";
        $conn = $p->moKetNoi();
        $ketqua = $conn->query($str);
        return $ketqua;
    }
}
