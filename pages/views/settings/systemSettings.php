<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/settings.php');


?>

<div class="card-body row">
    <div class="col-sm-6">
        <div class="row">        <!-- Profile Section -->
        <div id="msg"></div>
        <div class="col-12">
            <?php
            $query = "SELECT * FROM system_settings";
            $result = mysqli_query($conn, $query);

            if ($result) {
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
                        $checked = ($type == 'checkbox' && $value == 1) ? 'checked' : '';
                        ?>
                        <div class="mb-3 mx-2 row form-floating">
                            <?php if ($type == 'checkbox') { ?>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="<?php echo $row['key_name']; ?>" name="<?php echo $row['key_name']; ?>" <?php echo $checked; ?>>
                                    <label for="<?php echo $row['key_name']; ?>" class="form-check-label"><strong><?php echo $label; ?></strong></label>
                                </div>
                            <?php } else { ?>
                                <input type="<?php echo $type; ?>" class="form-control" id="<?php echo $row['key_name']; ?>" name="<?php echo $row['key_name']; ?>" value="<?php echo $value; ?>" required>
                                <label for="<?php echo $row['key_name']; ?>"><strong><?php echo $label; ?></strong></label>
                            <?php } ?>
                        </div>
                        <?php
                    }
                }

                echo '<div class="row">';
                foreach ($grouped_settings as $prefix => $settings) {
                    echo '<div class="col-md-6"><h4 class="mb-3">' . strtoupper($prefix) . ' Settings</h4>';
                    renderSettings($settings);
                    echo '</div>';
                }
                echo '</div>';
            }
            ?>
            <div class="app-card-footer p-4 mt-auto">
                <button type="submit" class="btn btn-primary" id="update_sys_settings">Save</button>
            </div>
        </div>
    </div>
    </div>
</div>
</div>

<script>
    $(document).ready(function () {
        $('#update_sys_settings').click(function () {
            var data = { request: 'updatesys' };
            $('input').each(function () {
                var key_name = $(this).attr('name');
                var value = $(this).val();
                if ($(this).attr('type') == 'checkbox') {
                    value = $(this).is(':checked') ? 1 : 0;
                }
                data[key_name] = value;
            });
            $.ajax({
                url: '/core/core.php',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (response) {
                    if(response.success){
                        $('#msg').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<i class="bi bi-check-circle-fill"></i> ' + response.success +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                    }else{
                        $('#msg').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<i class="bi bi-exclamation-triangle-fill"></i> ' + response.error +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#msg').html('<div class="alert alert-danger">' + error + '</div>');
                }
            });
        });
    });
</script>