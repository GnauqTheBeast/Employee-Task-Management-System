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
if($user_role != 1){
  header('Location: task-info.php');
}


if(isset($_GET['delete_user'])){
  $action_id = $_GET['admin_id'];

  $task_sql = "DELETE FROM task_info WHERE t_user_id = $action_id";
  $delete_task = $obj_admin->db->prepare($task_sql);
  $delete_task->execute();

  $attendance_sql = "DELETE FROM attendance_info WHERE atn_user_id = $action_id";
  $delete_attendance = $obj_admin->db->prepare($attendance_sql);
  $delete_attendance->execute();
  
  $sql = "DELETE FROM tbl_admin WHERE user_id = :id";
  $sent_po = "admin-manage-user.php";
  $obj_admin->delete_data_by_this_method($sql,$action_id,$sent_po);
}

$page_name="Admin";
include("include/sidebar.php");

if(isset($_POST['add_new_employee'])){
  $error = $obj_admin->add_new_user($_POST);
}

?>



<!--modal for employee add-->
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h2 class="modal-title text-center">Thêm thông tin nhân viên</h2>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <?php if(isset($error)){ ?>
                <h5 class="alert alert-danger"><?php echo $error; ?></h5>
                <?php } ?>
              <form role="form" action="" method="post" autocomplete="off">
                <div class="form-horizontal">

                  <div class="form-group">
                    <label class="control-label text-p-reset">Họ và tên</label>
                    <div class="">
                      <input type="text"name="em_fullname" list="expense" class="form-control input-custom" id="default" required>
                    </div>
                  </div>
                   <div class="form-group">
                    <label class="control-label text-p-reset">Tên đăng nhập</label>
                    <div class="">
                      <input type="text" name="em_username" class="form-control input-custom" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label text-p-reset">Email</label>
                    <div class="">
                      <input type="email" name="em_email" class="form-control input-custom" required>
                    </div>
                  </div>
                  
                 
                  
                  <div class="form-group">
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-3">
                      <button type="submit" name="add_new_employee" class="btn btn-primary btn-sm rounded-0">Thêm nhân viên mới</button>
                    </div>
                    <div class="col-sm-3">
                      <button type="submit" class="btn btn-default btn-sm rounded-0" data-dismiss="modal">Huỷ</button>
                    </div>
                  </div>
                </div>
              </form> 
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>



<!--modal for employee add-->



    <div class="row">
      <div class="col-md-12">
        <div class="row">
            
        <div class="well well-custom">
          <?php if(isset($error)){ ?>
          <script type="text/javascript">
            $('#myModal').modal('show');
          </script>
          <?php } ?>
            <?php if($user_role == 1){ ?>
                <div class="btn-group">
                  <button class="btn btn-primary-custom btn-menu" data-toggle="modal" data-target="#myModal">Thêm nhân viên mới</button>
                </div>
              <?php } ?>
          <ul class="nav nav-tabs nav-justified nav-tabs-custom">
            <li><a href="manage-admin.php">Quản lý người quản trị</a></li>
            <li class="active"><a href="admin-manage-user.php">Quản lý nhân viên</a></li>
          </ul>
          <div class="gap"></div>
          <div class="table-responsive">
            <table class="table table-codensed table-custom">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Họ và tên</th>
                  <th>Email</th>
                  <th>Tài khoản</th>
                  <th>Mật khẩu tạm thời </th>
                  <th>Thông tin</th>
                </tr>
              </thead>
              <tbody>

              <?php 
                $sql = "SELECT * FROM tbl_admin WHERE user_role = 2 ORDER BY user_id DESC";
                $info = $obj_admin->manage_all_info($sql);
                $serial  = 1;
                $num_row = $info->rowCount();
                  if($num_row==0){
                    echo '<tr><td colspan="7">No Data found</td></tr>';
                  }
                while( $row = $info->fetch(PDO::FETCH_ASSOC) ){
              ?>
                <tr>
                  <td><?php echo $serial; $serial++; ?></td>
                  <td><?php echo $row['fullname']; ?></td>
                  <td><?php echo $row['email']; ?></td>
                  <td><?php echo $row['username']; ?></td>
                  <td><?php echo $row['temp_password']; ?></td>
                  
                  <td><a title="Update Employee" href="update-employee.php?admin_id=<?php echo $row['user_id']; ?>"><span class="glyphicon glyphicon-edit"></span></a>&nbsp;&nbsp;<a title="Delete" href="?delete_user=delete_user&admin_id=<?php echo $row['user_id']; ?>" onclick=" return check_delete();"><span class="glyphicon glyphicon-trash"></span></a></td>
                </tr>
                
              <?php  } ?>


                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


<?php
if(isset($_SESSION['update_user_pass'])){

  echo '<script>alert("Password updated successfully");</script>';
  unset($_SESSION['update_user_pass']);
}
include("include/footer.php");

?>