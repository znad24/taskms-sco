<?php
require 'authentication.php'; // admin authentication check 

// ------------------ Auth Check ------------------
$user_id      = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
$user_name    = isset($_SESSION['name']) ? $_SESSION['name'] : null;
$security_key = isset($_SESSION['security_key']) ? $_SESSION['security_key'] : null;
$user_role    = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 0;

if (!$user_id || !$security_key) {
    header('Location: index.php');
    exit;
}

// ------------------ Delete Task ------------------
if (isset($_GET['delete_task']) && isset($_GET['task_id'])) {
    $task_id = intval($_GET['task_id']);
    $sql = "DELETE FROM task_info WHERE task_id = :id";
    $obj_admin->delete_data_by_this_method($sql, $task_id, "task-info.php");
    exit;
}

// ------------------ Add Task ------------------
if (isset($_POST['add_task_post'])) {
    $obj_admin->add_new_task($_POST);
    exit;
}

// ------------------ Sidebar ------------------
$page_name="Task_Info";
include("include/sidebar.php");

// ------------------ Paging Setup ------------------
$default_per_page = 10;
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : $default_per_page;
if (!in_array($per_page, array(10,15,20,25,50))) $per_page = $default_per_page;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

// ------------------ Search + Date Filter ------------------
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where = ' WHERE 1=1 ';
$params = array();

if ($search != '') {
    $where .= " AND (a.t_title LIKE :search OR a.t_category LIKE :search) ";
    $params[':search'] = "%$search%";
}
if ($start_date != '' && $end_date != '') {
    $where .= " AND DATE(a.t_start_time) BETWEEN :start_date AND :end_date ";
    $params[':start_date'] = $start_date;
    $params[':end_date']   = $end_date;
} elseif ($start_date != '') {
    $where .= " AND DATE(a.t_start_time) >= :start_date ";
    $params[':start_date'] = $start_date;
} elseif ($end_date != '') {
    $where .= " AND DATE(a.t_start_time) <= :end_date ";
    $params[':end_date'] = $end_date;
}

// ------------------ Total Data ------------------
if ($user_role == 1) {
    $sql_count = "SELECT COUNT(*) as total FROM task_info a $where";
} else {
    $where_user = " AND a.t_user_id = :user_id";
    $params[':user_id'] = $user_id;
    $sql_count = "SELECT COUNT(*) as total FROM task_info a $where $where_user";
}

$stmt_count = $obj_admin->db->prepare($sql_count);
foreach ($params as $k => $v) {
    $stmt_count->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt_count->execute();
$total_data = $stmt_count->fetchColumn();
$total_pages = ceil($total_data / $per_page);

// ------------------ Fetch Data ------------------
if ($user_role == 1) {
    $sql = "SELECT a.*, b.fullname 
            FROM task_info a
            INNER JOIN tbl_admin b ON a.t_user_id = b.user_id
            $where
            ORDER BY a.task_id DESC
            LIMIT :offset, :per_page";
} else {
    $sql = "SELECT a.*, b.fullname 
            FROM task_info a
            INNER JOIN tbl_admin b ON a.t_user_id = b.user_id
            $where $where_user
            ORDER BY a.task_id DESC
            LIMIT :offset, :per_page";
}

$stmt = $obj_admin->db->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);

$stmt->execute();
$info = $stmt;
?>

<div class="row">
  <div class="col-md-12">
    <div class="well well-custom rounded-0">
      <center><h3>Daily Task Report</h3></center>

      <!-- Search + Date + Per Page Form -->
      <form method="get" class="form-inline mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search task..." 
               value="<?php echo htmlspecialchars($search); ?>">

        <label class="mx-2">From:</label>
        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">

        <label class="mx-2">To:</label>
        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">

        <select name="per_page" class="form-control mx-2" onchange="this.form.submit()">
          <?php foreach(array(10,15,20,25,50) as $opt): ?>
            <option value="<?php echo $opt; ?>" <?php if($per_page==$opt) echo 'selected'; ?>>Show <?php echo $opt; ?></option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Apply</button>
      </form>
      
<div style="margin-top:15px;"></div>
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
            if($info->rowCount()==0){
                echo '<tr><td colspan="8">No Data found</td></tr>';
            } else {
                while($row = $info->fetch(PDO::FETCH_ASSOC)){
          ?>
                <tr>
                  <td><?php echo $serial++; ?></td>
                  <td><?php echo htmlspecialchars($row['t_title']); ?></td>
                  <td><?php echo htmlspecialchars($row['t_category']); ?></td>
                  <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                  <td><?php echo $row['t_start_time']; ?></td>
                  <td><?php echo $row['t_end_time']; ?></td>
                  <td>
                    <?php
                      $status_label = array(0=>'In Completed',1=>'In Progress',2=>'Completed');
                      $status_class = array(0=>'label-default',1=>'label-warning',2=>'label-success');
                      echo '<small class="label '.$status_class[$row['status']].' px-3">'.$status_label[$row['status']].'</small>';
                    ?>
                  </td>
                  <td>
                    <a href="edit-task.php?task_id=<?php echo $row['task_id']; ?>"><span class="glyphicon glyphicon-edit"></span></a>
                    &nbsp;
                    <a href="task-details.php?task_id=<?php echo $row['task_id']; ?>"><span class="glyphicon glyphicon-folder-open"></span></a>
                    &nbsp;
                    <?php if($user_role==1){ ?>
                      <a href="?delete_task=delete_task&task_id=<?php echo $row['task_id']; ?>" onclick="return check_delete();">
                        <span class="glyphicon glyphicon-trash"></span>
                      </a>
                    <?php } ?>
                  </td>
                </tr>
          <?php 
                }
            } 
          ?>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <nav aria-label="Page navigation">
        <ul class="pagination">
        <?php
          $query_params = array(
            'search'=>$search,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'per_page'=>$per_page
          );

          if($page>1){
              echo '<li><a href="?'.http_build_query($query_params).'&page='.($page-1).'">&laquo; Prev</a></li>';
          }

          for($i=max(1,$page-2); $i<=min($total_pages,$page+2); $i++){
              if($i==$page){
                  echo '<li class="active"><span>'.$i.'</span></li>';
              } else {
                  echo '<li><a href="?'.http_build_query($query_params).'&page='.$i.'">'.$i.'</a></li>';
              }
          }

          if($page<$total_pages){
              echo '<li><a href="?'.http_build_query($query_params).'&page='.($page+1).'">Next &raquo;</a></li>';
          }
        ?>
        </ul>
      </nav>
    </div>
  </div>
</div>

<?php include("include/footer.php"); ?>
