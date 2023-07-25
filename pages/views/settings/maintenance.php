  <table width="100%" border="0">
    <tr>
      <td>
      <ul>
        <li><a href="/pages/operations.php?do=backupDB">Backup DB</a></li>
       </ul>
      </td>
    </tr>
    <tr>
      <td>
      <ul>
        <li><a href="#" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#restore_db">Restore DB</a></li>
      </ul>
      </td>
    </tr>
    <?php if(getenv('phpMyAdmin') == "true"){ ?>
    <tr>
      <td>
      <ul>
        <li><a href="/phpMyAdmin/" target="_blank">phpMyAdmin</a></li>
      </ul>
      </td>
      <td></td>
  	</tr>
    <tr>
    	<td>
    	<div class="alert alert-warning">You have enabled phpMyAdmin, please note, managing PV using phpMyAdmin its NOT supported or recommended by any means. If you interfer with backend db you may break your installation and/or loose data.</div>
    	</td>
    </tr>
  <?php } ?>
  </table>
