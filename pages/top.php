<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <ul class="navbar-nav vault-top ml-auto">
          <!-- Nav Item - Notifications -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Notifications -->
                <span class="badge badge-danger badge-counter"><?php echo countPending(NULL, NULL, $conn);?></span>
              </a>
              <!-- Dropdown - Notifications -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM makeFormula WHERE toAdd = '1' GROUP BY name"))){ ?>
				<a href="?do=todo" class="dropdown-header"><h6>Pending formulas to make</h6></a>
				<?php 
				$toadd_q = mysqli_query($conn, "SELECT name,fid FROM makeFormula WHERE toAdd = '1' GROUP BY name ORDER BY name ASC LIMIT 5");
				while ($toadd_p = mysqli_fetch_array($toadd_q)){ 	
					$todoImg = mysqli_fetch_array(mysqli_query($conn, "SELECT image FROM formulasMetaData WHERE fid = '".$toadd_p['fid']."'"));
					if(empty($todoImg['image'])){
						$todoImg['image'] = 'img/logo_400.png';
					}
				?>
                <a class="dropdown-item d-flex align-items-center" href="pages/makeFormula.php?fid=<?php echo $toadd_p['fid'];?>" target="_blank">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="<?php echo $todoImg['image']; ?>">
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate"><?php echo $toadd_p['name'];?></div>
                    <div class="small text-gray-500">Ingredients left: <?php echo countPending(1, $toadd_p['fid'], $conn);?></div>
                  </div>
                </a>
				<?php } ?>
                <a class="dropdown-item text-center small text-gray-500" href="?do=todo">See all...</a>
	
				<?php }else{ ?>
                <a class="dropdown-item text-center small text-gray-500" href="?do=todo">No formulas to make</a>
				<?php } ?>	
				 
              </div>
            </li>

             <!-- Cart -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-shopping-cart fa-fw"></i>
                <!-- Counter - cart -->
                <span class="badge badge-danger badge-counter"><?php echo countCart($conn); ?></span>
              </a>
              <!-- Dropdown - cart -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM cart GROUP BY name"))){ ?>
				<a href="?do=cart" class="dropdown-header"><h6>To be ordered</h6></a>
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
	            <a class="dropdown-item text-center small text-gray-500" href="?do=cart">See all...</a>

				<?php }else{ ?>
                <a class="dropdown-item text-center small text-gray-500" href="?do=cart">No orders to place</a>
				<?php } ?>	
                </div>
            </li>
            
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $user['fullName'];?></span>
                <img class="img-profile rounded-circle" src="<?php if($user['avatar']){ echo $user['avatar']; }else{ echo 'img/logo_def.png'; } ?>">
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">

                <a class="dropdown-item" href="?do=settings">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="https://www.jbparfum.com/knowledge-base" target="_blank">
                  <i class="fas fa-book fa-sm fa-fw mr-2 text-gray-400"></i>
                  Documentation
                </a>
                <a class="dropdown-item" href="https://www.jbparfum.com/feature-request/" target="_blank">
                  <i class="fas fa-lightbulb fa-sm fa-fw mr-2 text-gray-400"></i>
                  Request a feature / Bug report
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://online.jbparfum.com/" target="_blank">
                  <i class="fas fa-globe fa-sm fa-fw mr-2 text-gray-400"></i>
                  PV Online
                </a>              
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://apps.apple.com/us/app/id1525381567" target="_blank">
                  <i class="fab fa-apple fa-sm fa-fw mr-2 text-gray-400"></i>
                  App Store
                </a>              
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
<?php if($settings['chkVersion'] == '1'){ ?>          
<?=checkVer($ver);?>
<div id="msg"><?php echo $db_up_msg;?></div>
<?php } ?>
</nav>
