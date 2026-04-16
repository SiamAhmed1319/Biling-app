<?php
include 'db.php';
if(isset($_GET['barcode'])) {
    $q = mysqli_real_escape_string($conn, $_GET['barcode']);
    // বারকোড অথবা নাম—যেকোনো একটা মিললেই ডাটা আসবে
    $sql = "SELECT * FROM products WHERE barcode = '$q' OR p_name LIKE '%$q%' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'name' => $row['p_name'], 'price' => $row['price']]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>