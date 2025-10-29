<?php
if (!class_exists('clsKetNoi')) {
    class clsKetNoi {

        public function moKetNoi()
        {
            $local = "localhost";
            $user = "root";
            $pass = "";
            $db = "quanlytruonghoc";
            $conn = mysqli_connect($local, $user, $pass, $db);
            // Đặt charset kết nối sang UTF-8 để tránh lỗi mã hóa khi lấy dữ liệu
            if ($conn) {
                // ưu tiên utf8mb4, fallback sang utf8 nếu server không hỗ trợ
                if (!mysqli_set_charset($conn, 'utf8mb4')) {
                    mysqli_set_charset($conn, 'utf8');
                }
            }
            return $conn;
        }

        public function dongKetNoi($conn)
        {
            $conn->close();
        }
    }
}
?>
