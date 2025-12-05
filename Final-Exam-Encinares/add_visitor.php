<?php
include("auth.php");
include("config.php");

date_default_timezone_set("Asia/Manila"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);
    $address  = trim($_POST['address']);
    $contact  = trim($_POST['contact']);
    $school   = trim($_POST['school_office']);
    $purpose  = $_POST['purpose'];

    $date = date("Y-m-d");
    $time = date("H:i:s");

    if (!empty($fullname) && !empty($purpose)) {
        $stmt = $conn->prepare("INSERT INTO visitors 
            (fullname, date, time, address, contact, school_office, purpose) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", 
            $fullname, $date, $time, $address, $contact, $school, $purpose
        );
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Visitor</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .gradient-header {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            color: white;
            border-radius: 8px 8px 0 0;
            padding: 15px;
            text-align: center;
        }
        .btn-save {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
            font-weight: bold;
            border: none;
        }
        .btn-save:hover {
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
            <h3><i class="bi bi-person-plus"></i> New Visitor</h3>
        </div>
        <div class="card-body">

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">School/Office</label>
                    <input type="text" name="school_office" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Purpose</label>
                    <select name="purpose" class="form-select" required>
                        <option value="Visit">Visit</option>
                        <option value="Inquiry">Inquiry</option>
                        <option value="Exam">Exam</option>
                    </select>
                </div>

                <div class="alert alert-info">
                    *Date and Time are recorded automatically.
                </div>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-save w-50 me-2">Save</button>
                    <a href="dashboard.php" class="btn btn-cancel w-50">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>
</body>
</html>
