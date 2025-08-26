<?php
// -------------------- Base Setup --------------------
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/authentication.php';

// -------------------- Auth Check --------------------
$user_id      = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
$user_name    = isset($_SESSION['name']) ? $_SESSION['name'] : null;
$security_key = isset($_SESSION['security_key']) ? $_SESSION['security_key'] : null;
$user_role    = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 0;

if (!$user_id || !$security_key) {
    die("Unauthorized");
}

// -------------------- Date Range --------------------
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d');
$to_date   = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

try {
    if ($user_role == 1) {
        $sql = "SELECT a.*, b.fullname 
                FROM task_info a
                INNER JOIN tbl_admin b ON a.t_user_id = b.user_id
                WHERE (DATE(a.t_start_time) BETWEEN :from_date AND :to_date
                       OR DATE(a.t_end_time) BETWEEN :from_date AND :to_date)
                ORDER BY a.task_id DESC";
        $stmt = $obj_admin->db->prepare($sql);
        $stmt->execute([':from_date'=>$from_date, ':to_date'=>$to_date]);
    } else {
        $sql = "SELECT a.*, b.fullname 
                FROM task_info a
                INNER JOIN tbl_admin b ON a.t_user_id = b.user_id
                WHERE a.t_user_id = :user_id
                  AND (DATE(a.t_start_time) BETWEEN :from_date AND :to_date
                       OR DATE(a.t_end_time) BETWEEN :from_date AND :to_date)
                ORDER BY a.task_id DESC";
        $stmt = $obj_admin->db->prepare($sql);
        $stmt->execute([':user_id'=>$user_id, ':from_date'=>$from_date, ':to_date'=>$to_date]);
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // -------------------- Export Excel --------------------
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=daily_task_report_{$from_date}_to_{$to_date}.xls");
    echo "<table border='1'>";
    echo "<tr>
            <th>No</th>
            <th>Task Title</th>
            <th>Task Category</th>
            <th>Technical Support</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
          </tr>";

    $no = 1;
    foreach ($rows as $row) {
        echo "<tr>";
        echo "<td>".$no++."</td>";
        echo "<td>".htmlspecialchars($row['t_title'])."</td>";
        echo "<td>".htmlspecialchars($row['t_category'])."</td>";
        echo "<td>".htmlspecialchars($row['fullname'])."</td>";
        echo "<td>".$row['t_start_time']."</td>";
        echo "<td>".$row['t_end_time']."</td>";
        $status = ($row['status']==0) ? 'In Completed' : (($row['status']==1)? 'In Progress' : 'Completed');
        echo "<td>".$status."</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch(PDOException $e){
    die("Export failed: ".$e->getMessage());
}
