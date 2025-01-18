<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


if($role !== 1){
    echo 'Unauthorised';
    return;
}

if (!$_GET['id']) {
    $response['error'] = 'User ID is required.';
	echo json_encode($response);
	return;
} 

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_array($result, MYSQLI_ASSOC);

if (!$user) {
    $response['error'] = 'User not found.';
    echo json_encode($response);
    return;
}
$countries = json_decode(file_get_contents(__ROOT__.'/db/countries.json'), true);

?>
<div id="editUserForm">
    <div class="form-floating mb-3">
        <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <label for="email">Email</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" class="form-control" name="password" id="password" required>
        <label for="password">Password</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" class="form-control" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user['fullName']); ?>" required>
        <label for="full_name">First Name</label>
    </div>
    <div class="form-floating mb-3">
        <select class="form-select" name="role" id="role">
            <option value="1" <?php echo $user['role'] == '1' ? 'selected' : ''; ?>>Admin</option>
            <option value="2" <?php echo $user['role'] == '2' ? 'selected' : ''; ?>>User</option>
        </select>
        <label for="role">Role</label>
    </div>
    <div class="form-check form-check-inline mb-3">
        <input type="checkbox" class="form-check-input" name="isActive" id="isActive" <?php echo $user['isActive'] ? 'checked' : ''; ?>>
        <label for="isActive" class="form-check-label">Active</label>
    </div>
    <div class="form-floating mb-3">
        <select class="form-select" name="country" id="country">
            <option value="">Choose your country</option>
            <?php foreach ($countries as $country): ?>
                <option value="<?php echo htmlspecialchars($country['isoAlpha2']); ?>" <?php echo $user['country'] == $country['isoAlpha2'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($country['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="country">Country</label>
    </div>
</div>


<script>
$(document).ready(function() {

    $('#saveUserChanges').click(function() {
        $('#editUserModalMsg').html('<div class="alert alert-info"><i class="bi bi-info-circle-fill mx-2"></i>Saving changes...</div>');
        var id = $('#editUserModal #id').val();
        
        var email = $('#editUserForm #email').val();
        var password = $('#editUserForm #password').val();
        var full_name = $('#editUserForm #full_name').val();
        var role = $('#editUserForm #role').val();
        var country = $('#editUserForm #country').val();
        var isActive = $('#editUserForm #isActive').is(':checked') ? 1 : 0;

        $.ajax({
            url: '/core/core.php',
            type: 'POST',
            data: {
                request: 'updateuser',
                user_id: id,
                email: email,
                password: password,
                full_name: full_name,
                role: role,
                country: country,
                isActive: isActive,
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // $('#editUserModal').modal('hide');
                    $('#editUserModalMsg').html('<div class="alert alert-success"><i class="bi bi-check-circle mx-2"></i>' + data.success + '</div>');
                    $('#tdUsers').DataTable().ajax.reload(null, true);
                } else {
                    $('#editUserModalMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-diamond mx-2"></i>' + data.error + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#editUserModalMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-diamond mx-2"></i>An error occurred while updating user data. ' + error + '</div>');
            }
        });
    });
});

</script>