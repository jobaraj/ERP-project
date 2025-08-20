<?php
require_once "../../../assets/template/layout.top.php";
$title='Voucher Varification';
$now=time();
do_calander('#do_date_fr');
do_calander('#do_date_to');
?>



<form id="form2" name="form2" method="post" action="">
<div class="row bg-form-titel">
    <div class="col-sm-10 col-md-10 col-lg-10 col-xl-10">
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                <div class="form-group row m-0">
                    <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Date From</label>
                    <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">
                       <input name="do_date_fr" type="text" id="do_date_fr" class="form-control" value="<?=$_POST['do_date_fr']?>" required autocomplete=
					   "off" />
                    </div>
                </div>

            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                <div class="form-group row m-0">
                    <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Date To</label>
                    <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">
                        <input name="do_date_to" type="text" id="do_date_to" value="<?=$_POST['do_date_to']?>" class="form-control"  required autocomplete=
					   "off" />

                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 pt-1">
                <div class="form-group row m-0">
                    <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Voucher Type</label>
                    <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">
                        <select name="voucher_type" id="voucher_type" class="form-control">
							<option></option>
			
							  <? foreign_relation('voucher_config','voucher_type','voucher_type',$_POST['voucher_type'],'1');?>
			
						</select>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2 pt-3">
        
        <input type="submit" name="submitit" id="submitit" value="Voucher View" class="btn1 btn1-bg-submit"/>
    </div>
</div>


	<div class="container-fluid pt-5 p-0 ">

			<table class="table1  table-striped table-bordered table-hover table-sm">
				<thead class="thead1">
				<tr class="bgc-info">
					 <th>SL</th>

					  <th>TR No #</th>
				
					  <th>JV No #</th>
				
					  <th>JV Date </th>
				
					  <th>TR From </th>
					  <th>DR Amt </th>
				
					  <th>CR Amt </th>
					  <th>Entry By </th>
				
					  <th>Entry At </th>
				
					  <th>&nbsp;</th>
				
					  <th>Checked?</th>
				</tr>
				</thead>

				<tbody class="tbody1">

				<?




if(isset($_POST['submitit'])){
		 if($_POST['do_date_fr']!=''){
		 $date_con = " and j.jv_date between '". $_POST['do_date_fr']."' AND  '". $_POST['do_date_to']."'";
		 }

	  $i=0;

		if($_POST['checked']!='') $checked_con = ' and j.secondary_approval="'.$_POST['checked'].'" ';

	 	if($_SESSION['user']['group']>1) $group_s='AND j.group_for='.$_SESSION['user']['group'];

		if($_POST['depot_id']>0) $depot_con = ' and w.warehouse_id="'.$_POST['depot_id'].'" ';

		if($_POST['voucher_type']!='') {$voucher_type_con=' and j.tr_from="'.$_POST['voucher_type'].'"';}
		


		 $sql=" select DISTINCT j.tr_no, j.jv_no, j.jv_date, sum(j.dr_amt) as dr_amt, sum(j.cr_amt) as cr_amt, j.tr_from, u.fname, j.entry_at, j.checked 
				

				FROM

				 secondary_journal j, accounts_ledger l, user_activity_management u 
				  
			

				WHERE


				 j.entry_by = u.user_id AND j.checked in ('','NO') and 
				
				  j.ledger_id = l.ledger_id ".$group_s.$checked_con.$depot_con.$voucher_type_con.$date_con." group by j.jv_no";

	  $query=mysql_query($sql);

	  

	  while($data=mysql_fetch_row($query)){
    $received = $received + $data[1];
    $already_approved = find_a_field('voucher_approved_history','max(priority)','voucher_no="'.$data[1].'" and voucher_mod="'.$data[5].'"');
	if($already_approved==0){
	$min_priority = find_a_field('voucher_approval_matrix','min(priority)','voucher_mod="'.$data[5].'"');
	$min_approver = find_a_field('voucher_approval_matrix','approver_id','voucher_mod="'.$data[5].'" and priority="'.$min_priority.'" and approver_id="'.$_SESSION['user']['id'].'"');
	}else{
	$min_approver = find_a_field('voucher_approval_matrix','approver_id','voucher_mod="'.$data[5].'" and priority>"'.$already_approved.'" and approver_id="'.$_SESSION['user']['id'].'"');
	}
	
	if($min_approver==$_SESSION['user']['id']){
	//approver check end
	$approver_name = find_a_field('user_activity_management','fname','user_id="'.$min_approver.'"');
	
	  ?>



      <tr class="alt">

      <td align="center">

        <?=++$i;?>

     </td>

      <td align="center"><? echo $data[0];?></td>

      <td align="center"><? echo $data[1];?></td>

      <td align="center"><?= date('d-m-Y',strtotime($data[2]));?></td>

      <td align="center"><? echo $data[5];?></td>
      <td align="center"><? echo number_format($data[3],2); $total_dr +=$data[3];?></td>

      <td align="center"><? echo number_format($data[4],2); $total_cr +=$data[4];?></td>
      <td align="center"><? echo $data[6];?></td>

      <td align="center"><? echo $data[7];?></td>

      <td align="center"><a target="_blank" href="general_voucher_print_view_for_approval.php?jv_no=<?=$data[1] ?>"><img src="../images/print.png" width="20" height="20" /></a></td>

      <td align="center"><span id="divi_<?=$data[1]?>">

            <? 

						  if(($data[8]=='YES')){
			
			?>
			
			<input type="button" name="Button" value="YES"  onclick="window.open('general_voucher_print_view_for_approval.php?jv_no=<?=$data[1] ?>');" class="btn1 btn1-bg-submit"/>
			
			<?
			
			}elseif(($data[8]=='NO')){
			
			?>
			
			<input type="button" name="Button" value="NO"  onclick="window.open('general_voucher_print_view_for_approval.php?jv_no=<?=$data[1] ?>','_self');" class="btn1 btn1-bg-cancel" />
			
			<? }?>
			
					  </span></td>
				  </tr>
			
				  <? } } ?>
				  <tr class="alt">

        <td align="center">&nbsp;</td>

        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center"><strong>Total</strong></td>
        <td align="center"><strong><?= number_format($total_dr,2);?></strong></td>
        <td align="center"><strong><?= number_format($total_cr,2);?></strong></td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>

        <td align="center">&nbsp;</td>
      </tr>
<? }?>
				</tbody>
			</table>





		</div>

</form>


<br /><br />

		

<?

require_once "../../../assets/template/layout.bottom.php";

?>

