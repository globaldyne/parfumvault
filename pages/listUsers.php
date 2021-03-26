<?php 

require('../inc/sec.php');

require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');

$users_q = mysqli_query($conn, "SELECT * FROM users ORDER BY username ASC");

?>

<table width="100%" border="0">
  <tr>
    <td width="16%"><input name="username" placeholder="Username" type="text" class="form-control" id="username" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="password" placeholder="Password" type="password" class="form-control" id="password" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="fullName" placeholder="Full Name" type="text" class="form-control" id="fullName" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input name="email" placeholder="Email" type="text" class="form-control" id="email" /></td>
    <td width="1%">&nbsp;</td>
    <td width="16%"><input type="submit" name="add-user" id="add-user" value="Add" class="btn btn-info" /></td>
  </tr>
  <tr>
    <td colspan="9">&nbsp;</td>
  </tr>
</table>

<table class="table table-bordered" id="tdDataUsers" width="100%" cellspacing="0">
	<thead>
		<tr>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Actions</th>
		</tr>
  </thead>
  <tbody id="users">
	<?php while ($users = mysqli_fetch_array($users_q)) { ?>
		<tr>
            <td align="center"><?php echo $users['username'];?></td>
            <td align="center"><?php echo $users['fullName'];?></td>
            <td align="center"><?php echo $users['email'];?></td>
            <td align="center"><a href="pages/editUser.php?id=<?php echo $users['id']; ?>" class="fas fa-edit popup-link"></a> <a href="javascript:usrDel('<?php echo $users['id'];?>')" onclick="return confirm('Delete user <?php echo $users['username'];?>?')" class="fas fa-trash"></a></td>
		</tr>
	<?php } ?>
		</tr>
  </tbody>
</table>

<script type="text/javascript" language="javascript" >
$('#add-user').click(function() {
		$.ajax({ 
			url: 'pages/update_settings.php', 
			type: 'POST',
			data: {
				manage: 'user',
				username: $("#username").val(),
				password: $("#password").val(),
				fullName: $("#fullName").val(),
				email: $("#email").val(),
				},
			dataType: 'html',
			success: function (data) {
				$('#usrMsg').html(data);
				list_users();
			}
		  });
});	

$('#tdDataUsers').DataTable({
    "paging":   true,
	"info":   true,
	"lengthMenu": [[20, 35, 60, -1], [20, 35, 60, "All"]]
});

$('.popup-link').magnificPopup({
	type: 'iframe',
	closeOnContentClick: false,
	closeOnBgClick: false,
	showCloseBtn: true,
});
</script>
