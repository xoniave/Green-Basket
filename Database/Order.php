<?php
include("connection.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($_POST['update_order']))
    {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $order_id]);

        header("Location: ../admin.php?page=orders");
        exit;
    }

    if (isset($_POST['delete_order'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: ../admin.php?page=orders");
        exit;
    }
}?>