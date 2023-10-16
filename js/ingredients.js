
$("#btnImportCSV").prop("disabled", true);
$("input[type=file]").on('change',function(){	
    $("#CSVImportMsg").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
	
	var fd = new FormData();
    var files = $('#CSVFile')[0].files;
    
    if(files.length > 0 ){
       fd.append('CSVFile',files[0]);

        $.ajax({
           url: '/pages/upload.php?type=ingCSVImport&step=upload',
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

var ingredient_name = '';
var iupac = '';
var cas = '';
var fema = '';
var type = '';
var strength = '';
var profile = '';
var physical_state = '';
var allergen = 0;
var odor = '';
var impact_top = 0;
var impact_heart = 0;
var impact_base = 0;


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

    if(total_selection == 13) {
		$('#btnImportCSV').prop("disabled", false);
		$('#btnImportCSV').show();

        ingredient_name = column_data.ingredient_name;
		iupac = column_data.iupac;
		cas = column_data.cas;
		fema = column_data.fema;
		type = column_data.type;
		strength = column_data.strength;
		profile = column_data.profile;
		physical_state = column_data.physical_state;
		allergen = column_data.allergen;
		odor = column_data.odor;
		impact_top = column_data.impact_top;
		impact_heart = column_data.impact_heart;
		impact_base = column_data.impact_base;
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
		  iupac: iupac,  
		  cas: cas, 
		  fema: fema,
		  type: type,
		  strength: strength,  
		  profile: profile,  
		  physical_state: physical_state,
		  allergen: allergen,
		  odor: odor,  
		  impact_top: impact_top, 
		  impact_heart: impact_heart, 
		  impact_base: impact_base
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

function get_stats(){
	$.ajax({ 
		url: pvOnlineAPI, 
		dataType: 'json',
		data: {
			'request': 'getStats'
		},
		success: function (resp) {
			$('#ingredientsTotal').html('<p>Ingredients online: <strong>' + resp.data.stats.Ingredients.count + '</strong></p>');
			
			$('#synonymsTotal').html('Synonyms online: <strong>' + resp.data.stats.Synonyms.count + '</strong>');
			$('#composTotal').html('Compositions online: <strong>' + resp.data.stats.Compositions.count + '</strong>');

		},
		error: function (request, status, error) {
			$('#pv_online_imp_area').html('<div class="alert alert-danger mt-3"><i class="bi bi-exclamation-circle mx-2"></i></i>Unable to handle request, server returned an error: '+request.status+'</div>');
			$('#btnImport').prop('disabled', true);
		},
		
	});
};

get_stats();
