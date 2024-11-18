class Payment {
    private $id;
    private $amount;
    private $paymentMethod;
    private $qrImage;
    private $refNo;
    private $paymentStatus;
    private $paymentDate;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->paymentMethod = $data['paymentMethod'] ?? null;
        $this->qrImage = $data['qrImage'] ?? null;
        $this->refNo = $data['refNo'] ?? null;
        $this->paymentStatus = $data['paymentStatus'] ?? 'PENDING';
        $this->paymentDate = $data['paymentDate'] ?? null;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }
}
