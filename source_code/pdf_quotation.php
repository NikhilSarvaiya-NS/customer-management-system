<?php
include 'includes/db.php';
checkLogin();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$qid = (int)$_GET['id'];

// Fetch quotation
$qres = mysqli_query($conn, "SELECT q.*, c.* FROM quotations q 
                              JOIN customers c ON q.customer_id = c.id 
                              WHERE q.id = $qid");
$q = mysqli_fetch_assoc($qres);

if (!$q) {
    die("Quotation not found!");
}

// Fetch items
$items = mysqli_query($conn, "SELECT * FROM quotation_items WHERE quotation_id = $qid");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation #<?= str_pad($qid, 5, '0', STR_PAD_LEFT) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; }
        .quotation-box { background: #fff; max-width: 900px; margin: 30px auto; padding: 40px; border-radius: 10px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
        .company-header { background: linear-gradient(135deg, #1a73e8, #0d47a1); color: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; }
        .company-header h2 { margin: 0; font-size: 24px; }
        .company-header p { margin: 5px 0 0; opacity: 0.9; }
        .quotation-title { font-size: 28px; font-weight: bold; color: #1a73e8; text-align: center; margin-bottom: 10px; }
        .quotation-num { text-align: center; color: #666; margin-bottom: 30px; }
        .info-section { background: #f8f9fa; border-left: 4px solid #1a73e8; padding: 15px 20px; border-radius: 0 8px 8px 0; margin-bottom: 20px; }
        .info-section h6 { color: #1a73e8; font-weight: bold; margin-bottom: 10px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px; }
        .info-label { font-weight: 600; color: #555; min-width: 160px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table thead th { background: #1a73e8; color: white; padding: 12px; text-align: left; }
        table tbody td { padding: 12px; border-bottom: 1px solid #eee; }
        table tbody tr:nth-child(even) { background: #f8f9fa; }
        .total-row td { font-weight: bold; font-size: 16px; background: #e8f0fe; }
        .grand-total { background: #1a73e8 !important; color: white !important; font-size: 18px; }
        .btn-download { background: #1a73e8; color: white; padding: 12px 30px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; }
        .btn-back { background: #6c757d; color: white; padding: 12px 30px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; text-decoration: none; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .quotation-box { box-shadow: none; margin: 0; padding: 20px; }
        }
    </style>
</head>
<body>

<div class="quotation-box">

    <!-- Header -->
    <div class="company-header">
        <h2><i class="fas fa-building me-2"></i>Customer Management System</h2>
        <p>Professional Quotation Management</p>
    </div>

    <!-- Title -->
    <div class="quotation-title">QUOTATION</div>
    <div class="quotation-num">
        <strong>Quotation No:</strong> QT-<?= str_pad($qid, 5, '0', STR_PAD_LEFT) ?> &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Date:</strong> <?= date('d M Y', strtotime($q['quotation_date'])) ?>
    </div>

    <!-- Customer Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="info-section">
                <h6><i class="fas fa-user me-2"></i>Customer Information</h6>
                <div class="info-row"><span class="info-label">Customer Code:</span> <span><?= htmlspecialchars($q['customer_code']) ?></span></div>
                <div class="info-row"><span class="info-label">Customer Name:</span> <span><?= htmlspecialchars($q['name']) ?></span></div>
                <div class="info-row"><span class="info-label">GSTIN:</span> <span><?= htmlspecialchars($q['gstin']) ?></span></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-section">
                <h6><i class="fas fa-map-marker-alt me-2"></i>Address & Contact</h6>
                <div class="info-row"><span class="info-label">Address:</span> <span><?= htmlspecialchars($q['addr1'] . ($q['addr2'] ? ', ' . $q['addr2'] : '')) ?></span></div>
                <div class="info-row"><span class="info-label">City / State:</span> <span><?= htmlspecialchars($q['city'] . ', ' . $q['state'] . ' - ' . $q['pincode']) ?></span></div>
                <div class="info-row"><span class="info-label">Contact Person:</span> <span><?= htmlspecialchars($q['contact_person']) ?></span></div>
                <div class="info-row"><span class="info-label">Phone:</span> <span><?= htmlspecialchars($q['contact_number']) ?></span></div>
                <div class="info-row"><span class="info-label">Email:</span> <span><?= htmlspecialchars($q['email']) ?></span></div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <h6 class="fw-bold mb-3"><i class="fas fa-box me-2"></i>Product Details</h6>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price (₹)</th>
                <th>Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sr = 1;
            while ($item = mysqli_fetch_assoc($items)):
            ?>
            <tr>
                <td><?= $sr++ ?></td>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td>₹ <?= number_format($item['price'], 2) ?></td>
                <td>₹ <?= number_format($item['total'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr class="total-row grand-total">
                <td colspan="4" style="text-align:right; padding-right:20px;">GRAND TOTAL</td>
                <td>₹ <?= number_format($q['total_amount'], 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="row mt-4">
        <div class="col-md-6">
            <small class="text-muted">This is a computer generated quotation. No signature required.</small>
        </div>
        <div class="col-md-6 text-end">
            <p class="fw-bold mb-0">For Customer Management System</p>
            <br><br>
            <p class="border-top pt-2">Authorized Signature</p>
        </div>
    </div>

    <!-- Buttons -->
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn-download me-3">
            <i class="fas fa-download me-2"></i>Download / Print PDF
        </button>
        <a href="quotation.php" class="btn-back">
            <i class="fas fa-plus me-2"></i>New Quotation
        </a>
        &nbsp;
        <a href="index.php" class="btn-back">
            <i class="fas fa-home me-2"></i>Home
        </a>
    </div>

</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>
