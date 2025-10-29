<?php
if (!class_exists('cGiaoVien')) {
    include_once __DIR__ . '/../Models/mGiaoVien.php';

    class cGiaoVien
    {
        public function getAllTeachers()
        {
            $m = new mGiaoVien();
            return $m->selectAllTeachersWithSubjects(); // có GROUP_CONCAT dsmon
        }
        public function getSubjectIdsByTeacher($magv)
        {
            $m = new mGiaoVien();
            return $m->getSubjectIdsByTeacher($magv);
        }
        public function insertTeacher($hoten, $ngaysinh, $gioitinh, $cmnd, $diachi, $dienthoai, $email, $trinhdo, $trangthai, $monhoc_ids = [])
        {
            $m = new mGiaoVien();
            return $m->insertTeacher($hoten, $ngaysinh, $gioitinh, $cmnd, $diachi, $dienthoai, $email, $trinhdo, $trangthai, $monhoc_ids);
        }
        public function updateTeacher($magv, $hoten, $ngaysinh, $gioitinh, $cmnd, $diachi, $dienthoai, $email, $trinhdo, $trangthai, $monhoc_ids = [])
        {
            $m = new mGiaoVien();
            return $m->updateTeacher($magv, $hoten, $ngaysinh, $gioitinh, $cmnd, $diachi, $dienthoai, $email, $trinhdo, $trangthai, $monhoc_ids);
        }
        public function deleteTeacher($magv)
        {
            $m = new mGiaoVien();
            return $m->deleteTeacher($magv);
        }
        public function getAllTeachersBySearch($kw)
        {
            $m = new mGiaoVien();
            return $m->searchTeachersWithSubjects($kw);
        }
    }
}
