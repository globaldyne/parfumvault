<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


if($role !== 1){
    echo 'Unauthorised';
    return;
}

?>

<div id="addUserForm">
    <div class="form-floating mb-3">
        <input type="email" class="form-control" name="email" id="email" required>
        <label for="email">Email</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" class="form-control" name="password" id="password" required>
        <label for="password">Password</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" class="form-control" name="full_name" id="full_name" required>
        <label for="full_name">Full name</label>
    </div>
    <div class="form-floating mb-3">
        <select class="form-select" name="role" id="role">
            <option value="1">Admin</option>
            <option value="2" selected>User</option>
        </select>
        <label for="role">Role</label>
    </div>
    <div class="form-check form-check-inline mb-3">
        <input type="checkbox" class="form-check-input" name="isActive" id="isActive">
        <label for="isActive" class="form-check-label">Active</label>
    </div>
    <div class="form-floating mb-3">
        <select class="form-select" name="country" id="country">
            <option value="">Choose your country</option>
            <?php
            $countries = json_decode($countriesJson, true);
            foreach ($countries as $country) {
                echo '<option value="' . htmlspecialchars($country['isoAlpha2']) . '">' . htmlspecialchars($country['name']) . '</option>';
            }
            ?>
        </select>
        <label for="country">Country</label>
    </div>
</div>


<script>

$(document).ready(function() {


$('#saveNewUser').click(function() {
    $('#addUserModalMsg').html('<div class="alert alert-info"><i class="bi bi-info-circle-fill mx-2"></i>Saving user...</div>');

    $.ajax({
        url: '/core/core.php',
        type: 'POST',
        data: {
            request: 'adduser',
            email: $('#addUserForm #email').val(),
            password: $('#addUserForm #password').val(),
            full_name: $('#addUserForm #full_name').val(),
            role: $('#addUserForm #role').val(),
            country: $('#addUserForm #country').val(),
            isActive: $('#addUserForm #isActive').is(':checked') ? 1 : 0,
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#addUserModalMsg').html('<div class="alert alert-success"><i class="bi bi-check-circle mx-2"></i>' + data.success + '</div>');
                $('#tdUsers').DataTable().ajax.reload(null, true);
            } else {
                $('#addUserModalMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-diamond mx-2"></i>' + data.error + '</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#addUserModalMsg').html('<div class="alert alert-danger"><i class="bi bi-exclamation-diamond mx-2"></i>An error occurred while saving user data. ' + error + '</div>');
        }
    });
});


});

</script>