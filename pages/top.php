<?php if (!defined('pvault_panel')){ die('Not Found');}?>
<div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <ul class="navbar-nav vault-top ml-auto">
          <!-- Nav Item - Messages -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Notifications -->
                <span class="badge badge-danger badge-counter"><?php echo countPending(NULL, NULL, $conn);?></span>
              </a>
              <!-- Dropdown - Notifications -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <?php if(mysqli_num_rows(mysqli_query($conn, "SELECT name,fid FROM makeFormula WHERE toAdd = '1' GROUP BY name"))){ ?>
				<a href="?do=todo" class="dropdown-header"><h6>Pending formulas to make</h6></a>
				<?php 
				$q = mysqli_query($conn, "SELECT name,fid FROM makeFormula WHERE toAdd = '1' GROUP BY name ORDER BY name ASC");
				while ($p = mysqli_fetch_array($q)){ 	
					$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT image FROM formulasMetaData WHERE fid = '".$p['fid']."'"));
				?>
                <a class="dropdown-item d-flex align-items-center" href="pages/makeFormula.php?fid=<?php echo $p['fid'];?>" target="_blank"">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="<?php echo $meta['image']; ?>" alt="">
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate"><?php echo $p['name'];?></div>
                    <div class="small text-gray-500">Ingredients left: <?php echo countPending(1, $p['fid'], $conn);?></div>
                  </div>
                </a>
				<?php } ?>
	
				<?php }else{ ?>
                <a class="dropdown-item text-center small text-gray-500" href="?do=todo">No formulas to make</a>
				<?php } ?>	
				 
              </div>
            </li>

             <!-- Cart -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">7</span>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                  Message Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/fn_BT9fwg_E/60x60" alt="">
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate">Hi there! I am wondering if you can help me with a problem I've been having.</div>
                    <div class="small text-gray-500">Emily Fowler · 58m</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/AU4VPcFN4LE/60x60" alt="">
                    <div class="status-indicator"></div>
                  </div>
                  <div>
                    <div class="text-truncate">I have the photos that you ordered last month, how would you like them sent to you?</div>
                    <div class="small text-gray-500">Jae Chun · 1d</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/CS2uCrpNzJY/60x60" alt="">
                    <div class="status-indicator bg-warning"></div>
                  </div>
                  <div>
                    <div class="text-truncate">Last month's report looks great, I am very happy with the progress so far, keep up the good work!</div>
                    <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="">
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div>
                    <div class="text-truncate">Am I a good boy? The reason I ask is because someone told me that people say this to all dogs, even if they aren't good...</div>
                    <div class="small text-gray-500">Chicken the Dog · 2w</div>
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
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
                <a class="dropdown-item" href="logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
<?php checkVer($ver, NULL);?>
<div id="msg"><?php checkVer(NULL, $pv_meta['schema_ver']);?>
</div>
</nav>
