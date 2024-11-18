class User {
    private $id;
    private $avatar;
    private $username;
    private $fullName; 
    private $dateOfBirth;
    private $sex;
    private $phone;
    private $email;
    private $password;
    private $eRole;
    private $createdAt;
    private $updatedAt;
    private $addresses = []; // Quan hệ 1-n với Address

    // Constructor
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->avatar = $data['avatar'] ?? '';
        $this->username = $data['username'] ?? '';
        $this->fullName = $data['fullName'] ?? '';
        $this->dateOfBirth = $data['dateOfBirth'] ?? null;
        $this->sex = $data['sex'] ?? null;
        $this->phone = $data['phone'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->eRole = $data['eRole'] ?? 'USER';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters & Setters
    public function getId() { return $this->id; }
    public function getAvatar() { return $this->avatar; }
    public function setAvatar($avatar) { $this->avatar = $avatar; }
    // ... các getter/setter khác

    // Phương thức kiểm tra mật khẩu
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    // Phương thức cập nhật mật khẩu
    public function updatePassword($newPassword) {
        $this->password = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    // Phương thức thêm địa chỉ
    public function addAddress(Address $address) {
        $this->addresses[] = $address;
    }
}
