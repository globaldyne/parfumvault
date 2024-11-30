<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 
define('pvault_panel', TRUE);

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/arrFilter.php');
require_once(__ROOT__.'/func/get_formula_notes.php');

if(!$_GET['id']){
	echo 'Formula id is missing.';
	return;
}
	
$fid = mysqli_real_escape_string($conn, $_GET['id']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulas WHERE fid = '$fid'")) == 0){
	echo '<div class="alert alert-info">Incomplete formula. Please add ingredients.</div>';
	return;
}

$description = mysqli_fetch_array(mysqli_query($conn, "SELECT notes FROM formulasMetaData WHERE fid = '$fid'"));

$top_cat = get_formula_notes($conn, $fid, 'top');
$heart_cat = get_formula_notes($conn, $fid, 'heart');
$base_cat = get_formula_notes($conn, $fid, 'base');

$top_ex = get_formula_excludes($conn, $fid, 'top');
$heart_ex = get_formula_excludes($conn, $fid, 'heart');
$base_ex = get_formula_excludes($conn, $fid, 'base');

?>
<style>
.img_ing {
    max-height: 40px;
}

.img_ing_sel {
    max-height: 30px;
	max-width: 30px;
	padding: 0 10px 0 0;
}

figure {
    display: inline;
    border: none;
    margin: 25px;
}

figure img {
    vertical-align: top;
}
figure figcaption {
    border: none;
    text-align: center;
}

formula td, table.table th {
	white-space: revert;
}

#notes_summary_view td {
	display: inline-block;	
}
</style>

<div id="notes_summary_view">
    <?php 
    function render_notes($title, $categories, $excludes) {
        if (!$categories) return;

        echo "<table border='0'>";
        echo "<tr><td colspan='2' height='30' align='left'><strong>{$title}</strong></td></tr>";
        echo "<tr>";

        foreach ($categories as $item) {
            if ($excludes && in_array($item['name'], $excludes)) {
                continue;
            }

            echo "<td>
                    <figure>
                        <img class='img_ing' src='{$item['image']}' alt='{$item['name']}' />
                        <figcaption>{$item['name']}</figcaption>
                    </figure>
                  </td>";
        }

        echo "</tr></table>";
    }

    render_notes("Top Notes", $top_cat ?? [], $top_ex ?? []);
    render_notes("Heart Notes", $heart_cat ?? [], $heart_ex ?? []);
    render_notes("Base Notes", $base_cat ?? [], $base_ex ?? []);
    ?>

    <?php if (!empty($description['notes']) && ($_GET['no_description'] ?? '0') != '1') : ?>
        <table width="50%" border="0">
            <tr>
                <td><?= htmlspecialchars($description['notes']) ?></td>
            </tr>
        </table>
    <?php endif; ?>
</div>

<?php if (!isset($_GET['embed'])) : ?>
<!-- Configure View Modal -->
<div class="modal fade" id="conf_view" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="conf_view" aria-hidden="true">
    <div class="modal-dialog modal-conf-view" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Choose which notes will be displayed</h5>
        </div>
        <div class="modal-body">
            <div id="confViewMsg"></div>
                <?php 
                function render_config_table($title, $categories, $excludes, $inputName) {
                    echo "<div class='conf_tbl'><table width='100%' border='0'>";
                    echo "<tr><td colspan='2'><strong>{$title}</strong><hr /></td></tr>";

                    foreach ($categories as $item) {
                        $checked = !in_array($item['name'], $excludes) ? "checked='checked'" : "";
                        $inputId = str_replace(' ', '_', $item['ing']);
                        echo "<tr>
                                <td width='54%' ex_{$inputName}_ing_name='{$item['name']}'>{$item['name']}</td>
                                <td width='46%'>
                                    <input name='ex_{$inputName}_ing' class='ex_ing' type='checkbox' id='{$inputId}' value='{$inputId}' {$checked} />
                                </td>
                              </tr>";
                    }

                    echo "</table></div>";
                }

                render_config_table("Top Notes", $top_cat ?? [], $top_ex ?? [], "top");
                render_config_table("Heart Notes", $heart_cat ?? [], $heart_ex ?? [], "heart");
                render_config_table("Base Notes", $base_cat ?? [], $base_ex ?? [], "base");
                ?>
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" id="btnUpdateView" value="Save">
            </div>
        </div>
    </div>
</div>

<script>
$('#btnUpdateView').click(function() {
	$('.ex_ing').each(function () {
		const data = {
			fid: '<?= $fid ?>',
			manage_view: '1',
			ex_status: $("#" + $(this).val()).is(':checked') ? 1 : 0,
			ex_ing: $(this).val()
		};

		$.ajax({
			url: '/core/core.php',
			type: 'GET',
			data,
			dataType: 'json',
			success: function (response) {
				if (response.success) {
					fetch_summary();
					$('#conf_view').modal('hide');
				} else {
					$('#confViewMsg').html(
						`<div class="alert alert-danger"><strong>${response.error}</strong></div>`
					);
				}
			},
			error: function (xhr, status, error) {
				$('#confViewMsg').html(
					`<div class="alert alert-danger">An error occurred: ${status}. ${error}</div>`
				);
			}
		});
	});
});
</script>
<?php endif; ?>
