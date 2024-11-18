<?php
namespace Models;

class Address {
    private $id;
    private $userId;
    private $fullName;
    private $phoneNumber;
    private $address;
    private $isDefault;
    private $createdAt;
    private $updatedAt;
    
    public function __construct(
        int $userId,
        string $fullName,
        string $phoneNumber, 
        string $address,
        bool $isDefault = false
    ) {
        $this->userId = $userId;
        $this->fullName = $fullName;
        $this->phoneNumber = $phoneNumber;
        $this->address = $address;
        $this->isDefault = $isDefault;
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'fullName' => $this->fullName,
            'phoneNumber' => $this->phoneNumber,
            'address' => $this->address,
            'isDefault' => $this->isDefault,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getUserId(): int {
        return $this->userId;
    }
    
    public function getFullName(): string {
        return $this->fullName;
    }
    
    public function getPhoneNumber(): string {
        return $this->phoneNumber;
    }
    
    public function getAddress(): string {
        return $this->address;
    }
    
    public function getIsDefault(): bool {
        return $this->isDefault;
    }
    
    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?string {
        return $this->updatedAt;
    }
}
