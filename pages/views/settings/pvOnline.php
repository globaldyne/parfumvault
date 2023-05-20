<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 


require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/config.php');
require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/func/pvOnline.php');
require_once(__ROOT__.'/inc/settings.php');

$auth = pvOnlineValAcc($pvOnlineAPI, $user['email'], $user['password'], $ver);

?>

<h3>PV Online Profile</h3>
<hr>
<div id="pvOnMsg"></div>
<div id="pv_online_conf" class="form-group">

<?php if($auth['code'] == '001'){ ?>
    <div class="row">
      <label class="col-sm-1 col-form-label pv_point_gen" data-toggle="tooltip" data-placement="right" title="Enable or disable PV Online access.">Enable Service:</label>
     <div class="col-sm-2">
        <input name="pv_online_state" type="checkbox" id="pv_online_state" value="1" <?php if($pv_online['enabled'] == '1'){ ?> checked <?php } ?>/>
      </div>
     </div>
     <div id="pv_profile">
         <div class="row">
          <label class="col-sm-1 col-form-label pv_point_gen" data-toggle="tooltip" data-placement="right" title="To enable or disable formula sharing service, please login to PV Online and navigate to the profile section.">Enable Formula sharing:</label>
          <div class="col-sm-2">
            <input name="sharing_status" type="checkbox" id="sharing_status" value="1"/>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-1 col-form-label pv_point_gen" data-toggle="tooltip" data-placement="right" title="If enabled, will get a new email when new ingredients are published to PV Online.">Notify me for new ingredients:</label>
          <div class="col-sm-2">
            <input name="new_ing_status" type="checkbox" id="new_ing_status" value="1"/>
          </div>
        </div>
       <hr>
      <div class="row">
        <label class="col-sm-1 col-form-label pv_point_gen" data-toggle="tooltip" data-placement="top" title="Choose a nick name to represent yourself in PV Online, this can be your full name or anything else.">Nickname:</label>
        <div class="col-sm-2">
          <input name="nickname" type="text" class="form-control" id="nickname" value="" placeholder="John Smith">
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-1 col-form-label pv_point_gen" data-toggle="tooltip" data-placement="top" title="A short description to introduce yourself to others in PV Online">Short introduction:</label>
        <div class="col-sm-2">
            <textarea class="form-control" id="intro" rows="3" placeholder="Hey fellow perfurmers..."></textarea>
        </div>
      </div>
      
      <button type="submit" class="btn btn-primary" id="update-profile">Update</button>
  </div>
     <hr>
       <div class="row">
          <span><label class="badge_int red"><i id="rmAcc" class="pv_point_gen">Delete my PV Online account</i></label></span>       
       </div>
<?php }elseif($auth['code'] == '002'){ ?>

	<div class="alert alert-danger">
    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Oops! <?=$auth['msg']?>.</h4>
    	<p>Please make sure your local installation password and PV Online account password match.</p>
    	<hr>
    	<p class="mb-0">You can <a href="https://online.jbparfum.com/forgotpass.php" target="_blank">reset</a> your PV Online password</p>
	</div>
    
<?php }elseif($auth['code'] == '003'){ ?>

	<div id="pv_account_error">
    	<div class="alert alert-warning">
    		<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Oops!  <?=$auth['msg']?>.</h4>
    		<p>Looks like you haven't created a PV Online account yet.</p>
            <p>To be able to use PV Online services, you need to register an account.</p>
    		<hr>
    		<p class="mb-0 pv_point_gen" id="autoCreateAcc"><strong>Click here to create an account and configure you local installation</strong></p>
		</div>
	</div>
           
<?php }else{ ?>
	<div id="pv_account_error">
    	<div class="alert alert-danger">
    		<h4 class="alert-heading"><i class="fa fa-exclamation-circle mr2"></i>Connection error</h4>
    		<p>Unable to connect to PV Online.</p>
            <p>Please make sure your network isn't blocking PV Online and the service is available.</p>
		</div>
	</div>
<?php } ?>
   <hr>
   <div class=" row">
      <?php require(__ROOT__.'/pages/privacy_note.php');?>       
   </div>
</div>

        
<script>
$(document).ready(function() {
	if($("#pv_online_state").is(':checked')){
		$("#sharing_status").prop('disabled', false);
		$("#intro").prop('disabled', false);
		$("#nickname").prop('disabled', false);
		$("#update-profile").prop('disabled', false);
		$("#new_ing_status").prop('disabled', false);

	}else{
		$("#sharing_status").prop('disabled', true);
		$("#intro").prop('disabled', true);
		$("#nickname").prop('disabled', true);
		$("#update-profile").prop('disabled', true);
		$("#new_ing_status").prop('disabled', true);
	}
	
	$(function () {
  		$('[data-toggle="tooltip"]').tooltip()
	});
	
<?php if($pv_online['enabled'] == '1'){?>
	getPVProfile();
	$('#rmAcc').click(function() {
		
		bootbox.dialog({
		   title: "Confirm acccount deletion",
		   message : '<strong>Permanently delete my account from PV Online?</strong>'+
		   			'<p class="alert-danger">Please note: This action <strong>cannot be reverted.</strong></p>',
		   buttons :{
			   main: {
				   label : "DELETE",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({ 
						url: '/pages/update_settings.php', 
						type: 'POST',
						data: {
							manage: 'pvonline',
							rmACC: 1
							},
						dataType: 'json',
						success: function (data) {
							if(data.success){
								var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.success + '</div>';
							}else{
								var msg ='<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>' + data.error + '</div>';
							}
							$('#pvOnMsg').html(msg);
						}						
					  });
					 return true;
				   }
			   },
			   cancel: {
				   label : "Cancel",
				   className : "btn-default",
				   callback : function() {
					   return true;
				   }
			   }   
		   },onEscape: function () {return true;}
	   });
	});
<?php } ?>

//ENABLE OR DISABLE PV ONLINE
$('#pv_online_state').on('change', function() {
	if($("#pv_online_state").is(':checked')){
		var val = 1;
	}else{
		var val = 0;
		$("#sharing_status").prop('disabled', true);
	}
	$.ajax({ 
		url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'pvonline',
			state_update: '1',
			pv_online_state: val,
			},
		dataType: 'json',
		success: function (data) {
			if(data.error){
				var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
			}else if (data.success){
				var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>PV Online service is now <strong>'+data.success+'</strong></div>';
				if (data.success == 'active'){
					$("#sharing_status").prop('disabled', false);
					$("#intro").prop('disabled', false);
					$("#nickname").prop('disabled', false);
					$("#update-profile").prop('disabled', false);
					$("#new_ing_status").prop('disabled', false);
					getPVProfile();
				}else if (data.success == 'in-active'){
					$("#sharing_status").prop('disabled', true);
					$("#intro").prop('disabled', true);
					$("#nickname").prop('disabled', true);
					$("#update-profile").prop('disabled', true);
					$("#new_ing_status").prop('disabled', true);
				}
			}
			$('#pvOnMsg').html(rmsg);
		}
	});
});
	
//ENABLE OR DISABLE FORMULA SHARING
$('#new_ing_status').on('change', function() {
	if($("#new_ing_status").is(':checked')){
		var val = 1;
	}else{
		var val = 0;
	}
	$.ajax({ 
		url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'pvonline',
			email_alerts: '1',
			new_ing_status: val,
			},
		dataType: 'json',
		success: function (data) {
			if(data.error){
				var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
			}else if (data.success){
				var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>New ingredient emails are <strong>'+data.success+'</strong></div>';
			}
			$('#pvOnMsg').html(rmsg);
		}
	});
});

	
//ENABLE OR DISABLE NEW INGREDIENT NOTIFY
$('#sharing_status').on('change', function() {
	if($("#sharing_status").is(':checked')){
		var val = 1;
	}else{
		var val = 0;
	}
	$.ajax({ 
		url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			manage: 'pvonline',
			share_update: '1',
			pv_online_share: val,
			},
		dataType: 'json',
		success: function (data) {
			if(data.error){
				var rmsg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>'+data.error+'</div>';
			}else if (data.success){
				var rmsg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>PV Online sharing service is now <strong>'+data.success+'</strong></div>';
			}
			$('#pvOnMsg').html(rmsg);
		}
	});
});

$('#pv_account_error').on('click', '[id*=autoCreateAcc]', function () {
	$('#pv_account_error').html('<div class="alert alert-info"><img src="/img/loading.gif"/> Please wait, configuring the system...<p><strong>Please do not close, refresh or navigate away from this page. You will be automatically redirected upon a succesfull installation.</strong></p></div>');															
		
	$.ajax({ 
		url: '/core/configureSystem.php', 
		type: 'POST',
		data: {
			action: 'create_pv_account',
			fullName: "<?=$user['fullName']?>",
			email: "<?=$user['email']?>",
			password: "<?=$user['password']?>",
		},
		dataType: 'json',
		success: function (data) {
			if (data.success){
				$('#pv_account_error').html('<div class="alert alert-success">'+data.success+'</div>');
				//getPVProfile();
			}else{
				$('#pv_account_error').html('<div class="alert alert-danger">'+data.error+'</div>');
			}
		},
		error: function () {
			$('#pv_account_error').html('<div class="alert alert-danger">Unable to connect, please try again later</div>');
		}
	});

});
//GET PROFILE DATA
function getPVProfile(){
	$.ajax({ 
		url: '<?=$pvOnlineAPI?>',
		dataType: 'json',
		data: {
			username: "<?=$pv_online['email']?>",
			password: "<?=$pv_online['password']?>",
			do: 'getProfile'
		},
		type: 'POST',
		success: function (data) {
			if(data.userProfile){
				$("#nickname").val(data.userProfile.nickname);
				$("#intro").val(data.userProfile.intro);
			}
			if(data.error){
				$('#msg').html('<div class="alert alert-danger">PV Online '+data.error+' You can <a href="javascript:disablePV()">disable</a> PV integration or <a href="https://online.jbparfum.com/forgotpass.php" target="_blank">reset</a> your PV Online password</p>');
			}else {
				if(data.userProfile.formulaSharing == 0){
					$("#sharing_status").prop('checked', false);
				}
				
				if(data.userProfile.newIngNotify == 0){
					$("#new_ing_status").prop('checked', false);
				}
				
				if (data.userProfile.formulaSharing == 1){
					$("#sharing_status").prop('checked', true);
				}
				
				if(data.userProfile.newIngNotify == 1){
					$("#new_ing_status").prop('checked', true);
				}
			}
		},
		error: function () {
				$('#pv_profile').html('<div class="alert alert-danger">Unable to connect, please try again later</div>');
			}
			
		});
};

//UPDATE PV ONLINE PROFILE
$('#update-profile').click(function() {
	$.ajax({ 
		url: '/pages/update_settings.php', 
		type: 'POST',
		data: {
			update_pvonline_profile: 1,
			nickname: $("#nickname").val(),		
			intro: $("#intro").val(),
			name: "<?=$user['fullName']?>"
			},
		dataType: 'json',
		error: function() {
			$('#pvOnMsg').html('<div class="alert alert-danger">Connection error</div>');
		},
		success: function (data) {
			if(data.success){
				$('#pvOnMsg').html('<div class="alert alert-success">'+data.success+'</div>');
			}else if( data.error){
				$('#pvOnMsg').html('<div class="alert alert-danger">'+data.error+'</div>');
			}
		}
	  });
});



}); //end doc
</script>