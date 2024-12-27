<?php
$mauSac = array("Đỏ", "Xanh", "Tím", "Vàng", "Chín", "Khô", "Khác");
$phoiKho = array("Phơi khô", "Xào tay");
$trongLuong = array("1g", "2g", "5g", "10g", "100g", "250g", "500g", "1Kg", "2kg", "5Kg", "10Kg", "Khác");
$combo = array("lẻ", "combo");
?>

<div id="variantSection">
    <div class="variant-types mb-4">
        <h6>Thông tin phân loại hàng</h6>

        <div id="variantTypesContainer">
            <div class="variant-type-group mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <select name="variant_types[]" class="form-control variant-type" required>
                        <option value="">Chọn phân loại</option>
                        <option value="Màu sắc">Màu sắc</option>
                        <option value="Phơi khô">Phơi khô</option>
                        <option value="Trọng lượng">Trọng lượng</option>
                        <option value="Combo">Combo</option>
                    </select>
                    <button type="button" class="btn btn-danger btn-sm remove-variant-type">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="variant-values-container">
                    <div class="variant-value d-flex align-items-center gap-2 mb-2">
                        <select name="variant_values[0][]" class="form-control" required>
                            <option value="">Chọn giá trị</option>
                            // Kiểm tra xem variant_types[] có giá trị nào thì hiển thị mảng giá trị tương ứng
                            <option value="" disabled>Chọn phân loại trước</option>
                        </select>
                        <button type="button" class="btn btn-danger btn-sm remove-value">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-secondary btn-sm add-value">
                    <i class="bi bi-plus"></i> Thêm giá trị
                </button>
            </div>
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addVariantType">
            <i class="bi bi-plus"></i> Thêm phân loại hàng
        </button>

        <button type="button" class="btn btn-primary mt-3" id="generateCombinations">
            Hoàn tất phân loại hàng
        </button>
    </div>
</div>

<div class="modal fade" id="variantDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nhập thông tin biến thể</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr id="variantTableHeader">
                            </tr>
                        </thead>
                        <tbody id="variantCombinations">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Quay lại chỉnh sửa phân loại
                </button>
                <button type="button" class="btn btn-primary" id="confirmVariants">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const variantTypesContainer = document.getElementById('variantTypesContainer');
        const variantModal = new bootstrap.Modal(document.getElementById('variantDetailsModal'));

        // Thêm xử lý cho nút "Xác nhận"
        document.getElementById('confirmVariants').addEventListener('click', function () {
            // Đóng modal biến thể
            variantModal.hide();

            // Xóa backdrop nếu còn
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });

        // Mảng giá trị tương ứng với từng loại phân loại
        const variantValuesMap = {
            "Màu sắc": <?= json_encode($mauSac) ?>,
            "Phơi khô": <?= json_encode($phoiKho) ?>,
            "Trọng lượng": <?= json_encode($trongLuong) ?>,
            "Combo": <?= json_encode($combo) ?>
        };

        // Cập nhật giá trị phân loại khi thay đổi phân loại hàng
        variantTypesContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('variant-type')) {
                const selectedType = e.target.value;
                const valueSelect = e.target.closest('.variant-type-group').querySelector('.variant-value select');

                // Xóa tất cả các tùy chọn hiện tại
                valueSelect.innerHTML = '<option value="">Chọn giá trị</option>';

                // Thêm các giá trị tương ứng
                if (variantValuesMap[selectedType]) {
                    variantValuesMap[selectedType].forEach(value => {
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = value;
                        valueSelect.appendChild(option);
                    });
                } else {
                    valueSelect.innerHTML += '<option value="" disabled>Không có giá trị nào</option>';
                }
            }
        });

        // Thêm sự kiện cho nút "Thêm giá trị"
        variantTypesContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-value')) {
                const group = e.target.closest('.variant-type-group');
                const variantType = group.querySelector('.variant-type').value;
                const valueContainer = group.querySelector('.variant-values-container');
                const newValueDiv = document.createElement('div');
                newValueDiv.className = 'variant-value d-flex align-items-center gap-2 mb-2';

                // Tạo select mới với các giá trị tương ứng
                const select = document.createElement('select');
                select.className = 'form-control';
                select.required = true;
                select.name = `variant_values[${Array.from(variantTypesContainer.children).indexOf(group)}][]`;

                select.innerHTML = '<option value="">Chọn giá trị</option>';
                if (variantValuesMap[variantType]) {
                    variantValuesMap[variantType].forEach(value => {
                        select.innerHTML += `<option value="${value}">${value}</option>`;
                    });
                }

                newValueDiv.innerHTML = `
                    <button type="button" class="btn btn-danger btn-sm remove-value">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                newValueDiv.insertBefore(select, newValueDiv.firstChild);
                valueContainer.appendChild(newValueDiv);
            }
        });

        // Thêm sự kiện cho nút "Xóa giá trị"
        variantTypesContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-value')) {
                const valueDiv = e.target.closest('.variant-value');
                if (valueDiv.parentElement.children.length > 1) {
                    valueDiv.remove();
                }
            }
        });

        // Thêm sự kiện cho nút "Thêm phân loại hàng"
        document.getElementById('addVariantType').addEventListener('click', function () {
            const newGroup = variantTypesContainer.querySelector('.variant-type-group').cloneNode(true);

            // Reset các giá trị
            newGroup.querySelectorAll('select').forEach(select => {
                select.value = '';
                if (select.classList.contains('variant-type')) {
                    // Gán lại sự kiện change cho select phân loại mới
                    select.addEventListener('change', function () {
                        updateVariantValues(this);
                    });
                }
            });

            // Reset container giá trị
            const valuesContainer = newGroup.querySelector('.variant-values-container');
            valuesContainer.innerHTML = `
                <div class="variant-value d-flex align-items-center gap-2 mb-2">
                    <select name="variant_values[${variantTypesContainer.children.length}][]" class="form-control" required>
                        <option value="">Chọn giá trị</option>
                        <option value="" disabled>Chọn phân loại trước</option>
                    </select>
                    <button type="button" class="btn btn-danger btn-sm remove-value">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

            variantTypesContainer.appendChild(newGroup);
        });

        // Hàm cập nhật giá trị cho select
        function updateVariantValues(selectElement) {
            const selectedType = selectElement.value;
            const valueSelect = selectElement.closest('.variant-type-group')
                .querySelector('.variant-value select');

            // Xóa tất cả các tùy chọn hiện tại
            valueSelect.innerHTML = '<option value="">Chọn giá trị</option>';

            // Thêm các giá trị tương ứng
            if (variantValuesMap[selectedType]) {
                variantValuesMap[selectedType].forEach(value => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = value;
                    valueSelect.appendChild(option);
                });
            } else {
                valueSelect.innerHTML += '<option value="" disabled>Không có giá trị nào</option>';
            }
        }

        // Thêm sự kiện cho nút "Xóa phân loại hàng"
        variantTypesContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-variant-type')) {
                const group = e.target.closest('.variant-type-group');
                if (variantTypesContainer.children.length > 1) {
                    group.remove();
                }
            }
        });

        // Sửa lại sự kiện cho nút "Hoàn tất phân loại hàng"
        document.getElementById('generateCombinations').addEventListener('click', function () {
            const variantTypes = [];
            const variantValues = [];

            document.querySelectorAll('.variant-type-group').forEach((group, index) => {
                const typeName = group.querySelector('select[name="variant_types[]"]').value.trim();
                if (!typeName) return;

                const values = Array.from(group.querySelectorAll('.variant-value select'))
                    .map(select => select.value.trim())
                    .filter(value => value);

                if (values.length) {
                    variantTypes.push(typeName);
                    variantValues.push(values);
                }
            });

            if (!variantTypes.length || !variantValues.length) {
                alert('Vui lòng thêm ít nhất một phân loại hàng và giá trị');
                return;
            }

            const combinations = generateAllCombinations(variantValues);
            updateVariantTable(variantTypes, combinations);
            variantModal.show();
        });

        // Các hàm hỗ trợ
        function generateAllCombinations(arrays) {
            if (arrays.length === 0) return [];
            if (arrays.length === 1) return arrays[0].map(x => [x]);

            const result = [];
            const firstArray = arrays[0];
            const remainingArrays = arrays.slice(1);
            const subCombinations = generateAllCombinations(remainingArrays);

            for (let i = 0; i < firstArray.length; i++) {
                for (let j = 0; j < subCombinations.length; j++) {
                    result.push([firstArray[i], ...subCombinations[j]]);
                }
            }

            return result;
        }

        function updateVariantTable(variantTypes, combinations) {
            const headerRow = document.getElementById('variantTableHeader');
            const tbody = document.getElementById('variantCombinations');

            headerRow.innerHTML = `
            <th style="min-width: 200px;">Biến thể</th>
            <th style="min-width: 120px;">Giá</th>
            <th style="min-width: 120px;">Số lượng</th>
            <th style="min-width: 200px;">Ảnh</th>
        `;

            tbody.innerHTML = combinations.map((combo, index) => {
                const variantString = combo.map((value, i) =>
                    `${variantTypes[i]}: ${value}`
                ).join(' - ');

                const hiddenInputs = combo.map((value, i) => `
                <input type="hidden" 
                       name="variant_combinations[${index}][${variantTypes[i]}]" 
                       value="${value}">
            `).join('');

                return `
                <tr>
                    <td>${variantString}${hiddenInputs}</td>
                    <td>
                        <input type="number" 
                               name="variant_combinations[${index}][price]" 
                               class="form-control" 
                               min="0" 
                               required>
                    </td>
                    <td>
                        <input type="number" 
                               name="variant_combinations[${index}][quantity]" 
                               class="form-control" 
                               min="0" 
                               required>
                    </td>
                    <td>
                        <input type="file" 
                               name="variant_combinations[${index}][image]" 
                               class="form-control"
                               accept="image/*">
                    </td>
                </tr>
            `;
            }).join('');
        }

        // Gán sự kiện change cho tất cả các select phân loại ban đầu
        document.querySelectorAll('.variant-type').forEach(select => {
            select.addEventListener('change', function () {
                updateVariantValues(this);
            });
        });
    });
</script>