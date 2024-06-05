$(function () {
  $('#modalToggle').click(function() {
    $('#modal').modal({
      backdrop: 'static'
    });
  });

  $('#supplierContinue').click(function (e) {
    e.preventDefault();
	if( $('#address').val() === "" ||  $('#po').val() === "" ||  $('#country').val() === "" ||  $('#telephone').val() === "" ||  $('#email').val() === "" ||  $('#url').val() === ""){
		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>All fields required');
		$('.toast-header').removeClass().addClass('toast-header alert-danger');
		$('.toast').toast('show');
		return;
	}
	$('.toast').toast('hide');
    $('.progress-bar').css('width', '40%');
    $('.progress-bar').html('Step 2 of 5');
    $('#SDSTabs a[href="#productPanel"]').tab('show');
  });

  $('#productContinue').click(function (e) {
    e.preventDefault();
		if( $('#prodName').val() === "" ||  $('#prodUse').val() === "" ||  $('#sdsCountry').val() === "" ||  $('#sdsLang').val() === "" ){
		$('#toast-title').html('<i class="fa-solid fa-circle-exclamation mr-2"></i>All fields required');
		$('.toast-header').removeClass().addClass('toast-header alert-danger');
		$('.toast').toast('show');
		return;
	}
	$('.toast').toast('hide');
    $('.progress-bar').css('width', '60%');
    $('.progress-bar').html('Step 3 of 5');
    $('#SDSTabs a[href="#compoPanel"]').tab('show');
  });

  $('#compoContinue').click(function (e) {
    e.preventDefault();
    $('.progress-bar').css('width', '80%');
    $('.progress-bar').html('Step 4 of 5');
	//$('.progress-bar').hide();
    $('#SDSTabs a[href="#safety_info"]').tab('show');
  });

  $('#ghsContinue').click(function (e) {
    e.preventDefault();
    $('.progress-bar').css('width', '100%');
    $('.progress-bar').html('Step 5 of 5');
	//$('.progress-bar').hide();
    $('#SDSTabs a[href="#reviewPanel"]').tab('show');
  })

  
$('#commitSDS').click(function (e) {
    e.preventDefault();

    var wizardData = {
        name: prodName,
        supplier: {
            name: $('#supplier_name').find("option:selected").text(),
            po: $('#po').val(),
            country: $('#country').val(),
            address: $('#address').val(),
            telephone: $('#telephone').val(),
            email: $('#email').val(),
            url: $('#url').val(),
        },
        product: {
            name: prodName,
            country: $('#sdsCountry').val(),
            use: $('#prodUse').val(),
            lang: $('#sdsLang').val(),
            type: $('input[name="productType"]:checked').attr('id'),
        },
        composition: {
            state: $('input[name="productState"]:checked').attr('id'),
        }
    }

    // Fetch data from the remote URL and append it to wizardData
    $.ajax({
        url: '/core/list_ing_compos_data.php?id=' + btoa(prodName),
        method: 'GET',
        dataType: 'json',
        success: function(remoteData) {
            // Assuming remoteData is an object that we want to merge with wizardData
            $.extend(true, wizardData, remoteData); // Deep merge

            // Pretty-print the merged JSON data with 2 spaces indentation
            var prettyJson = JSON.stringify(wizardData, null, 2);

            // Display the pretty-printed JSON data in the #jsonOut element
            $('#jsonOut').html(prettyJson);
			generatePDF(wizardData);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Failed to fetch data: ", textStatus, errorThrown);
        }
    });
});


})

function generatePDF(data) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Helper function to convert an object to a table row
            function objectToTableRow(obj) {
                return Object.keys(obj).map(key => [key, obj[key]]);
            }

            // Add Supplier Table
            doc.text("Supplier", 14, 10);
            doc.autoTable({
                startY: 20,
                head: [['Supplier details']],
                body: objectToTableRow(data.supplier),
            });

            // Add Product Table
            doc.text("Product", 14, doc.autoTable.previous.finalY + 10);
            doc.autoTable({
                startY: doc.autoTable.previous.finalY + 20,
                head: [['Key', 'Value']],
                body: objectToTableRow(data.product),
            });

            // Add Composition Table
/*			
            doc.text("Composition", 14, doc.autoTable.previous.finalY + 10);
            doc.autoTable({
                startY: doc.autoTable.previous.finalY + 20,
                head: [['Key', 'Value']],
                body: objectToTableRow(data.composition),
            });
*/
            // Add Data Table
            doc.text("Composition", 12, doc.autoTable.previous.finalY + 10);
            doc.autoTable({
                startY: doc.autoTable.previous.finalY + 20,
                head: [['Ingredient', 'Name', 'CAS', 'EC', 'GHS', 'Percentage']],
                body: data.data.map(item => [
                    item.ing, item.name, item.cas, item.ec, item.GHS, item.percentage
                ]),
				styles: { fontSize: 8 }  // Set font size to 8 for this table
            });
			
			doc.setProperties({
				title: prodName,
				subject: 'Safety Data Sheet',
				author: 'JB',
				keywords: 'SDS, PV, Perfumers Vault',
				creator: 'Perfumers Vault'
			});
            // Save the generated PDF
            doc.save('wizard_data.pdf');
        };
function fetch_safety(){
	$.ajax({ 
		url: '/pages/views/ingredients/safetyData.php', 
		type: 'POST',
		data: {
			ingID: ingID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_safety').html(data);
		},
	});
};

