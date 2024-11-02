<?php
if (!defined('pvault_panel')){ die('Not Found');}
$doc = mysqli_fetch_array(mysqli_query($conn,"SELECT docData AS avatar FROM documents WHERE ownerID = '".$_SESSION['userID']."' AND name = 'avatar' AND type = '3'"));

$db_ver = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
if($pv_meta['schema_ver'] < $db_ver){
	$show_db_upgrade = true;
}

?>
<div id="chkUpdMsg"></div>
<div id="content">
        <nav class="navbar navbar-expand bg-gradient-primary-navbar topbar mb-4 static-top shadow">
          <ul class="navbar-nav vault-top ml-auto">
            <!-- Cart -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-shopping-cart fa-fw text-white"></i>
                <!-- Counter - cart -->
                <span class="badge badge-danger badge-counter"><?php echo countCart(); ?></span>
              </a>
              <!-- Dropdown - cart -->
              <div class="dropdown-list dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM cart GROUP BY name"))){ ?>
				<a href="/?do=cart" class="dropdown-header">To be ordered</a>
				<?php
					$qC = mysqli_query($conn, "SELECT name,ingID FROM cart ORDER BY name ASC LIMIT 5");
					while ($pC = mysqli_fetch_array($qC)){
						$supDetails = getPrefSupplier($pC['ingID'],$conn);
				?>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo $supDetails['supplierLink'];?>" target="_blank">
                  <div class="font-weight-bold">
                    <div class="text-truncate"><?php echo $pC['name'];?></div>
                    <div class="small text-gray-500"><?php echo $supDetails['name'];?></div>
                  </div>
                </a>
				<?php } ?>
	            <a class="dropdown-item text-center small text-gray-500" href="/?do=cart">See all...</a>

				<?php }else{ ?>
                <a class="dropdown-item text-center small text-gray-500" href="/?do=cart">No orders to place</a>
				<?php } ?>	
                </div>
            </li>
            
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
            	<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mx-2 d-none d-lg-inline text-white small"><?php echo $user['fullName'];?></span>
               	<div class="icon-container">
                     <?php if ($doc['avatar']){ ?>
                        <img src="<?=$doc['avatar']?: '/img/ICO_TR.png'; ?>" class="img-profile rounded-circle">
                    <?php } else { ?>
						<i class="fa-regular fa-user fa-2xl text-info"></i>
                   <?php } ?>
				</div>
              </a>
              <div class="mx-2 dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
              
				<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editUser">
                  <i class="fas fa-user fa-sm fa-fw mx-2 text-gray-400"></i>
                  Edit my details
                </a>
                
                <a class="dropdown-item" href="/?do=settings">
                  <i class="fas fa-cogs fa-sm fa-fw mx-2 text-gray-400"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#calcTools">
                  <i class="fas fa-tools fa-sm fa-fw mx-2 text-gray-400"></i>
                  Calculation Tools
                </a>
                
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://discord.gg/WxNE8kR8ug" target="_blank">
                  <i class="fas fa-book fa-sm fa-fw mx-2 text-gray-400"></i>
                  Join our Discord Server
                </a>
                
                <a class="dropdown-item" href="https://www.perfumersvault.com/knowledge-base" target="_blank">
                  <i class="fas fa-book fa-sm fa-fw mx-2 text-gray-400"></i>
                  Documentation
                </a>
                <a class="dropdown-item" href="https://github.com/globaldyne/parfumvault/issues" target="_blank">
                  <i class="fas fa-lightbulb fa-sm fa-fw mx-2 text-gray-400"></i>
                  Bug report
                </a>             
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://apps.apple.com/us/app/id1525381567" target="_blank">
                  <i class="fab fa-apple fa-sm fa-fw mx-2 text-gray-400"></i>
                  App Store
                </a>              
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mx-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
<div id="msg"></div>
</nav>

<script>
$(document).ready(function() {

	$('#load-rel-notes').click(function() {
		var relUrl = 'https://raw.githubusercontent.com/globaldyne/parfumvault/master/releasenotes.md';
	
		$('#new-rel').load(relUrl);
		//console.log(relUrl);
	});
	
	<?php if($show_db_upgrade){?>
		$('#dbUpgradeDialog').modal('show');
		$('#dbUpOk').hide();
	<?php } ?>

	$(function() {
		$.ajax({ 
			url: '/pages/views/tools/calcTools.php', 
			type: 'GET',
			dataType: 'html',
			success: function (data) {
				$('.toolsHtml').html(data);
			}
		  });
	});
	
	$("#editUser").on("show.bs.modal", function(e) {
	  $.get("/pages/editUser.php")
		.then(data => {
		  $(".modal-body", this).html(data);
		});
	});
	
});//END DOC
</script>

<!--EDIT USER PROFILE MODAL-->            
<div class="modal fade" id="editUser" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserLabel">Edit my details</h5>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">Unable to get data</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- calcTools Modal -->
<div class="modal fade" id="calcTools" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="calcTools" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">Calculation tools
            </div>
            <div class="modal-body">
            	<div class="toolsHtml"></div>
            </div>
           	<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
        </div>
    </div>
</div>
<!-- /calcTools Modal -->

<!-- DB UPGRADE MODAL -->
<div class="modal fade" id="dbUpgradeDialog" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="dbUpgradeDialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Database Schema Upgrade</h5>
      </div>
      <div class="modal-body" id="dbUpdMsg">
        <div class="alert alert-warning"><strong>Your database schema needs to be upgraded to version <?php echo $db_ver; ?>. Please backup your database first and then click the upgrade button.</strong>
        </div>
      </div>
      <div class="modal-footer">
        <a href="/pages/operations.php?do=backupDB" role="button" class="btn btn-primary" id="dbBkBtn">Backup Database</a>
        <button type="button" class="btn btn-warning" id="dbUpBtn">Upgrade Schema</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="dbUpOk">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /DB UPGRADE MODAL -->

<!-- SYS UPGRADE MODAL -->
<div class="modal fade" id="sysUpgradeDialog" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="sysUpgradeDialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">PVault core Upgrade</h5>
      </div>
      <div class="modal-body" id="sysUpdMsg">
        <div class="alert alert-warning"><strong>Your PVault installation wiil be upgraded to its latest version.</strong></div>
        <pre><div id="new-rel">Check the release notes <a href="#" id="load-rel-notes">here</a></div></pre>
      </div>
      <div class="modal-footer">
        <a href="javascript:updateSYS()" role="button" class="btn btn-warning" id="sysUpBtn">Upgrade PVault</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="sysUpOk">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /SYS UPGRADE MODAL -->


<script src="/js/sys-upgrade.js"></script>
