<?php
error_log("View received product data: " . json_encode($product));
?>

<div class="container mt-4">
    <div class="row">
        <!-- Ảnh sản phẩm -->
        <div class="col-md-4">
            <div class="card border-0">
                <!-- Hiển thị ảnh chính -->
                <div class="main-image-container">
                    <img id="mainImage" src="<?= '/ecommerce-php/public' . $product['mainImage'] ?>"
                        class="card-img-top rounded img-fluid" alt="<?= htmlspecialchars($product['productName']) ?>">
                </div>

                <!-- Thumbnails -->
                <div class="variant-image-container d-flex gap-2 mt-3">
                    <?php if (!empty($product['images'])): ?>
                        <?php foreach ($product['images'] as $image): ?>
                            <img src="<?= '/ecommerce-php/public' . $image['imageUrl'] ?>" class="img-thumbnail"
                                style="width: 80px; height: 80px; object-fit: cover;" alt="Product thumbnail">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-8">
            <h2 class="mb-3"><?= $product['productName'] ?></h2>

            <!-- Giá và số lượng -->
            <div class="mb-3">
                <h4 id="variantPrice" class="text-primary">Giá: Vui lòng chọn biến thể</h4>
                <p id="variantQuantity" class="text-secondary">Số lượng còn lại: Vui lòng chọn biến thể</p>
            </div>

            <!-- Phân loại sản phẩm -->
            <div class="variant-selection mb-4">
                <h4>Phân loại sản phẩm</h4>
                <?php
                // Lấy danh sách các combinations và gộp các giá trị trùng nhau
                $groupedVariants = [];
                if (!empty($product['variants'])) {
                    foreach ($product['variants'] as $variant) {
                        foreach ($variant['combinations'] as $combination) {
                            $typeName = $combination['typeName'];
                            $value = $combination['value'];
                            if (!isset($groupedVariants[$typeName])) {
                                $groupedVariants[$typeName] = [];
                            }
                            if (!in_array($value, $groupedVariants[$typeName])) {
                                $groupedVariants[$typeName][] = $value;
                            }
                        }
                    }
                }
                ?>
                <div class="variant-selection mb-4">
                    <?php foreach ($groupedVariants as $typeName => $values): ?>
                        <div class="mb-3">
                            <label class="form-label"><?= $typeName ?>:</label>
                            <div class="d-flex flex-wrap">
                                <?php foreach ($values as $value): ?>
                                    <button class="btn btn-outline-secondary variant-option me-2 mb-2"
                                        data-type="<?= htmlspecialchars($typeName) ?>"
                                        data-value="<?= htmlspecialchars($value) ?>">
                                        <?= htmlspecialchars($value) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Chọn số lượng -->
            <div class="mb-3">
                <label for="quantity" class="form-label">Số lượng:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control w-25">
            </div>

            <!-- Mua ngay -->
            <button class="btn btn-primary me-2">Mua ngay</button>
            <!-- Nút thêm vào giỏ hàng -->
            <button id="addToCartBtn" class="btn btn-primary" disabled>
                Thêm vào giỏ hàng
            </button>
        </div>
    </div>
    <p class="text-muted mt-4"><?= $product['description'] ?></p>
</div>

<script>
    const variants = <?= json_encode($product['variants']) ?>;
    const images = <?= json_encode($product['images']) ?>;
    const priceElement = document.getElementById('variantPrice');
    const quantityElement = document.getElementById('variantQuantity');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const mainImage = document.getElementById('mainImage');

    let selectedOptions = {};

    document.querySelectorAll('.variant-option').forEach(button => {
        button.addEventListener('click', function () {
            const type = this.getAttribute('data-type');
            const value = this.getAttribute('data-value');

            // Cập nhật lựa chọn
            selectedOptions[type] = value;

            // Làm nổi bật nút được chọn
            document.querySelectorAll(`[data-type="${type}"]`).forEach(btn => btn.classList.remove('btn-secondary'));
            this.classList.add('btn-secondary');

            // Tìm biến thể phù hợp
            const matchedVariant = variants.find(variant => {
                return variant.combinations.every(combination => {
                    return selectedOptions[combination.typeName] === combination.value;
                });
            });

            if (matchedVariant) {
                // Cập nhật giá và số lượng
                priceElement.textContent = `Giá: ${matchedVariant.price} VND`;
                quantityElement.textContent = `Số lượng còn lại: ${matchedVariant.quantity}`;
                addToCartBtn.disabled = false;

                // Cập nhật ảnh chính dựa trên `variantId`
                const variantImage = images.find(image => image.variantId == matchedVariant.id);
                if (variantImage) {
                    mainImage.src = '/ecommerce-php/public' + variantImage.imageUrl;
                }
            } else {
                priceElement.textContent = 'Giá: Không có biến thể phù hợp';
                quantityElement.textContent = 'Số lượng còn lại: Không có biến thể phù hợp';
                addToCartBtn.disabled = true;

                // Trả về ảnh mặc định nếu không có biến thể phù hợp
                mainImage.src = '/ecommerce-php/public' + <?= json_encode($product['mainImage']) ?>;
            }
        });
    });

    // Hiển thị mặc định giá, số lượng và ảnh của biến thể đầu tiên
    if (variants.length > 0) {
        const defaultVariant = variants[0];
        priceElement.textContent = `Giá: ${defaultVariant.price} VND`;
        quantityElement.textContent = `Số lượng còn lại: ${defaultVariant.quantity}`;
        addToCartBtn.disabled = false;

        const defaultImage = images.find(image => image.variantId == defaultVariant.id);
        if (defaultImage) {
            mainImage.src = '/ecommerce-php/public' + defaultImage.imageUrl;
        }
    }
</script>



<style>
    .variant-option {
        margin: 0 5px;
        min-width: 60px;
        transition: all 0.2s;
    }

    .variant-option:not(:disabled):hover {
        background-color: #6c757d;
        color: white;
    }

    .variant-option.btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .opacity-50 {
        opacity: 0.5;
    }

    .img-thumbnail {
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .img-thumbnail:hover {
        opacity: 0.8;
    }

    .main-image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-width: 300px;
        max-width: 500px;
        margin: 0 auto;
    }

    .main-image-container img {
        width: 420px;
        height: 420px;
        object-fit: cover;
    }

    .variant-image-container {
        display: flex;
        justify-content: center;
    }
</style>