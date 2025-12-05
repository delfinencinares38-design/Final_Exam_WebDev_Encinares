<?php
include("auth.php");
include("config.php");

$id = $_GET['id'];

// Fetch visitor record
$stmt = $conn->prepare("SELECT * FROM visitors WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$visitor = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $address  = trim($_POST['address']);
    $contact  = trim($_POST['contact']);
    $school   = trim($_POST['school_office']);
    $purpose  = $_POST['purpose'];

    if (!empty($fullname) && !empty($purpose)) {
        $stmt = $conn->prepare("UPDATE visitors 
            SET fullname=?, address=?, contact=?, school_office=?, purpose=?, date=NOW(), time=NOW() 
            WHERE id=?");
        $stmt->bind_param("sssssi", $fullname, $address, $contact, $school, $purpose, $id);
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Visitor</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .gradient-header {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            color: white;
            border-radius: 8px 8px 0 0;
            padding: 15px;
            text-align: center;
        }
        .btn-update {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            color: white;
            font-weight: bold;
            border: none;
        }
        .btn-update:hover {
            opacity: 0.9;
            transform: scale(1.05);
            transition: all 0.2s ease-in-out;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #fa709a, #fee140);
            color: white;
            font-weight: bold;
            border: none;
        }
        .btn-cancel:hover {
            opacity: 0.9;
            transform: scale(1.05);
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-lg mx-auto" style="max-width: 600px;">
        <div class="gradient-header">
            <h3><i class="bi bi-pencil-square"></i> Edit Visitor</h3>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-control" 
                           value="<?php echo htmlspecialchars($visitor['fullname']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" 
                           value="<?php echo htmlspecialchars($visitor['address']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact" class="form-control" 
                           value="<?php echo htmlspecialchars($visitor['contact']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">School/Office</label>
                    <input type="text" name="school_office" class="form-control" 
                           value="<?php echo htmlspecialchars($visitor['school_office']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Purpose</label>
                    <select name="purpose" class="form-select" required>
                        <option value="Visit" <?php if($visitor['purpose']=='Visit') echo 'selected'; ?>>Visit</option>
                        <option value="Inquiry" <?php if($visitor['purpose']=='Inquiry') echo 'selected'; ?>>Inquiry</option>
                        <option value="Exam" <?php if($visitor['purpose']=='Exam') echo 'selected'; ?>>Exam</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-update w-50 me-2">Update</button>
                    <a href="dashboard.php" class="btn btn-cancel w-50">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>