<link href="/css/sb-admin-2.css" rel="stylesheet">
<link href="/css/vault.css" rel="stylesheet">
<script src="/js/jquery/jquery.min.js"></script>
<div id="content-wrapper" class="d-flex flex-column">
   <div class="container-fluid">
     <div>
       <div class="card shadow mb-4">
         <div class="card-header py-3">
           <h2 class="m-0 font-weight-bold text-primary"><a href="#">Calculation Tools</a></h2>
          </div>
          <div class="card-body">
       <div class="table-responsive">
<table width="100%" border="0">
  <tr>
    <td width="24%">What is 
      <input name="wis" type="text" id="wis"> 
      % of 
      <input name="ofp" type="text" id="ofp"></td>
    <td width="10%"><strong><div id="res1"></div></strong></td>
    <td width="66%"><a href="javascript:calc1()" id="calc1">Calculate</a></td>
    </tr>
  <tr>
    <td colspan="3"><hr></td>
    </tr>
  <tr>
    <td><input name="pof" type="text" id="pof"> is what percent of 
      <input name="quantity" type="text" id="quantity"></td>
    <td><strong><div id="res2"></div></strong></td>
    <td><a href="javascript:calc2()" id="calc2">Calculate</a></td>
    </tr>
  <tr>
    <td colspan="3"><hr></td>
    </tr>
</table>
     </div>
    </div>
   </div>
  </div>
 </div>
</div>
<script>
function calc1() {
	var c = $('#wis').val()/100*$('#ofp').val();
	$("#res1").html(" = " + c);
};

function calc2() {
 	var c = $('#pof').val()/$('#quantity').val()*100;
	$("#res2").html(" = " + c);
};
</script>