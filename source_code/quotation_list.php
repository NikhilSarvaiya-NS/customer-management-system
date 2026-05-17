<?php
include 'includes/db.php';
checkLogin();

// Delete quotation
if (isset($_GET['delete']) && isAdmin()) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM quotations WHERE id=$id");
    header("Location: quotation_list.php?msg=deleted");
    exit();
}

$success = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "Quotation deleted successfully!";
}

$quotations = mysqli_query($conn, "SELECT q.*, c.name as customer_name, c.customer_code 
                                    FROM quotations q 
                                    JOIN customers c ON q.customer_id = c.id 
                                    ORDER BY q.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid mt-4 px-4">

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Quotation History</h5>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-light text-dark fs-6"><?= mysqli_num_rows($quotations) ?> Quotations</span>
                <a href="quotation.php" class="btn btn-warning btn-sm fw-bold">
                    <i class="fas fa-plus me-1"></i>New Quotation
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Quotation No</th>
                            <th>Customer Code</th>
                            <th>Customer Name</th>
                            <th>Date</th>
                            <th>Amount (₹)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sr = 1;
                        mysqli_data_seek($quotations, 0);
                        while ($q = mysqli_fetch_assoc($quotations)):
                        ?>
                        <tr>
                            <td><?= $sr++ ?></td>
                            <td><span class="badge bg-success fs-6">QT-<?= str_pad($q['id'], 5, '0', STR_PAD_LEFT) ?></span></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($q['customer_code']) ?></span></td>
                            <td class="fw-semibold"><?= htmlspecialchars($q['customer_name']) ?></td>
                            <td><?= date('d M Y', strtotime($q['quotation_date'])) ?></td>
                            <td class="fw-bold text-success">₹ <?= number_format($q['total_amount'], 2) ?></td>
                            <td>
                                <a href="pdf_quotation.php?id=<?= $q['id'] ?>"
                                   class="btn btn-sm btn-danger me-1" target="_blank" title="View/Print PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <?php if (isAdmin()): ?>
                                <a href="quotation_list.php?delete=<?= $q['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this quotation?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($quotations) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No quotations found. <a href="quotation.php">Create your first quotation!</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
