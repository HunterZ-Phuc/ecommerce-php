<div id="variantSection" style="display: none;">
    <div class="variant-types mb-4">
        <h6>Thông tin phân loại hàng</h6>
        
        <div id="variantTypesContainer">
            <div class="variant-type-group mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <input type="text" name="variant_types[]" class="form-control" 
                           placeholder="Tên phân loại (VD: Màu sắc, Kích thước)">
                    <button type="button" class="btn btn-danger btn-sm remove-variant-type">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                
                <div class="variant-values-container">
                    <div class="variant-value d-flex align-items-center gap-2 mb-2">
                        <input type="text" name="variant_values[0][]" class="form-control" 
                               placeholder="Giá trị phân loại">
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
document.addEventListener('DOMContentLoaded', function() {
    const hasVariantsCheckbox = document.getElementById('hasVariants');
    const variantSection = document.getElementById('variantSection');
    const basicPricing = document.getElementById('basicPricing');
    const variantModal = new bootstrap.Modal(document.getElementById('variantDetailsModal'));

    hasVariantsCheckbox.addEventListener('change', function() {
        variantSection.style.display = this.checked ? 'block' : 'none';
        basicPricing.style.display = this.checked ? 'none' : 'block';
    });

    document.getElementById('addVariantType').addEventListener('click', function() {
        const container = document.getElementById('variantTypesContainer');
        const newGroup = container.querySelector('.variant-type-group').cloneNode(true);
        
        newGroup.querySelectorAll('input').forEach(input => input.value = '');
        
        const groupIndex = container.children.length;
        newGroup.querySelectorAll('.variant-value input').forEach(input => {
            input.name = `variant_values[${groupIndex}][]`;
        });
        
        setupEventListeners(newGroup);
        container.appendChild(newGroup);
    });

    document.getElementById('generateCombinations').addEventListener('click', function() {
        const variantTypes = [];
        const variantValues = [];
        
        document.querySelectorAll('.variant-type-group').forEach((group, index) => {
            const typeName = group.querySelector('input[name="variant_types[]"]').value.trim();
            if (!typeName) return;
            
            const values = Array.from(group.querySelectorAll('.variant-value input'))
                               .map(input => input.value.trim())
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

    document.getElementById('confirmVariants').addEventListener('click', function() {
        const rows = document.querySelectorAll('#variantCombinations tr');
        const form = document.querySelector('form');
        
        // Xóa các input hidden cũ (nếu có)
        form.querySelectorAll('input[name^="variant_prices"], input[name^="variant_quantities"]')
            .forEach(input => input.remove());
        
        rows.forEach((row, index) => {
            const price = row.querySelector('input[name$="[price]"]').value;
            const quantity = row.querySelector('input[name$="[quantity]"]').value;
            
            if (!price || !quantity || price < 0 || quantity < 0) {
                alert('Vui lòng nhập đầy đủ và chính xác giá và số lượng cho tất cả biến thể');
                return;
            }

            const priceInput = document.createElement('input');
            priceInput.type = 'hidden';
            priceInput.name = `variant_prices[${index}]`;
            priceInput.value = price;
            form.appendChild(priceInput);

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `variant_quantities[${index}]`;
            quantityInput.value = quantity;
            form.appendChild(quantityInput);
        });

        // Log ra dữ liệu biến thể sau khi thêm vào form
        console.log("Dữ liệu biến thể đã được thêm vào form:");
        console.log("Giá biến thể:", Array.from(form.querySelectorAll('input[name^="variant_prices"]')).map(input => input.value));
        console.log("Số lượng biến thể:", Array.from(form.querySelectorAll('input[name^="variant_quantities"]')).map(input => input.value));

        variantModal.hide();
    });

    // Thêm event listener cho form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        // e.preventDefault(); // Uncomment để test
        console.log("Form đang được submit với dữ liệu:");
        const formData = new FormData(this);
        
        // Log ra tất cả dữ liệu trong form
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Log cụ thể dữ liệu biến thể
        console.log("Dữ liệu biến thể khi submit:");
        console.log("Giá biến thể:", Array.from(formData.getAll('variant_prices[]')));
        console.log("Số lượng biến thể:", Array.from(formData.getAll('variant_quantities[]')));
    });

    function setupEventListeners(group) {
        group.querySelector('.remove-variant-type').addEventListener('click', function() {
            if (document.querySelectorAll('.variant-type-group').length > 1) {
                group.remove();
            }
        });

        group.querySelectorAll('.variant-value').forEach(valueDiv => {
            valueDiv.querySelector('.remove-value').addEventListener('click', function() {
                if (valueDiv.parentElement.children.length > 1) {
                    valueDiv.remove();
                }
            });
        });

        group.querySelector('.add-value').addEventListener('click', function() {
            const container = group.querySelector('.variant-values-container');
            const valueDiv = container.querySelector('.variant-value').cloneNode(true);
            valueDiv.querySelector('input').value = '';
            
            const groupIndex = Array.from(document.querySelectorAll('.variant-type-group')).indexOf(group);
            valueDiv.querySelector('input').name = `variant_values[${groupIndex}][]`;
            
            valueDiv.querySelector('.remove-value').addEventListener('click', function() {
                if (container.children.length > 1) {
                    valueDiv.remove();
                }
            });
            
            container.appendChild(valueDiv);
        });
    }

    setupEventListeners(document.querySelector('.variant-type-group'));
});

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
                <td>
                    ${variantString}
                    ${hiddenInputs}
                </td>
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
                           class="form-control">
                </td>
            </tr>
        `;
    }).join('');
}
</script> 