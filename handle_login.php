<?php
session_start();
include_once("config/ketnoi.php"); // Nạp file kết nối CSDL

// Chỉ xử lý nếu là phương thức POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Chuẩn bị truy vấn CSDL
    $sql = "SELECT password_hash, role FROM taikhoan_admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        $_SESSION['login_error'] = "Lỗi hệ thống: Không thể chuẩn bị truy vấn.";
        header('Location: login.php');
        exit();
    }
    
    $stmt->bind_param("s", $username);
    
    // 2. Thực thi và lấy kết quả
    if ($stmt->execute()) {
        $stmt->store_result(); // Cần thiết cho num_rows và bind_result
        
        // 3. Kiểm tra xem có tìm thấy User không
        if ($stmt->num_rows == 1) {
            
            // Gán kết quả vào các biến
            $stmt->bind_result($db_hash, $db_role);
            $stmt->fetch();
            
            // 4. Xác thực mật khẩu
            if (password_verify($password, $db_hash)) {
                // Đăng nhập thành công!
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $db_role; // (VD: "Admin")
                
                $stmt->close();
                header('Location: index.php?act=trangchu'); // Chuyển đến trang chủ
                exit();
                
            } else {
                // Sai mật khẩu
                $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không chính xác.";
            }
        } else {
            // Không tìm thấy user
            $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không chính xác.";
        }
    } else {
        // Lỗi thực thi SQL
        $_SESSION['login_error'] = "Lỗi hệ thống khi thực thi.";
    }

    $stmt->close();
    
} else {
    // Không phải POST
    $_SESSION['login_error'] = "Phương thức không hợp lệ.";
}

// Nếu có bất kỳ lỗi nào, quay lại trang login
header('Location: login.php');
exit();
?>