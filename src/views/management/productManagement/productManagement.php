<?php
require_once 'C:/xampp/htdocs/ecommerce-php/src/utils/db_connect.php';
require_once 'C:/xampp/htdocs/ecommerce-php/src/controllers/ProductController.php';

// Thêm dòng use namespace
use Controllers\ProductController;

// Khởi tạo ProductController
$productController = new ProductController($con);

// Xử lý các action
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $productController->addProduct($_POST);
            break;
        case 'edit':
            $productController->updateProduct($_POST);
            break;
        case 'delete':
            $productController->deleteProduct($_POST['id']);
            break;
        case 'add_variants':
            if (!isset($_POST['productId'])) {
                throw new Exception('Thiếu productId');
            }
            $productController->addProductVariants($_POST['productId'], $_POST);
            break;
    }
}

// Lấy danh sách sản phẩm
$products = $productController->getAllProducts();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản Lý Sản Phẩm</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .variant-type .card {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        .variant-type .card-body {
            padding: 1rem;
        }
        .variant-type label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản Lý Sản Phẩm</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="bi bi-plus-lg"></i> Thêm Sản Phẩm
        </button>
    </div>

    <!-- Bảng danh sách sản phẩm -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình Ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Danh Mục</th>
                    <th>Giá</th>
                    <th>Tồn Kho</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td>
                        <img src="<?= $product['thumbnail'] ?>" alt="<?= $product['productName'] ?>" 
                             class="img-thumbnail" style="width: 50px; height: 50px;">
                    </td>
                    <td><?= $product['productName'] ?></td>
                    <td><?= $product['category'] ?></td>
                    <td><?= number_format($product['price']) ?>đ</td>
                    <td><?= $product['stockQuantity'] ?></td>
                    <td>
                        <span class="badge <?= $product['status'] === 'ON_SALE' ? 'bg-success' : 'bg-danger' ?>">
                            <?= $product['status'] ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary me-1" onclick="editProduct(<?= $product['id'] ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?= $product['id'] ?>)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm/Sửa Sản Phẩm -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Sản Phẩm Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="productForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Tên Sản Phẩm</label>
                            <input type="text" name="productName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Danh Mục</label>
                            <select name="category" class="form-control" required>
                                <option value="FRUITS">Trái Cây</option>
                                <option value="VEGETABLES">Rau Củ</option>
                                <option value="GRAINS">Ngũ Cốc</option>
                                <option value="OTHERS">Khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Mô Tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div id="basicPricing" class="row mb-3">
                        <div class="col-md-4">
                            <label>Giá Gốc</label>
                            <input type="number" name="price" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label>Số Lượng</label>
                            <input type="number" name="stockQuantity" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label>Phần Trăm Giảm Giá</label>
                            <input type="number" name="salePercent" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="hasVariants" name="hasVariants">
                            <label class="form-check-label" for="hasVariants">
                                Sản phẩm có phân loại hàng
                            </label>
                        </div>
                    </div>

                    <div id="variantSection" style="display: none;">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Phân Loại Hàng</h6>
                                <div id="variantTypes">
                                    <!-- Các biến thể sẽ được thêm vào đây -->
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline-primary" onclick="addVariantType()">
                                        <i class="bi bi-plus-lg"></i> Thêm Phân Loại Hàng
                                    </button>
                                    <button type="button" class="btn btn-success ms-2" onclick="completeVariants()">
                                        <i class="bi bi-check-lg"></i> Hoàn Tất Phân Loại Hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Hình ảnh Sản Phẩm</label>
                        <input type="file" name="images[]" class="form-control" multiple>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="saveProduct()">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nhập Giá và Số Lượng cho Biến Thể -->
<div class="modal fade" id="variantPricingModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cập Nhật Giá và Số Lượng Biến Thể</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="variantPricingForm">
                    <input type="hidden" name="productId" id="variantProductId">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Biến Thể</th>
                                    <th>Giá</th>
                                    <th>Số Lượng</th>
                                    <th>Ảnh</th>
                                </tr>
                            </thead>
                            <tbody id="variantPricingTableBody">
                                <!-- Các hàng biến thể sẽ được thêm vào đây -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="saveVariantPricing()">Lưu</button>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
let variantTypeCount = 0;

// Xử lý hiển thị/ẩn phần biến thể
document.getElementById('hasVariants').addEventListener('change', function() {
    document.getElementById('variantSection').style.display = this.checked ? 'block' : 'none';
    document.getElementById('basicPricing').style.display = this.checked ? 'none' : 'block';
    // Reset form biến thể khi unchecked
    if (!this.checked) {
        document.getElementById('variantTypes').innerHTML = '';
        variantTypeCount = 0;
    }
});

function addVariantType() {
    variantTypeCount++;
    const variantTypeHtml = `
        <div class="variant-type mb-3" id="variantType_${variantTypeCount}">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Phân Loại ${variantTypeCount}</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                onclick="removeVariantType(${variantTypeCount})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Tên phân loại</label>
                            <input type="text" name="variant_names[]" class="form-control" 
                                   placeholder="VD: Size, Màu sắc..." required>
                        </div>
                        <div class="col-md-6">
                            <label>Các giá trị</label>
                            <input type="text" name="variant_values[]" class="form-control" 
                                   placeholder="VD: S,M,L hoặc Đỏ,Xanh..." required>
                            <small class="text-muted">Phân cách các giá trị bằng dấu phẩy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('variantTypes').insertAdjacentHTML('beforeend', variantTypeHtml);
}

function removeVariantType(id) {
    const element = document.getElementById(`variantType_${id}`);
    if (element) {
        element.remove();
    }
}

function generateVariantCombinations() {
    const variantTypes = [];
    document.querySelectorAll('.variant-type').forEach(type => {
        const name = type.querySelector('input[name="variant_names[]"]').value;
        const values = type.querySelector('input[name="variant_values[]"]').value.split(',').map(v => v.trim());
        variantTypes.push({ name, values });
    });

    // Hàm đệ quy để tạo tổ hợp
    function combine(arrays, current = [], index = 0) {
        if (index === arrays.length) {
            return [current];
        }
        const results = [];
        for (const value of arrays[index]) {
            results.push(...combine(arrays, [...current, value], index + 1));
        }
        return results;
    }

    const valueArrays = variantTypes.map(type => type.values);
    const combinations = combine(valueArrays);
    
    return combinations.map(combo => {
        return combo.map((value, index) => ({
            type: variantTypes[index].name,
            value: value
        }));
    });
}

function completeVariants() {
    // Kiểm tra xem đã có ít nhất 1 phân loại hàng chưa
    const variantTypes = document.querySelectorAll('.variant-type');
    if (variantTypes.length === 0) {
        alert('Vui lòng thêm ít nhất một phân loại hàng!');
        return;
    }

    // Kiểm tra các trường bắt buộc
    let isValid = true;
    variantTypes.forEach(type => {
        const name = type.querySelector('input[name="variant_names[]"]').value;
        const values = type.querySelector('input[name="variant_values[]"]').value;
        if (!name || !values) {
            isValid = false;
        }
    });

    if (!isValid) {
        alert('Vui lòng điền đầy đủ thông tin cho các phân loại hàng!');
        return;
    }

    // Hiển thị modal ngay lập tức mà không lưu sản phẩm
    showVariantPricingModal();
}

function showVariantPricingModal() {
    const combinations = generateVariantCombinations();
    const tbody = document.getElementById('variantPricingTableBody');
    tbody.innerHTML = '';

    combinations.forEach((combo, index) => {
        const tr = document.createElement('tr');
        const variantName = combo.map(v => `${v.type}: ${v.value}`).join(' - ');
        
        tr.innerHTML = `
            <td>${variantName}</td>
            <td>
                <input type="number" name="variant_prices[]" class="form-control" required min="0">
                <input type="hidden" name="variant_combinations[]" value="${JSON.stringify(combo)}">
            </td>
            <td>
                <input type="number" name="variant_quantities[]" class="form-control" required min="0">
            </td>
            <td>
                <input type="file" name="variant_images_${index}" class="form-control" accept="image/*">
            </td>
        `;
        tbody.appendChild(tr);
    });

    const modal = new bootstrap.Modal(document.getElementById('variantPricingModal'));
    modal.show();
}

async function saveVariantPricing() {
    const variantForm = document.getElementById('variantPricingForm');
    const formData = new FormData(variantForm);
    
    try {
        // Thêm thông tin biến thể vào formData
        document.querySelectorAll('input[type="file"][name^="variant_images_"]').forEach((input, index) => {
            if (input.files[0]) {
                formData.append(`variant_images_${index}`, input.files[0]);
            }
        });

        // Lưu biến thể
        const response = await fetch('?action=add_variants', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        // Nếu thành công, commit transaction
        await fetch('?action=commit_transaction', {
            method: 'POST'
        });
        alert('Lưu sản phẩm và biến thể thành công!');
        location.reload();
    } catch (error) {
        // Nếu có lỗi, rollback transaction
        await fetch('?action=rollback_transaction', {
            method: 'POST'
        });
        console.error('Error:', error);
        alert('Có lỗi xảy ra: ' + error.message);
    }
}

// Cập nhật hàm saveProduct để trả về Promise
async function saveProduct() {
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    const hasVariants = document.getElementById('hasVariants').checked;
    
    formData.append('hasVariants', hasVariants);
    
    try {
        // Bắt đầu transaction
        const response = await fetch('?action=start_transaction', {
            method: 'POST'
        });
        
        // Lưu thông tin sản phẩm cơ bản
        const productResponse = await fetch('?action=add_product', {
            method: 'POST',
            body: formData
        });
        const productData = await productResponse.json();
        
        if (!productData.success) {
            throw new Error(productData.message);
        }

        if (hasVariants) {
            // Nếu có biến thể, hiển thị modal để nhập thông tin biến thể
            showVariantPricingModal(productData.productId);
        } else {
            // Nếu không có biến thể, commit transaction
            await fetch('?action=commit_transaction', {
                method: 'POST'
            });
            alert('Lưu sản phẩm thành công!');
            location.reload();
        }
    } catch (error) {
        // Nếu có lỗi, rollback transaction
        await fetch('?action=rollback_transaction', {
            method: 'POST'
        });
        console.error('Error:', error);
        alert('Có lỗi xảy ra: ' + error.message);
    }
}
</script>

<style>
/* Thêm CSS để làm đẹp giao diện */
.variant-type {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.variant-type .card {
    box-shadow: none;
}

.variant-type .btn-outline-danger {
    padding: 0.25rem 0.5rem;
}

#variantPricingModal .table td {
    vertical-align: middle;
}

#variantPricingModal .form-control {
    margin-bottom: 0;
}
</style>

</body>
</html>
