<?php
 
require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";
$title='Purchase Order Status';

do_calander('#fdate');
do_calander('#tdate');

$table = 'purchase_master';
$unique = 'po_no';
$status = 'UNCHECKED';
$target_url = '../po/po_print_view_jamuna.php';
$target_url3 = '../po/grn_status_credit.php';
$tr_type="Show";
?>



<script language="javascript">
function custom(theUrl)
{
	window.open('<?=$target_url?>?<?=$unique?>='+ encodeURIComponent(theUrl));
}
function custom2(theUrl)
{   
	$.ajax({
		url: 'set_session.php',
		type: 'POST', 
		data: {po_no: theUrl},
		success: function(response) {

			window.open('po_create.php');
		}
	});
}
function custom3(theUrl)
{
	window.open('<?=$target_url3?>?<?=$unique?>='+theUrl);
}

</script>



<div class="form-container_large">
    
    <form action="" method="post" name="codz" id="codz">
	<div align="center"><?=$_SESSION['poMsg'];unset($_SESSION['poMsg']);?></div>
            
        <div class="container-fluid bg-form-titel">
            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <div class="form-group row m-0">
                        <label class="col-sm-5 col-md-5 col-lg-5 col-xl-5 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">From:</label>
                        <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7 p-0">
                            <input type="text" name="fdate" id="fdate"  value="<?=$_POST['fdate']?>" autocomplete="off" />
                        </div>
                    </div>

                </div>
				<div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <div class="form-group row m-0">
                        <label class="col-sm-5 col-md-5 col-lg-5 col-xl-5 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">To:</label>
                        <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7 p-0">
                            <input type="text" name="tdate" id="tdate"  value="<?=$_POST['tdate']?>" autocomplete="off" />
                        </div>
                    </div>

                </div>
                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <div class="form-group row m-0">
                        <label class="col-sm-6 col-md-6 col-lg-6 col-xl-6 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Warehouse:</label>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 p-0">
                            <select name="warehouse_id" id="warehouse_id">
								<option value="">ALL</option>
								<? foreign_relation('warehouse','warehouse_id','warehouse_name',$_POST['warehouse_id'],' use_type in ("WH","SD") and group_for="'.$_SESSION['user']['group'].'"');?>
							 </select>

                        </div>
                    </div>
                </div>

                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <div class="form-group row m-0">
                        <label class="col-sm-6 col-md-6 col-lg-6 col-xl-6 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Vendor:</label>
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 p-0">
                            <select name="warehouse_id" id="warehouse_id">
								<option value="">ALL</option>
								<? foreign_relation('warehouse','warehouse_id','warehouse_name',$_POST['warehouse_id'],' use_type in ("WH","SD") and group_for="'.$_SESSION['user']['group'].'"');?>
							 </select>

                        </div>
                    </div>
                </div>



                <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <input type="submit" name="submitit" id="submitit" value="View Detail" class="btn1 btn1-submit-input" />
					
                    
                </div>

            </div>
        </div>



        <div class="row pt-2 justify-content-center">
				<div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
				<div class="form-group row m-0">

					<a href="po_create.php?new=2" class="btn btn-info btn-block">Create New Order</a>
					
				</div>
				</div>
			</div>


            
        <div class="container-fluid pt-2 p-0 ">

                <table class="table1  table-striped table-bordered table-hover table-sm">
                    <thead class="thead1">
                    <tr class="bgc-info">
                        <th>PO No</th>
                        <th width="9%">PO Date</th>
                        <th>Reg No</th>
						<th>Quo No</th>
                        <th>Vendor Name </th>
                        <th>Warehouse</th>
                        

                        <th>Entry By</th>
						<th>Entry At</th>
						<th>Status</th>
						<th>Approval Authorities</th>
						<th colspan="2">Action</th>
                        <th>Grn</th>
                        
                    </tr>
                    </thead>

                    <tbody class="tbody1">
					
					<?
                            $last_two_months = date('Y-m-d', strtotime('-2 months'));

                            $con .= ' and a.po_date between "'.$last_two_months.'" and "'.date('Y-m-d').'"';
                            
							if(isset($_POST['fdate'])){
								if($_POST['status']!=''&&$_POST['status']!='ALL')
								$con .= 'and a.status="'.$_POST['status'].'"';
								
								if($_POST['fdate']!=''&&$_POST['tdate']!='')
								$con .= 'and a.po_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';
								
								if($_POST['group_for']!='')
								$con .= 'and b.group_for = "'.$_POST['group_for'].'"';
								
								if($_POST['warehouse_id']!='')
								$con .= 'and b.warehouse_id = "'.$_POST['warehouse_id'].'"';
                            }
								$res='select  a.po_no,a.po_no, a.po_date,a.current_approval_level, a.req_no,v.vendor_name, b.warehouse_name as warehouse, a.quotation_no as note, c.fname as "by", a.entry_at,a.status from purchase_master a,warehouse b,user_activity_management c, vendor v where   a.warehouse_id=b.warehouse_id and  a.group_for="'.$_SESSION['user']['group'].'" and a.entry_by=c.user_id and a.vendor_id=v.vendor_id '.$con.' order by a.po_no desc';
						

								
								$query=db_query($res);
								
								While($row=mysqli_fetch_object($query)){
								
								
								?>
                        <tr>
                            <td><?=$row->po_no?></td>
                            <td><?=$row->po_date?></td>
                            <td><?=$row->req_no?></td>
							<td><?=$row->note?></td>
                            <td style="text-align:left"><?=$row->vendor_name?></td>
                            <td><?=$row->warehouse?></td>
                            
							<td><?=$row->by?></td>
							<td><?=$row->entry_at?></td>
							<td><?=$row->status?></td>

							<?
                            if($row->status=='UNCHECKED'){
                            ?>
                            <td>
                            <?
                            $current_level = find_a_field('approval_matrix_history','MIN(approval_level)','tr_no="'.$row->po_no.'" and tr_from="Purchase" and status="1"');
                            if( $current_level>0){
                              $res_approval_member = 'SELECT a.*,u.fname from approval_matrix_setup a JOIN user_activity_management u ON a.user_id=u.user_id where a.level<"'.$current_level.'" and a.tr_from="Purchase" ORDER BY a.level DESC;
                                ';
                            }else{
                                $res_approval_member = 'SELECT a.*,u.fname from approval_matrix_setup a JOIN user_activity_management u ON a.user_id=u.user_id where a.tr_from="Purchase" ORDER BY a.level DESC;';
                            }



                            $query_approval_member=db_query($res_approval_member);
                            
                            While($row2=mysqli_fetch_object($query_approval_member)){ 
                                // echo '<pre>';
                                // echo print_r($row2);
                                // echo '</pre>';

                            ?>
                             <span style="background-color: #898121; color: white; padding: 4px 8px; border-radius: 3px;"><?=$row2->fname?></span><br>


                            <? } ?>
                            </td>
                            <? }else{ ?>
                                <td></td>
                           
                            <? } ?>
                            <td>
							
							<button type="button" onclick="custom('<?=url_encode($row->po_no);?>')" class="btn2 btn1-bg-submit"><i class="fa-solid fa-eye"></i></button>
							
							
							</td>
                            <td>
                            <? if($row->status=='MANUAL'){ ?>
                            <button type="button" onclick="custom2('<?=$row->po_no;?>')" class="btn2 btn1-bg-update"><i class="fa-solid fa-pen-to-square"></i></button>
                            <? } ?>
                            </td>
                            <td>
							
							<button type="button" onclick="custom3('<?=$row->po_no?>')" class="btn2 btn1-bg-submit"><i class="fa-solid fa-eye"></i></button>
							
							
							</td>

                        </tr>
							<? 
							}
							?>
							
							
                    </tbody>
                </table>





        </div>
    </form>
</div>





<?
$tr_from="Purchase";
require_once SERVER_CORE."routing/layout.bottom.php";
?>