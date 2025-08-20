<?php

session_start();

require_once "../../../assets/support/inc.all.php";

require_once ('../../common/class.numbertoword.php');


$jv_no=$_REQUEST['jv_no'];

$jv_all=find_all_field('secondary_journal','','jv_no='.$jv_no);

$tr_from = $jv_all->tr_from;


if($jv_all->tr_from=='Receipt'){$voucher_name='RECEIPT VOUCHER';$vtypes='Receipt';}

elseif($jv_all->tr_from=='Payment'){$voucher_name='PAYMENT VOUCHER';$vtypes='Payment';}

elseif($jv_all->tr_from=='Journal'){$voucher_name='JOURNAL VOUCHER';$vtypes='Journal';}

elseif($jv_all->tr_from=='Contra'){$voucher_name='CONTRA VOUCHER';$vtype='contra';$vtypes='Contra';}

elseif($jv_all->tr_from=='Sales'){$voucher_name='SALES secondary_journal';$vtypes='Sales';}

elseif($jv_all->tr_from=='Purchase'){$voucher_name='PURCHASE secondary_journal';$vtypes='Purchase';}

elseif($jv_all->tr_from=='tds_vds_reconsiliation'){$voucher_name='TDS VDS Voucher';$vtypes='tds_vds_reconsiliation';}


else{$vtype=='secondary_journal';$voucher_name='secondary_journal VOUCHER';$vtypes='secondary_journal';}



$bill_no=$_REQUEST['bill_no'];
$bill_date=$_REQUEST['bill_date'];

if($_POST['check']=='CHECK')
{
$time_now = date('Y-m-d H:i:s');
$voucher_date = $_POST['voucher_date'];
$cc_code = $_POST['cc_code'];


//$po_no = find_a_field('secondary_journal','tr_no','tr_from = "Purchase" and jv_no='.$jv_no);
//$po = find_all_field('purchase_master','po_no','po_no='.$po_no);

	//$ssql='update purchase_invoice set bill_no="'.$bill_no.'", bill_date="'.$bill_date.'" where po_no="'.$prold->po_no.'"';
	//mysql_query($ssql);
	
	
    //$narration = 'Sale#'.$po->sale_no.'/ PO#'.$po->po_no.' (Bill#'.$bill_no.'/ Dt:'.$bill_date.')';
	
	$ssql='update secondary_journal set  secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where tr_from = "'.$jv_all->tr_from.'" and jv_no="'.$jv_no.'"';
	mysql_query($ssql);

$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$jv_all->tr_from.'"');

if($jv_config=="Yes"){

sec_journal_journal($jv_no,$jv_no,$jv_all->tr_from);

$time_now = date('Y-m-d H:i:s');

$up2='update secondary_journal set checked="YES", checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$jv_all->tr_from.'"';

mysql_query($up2);

$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$jv_all->tr_from.'"';
mysql_query($sa_up2);


}


}


if($_POST['check']=='RE-CHECK')
{
$time_now = date('Y-m-d H:i:s');
$voucher_date = strtotime($_POST['voucher_date']);
$cc_code = $_POST['cc_code'];


$jvold = find_a_field('secondary_journal','tr_id','tr_from = "Purchase" and jv_no='.$jv_no);
$prold = find_all_field('purchase_invoice','po_no','id='.$jvold);

	$ssql='update purchase_invoice set bill_no="'.$bill_no.'", bill_date="'.$bill_date.'" where po_no="'.$prold->po_no.'"';
	mysql_query($ssql);
	
	$narration = 'GR#'.$prold->id.'/'.$prold->po_no.'(PO#'.$prold->po_no.')(Bill#'.$bill_no.'/Dt:'.$bill_date.')';
	$ssql='update secondary_journal set narration="'.$narration.'",jv_date="'.$voucher_date.'", cc_code="'.
	$cc_code.'", checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'", checked="YES" , 	final_jv_no="'.$jv.'" where tr_from = "Purchase" and jv_no="'.$jv_no.'"';
	mysql_query($ssql);
	$sssql = 'delete from secondary_journal where group_for="'.$_SESSION['user']['group'].'" and tr_from ="Purchase" and tr_no="'.$prold->po_no.'"';
	mysql_query($sssql);
	$jv=next_secondary_journal_voucher_id();
	sec_secondary_journal_secondary_journal($jv_no,$jv,'Purchase');
}

$address=find_a_field('project_info','proj_address',"1");

$jv = find_all_field('secondary_journal','jv_date','jv_no='.$jv_no);

$cccode = $jv->cc_code;


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Voucher :.</title>
<link href="../../../assets/css/voucher_print.css" type="text/css" rel="stylesheet"/>

<link href="../../css_js/css/pagination.css" rel="stylesheet" type="text/css" />
<link href="../../css_js/css/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css" />
<link href="../../css_js/css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../css_js/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../css_js/js/jquery-ui-1.8.2.custom.min.js"></script>

<script type="text/javascript" src="../../css_js/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="../../css_js/js/jquery.validate.js"></script>
<script type="text/javascript" src="../../css_js/js/paging.js"></script>
<script type="text/javascript" src="https://mahirgrouperp.com/ddaccordion_new.js"></script>
<script type="text/javascript" src="../../css_js/js/js.js"></script>
<script type="text/javascript" src="../../css_js/js/jquery.ui.datepicker.js"></script>
<script type="text/javascript">
function hide()
{
    document.getElementById("pr").style.display="none";
}
function DoNav(theUrl)
{
	var URL = 'unchecked_voucher_view_popup_purchase.php?'+theUrl;
	popUp(URL);
}

function popUp(URL) 
{
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=800,height=800,left = 383,top = -16');");
}
</script>

<style>
.jvbutton {
    display: block;
	float:left;
    width: auto;
    height: 25px;
    background: #4E9CAF;
    padding: 5px 20px 5px 20px;
    text-align: center;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    line-height: 25px;
	
	margin-right: 20px;
}


.jvbutton:hover {

    color: #000000;
    font-weight: bold;

}

</style>

<? do_calander('#voucher_date');?><? do_calander('#bill_date');?>
</head>
<body><form action="" method="post">
<table width="820" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td><div class="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="1%">
			<img src="../../../logo/toufiq.png" style="width:150px;" />		</td>
            <td width="83%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center" class="title">

				<?
if($_SESSION['user']['group']>0)
echo find_a_field('user_group','group_name',"id=".$jv->group_for);
else
echo $_SESSION['proj_name'];
				?>                </td>
              </tr>
              <tr>
                <td align="center"><?=$address?></td>
              </tr>
              <tr>
                <td align="center"><table  class="debit_box" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td>&nbsp;</td>
                      <td width="325"><div align="center"><?=$voucher_name?></div></td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
          </tr>

        </table></td>
	    </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
    </table>
    </div></td>
  </tr>
  <tr>
    
	<td>	</td>
  </tr>
  
  <tr>

    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2" class="tabledesign_text">

<div id="pr">
<?php /*?><? if($jv->secondary_approval!='Yes'){?>
<div align="left">

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td width="45"><input name="button" type="button" onclick="hide();window.print();" value="Print" /></td>
    <td width="162" align="right">
	<? if($jv->tr_from=='Sales'){?>
	<a target="_blank" href="../../../sales_mod/pages/direct_sales/sales_invoice_new.php?v_no=<?=$jv->tr_no;?>">Invoice# <?=$jv->tr_no;?> </a>	
	<? }?>
	</td>
    <td width="160" align="right">Voucher Date :</td>
    <td width="342">

<input name="jv_no" type="hidden" value="<?=$jv_no?>" />
<input name="voucher_date" type="text" id="voucher_date" value="<?=$jv->jv_date;?>" />
<!--<input type="button" name="Submit" value="EDIT VOUCHER"  onclick="DoNav('<?php echo '&v_no='.$jv_no.'&view=Show' ?>');" />--></td>
    <td width="111"><input name="check" type="submit" id="check" value="CHECK" />
        <input type="hidden" name="req_no" id="req_no" value="<?=$jv->jv_on?>" /></td>
  </tr>
</table>

<a target="_blank" href="chalan_view2.php?v_no=<?=$pr->po_no?>"></a></div><? }else{?><?php */?>
<div align="left">

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#00CC00">
  <tr>
    <td width="51" bgcolor="#82D8CF"><input name="button" type="button" onclick="hide();window.print();" value="Print" /></td>
    <td width="670" align="right" bgcolor="#82D8CF">
		</td>
    <td width="22" bgcolor="#82D8CF">

<input name="jv_no" type="hidden" value="<?=$jv_no?>" />
<input name="voucher_date" type="hidden" id="voucher_date" value="<?=$jv->jv_date;?>" />
<!--<input type="button" name="Submit" value="EDIT VOUCHER"  onclick="DoNav('<?php echo '&v_no='.$jv_no.'&view=Show' ?>');" />--></td>
    <td width="85" bgcolor="#82D8CF"><input name="check" type="hidden" id="check" value="RE-CHECK" />
        <input type="hidden" name="req_no" id="req_no" value="<?=$jv->jv_on?>" /></td>
  </tr>
</table>

<a target="_blank" href="chalan_view2.php?v_no=<?=$pr->po_no?>"></a></div><?php /*?><?  }?><?php */?>
</div></td>
        </tr>
      <tr>
        <td class="tabledesign_text">Voucher Date :
          <?php echo date('d-m-Y',strtotime($jv->jv_date));?></td>
        <td class="tabledesign_text">
          TR From:
          <?=$jv->tr_from;?></td>
      </tr>
      <tr>
        <td class="tabledesign_text">Voucher No  :
          <?=$jv_no?></td>
        <!--<td class="tabledesign_text">TR No  :
          <?=$jv->tr_no;?></td>-->
      </tr>
    </table></td>
  </tr>
 
  <tr style="font-size:14px">
    <td>&nbsp;</td>
  </tr>
  <tr style="font-size:14px">
    <td><strong>Remarks: </strong><?= $jv->remarks?></td>
  </tr>
  <?php /*?><tr style="font-size:14px">
    <td><? if($cccode>0){?>
      <strong>CC CODE:</strong> <? echo find_a_field('cost_center','center_name',"id='$cccode'")?><? }?></td>
  </tr><?php */?>
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#000000" class="tabledesign">
      <tr>
        <td align="center" bgcolor="#82D8CF"><div align="center"><strong>SL</strong></div></td>
        <td align="center" bgcolor="#82D8CF"><strong>GL Code </strong></td>
        <td align="center" bgcolor="#82D8CF"><strong>GL Name </strong></td>
        <td align="center" bgcolor="#82D8CF"><strong>Cost Center</strong></td>
        <td align="center" bgcolor="#82D8CF">Sub Ledger</td>
        <td align="center" bgcolor="#82D8CF"><strong>Narration</strong></td>
        <td bgcolor="#82D8CF"><strong>Debit</strong></td>
        <td bgcolor="#82D8CF"><strong>Credit</strong></td>
      </tr>
      
	  <?
 $sql2="SELECT a.ledger_id,a.ledger_name,sum(dr_amt) as dr_amt, a.ledger_group_id, b.narration, b.reference_id, b.cc_code,s.sub_ledger_name FROM accounts_ledger a, secondary_journal b left join general_sub_ledger s on s.sub_ledger_id=b.sub_ledger where b.jv_no='$jv_no' and a.ledger_id=b.ledger_id and jv_no=$jv_no and dr_amt>0 group by b.id order by dr_amt desc";
$data2=mysql_query($sql2);
while($info=mysql_fetch_object($data2)){		  
	  ?>
      <tr>
        <td align="left"><div align="center">
          <?=++$s;
		  ?>
        </div></td>
        <td align="left"><?=$info->ledger_id?>		</td>
        <td align="left"><?=$info->ledger_name?></td>
        <td align="left"><?=find_a_field('cost_center','center_name','id='.$info->cc_code);?></td>
        <td align="left"><?=$info->sub_ledger_name?></td>
        <td align="left"><?=$info->narration?></td>
        <td align="right"><? echo number_format($info->dr_amt,2); $ttd = $ttd + $info->dr_amt;?></td>
        <td align="right"><? echo number_format($info->cr_amt,2); $ttc = $ttc + $info->cr_amt;?></td>
        </tr>
<?php }?>
<?
 $sql2="SELECT a.ledger_id,a.ledger_name,sum(cr_amt) as cr_amt, a.ledger_group_id, b.narration, b.reference_id, b.cc_code,s.sub_ledger_name FROM accounts_ledger a, secondary_journal b left join general_sub_ledger s on s.sub_ledger_id=b.sub_ledger where b.jv_no='$jv_no' and a.ledger_id=b.ledger_id and jv_no=$jv_no and cr_amt>0 group by b.id order by cr_amt desc";
$data2=mysql_query($sql2);
while($info=mysql_fetch_object($data2)){		  
	  ?>
      <tr>
        <td align="left"><div align="center">
          <?=++$s;?>
        </div></td>
        <td align="left"><?=$info->ledger_id?></td>
        <td align="left"><?=$info->ledger_name?></td>
        <td align="left"><?=find_a_field('cost_center','center_name','id='.$info->cc_code);?></td>
        <td align="left"><?=$info->sub_ledger_name?></td>
        <td align="left"><?=$info->narration?></td>
        <td align="right"><? echo number_format($info->dr_amt,2); $ttd = $ttd + $info->dr_amt;?></td>
        <td align="right"><? echo number_format($info->cr_amt,2); $ttc = $ttc + $info->cr_amt;?></td>
        </tr>
<?php }?>





      <tr>
        <td colspan="6" align="right"><strong>Total : </strong></td>
        <td align="right"><strong>
          <?=number_format($ttd,2)?>
        </strong></td>
        <td align="right"><strong>
          <?=number_format($ttc,2)?>
        </strong></td>
        </tr>
      
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Amount in Word : 

	 (<? echo convertNumberMhafuz($ttc)?>)	 </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="tabledesign_text"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
        <td align="center" valign="bottom"><? echo find_a_field('user_activity_management','fname','user_id='.$jv->entry_by);?><br />................................<br />Prepared By</td> 
		<?
		$ssql = 'select v.*,h.entry_by from voucher_approval_matrix v left join voucher_approved_history h on h.priority=v.priority and h.voucher_mod=v.voucher_mod and h.voucher_no="'.$jv_all->jv_no.'" where v.voucher_mod="'.$tr_from.'" order by v.priority asc';
		$qqry = mysql_query($ssql);
		while($ap_data=mysql_fetch_object($qqry)){
		$approver = find_a_field('user_activity_management','fname','user_id="'.$ap_data->entry_by.'"');
		?>
        <td align="center" valign="bottom"><?=$approver?><br />................................<br /><?=$ap_data->signatory?></td>
		<? } ?>
      </tr>
	
      
      
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>
