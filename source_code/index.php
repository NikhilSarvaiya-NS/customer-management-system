<?php
include 'includes/db.php';
checkLogin();

$success = '';
$error   = '';

// ─── DELETE CUSTOMER ───
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Delete photo if exists
    $res = mysqli_query($conn, "SELECT photo FROM customers WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row['photo'] && file_exists('uploads/' . $row['photo'])) {
        unlink('uploads/' . $row['photo']);
    }
    mysqli_query($conn, "DELETE FROM customers WHERE id=$id");
    header("Location: index.php?msg=deleted");
    exit();
}

// ─── ADD / EDIT CUSTOMER ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_code  = mysqli_real_escape_string($conn, trim($_POST['customer_code']));
    $name           = mysqli_real_escape_string($conn, trim($_POST['name']));
    $addr1          = mysqli_real_escape_string($conn, trim($_POST['addr1']));
    $addr2          = mysqli_real_escape_string($conn, trim($_POST['addr2']));
    $city           = mysqli_real_escape_string($conn, trim($_POST['city']));
    $pincode        = mysqli_real_escape_string($conn, trim($_POST['pincode']));
    $state          = mysqli_real_escape_string($conn, trim($_POST['state']));
    $country        = mysqli_real_escape_string($conn, trim($_POST['country']));
    $contact_person = mysqli_real_escape_string($conn, trim($_POST['contact_person']));
    $contact_number = mysqli_real_escape_string($conn, trim($_POST['contact_number']));
    $email          = mysqli_real_escape_string($conn, trim($_POST['email']));
    $gstin          = mysqli_real_escape_string($conn, trim($_POST['gstin']));
    $edit_id        = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    // Validation
    if (empty($customer_code) || empty($name)) {
        $error = "Customer Code and Name are required!";
    } else {
        // Handle photo upload
        $photo = isset($_POST['existing_photo']) ? $_POST['existing_photo'] : '';
        if (!empty($_FILES['photo']['name'])) {
            $ext      = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($ext, $allowed)) {
                $error = "Only JPG, JPEG, PNG, GIF files are allowed!";
            } else {
                $photo = uniqid('photo_') . '.' . $ext;
                move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo);
            }
        }

        if (empty($error)) {
            if ($edit_id > 0) {
                // UPDATE
                $sql = "UPDATE customers SET 
                    customer_code='$customer_code', name='$name', addr1='$addr1', addr2='$addr2',
                    city='$city', pincode='$pincode', state='$state', country='$country',
                    contact_person='$contact_person', contact_number='$contact_number',
                    email='$email', gstin='$gstin', photo='$photo'
                    WHERE id=$edit_id";
                mysqli_query($conn, $sql);
                $success = "Customer updated successfully!";
            } else {
                // INSERT
                $sql = "INSERT INTO customers (customer_code, name, addr1, addr2, city, pincode, state, country, contact_person, contact_number, email, gstin, photo)
                        VALUES ('$customer_code','$name','$addr1','$addr2','$city','$pincode','$state','$country','$contact_person','$contact_number','$email','$gstin','$photo')";
                if (mysqli_query($conn, $sql)) {
                    $success = "Customer added successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Pre-fill for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $eid       = (int)$_GET['edit'];
    $edit_res  = mysqli_query($conn, "SELECT * FROM customers WHERE id=$eid");
    $edit_data = mysqli_fetch_assoc($edit_res);
}

// Fetch all customers
$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC");

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') $success = "Customer deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<?php include 'includes/navbar.php'; ?>
<nav class="navbar navbar-dark navbar-expand-lg" style="display:none">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-users me-2"></i>Customer Management System
        </a>
        <div class="ms-auto">
            <a href="quotation.php" class="btn btn-warning fw-bold">
                <i class="fas fa-file-invoice me-1"></i> Create Quotation
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4 px-4">

    <!-- ALERTS -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ADD / EDIT FORM -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-<?= $edit_data ? 'edit' : 'user-plus' ?> me-2"></i>
                <?= $edit_data ? 'Edit Customer' : 'Add New Customer' ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?>">
                    <input type="hidden" name="existing_photo" value="<?= $edit_data['photo'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <!-- Row 1 -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Customer Code <span class="text-danger">*</span></label>
                        <input type="text" name="customer_code" class="form-control"
                               value="<?= $edit_data['customer_code'] ?? '' ?>" placeholder="e.g. CUST001" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="<?= $edit_data['name'] ?? '' ?>" placeholder="Full name" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Address Line 1</label>
                        <input type="text" name="addr1" class="form-control"
                               value="<?= $edit_data['addr1'] ?? '' ?>" placeholder="Street / Area">
                    </div>

                    <!-- Row 2 -->
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Address Line 2</label>
                        <input type="text" name="addr2" class="form-control"
                               value="<?= $edit_data['addr2'] ?? '' ?>" placeholder="Landmark / Near (optional)">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">City</label>
                        <input type="text" name="city" class="form-control"
                               value="<?= $edit_data['city'] ?? '' ?>" placeholder="City">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Pincode</label>
                        <input type="text" name="pincode" class="form-control"
                               value="<?= $edit_data['pincode'] ?? '' ?>" placeholder="360001">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">State</label>
                        <input type="text" name="state" class="form-control"
                               value="<?= $edit_data['state'] ?? '' ?>" placeholder="Gujarat">
                    </div>

                    <!-- Row 3 -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Country</label>
                        <input type="text" name="country" class="form-control"
                               value="<?= $edit_data['country'] ?? 'India' ?>" placeholder="India">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Contact Person Name</label>
                        <input type="text" name="contact_person" class="form-control"
                               value="<?= $edit_data['contact_person'] ?? '' ?>" placeholder="Contact person">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control"
                               value="<?= $edit_data['contact_number'] ?? '' ?>" placeholder="Mobile number">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= $edit_data['email'] ?? '' ?>" placeholder="email@example.com">
                    </div>

                    <!-- Row 4 -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">GSTIN</label>
                        <input type="text" name="gstin" class="form-control"
                               value="<?= $edit_data['gstin'] ?? '' ?>" placeholder="GST Number">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Contact Person Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <?php if (!empty($edit_data['photo'])): ?>
                            <small class="text-muted">Current:
                                <img src="uploads/<?= $edit_data['photo'] ?>" height="30"
                                     class="rounded ms-1" alt="photo">
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-<?= $edit_data ? 'save' : 'plus-circle' ?> me-1"></i>
                            <?= $edit_data ? 'Update Customer' : 'Add Customer' ?>
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="index.php" class="btn btn-secondary w-50">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- CUSTOMERS TABLE -->
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>All Customers</h5>
            <span class="badge bg-primary fs-6"><?= mysqli_num_rows($customers) ?> Records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Contact Person</th>
                            <th>Number</th>
                            <th>Email</th>
                            <th>GSTIN</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sr = 1;
                        while ($row = mysqli_fetch_assoc($customers)):
                        ?>
                        <tr>
                            <td><?= $sr++ ?></td>
                            <td>
                                <?php if (!empty($row['photo']) && file_exists('uploads/' . $row['photo'])): ?>
                                    <img src="uploads/<?= $row['photo'] ?>" width="40" height="40"
                                         class="rounded-circle" alt="photo">
                                <?php else: ?>
                                    <div class="no-photo"><i class="fas fa-user"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['customer_code']) ?></span></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['city']) ?></td>
                            <td><?= htmlspecialchars($row['state']) ?></td>
                            <td><?= htmlspecialchars($row['contact_person']) ?></td>
                            <td><?= htmlspecialchars($row['contact_number']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><small><?= htmlspecialchars($row['gstin']) ?></small></td>
                            <td>
                                <a href="index.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="quotation.php?customer_id=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-success me-1" title="Create Quotation">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                                <a href="index.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                   title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this customer?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($customers) === 0): ?>
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No customers found. Add your first customer above!
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
