$(function () {
  $('#modalToggle').click(function() {
    $('#modal').modal({
      backdrop: 'static'
    });
  });

  $('#supplierContinue').click(function (e) {
    e.preventDefault();
    $('.progress-bar').css('width', '60%');
    $('.progress-bar').html('Step 2 of 4');
    $('#SDSTabs a[href="#productPanel"]').tab('show');
  });

  $('#productContinue').click(function (e) {
    e.preventDefault();
    $('.progress-bar').css('width', '80%');
    $('.progress-bar').html('Step 3 of 4');
    $('#SDSTabs a[href="#compoPanel"]').tab('show');
  });

  $('#compoContinue').click(function (e) {
    e.preventDefault();
    $('.progress-bar').css('width', '100%');
    $('.progress-bar').html('Step 4 of 4');
	//$('.progress-bar').hide();
    $('#SDSTabs a[href="#reviewPanel"]').tab('show');
  });


  
  $('#commitSDS').click(function (e) {
    e.preventDefault();
    var formData = {
      name: $('#name').val(),
      start_date: $('#start-date').val(),
      end_date: $('#end-date').val(),
      days: {
        sunday: $('#sunday').prop('checked'),
        monday: $('#monday').prop('checked'),
        tuesday: $('#tuesday').prop('checked'),
        wednesday: $('#wednesday').prop('checked'),
        thurday: $('#thursday').prop('checked'),
        friday: $('#friday').prop('checked'),
        saturday: $('#saturday').prop('checked'),
      },
      start_time: $('#start-time').val(),
      end_time: $('#end-time').val()
    }
    alert(JSON.stringify(formData));
  })
})
