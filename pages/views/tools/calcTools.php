<div class="card shadow mb-4">
  <div class="card-body">
    <div class="table-responsive">
    <table width="100%" border="0">
      <tr>
        <td >What is 
          <input name="wis" type="text" id="wis"> 
          % of 
          <input name="ofp" type="text" id="ofp"></td>
        <td width="10%"><strong><div id="res1"></div></strong></td>
        <td><button type="button" id="calc1" class="btn btn-primary">Calculate</button></td>
      </tr>
      <tr>
        <td colspan="3"><hr></td>
      </tr>
      <tr>
        <td><input name="pof" type="text" id="pof"> is what percent of 
          <input name="cq" type="text" id="cq"></td>
        <td><strong><div id="res2"></div></strong></td>
        <td><button type="button" id="calc2" class="btn btn-primary">Calculate</button></td>
      </tr>
      <tr>
        <td colspan="3"><hr></td>
      </tr>
    </table>
    </div>
  </div>
</div>

<script>
$('#calc1').click(function() {
	var c = $('#wis').val()/100*$('#ofp').val();
	$("#res1").html(" = " + c);
});

$('#calc2').click(function() {
 	var c = $('#pof').val()/$('#cq').val()*100;
	$("#res2").html(" = " + c);
});
</script>
