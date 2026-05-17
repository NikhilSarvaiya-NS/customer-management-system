<?php
include 'includes/db.php';
checkLogin();

$success = '';
$preload_customer = null;

// Pre-load customer if coming from table button
if (isset($_GET['customer_id'])) {
    $cid = (int)$_GET['customer_id'];
    $res = mysqli_query($conn, "SELECT * FROM customers WHERE id=$cid");
    $preload_customer = mysqli_fetch_assoc($res);
}

// Save quotation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_quotation'])) {
    $customer_id = (int)$_POST['customer_id'];
    $quote_date  = mysqli_real_escape_string($conn, $_POST['quote_date']);
    $total       = (float)$_POST['grand_total'];

    $sql = "INSERT INTO quotations (customer_id, quotation_date, total_amount) VALUES ($customer_id, '$quote_date', $total)";
    mysqli_query($conn, $sql);
    $quot_id = mysqli_insert_id($conn);

    // Save products
    $products = $_POST['product_name'];
    $qtys     = $_POST['qty'];
    $prices   = $_POST['price'];

    for ($i = 0; $i < count($products); $i++) {
        if (!empty($products[$i])) {
            $pname = mysqli_real_escape_string($conn, $products[$i]);
            $qty   = (int)$qtys[$i];
            $price = (float)$prices[$i];
            $ptotal = $qty * $price;
            mysqli_query($conn, "INSERT INTO quotation_items (quotation_id, product_name, qty, price, total)
                                 VALUES ($quot_id, '$pname', $qty, $price, $ptotal)");
        }
    }
    header("Location: pdf_quotation.php?id=$quot_id");
    exit();
}

// Fetch all customers for dropdown
$customers = mysqli_query($conn, "SELECT id, customer_code, name FROM customers ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Quotation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-users me-2"></i>Customer Management System
        </a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> Back to Customers
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4 px-4">
    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Create Customer Quotation</h5>
        </div>
        <div class="card-body">
            <form method="POST" id="quotationForm">

                <!-- ─── CUSTOMER SELECTION ─── -->
                <div class="section-title">
                    <i class="fas fa-user me-2"></i>Customer Details
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Select Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_select" class="form-select" required>
                            <option value="">-- Select Customer --</option>
                            <?php while ($c = mysqli_fetch_assoc($customers)): ?>
                                <option value="<?= $c['id'] ?>"
                                    <?= ($preload_customer && $preload_customer['id'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['customer_code']) ?> - <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Quotation Date</label>
                        <input type="date" name="quote_date" class="form-control"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <!-- Auto-filled customer fields -->
                <div class="row g-3 mb-4" id="customer_details_section">
                    <div class="col-md-3">
                        <label class="form-label">Customer Code</label>
                        <input type="text" id="f_code" class="form-control" disabled
                               value="<?= $preload_customer['customer_code'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" id="f_name" class="form-control" disabled
                               value="<?= $preload_customer['name'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" id="f_addr1" class="form-control" disabled
                               value="<?= $preload_customer['addr1'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" id="f_addr2" class="form-control" disabled
                               value="<?= $preload_customer['addr2'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">City</label>
                        <input type="text" id="f_city" class="form-control" disabled
                               value="<?= $preload_customer['city'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Pincode</label>
                        <input type="text" id="f_pincode" class="form-control" disabled
                               value="<?= $preload_customer['pincode'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">State</label>
                        <input type="text" id="f_state" class="form-control" disabled
                               value="<?= $preload_customer['state'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Country</label>
                        <input type="text" id="f_country" class="form-control" disabled
                               value="<?= $preload_customer['country'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contact Person</label>
                        <input type="text" id="f_contact_person" class="form-control" disabled
                               value="<?= $preload_customer['contact_person'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" id="f_contact_number" class="form-control" disabled
                               value="<?= $preload_customer['contact_number'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email Address</label>
                        <input type="text" id="f_email" class="form-control" disabled
                               value="<?= $preload_customer['email'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">GSTIN</label>
                        <input type="text" id="f_gstin" class="form-control" disabled
                               value="<?= $preload_customer['gstin'] ?? '' ?>">
                    </div>
                </div>

                <!-- ─── PRODUCT SECTION ─── -->
                <div class="section-title">
                    <i class="fas fa-box me-2"></i>Product Details
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="product_table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="45%">Product Name</th>
                                <th width="15%">Quantity</th>
                                <th width="15%">Price (₹)</th>
                                <th width="15%">Total (₹)</th>
                                <th width="5%">Remove</th>
                            </tr>
                        </thead>
                        <tbody id="product_rows">
                            <tr class="product-row">
                                <td class="row-num">1</td>
                                <td><input type="text" name="product_name[]" class="form-control" placeholder="Product name" required></td>
                                <td><input type="number" name="qty[]" class="form-control qty" min="1" value="1" required></td>
                                <td><input type="number" name="price[]" class="form-control price" min="0" step="0.01" value="0.00" required></td>
                                <td><input type="text" class="form-control row-total" value="0.00" disabled></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold fs-5">Grand Total (₹)</td>
                                <td colspan="2">
                                    <input type="text" id="grand_total_display" class="form-control fw-bold text-success fs-5" value="0.00" disabled>
                                    <input type="hidden" name="grand_total" id="grand_total_hidden" value="0.00">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <button type="button" class="btn btn-outline-primary" id="add_product_btn">
                        <i class="fas fa-plus me-1"></i> Add Product
                    </button>
                    <button type="submit" name="save_quotation" class="btn btn-success px-4">
                        <i class="fas fa-file-pdf me-1"></i> Save & Download PDF
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="js/quotation.js"></script>

<?php if ($preload_customer): ?>
<script>
    // Auto-trigger customer load if pre-loaded from button
    $(document).ready(function() {
        // Already pre-filled via PHP, just calculate
        calculateAll();
    });
</script>
<?php endif; ?>

</body>
</html>
