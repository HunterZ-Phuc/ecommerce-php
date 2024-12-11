<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đơn hàng #<?= $order['id'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .total {
            text-align: right;
            margin-top: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>ĐƠN HÀNG #<?= $order['id'] ?></h2>
        <p>Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['createdAt'])) ?></p>
    </div>

    <div class="info-section">
        <h3>Thông tin khách hàng</h3>
        <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['fullName']) ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
    </div>

    <div class="info-section">
        <h3>Thông tin đơn hàng</h3>
        <p>
            <strong>Phương thức thanh toán:</strong> 
            <?= $order['paymentMethod'] === 'COD' ? 'Tiền mặt khi nhận hàng' : 'Chuyển khoản ngân hàng' ?>
        </p>
        <p>
            <strong>Trạng thái thanh toán:</strong>
            <?= $order['paymentStatus'] === 'PAID' ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
        </p>
        <p>
            <strong>Trạng thái đơn hàng:</strong>
            <?php
            switch ($order['orderStatus']) {
                case 'PENDING':
                    echo 'Chờ xác nhận';
                    break;
                case 'PROCESSING':
                    echo 'Đang xử lý';
                    break;
                case 'SHIPPING':
                    echo 'Đang giao';
                    break;
                case 'DELIVERED':
                    echo 'Đã giao';
                    break;
                case 'CANCELLED':
                    echo 'Đã hủy';
                    break;
            }
            ?>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <?= htmlspecialchars($item['productName']) ?><br>
                        <small><?= htmlspecialchars($item['variantName']) ?></small>
                    </td>
                    <td><?= number_format($item['price']) ?>đ</td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'] * $item['quantity']) ?>đ</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">
        <p><strong>Tạm tính:</strong> <?= number_format($order['totalAmount']) ?>đ</p>
        <p><strong>Phí vận chuyển:</strong> 0đ</p>
        <p><strong>T��ng cộng:</strong> <?= number_format($order['totalAmount']) ?>đ</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">In đơn hàng</button>
        <button onclick="window.close()">Đóng</button>
    </div>
</body>
</html> 