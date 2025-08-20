<?php

require_once "../../../assets/template/layout.top.php";

$title='Unapproved MR List';



$table = 'master_requisition_master';

$unique = 'req_no';

$status = 'PENDING';

$target_url = '../mr_fg/mr_checking.php';



if($_POST[$unique]>0)

{

$_SESSION[$unique] = $_POST[$unique];

header('location:'.$target_url);

}



?><div class="form-container_large">

<form action="" method="post" name="codz" id="codz">

<table width="80%" border="0" align="center">

  <tr>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td align="right" bgcolor="#FF9966"><strong><?=$title?>: </strong></td>

    <td bgcolor="#FF9966"><strong>

      <select name="<?=$unique?>" id="<?=$unique?>">

        <? foreign_relation($table,$unique,$unique,$$unique,'warehouse_id='.$_SESSION['user']['depot'].' and status="'.$status.'"');?>

      </select>

    </strong></td>

    <td bgcolor="#FF9966"><strong>

      <input type="submit" name="submitit" id="submitit" value="VIEW DETAIL" style="width:170px; font-weight:bold; font-size:12px; height:30px; color:#090"/>

    </strong></td>

  </tr>

</table>



</form>

</div>



<?

$main_content=ob_get_contents();

ob_end_clean();

include ("../../template/main_layout.php");

?>