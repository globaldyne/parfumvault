  <table width="100%" border="0">
    <tr>
      <td width="13%">&nbsp;</td>
      <td width="87%">&nbsp;</td>
    </tr>
    <tr>
      <td><ul>
        <li><a href="/pages/operations.php?do=backupDB">Backup DB</a></li>
        </ul></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><ul>
        <li> <a href="#" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#restore_db">Restore DB</a></li>
      </ul></td>
      <td>&nbsp;</td>
    </tr>
    <?php if(getenv('phpMyAdmin') == FALSE){ ?>
    <tr>
      <td><ul>
        <li><a href="/phpMyAdmin/" target="_blank">phpMyAdmin</a></li>
      </ul></td>
      <td>&nbsp;</td>
  	</tr>
  <?php } ?>
  </table>
