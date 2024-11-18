class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function sanitizeInput($input) {
        return htmlspecialchars(strip_tags($input));
    }
    
    public static function validatePassword($password) {
        // Kiểm tra độ mạnh của mật khẩu
        return strlen($password) >= 8 && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
}
