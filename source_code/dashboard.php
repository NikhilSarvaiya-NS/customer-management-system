<?php
include 'includes/db.php';
checkLogin();

// ─── COUNTS ───
$total_customers  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM customers"))[0];
$total_quotations = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM quotations"))[0];
$total_revenue    = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) FROM quotations"))[0];
$today_quotations = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM quotations WHERE DATE(created_at)=CURDATE()"))[0];

// ─── RECENT CUSTOMERS ───
$recent_customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY created_at DESC LIMIT 5");

// ─── RECENT QUOTATIONS ───
$recent_quotations = mysqli_query($conn, "SELECT q.*, c.name as customer_name 
                                          FROM quotations q 
                                          JOIN customers c ON q.customer_id = c.id 
                                          ORDER BY q.created_at DESC LIMIT 5");

// ─── MONTHLY QUOTATION DATA FOR CHART ───
$monthly = mysqli_query($conn, "SELECT MONTH(quotation_date) as month, 
                                        COUNT(*) as count, 
                                        SUM(total_amount) as total 
                                 FROM quotations 
                                 WHERE YEAR(quotation_date) = YEAR(CURDATE())
                                 GROUP BY MONTH(quotation_date)
                                 ORDER BY month");

$months_labels = [];
$months_count  = [];
$months_total  = [];
$month_names   = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

while ($row = mysqli_fetch_assoc($monthly)) {
    $months_labels[] = $month_names[$row['month'] - 1];
    $months_count[]  = $row['count'];
    $months_total[]  = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .stat-card {
            border-radius: 15px;
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s;
            border: none;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .icon {
            font-size: 50px;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.3;
        }
        .stat-card h3 { font-size: 36px; font-weight: 800; margin: 5px 0; }
        .stat-card p  { margin: 0; font-size: 15px; opacity: 0.9; }
        .stat-card small { font-size: 12px; opacity: 0.8; }
        .bg-grad-blue    { background: linear-gradient(135deg, #1a73e8, #0d47a1); }
        .bg-grad-green   { background: linear-gradient(135deg, #43a047, #1b5e20); }
        .bg-grad-orange  { background: linear-gradient(135deg, #fb8c00, #e65100); }
        .bg-grad-purple  { background: linear-gradient(135deg, #8e24aa, #4a148c); }
        .section-card { border-radius: 15px; border: none; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .section-card .card-header {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        .welcome-bar {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: white;
            border-radius: 15px;
            padding: 20px 25px;
            margin-bottom: 25px;
        }
        .table th { background: #f8f9fa; font-size: 13px; }
        .table td { font-size: 13px; vertical-align: middle; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container-fluid mt-4 px-4">

    <!-- WELCOME BAR -->
    <div class="welcome-bar d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1"><i class="fas fa-hand-wave me-2"></i>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>! 👋</h5>
            <p class="mb-0 opacity-75"><i class="fas fa-calendar me-1"></i><?= date('l, d F Y') ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-light btn-sm">
                <i class="fas fa-user-plus me-1"></i>Add Customer
            </a>
            <a href="quotation.php" class="btn btn-warning btn-sm fw-bold">
                <i class="fas fa-file-invoice me-1"></i>New Quotation
            </a>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card bg-grad-blue shadow">
                <p>Total Customers</p>
                <h3><?= $total_customers ?></h3>
                <small><i class="fas fa-arrow-up me-1"></i>All registered customers</small>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card bg-grad-green shadow">
                <p>Total Quotations</p>
                <h3><?= $total_quotations ?></h3>
                <small><i class="fas fa-file-invoice me-1"></i>All time quotations</small>
                <div class="icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card bg-grad-orange shadow">
                <p>Total Revenue</p>
                <h3>₹<?= number_format($total_revenue, 0) ?></h3>
                <small><i class="fas fa-rupee-sign me-1"></i>All time revenue</small>
                <div class="icon"><i class="fas fa-rupee-sign"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card bg-grad-purple shadow">
                <p>Today's Quotations</p>
                <h3><?= $today_quotations ?></h3>
                <small><i class="fas fa-calendar-day me-1"></i>Created today</small>
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
            </div>
        </div>
    </div>

    <!-- CHART + RECENT CUSTOMERS -->
    <div class="row g-4 mb-4">

        <!-- CHART -->
        <div class="col-md-7">
            <div class="card section-card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i>Monthly Quotations (<?= date('Y') ?>)
                </div>
                <div class="card-body">
                    <canvas id="quotationChart" height="130"></canvas>
                </div>
            </div>
        </div>

        <!-- QUICK STATS -->
        <div class="col-md-5">
            <div class="card section-card h-100">
                <div class="card-header">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </div>
                <div class="card-body d-flex flex-column gap-3 justify-content-center">
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Add New Customer
                    </a>
                    <a href="quotation.php" class="btn btn-success btn-lg">
                        <i class="fas fa-file-invoice me-2"></i>Create New Quotation
                    </a>
                    <a href="quotation_list.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-history me-2"></i>View Quotation History
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-users me-2"></i>View All Customers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT CUSTOMERS + RECENT QUOTATIONS -->
    <div class="row g-4">

        <!-- RECENT CUSTOMERS -->
        <div class="col-md-6">
            <div class="card section-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users me-2"></i>Recent Customers</span>
                    <a href="index.php" class="btn btn-light btn-sm">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>City</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($c = mysqli_fetch_assoc($recent_customers)): ?>
                            <tr>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($c['customer_code']) ?></span></td>
                                <td class="fw-semibold"><?= htmlspecialchars($c['name']) ?></td>
                                <td><?= htmlspecialchars($c['city']) ?></td>
                                <td><?= htmlspecialchars($c['contact_number']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RECENT QUOTATIONS -->
        <div class="col-md-6">
            <div class="card section-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-file-invoice me-2"></i>Recent Quotations</span>
                    <a href="quotation_list.php" class="btn btn-light btn-sm">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Quot. No</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($recent_quotations) > 0):
                                while ($q = mysqli_fetch_assoc($recent_quotations)): ?>
                            <tr>
                                <td><span class="badge bg-success">QT-<?= str_pad($q['id'], 5, '0', STR_PAD_LEFT) ?></span></td>
                                <td><?= htmlspecialchars($q['customer_name']) ?></td>
                                <td><?= date('d M Y', strtotime($q['quotation_date'])) ?></td>
                                <td class="fw-bold text-success">₹<?= number_format($q['total_amount'], 2) ?></td>
                                <td>
                                    <a href="pdf_quotation.php?id=<?= $q['id'] ?>" class="btn btn-sm btn-danger" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No quotations yet</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ─── MONTHLY CHART ───
var ctx = document.getElementById('quotationChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months_labels ?: ['No Data']) ?>,
        datasets: [
            {
                label: 'Quotations Count',
                data: <?= json_encode($months_count ?: [0]) ?>,
                backgroundColor: 'rgba(26, 115, 232, 0.8)',
                borderRadius: 8,
                yAxisID: 'y'
            },
            {
                label: 'Revenue (₹)',
                data: <?= json_encode($months_total ?: [0]) ?>,
                backgroundColor: 'rgba(67, 160, 71, 0.8)',
                borderRadius: 8,
                type: 'line',
                borderColor: '#43a047',
                fill: false,
                tension: 0.4,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Quotations' },
                beginAtZero: true,
                ticks: { stepSize: 1 }
            },
            y1: {
                type: 'linear',
                position: 'right',
                title: { display: true, text: 'Revenue (₹)' },
                beginAtZero: true,
                grid: { drawOnChartArea: false }
            }
        }
    }
});
</script>
</body>
</html>
