<?php
require_once __DIR__ . '/../../config/ketnoi.php';

class mXemDiem
{
    public function layDanhSachKyHoc($maHS)
    {
        $p = new clsKetNoi();
        $conn = $p->moKetNoi();
        $str = "SELECT DISTINCT kyhoc, namhoc FROM diem WHERE mahs = '$maHS'";
        $ketqua = $conn->query($str);
        return $ketqua;
    }

    public function layBangDiemTheoKy($maHS, $kyHoc, $namHoc)
    {
        $p = new clsKetNoi();
        $conn = $p->moKetNoi();
        $str = "SELECT mon, diemMieng, diem15p, diem1Tiet, giuaky, cuoiky, tbmon
                FROM diem 
                WHERE mahs = '$maHS' AND kyhoc = '$kyHoc' AND namhoc = '$namHoc'";
        $ketqua = $conn->query($str);
        return $ketqua;
    }
}
