<?php

require 'authentication.php'; // admin authentication check 

// auth check
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
}

// check admin
$user_role = $_SESSION['user_role'];

if(isset($_GET['delete_task'])){
  $action_id = $_GET['task_id'];
  
  $sql = "DELETE FROM task_info WHERE task_id = :id";
  $sent_po = "task-info.php";
  $obj_admin->delete_data_by_this_method($sql,$action_id,$sent_po);
}

if(isset($_POST['add_task_post'])){
    $obj_admin->add_new_task($_POST);
}

$page_name="Task_Info";
include("include/sidebar.php");

// --- PAGING + FILTER SETUP ---
$default_per_page = 10;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : $default_per_page;
if (!in_array($per_page, [10, 15, 20, 25, 50])) $per_page = $default_per_page;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

// --- Search + Date Filter ---
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where_clause = '';
$params = [];

if ($search != '') {
    $where_clause .= " AND (a.t_title LIKE :search OR a.t_category LIKE :search) ";
    $params[':search'] = "%$search%";
}

// filter by date (menggunakan kolom t_start_time)
if ($start_date != '' && $end_date != '') {
    $where_clause .= " AND DATE(a.t_start_time) BETWEEN :start_date AND :end_date ";
    $params[':start_date'] = $start_date;
    $params[':end_date']   = $end_date;
} elseif ($start_date != '') {
    $where_clause .= " AND DATE(a.t_start_time) >= :start_date ";
    $params[':start_date'] = $start_date;
} elseif ($end_date != '') {
    $where_clause .= " AND DATE(a.t_start_time) <= :end_date ";
    $params[':end_date'] = $end_date;
}

// --- Hitung total data ---
if($user_role == 1){
    $sql_count = "SELECT COUNT(*) as total FROM task_info a WHERE 1=1 $where_clause";
} else {
    $sql_count = "SELECT COUNT(*) as total FROM task_info a WHERE a.t_user_id = $user_id $where_clause";
}
$stmt = $obj_admin->manage_all_info($sql_count);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->execute();
$total_row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $per_page);

// --- Query data sesuai paging ---
if($user_role == 1){
    $sql = "SELECT a.*, b.fullname 
            FROM task_info a
            INNER JOIN tbl_admin b ON(a.t_user_id = b.user_id)
            WHERE 1=1 $where_clause
            ORDER BY a.task_id DESC 
            LIMIT $offset, $per_page";
} else {
    $sql = "SELECT a.*, b.fullname 
            FROM task_info a
            INNER JOIN tbl_admin b ON(a.t_user_id = b.user_id)
            WHERE a.t_user_id = $user_id $where_clause
            ORDER BY a.task_id DESC 
            LIMIT $offset, $per_page";
}
$stmt = $obj_admin->manage_all_info($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->execute();
$info = $stmt;

?>

<div class="row">
  <div class="col-md-12">
    <div class="well well-custom rounded-0">
      <center><h3>Daily Task Report</h3></center>

      <!-- Search + Date + Per Page Form -->
      <form method="get" class="form-inline" style="margin-bottom:15px;">
        <div class="form-group">
          <input type="text" name="search" class="form-control" placeholder="Search task..." 
                 value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="form-group mx-sm-2">
          <label>From:</label>
          <input type="date" name="start_date" class="form-control"
                 value="<?php echo htmlspecialchars($start_date); ?>">
        </div>
        <div class="form-group mx-sm-2">
          <label>To:</label>
          <input type="date" name="end_date" class="form-control"
                 value="<?php echo htmlspecialchars($end_date); ?>">
        </div>
        <div class="form-group mx-sm-2">
          <select name="per_page" class="form-control" onchange="this.form.submit()">
            <option value="10" <?php if($per_page==10) echo 'selected'; ?>>Show 10</option>
            <option value="15" <?php if($per_page==15) echo 'selected'; ?>>Show 15</option>
            <option value="20" <?php if($per_page==20) echo 'selected'; ?>>Show 20</option>
            <option value="25" <?php if($per_page==25) echo 'selected'; ?>>Show 25</option>
            <option value="50" <?php if($per_page==50) echo 'selected'; ?>>Show 50</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Apply</button>
      </form>

      <div class="table-responsive">
        <table class="table table-codensed table-custom">
          <thead>
            <tr>
              <th>No</th>
              <th>Task Title</th>
              <th>Task Category</th>
              <th>Technical Support</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php 
            $serial = $offset + 1;
            $num_row = $info->rowCount();
            if($num_row == 0){
              echo '<tr><td colspan="8">No Data found</td></tr>';
            }
            while($row = $info->fetch(PDO::FETCH_ASSOC)){
          ?>
            <tr>
              <td><?php echo $serial++; ?></td>
              <td><?php echo $row['t_title']; ?></td>
              <td><?php echo $row['t_category']; ?></td>
              <td><?php echo $row['fullname']; ?></td>
              <td><?php echo $row['t_start_time']; ?></td>
              <td><?php echo $row['t_end_time']; ?></td>
              <td>
                <?php  
                  if($row['status'] == 0){
                    echo '<small class="label label-default border px-3">In Completed</small>';
                  }elseif ($row['status'] == 1){
                    echo '<small class="label label-warning px-3">In Progress</small>';		
                  }elseif($row['status'] == 2){
                    echo '<small class="label label-success px-3">Completed</small>';
                  }
                ?>
              </td>
              <td>
                <a href="edit-task.php?task_id=<?php echo $row['task_id'];?>" title="Update Task"><span class="glyphicon glyphicon-edit"></span></a>
                &nbsp;
                <a href="task-details.php?task_id=<?php echo $row['task_id'];?>" title="View"><span class="glyphicon glyphicon-folder-open"></span></a>
                &nbsp;
                <?php if($user_role == 1){ ?>
                <a href="?delete_task=delete_task&task_id=<?php echo $row['task_id']; ?>" onclick=" return check_delete();" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <?php 
          // bikin query string untuk search & date supaya ikut di link pagination
          $query_string = http_build_query([
              'search' => $search,
              'start_date' => $start_date,
              'end_date' => $end_date,
              'per_page' => $per_page
          ]);
          ?>

          <?php if($page > 1): ?>
            <li><a href="?<?php echo $query_string; ?>&page=<?php echo $page-1; ?>">&laquo; Prev</a></li>
          <?php endif; ?>

          <?php
            if ($page > 3) {
                echo '<li><a href="?'.$query_string.'&page=1">1</a></li>';
                if ($page > 4) {
                    echo '<li class="disabled"><span>...</span></li>';
                }
            }
            for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) {
                if ($i == $page) {
                    echo '<li class="active"><span>'.$i.'</span></li>';
                } else {
                    echo '<li><a href="?'.$query_string.'&page='.$i.'">'.$i.'</a></li>';
                }
            }
            if ($page < $total_pages - 2) {
                if ($page < $total_pages - 3) {
                    echo '<li class="disabled"><span>...</span></li>';
                }
                echo '<li><a href="?'.$query_string.'&page='.$total_pages.'">'.$total_pages.'</a></li>';
            }
          ?>

          <?php if($page < $total_pages): ?>
            <li><a href="?<?php echo $query_string; ?>&page=<?php echo $page+1; ?>">Next &raquo;</a></li>
          <?php endif; ?>
        </ul>
      </nav>

    </div>
  </div>
</div>

<?php include("include/footer.php"); ?>
