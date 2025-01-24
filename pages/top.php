<?php
if (!defined('pvault_panel')){ die('Not Found');}

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT docData AS avatar FROM documents WHERE ownerID = ? AND name = 'avatar' AND type = '3' AND owner_id = ?");
$stmt->bind_param("ss", $userID, $userID);
$stmt->execute();
$doc = $stmt->get_result()->fetch_array();

if ($role === 1) {
  $db_ver = trim(file_get_contents(__ROOT__.'/db/schema.ver'));
  if($pv_meta['schema_ver'] < $db_ver){
    $show_db_upgrade = true;
  }
}


if (empty($user_settings) && $_SERVER['REQUEST_URI'] !== '/?do=settings') {
  echo '<script>
    $(document).ready(function() {
      $("#forceSettingsModal").modal({
        backdrop: "static",
        keyboard: false
      }).modal("show");
    });
  </script>';
}

//HANDLE ANNOUNCEMENTS
if (!empty($system_settings['announcements']) && !empty($user_settings)) {
  $announcement = $system_settings['announcements'];
  $announcementHash = md5($announcement);

  try {
    // Fetch the stored announcement hash from the database
    $stmt = $conn->prepare("SELECT pref_data FROM user_prefs WHERE owner_id = ? AND pref_name = 'announcement'");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $storedHash = $result->fetch_assoc()['pref_data'] ?? '';

    if ($storedHash !== $announcementHash) {
      echo '<script>
        $(document).ready(function() {
          $("#announcementModal").modal({
            backdrop: "static",
            keyboard: false
          }).modal("show");
        });
      </script>';

      // Update the stored announcement hash in the database
      if ($storedHash) {
        $updateStmt = $conn->prepare("UPDATE user_prefs SET pref_data = ? WHERE owner_id = ? AND pref_name = 'announcement'");
        $updateStmt->bind_param("ss", $announcementHash, $userID);
      } else {
        $updateStmt = $conn->prepare("INSERT INTO user_prefs (owner_id, pref_name, pref_data) VALUES (?, 'announcement', ?)");
        $updateStmt->bind_param("ss", $userID, $announcementHash);
      }
      $updateStmt->execute();
    }
  } catch (Exception $e) {
    error_log("Error handling announcement: " . $e->getMessage());
  }
}
?>

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="announcementModalLabel">Announcement</h5>
      </div>
      <div class="modal-body">
        <?php echo htmlspecialchars($announcement, ENT_QUOTES, 'UTF-8'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>


<div id="content">
  <nav class="navbar navbar-expand bg-gradient-primary-navbar topbar mb-4 static-top shadow">
    <ul class="navbar-nav float-end ml-auto">
      <div class="mt-3" id="chkUpdMsg"></div>

      <!-- Cart -->
      <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-shopping-cart fa-fw text-white"></i>
          <!-- Counter - cart -->
          <span class="badge badge-danger badge-counter"><?php echo countCart(); ?></span>
        </a>
        <!-- Dropdown - cart -->
        <div class="dropdown-list dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="messagesDropdown">
          <?php
          $cartQuery = $conn->prepare("SELECT id FROM cart WHERE owner_id = ? GROUP BY name");
          $cartQuery->bind_param("s", $userID);
          $cartQuery->execute();
          $cartResult = $cartQuery->get_result();
          if ($cartResult->num_rows) { ?>
            <a href="/?do=cart" class="dropdown-header">To be ordered</a>
            <?php
            $qC = $conn->prepare("SELECT name, ingID FROM cart WHERE owner_id = ? ORDER BY name ASC LIMIT 5");
            $qC->bind_param("s", $userID);
            $qC->execute();
            $result = $qC->get_result();
            while ($pC = $result->fetch_array()) {
              $supDetails = getPrefSupplier($pC['ingID'], $conn);
            ?>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo htmlspecialchars($supDetails['supplierLink']); ?>" target="_blank">
                <div class="font-weight-bold">
                  <div class="text-truncate"><?php echo htmlspecialchars($pC['name']); ?></div>
                  <div class="small text-gray-500"><?php echo htmlspecialchars($supDetails['name']); ?></div>
                </div>
              </a>
            <?php } ?>
            <a class="dropdown-item text-center small text-gray-500" href="/?do=cart">See all...</a>
          <?php } else { ?>
            <a class="dropdown-item text-center small text-gray-500" href="/?do=cart">No orders to place</a>
          <?php } ?>
        </div>
      </li>

      <div class="topbar-divider d-none d-sm-block"></div>
      <li class="nav-item dropdown no-arrow">
        <a class="mx-4 nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php if ($role == 1) { ?>
            <span class="badge bg-success bg-opacity-75">ADMIN</span>
          <?php } ?>
          <span class="mx-2 d-none d-lg-inline text-white small"><?php echo htmlspecialchars($user['fullName']); ?></span>
          <div class="icon-container">
            <?php if ($doc['avatar']) { ?>
              <img src="<?php echo htmlspecialchars($doc['avatar'] ?: '/img/ICO_TR.png'); ?>" class="img-profile rounded-circle">
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
      });

      <?php if ($role === 1 && $show_db_upgrade) { ?>
        $('#dbUpgradeDialog').modal('show');
        $('#dbUpOk').hide();
      <?php } ?>

      $(function() {
        $.ajax({
          url: '/pages/views/tools/calcTools.php',
          type: 'GET',
          dataType: 'html',
          success: function(data) {
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
    });
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

  <!-- Force Settings Modal -->
  <div class="modal fade" id="forceSettingsModal" tabindex="-1" aria-labelledby="forceSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="forceSettingsModalLabel">Settings Required</h5>
        </div>
        <div class="modal-body">
          <p>Your user settings are incomplete. Please update your settings to continue.</p>
        </div>
        <div class="modal-footer">
          <a href="/?do=settings" class="btn btn-primary">Go to Settings</a>
        </div>
      </div>
    </div>
  </div>
  <!-- /Force Settings Modal -->

  <?php if ($role === 1) { ?>
  <!-- DB UPGRADE MODAL -->
  <div class="modal fade" id="dbUpgradeDialog" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="dbUpgradeDialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Database Schema Upgrade</h5>
        </div>
        <div class="modal-body" id="dbUpdMsg">
          <div class="alert alert-warning"><strong>Your database schema needs to be upgraded to version <?php echo htmlspecialchars($db_ver); ?>. Please backup your database first and then click the upgrade button.</strong>
          </div>
        </div>
        <div class="modal-footer">
          <a href="/core/core.php?do=backupDB" role="button" class="btn btn-primary" id="dbBkBtn">Backup Database</a>
          <button type="button" class="btn btn-warning" id="dbUpBtn">Upgrade Schema</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="dbUpOk">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /DB UPGRADE MODAL -->

  <!-- SYS UPGRADE MODAL -->
  <div class="modal fade" id="sysUpgradeDialog" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="sysUpgradeDialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Perfumers Vault version upgrade</h5>
        </div>
        <div class="modal-body" id="sysUpdMsg">
          <div class="alert alert-warning"><i class="fa-solid fa-circle-info mx-2"></i><strong>Perfumers Vault will be upgraded to its latest version.<p>Please make sure you have read the release notes before upgrading.</p></strong></div>
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
<?php } ?> 