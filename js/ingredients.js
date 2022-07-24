
function delete_ingredient(id){
	
	$.ajax({
		url: '/pages/update_data.php', 
		type: 'GET',
		data: {
			ingredient: "delete",
			ing_id: id,
			},
		dataType: 'html',
		success: function (data) {
		  	$('#innermsg').html(data);
			list_ingredients();
		}
	  });
};

$("#btnImportCSV").prop("disabled", true);
$("input[type=file]").on('change',function(){	
    $("#CSVImportMsg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	
	var fd = new FormData();
    var files = $('#CSVFile')[0].files;
    
    if(files.length > 0 ){
       fd.append('CSVFile',files[0]);

        $.ajax({
           url: 'pages/upload.php?type=ingCSVImport&step=upload',
           type: 'POST',
           data: fd,
           contentType: false,
           processData: false,
           success: function(response){
             if(response != 0){
				$("#CSVImportMsg").html('');
				$("#step_upload").html(response);
              }else{
                $("#CSVImportMsg").html('<div class="alert alert-danger">File upload failed!</div>');
				$("#btnImportCSV").prop("disabled", false);
              }
            },
         });
  }else{
	$("#CSVImportMsg").html('<div class="alert alert-danger">Please select a file to upload!</div>');
	$("#btnImportCSV").prop("disabled", false);
  }
});


var total_selection = 0;
var ingredient_name = 0;
var cas = 0;
var iupac = 0;
var description = 0;
var fema = 0;

var column_data = [];

$(document).on('change', '.set_column_data', function(){
    var column_name = $(this).val();
    var column_number = $(this).data('column_number');
	//console.log(column_data);
    if(column_name in column_data) {
	  $('#CSVImportMsg').html('<div class="alert alert-danger"><strong>'+column_name+'</strong> is already assigned.</div>');
      $(this).val('');
      return false;
    }else{
		$('#CSVImportMsg').html('');
	}

    if(column_name != '') {
      column_data[column_name] = column_number;
    } else {
      const entries = Object.entries(column_data);

      for(const [key, value] of entries) {
        if(value == column_number) {
          delete column_data[key];
        }
      }
    }

    total_selection = Object.keys(column_data).length;

    if(total_selection == 6) {
		$('#btnImportCSV').prop("disabled", false);
		$('#btnImportCSV').show();

        ingredient_name = column_data.ingredient_name;
		cas = column_data.cas;
		iupac = column_data.iupac;
		description = column_data.description;
		fema = column_data.fema;
		type = column_data.type;
    } else {
		$('#btnImportCSV').prop("disabled", true);
    }

  });

$(document).on('click', '#btnImportCSV', function(event){

    event.preventDefault();
   
    $.ajax({
      url: "/pages/upload.php?type=ingCSVImport&step=import",
      method: "POST",
      data:{		  
		  ingredient_name: ingredient_name, 
		  cas: cas, 
		  iupac: iupac, 
		  description: description,
		  fema: fema,
		  type: type
		  },
      beforeSend:function(){
        $('#btnImportCSV').prop("disabled", true);
      },
      success:function(data) {
		  if (data.indexOf('Error:') > -1) {
			  $('#btnImportCSV').prop("disabled", false);
			  $('#CSVImportMsg').html(data);
		  }else{
			$('#btnImportCSV').prop("disabled", false);
			$('#btnImportCSV').hide();
			$('#btnCloseCsv').prop('value', 'Close');
			$('#process_area').css('display', 'none');
			$('#CSVImportMsg').html(data);
			reload_ingredients_data();
		  }
      }
    })

  });

/*
function importING(name) {	  
	$.ajax({ 
		url: 'pages/update_data.php', 
		type: 'GET',
		data: {
			'import': 'ingredient',
			'name': name,
			},
		dataType: 'html',
		success: function (data) {
			$('#innermsg').html(data);
		}
	  });
};
*/