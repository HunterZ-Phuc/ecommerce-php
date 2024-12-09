<?php
namespace App\Models;

class Admin extends BaseModel
{
    protected $table = 'admins';

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $data['eRole'] = 'ADMIN';
        $data['createdAt'] = date('Y-m-d H:i:s');
        return parent::create($data);
    }

    public function update($id, $data)
    {
        $data['updatedAt'] = date('Y-m-d H:i:s');
        return parent::update($id, $data);
    }
}