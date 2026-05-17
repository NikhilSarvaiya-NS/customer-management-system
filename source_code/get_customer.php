<?php
include 'includes/db.php';
checkLogin();

if (isset($_GET['id'])) {
    $id  = (int)$_GET['id'];
    $res = mysqli_query($conn, "SELECT * FROM customers WHERE id=$id");
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        echo json_encode([
            'success'        => true,
            'customer_code'  => $row['customer_code'],
            'name'           => $row['name'],
            'addr1'          => $row['addr1'],
            'addr2'          => $row['addr2'],
            'city'           => $row['city'],
            'pincode'        => $row['pincode'],
            'state'          => $row['state'],
            'country'        => $row['country'],
            'contact_person' => $row['contact_person'],
            'contact_number' => $row['contact_number'],
            'email'          => $row['email'],
            'gstin'          => $row['gstin'],
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
