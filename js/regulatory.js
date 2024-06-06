$(function () {
 

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
    $('#SDSTabs a[href="#tech_composition"]').tab('show');
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

  

})


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

function fetch_cmps(){
	$.ajax({ 
		url: '/pages/views/ingredients/compos.php', 
		type: 'GET',
		data: {
			name:  btoa(prodName),
			id: ingID
		},
		dataType: 'html',
		success: function (data) {
			$('#fetch_composition').html(data);
		},
	});
}

 function generatePDF(data) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Utility function to add section header with background
    function addSectionHeader(title, y) {
      doc.setFillColor(100, 100, 25);
      doc.rect(10, y - 5, 190, 8, 'F');
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(12);
      doc.text(title, 12, y);
      doc.setTextColor(0, 0, 0); // Reset text color
    }

    // Add borders for sections
    function addSectionBorder(startY, height) {
      doc.setDrawColor(0, 0, 0);
    }

    let currentY = 10;

    // Supplier section
    addSectionHeader("1. Supplier contact details", currentY);
    currentY += 1;
    doc.setFontSize(10);
    doc.text(`Name: ${data.supplier.name}`, 12, currentY + 10);
    doc.text(`PO: ${data.supplier.po}`, 12, currentY + 15);
    doc.text(`Country: ${data.supplier.country}`, 12, currentY + 20);
    doc.text(`Address: ${data.supplier.address}`, 12, currentY + 25);
    doc.text(`Telephone: ${data.supplier.telephone}`, 12, currentY + 30);
    doc.text(`Email: ${data.supplier.email}`, 12, currentY + 35);
    doc.text(`URL: ${data.supplier.url}`, 12, currentY + 40);
    addSectionBorder(currentY, 60);
    currentY += 60;

    // Product section
    addSectionHeader("2. Product information", currentY);
    currentY += 10;
    doc.setFontSize(10);
    doc.text(`Name: ${data.product.name}`, 12, currentY + 10);
    doc.text(`Country: ${data.product.country}`, 12, currentY + 15);
    doc.text(`Use: ${data.product.use}`, 12, currentY + 20);
    doc.text(`Language: ${data.product.lang}`, 12, currentY + 25);
    doc.text(`Type: ${data.product.type}`, 12, currentY + 30);
    doc.text(`State: ${data.product.state}`, 12, currentY + 35);
    addSectionBorder(currentY, 60);
    currentY += 60;

    // Remote data section with autoTable
    addSectionHeader("3. Product composition", currentY);
    currentY += 10;

    if (data.data.length > 0) {
      const keys = Object.keys(data.data[0]);
	  const tableData = data.data.map(item => [
			item.name, item.cas, item.ec, item.GHS, item.percentage
		]);
	  const headData = [['Name', 'CAS', 'EC', 'GHS', 'Percentage']];

      doc.autoTable({
        startY: currentY,
        head: headData,
        body: tableData,
        styles: { fontSize: 8 }
      });

      currentY = doc.autoTable.previous.finalY + 10;
    }
    addSectionBorder(currentY, doc.autoTable.previous.finalY - currentY + 20);

    // Save the PDF
    //doc.save('wizardData.pdf');
	
	// Convert PDF to Blob
    const pdfBlob = doc.output('blob');

    // Send Blob to server
    const formData = new FormData();
    formData.append('pdf', pdfBlob, 'wizardData.pdf');
 	formData.append('name', prodName);

	formData.append('product_use', data.product.use);
    formData.append('country', data.product.country);
    formData.append('language', data.product.lang);
    formData.append('product_type', data.product.type);
    formData.append('state_state', data.product.state);
    formData.append('supplier_id', data.supplier.id);


  
	$.ajax({
      url: '/pages/operations.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        console.log('PDF successfully saved to database');
		 doc.save('wizardData.pdf');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Failed to save PDF: ", textStatus, errorThrown);
      }
    });
  }