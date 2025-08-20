<?php
require_once "../../../assets/template/layout.top.php";
$title='Purchase Order Status';

do_calander('#fdate');
do_calander('#tdate');

$table = 'purchase_master';
$unique = 'po_no';
$status = 'UNCHECKED';
$target_url = '../po/po_print_view.php';

?>
<script language="javascript">
function custom(theUrl)
{
	window.open('<?=$target_url?>?<?=$unique?>='+theUrl);
}
</script>



<div class="form-container_large">
    
    <form action="" method="post" name="codz" id="codz">
            
        <div class="container-fluid bg-form-titel">
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <div class="form-group row m-0">
                        <label class="col-sm-5 col-md-5 col-lg-5 col-xl-5 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Date From:</label>
                        <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7 p-0">
                            <input type="text" name="fdate" id="fdate"  value="<?=$_POST['fdate']?>" />
                        </div>
                    </div>

                </div>
				<div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <div class="form-group row m-0">
                        <label class="col-sm-5 col-md-5 col-lg-5 col-xl-5 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Date To:</label>
                        <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7 p-0">
                            <input type="text" name="tdate" id="tdate"  value="<?=$_POST['tdate']?>" />
                        </div>
                    </div>

                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group row m-0">
                        <label class="col-sm-6 col-md-6 col-lg-6 col-xl-6 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Receive Warehouse:</label>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 p-0">
                            <select name="warehouse_id" id="warehouse_id">
								<option value="">ALL</option>
								<? foreign_relation('warehouse','warehouse_id','warehouse_name',$_POST['warehouse_id'],' use_type in ("WH","SD") ');?>
							 </select>

                        </div>
                    </div>
                </div>

                <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <input type="submit" name="submitit" id="submitit" value="View Detail" class="btn1 btn1-submit-input" />
					
                    
                </div>

            </div>
        </div>






            
        <div class="container-fluid pt-5 p-0 ">

                <table class="table1  table-striped table-bordered table-hover table-sm">
                    <thead class="thead1">
                    <tr class="bgc-info">
                        <th>PO No</th>
                        <th>PO Date</th>
                        <th>Reg No</th>
						<th>Quo No</th>
                        <th>Vendor Name </th>
                        <th>Warehouse</th>
                        

                        <th>Entry By</th>
						<th>Entry At</th>
						<th>Status</th>
						<th>Age</th>
						<th>Final Approve By</th>
						<th>Additional Status</th>
						
						<th>Action</th>
                        
                    </tr>
                    </thead>

                    <tbody class="tbody1">
					
					<? 
							if(isset($_POST['fdate'])){
								if($_POST['status']!=''&&$_POST['status']!='ALL')
								$con .= 'and a.status="'.$_POST['status'].'"';
								
								if($_POST['fdate']!=''&&$_POST['tdate']!='')
								$con .= 'and a.po_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';
								
								if($_POST['group_for']!='')
								$con .= 'and b.group_for = "'.$_POST['group_for'].'"';
								
								if($_POST['warehouse_id']!='')
								$con .= 'and b.warehouse_id = "'.$_POST['warehouse_id'].'"';
								
								$today = date('Y-m-d');
								$res='select  a.po_no,a.po_no, a.po_date, a.req_no,v.vendor_name, b.warehouse_name as warehouse,a.entry_by, a.quotation_no as note, c.fname as "by", a.entry_at,a.status,a.vat_approve, a.lm_approve,a.buh_approve,a.gm_approver,a.md_approve,a.complete_approve,DATE_FORMAT(a.checked_at, "%Y-%m-%d") as checkd_by_date from purchase_master a,warehouse b,user_activity_management c, vendor v where a.status!="Manual" and a.warehouse_id=b.warehouse_id and a.entry_by=c.user_id and a.vendor_id=v.vendor_id '.$con.' order by a.po_no desc';
						

								
								$query=mysql_query($res);
								
								While($row=mysql_fetch_object($query)){
								
								 //$row->checkd_by_date;
								for ($idate = $row->po_date; $idate <= $today; $idate = date('Y-m-d', strtotime($idate . ' + 1 day'))) {
								++$day_count;
								}
								$add7day = date('Y-m-d',strtotime($row->po_date.' + 7 day'));
								$age = $day_count-1;
								
								
								if($row->complete_approve<1 && $age>7){
								 $line_color = '#FF0000';
								 
								}elseif($row->complete_approve>0 && $row->checkd_by_date>$add7day){
								 $line_color = '#FF0000';
								 
								 
								}else{
								$line_color = '';
								}
								
								if($row->status=='CANCELED'){
								 $line_color = '#FFCCCC';
								}
								?>
                        <tr bgcolor="<?=$line_color?>">
						
                            <td><?=$row->po_no?></td>
                            <td><?=$row->po_date?></td>
                            <td><?=$row->req_no?></td>
							<td><?=$row->note?></td>
                            <td><?=$row->vendor_name?></td>
                            <td><?=$row->warehouse?></td>
                            
							<td><?=$row->by?></td>
							<td><?=$row->entry_at?></td>
							<td><?=$row->status?></td>
							<td><?=$age.' Days';$day_count=0;$age=0;?></td>
							<td>
								<?php
								$sales_amount=find_a_field('purchase_invoice','sum(amount)','po_no="'.$row->po_no.'"');
								$line_manager=find_a_field('user_activity_management','fname','user_id="'.$row->lm_approve.'"');
								$buh_user=find_a_field('user_activity_management','fname','user_id="'.$row->buh_approve.'"');
								$gm_user=find_a_field('user_activity_management','fname','user_id="'.$row->gm_approver.'"');
								$md_user=find_a_field('user_activity_management','fname','user_id="'.$row->md_approve.'"');
								$approve_layer=find_a_field('user_activity_management','user_roll','user_id="'.$row->entry_by.'"');
								
							if($row->lm_approve>1 && $row->buh_approve<1 && $row->gm_approver<1 && $row->complete_approve> 0){
							echo "<span style='color:blue;font-weight:bold;'>Line Manager ($line_manager)</span>";
							}
							elseif($row->md_approve<1 && $row->complete_approve> 0 && $row->gm_approver>1 && $row->buh_approve<1){
							echo "<span style='color:blue;font-weight:bold;'>GM(Operation) ($gm_user)</span>";
							}
							elseif($row->buh_approve>1 && $row->md_approve<1 &&  $row->complete_approve> 0){
							echo "<span style='color:blue;font-weight:bold;'>CEO(RCL) ($buh_user)</span>";
							}
							elseif($row->complete_approve > 0 && $row->md_approve> 1){
							echo "<span style='color:blue;font-weight:bold;'>Managing Director ($md_user)</span>";
							}
							?>
							
							</td>
							<td><?php
							if($row->vat_approve<1 && $row->complete_approve<1){
							echo "<span style='color:red;font-weight:bold;'>VAT Approval Pending</span>";
							}
							elseif($row->lm_approve<1 && $row->complete_approve<1){
							echo "<span style='color:red;font-weight:bold;'>Line Manager Approval Pending</span>";
							}
							elseif($row->buh_approve<1 && $row->complete_approve<1 && $row->gm_approver<1){
							echo "<span style='color:red;font-weight:bold;'>GM(Operation) Approval Pending</span>";
							}
							elseif($row->buh_approve<1 && $row->complete_approve<1 ){
							echo "<span style='color:red;font-weight:bold;'>CEO(RCL) Approval Pending</span>";
							}
							
							elseif($row->complete_approve<1 && $row->md_approve<1){
							echo "<span style='color:red;font-weight:bold;'>Managing Director Approval Pending</span>";
							}
							else{
							echo "<span style='color:green;font-weight:bold;'>Approval Complete</span>";
							}
							?></td>
							

                            
                            <td><button type="button" onclick="custom(<?=$row->po_no?>)" class="btn1 btn1-bg-submit">View</button></td>

                        </tr>
							<? 
							}
							?>
							
							<? }?>
                    </tbody>
                </table>





        </div>
    </form>
</div>








<?

require_once "../../../assets/template/layout.bottom.php";

?>