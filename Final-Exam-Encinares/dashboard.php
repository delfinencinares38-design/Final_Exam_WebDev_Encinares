<?php
include("auth.php");
include("config.php");

$filter       = $_GET['purpose'] ?? '';
$search       = $_GET['search'] ?? '';
$filter_date  = $_GET['filter_date'] ?? '';
$filter_month = $_GET['filter_month'] ?? '';

$sql = "SELECT * FROM visitors";
$conditions = [];
$params = [];

if ($filter) {
    $conditions[] = "purpose = ?";
    $params[] = $filter;
}

if ($search) {
    $conditions[] = "(fullname LIKE ? OR contact LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filter_date) {
    $conditions[] = "DATE(date) = ?";
    $params[] = $filter_date;
}
if ($filter_month) {
    $conditions[] = "DATE_FORMAT(date, '%Y-%m') = ?";
    $params[] = $filter_month;
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

/* -- STATISTICS --*/
$count_today_row = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visitors 
    WHERE DATE(date) = CURDATE()
")->fetch_assoc();

$count_exam_row = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visitors 
    WHERE DATE(date) = CURDATE() 
      AND purpose = 'Exam'
")->fetch_assoc();

$count_others_row = $conn->query("
    SELECT COUNT(*) AS total 
    FROM visitors 
    WHERE DATE(date) = CURDATE() 
      AND purpose IN ('Visit','Inquiry')
")->fetch_assoc();

$count_today  = $count_today_row['total']  ?? 0;
$count_exam   = $count_exam_row['total']   ?? 0;
$count_others = $count_others_row['total'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>CCDI Visitor Dashboard</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        table td, table th {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
   <a class="navbar-brand d-flex align-items-center" href="#">
    <img src="assets/img/ccdi_logo.jpg" alt="Logo" style="height: 30px; width: auto;" class="me-2">
         CCDI Visitor Log
    </a>
    <div class="d-flex">
      <span class="navbar-text me-3">Welcome, <?php echo $_SESSION['username']; ?></span>
      <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center">
        <h4>Visitor Records</h4>
        <a href="add_visitor.php" 
           class="btn fw-bold text-white" 
           style="background: linear-gradient(135deg, #00c6ff, #0072ff); border: none; padding: 10px 20px; font-size: 1rem;">
           + New Visitor
        </a>
    </div>

    <h6 class="mt-4 fw-bold">Filter</h6>
    <form method="get" class="row g-2 align-items-end">

        <!-- Purpose -->
        <div class="col-md-3">
            <label class="form-label">Purpose</label>
            <select name="purpose" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Visit" <?php if($filter=='Visit') echo 'selected'; ?>>Visit</option>
                <option value="Inquiry" <?php if($filter=='Inquiry') echo 'selected'; ?>>Inquiry</option>
                <option value="Exam" <?php if($filter=='Exam') echo 'selected'; ?>>Exam</option>
            </select>
        </div>

        <!-- Date -->
        <div class="col-md-3">
            <label class="form-label">Date</label>
            <input type="date" name="filter_date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>" onchange="this.form.submit()">
        </div>

        <!-- Month -->
        <div class="col-md-3">
            <label class="form-label">Month</label>
            <input type="month" name="filter_month" class="form-control" value="<?php echo htmlspecialchars($filter_month); ?>" onchange="this.form.submit()">
        </div>

        <!-- Search -->
        <div class="col-md-3">
            <label class="form-label">Search</label>
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Name or contact" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </div>
        </div>
    </form>

    <!-- Visitor Table -->
    <table class="table table-striped table-hover mt-3 shadow-sm text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Date</th><th>Time</th><th>Name</th><th>Contact</th><th>Address</th><th>School/Office</th><th>Purpose</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo date("d M Y", strtotime($row['date'])); ?></td>
            <td><?php echo date("h:i A", strtotime($row['time'])); ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['contact']); ?></td>
            <td><?php echo htmlspecialchars($row['address']); ?></td>
            <td><?php echo htmlspecialchars($row['school_office']); ?></td>
           
            <td>
            <?php
                $purpose = trim($row['purpose']);
                switch($purpose) {
                    case 'Visit':   $badgeClass = 'bg-success'; $label = 'Visit'; break;
                    case 'Inquiry': $badgeClass = 'bg-primary'; $label = 'Inquiry'; break;
                    case 'Exam':    $badgeClass = 'bg-warning text-dark'; $label = 'Exam'; break;
                    default:        $badgeClass = 'bg-success'; $label = 'Visit'; break;
                }
            ?>
            <span class="badge <?php echo $badgeClass; ?>"><?php echo $label; ?></span>
            </td>

            <td>
                <a href="edit_visitor.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1">
                    <i class="bi bi-pencil-fill"></i>
                </a>
                <a href="delete_visitor.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this visitor?');">
                    <i class="bi bi-trash"></i>
                </a>
            </td>
        </tr>
        <?php } } else { ?>
        <tr><td colspan="8" class="text-center text-muted">No visitor records found.</td></tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Statistics Section -->
    <div class="row text-center mt-4">
        <!-- Today -->
        <div class="col-md-4">
            <div class="card shadow-sm" style="border-radius: 8px;">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #4facfe, #00f2fe); min-height: 150px;">
                    <i class="bi bi-calendar-check display-5 mb-2"></i>
                    <h6>Today</h6>
                    <p class="fs-2 mb-0"><?php echo $count_today; ?></p>
                </div>
            </div>
        </div>

        <!-- Exam -->
        <div class="col-md-4">
            <div class="card shadow-sm" style="border-radius: 8px;">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #fa709a, #fee140); min-height: 150px;">
                    <i class="bi bi-pencil-square display-5 mb-2"></i>
                    <h6>Exam</h6>
                    <p class="fs-2 mb-0"><?php echo $count_exam; ?></p>
                </div>
            </div>
        </div>

        <!-- Others -->
        <div class="col-md-4">
            <div class="card shadow-sm" style="border-radius: 8px;">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #667eea, #764ba2); min-height: 150px;">
                    <i class="bi bi-people display-5 mb-2"></i>
                    <h6>Others</h6>
                    <p class="fs-2 mb-0"><?php echo $count_others; ?></p>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
