<?php
require_once __DIR__ . '/../Models/mXemDiemGV.php';

class cXemDiemGV {
    public function hienThiDanhSach() {
        $model = new mXemDiemGV();
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'list';
        $message = $messageType = '';
        
        // Xử lý form thêm điểm
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            $data = array(
                'mahs' => isset($_POST['mahs']) ? trim($_POST['mahs']) : '',
                'tenhs' => isset($_POST['tenhs']) ? trim($_POST['tenhs']) : '',
                'lop' => isset($_POST['lop']) ? trim($_POST['lop']) : '',
                'namhoc' => isset($_POST['namhoc']) ? trim($_POST['namhoc']) : '',
                'hocky' => isset($_POST['hocky']) ? trim($_POST['hocky']) : '',
                'monhoc' => isset($_POST['monhoc']) ? trim($_POST['monhoc']) : '',
                'diem_mieng' => isset($_POST['diem_mieng']) ? floatval($_POST['diem_mieng']) : 0,
                'diem_15p' => isset($_POST['diem_15p']) ? floatval($_POST['diem_15p']) : 0,
                'diem_1tiet' => isset($_POST['diem_1tiet']) ? floatval($_POST['diem_1tiet']) : 0,
                'diem_giua_ky' => isset($_POST['diem_giua_ky']) ? floatval($_POST['diem_giua_ky']) : 0,
                'diem_cuoi_ky' => isset($_POST['diem_cuoi_ky']) ? floatval($_POST['diem_cuoi_ky']) : 0,
                'ghichu' => isset($_POST['ghichu']) ? trim($_POST['ghichu']) : ''
            );
            
            $required = array($data['mahs'], $data['tenhs'], $data['lop'], $data['namhoc'], $data['hocky'], $data['monhoc']);
            $hasEmpty = false;
            foreach ($required as $val) {
                if (empty($val)) {
                    $hasEmpty = true;
                    break;
                }
            }
            
            if ($hasEmpty) {
                $message = 'Vui lòng điền đầy đủ thông tin!';
                $messageType = 'danger';
            } elseif ($model->themDiemHocSinh($data)) {
                $message = 'Thêm điểm thành công!';
                $messageType = 'success';
                $_POST = array();
            } else {
                $message = 'Lỗi khi thêm điểm!';
                $messageType = 'danger';
            }
        }
        
        // Lấy dữ liệu
        if ($mode === 'detail' && isset($_GET['mahs'])) {
            $thongTin = $model->layThongTinHocSinh($_GET['mahs']);
            $dsDiem = $model->layDiemTheoHocSinh($_GET['mahs']);
        } else {
            $dsHocSinh = $model->layDanhSachHocSinh();
            if (is_array($dsHocSinh) && count($dsHocSinh) > 0) {
                $combined = array_combine(
                    array_map(create_function('$r', 'return $r["mahs"];'), $dsHocSinh),
                    $dsHocSinh
                );
                $dsHocSinh = array_values($combined);
            }
        }
        
    include __DIR__ . '/../Views/xemdiemGV.php';
    }
}
?>
