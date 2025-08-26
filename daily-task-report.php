<?php
// -------------------- Base Setup --------------------
if (session_status() == PHP_SESSION_NONE) session_start();

// Base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
$base_url = $protocol . "://" . $_SERVER['SERVER_NAME'] . '/' . explode('/', $_SERVER['PHP_SELF'])[1] . '/';

// Include admin class & authentication
require_once __DIR__ . '/authentication.php';

// -------------------- Auth Check --------------------
$user_id      = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
$user_name    = isset($_SESSION['name']) ? $_SESSION['name'] : null;
$security_key = isset($_SESSION['security_key']) ? $_SESSION['security_key'] : null;
$user_role    = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 0;

if (!$user_id || !$security_key) {
    header('Location: index.php');
    exit;
}

// -------------------- Delete Task --------------------
if (isset($_GET['delete_task']) && isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $sql = "DELETE FROM task_info WHERE task_id = :id";
    $obj_admin->delete_data_by_this_method($sql, $task_id, "daily-task-report.php");
    exit;
}

// -------------------- Add Task --------------------
if (isset($_POST['add_task_post'])) {
    $obj_admin->add_new_task($_POST);
    exit;
}

// -------------------- Include Sidebar --------------------
$page_name = "Daily-Task-Report";
include(__DIR__ . "/include/sidebar.php");

// -------------------- Date Range --------------------
$from_date = (isset($_GET['from_date']) && $_GET['from_date'] != '') ? $_GET['from_date'] : date('Y-m-d');
$to_date   = (isset($_GET['to_date']) && $_GET['to_date'] != '') ? $_GET['to_date'] : date('Y-m-d');

// -------------------- Limit / Paging Setup --------------------
$limit_options = [10,25,50];
$limit = (isset($_GET['limit']) && in_array(intval($_GET['limit']), $limit_options)) ? intval($_GET['limit']) : 10;
$page  = (isset($_GET['page']) && $_GET['page'] > 0) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// -------------------- Total Data Count --------------------
try {
    if ($user_role == 1) {
        $count_sql = "SELECT COUNT(*) as total 
                      FROM task_info a
                      INNER JOIN tbl_admin b ON a.t_user_id = b.user_id
                      WHERE (DATE(a.t_start_time) BETWEEN :from_date AND :to_date
                             OR DATE(a.t_end_time) BETWEEN :from_date AND :to_date)";
        $stmt_count = $obj_admin->db->prepare($count_sql);
        $stmt_count->execute([':from_date'=>$from_date, ':to_date'=>$to_date]);
    } else {
        $count_sql = "SELECT COUNT(*) as total 
                      FROM task_info a
                      INNER JOIN tbl_admin b ON a.t_user_id = b.user_id
                      WHERE a.t_user_id = :user_id
                        AND (DATE(a.t_start_time) BETWEEN :from_date AND :to_date
                             OR DATE(a.t_end_time) BETWEEN :from_date AND :to_date)";
        $stmt_count = $obj_admin->db->prepare($count_sql);
        $stmt_count->execute([':user_id'=>$user_id, ':from_date'=>$from_date, ':to_date'=>$to_date]);
    }
    $row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $total_data = $row_count['total'];
    $total_pages = ceil($total_data / $limit);
} catch (PDOException $e) {
    echo "Error counting data: " . $e->getMessage();
    $total_data = 0;
    $total_pages = 1;
}

// -------------------- Fetch Data --------------------
try {
    if ($user_role == 1) {
        $sql = "SELECT a.*, b.fullname 
                FROM task_info a
                INNER JOIN tbl_admin b ON a.t_user_id = b.user_id
                WHERE (DATE(a.t_start_time) BETWEEN :from_date AND :to_date
                       OR DATE(a.t_end_time) BETWEEN :from_date AND :to_date)
                ORDER BY a.task_id DESC
                LIMIT $offset, $limit";
        $stmt = $obj_admin->db->prepare($sql);
        $stmt->execute([':from_date'=>$from_date, ':to_date'=>$to_date]);
    } else {
        $sql = "SELECT a.*, b.fullname 
                FROM task_info a
                INNER JOIN tbl_admin b ON a.t_user_id = b.user_id
                WHERE a.t_user_id = :user_id
                  AND (DATE(a.t_start_time) BETWEEN :from_date AND :to_date
                       OR DATE(a.t_end_time) BETWEEN :from_date AND :to_date)
                ORDER BY a.task_id DESC
                LIMIT $offset, $limit";
        $stmt = $obj_admin->db->prepare($sql);
        $stmt->execute([':user_id'=>$user_id, ':from_date'=>$from_date, ':to_date'=>$to_date]);
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database query failed: " . $e->getMessage();
    $rows = [];
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="row">
    <div class="col-md-12">
        <div class="well well-custom rounded-0">

            <div class="row mb-3">
                <div class="col-md-2">
                    <select id="select_limit" class="form-control">
                        <?php foreach($limit_options as $opt): ?>
                            <option value="<?= $opt ?>" <?= $opt==$limit?'selected':'' ?>>Show <?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" id="from_date" value="<?= $from_date ?>" class="form-control rounded-0">
                </div>
                <div class="col-md-3">
                    <input type="date" id="to_date" value="<?= $to_date ?>" class="form-control rounded-0">
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary btn-sm" id="filter"><i class="glyphicon glyphicon-filter"></i> Search</button>
                    <button class="btn btn-success btn-sm" id="print"><i class="glyphicon glyphicon-print"></i> Print PDF</button>
                    <a href="export_task.php?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>">
                        <button class="btn btn-info btn-sm"><i class="glyphicon glyphicon-download"></i> Export Excel</button>
                    </a>
                </div>
            </div>

            <center><h3>Daily Task Report</h3></center>

            <div class="table-responsive" id="printout">
                <table class="table table-condensed table-custom table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Task Title</th>
                            <th>Task Category</th>
                            <th>Technical Support</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(count($rows)==0): ?>
                        <tr><td colspan="7">No Data Found</td></tr>
                    <?php else:
                        $serial = $offset+1;
                        foreach($rows as $row): ?>
                        <tr>
                            <td><?= $serial++ ?></td>
                            <td><?= htmlspecialchars($row['t_title']) ?></td>
                            <td><?= htmlspecialchars($row['t_category']) ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= $row['t_start_time'] ?></td>
                            <td><?= $row['t_end_time'] ?></td>
                            <td>
                                <?php
                                if ($row['status']==0) echo '<small class="label label-default border px-3">In Completed <span class="glyphicon glyphicon-remove"></span></small>';
                                elseif ($row['status']==1) echo '<small class="label label-warning px-3">In Progress <span class="glyphicon glyphicon-refresh"></span></small>';
                                elseif ($row['status']==2) echo '<small class="label label-success px-3">Completed <span class="glyphicon glyphicon-ok"></span></small>';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paging Links -->
            <nav aria-label="Page navigation">
              <ul class="pagination justify-content-center">
                <?php
                $prev = $page-1; if($prev<1)$prev=1;
                echo '<li class="page-item '.($page==1?'disabled':'').'">
                        <a class="page-link" href="?from_date='.$from_date.'&to_date='.$to_date.'&limit='.$limit.'&page='.$prev.'">Previous</a></li>';

                $start = max(1,$page-2); $end = min($total_pages,$page+2);
                if($start>1) echo '<li class="page-item"><span class="page-link">...</span></li>';
                for($p=$start;$p<=$end;$p++){
                    $active = ($p==$page)?'active':'';
                    echo '<li class="page-item '.$active.'">
                            <a class="page-link" href="?from_date='.$from_date.'&to_date='.$to_date.'&limit='.$limit.'&page='.$p.'">'.$p.'</a></li>';
                }
                if($end<$total_pages) echo '<li class="page-item"><span class="page-link">...</span></li>';

                $next = $page+1; if($next>$total_pages)$next=$total_pages;
                echo '<li class="page-item '.($page==$total_pages?'disabled':'').'">
                        <a class="page-link" href="?from_date='.$from_date.'&to_date='.$to_date.'&limit='.$limit.'&page='.$next.'">Next</a></li>';
                ?>
              </ul>
            </nav>

        </div>
    </div>
</div>

<script>
$(function(){
    $('#filter').click(function(){
        var limit = $('#select_limit').val();
        location.href = "./daily-task-report.php?from_date="+$('#from_date').val()+"&to_date="+$('#to_date').val()+"&limit="+limit;
    });

    $('#select_limit').change(function(){
        $('#filter').click();
    });

    $('#print').click(function(){
        var h = $('head').clone();
        var p = $('#printout').clone();
        var nw = window.open("", "_blank", "width:"+($(window).width()*.8)+",left:"+($(window).width()*.1)+",height:"+($(window).height()*.8)+",top:"+($(window).height()*.1));
        nw.document.querySelector('head').innerHTML = h.html();
        nw.document.querySelector('body').innerHTML = p[0].outerHTML;
        nw.document.close();
        setTimeout(function(){ nw.print(); setTimeout(function(){ nw.close(); },200); },200);
    });
});
</script>
