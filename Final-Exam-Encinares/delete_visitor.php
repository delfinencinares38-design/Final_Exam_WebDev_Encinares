<?php
include("auth.php");
include("config.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM visitors WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>