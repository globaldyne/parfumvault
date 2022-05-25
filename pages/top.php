<?php if (!defined('pvault_panel')){ die('Not Found');}?>

<div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <ul class="navbar-nav vault-top ml-auto">
          <!-- Nav Item - Notifications -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Notifications -->
                <span class="badge badge-danger badge-counter badge-counter-shared-formulas"></span>
              </a>
              <!-- Dropdown - Notifications -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
				<a href="#" class="dropdown-header"><h6>PV Online</h6></a>
                <div id="list-shared-formulas" class="dropdown-item text-gray-500"></div>
				<div id="list-shared-formulas-footer"></div>				 
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
                <a class="dropdown-item popup-link" href="pages/tools.php">
                  <i class="fas fa-tools fa-sm fa-fw mr-2 text-gray-400"></i>
                  Calculation Tools
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="https://www.jbparfum.com/knowledge-base" target="_blank">
                  <i class="fas fa-book fa-sm fa-fw mr-2 text-gray-400"></i>
                  Documentation
                </a>
                <a class="dropdown-item" href="https://github.com/globaldyne/parfumvault/issues" target="_blank">
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
<?php if($settings['chkVersion'] == '1'){ echo checkVer($ver); } ?>
<div id="msg"><?php echo $db_up_msg;?></div>
</nav>

<script>
<?php if($pv_online['email'] && $pv_online['password'] && $pv_online['enabled'] == '1'){?>

chk_shared();
var myVar = setInterval(chk_shared, 50000);
function chk_shared() {
  $('#list-shared-formulas').empty();

  $.ajax({
    url: '<?=$pvOnlineAPI?>',
	dataType: 'json',
	data: {
		username: "<?=$pv_online['email']?>",
		password: "<?=$pv_online['password']?>",
		do: 'getShared'
	},
	type: 'POST',
    success: function(data) {
		if(data.formulasTotal > 0){
			$('.badge-counter-shared-formulas').html(data.formulasTotal);
			for (var i=0;i<data.formulasTotal;++i){
				$('#list-shared-formulas').append('<div class="font-weight-bold">'+
					'<li>'+
						'<button class="shared-formula-accept" data-notes="'+data.formulas[i].notes+'" data-author="'+data.formulas[i].author+'" data-name="'+data.formulas[i].name+'" data-id="'+data.formulas[i].fid+'" id="acceptShared" title="Import formula">'+
              				'<span>Import</span>'+
            			'</button>'+
					'</li>'+
					'<div class="dropdown-divider"></div>'+
					'<li>'+
                    '<div class="text-truncate shared-formula-name"><li><a href="#">'+data.formulas[i].name+'</a></div>'+
                    '<div class="small text-gray-500 shared-formula-notes">'+data.formulas[i].notes+'</li></div>'+
					'<div class="small text-gray-500 shared-formula-author">Author: '+data.formulas[i].author+'</li></div>'+

                  '</div>').fadeIn('slow');
        	}
			$('#list-shared-formulas-footer').html('<a class="dropdown-item text-center small text-gray-500" href="#">View all</a>');

		}else{
			$('.badge-counter-shared-formulas').empty();
			$('#list-shared-formulas-footer').html('<a class="dropdown-item text-center small text-gray-500" href="#">No formulas</a>');
		};
					
		
    }
   
  });
}
  
$('#list-shared-formulas').on('click', '[id*=acceptShared]', function () {
	
	var sharedFormula = {};
	sharedFormula.ID = $(this).attr('data-id');
	sharedFormula.Name = $(this).attr('data-name');
   	sharedFormula.Author = $(this).attr('data-author');
   	sharedFormula.Notes = $(this).attr('data-notes');

	bootbox.dialog({
       title: 'Import formula from PV Online',
       message : '<div id="pvShImpMsg"></div>' + 
	   			 '<p>'+sharedFormula.Author+' shared its formula <strong>'+sharedFormula.Name+'</strong>, with you.</p>'+
				 '<p>Import formula as: <input id="newSharedFname" value="'+sharedFormula.Name+'" type="text" /></p>'+
				 '<p><strong>Formula description:</strong></p>' + 
				 '<p>'+sharedFormula.Notes+'</p>',
       buttons :{
           main: {
               label : 'Import',
               className : 'btn-success',
               callback: function (){
	    			
				$.ajax({
					url: 'pages/pvonline.php', 
					type: 'POST',
					data: {
						action: 'importShareFormula',
						fid: sharedFormula.ID,
						localName: $("#newSharedFname").val(),
						},
					dataType: 'json',
					success: function (data) {
						if(data.error){
							var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
						}else if(data.success){
							chk_shared();
							var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.success+'</div>';
							$('.btn-success').hide();
							$('.btn-default').html('Close');
						}
						$('#pvShImpMsg').html(rmsg);
						
					}
				});
				
                 return false;
               }
           },
           cancel: {
               label : "Cancel",
               className : 'btn-default',
               callback : function() {
				   chk_shared();
                   return true;
               }
           }   
       },onEscape: function () {return true;}
   });
});


<?php }else{ ?>

$('#list-shared-formulas').html('<div class="font-weight-bold">'+
		'<div class="alert alert-warning">PV Online account isn\'t configured yet. Please go to <a href="?do=settings#pvonline">settings</a> to configure it.</div>'+
    '</div>');


<?php } ?>
</script>
