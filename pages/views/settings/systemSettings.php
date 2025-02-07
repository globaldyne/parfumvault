<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');

if($role !== 1){
    die('You do not have permission to access this page');
}
?>

<div class="card-body row">
    <div class="col-12">
        <form id="systemSettingsForm">
            <?php
            $query = "SELECT * FROM system_settings";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $grouped_settings = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $prefix = explode('_', $row['key_name'])[0];
                    $grouped_settings[$prefix][] = $row;
                }

                function renderSettings($settings) {
                    foreach ($settings as $row) {
                        $label = $row['slug'];
                        $value = $row['value'];
                        $type = $row['type'];
                        $description = $row['description'];
                        $checked = ($type == 'checkbox' && $value == 1) ? 'checked' : '';
                        ?>
                        <div class="mb-3 mx-2 row form-floating">
                            <?php if ($type == 'checkbox') { ?>
                                <div class="form-check">
                                    <input type="hidden" name="<?php echo $row['key_name']; ?>" value="0">
                                    <input type="checkbox" class="form-check-input" id="<?php echo $row['key_name']; ?>" name="<?php echo $row['key_name']; ?>" value="1" <?php echo $checked; ?>>
                                    <label for="<?php echo $row['key_name']; ?>" class="form-check-label"><strong><?php echo $label; ?></strong> <i class="fa fa-info-circle" data-toggle="tooltip" title="<?php echo $description; ?>"></i></label>
                                </div>
                            <?php } elseif ($type == 'textarea') { ?>
                                <textarea class="form-control" id="<?php echo $row['key_name']; ?>" name="<?php echo $row['key_name']; ?>" rows="4"><?php echo $value; ?></textarea>
                                <label for="<?php echo $row['key_name']; ?>"><strong><?php echo $label; ?></strong> <i class="fa fa-info-circle pe-auto" data-toggle="tooltip" title="<?php echo $description; ?>"></i></label>
                            <?php } else { ?>
                                <input type="<?php echo $type; ?>" class="form-control" id="<?php echo $row['key_name']; ?>" name="<?php echo $row['key_name']; ?>" value="<?php echo $value; ?>">
                                <label for="<?php echo $row['key_name']; ?>"><strong><?php echo $label; ?></strong> <i class="fa fa-info-circle pe-auto" data-toggle="tooltip" title="<?php echo $description; ?>"></i></label>
                            <?php } ?>
                        </div>
                        <?php
                    }
                }

                echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">';
                foreach ($grouped_settings as $prefix => $settings) {
                    echo '<div class="col">
                            <div class="card shadow-sm p-3 mb-3">
                                <h5 class="card-title">' . ucfirst(strtolower($prefix)) . '</h5>';
                    renderSettings($settings);
                    echo '  </div>
                          </div>';
                }
                echo '</div>';
                ?>
                <div class="app-card-footer p-4 mt-auto">
                    <button type="submit" class="btn btn-primary" id="update_sys_settings">Save</button>
                </div>
                <?php
            } else {
                echo '<div class="alert alert-danger" role="alert">No system settings found.</div>';
            }
            ?>
        </form>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('#systemSettingsForm').submit(function (e) {
            e.preventDefault();
            var data = $(this).serializeArray();
            data.push({ name: 'request', value: 'updatesys' });
            $.ajax({
                url: '/core/core.php',
                type: 'POST',
                dataType: 'json',
                data: $.param(data),
                success: function (data) {
                    if(data.success){
                        $('#toast-title').html('<i class="fa-solid fa-circle-check mx-2"></i>' + data.success);
                        $('.toast-header').removeClass().addClass('toast-header alert-success');
                    } else if(data.error) {
                        $('#toast-title').html('<i class="fa-solid fa-warning mx-2"></i>' + data.error);
                        $('.toast-header').removeClass().addClass('toast-header alert-danger');
                    }
                    $('.toast').toast('show');
                },
                error: function(xhr, status, error) {
                    $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> An ' + status + ' occurred, check server logs for more info. '+ error);
			        $('.toast-header').removeClass().addClass('toast-header alert-danger');
			        $('.toast').toast('show');
                }
            });
        });
    });
</script>
