class AuthService {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    public function login($username, $password) {
        try {
            $user = $this->db->findOne('users', ['username' => $username]);
            
            if (!$user || !$user->verifyPassword($password)) {
                throw new Exception("Thông tin đăng nhập không chính xác");
            }
            
            return $this->generateToken($user);
            
        } catch (Exception $e) {
            throw new Exception("Lỗi đăng nhập: " . $e->getMessage());
        }
    }
    
    private function generateToken($user) {
        // Logic tạo JWT token
    }
} 