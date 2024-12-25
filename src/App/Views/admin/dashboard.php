<div class="container-fluid mt-4">
    <h1 class="h3 mb-4">Tổng quan cửa hàng</h1>

    <!-- Thống kê tổng quan -->
    <div class="row">
        <!-- Thống kê đơn hàng -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng đơn hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalOrders ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê doanh thu -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Doanh thu tháng này</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($monthlyRevenue) ?> đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê khách hàng -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng khách hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalCustomers ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê sản phẩm -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Sản phẩm sắp hết hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $lowStockProducts ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ doanh thu theo tháng</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ tròn phân bố đơn hàng -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bố trạng thái đơn hàng</h6>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script cho biểu đồ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ doanh thu
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($revenueData['labels']) ?>,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode($revenueData['values']) ?>,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    }
});

// Biểu đồ trạng thái đơn hàng
const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($orderStatusData['labels']) ?>,
        datasets: [{
            data: <?= json_encode($orderStatusData['values']) ?>,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)'
            ]
        }]
    }
});
</script>