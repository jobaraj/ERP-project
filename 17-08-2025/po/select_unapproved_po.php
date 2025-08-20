<?php
require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

$tr_type="Show";
$title='Unapproved Purchase Order';

do_calander('#fdate');
do_calander('#tdate');

// var_dump($_SESSION);

$tr_from="Purchase";
$table = 'purchase_master';
$unique = 'po_no';
$status = 'UNCHECKED';
$target_url = '../po/po_checking.php';
/*if($_POST[$unique]>0)
{
$_SESSION[$unique] = $_POST[$unique];
header('location:'.$target_url);
}*/

?>


<script language="javascript">
function custom(theUrl)
{
	window.open('<?=$target_url?>?<?=$unique?>='+ encodeURIComponent(theUrl));
}
</script>


<div class="form-container_large">
    
    <form action="" method="post" name="codz" id="codz">
            
        <div class="container-fluid bg-form-titel">
            <div class="row">
                <div class="col-sm-5 col-md-5 col-lg-5 col-xl-5">
                    <div class="form-group row m-0">
                        <label class="col-sm-5 col-md-5 col-lg-5 col-xl-5 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Date From:</label>
                        <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7 p-0">
                            <input type="text" name="fdate" id="fdate"  value="<? if($_POST['fdate']!='') echo $_POST['fdate']; else echo date('Y-m-01')?>" />
    </strong></td>
                        </div>
                    </div>

                </div>
				<div class="col-sm-5 col-md-5 col-lg-5 col-xl-5">
                    <div class="form-group row m-0">
                        <label class="col-sm-5 col-md-5 col-lg-5 col-xl-5 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Date To:</label>
                        <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7 p-0">
                            <input type="text" name="tdate" id="tdate"  value="<? if($_POST['tdate']!='') echo $_POST['tdate']; else echo date('Y-m-d')?>" />
                        </div>
                    </div>

                </div>
                <!--<div class="col-sm-4 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group row m-0">
                        <label class="col-sm-5 col-md-5 col-lg-5 col-xl-5 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Unapprove PO:</label>
                        <div class="col-sm-7 col-md-7 col-lg-7 col-xl-7 p-0">
                            
								<select name="status" id="status" >
								<option></option>
								<option <?//=($_POST['status']=='UNCHECKED')?'selected':''?>>UNCHECKED</option>
								<option <?//=($_POST['status']=='CHECKED')?'selected':''?>>CHECKED</option>
								</select>
									

                        </div>
                    </div>
                </div>-->

                <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    
					<input type="submit" name="submitit" id="submitit" value="View Detail" class="btn1 btn1-submit-input" />
                    
                </div>

            </div>
        </div>






            
        <div class="container-fluid pt-5 p-0 ">

                <table class="table1  table-striped table-bordered table-hover table-sm">
                    <thead class="thead1">
                    <tr class="bgc-info">
                        <th>Po No</th>
                        <th width="9%">Po Date</th>
                        <th>Vendor Name</th>

                        <th>Warehouse Name </th>
                        <th>Created By</th>
                        <th>Entry At</th>
						<th>Status</th>

                        <th>Action</th>
                        
                    </tr>
                    </thead>

                    <tbody class="tbody1">
						<? 

							//if($_POST['status']!=''&&$_POST['status']!='ALL')
							//$con .= 'and p.status="'.$_POST['status'].'"';
							
							if($_POST['fdate']!=''&&$_POST['tdate']!='')
							$con .= 'and p.po_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';
							
                            $res = '
                            SELECT 
                                p.po_no, 
                                p.po_no AS duplicate_po_no, 
                                p.po_date, 
                                v.vendor_name, 
                                w.warehouse_name, 
                                u.fname AS created_by, 
                                p.entry_at, 
                                p.status 
                            FROM 
                                purchase_master p
                            JOIN 
                                user_activity_management u ON u.user_id = p.entry_by
                            JOIN 
                                vendor v ON v.vendor_id = p.vendor_id
                            JOIN 
                                warehouse w ON w.warehouse_id = p.warehouse_id

                            WHERE 
                                p.group_for = "'.$_SESSION['user']['group'].'"
                                AND p.status = "UNCHECKED"
                                '.$con.'
                            ORDER BY 
                                p.po_no DESC';
								
								$query=db_query($res);
								
								While($row=mysqli_fetch_object($query)){
								
								if(approval_validity_check($row->po_no,"Purchase")>0){  
								?>
                        <tr>
                            <td><?=$row->po_no?></td>
                            <td><?=$row->po_date?></td>
                            <td style="text-align:left"><?=$row->vendor_name?></td>

                            <td style="text-align:left"><?=$row->warehouse_name?></td>
                            <td><?=$row->created_by?></td>
                            <td><?=$row->entry_at?></td>
							<td><?=$row->status?></td>

                            
                            <td>
							<button type="button" onclick="custom('<?=$row->po_no;?>')" class="btn2 btn1-bg-submit"><i class="fa-solid fa-eye"></i></button>
							</td>

                        </tr>
							<? 
                              }
							}
							?>
                    </tbody>
                </table>





        </div>
    </form>
</div>



<?
require_once SERVER_CORE."routing/layout.bottom.php";
?>