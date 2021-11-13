<?php 
if (!defined('pvault_panel')){ die('Not Found');}


function updateGHS( $ingID, $GHS, $conn){
	if(mysqli_num_rows(mysqli_query($conn,"SELECT ingID FROM ingSafetyInfo WHERE ingID='$ingID'"))) {
		mysqli_query($conn,"UPDATE ingSafetyInfo SET GHS = '$GHS' WHERE ingID='$ingID'");  
    }else{ 
        mysqli_query($conn,"INSERT INTO ingSafetyInfo (ingID, GHS) VALUES ('$ingID', '$GHS')");
    }
return;
}
?>