<?php
if (!class_exists('clsKetNoi')) {
    class clsKetNoi {

        public function moKetNoi()
        {
            $local = "localhost";
            $user = "root";
            $pass = "";
            $db = "quanlytruonghoc";
            return mysqli_connect($local, $user, $pass, $db);
        }

        public function dongKetNoi($conn)
        {
            $conn->close();
        }
    }
}
?>
