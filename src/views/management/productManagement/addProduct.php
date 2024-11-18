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
                    <input type="hidden" name="action" value="add_product">
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

                    <div class="mb-3">
                        <label>Xuất xứ</label>
                        <input type="text" name="origin" class="form-control" value="Việt Nam" required>
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
                <button type="button" class="btn btn-primary" onclick="saveProduct()">Lưu sản phẩm</button>
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
                        <table class="table table-bordered" id="variantPricingTable">
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
                <button type="button" class="btn btn-primary" onclick="saveVariantPricing()">Lưu biến thể</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>