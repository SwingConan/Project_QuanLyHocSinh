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
}
?>
