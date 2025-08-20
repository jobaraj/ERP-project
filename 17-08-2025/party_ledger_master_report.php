<?
require_once "../../../assets/template/layout.top.php";


date_default_timezone_set('Asia/Dhaka');
//echo $_REQUEST['report'];
if(isset($_REQUEST['submit'])&&isset($_REQUEST['report'])&&$_REQUEST['report']>0)
{
	if((strlen($_REQUEST['t_date'])==10))
	{
		$t_date=$_REQUEST['t_date'];
		$f_date=$_REQUEST['f_date'];
	}
	
if($_REQUEST['product_group']!='')  $product_group=$_REQUEST['product_group'];
if($_REQUEST['item_brand']!='') 	$item_brand=$_REQUEST['item_brand'];
if($_REQUEST['item_id']>0) 		    $item_id=$_REQUEST['item_id'];
if($_REQUEST['dealer_code']>0) 	    $dealer_code=$_REQUEST['dealer_code'];
if($_REQUEST['dealer_type']!='') 	$dealer_type=$_REQUEST['dealer_type'];

if($_REQUEST['status']!='') 		$status=$_REQUEST['status'];
if($_REQUEST['do_no']!='') 		    $do_no=$_REQUEST['do_no'];
if($_REQUEST['area_id']!='') 		$area_id=$_REQUEST['area_id'];
if($_REQUEST['zone_id']!='') 		$zone_id=$_REQUEST['zone_id'];
if($_REQUEST['region_id']>0) 		$region_id=$_REQUEST['region_id'];
if($_REQUEST['depot_id']!='') 		$depot_id=$_REQUEST['depot_id'];
if($_REQUEST['month_id']!='') 		$month_id=$_REQUEST['month_id'];

$item_info = find_all_field('item_info','','item_id='.$item_id);

if(isset($item_brand)) 			{$item_brand_con=' and i.item_brand="'.$item_brand.'"';} 
if(isset($dealer_code)) 		{$dealer_con=' and a.dealer_code="'.$dealer_code.'"';} 
 
if(isset($t_date)) 				{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if(isset($product_group)) {
if($product_group=='ABCDE') 
$pg_con=' and d.product_group!="M" and d.product_group!=""';
else $pg_con=' and d.product_group="'.$product_group.'"';
}

/*if(isset($product_group)) {			
	if($product_group=='ABCDE') 	{$pg_con=' and d.product_group!="M" and d.product_group!=""';}
	elseif($product_group=='ABD')	{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con=' and i.sales_item_type="'.$product_group.'"';}
}*/ 


//if(isset($dealer_type)) 		{if($dealer_type=='Distributor') {$dtype_con=' and d.dealer_type="Distributor"';} else {$dtype_con=' and d.dealer_type!="Distributor"';}}
//if(isset($dealer_type)) 		{if($dealer_type=='Distributor') {$dealer_type_con=' and d.dealer_type="Distributor"';} else {$dealer_type_con=' and d.dealer_type!="Distributor"';}} 
if($dealer_type!=''){
if($dealer_type=='MordernTrade')		{$dtype_con=$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';}
else 									{$dtype_con=$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';}}
		
if(isset($dealer_code)) 		{$dealer_con=' and m.dealer_code='.$dealer_code;} 
if(isset($item_id))				{$item_con=' and i.item_id='.$item_id;} 
if(isset($depot_id)) 			{$depot_con=' and d.depot="'.$depot_id.'"';} 
//if(isset($dealer_code)) 		{$dealer_con=' and a.dealer_code="'.$dealer_code.'"';} 
//if(isset($zone_id)) 			{$zone_con=' and a.buyer_id='.$zone_id;}
//if(isset($region_id)) 		{$region_con=' and d.id='.$region_id;}
//if(isset($item_id)) 			{$item_con=' and b.item_id='.$item_id;} 
//if(isset($status)) 			{$status_con=' and a.status="'.$status.'"';} 
//if(isset($do_no)) 			{$do_no_con=' and a.do_no="'.$do_no.'"';} 
//if(isset($t_date)) 			{$to_date=$t_date; $fr_date=$f_date; $order_con=' and o.order_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
//if(isset($t_date)) 			{$to_date=$t_date; $fr_date=$f_date; $chalan_con=' and c.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

switch ($_REQUEST['report']) {
    case 1:
	$report="Party Ledger for Corporate";
	break;
	case 1001:
	$report="Party Ledger for Distributor";
	break;
	case 1005:
	$report="Party Ledger for PMMD";
	break;
	
	case 100555:
	$report="Accounts Receive Report";
	break;
	
	
		case 100556:
	$report=" Accounts Receive Report Monthwise Summary";
	break;
	
	case 1100:
	$report="Sales Return Report";
	break;
	
case 21:
$report="Over DO Report";

		if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; 
		$date_con .= ' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		if(isset($dealer_type)){
		if($dealer_type=='MordernTrade'){$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';}
		else {$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';}
		}

$sql='select m.do_date, m.do_no, m.dealer_code, d.dealer_name_e as dealer_name, d.product_group,
m.rcv_amt as do_receive, m.payment_by, m.bank, m.branch,
m.received_amt as acc_receive, m.rec_date,m.checked_at, 
m.received_status,m.send_to_depot_at as do_approve_time,m.remarks,m.pb_amt as opening_balance, do_total as do_amount, m.over_do

from dealer_info d, sale_do_master m
where d.dealer_code=m.dealer_code
'.$date_con.$warehouse_con.$item_con.$dealer_type_con.$pg_con.' 
and m.over_do>0 and m.status in ("CHECKED","COMPLETED")
group by m.do_no
order by do_approve_time';


break;	
	
	

case 301:
$report="DO Pending Summary Brief";
break;


    case 1999:
	$report="DO Report for Scratch Card";
	$product_group = 'A';
	break;
	
case 2001:
		$report="Item Wise Chalan  Details Report (At A Glance)";
		
	break;
	




case 2002:
		$report="Last Year Vs This Year Item Wise Sales Report (Periodical)";
	break;
	
	case 2022:
		$report="Last Month Vs This Month Item Wise Sales Report (Periodical)";
	break;
	
	
	case 3001:
		$report="Last Year Vs This Year Zone/Item Wise Sales Report";
	break;
	

case 2005:
		$report="Yearly Distributor Sales Report";
		if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 
		
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		if(isset($dealer_type)) 		{$con.=' and d.dealer_type="'.$dealer_type.'"';}
		if(isset($product_group)) 		{$pg_con=' and i.sales_item_type like "%'.$product_group.'%"';}	
	
break;	

case 2004:
		$report="Last Year Vs This Year Item Wise Sales Report(With National Target)";	
break; 

case 2008:
		$report="National Sales Vs National Target(Year 2 Year Item wise)";
	
break; 

case 2009:
		$report="Modern Trade Sales Vs Target";	
break; 

case 201:
		$report="Group wise sales Comparison Report";
	
break; 

case 2006:
$report="Last Year Vs This Year Item Wise Sales Report(With Target)";	
break;

case 2025:
$report="Item Wise Target Vs Primary DO Report";	
break;

case 2007:
		$report="Party wise Target Vs Sales";
	
break;
	
case 2003:
		$report="Last Year Vs This Year Single Item Dealer Wise Sales Report (Periodical)";
		if(isset($t_date)) {
		$to_date=$t_date; $fr_date=$f_date; 
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}


		if(isset($depot_id)) 				{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 			{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		if(isset($dealer_type)) 			{$con.=' and d.dealer_type="'.$dealer_type.'"';}
		if(isset($item_id))		 			{$con.=' and a.item_id="'.$item_id.'"';}
		
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and b.BRANCH_ID="'.$region_id.'"';}
		$sql='select 
		dealer_name_e dealer_name,
		dealer_code,
		AREA_NAME area_name,
		ZONE_NAME zone_name,
		BRANCH_NAME region_name
		from dealer_info d, area a, branch b, zon z where a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_type="Distributor" and d.area_code=a.AREA_CODE  '.$product_group_con.$acon.' 
	    order by dealer_name_e';

		$sql2='select 
		d.dealer_code,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m, sale_do_chalan a, item_info i where 
		d.dealer_code=m.dealer_code and a.item_id=i.item_id and m.id=a.order_no  and i.item_brand!="" and  
	a.unit_price>0 '.$con.$date_con.$product_group_con.$item_con.' 
	group by  d.dealer_code';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->dealer_code] = $data2->sale_price;
	$this_year_sale_qty[$data2->dealer_code] = $data2->qty;
	$this_year_sale_pkt[$data2->dealer_code] = $data2->pkt;
	}
	
	
/*$sql2='select 
		i.item_id,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i where 
		d.dealer_code=m.dealer_code and m.id=a.order_no  and i.item_brand!="" and  
	a.unit_price>0  and a.item_id=i.item_id '.$con.$ydate_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by  a.item_id';*/
	
	
// last year	
$sql2='select 
		d.dealer_code,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m, sale_do_chalan a, item_info i where 
		d.dealer_code=m.dealer_code and a.item_id=i.item_id and m.id=a.order_no  and i.item_brand!="" and  
	a.unit_price>0 '.$con.$ydate_con.$product_group_con.$item_con.' 
	group by  d.dealer_code';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->dealer_code] = $data2->sale_price;
	$last_year_sale_qty[$data2->dealer_code] = $data2->qty;
	$last_year_sale_pkt[$data2->dealer_code] = $data2->pkt;
	}
	break;
	
		case 20031:
		$report="Last Year Vs This Year Single Item Region Wise Sales Report (Periodical)";
		if(isset($t_date)) 
		{
		$to_date=$t_date; $fr_date=$f_date; 
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($product_group)) 			{$product_group_con.=' and d.product_group="'.$product_group.'"';}
		if(isset($depot_id)) 				{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 			{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		if(isset($dealer_type)) 			{$con.=' and d.dealer_type="'.$dealer_type.'"';}
		if(isset($item_id))		 			{$con.=' and a.item_id="'.$item_id.'"';}
		
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and b.BRANCH_ID="'.$region_id.'"';}
		
$sql='select 
		BRANCH_NAME region_name,
		BRANCH_ID
		from dealer_info d, area a, branch b, zon z 
		where a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_type="Distributor" and d.area_code=a.AREA_CODE  '.$product_group_con.$acon.' 
	    group by BRANCH_NAME';

// this year
$sql2='select 
		BRANCH_ID,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m, sale_do_chalan a, item_info i, area ar, branch b, zon z 
		where 
		d.dealer_code=m.dealer_code and a.item_id=i.item_id and m.id=a.order_no  and i.item_brand!="" and  
		ar.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_type="Distributor" and d.area_code=ar.AREA_CODE and 
	a.unit_price>0 '.$con.$date_con.$product_group_con.$item_con.' 
	group by BRANCH_NAME';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->BRANCH_ID] = $data2->sale_price;
	$this_year_sale_qty[$data2->BRANCH_ID] = $data2->qty;
	$this_year_sale_pkt[$data2->BRANCH_ID] = $data2->pkt;
	}

// last year
$sql2='select 
		BRANCH_ID,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m, sale_do_chalan a, item_info i , area ar, branch b, zon z  where 
		d.dealer_code=m.dealer_code and a.item_id=i.item_id and m.id=a.order_no  and i.item_brand!="" and  
		ar.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_type="Distributor" and d.area_code=ar.AREA_CODE and 
	a.unit_price>0 '.$con.$ydate_con.$product_group_con.$item_con.' 
	group by  BRANCH_NAME';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->BRANCH_ID] = $data2->sale_price;
	$last_year_sale_qty[$data2->BRANCH_ID] = $data2->qty;
	$last_year_sale_pkt[$data2->BRANCH_ID] = $data2->pkt;
	}
	break;
	
		case 20032:
		$report="Last Year Vs This Year Single Item Zone Wise Sales Report (Periodical)";
		if(isset($t_date)) 
		{
		$to_date=$t_date; $fr_date=$f_date; 
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($product_group)) 			{$product_group_con.=' and d.product_group="'.$product_group.'"';}
		if(isset($depot_id)) 				{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 			{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		if(isset($dealer_type)) 			{$con.=' and d.dealer_type="'.$dealer_type.'"';}
		if(isset($item_id))		 			{$con.=' and a.item_id="'.$item_id.'"';}
		
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and b.BRANCH_ID="'.$region_id.'"';}
		
		$sql='select 
		ZONE_NAME zone_name,BRANCH_NAME region_name,
		ZONE_CODE
		from dealer_info d, area a, branch b, zon z 
		where a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_type="Distributor" and d.area_code=a.AREA_CODE  '.$product_group_con.$acon.' 
	    group by ZONE_CODE';

$sql2='select 
		ZONE_CODE,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m, sale_do_chalan a, item_info i, area ar, branch b, zon z 
		where 
		d.dealer_code=m.dealer_code and a.item_id=i.item_id and m.id=a.order_no  and i.item_brand!="" and  
		ar.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_type="Distributor" and d.area_code=ar.AREA_CODE and 
	a.unit_price>0 '.$con.$date_con.$product_group_con.$item_con.' 
	group by ZONE_CODE';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->ZONE_CODE] = $data2->sale_price;
	$this_year_sale_qty[$data2->ZONE_CODE] = $data2->qty;
	$this_year_sale_pkt[$data2->ZONE_CODE] = $data2->pkt;
	}

$sql2='select 
		ZONE_CODE,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m, sale_do_chalan a, item_info i , area ar, branch b, zon z  where 
		d.dealer_code=m.dealer_code and a.item_id=i.item_id and m.id=a.order_no  and i.item_brand!="" and  
		ar.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_type="Distributor" and d.area_code=ar.AREA_CODE and 
	a.unit_price>0 '.$con.$ydate_con.$product_group_con.$item_con.' 
	group by  ZONE_CODE';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->ZONE_CODE] = $data2->sale_price;
	$last_year_sale_qty[$data2->ZONE_CODE] = $data2->qty;
	$last_year_sale_pkt[$data2->ZONE_CODE] = $data2->pkt;
	}
	break;
    case 1991:

$report="Delivery Order Brief Report with Chalan Amount";
	break;
case 191:
$report="Delivery Order  Report (At A Glance)";
break;
	
    case 2:
		$report="Undelivered Do Details Report";

$sql = "select m.do_no,m.do_date,concat(d.dealer_code,'- ',d.dealer_name_e)  as dealer_name,d.product_group as grp,w.warehouse_name as depot,concat(i.finish_goods_code,'- ',item_name) as item_name,o.pkt_unit as crt,o.dist_unit as pcs,o.total_amt,m.rcv_amt,m.payment_by as PB from 
sale_do_master m,sale_do_details o, item_info i,dealer_info d , warehouse w
where m.do_no=o.do_no and i.item_id=o.item_id and m.dealer_code=d.dealer_code and m.status in ('CHECKED','COMPLETED') and w.warehouse_id=d.depot ".$date_con.$item_con.$depot_con.$dtype_con.$dealer_con.$item_brand_con;
	break;
	case 3:
$report="Undelivered Do Report Dealer Wise";
if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
if(isset($dealer_code)) {$dealer_con=' and m.dealer_code='.$dealer_code;} 
if(isset($item_id)){$item_con=' and i.item_id='.$item_id;} 
if(isset($depot_id)) {$depot_con=' and d.depot="'.$depot_id.'"';} 
	break;
	case 4:
	if($_REQUEST['do_no']>0)
header("Location:work_order_print.php?wo_id=".$_REQUEST['wo_id']);
	break;
	case 5:
if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
 
$report="Delivery Order Brief Report (Region Wise)";
	break;
		case 6:
	if($_REQUEST['do_no']>0)
header("Location:../report/do_view.php?v_no=".$_REQUEST['do_no']);
	break;

case 7:
		$report="Item wise DO Report";
		
		if(isset($t_date)) 	{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		 
		if(isset($depot_id)) {$depot_con=' and d.depot="'.$depot_id.'"';}
		if(isset($area_id))   {$acon.=' and d.area_code="'.$area_id.'"';}
		if(isset($zone_id))   {$acon.=' and d.zone_id="'.$zone_id.'"';}
		if(isset($region_id)) {$acon.=' and d.region_id="'.$region_id.'"';}
		
		if(isset($dealer_type)) {$acon.=' and d.dealer_type="'.$dealer_type.'"';}

$sql = "select 
		i.finish_goods_code as code, 
		i.item_name, i.item_brand, 
		i.sales_item_type as `group`, i.brand_category_type as item_category,
		floor(sum(o.total_unit)/o.pkt_size) as crt,
		mod(sum(o.total_unit),o.pkt_size) as pcs, 
		sum(o.total_amt)as dp_total
		from 
		sale_do_master m,sale_do_details o, item_info i, dealer_info d
		where  m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') 
".$dtype_con.$date_con.$item_con.$item_brand_con.$pg_con.$depot_con.$acon."  
and finish_goods_code not between 2000 and 2005
		group by i.finish_goods_code";
		
break;

case 2015:
		$report="Item wise Secondary Sales Report";
		
		if(isset($t_date)) 	{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		 
		if(isset($depot_id)) {$depot_con=' and d.depot="'.$depot_id.'"';}
		if(isset($area_id))   {$acon.=' and d.area_code="'.$area_id.'"';}
		if(isset($zone_id))   {$acon.=' and d.zone_id="'.$zone_id.'"';}
		if(isset($region_id)) {$acon.=' and d.region_id="'.$region_id.'"';}
		
		if(isset($dealer_type)) {$acon.=' and d.dealer_type="'.$dealer_type.'"';}

$sql = "select 
		i.finish_goods_code as code, 
		i.item_name, i.item_brand, 
		i.sales_item_type as `group`, i.brand_category_type as item_category,
		floor(sum(o.total_unit)/i.pack_size) as crt,
		mod(sum(o.total_unit),i.pack_size) as pcs, 
		sum(o.total_amt)as dp_total
		from 
		ss_do_master m,ss_do_details o, item_info i, dealer_info d
		where  m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') 
".$dtype_con.$date_con.$item_con.$item_brand_con.$pg_con.$depot_con.$acon."  
and finish_goods_code not between 2000 and 2005
		group by i.finish_goods_code";
		
break;

case 15:
		$report="Item wise DO Report(Modern Trade)";
		
		if(isset($t_date)) 	{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		 
		if(isset($depot_id)) {$depot_con=' and d.depot="'.$depot_id.'"';}

$sql = "select 
		i.finish_goods_code as code, 
		i.item_name, i.item_brand, 
		i.sales_item_type as `group`,
		floor(sum(o.total_unit)/o.pkt_size) as crt,
		mod(sum(o.total_unit),o.pkt_size) as pcs, 
		sum(o.total_amt)as amount
from sale_do_master m,sale_do_details o, item_info i, dealer_info d
where 
m.do_no=o.do_no 
and m.dealer_code=d.dealer_code 
and i.item_id=o.item_id
and ( d.dealer_type='Corporate' or d.dealer_type='SuperShop' or d.product_group='M') 
and m.status in ('CHECKED','COMPLETED') ".$dtype_con.$date_con.$item_con.$item_brand_con.$pg_con.$depot_con.$acon.'
group by code';
		
break;
		
		case 701:
		$report="Item wise Undelivered DO Report(With Gift)";
		break;
		
		case 704:
		$report="Storewise Item Undelivered Report(Cash)";
		break;
		
		case 884:
		$report="Storewise Item Undelivered Report(Cash)";
		break;		
		
case 705:
		$report="Storewise Item Undelivered Report(Ctn)";
		break;
		
	
		
		case 8:
if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
 
$report="Dealer Performance Report";
	    
		
case 9:
$report="Item Report (Region + Zone)(Dealer Group)";
if(isset($t_date)) 	{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
break;

case 41:
$report="Item Report (Region + Zone)(Item Group)";
if(isset($t_date)) 	{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
break;

			    case 14:
		$report="Item Report (Region)";
if(isset($t_date)) 	{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
 
		break;
		


case 10:
$report="Daily Collection Summary";

if($dealer_type!=''){
		if($dealer_type=='MordernTrade'){
			$dtype_con=$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';
			}elseif($dealer_type=='Distributor'){
				$dtype_con=$dealer_type_con = ' and d.dealer_type="Distributor" and  d.product_group!="M" ';	
	
			} else {
			$dtype_con=$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';
		}
	}

		
$sql="select m.do_no,m.do_date,concat(d.dealer_code,'- ',d.dealer_name_e)  as party_name,(SELECT AREA_NAME FROM area where AREA_CODE=d.area_code) as area  ,d.product_group as grp, m.bank as bank_name,m.branch as branch_name,m.payment_by as payment_mode, m.rcv_amt as amount,m.remarks,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' as Varification_Sign,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' as DO_Section_sign from 
sale_do_master m, dealer_info d, warehouse w
where m.status in ('CHECKED','COMPLETED') and  m.dealer_code=d.dealer_code and w.warehouse_id=d.depot
".$date_con.$pg_con.$dtype_con."
order by m.entry_at";


break;
		
		
		
		
case 11:
$report="Daily Collection &amp; Order Summary";
		
$sql="select m.do_no, m.do_date, concat(d.dealer_code,'- ',d.dealer_name_e)  as party_name,(SELECT AREA_NAME FROM area where AREA_CODE=d.area_code) as area  ,d.product_group as grp, m.bank as bank_name, m.payment_by as payment_mode,m.remarks, m.rcv_amt as collection_amount,(select sum(total_amt) from sale_do_details where do_no=m.do_no) as DP_Total,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' from 
sale_do_master m,dealer_info d  , warehouse w 
where m.status in ('CHECKED','COMPLETED') and  m.dealer_code=d.dealer_code and w.warehouse_id=d.depot".$date_con.$pg_con." order by m.entry_at";
		break;
				
				
				
case 13:
$report="Daily Collection Summary(EXT)";
		
 $sql="select m.do_no,m.do_date,m.entry_at,concat(d.dealer_code,'- ',d.dealer_name_e)  as party_name,(SELECT AREA_NAME FROM area where AREA_CODE=d.area_code) as area  ,d.product_group as grp, m.bank as bank_name,m.branch as branch_name,m.payment_by as payment_mode, m.rcv_amt as amount,m.remarks,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' as Varification_Sign,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' as DO_Section_sign from 
sale_do_master m,dealer_info d  , warehouse w
where m.status in ('CHECKED','COMPLETED') and  m.dealer_code=d.dealer_code and w.warehouse_id=d.depot
".$date_con.$pg_con.$dtype_con." 
order by m.entry_at";

break;


		case 99:
		if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		 
		$report="Dealer Performance Report";
		break;
		
		case 401:
		if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';} 
		$report="Dealer Performance Report";
		break;		
		
				case 991:
		if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		 
		$report="Dealer Performance Report(ALL)";
		break;
		case 100:
		if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		 
		$report="Dealer Performance Report";
		break;
		
		
		case 101:
		$report="Four(4) Months Comparison Report(CRT)";
		if(isset($t_date)) 	{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		 
		
		$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
		floor(sum(o.total_unit)/o.pkt_size) as crt,
		mod(sum(o.total_unit),o.pkt_size) as pcs, 
		sum(o.total_amt)as dP,
		sum(o.total_unit*o.t_price)as tP
		from 

		sale_do_master m,sale_do_details o, item_info i,dealer_info d
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') ".$date_con.$item_con.$item_brand_con.$pg_con.' group by i.finish_goods_code';
		break;
				case 102:
		$report="Four(4) Months Comparison Report(TK)";
		if(isset($t_date)) 	{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		 
		
		$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
		floor(sum(o.total_unit)/o.pkt_size) as crt,
		mod(sum(o.total_unit),o.pkt_size) as pcs, 
		sum(o.total_amt)as dP,
		sum(o.total_unit*o.t_price)as tP
		from 
		sale_do_master m,sale_do_details o, item_info i,dealer_info d
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') ".$date_con.$item_con.$item_brand_con.$pg_con.' group by i.finish_goods_code';
		break;
						case 103:
		$report="Four(4) Months Regioanl Report(CTR)";
		
		if($_REQUEST['region_id']!='') {$region_id = $_REQUEST['region_id'];$region_name = find_a_field('branch','BRANCH_NAME','BRANCH_ID='.$region_id);}

		$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
		floor(sum(o.total_unit)/o.pkt_size) as crt,
		mod(sum(o.total_unit),o.pkt_size) as pcs, 
		sum(o.total_amt)as dP,
		sum(o.total_unit*o.t_price)as tP
		from 
		sale_do_master m,sale_do_details o, item_info i,dealer_info d
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') ".$date_con.$item_con.$item_brand_con.$pg_con.' group by i.finish_goods_code';
		break;
						case 104:
		$report="Four(4) Months Regional Report(TK)";

		if($_REQUEST['region_id']!='') {$region_id = $_REQUEST['region_id'];$region_name = find_a_field('branch','BRANCH_NAME','BRANCH_ID='.$region_id);}
		
		$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
		floor(sum(o.total_unit)/o.pkt_size) as crt,
		mod(sum(o.total_unit),o.pkt_size) as pcs, 
		sum(o.total_amt)as dP,
		sum(o.total_unit*o.t_price)as tP
		from 
		sale_do_master m,sale_do_details o, item_info i,dealer_info d
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') ".$date_con.$item_con.$item_brand_con.$pg_con.' group by i.finish_goods_code';
		break;				case 105:
		$report="Four(4) Months Zonal Report(CTR)";

		if($_REQUEST['zone_id']!='') {$zone_id = $_REQUEST['zone_id'];$zone_name = find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_id);}

		$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
		floor(sum(o.total_unit)/o.pkt_size) as crt,
		mod(sum(o.total_unit),o.pkt_size) as pcs, 
		sum(o.total_amt)as dP,
		sum(o.total_unit*o.t_price)as tP
		from 
		sale_do_master m,sale_do_details o, item_info i,dealer_info d
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') ".$date_con.$item_con.$item_brand_con.$pg_con.' group by i.finish_goods_code';
		break;
						case 106:
		$report="Four(4) Months Area Report(TK)";

		
		if($_REQUEST['zone_id']!='') {$zone_id = $_REQUEST['zone_id'];$zone_name = find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_id);}
				
		$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
		floor(sum(o.total_unit)/o.pkt_size) as crt,
		mod(sum(o.total_unit),o.pkt_size) as pcs, 
		sum(o.total_amt)as dP,
		sum(o.total_unit*o.t_price)as tP
		from 
		sale_do_master m,sale_do_details o, item_info i,dealer_info d
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') ".$date_con.$item_con.$item_brand_con.$pg_con.' group by i.finish_goods_code';
		break;
		
								case 107:
		$report="Yearly Regional Sales Report(TK)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
	$m = $t_array[1]-($i-1);
	$t_stampq = strtotime(date('Y-m-15',strtotime($t_date)))-(60*60*24*30*($i-1));
	${'f_mos'.$i} = date('Y-m-15',$t_stampq);
	${'f_mons'.$i} = date('Y-m-01',$t_stampq);
	${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
$f_date=${'f_mons'.$i};

		break;
case 108:
$report="Yearly Regional Sales Report(Per Item)(CTN)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
		break;
case 109:
$report="Yearly Regional Sales Report(Per Item)(TK)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;									



case 110:
$report="Yearly Zone Wise Sales Report(Per Item)(Tk)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;
case 111:
$report="Yearly Zone Wise Sales Report(Per Item)(CTN)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;
case 112:
$report="Yearly Zone Wise Sales Report(Per Item)(Tk)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;
case 1130:
$report="Corporate Party Wise Sales Report YEARLY";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);
$dealer_type = 'Corporate';
unset($to_date);
for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;
case 11301:
$report="SuperShop Party Wise Sales Report YEARLY";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);
$dealer_type = 'SuperShop';
unset($to_date);
for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;

case 113:
$report="Yearly Dealer Wise Sales Report(Per Item)(Tk)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;

case 114:
$report="Yearly Dealer Wise Sales Report(Per Item)(CTN)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;

case 115:
$report="Yearly Dealer Wise Sales Report(Per Item)(Tk)";
if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

for($i=12;$i>0;$i--)
{
$m = $t_array[1]-($i-1);
$t_stampq = strtotime(date('Y-m-01',strtotime($t_date)))-(60*60*24*30*($i-1));

${'f_mons'.$i} = date('Y-m-01',$t_stampq);
${'f_mone'.$i} = date('Y-m-'.date('t',$t_stampq),$t_stampq);
}
break;
case 116:
$report="Single Item Sales Report(Zone Wise)";
break;


case 1992:
$report="Sales Statement(As Per DO)";
break;


case 1993:
$report="Party Collection Group Wise";
break;

case 2000:
$report="DO Summery Report Region Wise";
break;

case 2010:
  $report="Secondary Sales Summery Report Region Wise";
  break;
case 2011:
  $report="Secondary Sales Summery Report Region Wise";
  break;
case 2012:
$report="Secondary Sales Summery Report Dealer Wise";
break;  
case 2013:
$report="Secondary Sales Report DO Wise";
break; 

case 2014:
$report="Order Wise TSM,RSM Report";
break; 
case 2016:
$report="TSM,RSM Wise Secondary Sales Report";
break; 

}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=$report?></title>
<link href="../../../assets/css/report.css" type="text/css" rel="stylesheet" />
<script language="javascript">
function hide()
{
document.getElementById('pr').style.display='none';
}
</script>
    <style type="text/css" media="print">
      div.page
      {
        page-break-after: always;
        page-break-inside: avoid;
      }
    </style>
    <style type="text/css">
<!--
.style3 {color: #FFFFFF; font-weight: bold; }
.style5 {color: #FFFFFF}
-->
    </style>
</head>
<body>
<div align="center" id="pr">
<input type="button" value="Print" onclick="hide();window.print();"/>
</div>
<div class="main">
<?
		$str 	.= '<div class="header">';
		if(isset($_SESSION['company_name'])) 
		$str 	.= '<h1>'.$_SESSION['company_name'].'</h1>';
		if(isset($report)) 
		$str 	.= '<h2>'.$report.'</h2>';
		if(isset($dealer_code)) 
		$str 	.= '<h2>Dealer Name : '.$dealer_code.' - '.find_a_field('dealer_info','dealer_name_e','dealer_code='.$dealer_code).'</h2>';
		if(isset($depot_id)) 
		$str 	.= '<h2>Depot Name : '.find_a_field('warehouse','warehouse_name','warehouse_id='.$depot_id).'</h2>';
		if(isset($item_brand)) 
		$str 	.= '<h2>Item Brand : '.$item_brand.'</h2>';
		if(isset($item_info->item_id)) 
		$str 	.= '<h2>Item Name : '.$item_info->item_name.'('.$item_info->finish_goods_code.')'.'('.$item_info->sales_item_type.')'.'('.$item_info->item_brand.')'.'</h2>';
		if(isset($to_date)) 
		$str 	.= '<h2>Date Interval : '.$fr_date.' To '.$to_date.'</h2>';
		if(isset($product_group)) 
		$str 	.= '<h2>Product Group : '.$product_group.'</h2>';
		if(isset($region_id)) 
		$str 	.= '<h2>Region Name : '.find_a_field('branch','BRANCH_NAME','BRANCH_ID='.$region_id).'</h2>';
		if(isset($zone_id)) 
		$str 	.= '<h2>Zone Name: '.find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_id).'</h2>';
		if(isset($area_id)) 
		$str 	.= '<h3>Area Name: '.find_a_field('area','AREA_NAME','AREA_CODE='.$area_id).'</h3>';		
		if(isset($dealer_type)) 
		$str 	.= '<h2>Dealer Type : '.$dealer_type.'</h2>';
		$str 	.= '</div>';
		$str 	.= '<div class="left" style="width:100%">';



if($_REQUEST['report']==1) { // do summery report modify jan 24 2018



if($_REQUEST['dealer_code']!=''){
$dealer_con5=' and c.dealer_code="'.$_REQUEST['dealer_code'].'"';
$sub_ledger_id = find_a_field('dealer_info','sub_ledger_id','dealer_code="'.$_REQUEST['dealer_code'].'"');
}

if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con3=' and c.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

  $sql="select  c.chalan_no,c.chalan_date, sum(c.total_amt) as total_amt ,concat(d.dealer_code,'- ',d.dealer_name_e) as dealer_name,d.dealer_code,d.sub_ledger_id, m.rcv_amt
from sale_do_master m,  sale_do_chalan c, dealer_info d  
where  m.dealer_code=d.dealer_code and  m.dealer_type in ('Corporate')  ".$date_con3.$dealer_con5." 
group by c.chalan_no order by c.chalan_date,c.chalan_no";

$query = mysql_query($sql);
echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="13">'.$str.'</td></tr>
<tr>
<th>S/L</th>
<th>Date</th>
<th>Challan/ Receipt Voucher</th>
<th>Ref No</th>
<th>Debit Amount</th>
<th>Credit Amount</th>
<th> Balance  </th>
</tr>
</thead>
<tbody>';
while($data=mysql_fetch_object($query)){$s++;
$sub_ledger = $data->sub_ledger_id;
$sqld = 'select sum(total_amt),sum(t_price*total_unit) from sale_do_details where do_no='.$data->do_no;
$info = mysql_fetch_row(mysql_query($sqld));

$tot_amt = $tot_amt+$data->total_amttfdg;
$dp_t = $dp_t+$info[0];
$tp_t = $tp_t+$info[1];
$challan_amt=find_a_field('sale_do_chalan','sum(total_amt)','chalan_no="'.$data->chalan_no.'"');
$rcv_t = $rcv_t+$challan_amt;
?>
<tr>
<td><?=$s?></td>
<td><?=$data->chalan_date?></td>
<td><?=$data->chalan_no?></td>
<td>Delivery Challan</td>
<td><?=$challan_amt?></td>
<td></td>
<td></td>
</tr>
<?
}

?>
<?   $sql = 'select * from journal where jv_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'" and sub_ledger = "'.$sub_ledger_id.'" and tr_from in ("receipt","Commission","Goods Return","Journal","Damage Return") and cr_amt>0 ';
$query = mysql_query($sql);
while($j_data = mysql_fetch_object($query)){$s++;

 $rcv_t2 = $rcv_t2+$j_data->cr_amt;


 ?>
<tr>
<td><?=$s?></td>
<td><?=$j_data->jv_date?></td>
<td><?=$j_data->jv_no?></td>
<td><?=$j_data->narration?></td>
<td></td>
<td style="text-align:right"><?=$j_data->cr_amt?></td>
<td><?=number_format($diff_amt,2)?></td>
</tr>
<? } 
$diff_amt = $rcv_t2-$rcv_t; 
 ?>

 
<?
echo '<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Total</td><td style="text-align:right">'.number_format($rcv_t,2).'</td><td style="text-align:right">'.number_format($rcv_t2,2).'</td><td>&nbsp;</td></tr>

<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Difference :</td><td></td><td style="text-align:right">'.number_format($diff_amt,2).'</td></tr>
</tbody></table>';
}






elseif($_REQUEST['report']==1001){ // do summery report modify jan 24 2018



if($_REQUEST['dealer_code']!=''){
$dealer_con6=' and c.dealer_code="'.$_REQUEST['dealer_code'].'"';
$sub_ledger_id = find_a_field('dealer_info','sub_ledger_id','dealer_code="'.$_REQUEST['dealer_code'].'"');
}

if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con6=' and c.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

   $sql="select c.chalan_no,c.chalan_date, sum(c.total_amt) as total_amt ,concat(d.dealer_code,'- ',d.dealer_name_e) as dealer_name,d.dealer_code,d.sub_ledger_id, m.rcv_amt
from sale_do_master m, sale_do_chalan c, dealer_info d  
where  m.dealer_code=d.dealer_code and m.dealer_type in ('Distributor')  ".$date_con6.$dealer_con6." 
group by c.chalan_no order by c.chalan_date,c.chalan_no";

$query = mysql_query($sql);
echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="13">'.$str.'</td></tr>
<tr>
<th>S/L</th>
<th>Date</th>
<th>Challan/ Receipt Voucher</th>
<th>Ref No</th>
<th>Debit Amount</th>
<th>Credit Amount</th>
<th> Balance  </th>
</tr>
</thead>
<tbody>';
while($data=mysql_fetch_object($query)){$s++;
$sub_ledger = $data->sub_ledger_id;
$sqld = 'select sum(total_amt),sum(t_price*total_unit) from sale_do_details where do_no='.$data->do_no;
$info = mysql_fetch_row(mysql_query($sqld));

$tot_amt = $tot_amt+$data->total_amtt;
$dp_t = $dp_t+$info[0];
$tp_t = $tp_t+$info[1];
$challan_amt=find_a_field('sale_do_chalan','sum(total_amt)','chalan_no="'.$data->chalan_no.'"');
$rcv_t = $rcv_t+$challan_amt;
?>
<tr>
<td><?=$s?></td>
<td><?=$data->chalan_date?></td>
<td><?=$data->chalan_no?></td>
<td>Delivery Challan</td>
<td><?=$challan_amt?></td>
<td></td>
<td></td>
</tr>
<?
}

?>
<?  $sql = 'select * from journal where jv_date between "'.$_REQUEST['f_date'].'" and "'.$_REQUEST['t_date'].'" and sub_ledger = "'.$sub_ledger_id.'"';
$query = mysql_query($sql);
while($j_data = mysql_fetch_object($query)){$s++;

 $rcv_t2 = $rcv_t2+$j_data->cr_amt;
 
 $rcv_t +=$j_data->dr_amt;
 ?>
<tr>
<td><?=$s?></td>
<td><?=$j_data->jv_date?></td>
<td><?=$j_data->jv_no?></td>
<td><?=$j_data->narration?></td>
<td style="text-align:right"><? if($j_data->dr_amt>0) echo number_format($j_data->dr_amt,2); ?></td>
<td style="text-align:right"><? if($j_data->cr_amt>0) echo number_format($j_data->cr_amt,2); ?></td>
<td><?=number_format($diff_amt,2)?></td>
</tr>
<? } $diff_amt = $rcv_t2-$rcv_t;   ?>


<?
echo '<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Total</td><td style="text-align:right">'.number_format($rcv_t,2).'</td><td style="text-align:right">'.number_format($rcv_t2,2).'</td><td>&nbsp;</td></tr>

<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Difference :</td><td></td><td style="text-align:right">'.number_format($diff_amt,2).'</td></tr>
</tbody></table>';
}

elseif($_REQUEST['report']==1005) { // do summery report modify jan 24 2018



if($_REQUEST['dealer_code']!=''){
$dealer_con6=' and c.dealer_code="'.$_REQUEST['dealer_code'].'"';
$sub_ledger_id = find_a_field('dealer_info','sub_ledger_id','dealer_code="'.$_REQUEST['dealer_code'].'"');
}

if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con6=' and c.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

   $sql="select  c.chalan_no,c.chalan_date, sum(c.total_amt) as total_amt ,concat(d.dealer_code,'- ',d.dealer_name_e) as dealer_name,d.dealer_code,d.sub_ledger_id, m.rcv_amt
from sale_do_master m, sale_do_chalan c, dealer_info d  
where  m.dealer_code=d.dealer_code and m.do_no=c.do_no  ".$date_con6.$dealer_con6." 
group by c.chalan_no order by c.chalan_date,c.chalan_no";

$query = mysql_query($sql);
echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="13">'.$str.'</td></tr>
<tr>
<th>S/L</th>
<th>Date</th>
<th>Challan/ Receipt Voucher</th>
<th>Ref No</th>
<th>Debit Amount</th>
<th>Credit Amount</th>
<th> Balance  </th>
</tr>
</thead>
<tbody>';
while($data=mysql_fetch_object($query)){$s++;
$sub_ledger = $data->sub_ledger_id;
$sqld = 'select sum(total_amt),sum(t_price*total_unit) from sale_do_details where do_no='.$data->do_no;
$info = mysql_fetch_row(mysql_query($sqld));

$tot_amt = $tot_amt+$data->total_amtt;
$dp_t = $dp_t+$info[0];
$tp_t = $tp_t+$info[1];
$challan_amt=find_a_field('sale_do_chalan','sum(total_amt)','chalan_no="'.$data->chalan_no.'"');
$rcv_t = $rcv_t+$data->total_amt;
?>
<tr>
<td><?=$s?></td>
<td><?=$data->chalan_date?></td>
<td><?=$data->chalan_no?></td>
<td>Delivery Challan</td>
<td><?=$challan_amt?></td>
<td></td>
<td></td>
</tr>
<?
}

?>
<?   $sql = 'select * from journal where jv_date between "'.$_REQUEST['f_date'].'" and "'.$_REQUEST['t_date'].'" and sub_ledger = "'.$sub_ledger_id.'" and tr_from in ("Receipt","Commission","Goods Return") and cr_amt>0 ';
$query = mysql_query($sql);
while($j_data = mysql_fetch_object($query)){$s++;

 $rcv_t2 = $rcv_t2+$j_data->cr_amt;
 

 ?>
<tr>
<td><?=$s?></td>
<td><?=$j_data->jv_date?></td>
<td><?=$j_data->jv_no?></td>
<td><?=$j_data->narration?></td>
<td></td>
<td style="text-align:right"><?=$j_data->cr_amt?></td>
<td><?=number_format($diff_amt,2)?></td>
</tr>
<? }  $diff_amt = $rcv_t2-$rcv_t; ?>


<?
echo '<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Total</td><td style="text-align:right">'.number_format($rcv_t,2).'</td><td style="text-align:right">'.number_format($rcv_t2,2).'</td><td>&nbsp;</td></tr>

<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Difference :</td><td></td><td style="text-align:right">'.number_format($diff_amt,2).'</td></tr>
</tbody></table>';
}



elseif($_REQUEST['report']==100555) { // do summery report modify jan 24 2018



if($_REQUEST['dealer_code']!=''){
$dealer_con=' and c.dealer_code="'.$_REQUEST['dealer_code'].'"';
$sub_ledger_id = find_a_field('dealer_info','sub_ledger_id','dealer_code="'.$_REQUEST['dealer_code'].'"');

$sub_con=' and sub_ledger = "'.$sub_ledger_id.'"';
}

if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con=' and c.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

   $sql="select  c.chalan_no,c.chalan_date, sum(c.total_amt) as total_amt ,concat(d.dealer_code,'- ',d.dealer_name_e) as dealer_name,d.dealer_code,d.sub_ledger_id, m.rcv_amt
from sale_do_master m, sale_do_chalan c, dealer_info d  
where  m.dealer_code=d.dealer_code and m.do_no=c.do_no  ".$date_con.$dealer_con." 
group by c.chalan_no order by c.chalan_date,c.chalan_no";

$query = mysql_query($sql);
echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="13">'.$str.'</td></tr>
<tr>
<th>S/L</th>
<th>Dealer Name</th>
<th>Receipt Date</th>
<th>Receipt no</th>
<th>Ref No</th>
<th>Receipt Amount</th>
</tr>
</thead>
<tbody>';
while($data=mysql_fetch_object($query)){$s++;
$sub_ledger = $data->sub_ledger_id;
$sqld = 'select sum(total_amt),sum(t_price*total_unit) from sale_do_details where do_no='.$data->do_no;
$info = mysql_fetch_row(mysql_query($sqld));

$tot_amt = $tot_amt+$data->total_amtt;
$dp_t = $dp_t+$info[0];
$tp_t = $tp_t+$info[1];
$challan_amt=find_a_field('sale_do_chalan','sum(total_amt)','chalan_no="'.$data->chalan_no.'"');
$rcv_t = $rcv_t+$data->total_amt;
?>
<?php /*?><tr>
<td><?=$s?></td>
<td><?=$data->chalan_date?></td>
<td><?=$data->chalan_no?></td>
<td>Delivery Challan</td>
<td><?=$challan_amt?></td>
</tr><?php */?>
<?
}

?>
<?    $sql = 'select * from journal where jv_date between "'.$_REQUEST['f_date'].'" and "'.$_REQUEST['t_date'].'"  and tr_from in ("Receipt")  '.$sub_con.' ';
$query = mysql_query($sql);
while($j_data = mysql_fetch_object($query)){$s++;

 $rcv_t2 = $rcv_t2+$j_data->cr_amt;
 

 ?>
<tr>
<td><?=$s?></td>
<td><?=find_a_field('dealer_info','dealer_name_e','sub_ledger_id="'.$j_data->sub_ledger.'"');?></td>
<td><?=$j_data->jv_date?></td>
<td><?=$j_data->jv_no?></td>
<td><?=$j_data->narration?></td>
<td style="text-align:right"><?=$j_data->cr_amt?></td>
</tr>
<? }  $diff_amt = $rcv_t2-$rcv_t; ?>


<?
echo '<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Total</td><td style="text-align:right">'.number_format($rcv_t2,2).'</td></tr>


</tbody></table>';
}



elseif($_REQUEST['report']==100556) 
{

 $sdate = strtotime($_REQUEST['f_date']);
 $edate = strtotime($_REQUEST['t_date']);

 $s_date =date("Y-m-01",$sdate);
 $m_date = $new_date = date("Y-m-15",$sdate);
 $e_date =date("Y-m-t",$edate);

$start_date = strtotime(date("Y-m-01 00:00:00",strtotime($s_date)));
$end_date = strtotime(date("Y-m-t 23:59:59",strtotime($e_date)));
for($c=1;$c<40;$c++)
{

	if($new_date>$e_date)  {$c=$c-1;break;}
	else
	{
	$st_date[$c] = date("Y-m-01",(strtotime($new_date)));
	$en_date[$c] = date("Y-m-t",(strtotime($new_date)));
  $priod[$c] = date("Ym",(strtotime($new_date)));
	$period_name[$c] = date("M, Y",(strtotime($new_date)));
	}
$new_date = date("Y-m-d",(strtotime($new_date)+2592000));
} 



?>




<style>
body{
	margin:0px !important;
}


/* Apply styles for printing */
@media print {
@page {
/*  size: A4;  */            /* Set page size to A4 */
  margin: 5mm;          /* Define margins (top, right, bottom, left) */
}
}
</style>

<table width="100%" border="0" id="ExportTable" cellpadding="2" cellspacing="0">


<thead>

    <tr>
<td style="border:0px;" colspan="24">
<div class="header">
<h1><?=find_a_field('user_group','group_name','id="'.$_SESSION['user']['group'].'"');?></h1>
<h2><?=$report?></h2>
<h2><?=$warehouse_name?></h2>
<h2><?='<h2>For the Period of:- '.$f_date.' To '.$t_date.'</h2>';?></h2>

</div>
<div class="left"></div>
<div class="right"></div>
<div class="date" style="text-align:right">Reporting Time: <?=date("h:i A d-m-Y")?></div>
</td>
</tr>


<tbody>


<tr>


<th rowspan="2" style="text-align:center">S/L</th>
<th rowspan="2" ><div align="center">Dealer Type</div></th>
<th rowspan="2"><div align="center">Dealer Name</div></th>
<th rowspan="2"><div align="center">Dealer Code</div></th>
<th  colspan="<?=$c;?>"><div align="center">Receive Amount</div></th>


</tr>

<tr>
<? for($p=1;$p<=$c;$p++){?><td><?=$period_name[$p]?></td><? } ?>
</tr>
<? 


if($_POST['f_date']!=''&&$_POST['t_date']!='')


$con .= 'and a.ji_date between "'.$_POST['f_date'].'" and "'.$_POST['t_date'].'"';


if($_POST['dealer_code']!=''){$dealer_con = ' and i.dealer_code='.$_POST['dealer_code'];};


if($_POST['dealer_type']!=''){$dealer_type_con = ' and t.id='.$_POST['dealer_type'];};

if(isset($item_group)) 			    	{$item_group_con=' and s.group_id='.$item_group;}

if($_POST['sub_group_id']!=''){$item_sub_con = ' and s.sub_group_id='.$_POST['sub_group_id'];};


$sl=0;


echo  $arr_sql="SELECT concat(date_format(jv_date,'%Y%m')) month ,sum(cr_amt) as rate ,sub_ledger FROM `journal` WHERE   `tr_from` IN ('Receipt') group by concat(date_format(jv_date,'%Y%m')),sub_ledger";


$arr_query = mysql_query($arr_sql);

while($arr_data=mysql_fetch_object($arr_query)){

	$rate[$arr_data->sub_ledger][$arr_data->month]=$arr_data->rate;
}





  $res='select i.*, t.id, t.dealer_type


from dealer_info i, dealer_type t


where i.dealer_type=t.id '.$dealer_type_con.$dealer_con.$item_sub_con.$item_group_con.'  order by i.dealer_code asc';


$query = mysql_query($res);


while($data=mysql_fetch_object($query)){


?>


<tr>


<td style="text-align:center;"><?=++$sl;?></td>
<td style="text-align:center"><?=$data->dealer_type?></td>
<td><?=$data->dealer_name_e?></td>
<td style="text-align:center;"><?=$data->customer_code?></td>

<? for($p=1;$p<=$c;$p++){?>
<td style="text-align:center;">


<?=number_format($rate[$data->sub_ledger_id][$priod[$p]],2)?></td>


<? } ?>


</tr>


<? }?>

</tbody>
</table>

<br><br><br><br><br><br>
<?

}



elseif($_REQUEST['report']==1100) { // do summery report modify jan 24 2018

 $sql="select m.do_no,i.item_name,m.do_date,d.dealer_name_e as dealer_name,
w.warehouse_name as depot,s.unit_price,s.total_unit,s.total_amt,m.status


from sale_return_master m,sale_return_details s,dealer_info d  , warehouse w, item_info i

where m.do_no=s.do_no and m.status in ('CHECKED','COMPLETED')  and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.item_id=s.item_id
".$depot_con.$date_con.$item_con.$dealer_con.$dtype_con." order by m.do_date,m.do_no";


$query = mysql_query($sql); ?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">

<thead><tr><td style="border:0px;" colspan="11"><?=$str?></td></tr>

<tr>
<th>S/L</th>
<th>SR No</th>
<th>Item Name</th>
<th>SR Date</th>
<th>Dealer Name</th>
<th>Depot</th>
<th>Unit Price</th>
<th>Total Unit</th>
<th>Total Amount</th>
<th>Status</th>
<!--<th>Payment Details</th>
<th>Total Amt</th>-->
</tr>
</thead><tbody>

<?
while($data=mysql_fetch_object($query)){$s++;

?>
<tr>
<td><?=$s?></td>
<td><?=$data->do_no?></td>
<td><?=$data->item_name?></td>
<td><?=$data->do_date?></td>
<td><?=$data->dealer_name?></td>
<td><?=$data->depot?></td>
<td><?=$data->unit_price?></td>
<td><?=$data->total_unit?></td>
<td style="text-align:right"><?=$data->total_amt; $total=$total+$data->total_amt?></td>
<td style="text-align:right"><?=$data->status?></td>

</tr>
<? } ?>
<tr class="footer">
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td style="text-align:right">&nbsp;</td>
<td style="text-align:right">&nbsp;</td>
<td style="text-align:right;">Total Amount</td>
<td style="text-align:right;"><?=number_format($total,2)?></td>
</tr>
</tbody>
</table>
<? }







elseif($_REQUEST['report']==301) 
{
if($_REQUEST['warehouse_id']!=''){
$depCon=' and w.warehouse_id="'.$_REQUEST['warehouse_id'].'"';
}
$sql="select m.do_no,m.do_date,m.entry_time,concat(d.dealer_code,'- ',d.dealer_name_e) as dealer_name,(select AREA_NAME from area where AREA_CODE=d.area_code) as area,w.warehouse_name as depot, m.rcv_amt,concat(m.payment_by,':',m.bank,':',m.remarks) as Payment_Details 
from sale_do_master m,dealer_info d  , warehouse w
where m.status in ('PROCESSING')  
and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot".$depCon.$date_con.$pg_con.$dealer_con.$dtype_con." 
order by m.do_date,m.do_no";

$query = mysql_query($sql);
echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="13">'.$str.'</td></tr><tr><th>S/L</th><th>Do No</th><th>Do Date</th><th>Confirm At</th><th>Entry At</th><th>Dealer Name</th><th>Area</th><th>Depot</th><th>Grp</th><th>Rcv Amt</th><th>Payment Details</th><th>DP Total</th></tr></thead>
<tbody>';
while($data=mysql_fetch_object($query)){$s++;
$sqld = 'select sum(CASE WHEN total_amt>0 THEN total_amt ELSE 0 END),sum(t_price*total_unit) from sale_do_details where do_no='.$data->do_no;
$info = mysql_fetch_row(mysql_query($sqld));
$rcv_t = $rcv_t+$data->rcv_amt;
$dp_t = $dp_t+$info[0];
$tp_t = $tp_t+$info[1];
?>
<tr><td><?=$s?></td><td><?=$data->do_no?></td><td><?=$data->do_date?></td><td><?=$data->entry_at?></td><td><?=$data->entry_time?></td><td><?=$data->dealer_name?></td><td><?=$data->area?></td><td><?=$data->depot?></td><td><?=$data->grp?></td><td style="text-align:right"><?=$data->rcv_amt?></td><td><?=$data->Payment_Details?></td><td><?=$info[0]?></td></tr>
<?
}
echo '<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right">'.number_format($rcv_t,2).'</td><td>&nbsp;</td><td>'.number_format($dp_t,2).'</td></tr></tbody></table>';
}
elseif($_REQUEST['report']==1999) 
{
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
$sql="select concat(d.dealer_code,'- ',d.dealer_name_e) as dealer_name,AREA_NAME as area,ZONE_NAME zone,w.warehouse_name as depot, sum(i.t_price*sd.total_unit) do_amt from 
sale_do_master m,dealer_info d  , warehouse w,area a,sale_do_details sd,item_info i, zon z
where a.ZONE_ID=z.ZONE_CODE and m.status in ('CHECKED','COMPLETED') and a.AREA_CODE=d.area_code and m.do_no=sd.do_no and sd.item_id=i.item_id and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.finish_goods_code in (102 ,105 ,106 ,109 ,120 ,121 ,123 ,124 ,126 ,127 ,128 ,129 ,130 ,137 ,138 ,139 ,140 ,141 ,142 ,143)".$depot_con.$date_con.$pg_con.$dealer_con.$dtype_con.$acon." group by d.dealer_code order by d.dealer_name_e";
$query = mysql_query($sql);
echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="9">'.$str.'</td></tr><tr><th>S/L</th><th>Dealer Name</th><th>Zone</th><th>Area</th><th>Depot</th><th>TP Amt</th><th>80%-TP</th><th>SC QTY</th></tr></thead>
<tbody>';
while($data=mysql_fetch_object($query)){$s++;
//$sqld = 'select sum(total_amt),sum(t_price*total_unit) from sale_do_details where do_no='.$data->do_no;
//$info = mysql_fetch_row(mysql_query($sqld));
$do_tot = $do_tot+$data->do_amt;
$do_80  = (int)($data->do_amt*.8);
$do_80t = $do_80t+$do_80;
$do_sc  = (int)($do_80*.001);
$do_sct = $do_sct+$do_sc;
?>
<tr><td><?=$s?></td><td><?=$data->dealer_name?></td><td><?=$data->zone?></td><td><?=$data->area?></td><td><?=$data->depot?></td><td style="text-align:right"><?=$data->do_amt?></td><td style="text-align:right"><?=$do_80?></td><td style="text-align:right"><?=$do_sc?></td></tr>
<?
}
echo '<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right">'.number_format($do_tot,2).'</td><td style="text-align:right">'.number_format($do_80t,2).'</td><td style="text-align:right">'.number_format($do_sct,2).'</td></tr></tbody></table>';
}
elseif($_REQUEST['report']==1991) 
{

if($_REQUEST['warehouse_id']!=''){
$depCon=' and w.warehouse_id="'.$_REQUEST['warehouse_id'].'"';
}
 	
if((strlen($_REQUEST['cut_date'])==10)) $cut_date=$_REQUEST['cut_date'];
if(isset($cut_date)) 					{$cut_date_con=' and c.chalan_date <="'.$cut_date.'"';}

$sqld = 'select m.do_no, sum(c.total_amt) as ch_amt from sale_do_chalan c, sale_do_master m where c.unit_price>0 and m.do_no=c.do_no '.$date_con.$cut_date_con.' group by m.do_no';
$queryd = mysql_query($sqld);
while($info = mysql_fetch_object($queryd)){
$do_ch_amt[$info->do_no] = $info->ch_amt;
}

$sql="select m.do_no,m.do_date,concat(d.dealer_code,'- ',d.dealer_name_e) as dealer_name,w.warehouse_name as depot,d.product_group as grp, sum(ds.total_amt) as do_amt,m.rcv_amt,concat(m.payment_by,' ',m.bank,' ',m.remarks) as Payment_Details from 
sale_do_master m,dealer_info d  , warehouse w,sale_do_details ds
where m.do_no=ds.do_no and m.status in ('CHECKED','COMPLETED')  and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and ds.total_amt>0 ".$depCon.$date_con.$pg_con.$dealer_con.$dtype_con." group by m.do_no order by m.do_date,m.do_no";
$query = mysql_query($sql);
?><table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="12"><?=$str?></td></tr><tr><th>S/L</th><th>Do No</th><th>Do Date</th><th>Dealer Name</th><th>Area</th><th>Depot</th><th>Grp</th><th>Rcv Amt</th><th>Payment Details</th><th>DO Amt</th><th>Sale Amt</th><th>Due Amt</th></tr></thead>
<tbody><?
while($data=mysql_fetch_object($query)){$s++;
$due_amt = ($data->do_amt-$do_ch_amt[$data->do_no]);
$rcv_t = $rcv_t+$data->rcv_amt;
$dp_t = $dp_t+$data->do_amt;
$tp_t = $tp_t+$do_ch_amt[$data->do_no];
$due_t = $due_t+$due_amt;
?>
<tr><td><?=$s?></td><td><?=$data->do_no?></td><td><?=$data->do_date?></td><td><?=$data->dealer_name?></td><td><?=$data->area?></td><td><?=$data->depot?></td><td><?=$data->grp?></td><td style="text-align:right"><?=$data->rcv_amt?></td><td><?=$data->Payment_Details?></td><td style="text-align:right"><?=number_format($data->do_amt,2);?></td><td><?=number_format($do_ch_amt[$data->do_no],2)?></td><td><?=number_format($due_amt,2);?></td></tr>
<?
}
echo '<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right">'.number_format($rcv_t,2).'</td><td>&nbsp;</td><td>'.number_format($dp_t,2).'</td><td>'.number_format($tp_t,2).'</td><td>'.number_format($due_t,2).'</td></tr></tbody></table>';
}
elseif($_REQUEST['report']==191) 
{

if($_REQUEST['warehouse_id']!=''){
$depCon=' and w.warehouse_id="'.$_REQUEST['warehouse_id'].'"';
}
$sql="select m.do_no,m.do_date,concat(d.dealer_code,'- ',d.dealer_name_e) as dealer_name,(select AREA_NAME from area where AREA_CODE=d.area_code) as area,w.warehouse_name as depot,d.product_group as grp, m.rcv_amt,concat(m.payment_by,':',m.bank,':',m.remarks) as Payment_Details,d.dealer_code,sum(dl.total_amt) as dp_t,sum(dl.t_price*dl.total_unit) as tp_t from 
sale_do_master m,dealer_info d  , warehouse w, sale_do_details dl
where dl.do_no=m.do_no and m.status in ('CHECKED','COMPLETED')  and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot".$depCon.$date_con.$pg_con.$dealer_con.$dtype_con." group by d.dealer_code order by d.dealer_name_e";
$query = mysql_query($sql);
echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="9">'.$str.'</td></tr><tr><th>S/L</th><th>Dealer Name</th><th>Area</th><th>Depot</th><th>Grp</th><th>Rcv Amt</th><th>Total DO Price</th></tr></thead>
<tbody>';
while($data=mysql_fetch_object($query)){$s++;

$rcv_amt = $rcv_amt+$data->rcv_amt;
$dp_t = $dp_t+$data->dp_t;
?>
<tr><td><?=$s?></td><td><?=$data->dealer_name?></td><td><?=$data->area?></td><td><?=$data->depot?></td><td><?=$data->grp?></td><td style="text-align:right"><?=$data->rcv_amt?></td><td><?=number_format($data->dp_t,2)?></td></tr>
<?
}
echo '<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:right">'.number_format($rcv_amt,2).'</td><td>'.number_format($dp_t,2).'</td></tr></tbody></table>';
}
elseif($_REQUEST['report']==3) 
{
$sql2 	= "select distinct o.do_no, d.dealer_code,d.dealer_name_e,w.warehouse_name,m.do_date,d.address_e,d.mobile_no,d.product_group from 
sale_do_master m,sale_do_details o, item_info i,dealer_info d , warehouse w
where m.do_no=o.do_no and i.item_id=o.item_id and m.dealer_code=d.dealer_code and m.status in ('CHECKED','COMPLETED') and w.warehouse_id=d.depot ".$date_con.$item_con.$depot_con.$dtype_con.$pg_con.$dealer_con;
$query2 = mysql_query($sql2);

while($data=mysql_fetch_object($query2)){
echo '<div style="position:relative;display:block; width:100%; page-break-after:always; page-break-inside:avoid">';
	$dealer_code = $data->dealer_code;
	$dealer_name = $data->dealer_name_e;
	$warehouse_name = $data->warehouse_name;
	$do_date = $data->do_date;
	$do_no = $data->do_no;
		if($dealer_code>0) 
{
$str 	.= '<p style="width:100%">Dealer Name: '.$dealer_name.' - '.$dealer_code.'('.$data->product_group.')</p>';
$str 	.= '<p style="width:100%">DO NO: '.$do_no.' 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Depot:'.$warehouse_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date:'.$do_date.'</p>
<p style="width:100%">Destination:'.$data->address_e.'('.$data->mobile_no.')</p>';

$dealer_con = ' and m.dealer_code='.$dealer_code;
$do_con = ' and m.do_no='.$do_no;
$sql = "select concat(i.finish_goods_code,'- ',item_name) as item_name,o.pkt_unit as crt,o.dist_unit as pcs,o.total_amt as DP_Total,(o.t_price*o.total_unit) as TP_Total from 
sale_do_master m,sale_do_details o, item_info i,dealer_info d , warehouse w
where m.do_no=o.do_no and i.item_id=o.item_id and m.dealer_code=d.dealer_code and m.status in ('CHECKED','COMPLETED') and w.warehouse_id=d.depot ".$date_con.$item_con.$depot_con.$dtype_con.$do_con." order by m.do_date desc";
}

	//echo report_create($sql,1,$str);
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="8"><?=$str?></td></tr><tr>
<th>S/L</th><th>Item Name</th><th>Crt</th><th>Pcs</th>
<th>TP Total</th>
<th>DP Total</th>
<th>Discount</th>
<th>Actual Amt </th>
</tr></thead>
<tbody>
<?

$tp_t = 0;
$dp_t = 0;
$dis_t = 0;
$act_t = 0;$crt_t = 0;$pcs_t = 0;

$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$discount =0;
$actual_amt = $info->DP_Total;
if($info->DP_Total<0)
{$discount =$info->DP_Total*(-1); $info->DP_Total = 0; $info->TP_Total = 0;}
?>
<tr><td><?=++$i;?></td><td><?=$info->item_name;?></td><td style="text-align:right"><?=$info->crt;?></td><td style="text-align:right"><?=$info->pcs;?></td>
  <td style="text-align:right"><?=$info->TP_Total;?></td>
  <td style="text-align:right"><?=$info->DP_Total;?></td>
  <td style="text-align:right"><?=$discount;?></td>
  <td style="text-align:right"><?=$actual_amt;?></td></tr>
<?
$crt_t = $crt_t + $info->crt;
$pcs_t = $pcs_t + $info->pcs;

$tp_t = $tp_t + $info->TP_Total;
$dp_t = $dp_t + $info->DP_Total;
$dis_t = $dis_t + $discount;
$act_t = $act_t + $actual_amt;
}
?>
<tr class="footer"><td>&nbsp;</td><td><?=$tp_t?></td><td style="text-align:right"><?=$crt_t?></td><td style="text-align:right"><?=$pcs_t?></td><td style="text-align:right"><?=$tp_t?></td>
  <td style="text-align:right"><?=$dp_t?></td>
  <td style="text-align:right"><?=$dis_t?></td>
  <td style="text-align:right"><?=$act_t?></td></tr></tbody></table>
<?
		$str = '';
		echo '</div>';
}
}


elseif($_REQUEST['report']==2013) 
{
 $sql2 	= "select distinct o.do_no, d.dealer_code,d.dealer_name_e,m.do_date,d.address_e,d.mobile_no,d.product_group from 
ss_do_master m,ss_do_details o, item_info i,dealer_info d 
where m.do_no=o.do_no and i.item_id=o.item_id and m.dealer_code=d.dealer_code and m.status in ('CHECKED','COMPLETED')  ".$date_con.$item_con.$dtype_con.$pg_con.$dealer_con." order by o.do_no asc";
$query2 = mysql_query($sql2);

while($data=mysql_fetch_object($query2)){
echo '<div style="position:relative;display:block; width:100%; page-break-after:always; page-break-inside:avoid">';
	$dealer_code = $data->dealer_code;
	$dealer_name = $data->dealer_name_e;
	$warehouse_name = $data->warehouse_name;
	$do_date = $data->do_date;
	$do_no = $data->do_no;
		if($dealer_code>0) 
{
$str 	.= '<p style="width:100%">Dealer Name: '.$dealer_name.' - '.$dealer_code.'('.$data->product_group.')</p>';
$str 	.= '<p style="width:100%">DO NO: '.$do_no.' 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Depot:'.$warehouse_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date:'.$do_date.'</p>
<p style="width:100%">Destination:'.$data->address_e.'('.$data->mobile_no.')</p>';

$dealer_con = ' and m.dealer_code='.$dealer_code;
$do_con = ' and m.do_no='.$do_no;
 $sql = "select concat(i.finish_goods_code,'- ',item_name) as item_name,o.pkt_unit as crt,o.dist_unit as pcs,o.total_unit as qty,o.total_amt as DP_Total,(o.t_price*o.total_unit) as TP_Total from 
ss_do_master m,ss_do_details o, item_info i,dealer_info d 
where m.do_no=o.do_no and i.item_id=o.item_id and m.dealer_code=d.dealer_code and m.status in ('CHECKED','COMPLETED')  ".$date_con.$item_con.$dtype_con.$do_con." order by m.do_date desc";
}

	//echo report_create($sql,1,$str);
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="8"><?=$str?></td></tr><tr>
<th>S/L</th><th>Item Name</th><th>Qty</th>
<th>TP Total</th>
<th>DP Total</th>
<th>Discount</th>
<th>Actual Amt </th>
</tr></thead>
<tbody>
<?

$tp_t = 0;
$dp_t = 0;
$dis_t = 0;
$act_t = 0;$crt_t = 0;$pcs_t = 0;

$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$discount =0;
$actual_amt = $info->DP_Total;
if($info->DP_Total<0)
{$discount =$info->DP_Total*(-1); $info->DP_Total = 0; $info->TP_Total = 0;}
?>
<tr><td><?=++$i;?></td><td><?=$info->item_name;?></td><td style="text-align:right"><?=$info->qty;?></td>
  <td style="text-align:right"><?=$info->TP_Total;?></td>
  <td style="text-align:right"><?=$info->DP_Total;?></td>
  <td style="text-align:right"><?=$discount;?></td>
  <td style="text-align:right"><?=$actual_amt;?></td></tr>
<?
$crt_t = $crt_t + $info->crt;
$pcs_t = $pcs_t + $info->qty;

$tp_t = $tp_t + $info->TP_Total;
$dp_t = $dp_t + $info->DP_Total;
$dis_t = $dis_t + $discount;
$act_t = $act_t + $actual_amt;
}
?>
<tr class="footer"><td>&nbsp;</td><td><?=$tp_t?></td><td style="text-align:right"><?=$pcs_t?></td><td style="text-align:right"><?=$tp_t?></td>
  <td style="text-align:right"><?=$dp_t?></td>
  <td style="text-align:right"><?=$dis_t?></td>
  <td style="text-align:right"><?=$act_t?></td></tr></tbody></table>
<?
		$str = '';
		echo '</div>';
}
}




elseif($_REQUEST['report']==702) 
{
if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if(isset($product_group)) 		{$pg_con=' and d.product_group="'.$product_group.'"';} 
if($depot_id>0) {$dpt_con=' and d.depot="'.$depot_id.'"';} 

$sql = "select 
o.id as code,
sum(o.total_unit) as total_unit
from 
sale_do_master m,sale_do_details o, item_info i,dealer_info d
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') and d.group_for='".$_SESSION['user']['group']."' ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by o.id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty[$info->code] = $info->total_unit;
}

$sql = "select 
c.order_no as code,
sum(c.total_unit) as total_unit
from 
sale_do_master m, item_info i,dealer_info d,sale_do_chalan c
where m.do_no=c.do_no and m.dealer_code=d.dealer_code and i.item_id=c.item_id  and m.status in ('CHECKED','COMPLETED') and d.group_for='".$_SESSION['user']['group']."'  ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by c.order_no';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch_qty[$info->code] = $info->total_unit;
$area[$info->code] = $info->total_unit;
}
		$sql = "select 
		m.do_date,
		m.do_no,
		d.dealer_name_e dealer_name,a.AREA_NAME area, d.dealer_code,d.product_group,
		o.id as code, 
		i.finish_goods_code as fg_code, 
		i.item_name, i.item_brand, 
		i.sales_item_type as `group`,i.pack_size,o.unit_price d_price 
		from 
		sale_do_master m,sale_do_details o, item_info i,dealer_info d, area a
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') 
		and d.group_for='".$_SESSION['user']['group']."'  and i.finish_goods_code not in(2001,2002,2003)
		".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
		group by o.id';
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="18"><?=$str?></td></tr><tr>
<th rowspan="2">S/L</th>
<th rowspan="2">DO Date</th>
<th rowspan="2">DO NO</th>
<th rowspan="2">Dealer Code</th>
<th rowspan="2">GRP</th>
<th rowspan="2">Dealer Name</th>
<th rowspan="2">Area</th>
<th rowspan="2">FG Code</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Pack Size </th>
<th rowspan="2">Price Rate </th>
<th colspan="2">DO Qty</th>
<th colspan="2">CH Qty</th>
<th colspan="2">DUE Qty</th>
<th rowspan="2">DUE Amt </th>
</tr>
<tr>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
</tr>
</thead>
<tbody>
<?

$tp_t = 0;
$dp_t = 0;
$dis_t = 0;
$act_t = 0;$crt_t = 0;$pcs_t = 0;

$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

$discount =0;

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
//if($due_qty[$info->code]>0){

$actual_amt = $info->DP_Total;
$crt = (int)($do_qty[$info->code]/$info->pack_size);
$pcs = (int)($do_qty[$info->code]%$info->pack_size);
$do_total = $do_total + $do_qty[$info->code];

$ccrt = (int)($ch_qty[$info->code]/$info->pack_size);
$cpcs = (int)($ch_qty[$info->code]%$info->pack_size);
$ch_total = $ch_total + $ch_qty[$info->code];

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
$dcrt = (int)($due_qty[$info->code]/$info->pack_size);
$dpcs = (int)($due_qty[$info->code]%$info->pack_size);
$due_total = $due_total + $due_qty[$info->code];
$amt_total = $amt_total + (int)($info->d_price*$due_qty[$info->code]);

if($info->do_no==$odo_no)
{
$oodo_no = '';
$odo_date = '';
$odealer_code = '';
$oproduct_group = '';
$odealer_name = '';
}
else
{
$odo_no = $info->do_no;
$oodo_no = $info->do_no;
$odo_date = $info->do_date;
$odealer_code = $info->dealer_code;
$oproduct_group = $info->product_group;
$odealer_name = $info->dealer_name;
}
?>
<tr><td><?=++$i;?></td>
  <td><?=$odo_date?></td>
  <td><?=$odo_no?></td>
  <td><?=$odealer_code?></td>
  <td><?=$oproduct_group?></td>
  <td><?=$odealer_name?></td>
  <td><?=$info->area;?></td>
  <td><?=$info->fg_code;?></td>
  <td><?=$info->item_name;?></td>
  <td style="text-align:center"><?=$info->pack_size?></td>
  <td style="text-align:right"><?=number_format($info->d_price,2);?></td>
  <td style="text-align:right"><?=(($crt>0)?$crt:'0');?></td>
  <td style="text-align:right"><?=$pcs?></td>
  <td style="text-align:right"><?=(($ccrt>0)?$ccrt:'0');?></td>
  <td style="text-align:right"><?=$cpcs?></td>
  <td style="text-align:right"><?=(($dcrt>0)?$dcrt:'0');?></td>
  <td style="text-align:right"><?=$dpcs?></td>
  <td style="text-align:right"><?=number_format((int)(($info->d_price*$due_qty[$info->code])),2);?></td></tr>
<?
//}
}
?>
<tr class="footer"><td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
<td style="text-align:right">&nbsp;</td>
<td colspan="2" style="text-align:right"><?=$do_total;?></td>
  <td colspan="2" style="text-align:right"><?=$ch_total?></td>
  <td colspan="2" style="text-align:right"><?=$due_total?></td>
  <td style="text-align:right"><?=number_format($amt_total,2)?></td></tr></tbody></table>
<?
		$str = '';
		echo '</div>';

} // END 702



elseif($_REQUEST['report']==707) 
{
if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if(isset($product_group)) 		{$pg_con=' and d.product_group="'.$product_group.'"';} 
if($depot_id>0) {$dpt_con=' and d.depot="'.$depot_id.'"';} 

// DO LIST
$sql = "select 
o.id as code,
sum(o.total_unit) as total_unit
from 
sale_do_master m,sale_do_details o, item_info i,dealer_info d
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') and d.group_for='".$_SESSION['user']['group']."' ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by o.id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty[$info->code] = $info->total_unit;
}

// Chalan list
$sql = "select 
c.order_no as code,
sum(c.total_unit) as total_unit
from 
sale_do_master m, item_info i,dealer_info d,sale_do_chalan c
where m.do_no=c.do_no and m.dealer_code=d.dealer_code and i.item_id=c.item_id  and m.status in ('CHECKED','COMPLETED') and d.group_for='".$_SESSION['user']['group']."'  ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by c.order_no';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch_qty[$info->code] = $info->total_unit;
$area[$info->code] = $info->total_unit;
}
	
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="18"><?=$str?></td></tr><tr>
<th rowspan="2">S/L</th>
<th rowspan="2">DO Date</th>
<th rowspan="2">DO NO</th>
<th rowspan="2">Dealer Code</th>
<th rowspan="2">GRP</th>
<th rowspan="2">Dealer Name</th>
<th rowspan="2">Area</th>
<th rowspan="2">FG Code</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Pack Size </th>
<th rowspan="2">Price Rate </th>
<th colspan="2">DO Qty</th>
<th colspan="2">CH Qty</th>
<th colspan="2">DUE Qty</th>
<th rowspan="2">DUE Amt </th>
</tr>
<tr>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
</tr>
</thead>
<tbody>
<?
$tp_t = 0;
$dp_t = 0;
$dis_t = 0;
$act_t = 0;$crt_t = 0;$pcs_t = 0;


$sql = "select 
		m.do_date,
		m.do_no,
		d.dealer_name_e dealer_name,a.AREA_NAME area, d.dealer_code,d.product_group,
		o.id as code, 
		i.finish_goods_code as fg_code, 
		i.item_name, i.item_brand, 
		i.sales_item_type as `group`,i.pack_size,o.unit_price d_price 
		from 
		sale_do_master m,sale_do_details o, item_info i,dealer_info d, area a
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') 
		and d.group_for='".$_SESSION['user']['group']."'  and i.finish_goods_code not in(2001,2002,2003)
		".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
		group by o.id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

$discount =0;

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
if($due_qty[$info->code]>0){

$actual_amt = $info->DP_Total;
$crt = (int)($do_qty[$info->code]/$info->pack_size);
$pcs = (int)($do_qty[$info->code]%$info->pack_size);
$do_total = $do_total + $do_qty[$info->code];

$ccrt = (int)($ch_qty[$info->code]/$info->pack_size);
$cpcs = (int)($ch_qty[$info->code]%$info->pack_size);
$ch_total = $ch_total + $ch_qty[$info->code];

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
$dcrt = (int)($due_qty[$info->code]/$info->pack_size);
$dpcs = (int)($due_qty[$info->code]%$info->pack_size);
$due_total = $due_total + $due_qty[$info->code];
$amt_total = $amt_total + (int)($info->d_price*$due_qty[$info->code]);

if($info->do_no==$odo_no)
{
$oodo_no = '';
$odo_date = '';
$odealer_code = '';
$oproduct_group = '';
$odealer_name = '';
}
else
{
$odo_no = $info->do_no;
$oodo_no = $info->do_no;
$odo_date = $info->do_date;
$odealer_code = $info->dealer_code;
$oproduct_group = $info->product_group;
$odealer_name = $info->dealer_name;
}
?>
<tr><td><?=++$i;?></td>
  <td><?=$odo_date?></td>
  <td><?=$odo_no?></td>
  <td><?=$odealer_code?></td>
  <td><?=$oproduct_group?></td>
  <td><?=$odealer_name?></td>
  <td><?=$info->area;?></td>
  <td><?=$info->fg_code;?></td>
  <td><?=$info->item_name;?></td>
  <td style="text-align:center"><?=$info->pack_size?></td>
  <td style="text-align:right"><?=number_format($info->d_price,2);?></td>
  <td style="text-align:right"><?=(($crt>0)?$crt:'0');?></td>
  <td style="text-align:right"><?=$pcs?></td>
  <td style="text-align:right"><?=(($ccrt>0)?$ccrt:'0');?></td>
  <td style="text-align:right"><?=$cpcs?></td>
  <td style="text-align:right"><?=(($dcrt>0)?$dcrt:'0');?></td>
  <td style="text-align:right"><?=$dpcs?></td>
  <td style="text-align:right"><?=number_format((int)(($info->d_price*$due_qty[$info->code])),2);?></td></tr>
<?
}
}
?>
<tr class="footer"><td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
<td style="text-align:right">&nbsp;</td>
<td colspan="2" style="text-align:right"><?=$do_total;?></td>
  <td colspan="2" style="text-align:right"><?=$ch_total?></td>
  <td colspan="2" style="text-align:right"><?=$due_total?></td>
  <td style="text-align:right"><?=number_format($amt_total,2)?></td></tr></tbody></table>
<?
		$str = '';
		echo '</div>';

} // END 707




elseif($_REQUEST['report']==703) 
{
if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if(isset($product_group)) 		{$pg_con=' and d.product_group="'.$product_group.'"';} 
if($depot_id>0) {$dpt_con=' and d.depot="'.$depot_id.'"';} 

$sql = "select 
o.id as code,
sum(o.total_unit) as total_unit
from 
sale_do_master m,sale_do_details o, item_info i,dealer_info d
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') and d.group_for='".$_SESSION['user']['group']."' ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by o.id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty[$info->code] = $info->total_unit;
}
$sql = "select 
c.order_no as code,
sum(c.total_unit) as total_unit
from 
sale_do_master m, item_info i,dealer_info d,sale_do_chalan c
where m.do_no=c.do_no and m.dealer_code=d.dealer_code and i.item_id=c.item_id  and m.status in ('CHECKED','COMPLETED') and d.group_for='".$_SESSION['user']['group']."'  ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by c.order_no';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch_qty[$info->code] = $info->total_unit;
$area[$info->code] = $info->total_unit;
}
		$sql = "select 
		m.do_date,
		m.do_no,
		d.dealer_name_e dealer_name,a.AREA_NAME area, d.dealer_code,d.product_group,
		o.id as code, 
		i.finish_goods_code as fg_code, 
		i.item_name, i.item_brand, 
		i.sales_item_type as `group`,i.pack_size,o.unit_price d_price 
		from 
		sale_do_master m,sale_do_details o, item_info i,dealer_info d, area a
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') 
		and d.group_for='".$_SESSION['user']['group']."'  and i.finish_goods_code not in(2001,2002,2003)
		".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
		group by o.id order by d.dealer_code,m.do_date';
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="11">Order Wise Undelivered Report</td></tr><tr>
<th rowspan="2">S/L</th>
<th rowspan="2">Dealer Code</th>
<th rowspan="2">GRP</th>
<th rowspan="2">Dealer Name</th>
<th rowspan="2">Area</th>
<th rowspan="2">Item Name</th>
<th colspan="2">DO Qty</th>
<th colspan="2">DUE Qty</th>
<th rowspan="2">DUE Amt </th>
</tr>
<tr>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
</tr>
</thead>
<tbody>
<?

$tp_t = 0;
$dp_t = 0;
$dis_t = 0;
$act_t = 0;$crt_t = 0;$pcs_t = 0;

$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

$discount =0;

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
if($due_qty[$info->code]>0){
$actual_amt = $info->DP_Total;
$crt = (int)($do_qty[$info->code]/$info->pack_size);
$pcs = (int)($do_qty[$info->code]%$info->pack_size);
$do_total = $do_total + $do_qty[$info->code];

$ccrt = (int)($ch_qty[$info->code]/$info->pack_size);
$cpcs = (int)($ch_qty[$info->code]%$info->pack_size);
$ch_total = $ch_total + $ch_qty[$info->code];

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
$dcrt = (int)($due_qty[$info->code]/$info->pack_size);
$dpcs = (int)($due_qty[$info->code]%$info->pack_size);
$due_total = $due_total + $due_qty[$info->code];
$amt_total = $amt_total + (int)($info->d_price*$due_qty[$info->code]);

if($info->do_no==$odo_no)
{
$oodo_no = '';
$odo_date = '';
$oproduct_group = '';
$odealer_name = '';
}
else
{
if($do_amt_total>0){
?>
<tr><td colspan="10">&nbsp;</td>
<td style="text-align:right"><?=number_format($do_amt_total,2); ?></td></tr>
<?
}
if($odealer_code!=$info->dealer_code&&$dealer_amt_total>0){
?>
<tr><td colspan="10">&nbsp;</td>
<td style="text-align:right"><?=number_format($dealer_amt_total,2); ?></td></tr>
<?
}

$do_amt_total = 0;
$odo_no = $info->do_no;
$oodo_no = $info->do_no;
$odo_date = $info->do_date;

$oproduct_group = $info->product_group;
$odealer_name = $info->dealer_name;

if($odo_no>0)
{
$date1=date_create($odo_date);
$date2=date_create(date('Y-m-d'));
$diff=date_diff($date1,$date2);

// %a outputs the total number of days
$val_days =  $diff->format("%a");

if($odealer_code==$info->dealer_code)
{}
else
{
$dealer_amt_total = 0;
?>
<tr>
  <td><?=++$i;?></td>
  <td><?=$odealer_code?></td>
  <td><?=$oproduct_group?></td>
  <td><?=$odealer_name?></td>
  <td><?=$info->area;?></td>
  <td colspan="6">&nbsp;</td>
</tr>
<? }
$odealer_code = $info->dealer_code;
?>
<tr <? if($val_days>10) echo 'bgcolor="#FF9999"'; else echo 'bgcolor="#FFCCFF"';?> >
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>DO#<?=$odo_no?></td>
  <td>Date#<?=$odo_date?></td>
  <td colspan="6"><?=$val_days?> days Old.</td>
</tr>
<?

}

}
?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td><?=$info->fg_code;?> - <?=$info->item_name;?></td>
  <td style="text-align:right"><?=(($crt>0)?$crt:'0');?></td>
  <td style="text-align:right"><?=$pcs;?></td>
  <td style="text-align:right"><?=(($dcrt>0)?$dcrt:'0');?></td>
  <td style="text-align:right"><?=$dpcs;?></td>
  <td style="text-align:right"><?=number_format((int)(($info->d_price*$due_qty[$info->code])),2);?></td>
</tr>
<?
$do_amt_total = $do_amt_total + (int)($info->d_price*$due_qty[$info->code]);
$dealer_amt_total = $dealer_amt_total + (int)($info->d_price*$due_qty[$info->code]);
}}
?>
<tr><td colspan="10">&nbsp;</td>
<td style="text-align:right"><?=number_format($do_amt_total,2); ?></td></tr>
<tr class="footer"><td colspan="10">&nbsp;</td>
<td style="text-align:right"><?=number_format($amt_total,2)?></td></tr>
  
  </tbody></table>
<?
		$str = '';
		echo '</div>';

}


elseif($_REQUEST['report']==2014) 
{
if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if(isset($product_group)) 		{$pg_con=' and d.product_group="'.$product_group.'"';} 
if($depot_id>0) {$dpt_con=' and d.depot="'.$depot_id.'"';} 

$sql = "select 
o.id as code,
sum(o.total_unit) as total_unit
from 
ss_do_master m,ss_do_details o, item_info i,dealer_info d, personnel_basic_info p
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED')  and p.dealer_code=d.dealer_code 
and d.group_for='".$_SESSION['user']['group']."' ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by o.id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty[$info->code] = $info->total_unit;
}
$sql = "select 
c.order_no as code,
sum(c.total_unit) as total_unit
from 
ss_do_master m, item_info i,dealer_info d,ss_do_chalan c, personnel_basic_info p
where m.do_no=c.do_no and m.dealer_code=d.dealer_code and i.item_id=c.item_id  and m.status in ('CHECKED','COMPLETED') and p.dealer_code=d.dealer_code
and d.group_for='".$_SESSION['user']['group']."'  ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by c.order_no';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch_qty[$info->code] = $info->total_unit;
$area[$info->code] = $info->total_unit;
}
		 $sql = "select 
		m.do_date,
		m.do_no,
		d.dealer_name_e dealer_name,a.AREA_NAME area, d.dealer_code,d.product_group,
		o.id as code, 
		i.finish_goods_code as fg_code, 
		i.item_name, i.item_brand, i.brand_category_type,
		i.sales_item_type as `group`,i.pack_size,o.unit_price d_price , p.PBI_NAME
		from 
		ss_do_master m,ss_do_details o, item_info i,dealer_info d, area a, personnel_basic_info p
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and i.item_id=o.item_id  and p.dealer_code=d.dealer_code
		 and m.status in ('CHECKED','COMPLETED') 
		and d.group_for='".$_SESSION['user']['group']."'  and i.finish_goods_code not in(2001,2002,2003)
		".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
		group by o.do_no,o.item_id order by o.do_no, d.dealer_code, m.do_date';
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="10"><h4>SO TSM DSM Wise Order Report</h4></td></tr><tr>
<th rowspan="2" style="text-align:center">S/L</th>
<th rowspan="2" style="text-align:center">Dealer Code</th>
<th rowspan="2" style="text-align:center">Dealer Name</th>
<th rowspan="2" style="text-align:center">Area</th>
<!--<th rowspan="2" style="text-align:center">&nbsp;</th>-->
<th rowspan="2" style="text-align:center">SO</th>
<th rowspan="2" style="text-align:center">TSM</th>
<th rowspan="2" style="text-align:center">DSM</th>


<th rowspan="2" style="text-align:center">Brand</th>
<th rowspan="2" style="text-align:center">Group</th>
<th rowspan="2" style="text-align:center">Item Category</th>
<th rowspan="2" style="text-align:center">Item Name</th>
<th colspan="2" style="text-align:center">DO Qty</th>
<th rowspan="2" style="text-align:center">Total Qty </th>
<th rowspan="2" style="text-align:center">Amount </th>
</tr>
<tr>
  <th>CTN</th>
  <th>PCS</th>
  </tr>
</thead>
<tbody>
<?

$tp_t = 0;
$dp_t = 0;
$dis_t = 0;
$act_t = 0;$crt_t = 0;$pcs_t = 0;

$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

$discount =0;

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
if($due_qty[$info->code]>0){
$actual_amt = $info->DP_Total;
$crt = (int)($do_qty[$info->code]/$info->pack_size);
$pcs = (int)($do_qty[$info->code]%$info->pack_size);
$do_total = $do_total + $do_qty[$info->code];
$total_unit =  $do_qty[$info->code];

$ccrt = (int)($ch_qty[$info->code]/$info->pack_size);
$cpcs = (int)($ch_qty[$info->code]%$info->pack_size);
$ch_total = $ch_total + $ch_qty[$info->code];

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
$dcrt = (int)($due_qty[$info->code]/$info->pack_size);
$dpcs = (int)($due_qty[$info->code]%$info->pack_size);
$due_total = $due_total + $due_qty[$info->code];
$amt_total = $amt_total + (int)($info->d_price*$due_qty[$info->code]);

if($info->do_no==$odo_no)
{
$oodo_no = '';
$odo_date = '';
$oproduct_group = '';
$odealer_name = '';
}
else
{
if($do_amt_total>0){
?>
<tr><td colspan="9">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
<td style="text-align:right"><?=number_format($do_amt_total,2); ?></td></tr>
<?
}
if($odealer_code!=$info->dealer_code&&$dealer_amt_total>0){
?>
<tr><td colspan="10">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
<td style="text-align:right"><?=number_format($dealer_amt_total,2); ?></td></tr>
<?
}

$do_amt_total = 0;
$odo_no = $info->do_no;
$oodo_no = $info->do_no;
$odo_date = $info->do_date;

$oproduct_group = $info->product_group;
$odealer_name = $info->dealer_name;

if($odo_no>0)
{
$date1=date_create($odo_date);
$date2=date_create(date('Y-m-d'));
$diff=date_diff($date1,$date2);

// %a outputs the total number of days
$val_days =  $diff->format("%a");

if($odealer_code==$info->dealer_code)
{}
else
{
$dealer_amt_total = 0;
?>
<tr>
  <td><?=++$i;?></td>
  <td><?='('.$info->dealer_code.')'; //$odealer_code;?></td>
  <td><?=$odealer_name?> </td>
  <td><?=$info->area;?></td>
  <!--<td>&nbsp;</td>-->
  <td><?=find_a_field('personnel_basic_info','PBI_NAME','DESG_ID=185 and dealer_code = '.$info->dealer_code);?></td>
  <td><?=find_a_field('personnel_basic_info','PBI_NAME','DESG_ID=395 and dealer_code = '.$info->dealer_code);?></td>
  <td><?=find_a_field('personnel_basic_info','PBI_NAME','DESG_ID=326 and dealer_code = '.$info->dealer_code);?></td>
  
  
  <?php /*?><td><?=$info->item_brand;?></td><?php */?>
  <td></td>
  <?php /*?><td><?=$info->group;?></td><?php */?>
  <td></td>
  <td></td>
  
  <td colspan="4">&nbsp;</td>
  <td></td>
</tr>
<? }
$odealer_code = $info->dealer_code;
?>
<tr <? if($val_days>10) echo 'bgcolor="#FF9999"'; else echo 'bgcolor="#FFCCFF"';?> >
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <!--<td>&nbsp;</td>-->
  <td>DO#<?=$odo_no?></td>
  <td>Date: <?=$odo_date?></td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  
  
  <td></td>
  <td></td>
  <td></td>
  <td colspan="4"><?php /*?><?=$val_days?> days Old.<?php */?></td>
  <td> </td>
  <td> </td>
</tr>
<?

}

}
?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <!--<td>&nbsp;</td>-->
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:center"><?=$info->item_brand;?></td>
  <td style="text-align:center"><?=$info->group;?></td>
  <td style="text-align:center"><?=$info->brand_category_type;?></td>
  <td><?=$info->fg_code;?> - <?=$info->item_name;?></td>
  <td style="text-align:right"><?=(($crt>0)?$crt:'0');?></td>
  <td style="text-align:right"><?=$pcs;?></td>
  <td style="text-align:right"><?=$total_unit;?></td>
  <td style="text-align:right"><?=number_format((int)(($info->d_price*$due_qty[$info->code])),2);?></td>
  
</tr>
<?
$do_amt_total = $do_amt_total + (int)($info->d_price*$do_qty[$info->code]);
$dealer_amt_total = $dealer_amt_total + (int)($info->d_price*$do_qty[$info->code]);
}}
?>
<tr><td colspan="10">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td style="text-align:right"><?=number_format($do_total,2)?></td>
<td style="text-align:right"><?=number_format($do_amt_total,2); ?></td></tr>
<tr class="footer"><td colspan="10">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td style="text-align:right"></td>
<td style="text-align:right"><?=number_format($amt_total,2)?></td></tr>
  </tbody></table>
<?
		$str = '';
		echo '</div>';

}


elseif($_REQUEST['report']==2016) 
{
if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if(isset($product_group)) 		{$pg_con=' and d.product_group="'.$product_group.'"';} 
if($depot_id>0) {$dpt_con=' and d.depot="'.$depot_id.'"';} 


$sql = "select d.dealer_code, d.dealer_name_e, p.PBI_NAME,p.PBI_ID, o.id as code, i.pack_size, sum(o.total_unit) as total_unit, sum(o.total_unit*i.d_price) as total_amt
from 
ss_do_master m,ss_do_details o, item_info i,dealer_info d, personnel_basic_info p
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED')  and p.dealer_code=d.dealer_code and p.DESG_ID=185
and d.group_for='".$_SESSION['user']['group']."' AND i.sales_item_type='Oil'  ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by p.PBI_ID';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty_oil[$info->PBI_ID] = $info->total_unit;
$do_amt_oil[$info->PBI_ID] = $info->total_amt;
}	

 $sql = "select d.dealer_code, d.dealer_name_e, p.PBI_NAME,p.PBI_ID, o.id as code, i.pack_size, sum(o.total_unit) as total_unit, sum(o.total_unit*i.d_price) as total_amt
from 
ss_do_master m,ss_do_details o, item_info i,dealer_info d, personnel_basic_info p
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED')  and p.dealer_code=d.dealer_code and p.DESG_ID=185
and d.group_for='".$_SESSION['user']['group']."' AND i.sales_item_type='Agro'  ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by p.PBI_ID';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty_agro[$info->PBI_ID] = $info->total_unit;
$do_amt_agro[$info->PBI_ID] = $info->total_amt;
}	


 $sql = "select d.dealer_code, d.dealer_name_e, p.PBI_NAME,p.PBI_ID, o.id as code, i.pack_size, sum(o.total_unit) as total_unit, sum(o.total_unit*i.d_price) as total_amt
from 
ss_do_master m,ss_do_details o, item_info i,dealer_info d, personnel_basic_info p
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED')  and p.dealer_code=d.dealer_code and p.DESG_ID=185
and d.group_for='".$_SESSION['user']['group']."' ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dealer_con.$dtype_con.' 
group by p.PBI_ID';
$query = mysql_query($sql);
//while($info = mysql_fetch_object($query)){
//$do_qty[$info->code] = $info->total_unit;
//}		
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
	<thead>
		<tr>
			<th>S/L</th>
			<th>Dealer Code</th>
			<th>Dealer Name</th>
			<th>Area</th>
			<th>SO</th>
			<th>TSM</th>
			<th>DSM</th>
			<th>Brand</th>
			<th>Oil Do Qty</th>
			<th>Oil Amount</th>
			<th>Agro DO Qty</th>
			<th>Agro Amount</th>
		</tr>
		
	</thead>
	<tbody>
	<?
		$sl = 1;
		while($info = mysql_fetch_object($query)){
	?>
		<tr>
			<td><?=$sl++;?></td>
			<td><?=$info->dealer_code;?></td>
			<td><?=$info->dealer_name_e;?></td>
			<td></td>
			<td><?=$info->PBI_NAME;?></td>
			<td></td>
			<td></td>
			<td></td>
			<td><?=number_format(($do_qty_oil[$info->PBI_ID]),2);?></td>
			<td><?=number_format(($do_amt_oil[$info->PBI_ID]),2);?></td>
			<td><?=number_format(($do_qty_agro[$info->PBI_ID]),2);?></td>
			<td><?=number_format(($do_amt_agro[$info->PBI_ID]),2);?></td>
		</tr>
	<? } ?>	
	</tbody>
</table>
<?
		$str = '';
		echo '</div>';

}

elseif($_REQUEST['report']==701) 
{
if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if($dealer_type!=''){
	if($dealer_type=='MordernTrade'){
		$dtype_con=$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';
	
	} else {$dtype_con=$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';
	}
}

if(isset($product_group)) 		{$pg_con=' and i.sales_item_type="'.$product_group.'"';} 
if($depot_id>0) {$dpt_con=' and d.depot="'.$depot_id.'"';} 

// Do qty
$sql = "select 
i.finish_goods_code as code,
sum(o.total_unit) as total_unit, sum(o.total_amt) as amount
from 
sale_do_master m,sale_do_details o, item_info i,dealer_info d
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  
and i.finish_goods_code not between 2000 and 2005
and m.status in ('CHECKED','COMPLETED') ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dtype_con.' 
group by i.finish_goods_code';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty[$info->code] = $info->total_unit;
$do_amt[$info->code] = $info->amount;
}


// Chalan qty
$sql = "select 
i.finish_goods_code as code,
sum(c.total_unit) as total_unit, sum(c.total_amt) as amount
from sale_do_master m, item_info i,dealer_info d,sale_do_chalan c
where m.do_no=c.do_no and m.dealer_code=d.dealer_code 
and i.finish_goods_code not between 2000 and 2005
and i.item_id=c.item_id  and m.status in ('CHECKED','COMPLETED') "
.$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dtype_con.' 
group by i.finish_goods_code';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch_qty[$info->code] = $info->total_unit;
$ch_amt[$info->code] = $info->amount;
}

$sql = "select 
		i.finish_goods_code as code, 
		i.item_name, i.item_brand, 
		i.sales_item_type as `group`,i.pack_size,i.d_price
		from sale_do_master m,sale_do_details o, item_info i,dealer_info d
		where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  
		and i.finish_goods_code not between 2000 and 2005
		and m.status in ('CHECKED','COMPLETED') "
		.$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dtype_con.' 
		group by i.finish_goods_code';
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="14"><?=$str?></td></tr><tr>
<th rowspan="2">S/L</th>
<th rowspan="2">Code</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Grp</th>
<th rowspan="2">Brand</th>
<th rowspan="2">Pack Size </th>
<th rowspan="2">Price Rate </th>
<th colspan="2">DO Qty</th>
<th colspan="2">CH Qty</th>
<th colspan="2">DUE Qty</th>
<th rowspan="2">DUE Amt </th>
</tr>
<tr>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
</tr>
</thead>
<tbody>
<?

$tp_t = 0;
$dp_t = 0;
$dis_t = 0;
$act_t = 0;$crt_t = 0;$pcs_t = 0;

$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

$discount =0;


$actual_amt = $info->DP_Total;
$crt = (int)($do_qty[$info->code]/$info->pack_size);
$pcs = (int)($do_qty[$info->code]%$info->pack_size);
$do_total = $do_total + $do_qty[$info->code];

$ccrt = (int)($ch_qty[$info->code]/$info->pack_size);
$cpcs = (int)($ch_qty[$info->code]%$info->pack_size);
$ch_total = $ch_total + $ch_qty[$info->code];

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
$due_amt[$info->code] = ($do_amt[$info->code] - $ch_amt[$info->code]);
$dcrt = (int)($due_qty[$info->code]/$info->pack_size);
$dpcs = (int)($due_qty[$info->code]%$info->pack_size);
$due_total = $due_total + $due_qty[$info->code];
$amt_total = $amt_total + $due_amt[$info->code];
?>
<tr><td><?=++$i;?></td>
  <td><?=$info->code;?></td>
  <td><?=$info->item_name;?></td>
  <td><?=$info->group?></td>
  <td><?=$info->item_brand?></td>
  <td style="text-align:center"><?=$info->pack_size?></td>
  <td style="text-align:right"><?=number_format($info->d_price,2);?></td>
  <td style="text-align:right"><?=(($crt>0)?$crt:'0');?></td>
  <td style="text-align:right"><?=$pcs?></td>
  <td style="text-align:right"><?=(($ccrt>0)?$ccrt:'0');?></td>
  <td style="text-align:right"><?=$cpcs?></td>
  <td style="text-align:right"><?=(($dcrt>0)?$dcrt:'0');?></td>
  <td style="text-align:right"><?=$dpcs?></td>
  <td style="text-align:right"><?=number_format((int)($due_amt[$info->code]),2);?></td></tr>
<?
}
?>
<tr class="footer"><td colspan="13">&nbsp;</td>
<td style="text-align:right"><?=number_format($amt_total,2)?></td></tr>

</tbody></table>
<?
		$str = '';
		echo '</div>';

}

elseif($_POST['report']==704) 
{
			if(isset($sub_group_id)) 			{$item_sub_con=' and i.sub_group_id='.$sub_group_id;} 
			elseif(isset($item_id)) 			{$item_sub_con=' and i.item_id='.$item_id;} 
			
			if(isset($product_group)) 			{$product_group_con=' and i.sales_item_type="'.$product_group.'"';} 
			if(isset($item_brand)) 				{$item_brand_con=' and i.item_brand="'.$item_brand.'"';} 
			
			if(isset($t_date)) 
			{$to_date=$t_date; $fr_date=$f_date; $date_con=' and do_date between "'.$fr_date.'" and "'.$to_date.'" ';}
// DO QTY
$sql = "select 
item_id,depot_id,
sum(total_amt) as total_unit
from 
sale_do_details
where 1 ".$date_con.'
group by item_id,depot_id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do[$info->item_id][$info->depot_id] = $info->total_unit;
}

// Chalan QTY
$sql = "select 
item_id,depot_id,
sum(total_amt) as total_unit
from 
sale_do_chalan
where 1 ".$date_con.' 
group by item_id,depot_id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch[$info->item_id][$info->depot_id] = $info->total_unit;
}

$sql='select distinct i.item_id,i.unit_name,i.brand_category_type,i.item_name,"Finished Goods",i.finish_goods_code,i.sales_item_type,i.item_brand,i.pack_size 
from item_info i where i.finish_goods_code!=2000 and i.finish_goods_code!=2001 and i.finish_goods_code!=2002 and i.finish_goods_code>0 and i.finish_goods_code not between 5000 and 6000 and i.sub_group_id="1096000100010000" '.$item_sub_con.$product_group_con.$item_brand_con.' and i.item_brand != "" and i.status="Active" order by i.finish_goods_code';
		   
$query =mysql_query($sql);
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead><tr><td style="border:0px;" colspan="9"><div class="header">
  <h1>Natura Agro Processing Ltd</h1>
  <h2><?=$report?></h2>
<h3>Date Interval : <?=$fr_date?> to <?=$to_date?> </h3>
</div>
<div class="left"></div><div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr><tr>
<th>S/L-704</th>
<th>Brand</th>
<th>FG</th>
<th>Item Name</th>
<th>Group</th>
<th>Unit</th>
<th>Dhaka</th>
<th>Gazipur</th>
<th>Chittagong</th>
<!--<th>Barisal</th>-->
<th>South Bengal</th>
<!--<th>Bogura</th>-->
<th>North Bengal</th>
<th>Sylhet</th>
<th>Jessore</th>
<th>Rangpur</th>
<th>Comilla</th>
<th>Total</th>
</tr>
</thead><tbody>
<?
while($data=mysql_fetch_object($query)){


	

	$dhaka = 	$do[$data->item_id]['3'] - $ch[$data->item_id]['3'];
	$ctg = 		$do[$data->item_id]['6'] - $ch[$data->item_id]['6'];
	$sylhet =   $do[$data->item_id]['9'] - $ch[$data->item_id]['9'];
	$bogura =   $do[$data->item_id]['7'] - $ch[$data->item_id]['7'];
	$borisal =  $do[$data->item_id]['8'] - $ch[$data->item_id]['8'];
	$jessore =  $do[$data->item_id]['10'] - $ch[$data->item_id]['10'];
	$gajipur =  $do[$data->item_id]['54'] - $ch[$data->item_id]['54'];
	$rangpur =  $do[$data->item_id]['89'] - $ch[$data->item_id]['89'];
	$comilla =  $do[$data->item_id]['90'] - $ch[$data->item_id]['90'];
	
	$total = 	$dhaka + $ctg + $sylhet + $bogura + $borisal + $jessore + $gajipur+ $rangpur + $comilla;	
	//echo $sql = 'select sum(item_in-item_ex) from journal_item where item_id="'.$data->item_id.'"'.$date_con.' and warehouse_id="9"';
if($total>0){

$total_3 = $total_3 + $dhaka;
$total_6 = $total_6 + $ctg;
$total_9 = $total_9 + $sylhet;
$total_7 = $total_7 + $bogura;
$total_8 = $total_8 + $borisal;
$total_10 = $total_10 + $jessore;
$total_54 = $total_54 + $gajipur;
$total_89 = $total_89 + $rangpur;
$total_90 = $total_90 + $comilla;
$total_total = $total_total + $total;
?>
<tr>
	<td><?=++$j?></td>
	<td><?=$data->item_brand?></td>
	<td><?=$data->finish_goods_code?></td>
	<td><?=$data->item_name?></td>
	<td><?=$data->sales_item_type?></td>
	<td><?=$data->unit_name?></td>
	<td style="text-align:right"><?=(int)$dhaka?></td>
	<td style="text-align:right"><?=(int)$gajipur?></td>
	<td style="text-align:right"><?=(int)$ctg?></td>
	<td style="text-align:right"><?=(int)$borisal?></td>
	<td style="text-align:right"><?=(int)$bogura?></td>
	<td style="text-align:right"><?=(int)$sylhet?></td>
	<td style="text-align:right"><?=(int)$jessore?></td>
	<td style="text-align:right"><?=(int)$rangpur?></td>
	<td style="text-align:right"><?=(int)$comilla?></td>
	<td style="text-align:right; font-weight:bold"><?=(int)$total?></td>
</tr>
<?
}
?>

<?
}	
?>
<tr>
	<td colspan="6">Total : </td>
	<td style="text-align:right"><?=(int)$total_3?></td>
	<td style="text-align:right"><?=(int)$total_54?></td>
	<td style="text-align:right"><?=(int)$total_6?></td>
	<td style="text-align:right"><?=(int)$total_8?></td>
	<td style="text-align:right"><?=(int)$total_7?></td>
	<td style="text-align:right"><?=(int)$total_9?></td>
	<td style="text-align:right"><?=(int)$total_10?></td>
	<td style="text-align:right"><?=(int)$total_89?></td>
	<td style="text-align:right"><?=(int)$total_90?></td>
	<td style="text-align:right; font-weight:bold"><?=(int)$total_total?></td>
</tr></tbody></table>
<?
}


elseif($_POST['report']==705) // storewise undelivered report ctn
{
			
			//if(isset($dealer_type)) 		{$dealer_type_con.=' and d.dealer_type="'.$dealer_type.'"';}
			if(isset($sub_group_id)) 			{$item_sub_con=' and i.sub_group_id='.$sub_group_id;} 
			elseif(isset($item_id)) 			{$item_sub_con=' and i.item_id='.$item_id;} 
			
			if(isset($product_group)) 			{$product_group_con=' and i.sales_item_type="'.$product_group.'"';} 
			if(isset($item_brand)) 				{$item_brand_con=' and i.item_brand="'.$item_brand.'"';} 
			
			if(isset($t_date)) 
			{$to_date=$t_date; $fr_date=$f_date; $date_con=' and o.do_date between "'.$fr_date.'" and "'.$to_date.'" ';}
// DO QTY
$sql = "select o.item_id,o.depot_id,sum(o.total_unit)/o.pkt_size as total_unit
from sale_do_details o,   sale_do_master m, dealer_info d
where d.dealer_code=m.dealer_code and o.do_no=m.do_no ".$date_con.$dealer_type_con.' and o.unit_price>0 and m.status in ("CHECKED","COMPLETED")
group by o.item_id,o.depot_id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do[$info->item_id][$info->depot_id] = $info->total_unit;
}

// Chalan QTY
$sql = "select o.item_id,o.depot_id,sum(o.total_unit)/o.pkt_size as total_unit
from sale_do_master m, sale_do_chalan o, dealer_info d
where d.dealer_code=m.dealer_code and o.do_no=m.do_no ".$date_con.$dealer_type_con.' and o.unit_price>0 and m.status in ("CHECKED","COMPLETED")
group by o.item_id,o.depot_id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch[$info->item_id][$info->depot_id] = $info->total_unit;
}

 $sql='select distinct i.item_id,i.unit_name,i.brand_category_type,i.item_name,"Finished Goods",i.finish_goods_code,i.sales_item_type,i.item_brand,i.pack_size 
from item_info i 
where i.finish_goods_code!=2000 and i.finish_goods_code!=2001 
and i.finish_goods_code!=2002 and i.finish_goods_code>0 
and i.finish_goods_code not between 5000 and 6000 
and i.sub_group_id="1096000100010000" 
'.$item_sub_con.$product_group_con.$item_brand_con.'  
and i.status="Active" 
order by i.finish_goods_code';
		   
$query =mysql_query($sql);
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead><tr><td style="border:0px;" colspan="9"><div class="header"><h1>Natura Agro Processing Ltd</h1>
<h2><?=$report?></h2>
<h3>Date Interval : <?=$fr_date?> to <?=$to_date?> </h3>
</div>
<div class="left"></div><div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr><tr>
<th>S/L</th>
<th>Brand</th>
<th>FG</th>
<th>Item Name</th>
<th>Group</th>
<th>Unit</th>
<th>Dhaka</th>
<th>Gazipur</th>
<th>Chittagong</th>
<th><!--Borisal-->South Bengal</th>
<th><!--Bogura-->North Bengal</th>
<th>Sylhet</th>
<th>Jessore</th>
<th>Rangpur</th>
<th>Comilla</th>
<th>Total</th>
</tr>
</thead><tbody>
<?
while($data=mysql_fetch_object($query)){


	$dhaka = 	$do[$data->item_id]['3'] - $ch[$data->item_id]['3'];
	$ctg = 		$do[$data->item_id]['6'] - $ch[$data->item_id]['6'];
	$sylhet =   $do[$data->item_id]['9'] - $ch[$data->item_id]['9'];
	$bogura =   $do[$data->item_id]['7'] - $ch[$data->item_id]['7'];
	$borisal =  $do[$data->item_id]['8'] - $ch[$data->item_id]['8'];
	$jessore =  $do[$data->item_id]['10'] - $ch[$data->item_id]['10'];
	$gajipur =  $do[$data->item_id]['54'] - $ch[$data->item_id]['54'];
	$rangpur =  $do[$data->item_id]['89'] - $ch[$data->item_id]['89'];
	$comilla =  $do[$data->item_id]['90'] - $ch[$data->item_id]['90'];
	
	$total = 	$dhaka + $ctg + $sylhet + $bogura + $borisal + $jessore + $gajipur+ $rangpur + $comilla;	
	//echo $sql = 'select sum(item_in-item_ex) from journal_item where item_id="'.$data->item_id.'"'.$date_con.' and warehouse_id="9"';
if($total>0){

$total_3 = $total_3 + $dhaka;
$total_6 = $total_6 + $ctg;
$total_9 = $total_9 + $sylhet;
$total_7 = $total_7 + $bogura;
$total_8 = $total_8 + $borisal;
$total_10 = $total_10 + $jessore;
$total_54 = $total_54 + $gajipur;
$total_89 = $total_89 + $rangpur;
$total_90 = $total_90 + $comilla;
$total_total = $total_total + $total;
?>
<tr>
	<td><?=++$j?></td>
	<td><?=$data->item_brand?></td>
	<td><?=$data->finish_goods_code?></td>
	<td><?=$data->item_name?></td>
	<td><?=$data->sales_item_type?></td>
	<td><?=$data->unit_name?></td>
	<td style="text-align:right"><?=(int)$dhaka?></td>
	<td style="text-align:right"><?=(int)$gajipur?></td>
	<td style="text-align:right"><?=(int)$ctg?></td>
	<td style="text-align:right"><?=(int)$borisal?></td>
	<td style="text-align:right"><?=(int)$bogura?></td>
	<td style="text-align:right"><?=(int)$sylhet?></td>
	<td style="text-align:right"><?=(int)$jessore?></td>
	<td style="text-align:right"><?=(int)$rangpur?></td>
	<td style="text-align:right"><?=(int)$comilla?></td>
	<td style="text-align:right; font-weight:bold"><?=(int)$total?></td>
</tr>
<?
}
?>

<?
}	
?>
<tr>
	<td colspan="6">Total : </td>
	<td style="text-align:right"><?=(int)$total_3?></td>
	<td style="text-align:right"><?=(int)$total_54?></td>
	<td style="text-align:right"><?=(int)$total_6?></td>
	<td style="text-align:right"><?=(int)$total_8?></td>
	<td style="text-align:right"><?=(int)$total_7?></td>
	<td style="text-align:right"><?=(int)$total_9?></td>
	<td style="text-align:right"><?=(int)$total_10?></td>
	<td style="text-align:right"><?=(int)$total_89?></td>
	<td style="text-align:right"><?=(int)$total_90?></td>
	<td style="text-align:right; font-weight:bold"><?=(int)$total_total?></td>
</tr></tbody></table>
<?
}







elseif($_POST['report']==706) {
			if(isset($sub_group_id)) 			{$item_sub_con=' and i.sub_group_id='.$sub_group_id;} 
			elseif(isset($item_id)) 			{$item_sub_con=' and i.item_id='.$item_id;} 
			if(isset($product_group)) 			{$dealer_group_con=' and product_group="'.$product_group.'"';} 	
			if(isset($t_date)) 
			{$to_date=$t_date; $fr_date=$f_date; 
			$date_con=' and do_date between "'.$fr_date.'" and "'.$to_date.'" ';
			$date_con_do=' and m.do_date between "'.$fr_date.'" and "'.$to_date.'" ';
			$date_con_ji=' and j.ji_date <= "'.$to_date.'" ';
			$chalan_date_con=' and o.chalan_date between "'.$fr_date.'" and "'.$to_date.'" ';
			$date_damage_con=' and m.pi_date between "'.$fr_date.'" and "'.$to_date.'" ';}

// warehouse in query
$ssql = 'select warehouse_id from warehouse 
where use_type = "SD" AND group_for ="'.$_SESSION['user']['group'].'" AND status = "Active" and warehouse_id not in(128,51)';
$qquery = mysql_query($ssql);
    while($sec = mysql_fetch_object($qquery))
{
if($warehouse_list == '') $warehouse_list .= $sec->warehouse_id;
else $warehouse_list .= ','.$sec->warehouse_id;
}


// Stock findout
$sql="SELECT j.warehouse_id, i.item_id, SUM(j.item_in-j.item_ex)/i.pack_size AS qty 
FROM journal_item j, item_info i 
where i.item_id=j.item_id 
and i.finish_goods_code>0 ".$date_con_ji.$item_con1." 
and j.warehouse_id in(".$warehouse_list.")
GROUP BY j.item_id,j.warehouse_id";
	$res	 = mysql_query($sql);
	while($row=mysql_fetch_object($res))
	{
		$stock[$row->warehouse_id][$row->item_id] = $row->qty;	
	}


// DO QTY
$sql = "select o.item_id,o.depot_id,sum(o.total_unit)/o.pkt_size as total_unit
from sale_do_details o,   sale_do_master m
where o.do_no=m.do_no ".$date_con_do.' and o.unit_price>0 and m.status in ("CHECKED","COMPLETED")
group by o.item_id,o.depot_id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do[$info->item_id][$info->depot_id] = $info->total_unit;
}

// Chalan QTY
$sql = "select o.item_id as code,o.depot_id,sum(o.total_unit)/o.pkt_size as total_unit
from sale_do_master m, sale_do_chalan o
where o.do_no=m.do_no ".$date_con_do.' and o.unit_price>0 and m.status in ("CHECKED","COMPLETED")
group by o.item_id,o.depot_id';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch[$info->code][$info->depot_id] = $info->total_unit;
}
?>
<div class="header"><h1>Store Wise Stock vs Due</h1><h4>Date Interval : <?=$fr_date?> to <?=$to_date?> </h4>
</div>
<div class="left"></div><div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead>
<tr>
<th rowspan="2">S/L-706</th>
<th rowspan="2">Item Code</th>
<th rowspan="2">Item Name</th>
<? // store column list
$store_sql='SELECT w.warehouse_id,w.* FROM warehouse w
WHERE use_type = "SD" AND group_for ="'.$_SESSION['user']['group'].'" AND status = "Active" and warehouse_id not in(128,51)';
$query =mysql_query($store_sql);
while($data=mysql_fetch_object($query)){ ?>
<th height="75" colspan="3" bgcolor="#339999"><font class="vertical-text" style="margin-top:170px; width:5px; vertical-align:bottom"><nobr>
<?=$data->warehouse_id?>-<?=$data->warehouse_name?>
</nobr></font></th>
<? } ?>   
	  <th rowspan="2">All Store Gap </th>
	  <th rowspan="2" bgcolor="#FF99FF">Short Total</th>
    </tr>
<tr>

<? 
// store loop
$query =mysql_query($store_sql);
while($data=mysql_fetch_object($query)){ ?>
  <th height="75" bgcolor="#339999"><span style="text-align:right">Stock</span></th>
  <th height="75" bgcolor="#339999"><span style="text-align:right">Due</span></th>
  <th bgcolor="#FF99FF"><span style="text-align:right">Gap</span></th>
<? } ?> </tr>
</thead>
<tbody>


<?
// Item list
$item_sql='select * from item_info i 
where i.finish_goods_code not between 2000 and 2005 and i.finish_goods_code>0 and i.finish_goods_code <9000 and i.finish_goods_code not between 5000 and 6000 
'.$item_sub_con.$product_group_con.$item_brand_con.' 
and i.status="Active" 
order by i.finish_goods_code';
$query =mysql_query($item_sql);
while($data2=mysql_fetch_object($query)){
$gap=0;
$tgap=0;
$short_gap=0;
?>
<tr>
	<td><?=++$k?></td>
	<td><?=$data2->finish_goods_code?></td>
	<td><?=$data2->item_name?></td>
<?
// store loop 
$query3 =mysql_query($store_sql);
while($data3=mysql_fetch_object($query3)){ 

$store_stock = $stock[$data3->warehouse_id][$data2->item_id];
$due = $do[$data2->item_id][$data3->warehouse_id] - $ch[$data2->item_id][$data3->warehouse_id];


$gap= $store_stock-$due; 
$tgap += (int)$gap;
if($gap<0){ $short_gap += (int)$gap; }


?>
      <td style="text-align:right"><? echo (int)$store_stock;?></td>
	  <td style="text-align:right"><!--(<? echo 'DO='.(int)$do[$data2->item_id][$data3->warehouse_id].
	  '-Chalan='.(int)$ch[$data2->item_id][$data3->warehouse_id].')'?>
	 <br>--><strong><? echo (int)$due;?></strong></td>
      <td bordercolor="#FFCCFF" style="text-align:right"><? echo (int)$gap;?></td>
      <? } ?>
	  <td style="text-align:right; font-weight:bold"><span style="text-align:right"><? echo $tgap; ?></span></td>
	  <td style="text-align:right; font-weight:bold"><span style="text-align:right"><? echo $short_gap; ?></span></td>
</tr>

<? } ?>


</tbody></table>
<?
} // end 706
















elseif($_REQUEST['report']==7011) { // 	Item Wise Undelivered DO Report(Without Gift)

if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if($dealer_type!=''){
	if($dealer_type=='MordernTrade'){
		$dtype_con=$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';
	
	} else {$dtype_con=$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';
	}
}

if(isset($product_group)) 		{$pg_con=' and i.sales_item_type="'.$product_group.'"';} 
if($depot_id>0) {$dpt_con=' and d.depot="'.$depot_id.'"';} 


// DO QTY
 $sql = "select 
i.finish_goods_code as code,
sum(o.total_unit) as total_unit, sum(o.total_amt) as amount
from 
sale_do_master m,sale_do_details o, item_info i,dealer_info d
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') and o.unit_price>0
".$dtype_con.$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.' 
group by i.finish_goods_code';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty[$info->code] = $info->total_unit;
$do_amt[$info->code] = $info->amount;
}

// Chalan QTY
$sql = "select 
i.finish_goods_code as code,
sum(c.total_unit) as total_unit, sum(c.total_amt) as amount
from 
sale_do_master m, item_info i,dealer_info d,sale_do_chalan c
where m.do_no=c.do_no and m.dealer_code=d.dealer_code and i.item_id=c.item_id  and m.status in ('CHECKED','COMPLETED') and c.unit_price>0
".$dtype_con.$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.' 
group by i.finish_goods_code';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$ch_qty[$info->code] = $info->total_unit;
$ch_amt[$info->code] = $info->amount;
}


$sql = "select 
i.finish_goods_code as code, 
i.item_name, i.item_brand, 
i.sales_item_type as `group`,i.pack_size,i.d_price
from sale_do_master m,sale_do_details o, item_info i,dealer_info d
where i.finish_goods_code not between 5000 and 6000 
and i.finish_goods_code not between 2000 and 3000 
and m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  
and m.status in ('CHECKED','COMPLETED') ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dtype_con.' 
group by i.finish_goods_code';
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="14"><?=$str?></td></tr><tr>
<th rowspan="2">S/L</th>
<th rowspan="2">Code</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Grp</th>
<th rowspan="2">Brand</th>
<th rowspan="2">Pack Size </th>
<th rowspan="2">Price Rate </th>
<th colspan="2">DO Qty</th>
<th colspan="2">CH Qty</th>
<th colspan="2">DUE Qty</th>
<th rowspan="2">DUE Amt </th>
</tr>
<tr>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
</tr>
</thead>
<tbody>
<?

$tp_t = 0;
$dp_t = 0;
$dis_t = 0;
$act_t = 0;$crt_t = 0;$pcs_t = 0;

$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

$discount =0;

$actual_amt = $info->DP_Total;
$crt = (int)($do_qty[$info->code]/$info->pack_size);
$pcs = (int)($do_qty[$info->code]%$info->pack_size);
$do_total = $do_total + $do_qty[$info->code];


$ccrt = (int)($ch_qty[$info->code]/$info->pack_size);
$cpcs = (int)($ch_qty[$info->code]%$info->pack_size);
$ch_total = $ch_total + $ch_qty[$info->code];

$due_qty[$info->code] = ($do_qty[$info->code] - $ch_qty[$info->code]);
$due_amt[$info->code] = ($do_amt[$info->code] - $ch_amt[$info->code]);
$dcrt = (int)($due_qty[$info->code]/$info->pack_size);
$dpcs = (int)($due_qty[$info->code]%$info->pack_size);
$due_total = $due_total + $due_qty[$info->code];
$amt_total = $amt_total + $due_amt[$info->code];
?>
<tr><td><?=++$i;?></td>
  <td><?=$info->code;?></td>
  <td><?=$info->item_name;?></td>
  <td><?=$info->group?></td>
  <td><?=$info->item_brand?></td>
  <td style="text-align:center"><?=$info->pack_size?></td>
  <td style="text-align:right"><?=number_format($info->d_price,2);?></td>
  <td style="text-align:right"><?=(($crt>0)?$crt:'0');?></td>
  <td style="text-align:right"><?=$pcs?></td>
  <td style="text-align:right"><?=(($ccrt>0)?$ccrt:'0');?></td>
  <td style="text-align:right"><?=$cpcs?></td>
  <td style="text-align:right"><?=(($dcrt>0)?$dcrt:'0');?></td>
  <td style="text-align:right"><?=$dpcs?></td>
  <td style="text-align:right"><?=number_format((int)(($due_amt[$info->code])),2);?></td></tr>
<?
}
?>
<tr class="footer"><td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td style="text-align:right">&nbsp;</td>
<td style="text-align:right">&nbsp;</td>
<td colspan="2" style="text-align:right">&nbsp;</td>
<td colspan="2" style="text-align:right">&nbsp;</td>
<td colspan="2" style="text-align:right">&nbsp;</td>
<td style="text-align:right"><?=number_format($amt_total,2)?></td></tr></tbody></table>

<?
		$str = '';
		echo '</div>';

}



elseif($_REQUEST['report']==7012) { // 	SuperShop Undelivered report

if(isset($t_date)) {
$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

$dtype_con=$dealer_type_con = ' and d.dealer_type="SuperShop"';

if($depot_id>0) {$dpt_con=' and d.depot="'.$depot_id.'"';} 


// Item list
$sql = "select i.finish_goods_code as code, i.item_name, i.item_brand, i.sales_item_type as `group`,i.pack_size,i.d_price,
sum(o.total_unit) as total_unit, sum(o.total_amt) as amount
from sale_do_master m, sale_do_details o, item_info i, dealer_info d
where i.finish_goods_code not between 5000 and 6000 
and i.finish_goods_code not between 2000 and 3000 
and m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  
and m.status in ('CHECKED') ".$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.$dtype_con.' 
group by i.finish_goods_code';
?>

<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="14"><?=$str?>
<div class="left">Note: This data come from those are Fully Untouch DO.</div><div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div>
</td></tr><tr>
<th rowspan="2">S/L</th>
<th rowspan="2">Code</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Grp</th>
<th rowspan="2">Brand</th>
<th rowspan="2">Pack Size </th>
<th rowspan="2">DP Rate</th>
<th colspan="2">Undelivered Qty</th>
<th rowspan="2">Undelivered Amt</th>
</tr>
<tr>
  <th>CTN</th>
  <th>PCS</th>
</tr>
</thead>
<tbody>
<?
$crt_t = 0;
$pcs_t = 0;

$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

$dcrt = (int)($info->total_unit/$info->pack_size);
$dpcs = (int)($info->total_unit%$info->pack_size);
$amount = $info->amount;
?>
<tr><td><?=++$i;?></td>
  <td><?=$info->code;?></td>
  <td><?=$info->item_name;?></td>
  <td><?=$info->group?></td>
  <td><?=$info->item_brand?></td>
  <td style="text-align:center"><?=$info->pack_size?></td>
  <td style="text-align:right"><?=number_format($info->d_price,2);?></td>
  <td style="text-align:right"><?=(($dcrt>0)?$dcrt:'0');?></td>
  <td style="text-align:right"><?=$dpcs?></td>
  <td style="text-align:right"><? echo number_format($amount,2); $total_amount = $total_amount+ $amount;?></td>
  </tr>
<? } ?>
<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td style="text-align:right">&nbsp;</td><td style="text-align:right">&nbsp;</td>
<td colspan="2" style="text-align:right">&nbsp;</td>
<td style="text-align:right"><?=number_format($total_amount,2);?></td>
</tr></tbody></table>
<?

} // end 7012




elseif($_REQUEST['report']==22) { // 	Daily SMS View: 4-May-2021

$ss_date = date('Y-m-01');


if(isset($t_date)) {
$to_date=$t_date; 
$fr_date=$f_date; 
$date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';
}


// Total DO Distributor Single Date
$sql = "select d.product_group,sum(c.total_amt) as total_do 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and d.dealer_type='Distributor' 
and c.total_amt>0 and d.product_group not in('M')
group by d.product_group";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$do_dis[$data->product_group] = $data->total_do;
}


// Total DO Corporate Single Date
$sql = "select sum(c.total_amt) as total_do 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and d.dealer_type='Corporate' 
and c.total_amt>0 
";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$do_corporate = $data->total_do;
}


// Total DO Supershop Single Date
$sql = "select sum(c.total_amt) as total_do 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and (d.dealer_type='SuperShop' or  d.product_group='M')
and c.total_amt>0 
";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$do_super = $data->total_do;
}



// --------------------- Total DO Distributor Monthly
$sql = "select d.product_group,sum(c.total_amt) as total_do 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$ss_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and d.dealer_type='Distributor' 
and c.total_amt>0 and d.product_group not in('M')
group by d.product_group";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$do_dis_monthly[$data->product_group] = $data->total_do;
}

// Total DO Modern Trade Monthly
$sql = "select sum(c.total_amt) as total_do 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$ss_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and ( d.dealer_type='Corporate' or  d.dealer_type='SuperShop' or  d.product_group='M')
and c.total_amt>0 
";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$do_mt_monthly = $data->total_do;
}


// ----------------------- Cash Collection Distributor: 
$sql="select sum(m.rcv_amt) as amount
from sale_do_master m, dealer_info d
where d.dealer_type='Distributor' and d.product_group not in('M')
and m.status in ('CHECKED','COMPLETED') and  m.dealer_code=d.dealer_code
and m.do_date between '".$f_date."' and '".$t_date."'
";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$do_collection = $data->amount;
}

// Cash Collection MT: 
$sql="select sum(m.rcv_amt) as amount
from sale_do_master m, dealer_info d
where ( d.dealer_type='Corporate' or  d.dealer_type='SuperShop' or  d.product_group='M')
and m.status in ('CHECKED','COMPLETED') and  m.dealer_code=d.dealer_code
and m.do_date between '".$f_date."' and '".$t_date."'
";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$mt_collection = $data->amount;
}


// ----------------------- Cash Collection Monthly: 
$sql="select sum(m.rcv_amt) as amount
from sale_do_master m, dealer_info d
where d.dealer_type='Distributor' and d.product_group not in('M')
and m.status in ('CHECKED','COMPLETED') and  m.dealer_code=d.dealer_code
and m.do_date between '".$ss_date."' and '".$t_date."'
";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$do_collection_monthly = $data->amount;
}

// Cash Collection MT: 
$sql="select sum(m.rcv_amt) as amount
from sale_do_master m, dealer_info d
where ( d.dealer_type='Corporate' or  d.dealer_type='SuperShop' or  d.product_group='M')
and m.status in ('CHECKED','COMPLETED') and  m.dealer_code=d.dealer_code
and m.do_date between '".$ss_date."' and '".$t_date."'
";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$mt_collection_monthly = $data->amount;
}
 // End query


?>
<br>Date:<?=date('M-d');?>
<br>1)Retail Sales:
<br>Total DO-<?=(int)($do_dis['A']+$do_dis['B']+$do_dis['C']);?>
<br>A-<?=(int)($do_dis['A']);?>
<br>B-<?=(int)($do_dis['B']);?>
<br>C-<?=(int)($do_dis['C']);?>
<br>Monthly DO-<?=(int)($do_dis_monthly['A']+$do_dis_monthly['B']+$do_dis_monthly['C']);?>

<br>Today Collection-<?=(int)$do_collection;?>
<br>Monthly Collection-<?=(int)$do_collection_monthly;?>
<br>




<br>2)Modern Trade: 
<br>Supershop DO-<?=(int)$do_super;?>
<br>Corporate DO-<?=(int)$do_corporate;?>
<br>Monthly DO-<?=(int)$do_mt_monthly;?>

<br>Today Collection-<?=(int)$mt_collection;?>
<br>Monthly Collection-<?=(int)$mt_collection_monthly;?>
<br>Regards,

<?

} // end 22









elseif($_REQUEST['report']==5) 
{
if(isset($region_id)) 
$sqlbranch 	= "select * from branch where BRANCH_ID=".$region_id;
else
$sqlbranch 	= "select * from branch";
$querybranch = mysql_query($sqlbranch);
while($branch=mysql_fetch_object($querybranch)){
	$rp=0;
	echo '<div>';
if(isset($zone_id)) 
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID." and ZONE_CODE=".$zone_id;
else
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID;

	$queryzone = mysql_query($sqlzone);
	while($zone=mysql_fetch_object($queryzone)){
if($area_id>0) 
$area_con = "and a.AREA_CODE=".$area_id;
$sql="select m.do_no,m.do_date,concat(d.dealer_code,'- ',d.dealer_name_e)  as dealer_name,w.warehouse_name as depot,a.AREA_NAME as area,d.product_group as grp,(select sum(total_amt) from sale_do_details where do_no=m.do_no) as DP_Total,(select sum(t_price*total_unit) from sale_do_details where do_no=m.do_no)  as TP_Total from 
sale_do_master m,dealer_info d  , warehouse w,area a
where  m.status in ('CHECKED','COMPLETED') and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$pg_con.$area_con." order by do_no";
$sqlt="select sum(s.t_price*s.total_unit) as total,sum(total_amt) as dp_total from 
sale_do_master m,dealer_info d  , warehouse w,area a,sale_do_details s
where  m.status in ('CHECKED','COMPLETED') and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and s.do_no=m.do_no and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$pg_con.$area_con;

		$queryt = mysql_query($sqlt);
		$t= mysql_fetch_object($queryt);
		if($t->total>0)
		{
			if($rp==0) {$reg_total=0;$dp_total=0; $str .= '<p style="width:100%">Region Name: '.$branch->BRANCH_NAME.' Region</p>';$rp++;}
			$str .= '<p style="width:100%">Zone Name: '.$zone->ZONE_NAME.' Zone</p>';
			echo report_create($sql,1,$str);
			$str = '';
			
			$reg_total= $reg_total+$t->total;
			$dp_total= $dp_total+$t->dp_total;
		}

	}
	
			if($rp>0){
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;"></td></tr></thead>
<tbody>
  <tr class="footer">
    <td align="right"><?=$branch->BRANCH_NAME?> Region  DP Total: <?=number_format($dp_total,2)?> ||| TP Total: <?=number_format($reg_total,2)?></td></tr></tbody>
</table><br /><br /><br />
<?  }
	echo '</div>';
}
?>

<?


}

elseif($_REQUEST['report']==501) 
{
if(isset($region_id)) 
$sqlbranch 	= "select * from branch where BRANCH_ID=".$region_id;
else
$sqlbranch 	= "select * from branch";
$querybranch = mysql_query($sqlbranch);
while($branch=mysql_fetch_object($querybranch)){
	$rp=0;
	echo '<div>';
if(isset($zone_id)) 
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID." and ZONE_CODE=".$zone_id;
else
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID;

	$queryzone = mysql_query($sqlzone);
	while($zone=mysql_fetch_object($queryzone)){
if($area_id>0) 
$area_con = "and a.AREA_CODE=".$area_id;
$sql="select m.do_no,m.do_date,concat(d.dealer_code,'- ',d.dealer_name_e)  as dealer_name,w.warehouse_name as depot,a.AREA_NAME as area,d.product_group as grp,(select sum(total_amt) from sale_do_details where do_no=m.do_no) as DP_Total,(select sum(t_price*total_unit) from sale_do_details where do_no=m.do_no)  as TP_Total from 
sale_do_master m,dealer_info d  , warehouse w,area a
where  m.status in ('CHECKED','COMPLETED') and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$pg_con.$area_con." order by do_no";
$sqlt="select sum(s.t_price*s.total_unit) as total,sum(total_amt) as dp_total from 
sale_do_master m,dealer_info d  , warehouse w,area a,sale_do_details s
where  m.status in ('CHECKED','COMPLETED') and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and s.do_no=m.do_no and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$pg_con.$area_con;

		$queryt = mysql_query($sqlt);
		$t= mysql_fetch_object($queryt);
		if($t->total>0)
		{
			if($rp==0) {$reg_total=0;$dp_total=0; $str .= '<p style="width:100%">Region Name: '.$branch->BRANCH_NAME.' Region</p>';$rp++;}
			$str .= '<p style="width:100%">Zone Name: '.$zone->ZONE_NAME.' Zone</p>';
			echo report_create($sql,1,$str);
			$str = '';
			
			$reg_total= $reg_total+$t->total;
			$dp_total= $dp_total+$t->dp_total;
		}

	}
	
			if($rp>0){
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;"></td></tr></thead>
<tbody>
  <tr class="footer">
    <td align="right"><?=$branch->BRANCH_NAME?> Region  DP Total: <?=number_format($dp_total,2)?> ||| TP Total: <?=number_format($reg_total,2)?></td></tr></tbody>
</table><br /><br /><br />
<?  }
	echo '</div>';
}
?>
<?
}


elseif($_REQUEST['report']==9) 
{
if(isset($region_id)) 
$sqlbranch 	= "select * from branch where BRANCH_ID=".$region_id;
else
$sqlbranch 	= "select * from branch";
$querybranch = mysql_query($sqlbranch);
while($branch=mysql_fetch_object($querybranch)){
	$rp=0;
	echo '<div>';
if(isset($zone_id)) 
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID." and ZONE_CODE=".$zone_id;
else
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID;

	$queryzone = mysql_query($sqlzone);
	while($zone=mysql_fetch_object($queryzone)){
if($area_id>0) 
$area_con = "and a.AREA_CODE=".$area_id;

$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
floor(sum(o.total_unit)/o.pkt_size) as crt,
mod(sum(o.total_unit),o.pkt_size) as pcs, 
sum(o.total_amt) as DP,
sum(o.total_unit*o.t_price) as TP
from 
sale_do_master m,sale_do_details o, item_info i, warehouse w, dealer_info d, area a
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.item_id=o.item_id and a.AREA_CODE=d.area_code 
and m.status in ('CHECKED','COMPLETED') and a.ZONE_ID=".$zone->ZONE_CODE." and o.unit_price>0
".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.' 
group by i.finish_goods_code';

$sqlt="select sum(o.t_price*o.total_unit) as total,sum(total_amt) as dp_total
from 
sale_do_master m,sale_do_details o, item_info i, warehouse w, dealer_info d, area a
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot 
and i.item_id=o.item_id and a.AREA_CODE=d.area_code  and m.status in ('CHECKED','COMPLETED') 
and o.unit_price>0
and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.'';

		$queryt = mysql_query($sqlt);
		$t= mysql_fetch_object($queryt);
		if($t->total>0)
		{
			if($rp==0) {$reg_total=0;$dp_total=0; 
			$str .= '<p style="width:100%">Region Name: '.$branch->BRANCH_NAME.' Region</p>';$rp++;}
			$str .= '<p style="width:100%">Zone Name: '.$zone->ZONE_NAME.' Zone</p>';
			echo report_create($sql,1,$str);
			$str = '';
			
			$reg_total= $reg_total+$t->total;
			$dp_total= $dp_total+$t->dp_total;
		}

	}
	
if($rp>0){
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;"></td></tr></thead>
<tbody>
  <tr class="footer">
    <td align="right"><?=$branch->BRANCH_NAME?> Region  DP Total: <?=number_format($dp_total,2)?> ||| TP Total: <?=number_format($reg_total,2)?></td></tr></tbody>
</table><br /><br /><br />
<?  }
	echo '</div>';
}
}

elseif($_REQUEST['report']==2011) 
{ 
if(isset($region_id)) 
$sqlbranch 	= "select * from branch where BRANCH_ID=".$region_id;
else
$sqlbranch 	= "select * from branch";
$querybranch = mysql_query($sqlbranch);
while($branch=mysql_fetch_object($querybranch)){
	$rp=0;
	echo '<div>';
if(isset($zone_id)) 
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID." and ZONE_CODE=".$zone_id;
else
 $sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID;

	$queryzone = mysql_query($sqlzone);
	while($zone=mysql_fetch_object($queryzone)){
if($area_id>0) 
$area_con = "and a.AREA_CODE=".$area_id;

$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
floor(sum(o.total_unit)/i.pack_size) as crt,
mod(sum(o.total_unit),i.pack_size) as pcs, 
sum(o.total_amt) as DP,
sum(o.total_unit*o.t_price) as TP
from 
ss_do_master m,ss_do_details o, item_info i, warehouse w, dealer_info d, area a
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.item_id=o.item_id and a.AREA_CODE=d.area_code 
and m.status in ('CHECKED','COMPLETED') and a.ZONE_ID=".$zone->ZONE_CODE." and o.unit_price>0
".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.' 
group by i.finish_goods_code';

 $sqlt="select sum(o.t_price*o.total_unit) as total,sum(total_amt) as dp_total
from 
ss_do_master m,ss_do_details o, item_info i, warehouse w, dealer_info d, area a
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot 
and i.item_id=o.item_id and a.AREA_CODE=d.area_code  and m.status in ('CHECKED','COMPLETED') 
and o.unit_price>0
and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.'';

		$queryt = mysql_query($sqlt);
		$t= mysql_fetch_object($queryt);
		if($t->dp_total>0)
		{
			if($rp==0) {$reg_total=0;$dp_total=0; 
			$str .= '<p style="width:100%">Region Name: '.$branch->BRANCH_NAME.' Region</p>';$rp++;}
			$str .= '<p style="width:100%">Zone Name: '.$zone->ZONE_NAME.' Zone</p>';
			echo report_create($sql,1,$str);
			$str = '';
			
			$reg_total= $reg_total+$t->total;
			$dp_total= $dp_total+$t->dp_total;
		}

	}
	
if($rp>0){
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;"></td></tr></thead>
<tbody>
  <tr class="footer">
    <td align="right"><?=$branch->BRANCH_NAME?> Region  DP Total: <?=number_format($dp_total,2)?> ||| TP Total: <?=number_format($reg_total,2)?></td></tr></tbody>
</table><br /><br /><br />
<?  }
	echo '</div>';
}
}

elseif($_REQUEST['report']==41) {

if(isset($product_group)) {			

	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}


if(isset($region_id)) 
$sqlbranch 	= "select * from branch where BRANCH_ID=".$region_id;
else
$sqlbranch 	= "select * from branch";
$querybranch = mysql_query($sqlbranch);
while($branch=mysql_fetch_object($querybranch)){
	$rp=0;
	echo '<div>';
if(isset($zone_id)) 
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID." and ZONE_CODE=".$zone_id;
else
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID;

	$queryzone = mysql_query($sqlzone);
	while($zone=mysql_fetch_object($queryzone)){
if($area_id>0) 
$area_con = "and a.AREA_CODE=".$area_id;

$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
floor(sum(o.total_unit)/o.pkt_size) as crt,
mod(sum(o.total_unit),o.pkt_size) as pcs, 
sum(o.total_amt) as DP,
sum(o.total_unit*o.t_price) as TP
from 
sale_do_master m,sale_do_details o, item_info i, warehouse w, dealer_info d, area a
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.item_id=o.item_id and a.AREA_CODE=d.area_code  and m.status in ('CHECKED','COMPLETED') and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.' 
group by i.finish_goods_code';

$sqlt="select sum(o.t_price*o.total_unit) as total,sum(total_amt) as dp_total
from 
sale_do_master m,sale_do_details o, item_info i, warehouse w, dealer_info d, area a
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.item_id=o.item_id and a.AREA_CODE=d.area_code  and m.status in ('CHECKED','COMPLETED') 
and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.'';

		$queryt = mysql_query($sqlt);
		$t= mysql_fetch_object($queryt);
		if($t->total>0)
		{
			if($rp==0) {$reg_total=0;$dp_total=0; 
			$str .= '<p style="width:100%">Region Name: '.$branch->BRANCH_NAME.' Region</p>';$rp++;}
			$str .= '<p style="width:100%">Zone Name: '.$zone->ZONE_NAME.' Zone</p>';
			echo report_create($sql,1,$str);
			$str = '';
			
			$reg_total= $reg_total+$t->total;
			$dp_total= $dp_total+$t->dp_total;
		}

	}
	
if($rp>0){
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;"></td></tr></thead>
<tbody>
  <tr class="footer">
    <td align="right"><?=$branch->BRANCH_NAME?> Region  DP Total: <?=number_format($dp_total,2)?> ||| TP Total: <?=number_format($reg_total,2)?></td></tr></tbody>
</table><br /><br /><br />
<?  }
	echo '</div>';
}}


elseif($_REQUEST['report']==14) 
{
if(isset($region_id)) 
$sqlbranch 	= "select * from branch where BRANCH_ID=".$region_id;
else $sqlbranch 	= "select * from branch";
$querybranch = mysql_query($sqlbranch);
while($branch=mysql_fetch_object($querybranch)){
	$rp=0;
	echo '<div>';


$sql = "select i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
floor(sum(o.total_unit)/o.pkt_size) as crt,
mod(sum(o.total_unit),o.pkt_size) as pcs, 
sum(o.total_amt) as DP,
sum(o.total_unit*o.t_price) as TP
from 
sale_do_master m,sale_do_details o, item_info i, warehouse w, dealer_info d, area a, zon z
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.item_id=o.item_id and a.AREA_CODE=d.area_code  and m.status in ('CHECKED','COMPLETED') and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=".$region_id." ".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.' group by i.finish_goods_code';

 $sqlt="select sum(o.t_price*o.total_unit) as total,sum(total_amt) as dp_total from 
sale_do_master m,sale_do_details o, item_info i, warehouse w, dealer_info d, area a, zon z
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.item_id=o.item_id and a.AREA_CODE=d.area_code  and m.status in ('CHECKED','COMPLETED') and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=".$region_id." ".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.'';

		$queryt = mysql_query($sqlt);
		$t= mysql_fetch_object($queryt);
		if($t->total>0)
		{
			if($rp==0) {$reg_total=0;$dp_total=0; 
			$str .= '<p style="width:100%">Region Name: '.$branch->BRANCH_NAME.' Region</p>';$rp++;
		}
			echo report_create($sql,1,$str);
			$str = '';
			
			$reg_total= $reg_total+$t->total;
			$dp_total= $dp_total+$t->dp_total;
		}

	
	
			if($rp>0){
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;"></td></tr></thead>
<tbody>
  <tr class="footer">
    <td align="right"><?=$branch->BRANCH_NAME?> Region  DP Total: <?=number_format($dp_total,2)?> ||| TP Total: <?=number_format($reg_total,2)?></td></tr></tbody>
</table><br /><br /><br />
<?  }
	echo '</div>';
}

}
elseif($_REQUEST['report']==8) 
{
if(isset($region_id)) 
$sqlbranch 	= "select * from branch where BRANCH_ID=".$region_id;
else
$sqlbranch 	= "select * from branch";
$querybranch = mysql_query($sqlbranch);
while($branch=mysql_fetch_object($querybranch)){
	$rp=0;
	echo '<div>';
if(isset($zone_id)) 
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID." and ZONE_CODE=".$zone_id;
else
$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID;

	$queryzone = mysql_query($sqlzone);
	while($zone=mysql_fetch_object($queryzone)){
if(isset($area_id)) 
{
$sql="select concat(d.dealer_code,'- ',d.dealer_name_e)  as dealer_name,w.warehouse_name as depot,a.AREA_NAME as area,d.product_group as grp,(select sum(total_amt) from sale_do_details where do_no=m.do_no) as DP_Total,(select sum(t_price*total_unit) from sale_do_details where do_no=m.do_no)  as TP_Total from 
sale_do_master m,dealer_info d  , warehouse w,area a
where  m.status in ('CHECKED','COMPLETED') and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and a.ZONE_ID=".$zone->ZONE_CODE." and a.AREA_CODE=".$area_id." ".$date_con.$pg_con." order by do_no";
$sqlt="select sum(s.t_price*s.total_unit) as total,sum(total_amt) as dp_total from 
sale_do_master m,dealer_info d  , warehouse w,area a,sale_do_details s
where  m.status in ('CHECKED','COMPLETED') and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and s.do_no=m.do_no and a.AREA_CODE=".$area_id." and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$pg_con;
}
else
{
$sql="select concat(d.dealer_code,'- ',d.dealer_name_e)  as dealer_name,w.warehouse_name as depot,a.AREA_NAME as area,d.product_group as grp,(select sum(total_amt) from sale_do_details where do_no=m.do_no) as DP_Total,(select sum(t_price*total_unit) from sale_do_details where do_no=m.do_no)  as TP_Total from 
sale_do_master m,dealer_info d  , warehouse w,area a
where  m.status in ('CHECKED','COMPLETED') and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$pg_con." order by do_no";
$sqlt="select sum(s.t_price*s.total_unit) as total,sum(total_amt) as dp_total from 
sale_do_master m,dealer_info d  , warehouse w,area a,sale_do_details s
where  m.status in ('CHECKED','COMPLETED') and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and s.do_no=m.do_no and a.ZONE_ID=".$zone->ZONE_CODE." ".$date_con.$pg_con;
}
		$queryt = mysql_query($sqlt);
		$t= mysql_fetch_object($queryt);
		if($t->total>0)
		{
			if($rp==0) {$reg_total=0;$dp_total=0; $str .= '<p style="width:100%">Region Name: '.$branch->BRANCH_NAME.' Region</p>';$rp++;}
			$str .= '<p style="width:100%">Zone Name: '.$zone->ZONE_NAME.' Zone</p>';
			echo report_create($sql,1,$str);
			$str = '';
			
			$reg_total= $reg_total+$t->total;
			$dp_total= $dp_total+$t->dp_total;
		}

	}
	
			if($rp>0){
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;"></td></tr></thead>
<tbody>
  <tr class="footer">
    <td align="right"><?=$branch->BRANCH_NAME?> Region  DP Total: <?=number_format($dp_total,2)?> ||| TP Total: <?=number_format($reg_total,2)?></td></tr></tbody>
</table><br /><br /><br />
<?  }
	echo '</div>';
}

}

elseif($_POST['report']==10041) 
{
			if(isset($sub_group_id)) 			{$item_sub_con=' and i.sub_group_id='.$sub_group_id;} 
			elseif(isset($item_id)) 			{$item_sub_con=' and i.item_id='.$item_id;} 
			
			if(isset($product_group)) 			{$product_group_con=' and i.sales_item_type="'.$product_group.'"';} 
			if(isset($item_brand)) 				{$item_brand_con=' and i.item_brand="'.$item_brand.'"';} 
			
			if(isset($t_date)) 
			{$to_date=$t_date; $fr_date=$f_date; $date_con=' and do_date between "'.$fr_date.'" and "'.$to_date.'" ';}

	$sql="select sum(total_unit) as item_ex,item_id,depot_id warehouse_id from sale_do_details d where 1 ".$date_con." group by item_id,depot_id";
	$res	 = mysql_query($sql);
	while($row=mysql_fetch_object($res))
	{
		$w[$row->item_id][$row->warehouse_id] = $row->item_ex;
	}
	
$sql='select distinct i.item_id,i.unit_name,i.brand_category_type,i.item_name,"Finished Goods",i.finish_goods_code,i.sales_item_type,i.item_brand,i.pack_size 
from item_info i where i.finish_goods_code!=2000 and i.finish_goods_code!=2001 and i.finish_goods_code!=2002 and i.finish_goods_code>0 and i.finish_goods_code not between 5000 and 6000 and i.sub_group_id="1096000100010000" '.$item_sub_con.$product_group_con.$item_brand_con.' and i.item_brand != "" and i.status="Active" order by i.finish_goods_code';
		   
$query =mysql_query($sql);
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead><tr><td style="border:0px;" colspan="9"><div class="header"><h1>Natura Agro Processing Ltd</h1>
<h2><?=$report?></h2>
<h2>Store Wise DO Report</h2>
<h3>Date Interval : <?=$fr_date?> to <?=$to_date?> </h3>
</div>
<div class="left"></div><div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div>
</td></tr><tr>
<th>S/L</th>
<th>Brand</th>
<th>FG</th>
<th>Item Name</th>
<th>Group</th>
<th>Unit</th>
<th>Dhaka</th>
<th>Gazipur</th>
<th>Chittagong</th>
<th><!--Borisal--> South Bengal</th>
<th><!--Bogura-->North Bengal</th>
<th>Sylhet</th>
<th>Jessore</th>
<th>Rangpur</th>
<th>Comilla</th>
<th>Total</th>
</tr>
</thead><tbody>
<?
while($data=mysql_fetch_object($query)){


	

	$dhaka = 	floor($w[$data->item_id]['3']/$data->pack_size);
	$ctg = 		floor($w[$data->item_id]['6']/$data->pack_size);
	$sylhet =   floor($w[$data->item_id]['9']/$data->pack_size);
	$bogura =   floor($w[$data->item_id]['7']/$data->pack_size);
	$borisal =  floor($w[$data->item_id]['8']/$data->pack_size);
	$jessore =  floor($w[$data->item_id]['10']/$data->pack_size);
	$gajipur =  floor($w[$data->item_id]['54']/$data->pack_size);
	$rangpur =  floor($w[$data->item_id]['89']/$data->pack_size);
	$comilla =  floor($w[$data->item_id]['90']/$data->pack_size);
	
	$total = 	$dhaka + $ctg + $sylhet + $bogura + $borisal + $jessore + $gajipur+ $rangpur + $comilla;	   
	
	//echo $sql = 'select sum(item_in-item_ex) from journal_item where item_id="'.$data->item_id.'"'.$date_con.' and warehouse_id="9"';
if($total>0){
?>
<tr>
	<td><?=++$j?></td>
	<td><?=$data->item_brand?></td>
	<td><?=$data->finish_goods_code?></td>
	<td><?=$data->item_name?></td>
	<td><?=$data->sales_item_type?></td>
	<td><?=$data->unit_name?></td>
	<td style="text-align:right"><?=(int)$dhaka?></td>
	<td style="text-align:right"><?=(int)$gajipur?></td>
	<td style="text-align:right"><?=(int)$ctg?></td>
	<td style="text-align:right"><?=(int)$borisal?></td>
	<td style="text-align:right"><?=(int)$bogura?></td>
	<td style="text-align:right"><?=(int)$sylhet?></td>
	<td style="text-align:right"><?=(int)$jessore?></td>
	<td style="text-align:right"><?=(int)$rangpur?></td>
	<td style="text-align:right"><?=(int)$comilla?></td>
	<td style="text-align:right"><?=(int)$total?></td>
</tr>
<?
}
}	
?>
</tbody></table>
<?
}

elseif($_REQUEST['report']==100) 
{
if(isset($dealer_code)) 		{$dealer_con=' and d.dealer_code='.$dealer_code;} 

if(isset($region_id))			{$con .= " and z.REGION_ID=".$region_id; $str .= '<p style="width:100%">Region Name: '.find_a_field('branch','BRANCH_NAME','BRANCH_ID='.$region_id).' Region</p>';}
								 
if(isset($zone_id))				{$con .= " and a.ZONE_ID=".$zone_id;	 $str .= '<p style="width:100%">Zone Name: '.find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_id).' Zone</p>';}
								 
if(isset($area_id)) 			{$con .= " and a.AREA_CODE=".$area_id;	 $str .= '<p style="width:100%">Area Name: '.find_a_field('area','AREA_NAME','AREA_CODE='.$area_id).' Area</p>';}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<thead>
<tr><td style="border:0px;" colspan="11"><?=$str;?></td></tr>
<tr><th>S/L</th>
  <th>CODE</th>
  <th>Dealer Name</th><th>Grp</th><th>Depot</th>
  <th>Region</th>
  <th>Zone</th>
  <th>Area</th>
  <th>Damage</th>
  <th>DO Total</th>
  <th>CH Delivery</th>
  <th>DO Delivery </th>
  <th>Sales Rtn </th>
  </tr>
</thead>
<tbody>
<?
$sql="select d.dealer_code, d.dealer_name_e, w.warehouse_name, a.AREA_NAME as area,z.ZONE_NAME as zone,b.BRANCH_NAME as region, d.product_group as grp from 
dealer_info d  , warehouse w,area a,zon z,branch b
where a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code ".$pg_con.$con.$dealer_con." ";

$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$sql_o='select sum(o.total_amt) from sale_do_master m, sale_do_details o where m.dealer_code="'.$data->dealer_code.'" and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and m.do_date between "'.$fr_date.'" and "'.$to_date.'"';
$data_o=find_a_field_sql($sql_o);
$sql_d='select sum(o.total_amt) from sale_do_master m, sale_do_chalan o where m.dealer_code="'.$data->dealer_code.'" and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and m.do_date between "'.$fr_date.'" and "'.$to_date.'"';
$data_d=find_a_field_sql($sql_d);
$sql_c='select sum(o.total_amt) from sale_do_master m, sale_do_chalan o where m.dealer_code="'.$data->dealer_code.'" and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and o.chalan_date between "'.$fr_date.'" and "'.$to_date.'"';
$data_c=find_a_field_sql($sql_c);
$sql_sr='select sum(o.amount) from warehouse_other_receive_detail o where o.vendor_id="'.$data->dealer_code.'" and o.receive_type ="Return" and o.or_date between "'.$fr_date.'" and "'.$to_date.'"';
$data_sr=find_a_field_sql($sql_sr);

$sql_dr='select sum(o.amount) from warehouse_damage_receive_detail o,damage_cause d where o.vendor_id="'.$data->dealer_code.'" and o.receive_type =d.id and d.payable="Yes" and o.or_date between "'.$fr_date.'" and "'.$to_date.'"';
$data_dr=find_a_field_sql($sql_dr);

?>

<tr><td><?=++$op;?></td>
  <td><?=$data->dealer_code?></td>
  <td><?=$data->dealer_name_e?></td>
  <td><?=$data->grp?></td>
  <td><?=$data->warehouse_name?></td>
  <td><?=$data->region?></td>
  <td><?=$data->zone?></td>
  <td><?=$data->area?></td>
  <td><div align="right"><?=number_format($data_dr,2)?></div></td>
  <td><div align="right"><?=number_format($data_o,2)?></div></td>
  <td><div align="right"><?=number_format($data_c,2)?></div></td>
  <td><div align="right"><?=number_format($data_d,2)?></div></td>
  <td><div align="right"><?=number_format($data_sr,2)?></div></td>
  <?
$ct = $ct + $data_c;
$ot = $ot + $data_o;
$dt = $dt + $data_d;
$srt = $srt + $data_sr;
$drt = $drt + $data_dr;
$ddiff = $ddiff + $diff;
}
?>
</tr>
<tr class="footer"><td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:right"><?=number_format($drt,2)?></td>
  <td style="text-align:right"><div align="right"><?=number_format($ot,2)?></div></td>
  <td style="text-align:right"><div align="right"><?=number_format($ct,2)?></div></td>
  <td style="text-align:right"><div align="right"><?=number_format($dt,2)?></div></td>
  <td style="text-align:right"><div align="right"><?=number_format($srt,2)?></div></td>
  </tr>
</tbody></table>
<?
}

// dealer performance report (do, chalan) 19-4-18
elseif($_REQUEST['report']==99) 
{

if(isset($dealer_code)) 		{$dealer_con=' and d.dealer_code='.$dealer_code;} 
if(isset($depot_id)) 		{$depot_id_con=' and d.depot='.$depot_id;}
if(isset($region_id))			{$con .= " and z.REGION_ID=".$region_id; $str .= '<p style="width:100%">Region Name: '.find_a_field('branch','BRANCH_NAME','BRANCH_ID='.$region_id).' Region</p>';}
if(isset($zone_id))				{$con .= " and a.ZONE_ID=".$zone_id;	 $str .= '<p style="width:100%">Zone Name: '.find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_id).' Zone</p>';}
if(isset($area_id)) 			{$con .= " and a.AREA_CODE=".$area_id;	 $str .= '<p style="width:100%">Area Name: '.find_a_field('area','AREA_NAME','AREA_CODE='.$area_id).' Area</p>';}

if(isset($item_brand)) 			{$item_brand_con=' and i.item_brand="'.$item_brand.'"'; $str .= '<p style="width:100%">Brand Name: '.$item_brand.' Area</p>';} 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<thead>
<tr>
  <td style="border:0px;" colspan="10"><?=$str;?></td>
</tr>
<tr><th>S/L-99</th>
  <th>CODE</th>
  <th>Dealer Name</th>
  <th>Grp</th>
  <th>Depot</th>
  <th>Region</th>
  <th>Zone</th>
  <th>Area</th>
  <th>DO Total</th>
  <th>CH Delivery</th>
  <th>DO Wise Chalan </th>
  <th>Damage </th>
  <th>Sales Return  </th>
  </tr>
</thead>
<tbody>
<?
// do amount
$sql='select m.dealer_code, sum(o.total_amt) total_amt from 
sale_do_master m, sale_do_details o, dealer_info d, warehouse w, area a, zon z, branch b, item_info i where  o.item_id=i.item_id and 
o.total_amt>0 and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and  m.dealer_code=d.dealer_code and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and m.do_date between "'.$fr_date.'" and "'.$to_date.'"  '.$pg_con.$con.$dealer_con.$item_brand_con.$depot_id_con.' group by m.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_o[$data->dealer_code] = $data->total_amt;
}

// do wise chalan
$sql='select m.dealer_code, sum(o.total_amt) total_amt from 
sale_do_master m, sale_do_chalan o, dealer_info d, warehouse w, area a, zon z, branch b, item_info i where  o.item_id=i.item_id and 
o.unit_price>0  and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and  m.dealer_code=d.dealer_code and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and m.do_date between "'.$fr_date.'" and "'.$to_date.'"  '.$pg_con.$con.$dealer_con.$item_brand_con.$depot_id_con.' group by m.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_d[$data->dealer_code] = $data->total_amt;
}

// chalan amount
$sql='select m.dealer_code, sum(o.total_amt) total_amt from 
sale_do_master m, sale_do_chalan o, dealer_info d, warehouse w, area a, zon z, branch b, item_info i where o.item_id=i.item_id and 
o.unit_price>0 and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and  m.dealer_code=d.dealer_code and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and o.chalan_date between "'.$fr_date.'" and "'.$to_date.'"  '.$pg_con.$con.$dealer_con.$item_brand_con.$depot_id_con.' group by m.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_c[$data->dealer_code] = $data->total_amt;
}

// sales return
$sql='select d.dealer_code, sum(w.amount) total_amt 
from warehouse_other_receive_detail w, dealer_info d, item_info i 
where w.item_id=i.item_id 
and  w.vendor_id=d.dealer_code 
and w.receive_type="Return"
and w.or_date between "'.$fr_date.'" and "'.$to_date.'"  
'.$pg_con.$con.$dealer_con.$item_brand_con.$depot_id_con.' 
group by d.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_return[$data->dealer_code] = $data->total_amt;
}


// Damage
$sql='select d.dealer_code, sum(w.amount) total_amt 
from warehouse_damage_receive_detail w, dealer_info d, item_info i 
where w.item_id=i.item_id 
and  w.vendor_id=d.dealer_code 
and w.or_date between "'.$fr_date.'" and "'.$to_date.'"  
'.$pg_con.$con.$dealer_con.$item_brand_con.$depot_id_con.' 
group by d.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_damage[$data->dealer_code] = $data->total_amt;
}


// ---------- Dealer list
$sql="select d.dealer_code, d.dealer_name_e, w.warehouse_name, 
a.AREA_NAME as area,z.ZONE_NAME as zone,b.BRANCH_NAME as region, d.product_group as grp 

from dealer_info d, warehouse w, area a, zon z, branch b
where a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code 
".$pg_con.$con.$dealer_con.$depot_id_con." order by region,zone,area";

$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
?>

<tr><td><?=++$op;?></td>
  <td><?=$data->dealer_code?></td>
  <td><?=$data->dealer_name_e?></td>
  <td><?=$data->grp?></td>
  <td><?=$data->warehouse_name?></td>
  <td><?=$data->region?></td>
  <td><?=$data->zone?></td>
  <td><?=$data->area?></td>
  <td><div align="right"><?=number_format($data_o[$data->dealer_code],2)?></div></td>
  <td><div align="right"><?=number_format($data_c[$data->dealer_code],2)?></div></td>
  <td><div align="right"><?=number_format($data_d[$data->dealer_code],2)?></div></td>
  <td><div align="right"><?=number_format($data_damage[$data->dealer_code],2)?></div></td>
  <td><div align="right"><?=number_format($data_return[$data->dealer_code],2)?></div></td>
  <?
$ct = $ct + $data_c[$data->dealer_code];
$ot = $ot + $data_o[$data->dealer_code];
$dt = $dt + $data_d[$data->dealer_code];
$returnt += $data_return[$data->dealer_code];
$damaget += $data_damage[$data->dealer_code];

}
?>
</tr>
<tr class="footer"><td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:right"><div align="right"><?=number_format($ot,2)?></div></td>
  <td style="text-align:right"><div align="right"><?=number_format($ct,2)?></div></td>
  <td style="text-align:right"><?=number_format($dt,2)?></td>
  <td style="text-align:right"><?=number_format($damaget,2)?></td>
  <td style="text-align:right"><?=number_format($returnt,2)?></td>
  </tr>
</tbody></table>
<?
}


// Dealer Do vs Chalan report.
elseif($_REQUEST['report']==401){

if(!isset($item_id)) { die('Please select Item'); }


if(isset($dealer_code)) 		{$dealer_con=' and d.dealer_code='.$dealer_code;} 
if(isset($depot_id)) 		{$depot_id_con=' and d.depot='.$depot_id;}

if(isset($region_id))		{$con .= " and z.REGION_ID=".$region_id; $str .= '<p style="width:100%">Region Name: '.find_a_field('branch','BRANCH_NAME','BRANCH_ID='.$region_id).' Region</p>';}

if(isset($zone_id))				{$con .= " and a.ZONE_ID=".$zone_id;	 $str .= '<p style="width:100%">Zone Name: '.find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_id).' Zone</p>';}

if(isset($area_id)) 			{$con .= " and a.AREA_CODE=".$area_id;	 $str .= '<p style="width:100%">Area Name: '.find_a_field('area','AREA_NAME','AREA_CODE='.$area_id).' Area</p>';}

if(isset($item_brand)) 			{$item_brand_con=' and i.item_brand="'.$item_brand.'"'; $str .= '<p style="width:100%">Brand Name: '.$item_brand.' Area</p>';} 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<thead>
<tr><td style="border:0px;" colspan="10"><?=$str;?></td></tr>
<tr>
<th>S/L-401</th>
  <th>CODE</th>
  <th>Dealer Name</th>
  <th>Grp</th>
  <th>Depot</th>
  <th>Region</th>
  <th>Zone</th>
  <th>Area</th>
  <th>DO Qty </th>
  <th>DO Wise Chalan Qty </th>
  <th>&nbsp;</th>
  <th>&nbsp;</th>
  </tr>
</thead>
<tbody>
<?
// DO Qty
$sql='select m.dealer_code, sum(o.pkt_unit) total_amt 
from sale_do_master m, sale_do_details o, dealer_info d, warehouse w, area a, zon z, branch b, item_info i 
where  o.item_id=i.item_id and 
o.total_amt>0 and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and  m.dealer_code=d.dealer_code and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and m.do_date between "'.$fr_date.'" and "'.$to_date.'" 
and i.item_id="'.$item_id.'"
'.$pg_con.$con.$dealer_con.$item_brand_con.$depot_id_con.' group by m.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_o[$data->dealer_code] = $data->total_amt;
}

// DO wise chalan qty
$sql='select m.dealer_code, sum(o.pkt_unit) total_amt 
from sale_do_master m, sale_do_chalan o, dealer_info d, warehouse w, area a, zon z, branch b, item_info i 
where  o.item_id=i.item_id and 
o.unit_price>0  and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code and  m.dealer_code=d.dealer_code and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and m.do_date between "'.$fr_date.'" and "'.$to_date.'" 
and i.item_id="'.$item_id.'"
'.$pg_con.$con.$dealer_con.$item_brand_con.$depot_id_con.' group by m.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_d[$data->dealer_code] = $data->total_amt;
}



// ---------- Dealer list
$sql="select d.dealer_code, d.dealer_name_e, w.warehouse_name, 
a.AREA_NAME as area,z.ZONE_NAME as zone,b.BRANCH_NAME as region, d.product_group as grp 

from dealer_info d, warehouse w, area a, zon z, branch b
where a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID and w.warehouse_id=d.depot and a.AREA_CODE=d.area_code 
".$pg_con.$con.$dealer_con.$depot_id_con." order by region,zone,area";

$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
if($data_o[$data->dealer_code]<>0 && $data_d[$data->dealer_code]<>0){
?>

<tr>
<td><?=++$op;?></td>
  <td><?=$data->dealer_code?></td>
  <td><?=$data->dealer_name_e?></td>
  <td><?=$data->grp?></td>
  <td><?=$data->warehouse_name?></td>
  <td><?=$data->region?></td>
  <td><?=$data->zone?></td>
  <td><?=$data->area?></td>
  <td><div align="right"><?=number_format($data_o[$data->dealer_code],2)?></div></td>
  <td><div align="right"><?=number_format($data_d[$data->dealer_code],2)?></div></td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<?
}
} // logic to remove 0
?>
</table>
<?

} // end 


elseif($_REQUEST['report']==991) 
{
if(isset($dealer_code)) 		{$dealer_con=' and d.dealer_code='.$dealer_code;} 

if(isset($item_brand)) 			{$item_brand_con=' and i.item_brand="'.$item_brand.'"'; $str .= '<p style="width:100%">Brand Name: '.$item_brand.' Area</p>';} 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<thead>
<tr>
  <td style="border:0px;" colspan="10"><?=$str;?></td>
</tr>
<tr><th>S/L</th>
  <th>CODE</th>
  <th>Dealer Name</th>
  <th>Grp</th>
  <th>Depot</th>
  <th>DO Total</th>
  <th>CH Delivery</th>
  <th>DO Wise Chalan </th>
  </tr>
</thead>
<tbody>
<?
$sql='select m.dealer_code, sum(o.total_amt) total_amt from 
sale_do_master m, sale_do_details o, dealer_info d, warehouse w, item_info i where  o.item_id=i.item_id and 
o.total_amt>0 and w.warehouse_id=d.depot and  m.dealer_code=d.dealer_code and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and m.do_date between "'.$fr_date.'" and "'.$to_date.'"  '.$dealer_type_con.$pg_con.$con.$dealer_con.$item_brand_con.' group by m.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_o[$data->dealer_code] = $data->total_amt;
}

$sql='select m.dealer_code, sum(o.total_amt) total_amt from 
sale_do_master m, sale_do_chalan o, dealer_info d, warehouse w,  item_info i where  o.item_id=i.item_id and 
o.unit_price>0  and w.warehouse_id=d.depot and  m.dealer_code=d.dealer_code and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and m.do_date between "'.$fr_date.'" and "'.$to_date.'"  '.$dealer_type_con.$pg_con.$con.$dealer_con.$item_brand_con.' group by m.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_d[$data->dealer_code] = $data->total_amt;
}

$sql='select m.dealer_code, sum(o.total_amt) total_amt from 
sale_do_master m, sale_do_chalan o, dealer_info d, warehouse w, item_info i where o.item_id=i.item_id and 
o.unit_price>0 and w.warehouse_id=d.depot and  m.dealer_code=d.dealer_code and m.do_no=o.do_no and m.status in ("COMPLETED","CHECKED") and o.chalan_date between "'.$fr_date.'" and "'.$to_date.'"  '.$dealer_type_con.$pg_con.$con.$dealer_con.$item_brand_con.' group by m.dealer_code';
$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
$data_c[$data->dealer_code] = $data->total_amt;
}

 $sql="select d.dealer_code, d.dealer_name_e, w.warehouse_name, d.product_group as grp from 
dealer_info d, warehouse w
where  w.warehouse_id=d.depot  ".$dealer_type_con.$pg_con.$con.$dealer_con." ";

$query = mysql_query($sql);
while($data= mysql_fetch_object($query)){
?>

<tr><td><?=++$op;?></td>
  <td><?=$data->dealer_code?></td>
  <td><?=$data->dealer_name_e?></td>
  <td><?=$data->grp?></td>
  <td><?=$data->warehouse_name?></td>
  <td><div align="right"><?=number_format($data_o[$data->dealer_code],2)?></div></td>
  <td><div align="right"><?=number_format($data_c[$data->dealer_code],2)?></div></td>
  <td><div align="right"><?=number_format($data_d[$data->dealer_code],2)?></div></td>
  <?
$ct = $ct + $data_c[$data->dealer_code];
$ot = $ot + $data_o[$data->dealer_code];
$dt = $dt + $data_d[$data->dealer_code];

}
?>
</tr>
<tr class="footer"><td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
  <td style="text-align:right"><div align="right"><?=number_format($ot,2)?></div></td>
  <td style="text-align:right"><div align="right"><?=number_format($ct,2)?></div></td>
  <td style="text-align:right"><div align="right"><?=number_format($dt,2)?></div></td>
  </tr>
</tbody></table>
<?


}elseif($_REQUEST['report']==101) 
{
echo $str;
 if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

$f_mons0 = date('Y-m-01',$t_stamp);
$f_mone0 = date('Y-m-'.date('t',$t_stamp),$t_stamp);

$f_mons1 = date('Y-'.($t_array[1]-1).'-01',$t_stamp);
$f_mone1 = date('Y-'.($t_array[1]-1).'-'.date('t',strtotime($f_mons1)),strtotime($f_mons1));

$f_mons2 = date('Y-'.($t_array[1]-2).'-01',$t_stamp);
$f_mone2 = date('Y-'.($t_array[1]-2).'-'.date('t',strtotime($f_mons2)),strtotime($f_mons2));

$f_mons3 = date('Y-'.($t_array[1]-3).'-01',$t_stamp);
$f_mone3 = date('Y-'.($t_array[1]-3).'-'.date('t',strtotime($f_mons3)),strtotime($f_mons3));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">Item Code </span></td>
    <td bgcolor="#333333"><span class="style3">Item Name </span></td>
    <td bgcolor="#333333"><span class="style3">Grp</span></td>
    <td bgcolor="#333333"><span class="style3">Brand</span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons3))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons2))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons1))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('jS-M\'y',strtotime($t_date))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons0))?>
      (Apx.)</span></td>
    <td bgcolor="#333333"><span class="style3">Growth</span></td>
  </tr>
 <?
if(isset($product_group)) 		{$pg_con=' and i.sales_item_type like "%'.$product_group.'%"';}
$sql = "select i.item_id, i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`
from item_info i
where i.item_brand!='Promotional' and i.sales_item_type!='' ".$item_con.$item_brand_con.$pg_con.' group by i.finish_goods_code';

$query = mysql_query($sql);
while($item=mysql_fetch_object($query)){

$sqlmon0 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c, dealer_info d where d.dealer_code=c.dealer_code and c.chalan_date between '".$f_mons0."' and '".$t_date."' and c.item_id=".$item->item_id.$pg_con));

$sqlmon1 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c, dealer_info d where d.dealer_code=c.dealer_code and c.chalan_date between '".$f_mons1."' and '".$f_mone1."' and c.item_id=".$item->item_id.$pg_con));

$sqlmon2 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c, dealer_info d where d.dealer_code=c.dealer_code and c.chalan_date between '".$f_mons2."' and '".$f_mone2."' and c.item_id=".$item->item_id.$pg_con));

$sqlmon3 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c, dealer_info d where d.dealer_code=c.dealer_code and c.chalan_date between '".$f_mons3."' and '".$f_mone3."' and c.item_id=".$item->item_id.$pg_con));

$sqlmon = ((($sqlmon0[0])*date('t'))/$t_array[2]);
$diff = ($sqlmon-$sqlmon1[0]);


 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$i?></td>
    <td><?=$item->code?></td>
    <td><?=$item->item_name?></td>
    <td><?=$item->group?></td>
    <td><?=$item->item_brand?></td>
    <td bgcolor="#99CCFF"><?=$sqlmon3[0]?></td>
    <td bgcolor="#66CC99"><?=$sqlmon2[0]?></td>
    <td bgcolor="#FFFF99"><?=$sqlmon1[0]?></td>
    <td><?=$sqlmon0[0]?></td>
    <td><?=$sqlmon?></td>
    <td style="color:<?=($diff>0)?'#009900;':'#FF0000;'?>"><?=$diff?></td>
  </tr>
  <? }?>
</table>
<?

}
elseif($_REQUEST['report']==102) 
{
echo $str;
 if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

$f_mons0 = date('Y-m-01',$t_stamp);
$f_mone0 = date('Y-m-'.date('t',$t_stamp),$t_stamp);

$f_mons1 = date('Y-'.($t_array[1]-1).'-01',$t_stamp);
$f_mone1 = date('Y-'.($t_array[1]-1).'-'.date('t',strtotime($f_mons1)),strtotime($f_mons1));

$f_mons2 = date('Y-'.($t_array[1]-2).'-01',$t_stamp);
$f_mone2 = date('Y-'.($t_array[1]-2).'-'.date('t',strtotime($f_mons2)),strtotime($f_mons2));

$f_mons3 = date('Y-'.($t_array[1]-3).'-01',$t_stamp);
$f_mone3 = date('Y-'.($t_array[1]-3).'-'.date('t',strtotime($f_mons3)),strtotime($f_mons3));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">Item Code </span></td>
    <td bgcolor="#333333"><span class="style3">Item Name </span></td>
    <td bgcolor="#333333"><span class="style3">Grp</span></td>
    <td bgcolor="#333333"><span class="style3">Brand</span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons3))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons2))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons1))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('jS-M\'y',strtotime($t_date))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons0))?>
      (Apx.)</span></td>
    <td bgcolor="#333333"><span class="style3">Growth</span></td>
  </tr>
<?
if(isset($product_group)) 		{$pg_con=' and i.sales_item_type like "%'.$product_group.'%"';}
$sql = "select i.item_id, i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`
from item_info i
where i.item_brand!='Promotional' and i.sales_item_type!='' ".$item_con.$item_brand_con.$pg_con.' group by i.finish_goods_code';

	$query = mysql_query($sql);
	while($item=mysql_fetch_object($query)){
$sqlmon0 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c, dealer_info d where d.dealer_code=c.dealer_code and c.chalan_date between '".$f_mons0."' and '".$t_date."' and c.item_id=".$item->item_id.$pg_con));
$sqlmon1 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c, dealer_info d where d.dealer_code=c.dealer_code and c.chalan_date between '".$f_mons1."' and '".$f_mone1."' and c.item_id=".$item->item_id.$pg_con));
$sqlmon2 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c, dealer_info d where d.dealer_code=c.dealer_code and c.chalan_date between '".$f_mons2."' and '".$f_mone2."' and c.item_id=".$item->item_id.$pg_con));
$sqlmon3 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c, dealer_info d where d.dealer_code=c.dealer_code and c.chalan_date between '".$f_mons3."' and '".$f_mone3."' and c.item_id=".$item->item_id.$pg_con));

$sqlmon = ((($sqlmon0[0])*date('t'))/$t_array[2]);
$diff = ($sqlmon-$sqlmon1[0]);

$sqlmont3 = $sqlmont3 + $sqlmon3[0];
$sqlmont2 = $sqlmont2 + $sqlmon2[0];
$sqlmont1 = $sqlmont1 + $sqlmon1[0];

$sqlmont = $sqlmont + $sqlmon;
$sqlmont0 = $sqlmont0 + $sqlmon0[0];
 ?>

  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$i?></td>
    <td><?=$item->code?></td>
    <td><?=$item->item_name?></td>
    <td><?=$item->group?></td>
    <td><?=$item->item_brand?></td>
    <td bgcolor="#99CCFF"><?=number_format($sqlmon3[0],2);?></td>
    <td bgcolor="#66CC99"><?=number_format($sqlmon2[0],2);?></td>
    <td bgcolor="#FFFF99"><?=number_format($sqlmon1[0],2);?></td>
    <td><?=number_format($sqlmon0[0],2);?></td>
    <td><?=number_format($sqlmon,2);?></td>
    <td style="color:<?=($diff>0)?'#009900;':'#FF0000;'?>"><?=number_format($diff,2);?></td>
  </tr>
  <? }?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td colspan="5" bgcolor="#FFFFFF"><strong>Total</strong></td>
    <td bgcolor="#FFFFFF"><strong>&nbsp;
        <?=number_format($sqlmont3,2);?>
    </strong></td>
    <td bgcolor="#FFFFFF"><strong>&nbsp;
        <?=number_format($sqlmont2,2);?>
    </strong></td>
    <td bgcolor="#FFFFFF"><strong>&nbsp;
        <?=number_format($sqlmont1,2);?>
    </strong></td>
    <td bgcolor="#FFFFFF"><strong>
      <?=number_format($sqlmont0,2);?>
    </strong></td>
    <td bgcolor="#FFFFFF"><strong>
      <?=number_format($sqlmont,2);?>
    </strong></td>
    <td bgcolor="#FFFFFF" style="color:<?=($diff>0)?'#009900;':'#FF0000;'?>">&nbsp;</td>
  </tr>
</table>
<?

}

elseif($_REQUEST['report']==103) 
{
echo $str;
 if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

$f_mons0 = date('Y-m-01',$t_stamp);
$f_mone0 = date('Y-m-'.date('t',$t_stamp),$t_stamp);

$f_mons1 = date('Y-'.($t_array[1]-1).'-01',$t_stamp);
$f_mone1 = date('Y-'.($t_array[1]-1).'-'.date('t',strtotime($f_mons1)),strtotime($f_mons1));

$f_mons2 = date('Y-'.($t_array[1]-2).'-01',$t_stamp);
$f_mone2 = date('Y-'.($t_array[1]-2).'-'.date('t',strtotime($f_mons2)),strtotime($f_mons2));

$f_mons3 = date('Y-'.($t_array[1]-3).'-01',$t_stamp);
$f_mone3 = date('Y-'.($t_array[1]-3).'-'.date('t',strtotime($f_mons3)),strtotime($f_mons3));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">ZONE NAME </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons3))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons2))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons1))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('jS-M\'y',strtotime($t_date))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons0))?>
      (Apx.)</span></td>
    <td bgcolor="#333333"><span class="style3">Growth</span></td>
  </tr>
 <?
 


$sql = "select * from zon where REGION_ID='".$region_id."' order by ZONE_NAME";
	$query = mysql_query($sql);
	while($item=mysql_fetch_object($query)){
 $zone_code = $item->ZONE_CODE;


echo "select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons0."' and '".$t_date."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con;
$sqlmon0 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons0."' and '".$t_date."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon1 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons1."' and '".$f_mone1."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon2 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons2."' and '".$f_mone2."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon3 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons3."' and '".$f_mone3."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con));



$sqlmon = ((($sqlmon0[0])*date('t'))/$t_array[2]);
$diff = ($sqlmon-$sqlmon1[0]);

 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$i?></td>
    <td><a href="master_report.php?submit=105&report=105&item_id=<?=$_REQUEST['item_id']?>&zone_id=<?=$zone_code?>&t_date=<?=$_REQUEST['t_date']?>" target="_blank"><?=find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_code)?></a></td>
    <td bgcolor="#99CCFF"><?=number_format($sqlmon3[0],2);?></td>
    <td bgcolor="#66CC99"><?=number_format($sqlmon2[0],2);?></td>
    <td bgcolor="#FFFF99"><?=number_format($sqlmon1[0],2);?></td>
    <td><?=number_format($sqlmon0[0],2);?></td>
    <td><?=number_format($sqlmon,2);?></td>
    <td style="color:<?=($diff>0)?'#009900;':'#FF0000;'?>"><?=number_format($diff,2);?></td>
  </tr>
  <? }?>
</table>
<?

}



elseif($_POST['report']==2002) 
{


if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 
		
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		if(isset($dealer_type)) 		{$con.=' and d.dealer_type="'.$dealer_type.'"';}


if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}
		
		$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,
		i.pack_size pkt
		from item_info i where  i.finish_goods_code>0 and i.status="Active" and i.item_brand!="Promotional" and finish_goods_code!=2000 and finish_goods_code!=2001 and finish_goods_code!=2002 and i.item_brand!="Memo" and finish_goods_code not between 9000 and 10000 '.$item_brand_con.$pg_con.$item_con.' 
	 order by i.finish_goods_code';
	 
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and b.BRANCH_ID="'.$region_id.'"';}

// this year
$sql2='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i, area ar, branch b, zon z where ar.ZONE_ID=z.ZONE_CODE and d.area_code=ar.AREA_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_code=m.dealer_code and m.id=a.order_no 
and a.unit_price>0 
and a.item_id=i.item_id '.$acon.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by  a.item_id';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$this_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// last year	
$sql3='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i, area ar, branch b, zon z where ar.ZONE_ID=z.ZONE_CODE and d.area_code=ar.AREA_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_code=m.dealer_code and m.id=a.order_no and  
	a.unit_price>0 and a.item_id=i.item_id '.$acon.$con.$ydate_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by  a.item_id';
	
	$query2 = mysql_query($sql3);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$last_year_sale_qty[$data2->item_id] = $data2->qty;
	}



?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead><tr><td style="border:0px;" colspan="9"><?=$str?><div class="left"></div><div class="right"></div><div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr>

<tr><th rowspan="2">S/L</th><th rowspan="2">Fg</th><th rowspan="2">Item Name</th><th rowspan="2">Unit</th><th rowspan="2">Brand</th><th rowspan="2">Pack Size</th><th rowspan="2">GRP</th>
  <th colspan="3" bgcolor="#FFCCFF"><div align="center">Last Year </div></th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Year </div></th>
<th>Growth</th>
</tr>
<tr>
  <th>Sale Ctn </th>
  <th>Sale Qty </th>
  <th>Sale Amt </th>
  <th>Sale Ctn </th>
  <th>Sale Qty </th>
  <th>Sale Amt </th>
  <th>in % </th>
</tr>

</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->item_id]>$last_year_sale_qty[$data->item_id])
{
$growth = @((($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]));
$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]);
$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;
?>
<tr><td><?=++$s?></td><td><?=$data->fg?></td><td><?=$data->item_name?></td><td><?=$data->unit?></td><td><?=$data->brand?></td><td><?=$data->pkt?></td><td><?=$data->sales_item_type?></td>
  <td style="text-align:right"><?=(int)($last_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><?=(int)($this_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->item_id],0,'',',')?></td>
<td style="text-align:right"><?=number_format($this_year_sale_amt[$data->item_id],0,'',',')?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($growth)),2)?></td>
</tr>
<?
}
?>
</tbody></table>
<?
}


elseif($_POST['report']==2022) 
{


if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 
		
		// $pre_m = date(('m'),strtotime($f_date))-1;
		
		 $yfr_date=date('Y-').(date(('m'),strtotime($f_date))-1).date(('-d'),strtotime($f_date));
		 $yto_date=date('Y-').(date(('m'),strtotime($t_date))-1).date(('-31'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		if(isset($dealer_type)) 		{$con.=' and d.dealer_type="'.$dealer_type.'"';}


if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}
		
		$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,
		i.pack_size pkt
		from item_info i where  i.finish_goods_code>0 and i.status="Active" and i.item_brand!="Promotional" and finish_goods_code!=2000 and finish_goods_code!=2001 and finish_goods_code!=2002 and i.item_brand!="Memo" and finish_goods_code not between 9000 and 10000 '.$item_brand_con.$pg_con.$item_con.' 
	 order by i.finish_goods_code';
	 
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and b.BRANCH_ID="'.$region_id.'"';}

// this year
 $sql2='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i, area ar, branch b, zon z where ar.ZONE_ID=z.ZONE_CODE and d.area_code=ar.AREA_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_code=m.dealer_code and m.id=a.order_no 
and a.unit_price>0 
and a.item_id=i.item_id '.$acon.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by  a.item_id';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$this_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// last year	
 $sql3='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i, area ar, branch b, zon z where ar.ZONE_ID=z.ZONE_CODE and d.area_code=ar.AREA_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_code=m.dealer_code and m.id=a.order_no and  
	a.unit_price>0 and a.item_id=i.item_id '.$acon.$con.$ydate_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by  a.item_id';
	
	$query2 = mysql_query($sql3);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$last_year_sale_qty[$data2->item_id] = $data2->qty;
	}



?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead><tr><td style="border:0px;" colspan="9"><?=$str?><div class="left"></div><div class="right"></div><div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr>

<tr><th rowspan="2">S/L</th><th rowspan="2">Fg</th><th rowspan="2">Item Name</th><th rowspan="2">Unit</th><th rowspan="2">Brand</th><th rowspan="2">Pack Size</th><th rowspan="2">GRP</th>
  <th colspan="3" bgcolor="#FFCCFF"><div align="center">Last Month </div></th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Month </div></th>
<th>Growth</th>
</tr>
<tr>
  <th>Sale Ctn </th>
  <th>Sale Qty </th>
  <th>Sale Amt </th>
  <th>Sale Ctn </th>
  <th>Sale Qty </th>
  <th>Sale Amt </th>
  <th>in % </th>
</tr>

</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->item_id]>$last_year_sale_qty[$data->item_id])
{
$growth = @((($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]));
$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]);
$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;
?>
<tr><td><?=++$s?></td><td><?=$data->fg?></td><td><?=$data->item_name?></td><td><?=$data->unit?></td><td><?=$data->brand?></td><td><?=$data->pkt?></td><td><?=$data->sales_item_type?></td>
  <td style="text-align:right"><?=(int)($last_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><?=(int)($this_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->item_id],0,'',',')?></td>
<td style="text-align:right"><?=number_format($this_year_sale_amt[$data->item_id],0,'',',')?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($growth)),2)?></td>
</tr>
<?
}
?>
</tbody></table>
<?
}

// ----------- ZONE WISE / ITEM WISE SALES REPORT 23-Jan-2021
elseif($_POST['report']==3001) 
{


if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 
		
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		//if(isset($dealer_type)) 		{$con.=' and d.dealer_type="'.$dealer_type.'"';}


if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}

if($dealer_type!='') { $acon.=' and d.dealer_type="'.$dealer_type.'"'; }else { $acon.=' and d.dealer_type="Distributor"';}
	 
//if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$zcon.=' and ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$zcon.=' and REGION_ID="'.$region_id.'"';}


// this year
$sql2='select d.zone_id,i.item_id,sum(a.total_unit/i.pack_size) qty,sum(a.total_unit*a.unit_price) as amount	
from dealer_info d, sale_do_chalan a, item_info i 
where d.dealer_code=a.dealer_code and a.unit_price>0 and a.item_id=i.item_id 
'.$acon.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.' 
group by  d.zone_id, i.item_id order by d.zone_id,i.item_id';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->zone_id][$data2->item_id] = $data2->amount;
	$this_year_sale_qty[$data2->zone_id][$data2->item_id] = $data2->qty;
	}


// last year	
$sql3='select d.zone_id,i.item_id,sum(a.total_unit/i.pack_size) qty,sum(a.total_unit*a.unit_price) as amount	
from dealer_info d, sale_do_chalan a, item_info i 
where d.dealer_code=a.dealer_code and a.unit_price>0 and a.item_id=i.item_id 
'.$acon.$con.$ydate_con.$warehouse_con.$item_con.$item_brand_con.' 
group by  d.zone_id, i.item_id order by d.zone_id,i.item_id';
	$query3 = mysql_query($sql3);
	while($data3 = mysql_fetch_object($query3))
	{
	$last_year_sale_amt[$data3->zone_id][$data3->item_id] = $data3->amount;
	$last_year_sale_qty[$data3->zone_id][$data3->item_id] = $data3->qty;
	}


?>
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 4px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead><tr><td style="border:0px;" colspan="9"><?=$str?><div class="left"></div><div class="right"></div><div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr>

<tr><th rowspan="2">S/L</th><th rowspan="2">Fg</th><th rowspan="2">Item Name</th><th rowspan="2">Unit</th><th rowspan="2">Brand</th><th rowspan="2">Pack Size</th><th rowspan="2">GRP</th>

      <? 
	  $sql_zone='select ZONE_CODE as zone_id,ZONE_NAME from zon where 1 '.$zcon;
	  $q1=mysql_query($sql_zone);
	  while($zdata=mysql_fetch_object($q1)){
	  ?>
      <th height="30" colspan="4" bgcolor="#339999">
	  <font class="vertical-text" style="margin-top:170px; width:5px; vertical-align:bottom"><nobr>
		<?=$zdata->zone_id?>-<?=$zdata->ZONE_NAME?>
	  </nobr></font></th>
      <? } ?> 


</tr>
<tr>
  <? 
  $q1=mysql_query($sql_zone);
  while($zdata=mysql_fetch_object($q1)){?>
  <th bgcolor="#CCCC00" style="text-align:right">L Qty</th>
  <th bgcolor="#CCCC00" style="text-align:right">L Amt</th>
  <th bgcolor="#4563E8" style="text-align:right">T Qty</th>
  <th bgcolor="#4563E8" style="text-align:right">T Amt</th>
  <? } ?> 
</tr>

</thead><tbody>
<?
// -------------- ITEM LIST
$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,
		i.pack_size pkt
from item_info i 
where i.finish_goods_code>0 and i.status="Active" and i.item_brand not in("Promotional","Memo")
and finish_goods_code not between 2000 and 2005
'.$item_brand_con.$pg_con.$item_con.' 
order by i.finish_goods_code';
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$l_amt=0; $t_amt=0;
?>
<tr>
<td><?=++$s?></td><td><?=$data->fg?></td><td><?=$data->item_name?></td><td><?=$data->unit?></td><td><?=$data->brand?></td>
<td><?=$data->pkt?></td><td><?=$data->sales_item_type?></td>
  
<? $q1=mysql_query($sql_zone);
  while($zdata=mysql_fetch_object($q1)){  ?> 
  <td style="text-align:right"><?=(int)($last_year_sale_qty[$zdata->zone_id][$data->item_id])?></td>
  <td style="text-align:right"><strong><? echo $l_amt=(int)($last_year_sale_amt[$zdata->zone_id][$data->item_id]); //$f_l_amt+=$l_amt;?></strong></td>
  <td style="text-align:right"><?=(int)($this_year_sale_qty[$zdata->zone_id][$data->item_id])?></td>
  <td style="text-align:right"><strong><? echo $t_amt=(int)($this_year_sale_amt[$zdata->zone_id][$data->item_id]); //$f_t_amt+=$t_amt;?></strong></td>
		<?
$z_l_amt[$zdata->zone_id] += $l_amt;
$z_t_amt[$zdata->zone_id] += $t_amt;		
		
/*		if($this_year_sale_qty[$zdata->zone_id][$data->item_id]>=$last_year_sale_qty[$zdata->zone_id][$data->item_id])
		{
		$growth = (($this_year_sale_qty[$zdata->zone_id][$data->item_id])/($last_year_sale_qty[$zdata->zone_id][$data->item_id])*100);
		$bg = '; background-color:#99FFFF';
		}else{
		$growth = -(($this_year_sale_qty[$zdata->zone_id][$data->item_id])/($last_year_sale_qty[$zdata->zone_id][$data->item_id])*100);
		$bg = '; background-color:#FFCCCC';
		}*/
		?>  
  <!--<td style="text-align:right<?=$bg?>"><? echo number_format($growth,2);?></td>-->
<? } ?>
</tr>
<?
}
?>
<tr>
<td colspan="7" style="text-align:right"><strong>Total</strong></td>
<? $q1=mysql_query($sql_zone);
  while($zdata=mysql_fetch_object($q1)){ ?> 
<td></td><td><strong><?=$z_l_amt[$zdata->zone_id]?></strong></td>
<td></td><td><strong><?=$z_t_amt[$zdata->zone_id]?><strong></td>
<? } ?>
</tr>
</tbody></table>
<?
} // end 2008



// 2017 VS 2018 REPORT
elseif($_POST['report']==2005) {

?>
<center>
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 3px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="10"><h2>Yearly Distributor Sales Report</h2><div class="left"></div>
<div class="left"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr>

<tr><th rowspan="2">S/L</th><th rowspan="2">Fg</th><th rowspan="2">Item Name</th><th rowspan="2">Unit</th><th rowspan="2">Brand</th>
  <th rowspan="2">Type</th>
  <th rowspan="2">Pack Size</th><th rowspan="2">GRP</th>
  <th colspan="13" bgcolor="#FFCCFF"><div align="center">2018 Sales(Ctn) </div></th>
  <th colspan="13" bgcolor="#FFFF99"><div align="center">2019  Sales(Ctn)</div></th>
  <th colspan="13" bgcolor="#CC9933"><div align="center">2020  Sales(Ctn)</div></th>
<th>&nbsp;</th>
</tr>
<tr>
  <th>Jan </th>
  <th>Feb </th>
  <th>Mar</th>
  <th>Apr</th>
  <th>May</th>
  <th>Jun</th>
  <th>Jul</th>
  <th>Aug</th>
  <th>Sep</th>
  <th>Oct</th>
  <th>Nov</th>
  <th>Dec</th>
  <th>Total</th>
  <th>Jan </th>
  <th>Feb </th>
  <th>Mar</th>
  <th>Apr</th>
  <th>May</th>
  <th>Jun</th>
  <th>Jul</th>
  <th>Aug</th>
  <th>Sep</th>
  <th>Oct</th>
  <th>Nov</th>
  <th>Dec</th>
  <th>Total</th>
    <th>Jan </th>
  <th>Feb </th>
  <th>Mar</th>
  <th>Apr</th>
  <th>May</th>
  <th>Jun</th>
  <th>Jul</th>
  <th>Aug</th>
  <th>Sep</th>
  <th>Oct</th>
  <th>Nov</th>
  <th>Dec</th>
  <th>Total</th>
  <th>Remarks</th>
</tr>

</thead><tbody>
<?

// January
$sql2='select fg_code as code,year_18,year_19,year_20 from report_item_wise where mon=1';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$jan_18[$data2->code] = $data2->year_18;
	$jan_19[$data2->code] = $data2->year_19;
	$jan_20[$data2->code] = $data2->year_20;
	}
	
// February
$sql2='select fg_code as code,year_18,year_19,year_20 from report_item_wise where mon=2';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$feb_18[$data2->code] = $data2->year_18;
	$feb_19[$data2->code] = $data2->year_19;
	$feb_20[$data2->code] = $data2->year_20;
	}
	
// March
$sql2='select fg_code as code,year_18,year_19,year_20 from report_item_wise where mon=3';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$mar_20[$data2->code] = $data2->year_20;
	$mar_18[$data2->code] = $data2->year_18;
	$mar_19[$data2->code] = $data2->year_19;
	}	
	
// April
$sql2='select fg_code as code,year_18,year_19,year_20 from report_item_wise where mon=4';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$apr_20[$data2->code] = $data2->year_20;
	$apr_18[$data2->code] = $data2->year_18;
	$apr_19[$data2->code] = $data2->year_19;
	}	

// May
$sql2='select fg_code as code,year_18,year_19,year_20 from report_item_wise where mon=5';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$may_20[$data2->code] = $data2->year_20;
	$may_18[$data2->code] = $data2->year_18;
	$may_19[$data2->code] = $data2->year_19;
	}

// June
$sql2='select fg_code as code,year_20,year_18,year_19 from report_item_wise where mon=6';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$jun_20[$data2->code] = $data2->year_20;
	$jun_18[$data2->code] = $data2->year_18;
	$jun_19[$data2->code] = $data2->year_19;
	}

// July
$sql2='select fg_code as code,year_20,year_18,year_19 from report_item_wise where mon=7';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$jul_20[$data2->code] = $data2->year_20;
	$jul_18[$data2->code] = $data2->year_18;
	$jul_19[$data2->code] = $data2->year_19;
	}

// August
$sql2='select fg_code as code,year_20,year_18,year_19 from report_item_wise where mon=8';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$aug_20[$data2->code] = $data2->year_20;
	$aug_18[$data2->code] = $data2->year_18;
	$aug_19[$data2->code] = $data2->year_19;
	}


// September
$sql2='select fg_code as code,year_20,year_18,year_19 from report_item_wise where mon=9';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$sep_20[$data2->code] = $data2->year_20;
	$sep_18[$data2->code] = $data2->year_18;
	$sep_19[$data2->code] = $data2->year_19;
	}

// October
$sql2='select fg_code as code,year_20,year_18,year_19 from report_item_wise where mon=10';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$oct_20[$data2->code] = $data2->year_20;
	$oct_18[$data2->code] = $data2->year_18;
	$oct_19[$data2->code] = $data2->year_19;
	}

// November
$sql2='select fg_code as code,year_20,year_18,year_19 from report_item_wise where mon=11';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$nov_20[$data2->code] = $data2->year_20;
	$nov_18[$data2->code] = $data2->year_18;
	$nov_19[$data2->code] = $data2->year_19;
	}
	
// December
$sql2='select fg_code as code,year_20,year_18,year_19 from report_item_wise where mon=12';
$query = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query))
	{
	$dec_20[$data2->code] = $data2->year_20;
	$dec_18[$data2->code] = $data2->year_18;
	$dec_19[$data2->code] = $data2->year_19;
	}	

// item list
$sql='select 
		i.finish_goods_code as code,
		i.item_id,
		i.item_name,i.brand_category_type as item_type,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,
		i.pack_size pkt		
from item_info i where 
i.finish_goods_code>0 and i.status="Active" and i.item_brand!="Promotional" and i.item_brand!="Memo" 
and finish_goods_code not between 2000 and 2003 
and finish_goods_code not between 9000 and 10000

'.$item_brand_con.$pg_con.$item_con.' 
order by i.finish_goods_code';

$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){


?>
<tr><td><?=++$s?></td><td><?=$data->code?></td><td><?=$data->item_name?></td><td><?=$data->unit?></td><td><?=$data->brand?></td>
  <td><?=$data->item_type?></td>
  <td><?=$data->pkt?></td>
<td><?=$data->sales_item_type?></td>
<td style="text-align:right"><? if($jan_18[$data->code]>0) echo $jan_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($feb_18[$data->code]>0) echo $feb_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($mar_18[$data->code]>0) echo $mar_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($apr_18[$data->code]>0) echo $apr_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($may_18[$data->code]>0) echo $may_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($jun_18[$data->code]>0) echo $jun_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($jul_18[$data->code]>0) echo $jul_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($aug_18[$data->code]>0) echo $aug_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($sep_18[$data->code]>0) echo $sep_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($oct_18[$data->code]>0) echo $oct_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($nov_18[$data->code]>0) echo $nov_18[$data->code]; else echo '';?></td>
<td style="text-align:right"><? if($dec_18[$data->code]>0) echo $dec_18[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? 
  echo $total_18=($jan_18[$data->code]+$feb_18[$data->code]+$mar_18[$data->code]+$apr_18[$data->code]+$may_18[$data->code]+$jun_18[$data->code]
  +$jul_18[$data->code]+$aug_18[$data->code]+$sep_18[$data->code]+$oct_18[$data->code]+$nov_18[$data->code]+$dec_18[$data->code]);
  ?></td>
  <td style="text-align:right"><? if($jan_19[$data->code]>0) echo $jan_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($feb_19[$data->code]>0) echo $feb_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($mar_19[$data->code]>0) echo $mar_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($apr_19[$data->code]>0) echo $apr_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($may_19[$data->code]>0) echo $may_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($jun_19[$data->code]>0) echo $jun_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($jul_19[$data->code]>0) echo $jul_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($aug_19[$data->code]>0) echo $aug_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($sep_19[$data->code]>0) echo $sep_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($oct_19[$data->code]>0) echo $oct_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($nov_19[$data->code]>0) echo $nov_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($dec_19[$data->code]>0) echo $dec_19[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? 
  echo $total_19=($jan_19[$data->code]+$feb_19[$data->code]+$mar_19[$data->code]+$apr_19[$data->code]+$may_19[$data->code]+$jun_19[$data->code]
  +$jul_19[$data->code]+$aug_19[$data->code]+$sep_19[$data->code]+$oct_19[$data->code]+$nov_19[$data->code]+$dec_19[$data->code]);
  ?></td>
  <td style="text-align:right"><? if($jan_20[$data->code]>0) echo $jan_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($feb_20[$data->code]>0) echo $feb_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($mar_20[$data->code]>0) echo $mar_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($apr_20[$data->code]>0) echo $apr_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($may_20[$data->code]>0) echo $may_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($jun_20[$data->code]>0) echo $jun_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($jul_20[$data->code]>0) echo $jul_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($aug_20[$data->code]>0) echo $aug_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($sep_20[$data->code]>0) echo $sep_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($oct_20[$data->code]>0) echo $oct_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($nov_20[$data->code]>0) echo $nov_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? if($dec_20[$data->code]>0) echo $dec_20[$data->code]; else echo '';?></td>
  <td style="text-align:right"><? 
  echo $total_20=($jan_20[$data->code]+$feb_20[$data->code]+$mar_20[$data->code]+$apr_20[$data->code]+$may_20[$data->code]+$jun_20[$data->code]
  +$jul_20[$data->code]+$aug_20[$data->code]+$sep_20[$data->code]+$oct_20[$data->code]+$nov_20[$data->code]+$dec_20[$data->code]);
  ?></td>
  <td style="text-align:right<?=$bg?>">&nbsp;</td>
</tr>
<?
}
?>
</tbody></table>
<?
}


// 2004 - Last Year Vs This Year Item Wise Sales Report(With National Target)
elseif($_POST['report']==2004) {

	if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 

$tmonth1 = date("m",strtotime($f_date));
$tmonth2 = date("m",strtotime($t_date));
$month_id_con.=' and month_id between "'.$tmonth1.'" and "'.$tmonth2.'"';
$yy = date("Y",strtotime($f_date));		
		
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		
		//if(isset($dealer_type)) 		{$con.=' and d.dealer_type="'.$dealer_type.'"';}

if($dealer_type!=''){
	if($dealer_type=='MordernTrade'){
		$dtype_con=$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';
	
	} else {$dtype_con=$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';
	}
}

if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}
		
//if(isset($month_id)){$month_id_con.=' and month_id ="'.$month_id.'"';}

$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,i.brand_category,i.brand_category_type,
		i.pack_size pkt
		
		from item_info i 
		where  i.finish_goods_code>0 and i.status="Active" and i.item_brand!="Promotional" and finish_goods_code!=2000 and finish_goods_code!=2001 and finish_goods_code!=2002 and i.item_brand!="Memo" 
		and finish_goods_code not between 9000 and 10000  '.$item_brand_con.$pg_con.$item_con.' 
	 order by i.finish_goods_code';
	 
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and b.BRANCH_ID="'.$region_id.'"';}
 
// This year
$sql2='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i, area ar, branch b, zon z where ar.ZONE_ID=z.ZONE_CODE and d.area_code=ar.AREA_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_code=m.dealer_code and m.id=a.order_no  and i.item_brand!="" and  
	a.unit_price>0 and a.item_id=i.item_id '.$acon.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by  a.item_id';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$this_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// Last year 	
$sql3='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i, area ar, branch b, zon z 
		where ar.ZONE_ID=z.ZONE_CODE and d.area_code=ar.AREA_CODE and z.REGION_ID=b.BRANCH_ID and d.dealer_code=m.dealer_code 
		and m.id=a.order_no  and i.item_brand!="" 
		and a.unit_price>0 and a.item_id=i.item_id '.$acon.$con.$ydate_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by  a.item_id';
	
	$query2 = mysql_query($sql3);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$last_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// target 2020 information	
$sql4='select item_id,sum(target) as target,d_price from sale_target_'.$yy.' where 1'.$month_id_con.' group by item_id';
	
	$query2 = mysql_query($sql4);
	while($data2 = mysql_fetch_object($query2))
	{
	$target[$data2->item_id] = $data2->target;
	$target_itemrate[$data2->item_id] = $data2->d_price;
	}

?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead>
<tr>
<td style="border:0px;" colspan="11"><?=$str?><div class="left"></div>
<div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td>
</tr>

<tr>
<th rowspan="2">S/L-2004</th>
<th rowspan="2">Fg</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Unit</th>
<th rowspan="2">Brand</th>
<th rowspan="2">Category </th>
<th rowspan="2">Sub Brand </th>
<th rowspan="2">Pack Size</th>
<th rowspan="2">GRP</th>
  <th colspan="3" bgcolor="#FFCCFF"><div align="center">Last Year</div></th>
  <th bgcolor="#0099CC">Target </th>
  <th bgcolor="#0099CC">Target</th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Year</div></th>
  <th bgcolor="#FFCCFF">Sales Growth</th>
  <th bgcolor="#0099CC">Target Achive </th>
</tr>
<tr>
  <th>Sale Ctn </th>
  <th>Sale Qty </th>
  <th>Sale Amt </th>
  <th>CTN</th>
  <th bgcolor="#99FF99">TK</th>
  <th bgcolor="#0099CC">Sale Ctn </th>
  <th>Sale Qty </th>
  <th bgcolor="#99FF99">Sale Amt </th>
  <th>in % </th>
  <th bgcolor="#0099CC">in % </th>
</tr>
</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->item_id]>$last_year_sale_qty[$data->item_id])
{
$growth = @((($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]));
//$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]);
//$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;

// target achive percentage

$tgrowth = @((($this_year_sale_qty[$data->item_id]/$data->pkt)/$target[$data->item_id]));
$tgrowth = ($tgrowth*100);

?>
<tr><td><?=++$s?></td>
<td><?=$data->fg?></td>
<td><?=$data->item_name?></td>
<td><?=$data->unit?></td>
<td><?=$data->brand?></td>
<td><?=$data->brand_category?></td>
<td><?=$data->brand_category_type?></td>
<td><?=$data->pkt?></td>
<td><?=$data->sales_item_type?></td>
  <td style="text-align:right"><?=(int)($last_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->item_id],0,'',','); 
  $last_amount_total += $last_year_sale_amt[$data->item_id]; ?></td>
  <td style="text-align:right"><?=number_format($target[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><? $target_amount = ($target[$data->item_id]*$target_itemrate[$data->item_id]*$data->pkt);
  echo number_format($target_amount,0);
  $target_amount_total += $target_amount; ?></td>
  <td style="text-align:right"><?=(int)($this_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->item_id],0,'',',')?></td>
<td style="text-align:right"><?=number_format($this_year_sale_amt[$data->item_id],0,'',',');
$this_amount_total += $this_year_sale_amt[$data->item_id]; ?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($growth)),2)?>%</td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($tgrowth)),2)?>%</td>
</tr>

<? } ?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td bgcolor="#FFCCCC" style="text-align:right"><?=number_format($last_amount_total,0,'',','); ?></td>
  <td style="text-align:right">&nbsp;</td>
  <td bgcolor="#99FF00" style="text-align:right"><?=number_format($target_amount_total,0); ?></td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right"><?=number_format($this_amount_total,0,'',','); ?></td>
  <td bgcolor="#FFCCCC" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total-$last_amount_total)/$last_amount_total)*100,2); ?>%</td>
  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total)/$target_amount_total)*100,2); ?>%</td>
</tr>
</tbody></table>
<?
}


// 2009 - Modern Trade sales with target
elseif($_POST['report']==2009) {

	if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 

$tmonth1 = date("m",strtotime($f_date));
$tmonth2 = date("m",strtotime($t_date));
$month_id_con.=' and month_id between "'.$tmonth1.'" and "'.$tmonth2.'"';
		
		
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		

if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		
$dtype_con=$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';


if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}


// item list
$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,i.brand_category,i.brand_category_type,
		i.pack_size pkt
		from item_info i where  i.finish_goods_code>0 and i.status="Active" and i.item_brand!="Promotional" and finish_goods_code!=2000 and finish_goods_code!=2001 and finish_goods_code!=2002 and i.item_brand!="Memo" 
		and finish_goods_code not between 9000 and 10000  '.$item_brand_con.$pg_con.$item_con.' 
	 order by i.finish_goods_code';
	 
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and b.BRANCH_ID="'.$region_id.'"';}
 
// This year
$sql2='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
from dealer_info d, sale_do_chalan a, item_info i

where
d.dealer_code=a.dealer_code 
and a.unit_price>0 
and a.item_id=i.item_id
'.$acon.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
group by  a.item_id';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$this_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// Last year 	
$sql3='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
from dealer_info d, sale_do_chalan a, item_info i

where
d.dealer_code=a.dealer_code 
and a.unit_price>0 
and a.item_id=i.item_id
'.$acon.$con.$ydate_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
group by  a.item_id';
	
	$query2 = mysql_query($sql3);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$last_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// target 2019 information	
$sql4='select item_id,sum(target) as target,d_price from sale_target_mt_2019 where 1'.$month_id_con.' group by item_id';
	
	$query2 = mysql_query($sql4);
	while($data2 = mysql_fetch_object($query2))
	{
	$target[$data2->item_id] = $data2->target;
	$target_itemrate[$data2->item_id] = $data2->d_price;
	}

?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead>
<tr>
<td style="border:0px;" colspan="11"><?=$str?><div class="left"></div>
<div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td>
</tr>

<tr>
<th rowspan="2">S/L</th>
<th rowspan="2">Fg</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Unit</th>
<th rowspan="2">Brand</th>
<th rowspan="2">Category </th>
<th rowspan="2">Sub Brand </th>
<th rowspan="2">Pack Size</th>
<th rowspan="2">GRP</th>
  <th colspan="3" bgcolor="#FFCCFF"><div align="center">Year 2018</div></th>
  <th bgcolor="#0099CC">Target 19</th>
  <th bgcolor="#0099CC">Target 19</th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Year 2019 </div></th>
  <th bgcolor="#FFCCFF">Sales Growth</th>
  <th bgcolor="#0099CC">Target Achive </th>
</tr>
<tr>
  <th>Sale Ctn </th>
  <th>Sale Qty </th>
  <th>Sale Amt </th>
  <th>CTN</th>
  <th bgcolor="#99FF99">TK</th>
  <th bgcolor="#0099CC">Sale Ctn </th>
  <th>Sale Qty </th>
  <th bgcolor="#99FF99">Sale Amt </th>
  <th>in % </th>
  <th bgcolor="#0099CC">in % </th>
</tr>
</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->item_id]>$last_year_sale_qty[$data->item_id])
{
$growth = @((($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]));
//$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]);
//$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;

// target achive percentage

$tgrowth = @((($this_year_sale_qty[$data->item_id]/$data->pkt)/$target[$data->item_id]));
$tgrowth = ($tgrowth*100);

?>
<tr><td><?=++$s?></td>
<td><?=$data->fg?></td>
<td><?=$data->item_name?></td>
<td><?=$data->unit?></td>
<td><?=$data->brand?></td>
<td><?=$data->brand_category?></td>
<td><?=$data->brand_category_type?></td>
<td><?=$data->pkt?></td>
<td><?=$data->sales_item_type?></td>
  <td style="text-align:right"><?=(int)($last_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->item_id],0,'',','); 
  $last_amount_total += $last_year_sale_amt[$data->item_id]; ?></td>
  <td style="text-align:right"><?=number_format($target[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><? $target_amount = ($target[$data->item_id]*$target_itemrate[$data->item_id]*$data->pkt);
  echo number_format($target_amount,0);
  $target_amount_total += $target_amount; ?></td>
  <td style="text-align:right"><?=(int)($this_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->item_id],0,'',',')?></td>
<td style="text-align:right"><?=number_format($this_year_sale_amt[$data->item_id],0,'',',');
$this_amount_total += $this_year_sale_amt[$data->item_id]; ?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($growth)),2)?>%</td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($tgrowth)),2)?>%</td>
</tr>

<? } ?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td bgcolor="#FFCCCC" style="text-align:right"><?=number_format($last_amount_total,0,'',','); ?></td>
  <td style="text-align:right">&nbsp;</td>
  <td bgcolor="#99FF00" style="text-align:right"><?=number_format($target_amount_total,0); ?></td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right"><?=number_format($this_amount_total,0,'',','); ?></td>
  <td bgcolor="#FFCCCC" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total-$last_amount_total)/$last_amount_total)*100,2); ?>%</td>
  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total)/$target_amount_total)*100,2); ?>%</td>
</tr>
</tbody></table>
<?
} // end 2009


// 2008 Natinal sales vs national target
elseif($_POST['report']==2008) {

	if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 
		
$tmonth1 = date("m",strtotime($f_date));
$tmonth2 = date("m",strtotime($t_date));
$month_id_con.=' and month_id between "'.$tmonth1.'" and "'.$tmonth2.'"';
		
		
		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 


if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}

$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,i.brand_category,i.brand_category_type,
		i.pack_size pkt
		from item_info i 

where i.finish_goods_code>0 and i.status="Active" and i.item_brand!="Promotional" 
and finish_goods_code not in(2000,2001,2002) 
and i.item_brand!="Memo" and finish_goods_code not between 9000 and 10000 
'.$item_brand_con.$pg_con.$item_con.' 
order by i.finish_goods_code';
	 
 
// This year
$sql2='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from sale_do_chalan a, item_info i
where a.item_id = i.item_id
and a.unit_price>0
'.$con.$date_con.$item_con.$item_brand_con.' 
	group by  i.item_id';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$this_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// Last year 	
$sql3='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		from sale_do_chalan a, item_info i

where a.item_id = i.item_id
and a.unit_price>0
'.$con.$ydate_con.$item_con.$item_brand_con.' 
group by i.item_id';
	
	$query2 = mysql_query($sql3);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$last_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// target 2019 information	
$sql4='select item_id,sum(target) as target,d_price from sale_target_2019 where 1'.$month_id_con.' group by item_id';
	
	$query2 = mysql_query($sql4);
	while($data2 = mysql_fetch_object($query2))
	{
	$target[$data2->item_id] = $data2->target;
	$target_itemrate[$data2->item_id] = $data2->d_price;
	}

?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead>
<tr>
<td style="border:0px;" colspan="11"><?=$str?><div class="left"></div>
<div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td>
</tr>

<tr>
<th rowspan="2">S/L</th>
<th rowspan="2">Fg</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Unit</th>
<th rowspan="2">Brand</th>
<th rowspan="2">Category </th>
<th rowspan="2">Sub Brand </th>
<th rowspan="2">Pack Size</th>
<th rowspan="2">GRP</th>
  <th colspan="3" bgcolor="#FFCCFF"><div align="center">Year 2018</div></th>
  <th bgcolor="#0099CC">Target 19</th>
  <th bgcolor="#0099CC">Target 19</th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Year 2019 </div></th>
  <th bgcolor="#FFCCFF">Sales Growth</th>
  <th bgcolor="#0099CC">Target Achive </th>
</tr>
<tr>
  <th>Sale Ctn </th>
  <th>Sale Qty </th>
  <th>Sale Amt </th>
  <th>CTN</th>
  <th bgcolor="#99FF99">TK</th>
  <th bgcolor="#0099CC">Sale Ctn </th>
  <th>Sale Qty </th>
  <th bgcolor="#99FF99">Sale Amt </th>
  <th>in % </th>
  <th bgcolor="#0099CC">in % </th>
</tr>
</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->item_id]>$last_year_sale_qty[$data->item_id])
{
$growth = @((($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]));
//$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]);
//$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;

// target achive percentage

$tgrowth = @((($this_year_sale_qty[$data->item_id]/$data->pkt)/$target[$data->item_id]));
$tgrowth = ($tgrowth*100);

?>
<tr><td><?=++$s?></td>
<td><?=$data->fg?></td>
<td><?=$data->item_name?></td>
<td><?=$data->unit?></td>
<td><?=$data->brand?></td>
<td><?=$data->brand_category?></td>
<td><?=$data->brand_category_type?></td>
<td><?=$data->pkt?></td>
<td><?=$data->sales_item_type?></td>
  <td style="text-align:right"><?=(int)($last_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->item_id],0,'',','); 
  $last_amount_total += $last_year_sale_amt[$data->item_id]; ?></td>
  <td style="text-align:right"><?=number_format($target[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><? $target_amount = ($target[$data->item_id]*$target_itemrate[$data->item_id]*$data->pkt);
  echo number_format($target_amount,0);
  $target_amount_total += $target_amount; ?></td>
  <td style="text-align:right"><?=(int)($this_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->item_id],0,'',',')?></td>
<td style="text-align:right"><?=number_format($this_year_sale_amt[$data->item_id],0,'',',');
$this_amount_total += $this_year_sale_amt[$data->item_id]; ?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($growth)),2)?>%</td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($tgrowth)),2)?>%</td>
</tr>

<? } ?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td bgcolor="#FFCCCC" style="text-align:right"><?=number_format($last_amount_total,0,'',','); ?></td>
  <td style="text-align:right">&nbsp;</td>
  <td bgcolor="#99FF00" style="text-align:right"><?=number_format($target_amount_total,0); ?></td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right"><?=number_format($this_amount_total,0,'',','); ?></td>
  <td bgcolor="#FFCCCC" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total-$last_amount_total)/$last_amount_total)*100,2); ?>%</td>
  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total)/$target_amount_total)*100,2); ?>%</td>
</tr>
</tbody></table>
<?
}


// group wise sales 
elseif($_POST['report']==201) {

$report="Grop wise sales report";

if(isset($t_date)) { $to_date=$t_date;  $fr_date=$f_date; 		
$date_con=' and c.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
}

	 
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$region_con.=' and b.BRANCH_ID="'.$region_id.'"';}
 
// Group A
$sql2='select z.ZONE_CODE as zone_code,z.ZONE_NAME,sum(c.total_amt) as group_amount
from sale_do_chalan c,dealer_info d,area a , zon z, branch b
where d.area_code=a.AREA_CODE 
and c.unit_price>0
and c.dealer_code=d.dealer_code and a.ZONE_ID=z.ZONE_CODE and b.BRANCH_ID=z.REGION_ID 
'.$date_con.$region_con.' and d.dealer_type="Distributor" and d.product_group="A" GROUP BY z.ZONE_CODE';

// and c.total_unit>0 
$query2 = mysql_query($sql2);
while($data2 = mysql_fetch_object($query2)){$group_a[$data2->zone_code] = $data2->group_amount;}

// Group B
$sql2='select z.ZONE_CODE as zone_code,z.ZONE_NAME,sum(c.total_amt) as group_amount
from sale_do_chalan c,dealer_info d,area a , zon z, branch b
where d.area_code=a.AREA_CODE and c.unit_price>0
and c.dealer_code=d.dealer_code and a.ZONE_ID=z.ZONE_CODE and b.BRANCH_ID=z.REGION_ID
'.$date_con.$region_con.' and d.dealer_type="Distributor" and d.product_group="B" GROUP BY z.ZONE_CODE';
$query2 = mysql_query($sql2);
while($data2 = mysql_fetch_object($query2)){$group_b[$data2->zone_code] = $data2->group_amount;}

// Group C
$sql2='select z.ZONE_CODE as zone_code,z.ZONE_NAME,sum(c.total_amt) as group_amount
from sale_do_chalan c,dealer_info d,area a , zon z, branch b
where d.area_code=a.AREA_CODE and c.unit_price>0
and c.dealer_code=d.dealer_code and a.ZONE_ID=z.ZONE_CODE and b.BRANCH_ID=z.REGION_ID
'.$date_con.$region_con.' and d.dealer_type="Distributor" and d.product_group="C" GROUP BY z.ZONE_CODE';
$query2 = mysql_query($sql2);
while($data2 = mysql_fetch_object($query2)){$group_c[$data2->zone_code] = $data2->group_amount;}

// Group D
$sql2='select z.ZONE_CODE as zone_code,z.ZONE_NAME,sum(c.total_amt) as group_amount
from sale_do_chalan c,dealer_info d,area a , zon z, branch b
where d.area_code=a.AREA_CODE and c.unit_price>0
and c.dealer_code=d.dealer_code and a.ZONE_ID=z.ZONE_CODE and b.BRANCH_ID=z.REGION_ID 
'.$date_con.$region_con.' and d.dealer_type="Distributor" and d.product_group="D" GROUP BY z.ZONE_CODE';
$query2 = mysql_query($sql2);
while($data2 = mysql_fetch_object($query2)){$group_d[$data2->zone_code] = $data2->group_amount;}

// Group E
$sql2='select z.ZONE_CODE as zone_code,z.ZONE_NAME,sum(c.total_amt) as group_amount
from sale_do_chalan c,dealer_info d,area a , zon z, branch b
where d.area_code=a.AREA_CODE and c.unit_price>0
and c.dealer_code=d.dealer_code and a.ZONE_ID=z.ZONE_CODE and b.BRANCH_ID=z.REGION_ID
'.$date_con.$region_con.' and d.dealer_type="Distributor" and d.product_group="E" GROUP BY z.ZONE_CODE';
$query2 = mysql_query($sql2);
while($data2 = mysql_fetch_object($query2)){$group_e[$data2->zone_code] = $data2->group_amount;}



$sql='SELECT z.ZONE_CODE as zone_code,b.BRANCH_NAME as region_name,z.ZONE_NAME as zone_name
FROM  branch b,zon z
where b.BRANCH_ID=z.REGION_ID
'.$region_con.'
order by b.BRANCH_NAME,z.ZONE_NAME';
?>
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}
td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 4px;
}
tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead>
<tr>
<td style="border:0px;" colspan="12"><?=$str?><div class="left"></div>
<div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td>
</tr>

<tr>
<th bgcolor="#FFCCCC">S/L</th>
<th bgcolor="#FFCCCC">Region</th>
<th bgcolor="#FFCCCC">Zone</th>
<th bgcolor="#FFCCCC">Tang</th>
<th bgcolor="#FFCCCC">Kolson</th>
<th bgcolor="#FFCCCC">Shezan</th>
<th bgcolor="#FFCCCC">Sajeeb</th>
<th bgcolor="#FFCCCC">Bevarage</th>
<th bgcolor="#FFCCCC">Total</th>
</tr></thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
?>
<tr>
  <td><?=++$s?></td>
  <td><?=$data->region_name?></td>
  <td><?=$data->zone_name?></td>
  <td><span style="text-align:right"><? $gt_a +=$group_a[$data->zone_code]; echo $group_a[$data->zone_code]; ?></span></td>
  <td><span style="text-align:right"><? $gt_b +=$group_b[$data->zone_code]; echo $group_b[$data->zone_code]; ?></span></td>
  <td><span style="text-align:right"><? $gt_c +=$group_c[$data->zone_code]; echo $group_c[$data->zone_code]; ?></span></td>
  <td><span style="text-align:right"><? $gt_d +=$group_d[$data->zone_code]; echo $group_d[$data->zone_code]; ?></span></td>
  <td><span style="text-align:right"><? $gt_e +=$group_e[$data->zone_code]; echo $group_e[$data->zone_code]; ?></span></td>
<? $zone_total= $group_a[$data->zone_code]+$group_b[$data->zone_code]+$group_c[$data->zone_code]+$group_d[$data->zone_code]+$group_e[$data->zone_code];
$grand_total +=$zone_total;
?>  
<td><strong><?=$zone_total;?></strong></td>
</tr>
<? } ?>

<tr>
  <td bgcolor="#FFCCCC">&nbsp;</td>
  <td bgcolor="#FFCCCC">&nbsp;</td>
  <td bgcolor="#FFCCCC"><strong>Total</strong></td>
  <td bgcolor="#FFCCCC"><strong>
    <?=$gt_a;?>
  </strong></td>
  <td bgcolor="#FFCCCC"><strong>
    <?=$gt_b;?>
  </strong></td>
  <td bgcolor="#FFCCCC"><strong>
    <?=$gt_c;?>
  </strong></td>
  <td bgcolor="#FFCCCC"><strong>
    <?=$gt_d;?>
  </strong></td>
  <td bgcolor="#FFCCCC"><strong>
    <?=$gt_e;?>
  </strong></td>
  <td bgcolor="#FFCCCC"><strong>
    <?=$grand_total;?>
  </strong></td>
</tr>
</tbody></table>
<?
} // end gorup wise sales 


// 2006 - Last Year Vs This Year Item Wise Sales Report(With Target)
elseif($_POST['report']==2006) {


$location='';
if(isset($region_id)) 			{$location.=' and region_id="'.$region_id.'"';}
if(isset($zone_id)) 			{$location.=' and zone_id="'.$zone_id.'"';}
if(isset($area_id)) 			{$location.=' and area_id="'.$area_id.'"';}


	if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 

		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		
		//if(isset($dealer_type)) 		{$con.=' and d.dealer_type="'.$dealer_type.'"';}

if($dealer_type!=''){
	if($dealer_type=='MordernTrade'){
		$dtype_con=$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';
	
	} else {$dtype_con=$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';
	}
}

if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}
		
//if(isset($month_id)){$month_id_con.=' and month_id ="'.$month_id.'"';}


	 
if(isset($area_id)) 		{$acon.=' and d.area_code="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and d.zone_id="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and d.region_id="'.$region_id.'"';}
 
// This year
$sql2='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		
		from dealer_info d, sale_do_chalan a, item_info i
		
		where d.dealer_code=a.dealer_code and a.item_id=i.item_id and a.unit_price>0  
	'.$acon.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by a.item_id';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$this_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// Last year 	
$sql3='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		
		from dealer_info d, sale_do_chalan a, item_info i
		
		where d.dealer_code=a.dealer_code and a.item_id=i.item_id and a.unit_price>0 
		'.$acon.$con.$ydate_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by  a.item_id';
	
	$query2 = mysql_query($sql3);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$last_year_sale_qty[$data2->item_id] = $data2->qty;
	}

// target information	
$fmon = date("Ym",strtotime($f_date));
$tmon = date("Ym",strtotime($t_date));

/*if($_POST['zone_id']>0){
$sql4='select item_id,sum(target_ctn) as target from sale_target_upload 
where zone_id="'.$_POST['zone_id'].'" and target_period between "'.$fmon.'" and  "'.$tmon.'" group by item_id';

}elseif($_POST['region_id']>0){
$sql4='select item_id,sum(target_ctn) as target from sale_target_upload 
where region_id="'.$_POST['region_id'].'" and target_period between "'.$fmon.'" and  "'.$tmon.'"  group by item_id';}

else{

$sql4='select item_id,sum(target_ctn) as target from sale_target_upload where target_period between "'.$fmon.'" and  "'.$tmon.'"  group by item_id';
}*/


$sql4='select item_id,sum(target_ctn) as target from sale_target_upload where target_period between "'.$fmon.'" and  "'.$tmon.'"  
'.$location.'
group by item_id';


if($sql4!=''){
	
	$query2 = mysql_query($sql4);
	while($data2 = mysql_fetch_object($query2)){
	$target[$data2->item_id] = $data2->target;
	}}

?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead>
<tr>
<td style="border:0px;" colspan="12"><?=$str?><div class="left"></div>
<div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?>. Note: Sales is coming without Gift Item.</div></td>
</tr>

<tr>
<th rowspan="2">S/L-2006</th>
<th rowspan="2">Fg</th>
<th rowspan="2">Item Name</th>
<th rowspan="2">Unit</th>
<th rowspan="2">Brand</th>
<th rowspan="2">Category </th>
<th rowspan="2">Sub Brand </th>
<th rowspan="2">Pack Size</th>
<th rowspan="2">DP Price </th>
<th rowspan="2">GRP</th>
  <th colspan="3" bgcolor="#FFCCFF"><div align="center">Last Year </div></th>
  <th bgcolor="#0099CC">Target</th>
  <th bgcolor="#0099CC">Target</th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Year</div></th>
  <th bgcolor="#FFCCFF">Sales Growth</th>
  <th bgcolor="#0099CC">Target Achive </th>
  <th colspan="2" bgcolor="#0099CC">ShortFall</th>
  </tr>
<tr>
  <th>Sale Ctn </th>
  <th>Sale Qty </th>
  <th>Sale Amt </th>
  <th>CTN</th>
  <th bgcolor="#99FF99">TK</th>
  <th bgcolor="#0099CC">Sale Ctn </th>
  <th>Sale Qty </th>
  <th bgcolor="#99FF99">Sale Amt </th>
  <th>in % </th>
  <th bgcolor="#0099CC">in %</th>
  <th bgcolor="#0099CC">L/P</th>
  <th bgcolor="#0099CC">T/P</th>
</tr>
</thead><tbody>
<?
$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,i.brand_category,i.brand_category_type,
		i.pack_size pkt,
		i.d_price
		from item_info i where  i.finish_goods_code>0 and i.status="Active" and i.item_brand!="Promotional" 
		and finish_goods_code not between 2000 and 2005 
		and finish_goods_code not between 9000 and 10000  '.$item_brand_con.$pg_con.$item_con.' 
	 order by i.finish_goods_code';
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->item_id]>$last_year_sale_qty[$data->item_id])
{
$growth = @((($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]));
//$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]);
//$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;

// target achive percentage

$tgrowth = @((($this_year_sale_qty[$data->item_id]/$data->pkt)/$target[$data->item_id]));
$tgrowth = ($tgrowth*100);


if($last_year_sale_qty[$data->item_id]<>0 || $this_year_sale_qty[$data->item_id]<>0 || $target[$data->item_id] <>0 ){
?>
<tr><td><?=++$s?></td>
<td><?=$data->fg?></td>
<td><?=$data->item_name?></td>
<td><?=$data->unit?></td>
<td><?=$data->brand?></td>
<td><?=$data->brand_category?></td>
<td><?=$data->brand_category_type?></td>
<td><?=$data->pkt?></td>
<td><?=$data->d_price?></td>
<td><?=$data->sales_item_type?></td>
  <td style="text-align:right"><?=(int)($last_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->item_id],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->item_id],0,'',','); 
  $last_amount_total += $last_year_sale_amt[$data->item_id]; ?></td>
  <td style="text-align:right">
<? echo number_format($target[$data->item_id],0); $gt += $target[$data->item_id]; ?></td>

  <td style="text-align:right"><? $target_amount = ($target[$data->item_id]*$data->d_price*$data->pkt);
  echo number_format($target_amount,0);
  $target_amount_total += $target_amount; ?></td>
  <td style="text-align:right"><?=(int)($this_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->item_id],0,'',',')?></td>
<td style="text-align:right"><?=number_format($this_year_sale_amt[$data->item_id],0,'',',');
$this_amount_total += $this_year_sale_amt[$data->item_id]; ?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100){ echo number_format((($growth)),2)?>%<? } ?></td>
  <td style="text-align:right<?=$bg?>"><? if($tgrowth!=0){ echo number_format((($tgrowth)),2)?>
    %
      <? } ?></td>
  <td style="text-align:right; color:red;"><?=(int)(($this_year_sale_qty[$data->item_id]/$data->pkt)-($last_year_sale_qty[$data->item_id]/$data->pkt));?></td>
  <td style="text-align:right; color:red;"><?=(int)(($this_year_sale_qty[$data->item_id]/$data->pkt)-($target[$data->item_id]));?></td>
</tr>

<? } }?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td bgcolor="#FFCCCC" style="text-align:right"><?=number_format($last_amount_total,0,'',','); ?></td>
  <td style="text-align:right"><?=number_format($gt,2);?></td>
  <td bgcolor="#99FF00" style="text-align:right"><?=number_format($target_amount_total,0); ?></td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right"><?=number_format($this_amount_total,0,'',','); ?></td>
  <td bgcolor="#FFCCCC" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total-$last_amount_total)/$last_amount_total)*100,2); ?>%</td>
  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total)/$target_amount_total)*100,2); ?>
    %</td>
  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>">&nbsp;</td>
  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>">&nbsp;</td>
</tr>
</tbody></table>
<?
}



elseif($_POST['report']==2025) {

$location='';
if(isset($region_id)) 			{$location.=' and region_id="'.$region_id.'"';}
if(isset($zone_id)) 			{$location.=' and zone_id="'.$zone_id.'"';}
if(isset($area_id)) 			{$location.=' and area_id="'.$area_id.'"';}


	if(isset($t_date)) {
		$to_date=$t_date; 
		$fr_date=$f_date; 

		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and a.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and a.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		
		//if(isset($dealer_type)) 		{$con.=' and d.dealer_type="'.$dealer_type.'"';}

if($dealer_type!=''){
	if($dealer_type=='MordernTrade'){
		$dtype_con=$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';
	
	} else {$dtype_con=$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';
	}
}

if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and i.sales_item_type in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and i.sales_item_type in ("C","E") ';}
	else 							{$pg_con = ' and i.sales_item_type="'.$product_group.'"';}
}
		
//if(isset($month_id)){$month_id_con.=' and month_id ="'.$month_id.'"';}

 
if(isset($area_id)) 		{$acon.=' and d.area_code="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and d.zone_id="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and d.region_id="'.$region_id.'"';}
 
// This year
$sql2='select 
		i.item_id,
		i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,		
		sum(a.total_unit*a.unit_price) as sale_price
		
		from dealer_info d, sale_do_details a, item_info i
		
		where d.dealer_code=a.dealer_code and a.item_id=i.item_id and a.unit_price>0  
	'.$acon.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dtype_con.' 
	group by a.item_id';
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->item_id] = $data2->sale_price;
	$this_year_sale_qty[$data2->item_id] = $data2->qty;
	}



// target information	
$fmon = date("Ym",strtotime($f_date));
$tmon = date("Ym",strtotime($t_date));

$sql4='select item_id,sum(target_ctn) as target from sale_target_upload where target_period between "'.$fmon.'" and  "'.$tmon.'"  
'.$location.'
group by item_id';


if($sql4!=''){
	
	$query2 = mysql_query($sql4);
	while($data2 = mysql_fetch_object($query2)){
	$target[$data2->item_id] = $data2->target;
	}}

?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead>
<tr>
<td style="border:0px;" colspan="12"><?=$str?><div class="left"></div>
<div class="right"></div>
<div class="date">Reporting Time: <?=date("h:i A d-m-Y")?>. Note: Sales is coming without Gift Item.</div></td>
</tr>

<tr>
<th rowspan="2">S/L-2025</th>
<th rowspan="2">Fg</th>
<th rowspan="2">Item Name</th>

<th rowspan="2">Brand</th>
<th rowspan="2">Category </th>
<th rowspan="2">Sub Brand </th>

<th rowspan="2">GRP</th>
  <th bgcolor="#0099CC">Target</th>
  <th bgcolor="#0099CC">Target</th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">Order Information </div></th>

  <th bgcolor="#0099CC">Target Achive </th>
  <th bgcolor="#0099CC">ShortFall</th>
  </tr>
<tr>
  <th>CTN</th>
  <th bgcolor="#99FF99">TK</th>
  <th bgcolor="#0099CC">Order Ctn </th>
  <th>Order Qty </th>
  <th bgcolor="#99FF99">Order Amt </th>

  <th bgcolor="#0099CC">in %</th>
  <th bgcolor="#0099CC">Qty</th>
</tr>
</thead><tbody>
<?
$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,
		i.unit_name as unit,
		i.sales_item_type ,
		i.item_brand as brand,i.brand_category,i.brand_category_type,
		i.pack_size pkt,
		i.d_price
		from item_info i where  i.finish_goods_code>0 and i.status="Active" and i.item_brand!="Promotional" 
		and finish_goods_code not between 2000 and 2005 
		and finish_goods_code not between 9000 and 10000  '.$item_brand_con.$pg_con.$item_con.' 
	 order by i.finish_goods_code';
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->item_id]>$last_year_sale_qty[$data->item_id])
{
$growth = @((($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]));
//$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->item_id])/$last_year_sale_qty[$data->item_id]);
//$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;

// target achive percentage

$tgrowth = @((($this_year_sale_qty[$data->item_id]/$data->pkt)/$target[$data->item_id]));
$tgrowth = ($tgrowth*100);


if($last_year_sale_qty[$data->item_id]<>0 || $this_year_sale_qty[$data->item_id]<>0 || $target[$data->item_id] <>0 ){
?>
<tr><td><?=++$s?></td>
<td><?=$data->fg?></td>
<td><?=$data->item_name?></td>

<td><?=$data->brand?></td>
<td><?=$data->brand_category?></td>
<td><?=$data->brand_category_type?></td>

<td><?=$data->sales_item_type?></td>
  <td style="text-align:right">
<? echo number_format($target[$data->item_id],0); $gt += $target[$data->item_id]; ?></td>

  <td style="text-align:right"><? $target_amount = ($target[$data->item_id]*$data->d_price*$data->pkt);
  echo number_format($target_amount,0);
  $target_amount_total += $target_amount; ?></td>
  <td style="text-align:right"><?=(int)($this_year_sale_qty[$data->item_id]/$data->pkt)?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->item_id],0,'',',')?></td>
<td style="text-align:right"><?=number_format($this_year_sale_amt[$data->item_id],0,'',',');
$this_amount_total += $this_year_sale_amt[$data->item_id]; ?></td>

  <td style="text-align:right<?=$bg?>"><? if($tgrowth!=0){ echo number_format((($tgrowth)),2)?>
    %
      <? } ?></td>

  <td style="text-align:right; color:red;"><?=(int)(($this_year_sale_qty[$data->item_id]/$data->pkt)-($target[$data->item_id]));?></td>
</tr>

<? } }?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>

  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>

  <td>&nbsp;</td>

  <td style="text-align:right"><?=number_format($gt,2);?></td>
  <td bgcolor="#99FF00" style="text-align:right"><?=number_format($target_amount_total,0); ?></td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right">&nbsp;</td>
  <td style="text-align:right"><?=number_format($this_amount_total,0,'',','); ?></td>

  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total)/$target_amount_total)*100,2); ?>
    %</td>

  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>">&nbsp;</td>
</tr>
</tbody></table>
<?
} // 2025





// 2007 - party wise target vs sales
elseif($_POST['report']==2007) {

	if(isset($t_date)) { $to_date=$t_date;  $fr_date=$f_date; 

		$yfr_date=(date(('Y'),strtotime($f_date))-1).date(('-m-d'),strtotime($f_date));
		$yto_date=(date(('Y'),strtotime($t_date))-1).date(('-m-d'),strtotime($t_date));
		
		$date_con=' and c.chalan_date between \''.$fr_date.'\' and \''.$to_date.'\'';
		$ydate_con=' and c.chalan_date between \''.$yfr_date.'\' and \''.$yto_date.'\'';
		}
		
		//if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$dealer_con=' and d.dealer_code="'.$dealer_code.'"';} 

if(isset($product_group)) {			
	if($product_group=='ABD')		{$pg_con = ' and d.product_group in ("A","B","D") ';}
	elseif($product_group=='CE') 	{$pg_con = ' and d.product_group in ("C","E") ';}
	else  {$pg_con = ' and d.product_group="'.$product_group.'"';}
}
if(isset($area_id)) 		{$acon.=' and a.AREA_CODE="'.$area_id.'"';}
if(isset($zone_id)) 		{$acon.=' and z.ZONE_CODE="'.$zone_id.'"';}
if(isset($region_id)) 		{$acon.=' and b.BRANCH_ID="'.$region_id.'"';}

// this year	
$sql2='select d.dealer_code as code,sum(c.total_unit) as qty,sum(c.total_amt) as amount
FROM  sale_do_chalan c,dealer_info d, item_info i
where c.dealer_code=d.dealer_code and c.item_id=i.item_id
'.$pg_con.$date_con.$item_brand_con.'
and d.dealer_type="Distributor"
and c.unit_price>0 and i.item_brand!="Promotional"
group by code order by code';
	
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$this_year_sale_amt[$data2->code] = $data2->amount;
	$this_year_sale_qty[$data2->code] = $data2->qty;
	}

// Last year 	
$sql3='select d.dealer_code as code,sum(c.total_unit) as qty,sum(c.total_amt) as amount
FROM  sale_do_chalan c,dealer_info d, item_info i
where c.dealer_code=d.dealer_code and c.item_id=i.item_id
'.$pg_con.$ydate_con.$item_brand_con.'
and d.dealer_type="Distributor"
and c.unit_price>0 and i.item_brand!="Promotional"
group by code order by code';
	
	$query2 = mysql_query($sql3);
	while($data2 = mysql_fetch_object($query2))
	{
	$last_year_sale_amt[$data2->code] = $data2->amount;
	$last_year_sale_qty[$data2->code] = $data2->qty;
	}

// target information	
$fmon = date("Ym",strtotime($f_date));
$tmon = date("Ym",strtotime($t_date));

if($_POST['zone_id']>0){ $sql4='select dealer_code as code,sum(target_ctn) as target,sum(target_amount) as target_amount from sale_target_upload 
where zone_id="'.$_POST['zone_id'].'" and target_period between "'.$fmon.'" and  "'.$tmon.'" group by code';

}elseif($_POST['region_id']>0){ $sql4='select dealer_code as code,sum(target_ctn) as target,sum(target_amount) as target_amount from sale_target_upload 
where region_id="'.$_POST['region_id'].'" and target_period between "'.$fmon.'" and  "'.$tmon.'"  group by code';}

else{ $sql4='select dealer_code as code,sum(target_ctn) as target,sum(target_amount) as target_amount from sale_target_upload 
where target_period between "'.$fmon.'" and  "'.$tmon.'"  group by code';
}

if($sql4!=''){
	$query2 = mysql_query($sql4);
	while($data2 = mysql_fetch_object($query2))
	{ 
	$target[$data2->code] = $data2->target;
	$target_amount[$data2->code] = $data2->target_amount;
	}}

?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<thead><tr><td style="border:0px;" colspan="11"><?=$str?><div class="left"></div>
<div class="right"></div><div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr>
<tr>
<th rowspan="2">S/L-2007</th>
<th rowspan="2">Code</th>
<th rowspan="2">Party </th>
<th rowspan="2">Region</th>
<th rowspan="2">Zone</th>
<th rowspan="2">Area </th>
<th rowspan="2">GRP</th>
<th colspan="1" bgcolor="#FFCCFF"><div align="center">Last Year </div></th>
<th bgcolor="#0099CC">Target</th>
<th colspan="1" bgcolor="#FFFF99"><div align="center">This Year</div></th>
<th bgcolor="#FFCCFF">Sales Growth</th>
<th bgcolor="#0099CC">Target Achive </th>
</tr>
<tr>
  <th>Sale Amt </th>
  <th bgcolor="#99FF99">TK</th>
  <th bgcolor="#99FF99">Sale Amt </th>
  <th>in % </th>
  <th bgcolor="#0099CC">in % </th>
</tr>
</thead><tbody>
<?
$sql='SELECT d.dealer_code as code,d.dealer_name_e as party,d.product_group,a.AREA_NAME as area,z.ZONE_NAME as zone,b.BRANCH_NAME as region
FROM  dealer_info d, area a,zon z, branch b
where d.area_code=a.AREA_CODE and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=b.BRANCH_ID
and d.dealer_type="Distributor" '.$acon.$pg_con.$dealer_con.' order by region,zone,area,d.dealer_code';

$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_amt[$data->code]>$last_year_sale_amt[$data->code])
{ $growth = @((($this_year_sale_amt[$data->code])/$last_year_sale_amt[$data->code]));
//$bg = '; background-color:#99FFFF';
}else{ $growth = @(($this_year_sale_amt[$data->code])/$last_year_sale_amt[$data->code]);
//$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;
// target achive percentage
$tgrowth = @(($this_year_sale_amt[$data->code]/$target_amount[$data->code]));
$tgrowth = ($tgrowth*100);

if($last_year_sale_qty[$data->code]<>0 || $this_year_sale_qty[$data->code]<>0 || $target[$data->code] <>0 ){
?>
<tr><td><?=++$s?></td>
<td><?=$data->code?></td>
<td><?=$data->party?></td>
<td><?=$data->region?></td>
<td><?=$data->zone?></td>
<td><?=$data->area?></td>
<td><?=$data->product_group?></td>

  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->code],0,'',','); 
  $last_amount_total += $last_year_sale_amt[$data->code]; ?></td>
  <td style="text-align:right"><?=number_format($target_amount[$data->code],0,'',',');
  $target_amount_total += $target_amount[$data->code]; ?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_amt[$data->code],0,'',',');
$this_amount_total += $this_year_sale_amt[$data->code]; ?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100){ echo number_format((($growth)),2)?>%<? } ?></td>
  <td style="text-align:right<?=$bg?>"><? if($tgrowth!=0){ echo number_format((($tgrowth)),2)?>%<? } ?></td>
</tr>
<? } }?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td bgcolor="#FFCCCC" style="text-align:right"><?=number_format($last_amount_total,0,'',','); ?></td>
  <td bgcolor="#99FF00" style="text-align:right"><?=number_format($target_amount_total,0); ?></td>
  <td style="text-align:right"><?=number_format($this_amount_total,0,'',','); ?></td>
  <td bgcolor="#FFCCCC" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total-$last_amount_total)/$last_amount_total)*100,2); ?>%</td>
  <td bgcolor="#99FF00" style="text-align:right<?=$bg?>"><?=number_format((($this_amount_total)/$target_amount_total)*100,2); ?>
    %</td>
</tr>
</tbody></table>
<?
} // end 2007




elseif($_POST['report']==2003){
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead><tr><td style="border:0px;" colspan="9"><?=$str?><div class="left"></div><div class="right"></div><div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr>

<tr><th rowspan="2">S/L</th>
<th rowspan="2" bgcolor="#0000FF">Code</th>
<th rowspan="2">Dealer Name</th>
<th rowspan="2">Area</th>
<th rowspan="2">Zone</th>
<th rowspan="2">Region</th><th colspan="3" bgcolor="#FFCCFF"><div align="center">Last Year </div></th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Year </div></th>
<th>Growth</th>
</tr>
<tr>
  <th>Sale Ctn</th>
  <th>Sale Total Pcs</th>
  <th>Sale Amt </th>
  <th>Sale Ctn </th>
  <th>Sale Total Pcs</th>
  <th>Sale Amt </th>
  <th>in pcs% </th>
</tr>

</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->dealer_code]>$last_year_sale_qty[$data->dealer_code])
{
$growth = @((($this_year_sale_qty[$data->dealer_code])/$last_year_sale_qty[$data->dealer_code]));
$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->dealer_code])/$last_year_sale_qty[$data->dealer_code]);
$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;
$ytotal_sale_pkt = $ytotal_sale_pkt + (int)($last_year_sale_pkt[$data->dealer_code]);
$ytotal_sale_amt = $ytotal_sale_amt + (int)($last_year_sale_amt[$data->dealer_code]);
$ytotal_sale_qty = $ytotal_sale_qty + (int)($last_year_sale_qty[$data->dealer_code]);
$total_sale_pkt = $total_sale_pkt + (int)($this_year_sale_pkt[$data->dealer_code]);
$total_sale_amt = $total_sale_amt + (int)($this_year_sale_amt[$data->dealer_code]);
$total_sale_qty = $total_sale_qty + (int)($this_year_sale_qty[$data->dealer_code]);
?>
<tr><td><?=++$s?></td><td><?=$data->dealer_code?></td><td><?=$data->dealer_name?></td>
  <td><?=$data->area_name?></td>
  <td><?=$data->zone_name?></td>
  <td><?=$data->region_name?></td><td style="text-align:right"><?=(int)@($last_year_sale_pkt[$data->dealer_code])?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->dealer_code],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->dealer_code],0,'',',')?></td>
  <td style="text-align:right"><?=(int)@($this_year_sale_pkt[$data->dealer_code])?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->dealer_code],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_amt[$data->dealer_code],0,'',',')?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($growth)),2)?></td>
</tr>
<?
}
?>
<tr><td colspan="6">&nbsp;</td>
  <td style="text-align:right"><?=(int)($ytotal_sale_pkt)?></td>
  <td style="text-align:right"><?=(int)($ytotal_sale_qty)?></td>
  <td style="text-align:right"><?=(int)($ytotal_sale_amt)?></td>
  <td style="text-align:right"><?=(int)($total_sale_pkt)?></td>
  <td style="text-align:right"><?=(int)($total_sale_qty)?></td>
  <td style="text-align:right"><?=(int)($total_sale_amt)?></td><td>&nbsp;</td>
</tr>
</tbody></table>
<?
}

elseif($_POST['report']==20031) 
{
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
    <tr><td style="border:0px;" colspan="6"><?=$str?><div class="left"></div><div class="right"></div><div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr>
    <tr><th rowspan="2">S/L</th>
    <th rowspan="2">Code</th>
    <th rowspan="2">Region</th><th colspan="3" bgcolor="#FFCCFF"><div align="center">Last Year </div></th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Year </div></th>
<th>Growth</th>
</tr>
<tr>
  <th>Sale Ctn</th>
  <th>Sale Total Pcs</th>
  <th>Sale Amt </th>
  <th>Sale Ctn </th>
  <th>Sale Total Pcs</th>
  <th>Sale Amt </th>
  <th>in pcs%</th>
</tr>

</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->BRANCH_ID]>$last_year_sale_qty[$data->BRANCH_ID])
{
$growth = @((($this_year_sale_qty[$data->BRANCH_ID])/$last_year_sale_qty[$data->BRANCH_ID]));
$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->BRANCH_ID])/$last_year_sale_qty[$data->BRANCH_ID]);
$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;
$ytotal_sale_pkt = $ytotal_sale_pkt + (int)($last_year_sale_pkt[$data->BRANCH_ID]);
$ytotal_sale_amt = $ytotal_sale_amt + (int)($last_year_sale_amt[$data->BRANCH_ID]);
$ytotal_sale_qty = $ytotal_sale_qty + (int)($last_year_sale_qty[$data->BRANCH_ID]);
$total_sale_pkt = $total_sale_pkt + (int)($this_year_sale_pkt[$data->BRANCH_ID]);
$total_sale_amt = $total_sale_amt + (int)($this_year_sale_amt[$data->BRANCH_ID]);
$total_sale_qty = $total_sale_qty + (int)($this_year_sale_qty[$data->BRANCH_ID]);
?>
<tr><td><?=++$s?></td><td><?=$data->BRANCH_ID?></td><td><?=$data->region_name?></td><td style="text-align:right"><?=(int)@($last_year_sale_pkt[$data->BRANCH_ID])?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->BRANCH_ID],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->BRANCH_ID],0,'',',')?></td>
  <td style="text-align:right"><?=(int)@($this_year_sale_pkt[$data->BRANCH_ID])?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->BRANCH_ID],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_amt[$data->BRANCH_ID],0,'',',')?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($growth)),2)?></td>
</tr>
<?
}
?>
<tr><td colspan="3" bgcolor="#EAEAEA">&nbsp;</td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($ytotal_sale_pkt)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($ytotal_sale_qty)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($ytotal_sale_amt)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($total_sale_pkt)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($total_sale_qty)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($total_sale_amt)?>
  </strong></td><td bgcolor="#EAEAEA" style="text-align:right; font-weight:bold"><?=@number_format((((-1)*(($ytotal_sale_qty-$total_sale_qty)/$ytotal_sale_qty))*100),2);?></td>
</tr>
</tbody></table>
<?
}











elseif($_POST['report']==20032) 
{
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
    <tr><td style="border:0px;" colspan="6"><?=$str?><div class="left"></div><div class="right"></div><div class="date">Reporting Time: <?=date("h:i A d-m-Y")?></div></td></tr>
    <tr><th rowspan="2">S/L</th>
    <th rowspan="2">Region</th>
    <th rowspan="2">Zone</th>
    <th colspan="3" bgcolor="#FFCCFF"><div align="center">Last Year </div></th>
  <th colspan="3" bgcolor="#FFFF99"><div align="center">This Year </div></th>
<th>Growth</th>
</tr>
<tr>
  <th>Sale Ctn</th>
  <th>Sale Total Pcs</th>
  <th>Sale Amt </th>
  <th>Sale Ctn </th>
  <th>Sale Total Pcs</th>
  <th>Sale Amt </th>
  <th>in % </th>
</tr>

</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){

if($this_year_sale_qty[$data->ZONE_CODE]>$last_year_sale_qty[$data->ZONE_CODE])
{
$growth = @((($this_year_sale_qty[$data->ZONE_CODE])/$last_year_sale_qty[$data->ZONE_CODE]));


$bg = '; background-color:#99FFFF';
}else
{
$growth = @(($this_year_sale_qty[$data->ZONE_CODE])/$last_year_sale_qty[$data->ZONE_CODE]);
$bg = '; background-color:#FFCCCC';
}
$growth = ($growth*100)-100;
$ytotal_sale_pkt = $ytotal_sale_pkt + (int)($last_year_sale_pkt[$data->ZONE_CODE]);
$ytotal_sale_amt = $ytotal_sale_amt + (int)($last_year_sale_amt[$data->ZONE_CODE]);
$ytotal_sale_qty = $ytotal_sale_qty + (int)($last_year_sale_qty[$data->ZONE_CODE]);
$total_sale_pkt = $total_sale_pkt + (int)($this_year_sale_pkt[$data->ZONE_CODE]);
$total_sale_amt = $total_sale_amt + (int)($this_year_sale_amt[$data->ZONE_CODE]);
$total_sale_qty = $total_sale_qty + (int)($this_year_sale_qty[$data->ZONE_CODE]);
?>
<tr><td><?=++$s?></td>
<td><?=$data->region_name?></td>
<td><?=$data->zone_name?></td>
<td style="text-align:right"><?=(int)@($last_year_sale_pkt[$data->ZONE_CODE])?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_qty[$data->ZONE_CODE],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($last_year_sale_amt[$data->ZONE_CODE],0,'',',')?></td>
  <td style="text-align:right"><?=(int)@($this_year_sale_pkt[$data->ZONE_CODE])?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_qty[$data->ZONE_CODE],0,'',',')?></td>
  <td style="text-align:right"><?=number_format($this_year_sale_amt[$data->ZONE_CODE],0,'',',')?></td>
  <td style="text-align:right<?=$bg?>"><? if($growth!=-100) echo number_format((($growth)),2)?></td>
</tr>
<?
}
?>
<tr><td colspan="3" bgcolor="#EAEAEA">&nbsp;</td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($ytotal_sale_pkt)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($ytotal_sale_qty)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($ytotal_sale_amt)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($total_sale_pkt)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($total_sale_qty)?>
  </strong></td>
  <td bgcolor="#EAEAEA" style="text-align:right"><strong>
    <?=(int)($total_sale_amt)?>
  </strong></td><td bgcolor="#EAEAEA" style="text-align:right; font-weight:bold"><?=@number_format((((-1)*(($ytotal_sale_qty-$total_sale_qty)/$ytotal_sale_qty))*100),2);?></td>
</tr>
</tbody></table>
<?
}
elseif($_REQUEST['report']==104) 
{
echo $str;
 if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

$f_mons0 = date('Y-m-01',$t_stamp);
$f_mone0 = date('Y-m-'.date('t',$t_stamp),$t_stamp);

$f_mons1 = date('Y-'.($t_array[1]-1).'-01',$t_stamp);
$f_mone1 = date('Y-'.($t_array[1]-1).'-'.date('t',strtotime($f_mons1)),strtotime($f_mons1));

$f_mons2 = date('Y-'.($t_array[1]-2).'-01',$t_stamp);
$f_mone2 = date('Y-'.($t_array[1]-2).'-'.date('t',strtotime($f_mons2)),strtotime($f_mons2));

$f_mons3 = date('Y-'.($t_array[1]-3).'-01',$t_stamp);
$f_mone3 = date('Y-'.($t_array[1]-3).'-'.date('t',strtotime($f_mons3)),strtotime($f_mons3));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">ZONE NAME </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons3))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons2))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons1))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('jS-M\'y',strtotime($t_date))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons0))?>
      (Apx.)</span></td>
    <td bgcolor="#333333"><span class="style3">Growth</span></td>
  </tr>
 <?
 


$sql = "select * from zon where REGION_ID='".$region_id."' order by ZONE_NAME";

	$query = mysql_query($sql);
	while($item=mysql_fetch_object($query)){
 $zone_code = $item->ZONE_CODE;
$sqlmon0 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons0."' and '".$t_date."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon1 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons1."' and '".$f_mone1."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon2 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons2."' and '".$f_mone2."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon3 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_mons3."' and '".$f_mone3."' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_info->item_id.$pg_con));



$sqlmon = ((($sqlmon0[0])*date('t'))/$t_array[2]);
$diff = ($sqlmon-$sqlmon1[0]);

 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$i?></td>

	    <td><a href="master_report.php?submit=105&report=106&item_id=<?=$_REQUEST['item_id']?>&zone_id=<?=$zone_code?>&t_date=<?=$_REQUEST['t_date']?>" target="_blank" style="text-decoration:none"><?=find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_code)?></a></td>
    <td bgcolor="#99CCFF"><?=number_format($sqlmon3[0],2);?></td>
    <td bgcolor="#66CC99"><?=number_format($sqlmon2[0],2);?></td>
    <td bgcolor="#FFFF99"><?=number_format($sqlmon1[0],2);?></td>
    <td><?=number_format($sqlmon0[0],2);?></td>
    <td><?=number_format($sqlmon,2);?></td>
    <td style="color:<?=($diff>0)?'#009900;':'#FF0000;'?>"><?=number_format($diff,2);?></td>
  </tr>
  <? }?>
</table>
<?

}

elseif($_REQUEST['report']==105) 
{
echo $str;
 if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

$f_mons0 = date('Y-m-01',$t_stamp);
$f_mone0 = date('Y-m-'.date('t',$t_stamp),$t_stamp);

$f_mons1 = date('Y-'.($t_array[1]-1).'-01',$t_stamp);
$f_mone1 = date('Y-'.($t_array[1]-1).'-'.date('t',strtotime($f_mons1)),strtotime($f_mons1));

$f_mons2 = date('Y-'.($t_array[1]-2).'-01',$t_stamp);
$f_mone2 = date('Y-'.($t_array[1]-2).'-'.date('t',strtotime($f_mons2)),strtotime($f_mons2));

$f_mons3 = date('Y-'.($t_array[1]-3).'-01',$t_stamp);
$f_mone3 = date('Y-'.($t_array[1]-3).'-'.date('t',strtotime($f_mons3)),strtotime($f_mons3));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">DEALER NAME </span></td>
    <td bgcolor="#333333"><span class="style3">AREA NAME </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons3))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons2))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons1))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('jS-M\'y',strtotime($t_date))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons0))?>
      (Apx.)</span></td>
    <td bgcolor="#333333"><span class="style3">Growth</span></td>
  </tr>
 <?
 


$sql = "select d.dealer_code, d.dealer_name_e, a.area_name from dealer_info d, area a where d.area_code=a.AREA_CODE and ZONE_ID='".$zone_id."' order by d.dealer_name_e";

	$query = mysql_query($sql);
	while($item=mysql_fetch_object($query)){
 $zone_code = $item->ZONE_CODE;
$sqlmon0 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".$f_mons0."' and '".$t_date."' and c.dealer_code=d.dealer_code and d.dealer_code='".$item->dealer_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon1 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".$f_mons1."' and '".$f_mone1."' and c.dealer_code=d.dealer_code and d.dealer_code='".$item->dealer_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon2 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".$f_mons2."' and '".$f_mone2."' and c.dealer_code=d.dealer_code and d.dealer_code='".$item->dealer_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon3 = mysql_fetch_row(mysql_query("select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".$f_mons3."' and '".$f_mone3."' and c.dealer_code=d.dealer_code and d.dealer_code='".$item->dealer_code."' and c.item_id=".$item_info->item_id.$pg_con));



$sqlmon = ((($sqlmon0[0])*date('t'))/$t_array[2]);
$diff = ($sqlmon-$sqlmon1[0]);

 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$i?></td>
    <td><?=$item->dealer_name_e?></td>
    <td><?=$item->area_name?></td>
    <td bgcolor="#99CCFF"><?=number_format($sqlmon3[0],2);?></td>
    <td bgcolor="#66CC99"><?=number_format($sqlmon2[0],2);?></td>
    <td bgcolor="#FFFF99"><?=number_format($sqlmon1[0],2);?></td>
    <td><?=number_format($sqlmon0[0],2);?></td>
    <td><?=number_format($sqlmon,2);?></td>
    <td style="color:<?=($diff>0)?'#009900;':'#FF0000;'?>"><?=number_format($diff,2);?></td>
  </tr>
  <? }?>
</table>
<?

}

elseif($_REQUEST['report']==106) 
{
echo $str;
 if($t_date=='') $t_date = date('Y-m-d');
$t_array = explode('-',$t_date);
$t_stamp = strtotime($t_date);

$f_mons0 = date('Y-m-01',$t_stamp);
$f_mone0 = date('Y-m-'.date('t',$t_stamp),$t_stamp);

$f_mons1 = date('Y-'.($t_array[1]-1).'-01',$t_stamp);
$f_mone1 = date('Y-'.($t_array[1]-1).'-'.date('t',strtotime($f_mons1)),strtotime($f_mons1));

$f_mons2 = date('Y-'.($t_array[1]-2).'-01',$t_stamp);
$f_mone2 = date('Y-'.($t_array[1]-2).'-'.date('t',strtotime($f_mons2)),strtotime($f_mons2));

$f_mons3 = date('Y-'.($t_array[1]-3).'-01',$t_stamp);
$f_mone3 = date('Y-'.($t_array[1]-3).'-'.date('t',strtotime($f_mons3)),strtotime($f_mons3));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">DEALER NAME </span></td>
    <td bgcolor="#333333"><span class="style3">AREA NAME </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons3))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons2))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons1))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('jS-M\'y',strtotime($t_date))?>
    </span></td>
    <td bgcolor="#333333"><span class="style3">
      <?=date('M\'y',strtotime($f_mons0))?>
      (Apx.)</span></td>
    <td bgcolor="#333333"><span class="style3">Growth</span></td>
  </tr>
 <?
 


$sql = "select d.dealer_code, d.dealer_name_e, a.area_name from dealer_info d, area a where d.area_code=a.AREA_CODE and ZONE_ID='".$zone_id."' order by d.dealer_name_e";

$query = mysql_query($sql);
while($item=mysql_fetch_object($query)){

$zone_code = $item->ZONE_CODE;
$sqlmon0 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".$f_mons0."' and '".$t_date."' and c.dealer_code=d.dealer_code and d.dealer_code='".$item->dealer_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon1 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".$f_mons1."' and '".$f_mone1."' and c.dealer_code=d.dealer_code and d.dealer_code='".$item->dealer_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon2 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".$f_mons2."' and '".$f_mone2."' and c.dealer_code=d.dealer_code and d.dealer_code='".$item->dealer_code."' and c.item_id=".$item_info->item_id.$pg_con));

$sqlmon3 = mysql_fetch_row(mysql_query("select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".$f_mons3."' and '".$f_mone3."' and c.dealer_code=d.dealer_code and d.dealer_code='".$item->dealer_code."' and c.item_id=".$item_info->item_id.$pg_con));



$sqlmon = (int)((($sqlmon0[0])*date('t'))/$t_array[2]);
$diff = ($sqlmon-$sqlmon1[0]);

 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$i?></td>
    <td><?=$item->dealer_name_e?></td>
    <td><?=$item->area_name?></td>
    <td bgcolor="#99CCFF"><?=number_format($sqlmon3[0],2);?></td>
    <td bgcolor="#66CC99"><?=number_format($sqlmon2[0],2);?></td>
    <td bgcolor="#FFFF99"><?=number_format($sqlmon1[0],2);?></td>
    <td><?=number_format($sqlmon0[0],2);?></td>
    <td><?=number_format($sqlmon,2);?></td>
    <td style="color:<?=($diff>0)?'#009900;':'#FF0000;'?>"><?=number_format($diff,2);?></td>
  </tr>
  <? }?>
</table>
<?
}
elseif($_REQUEST['report']==107) 
{
echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">REGION NAME </span></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#333333"><div align="center"><span class="style3"><?=date('M\'y',strtotime(${'f_mos'.$i}))?></span></div></td>
<?
}
?>
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
 <?
 


$sql = "select BRANCH_ID,BRANCH_NAME from branch  order by BRANCH_NAME";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$BRANCH_ID = $item->BRANCH_ID;
for($i=12;$i>0;$i--)
{
$m = ($i-1);

$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d,area a, zon z where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.area_code=a.AREA_CODE and z.ZONE_CODE=a.ZONE_ID and z.REGION_ID='".$BRANCH_ID."'".$con;



${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$BRANCH_ID} = ${'totalr'.$BRANCH_ID} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->BRANCH_NAME?></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmon'.$i}[0],2);?></div></td>
<?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
        <? $totalallr= $totalallr + ${'totalr'.$BRANCH_ID};echo number_format(${'totalr'.$BRANCH_ID},2)?>
    </strong></div></td>
  </tr>
  <? }

for($i=12;$i>0;$i--)
{
$m = ($i-1);


$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Corporate'".$con;

${'sqlmonc'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totalco'.$i} = ${'totalco'.$i} + ${'sqlmonc'.$i}[0];
${'totalrc1'} = ${'totalrc1'} + ${'sqlmonc'.$i}[0];
}

  ?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
      <td>&nbsp;</td>
      <td><strong>D Total</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format(${'totalc'.$i},2);?></div></td>
<?
}
?>

      <td bgcolor="#FFFF99"><div align="right">
        <?=number_format($totalallr,2);?>
      </div></td>
    </tr>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td>Corporate</td>
	
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmonc'.$i}[0],2);?></div></td>
<?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
        <?=number_format($totalrc1,2)?>
    </strong></div></td>
    </tr>
	<?
	  for($i=12;$i>0;$i--)
{
$m = ($i-1);


$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='SuperShop'".$con;
${'sqlmons'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totals'.$i} = ${'totals'.$i} + ${'sqlmons'.$i}[0];
${'totalrc'} = ${'totalrc'} + ${'sqlmons'.$i}[0];
}
	?>
	
<tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
<td><?=++$j?></td>
<td>SuperShop</td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmons'.$i}[0],2);?></div></td>
<?
}
?>
<td bgcolor="#FFFF99"><div align="right"><strong>
  <?=number_format($totalrc,2)?>
</strong></div></td>
</tr>
<tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
  <td>&nbsp;</td>
  <td><strong>Corporate+SuperShop</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totalall'} = ${'totalall'} + (${'totals'.$i}+${'totalco'.$i});
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format((${'totals'.$i}+${'totalco'.$i}),2)?></div></td>

<?
}
?><td bgcolor="#FFFF99"><div align="right"><strong><?=number_format(${'totalall'},2)?></strong></div></td>
  </tr>
<tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
<td>&nbsp;</td>
<td><strong>N Total</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totalallall'} = ${'totalallall'} + (${'totalc'.$i}+${'totals'.$i}+${'totalco'.$i});
?>
<td bgcolor="#FF9999"><div align="right"><?=number_format((${'totalc'.$i}+${'totals'.$i}+${'totalco'.$i}),2)?></div></td>
<?
}
?>
<td bgcolor="#FF3333"><div align="right"><strong>
  <?=number_format(${'totalallall'},2)?>
</strong></div></td>
</tr>
</table>
<?
}
elseif($_REQUEST['report']==108) 
{
echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">REGION NAME </span></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#333333"><div align="center"><span class="style3"><?=date('M\'y',strtotime(${'f_mons'.$i}))?></span></div></td>
<?
}
?>
   
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
 <?
 


$sql = "select BRANCH_ID,BRANCH_NAME from branch  order by BRANCH_NAME";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
if(isset($item_id)) 		{$con=' and c.item_id="'.$item_id.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$BRANCH_ID = $item->BRANCH_ID;
for($i=12;$i>0;$i--)
{
$m = ($i-1);
$sqql = "select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d,area a, zon z where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.area_code=a.AREA_CODE and z.ZONE_CODE=a.ZONE_ID and z.REGION_ID='".$BRANCH_ID."'".$con;
${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$BRANCH_ID} = ${'totalr'.$BRANCH_ID} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->BRANCH_NAME?></td>
	
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmon'.$i}[0],0);?></div></td>
<?
}
?>
	
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <?=number_format(${'totalr'.$BRANCH_ID},0)?>
    </strong></div></td>
  </tr>
  <? }
  
  for($i=12;$i>0;$i--)
{
$m = ($i-1);
$sqql = "select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Corporate'".$con;
${'sqlmonc'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totalco'.$i} = ${'totalco'.$i} + ${'sqlmonc'.$i}[0];
${'totalrc1'} = ${'totalrc1'} + ${'sqlmonc'.$i}[0];
}

  ?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
      <td>&nbsp;</td>
      <td><strong>D Total</strong></td>
	  
<?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format(${'totalc'.$i},0);?></div></td>
<?
}
?>

	  
      <td bgcolor="#FFFF99"><div align="right"><?=number_format(${'totald'},0);?></div></td>
    </tr>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td>Corporate</td>
	
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmonc'.$i}[0],0);?></div></td>
<?
}
?>
	
    
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <?=number_format($totalrc1,0)?>
    </strong></div></td>
    </tr>
	<?
	  for($i=12;$i>0;$i--)
{
$m = ($i-1);
$sqql = "select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='SuperShop'".$con;;
${'sqlmons'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totals'.$i} = ${'totals'.$i} + ${'sqlmons'.$i}[0];
${'totalrc'} = ${'totalrc'} + ${'sqlmons'.$i}[0];
}
	?>
	
<tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
<td><?=++$j?></td>
<td>SuperShop</td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmons'.$i}[0],0);?></div></td>
<?
}
?>

<td bgcolor="#FFFF99"><div align="right"><strong><?=number_format($totalrc,0)?></strong></div></td>
</tr>
<tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
  <td>&nbsp;</td>
  <td><strong>Corporate+SuperShop</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totalall'} = ${'totalall'} + (${'totals'.$i}+${'totalco'.$i});
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format((${'totals'.$i}+${'totalco'.$i}),0)?></div></td>
<?
}
?>
<td bgcolor="#FFFF99"><div align="right"><strong><?=number_format(${'totalall'},0)?></strong></div></td>
</tr>
<tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
<td>&nbsp;</td>
<td><strong>N Total</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totalallall'} = ${'totalallall'} + (${'totalc'.$i}+${'totals'.$i}+${'totalco'.$i});
?>
<td bgcolor="#FF9999"><div align="right"><?=number_format((${'totalc'.$i}+${'totals'.$i}+${'totalco'.$i}),0)?></div></td>
<?
}
?>

<td bgcolor="#FF3333"><div align="right"><strong><?=number_format(${'totalallall'},0)?></strong></div></td>
</tr>
</table>
<?
}
elseif($_REQUEST['report']==109) 
{
echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">REGION NAME </span></td>
    <?
for($i=12;$i>0;$i--)
{
?>
    <td bgcolor="#333333"><div align="center"><span class="style3">
      <?=date('M\'y',strtotime(${'f_mons'.$i}))?>
    </span></div></td>
    <?
}
?>
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
  <?
 


$sql = "select BRANCH_ID,BRANCH_NAME from branch  order by BRANCH_NAME";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
if(isset($item_id)) 		{$con=' and c.item_id="'.$item_id.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$BRANCH_ID = $item->BRANCH_ID;
for($i=12;$i>0;$i--)
{
$m = ($i-1);

$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d,area a, zon z where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.area_code=a.AREA_CODE and z.ZONE_CODE=a.ZONE_ID and z.REGION_ID='".$BRANCH_ID."'".$con;



${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$BRANCH_ID} = ${'totalr'.$BRANCH_ID} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->BRANCH_NAME?></td>
    <?
for($i=12;$i>0;$i--)
{
?>
    <td bgcolor="#99CCFF"><div align="right">
      <?=number_format(${'sqlmon'.$i}[0],2);?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <? 
$totalallr= $totalallr + ${'totalr'.$BRANCH_ID};
echo number_format(${'totalr'.$BRANCH_ID},2);
	  ?>
    </strong></div></td>
  </tr>
  <? }

for($i=12;$i>0;$i--)
{
$m = ($i-1);


$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Corporate'".$con;

${'sqlmonc'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totalco'.$i} = ${'totalco'.$i} + ${'sqlmonc'.$i}[0];
${'totalrc1'} = ${'totalrc1'} + ${'sqlmonc'.$i}[0];
}

  ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td>&nbsp;</td>
    <td><strong>D Total</strong></td>
    <?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
    <td bgcolor="#FFFF66"><div align="right">
      <?=number_format(${'totalc'.$i},2);?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right">
      <?=number_format($totalallr,2);?>
    </div></td>
  </tr>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td>Corporate</td>
    <?
for($i=12;$i>0;$i--)
{
?>
    <td bgcolor="#99CCFF"><div align="right">
      <?=number_format(${'sqlmonc'.$i}[0],2);?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <?=number_format($totalrc1,2)?>
    </strong></div></td>
  </tr>
  <?
	  for($i=12;$i>0;$i--)
{
$m = ($i-1);


$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='SuperShop'".$con;
${'sqlmons'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totals'.$i} = ${'totals'.$i} + ${'sqlmons'.$i}[0];
${'totalrc'} = ${'totalrc'} + ${'sqlmons'.$i}[0];
}
	?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td>SuperShop</td>
    <?
for($i=12;$i>0;$i--)
{
?>
    <td bgcolor="#99CCFF"><div align="right">
      <?=number_format(${'sqlmons'.$i}[0],2);?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <?=number_format($totalrc,2)?>
    </strong></div></td>
  </tr>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td>&nbsp;</td>
    <td><strong>Corporate+SuperShop</strong></td>
    <?
for($i=12;$i>0;$i--)
{
${'totalall'} = ${'totalall'} + (${'totals'.$i}+${'totalco'.$i});
?>
    <td bgcolor="#FFFF66"><div align="right">
      <?=number_format((${'totals'.$i}+${'totalco'.$i}),2)?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <?=number_format(${'totalall'},2)?>
    </strong></div></td>
  </tr>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td>&nbsp;</td>
    <td><strong>N Total</strong></td>
    <?
for($i=12;$i>0;$i--)
{
${'totalallall'} = ${'totalallall'} + (${'totalc'.$i}+${'totals'.$i}+${'totalco'.$i});
?>
    <td bgcolor="#FF9999"><div align="right">
      <?=number_format((${'totalc'.$i}+${'totals'.$i}+${'totalco'.$i}),2)?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FF3333"><div align="right"><strong>
      <?=number_format(${'totalallall'},2)?>
    </strong></div></td>
  </tr>
</table>
<?
}

elseif($_POST['report']==2001) 
{

if(isset($t_date)) {$to_date=$t_date; $fr_date=$f_date; $date_con .= ' and a.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}
		if($_POST['cut_date']!='') 								$date_con .= ' and  a.chalan_date<="'.$_POST['cut_date'].'"'; 
		if(isset($depot_id)) 			{$con.=' and d.depot="'.$depot_id.'"';}
		if(isset($dealer_code)) 		{$con.=' and d.dealer_code="'.$dealer_code.'"';} 
		if(isset($dealer_type)){
		if($dealer_type=='MordernTrade')		{$dealer_type_con = ' and ( d.dealer_type="Corporate" or  d.dealer_type="SuperShop" or  d.product_group="M") ';}
		else 									{$dealer_type_con = ' and d.dealer_type="'.$dealer_type.'"';}
		}
		
// first display info
$sql='select 
		i.finish_goods_code as fg,
		i.item_id,
		i.item_name,i.sales_item_type,
		i.unit_name as unit,
		i.item_brand as brand,
		i.pack_size,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,
		sum(a.total_unit*i.d_price) as DP,
		sum(a.total_unit*a.unit_price)/sum(a.total_unit) as sale_price,
		sum((a.total_unit*i.d_price)-(a.total_amt)) as discount,
		
		sum(a.total_unit*a.unit_price) as actual_price
		
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i 
		where d.dealer_code=m.dealer_code and m.id=a.order_no  and i.item_brand!="" and  
	a.unit_price>0  and a.item_id=i.item_id '.$date_con.$warehouse_con.$item_con.$item_brand_con.$dealer_type_con.$pg_con.' 
	and i.finish_goods_code not between 5000 and 6000
	group by  a.item_id order by i.finish_goods_code';
	
$sql2='select 
		i.finish_goods_code as fg,
		m.gift_on_item as item_id,
		i.item_name,
		i.unit_name as unit,
		i.item_brand as brand,
		i.pack_size,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,
		sum(a.total_unit*i.d_price) as DP,
		sum(a.total_unit*a.unit_price)/sum(a.total_unit) as sale_price,
		sum((a.total_unit*i.d_price)-(a.total_amt)) as discount,
		sum(a.total_unit*a.unit_price) as actual_price
		
from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i 
		
where d.dealer_code=m.dealer_code and m.id=a.order_no  
	and i.item_brand!="" 
	and a.item_id = 1096000100010312 
	and m.gift_on_item=i.item_id 
	'.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dealer_type_con.$pg_con.' 
	group by  m.gift_on_item order by i.finish_goods_code';
	
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$citem[$data2->item_id] = $data2->sale_price*$data2->qty;
	$citempkt[$data2->item_id] = $data2->pkt;
	$citempcs[$data2->item_id] = $data2->pcs;
	$citemqty[$data2->item_id] = $data2->qty;
	}
	
	$sql2='select 
		i.finish_goods_code as fg,
		m.gift_on_item as item_id,
		i.item_name,
		i.unit_name as unit,
		i.item_brand as brand,
		i.pack_size,
		i.d_price,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,
		sum(a.total_unit*i.d_price) as DP,
		sum(a.total_unit*a.unit_price)/sum(a.total_unit) as sale_price,
		sum((a.total_unit*i.d_price)-(a.total_amt)) as discount,
		
		sum(a.total_unit*a.unit_price) as actual_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i 
		
		where m.gift_on_item=i.item_id and d.dealer_code=m.dealer_code and m.id=a.order_no  and i.item_brand!="" and   
	a.unit_price = 0 and m.item_id=i.item_id '.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dealer_type_con.$pg_con.' 
	group by  m.gift_on_item order by i.finish_goods_code';
	
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$ditem[$data2->item_id] = $data2->d_price*$data2->qty;
	$ditempkt[$data2->item_id] = $data2->pkt;
	$ditempcs[$data2->item_id] = $data2->pcs;
	$ditemqty[$data2->item_id] = $data2->qty;
	}

$sql2='select 
		i.finish_goods_code as fg,
		m.gift_on_item as item_id,
		i.item_name,
		i.unit_name as unit,
		i.item_brand as brand,
		i.pack_size,
		if(i.d_price>0,i.d_price,i.f_price) price,
		sum(a.total_unit) div i.pack_size as pkt,
		sum(a.total_unit) mod i.pack_size as pcs,
		sum(a.total_unit) qty,
		sum(a.total_unit*i.d_price) as DP,
		sum(a.total_unit*a.unit_price)/sum(a.total_unit) as sale_price,
		sum((a.total_unit*i.d_price)-(a.total_amt)) as discount,
		
		sum(a.total_unit*a.unit_price) as actual_price
		from dealer_info d, sale_do_details m,sale_do_chalan a, item_info i 
		
		where m.gift_on_item!=i.item_id and d.dealer_code=m.dealer_code and m.id=a.order_no  and i.item_brand!="" and   
	a.unit_price = 0 and m.item_id=i.item_id '.$con.$date_con.$warehouse_con.$item_con.$item_brand_con.$dealer_type_con.$pg_con.' 
	group by  m.gift_on_item order by i.finish_goods_code';
	
	$query2 = mysql_query($sql2);
	while($data2 = mysql_fetch_object($query2))
	{
	$pitem[$data2->item_id] = $data2->price*$data2->qty;
	$pitempkt[$data2->item_id] = $data2->pkt;
	$pitempcs[$data2->item_id] = $data2->pcs;
	$pitemqty[$data2->item_id] = $data2->qty;
	}


?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead><tr><td style="border:0px;" colspan="8"><?=$str?><div class="left"></div><div class="right"></div><div class="date">Reporting Time: <?=date("h:i A d-m-Y")?>222</div></td></tr>

<tr><th rowspan="2">S/L</th>
<th rowspan="2">CODE</th>
<th rowspan="2">Product Name</th>
<th rowspan="2">GRP</th>
<th colspan="2">SALES</th>
<th colspan="2">FREE/BONUS</th>
<th colspan="2">TOTAL GOODS</th>
<th colspan="2">CP</th>
  <th colspan="4">AMOUNT IN TAKA</th>
  <th>TOTAL TRADE</th>
  <th rowspan="2" valign="middle">%</th>
</tr>
<tr>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
  <th>CTN</th>
  <th>PCS</th>
  <th>SALES</th>
  <th>FREE</th>
  <th>DISCOUNT</th>
  <th>CP</th>
  <th>TAKA</th>
  </tr>

</thead><tbody>
<?
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
$payable = $data->actual_price - ((($citem[$data->item_id])*(-1)) + (($ditem[$data->item_id])));
$payable_total = $payable_total + $payable;
$dis_total = $dis_total + (($citem[$data->item_id])*(-1));
$dis_total2 = $dis_total2 + (($ditem[$data->item_id]));
$actual_total = $actual_total + $data->actual_price;
$total_qty = $ditemqty[$data->item_id] + $data->qty;

?>
<tr><td><?=++$s?></td><td><?=$data->fg?></td>
  <td><?=$data->item_name?></td>
  <td><?=$data->sales_item_type?></td>
  <td><?=$data->pkt?></td><td><?=$data->pcs?></td><td><span style="text-align:right">
  <?=number_format((($ditempkt[$data->item_id])),0)?>
</span></td>
  <td><span style="text-align:right">
    <?=number_format((($ditempcs[$data->item_id])),0)?>
  </span></td>
  <td style="text-align:right"><?=(int)($total_qty/$data->pack_size)?></td>
  <td style="text-align:right"><?=(int)($total_qty%$data->pack_size)?></td>
  <td><span style="text-align:right">
    <?=number_format((($pitempkt[$data->item_id])),0)?>
  </span></td>
  <td><span style="text-align:right">
    <?=number_format((($pitempcs[$data->item_id])),0)?>
  </span></td>
  <td style="text-align:right"><?=number_format((($data->actual_price)),2)?></td>
  <td style="text-align:right"><?=number_format((($ditem[$data->item_id])),2)?></td>
  <td style="text-align:right"><?=number_format(($citem[$data->item_id])*(-1),2)?></td>
  <td style="text-align:right"><?=number_format((($pitem[$data->item_id])),2)?></td>
  <td style="text-align:right"><?=number_format((($ditem[$data->item_id]) + (($citem[$data->item_id])*(-1)) + (($pitem[$data->item_id]))),2)?></td>
  <td style="text-align:right"><?=number_format(@(@(@(($ditem[$data->item_id]) + (($citem[$data->item_id])*(-1)) + @(($pitem[$data->item_id])))*100)/($data->actual_price)),2)?></td></tr>
<?
$total_actual_price = $total_actual_price + $data->actual_price;
$free_actual_price = $free_actual_price + ($ditem[$data->item_id]);
$distount_actual_price = $distount_actual_price + (($citem[$data->item_id])*(-1));
$cp_actual_price = $cp_actual_price + (($pitem[$data->item_id]));
$total_trade_amount = $total_trade_amount + ($ditem[$data->item_id]) + (($citem[$data->item_id])*(-1)) + (($pitem[$data->item_id]));
}
?>
<tr class="footer"><td>&nbsp;</td><td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">&nbsp;</td><td colspan="5" style="text-align:right"><?=number_format((($total_actual_price)),2)?></td><td><span style="text-align:right">
  <?=number_format((($free_actual_price)),2)?>
</span></td>
  <td><span style="text-align:right"><?=number_format((($distount_actual_price)),2)?></span></td>
  <td><span style="text-align:right"><?=number_format((($cp_actual_price)),2)?></span></td>
  <td><span style="text-align:right"><?=number_format((($total_trade_amount)),2)?></span></td>
  <td style="text-align:right"><?=number_format((@($total_trade_amount/$total_actual_price)*100),2)?></td></tr></tbody></table>
<?
}




elseif($_REQUEST['report']==110) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">ZONE NAME</span></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#333333"><div align="center"><span class="style3"><?=date('M\'y',strtotime(${'f_mons'.$i}))?></span></div></td>
<?
}
?>
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
 <?
 


$sql = "select ZONE_CODE,ZONE_NAME from zon where REGION_ID=".$_REQUEST['region_id']." order by ZONE_NAME";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$ZONE_CODE = $item->ZONE_CODE;
for($i=12;$i>0;$i--)
{
$m = ($i-1);

$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d,area a where c.total_unit>0 and c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.area_code=a.AREA_CODE and a.ZONE_ID ='".$ZONE_CODE."'".$con;



${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$ZONE_CODE} = ${'totalr'.$ZONE_CODE} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->ZONE_NAME?></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmon'.$i}[0],2);?></div></td>
<?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
        <? $totalallr= $totalallr + ${'totalr'.$ZONE_CODE};echo number_format(${'totalr'.$ZONE_CODE},2)?>
    </strong></div></td>
  </tr>
  <? }



  ?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
      <td>&nbsp;</td>
      <td><strong>D Total</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format(${'totalc'.$i},2);?></div></td>
<?
}
?>

      <td bgcolor="#FFFF99"><div align="right">
        <?=number_format($totalallr,2);?>
      </div></td>
    </tr>

</table>
<?
}
elseif($_REQUEST['report']==111) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">ZONE NAME </span></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#333333"><div align="center"><span class="style3"><?=date('M\'y',strtotime(${'f_mons'.$i}))?></span></div></td>
<?
}
?>
   
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
 <?
 


$sql = "select ZONE_CODE,ZONE_NAME from zon where REGION_ID=".$_REQUEST['region_id']." order by ZONE_NAME";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
if(isset($item_id)) 		{$con=' and c.item_id="'.$item_id.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$ZONE_CODE = $item->ZONE_CODE;
for($i=12;$i>0;$i--)
{
$m = ($i-1);
$sqql = "select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d,area a where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.area_code=a.AREA_CODE and a.ZONE_ID ='".$ZONE_CODE."'".$con;
${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$ZONE_CODE} = ${'totalr'.$ZONE_CODE} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->ZONE_NAME?></td>
	
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmon'.$i}[0],0);?></div></td>
<?
}
?>
	
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <?=number_format(${'totalr'.$ZONE_CODE},0)?>
    </strong></div></td>
  </tr>
  <? }
  


  ?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
      <td>&nbsp;</td>
      <td><strong>D Total</strong></td>
	  
<?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format(${'totalc'.$i},0);?></div></td>
<?
}
?>

	  
      <td bgcolor="#FFFF99"><div align="right"><?=number_format(${'totald'},0);?></div></td>
    </tr>

</table>
<?
}
elseif($_REQUEST['report']==112) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">ZONE NAME </span></td>
    <?
for($i=12;$i>0;$i--)
{
?>
    <td bgcolor="#333333"><div align="center"><span class="style3">
      <?=date('M\'y',strtotime(${'f_mons'.$i}))?>
    </span></div></td>
    <?
}
?>
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
  <?
 


$sql = "select ZONE_CODE,ZONE_NAME from zon where REGION_ID=".$_REQUEST['region_id']." order by ZONE_NAME";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
if(isset($item_id)) 		{$con=' and c.item_id="'.$item_id.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$ZONE_CODE = $item->ZONE_CODE;
for($i=12;$i>0;$i--)
{
$m = ($i-1);

$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d,area a where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.area_code=a.AREA_CODE and a.ZONE_ID ='".$ZONE_CODE."'".$con;



${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$ZONE_CODE} = ${'totalr'.$ZONE_CODE} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->ZONE_NAME?></td>
    <?
for($i=12;$i>0;$i--)
{
?>
    <td bgcolor="#99CCFF"><div align="right">
      <?=number_format(${'sqlmon'.$i}[0],2);?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <? 
$totalallr= $totalallr + ${'totalr'.$ZONE_CODE};
echo number_format(${'totalr'.$ZONE_CODE},2);
	  ?>
    </strong></div></td>
  </tr>
  <? }


  ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td>&nbsp;</td>
    <td><strong>D Total</strong></td>
    <?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
    <td bgcolor="#FFFF66"><div align="right">
      <?=number_format(${'totalc'.$i},2);?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right">
      <?=number_format($totalallr,2);?>
    </div></td>
  </tr>
</table>
<?
}

elseif($_REQUEST['report']==1130) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">CODE</span></td>
    <td bgcolor="#333333"><span class="style3">DEALER NAME </span></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#333333"><div align="center"><span class="style3"><?=date('M\'y',strtotime(${'f_mons'.$i}))?></span></div></td>
<?
}
?>
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
 <?
 


$sql = "select dealer_code,dealer_name_e as dealer_name from dealer_info m where dealer_type = 'Corporate' ".$dealer_con." order by dealer_name_e";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$dealer_code = $item->dealer_code;
for($i=12;$i>0;$i--)
{
$m = ($i-1);

$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d,item_info i where i.item_id=c.item_id and c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type = 'Corporate' and d.dealer_code='".$dealer_code."'".$item_brand_con.$item_con.$con;



${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$dealer_code} = ${'totalr'.$dealer_code} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->dealer_code?></td>
    <td><?=$item->dealer_name?></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmon'.$i}[0],2);?></div></td>
<?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
        <? $totalallr= $totalallr + ${'totalr'.$dealer_code};echo number_format(${'totalr'.$dealer_code},2)?>
    </strong></div></td>
  </tr>
  <? }


  ?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
      <td colspan="2">&nbsp;</td>
      <td><strong>D Total</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format(${'totalc'.$i},2);?></div></td>
<?
}
?>

      <td bgcolor="#FFFF99"><div align="right">
        <?=number_format($totalallr,2);?>
      </div></td>
    </tr>
</table>
<?
}
elseif($_REQUEST['report']==11301) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">CODE</span></td>
    <td bgcolor="#333333"><span class="style3">DEALER NAME </span></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#333333"><div align="center"><span class="style3"><?=date('M\'y',strtotime(${'f_mons'.$i}))?></span></div></td>
<?
}
?>
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
 <?
 


$sql = "select dealer_code,dealer_name_e as dealer_name from dealer_info m where dealer_type = 'SuperShop' ".$dealer_con." order by dealer_name_e";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$dealer_code = $item->dealer_code;
for($i=12;$i>0;$i--)
{
$m = ($i-1);

$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d,item_info i where i.item_id=c.item_id and c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type = 'SuperShop' and d.dealer_code='".$dealer_code."'".$item_brand_con.$item_con.$con;



${'sqlmon'.$i} = @mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$dealer_code} = ${'totalr'.$dealer_code} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->dealer_code?></td>
    <td><?=$item->dealer_name?></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmon'.$i}[0],2);?></div></td>
<?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
        <? $totalallr= $totalallr + ${'totalr'.$dealer_code};echo number_format(${'totalr'.$dealer_code},2)?>
    </strong></div></td>
  </tr>
  <? }


  ?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
      <td colspan="2">&nbsp;</td>
      <td><strong>D Total</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format(${'totalc'.$i},2);?></div></td>
<?
}
?>

      <td bgcolor="#FFFF99"><div align="right">
        <?=number_format($totalallr,2);?>
      </div></td>
    </tr>
</table>
<?
}
elseif($_REQUEST['report']==113) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">CODE</span></td>
    <td bgcolor="#333333"><span class="style3">DEALER NAME </span></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#333333"><div align="center"><span class="style3"><?=date('M\'y',strtotime(${'f_mons'.$i}))?></span></div></td>
<?
}
?>
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
 <?
 


$sql = "select dealer_code,dealer_name_e as dealer_name from dealer_info d, area a where d.area_code=a.AREA_CODE and a.ZONE_ID=".$_REQUEST['zone_id']."  order by dealer_name_e";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$dealer_code = $item->dealer_code;
for($i=12;$i>0;$i--)
{
$m = ($i-1);

$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.dealer_code='".$dealer_code."'".$con;



${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$dealer_code} = ${'totalr'.$dealer_code} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->dealer_code?></td>
    <td><?=$item->dealer_name?></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmon'.$i}[0],2);?></div></td>
<?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
        <? $totalallr= $totalallr + ${'totalr'.$dealer_code};echo number_format(${'totalr'.$dealer_code},2)?>
    </strong></div></td>
  </tr>
  <? }

for($i=12;$i>0;$i--)
{
$m = ($i-1);


$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Corporate'".$con;

${'sqlmonc'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totalco'.$i} = ${'totalco'.$i} + ${'sqlmonc'.$i}[0];
${'totalrc1'} = ${'totalrc1'} + ${'sqlmonc'.$i}[0];
}

  ?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
      <td colspan="2">&nbsp;</td>
      <td><strong>D Total</strong></td>
<?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format(${'totalc'.$i},2);?></div></td>
<?
}
?>

      <td bgcolor="#FFFF99"><div align="right">
        <?=number_format($totalallr,2);?>
      </div></td>
    </tr>
	<?
	  for($i=12;$i>0;$i--)
{
$m = ($i-1);


$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='SuperShop'".$con;
${'sqlmons'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totals'.$i} = ${'totals'.$i} + ${'sqlmons'.$i}[0];
${'totalrc'} = ${'totalrc'} + ${'sqlmons'.$i}[0];
}
	?>
</table>
<?
}
elseif($_REQUEST['report']==116) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">REGION NAME</span></td>
    <td bgcolor="#333333"><span class="style3">ZONE NAME </span></td>
    <td bgcolor="#333333"><span class="style3">CTN</span></td>
    <td bgcolor="#333333"><span class="style3">TAKA(DP)</span></td>
  </tr>
  <?
 
//$region_name = find_a_field('branch','BRANCH_NAME','BRANCH_ID='.$region_id);
if($region_id>0) $region_con = ' and REGION_ID="'.$region_id.'"';
$sql = "select z.*,b.BRANCH_NAME from zon z,branch b where 1 and BRANCH_ID=REGION_ID ".$region_con." order by BRANCH_NAME,ZONE_NAME";

	$query = mysql_query($sql);
	while($item=mysql_fetch_object($query)){
 $zone_code = $item->ZONE_CODE;
$sqlmon = mysql_fetch_row(mysql_query("select sum(c.total_amt),sum(c.total_unit),c.pkt_size from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_date."' and '".$t_date."' and d.dealer_type='Distributor' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_id));

//echo "select sum(c.total_amt),sum(c.total_unit),i.pack_size from sale_do_chalan c,dealer_info d, area a where c.chalan_date between '".$f_date."' and '".$t_date."' and d.dealer_type='Distributor' and c.dealer_code=d.dealer_code and d.area_code=a.AREA_CODE and a.ZONE_ID='".$zone_code."' and c.item_id=".$item_id;
$totalq = $totalq + (int)@($sqlmon[1]/$sqlmon[2]);
$totalt = $totalt + $sqlmon[0];
 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$i?></td>
    <td><?=$item->BRANCH_NAME?></td>
    <td>
      <?=find_a_field('zon','ZONE_NAME','ZONE_CODE='.$zone_code)?>    </td>
    <td bgcolor="#99CCFF"><?=(int)@($sqlmon[1]/$sqlmon[2]);?></td>
    <td bgcolor="#66CC99"><?=number_format($sqlmon[0],2);?></td>
  </tr>
  <? }
  
  ?>  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td colspan="3">TOTAL :</td>
    <td><?=(int)@($totalq);?></td>
    <td><?=number_format($totalt,2);?></td>
    </tr>
</table>
<?
}
elseif($_REQUEST['report']==114) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">DEALER NAME </span></td>
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#333333"><div align="center"><span class="style3"><?=date('M\'y',strtotime(${'f_mons'.$i}))?></span></div></td>
<?
}
?>
   
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
 <?
 


$sql = "select dealer_code,dealer_name_e as dealer_name from dealer_info d, area a where d.area_code=a.AREA_CODE and a.ZONE_ID=".$_REQUEST['zone_id']."  order by dealer_name_e";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
if(isset($item_id)) 		{$con=' and c.item_id="'.$item_id.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$dealer_code = $item->dealer_code;
for($i=12;$i>0;$i--)
{
$m = ($i-1);
$sqql = "select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.dealer_code='".$dealer_code."'".$con;
${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$dealer_code} = ${'totalr'.$dealer_code} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->dealer_name?></td>
	
<?
for($i=12;$i>0;$i--)
{
?>
<td bgcolor="#99CCFF"><div align="right"><?=number_format(${'sqlmon'.$i}[0],0);?></div></td>
<?
}
?>
	
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <?=number_format(${'totalr'.$dealer_code},0)?>
    </strong></div></td>
  </tr>
  <? }
  
  for($i=12;$i>0;$i--)
{
$m = ($i-1);
$sqql = "select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Corporate'".$con;
${'sqlmonc'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totalco'.$i} = ${'totalco'.$i} + ${'sqlmonc'.$i}[0];
${'totalrc1'} = ${'totalrc1'} + ${'sqlmonc'.$i}[0];
}

  ?>
    <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
      <td>&nbsp;</td>
      <td><strong>D Total</strong></td>
	  
<?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
<td bgcolor="#FFFF66"><div align="right"><?=number_format(${'totalc'.$i},0);?></div></td>
<?
}
?>

	  
      <td bgcolor="#FFFF99"><div align="right"><?=number_format(${'totald'},0);?></div></td>
    </tr>
	<?
	  for($i=12;$i>0;$i--)
{
$m = ($i-1);
$sqql = "select sum(c.pkt_unit) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='SuperShop'".$con;;
${'sqlmons'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totals'.$i} = ${'totals'.$i} + ${'sqlmons'.$i}[0];
${'totalrc'} = ${'totalrc'} + ${'sqlmons'.$i}[0];
}
	?>
</table>
<?
}
elseif($_REQUEST['report']==115) 
{echo $str;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L</span></td>
    <td bgcolor="#333333"><span class="style3">DEALER NAME </span></td>
    <?
for($i=12;$i>0;$i--)
{
?>
    <td bgcolor="#333333"><div align="center"><span class="style3">
      <?=date('M\'y',strtotime(${'f_mons'.$i}))?>
    </span></div></td>
    <?
}
?>
    <td bgcolor="#333333"><div align="center"><strong><span class="style5">Total</span></strong></div></td>
  </tr>
  <?
 


$sql = "select dealer_code,dealer_name_e as dealer_name from dealer_info d, area a where d.area_code=a.AREA_CODE and a.ZONE_ID=".$_REQUEST['zone_id']."  order by dealer_name_e";
if(isset($product_group)) 		{$con=' and d.product_group="'.$product_group.'"';}
if(isset($item_id)) 		{$con=' and c.item_id="'.$item_id.'"';}
$query = @mysql_query($sql);
while($item=@mysql_fetch_object($query)){

$dealer_code = $item->dealer_code;
for($i=12;$i>0;$i--)
{
$m = ($i-1);

$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Distributor' and d.dealer_code='".$dealer_code."'".$con;



${'sqlmon'.$i} = mysql_fetch_row(mysql_query($sqql));

${'totalc'.$i} = ${'totalc'.$i} + ${'sqlmon'.$i}[0];
${'totalr'.$dealer_code} = ${'totalr'.$dealer_code} + ${'sqlmon'.$i}[0];
}



 ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$item->dealer_name?></td>
    <?
for($i=12;$i>0;$i--)
{
?>
    <td bgcolor="#99CCFF"><div align="right">
      <?=number_format(${'sqlmon'.$i}[0],2);?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right"><strong>
      <? 
$totalallr= $totalallr + ${'totalr'.$dealer_code};
echo number_format(${'totalr'.$dealer_code},2);
	  ?>
    </strong></div></td>
  </tr>
  <? }

for($i=12;$i>0;$i--)
{
$m = ($i-1);


$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='Corporate'".$con;

${'sqlmonc'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totalco'.$i} = ${'totalco'.$i} + ${'sqlmonc'.$i}[0];
${'totalrc1'} = ${'totalrc1'} + ${'sqlmonc'.$i}[0];
}

  ?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td>&nbsp;</td>
    <td><strong>D Total</strong></td>
    <?
for($i=12;$i>0;$i--)
{
${'totald'} = ${'totald'} + ${'totalc'.$i};
?>
    <td bgcolor="#FFFF66"><div align="right">
      <?=number_format(${'totalc'.$i},2);?>
    </div></td>
    <?
}
?>
    <td bgcolor="#FFFF99"><div align="right">
      <?=number_format($totalallr,2);?>
    </div></td>
  </tr>
  <?
	  for($i=12;$i>0;$i--)
{
$m = ($i-1);


$sqql = "select sum(c.total_amt) from sale_do_chalan c,dealer_info d where c.chalan_date between '".${'f_mons'.$i}."' and '".${'f_mone'.$i}."' and c.dealer_code=d.dealer_code and d.dealer_type='SuperShop'".$con;
${'sqlmons'.$i} = mysql_fetch_row(mysql_query($sqql));
${'totals'.$i} = ${'totals'.$i} + ${'sqlmons'.$i}[0];
${'totalrc'} = ${'totalrc'} + ${'sqlmons'.$i}[0];
}
	?>
</table>
<?
}




elseif($_REQUEST['report']==1992) {

echo $str;
$t_date2 = date('Y-m-d',strtotime($t_date . "+1 days"));
$begin = new DateTime($f_date);
$end = new DateTime($t_date2);

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

 $sql = "select sum(c.total_amt) as total_amt,m.do_date 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and d.dealer_type='Distributor' and d.product_group='A' and c.total_amt>0 group by m.do_date";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
${'A'.$data->do_date} = $data->total_amt;
}
$sql = "select sum(c.total_amt) as total_amt,m.do_date 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and d.dealer_type='Distributor' and d.product_group='B' and c.total_amt>0 group by m.do_date";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
${'B'.$data->do_date} = $data->total_amt;
}
$sql = "select sum(c.total_amt) as total_amt,m.do_date 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and d.dealer_type='Distributor' and d.product_group='C' and c.total_amt>0 group by m.do_date";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){
${'C'.$data->do_date} = $data->total_amt;
}
$sql = "select sum(c.total_amt) as total_amt,m.do_date 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and d.dealer_type='Distributor' and d.product_group='D' and c.total_amt>0 group by m.do_date";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){${'D'.$data->do_date} = $data->total_amt;}
$sql = "select sum(c.total_amt) as total_amt,m.do_date 
from sale_do_details c,dealer_info d,sale_do_master m  
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and d.dealer_type='Distributor' and d.product_group='E' and c.total_amt>0 group by m.do_date";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){${'E'.$data->do_date} = $data->total_amt;}
$sql = "select sum(c.total_amt) as total_amt,m.do_date from sale_do_details c,dealer_info d ,sale_do_master m 
where c.do_no=m.do_no and m.status in('CHECKED','COMPLETED') and m.do_date between '".$f_date."' and '".$t_date."' and c.dealer_code=d.dealer_code 
and (d.dealer_type='Corporate' or d.dealer_type='SuperShop' or (d.dealer_type='Distributor' AND product_group='M')) and c.total_amt>0 group by m.do_date";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){${'X'.$data->do_date} = $data->total_amt;}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L-1992</span></td>
    <td bgcolor="#333333"><span class="style3">Date</span></td>
    <td bgcolor="#333333"><span class="style3">Group-A</span></td>
    <td bgcolor="#333333"><span class="style3">Group-B</span></td>
    <td bgcolor="#333333"><span class="style3">Group-C</span></td>
    <td bgcolor="#333333"><span class="style3">Group-D</span></td>
    <td bgcolor="#333333"><span class="style3">Group-E</span></td>
    <td width="1" bgcolor="#333333"><div align="right"><strong><span class="style5">Total DO<br />
      (A+B+C+D+E)</span></strong></div></td>
    <td width="1" bgcolor="#333333"><span class="style3">Mordern Trade</span></td>
    <td width="1" bgcolor="#333333"><span class="style3">ALL DO</span></td>
  </tr>
<? foreach ( $period as $dt ){ $today_date = $dt->format("Y-m-d");
$day_total = ${'A'.$today_date} + ${'B'.$today_date} + ${'C'.$today_date} + ${'D'.$today_date} + ${'E'.$today_date};
$do_all = ${'A'.$today_date} + ${'B'.$today_date} + ${'C'.$today_date} + ${'D'.$today_date} + ${'E'.$today_date} + ${'X'.$today_date};
$do_total = $do_total + $do_all;
$mon_total = $mon_total + ${'A'.$today_date} + ${'B'.$today_date} + ${'C'.$today_date} + ${'D'.$today_date} + ${'E'.$today_date};
$A_total = $A_total + ${'A'.$today_date};
$B_total = $B_total + ${'B'.$today_date};
$C_total = $C_total + ${'C'.$today_date};
$D_total = $D_total + ${'D'.$today_date};
$E_total = $E_total + ${'E'.$today_date};
$X_total = $X_total + ${'X'.$today_date};
?>
  <tr bgcolor="#<?=(($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$today_date;?></td>
    <td><div align="right"><?=number_format(${'A'.$today_date},2);?></div></td>
    <td><div align="right"><?=number_format(${'B'.$today_date},2);?></div></td>
    <td><div align="right"><?=number_format(${'C'.$today_date},2);?></div></td>
    <td><div align="right"><?=number_format(${'D'.$today_date},2);?></div></td>
    <td><div align="right"><?=number_format(${'E'.$today_date},2);?></div></td>
    <td bgcolor="#FFFF99"><div align="right"><?=number_format($day_total,2);?></div></td>
    <td><div align="right"><?=number_format(${'X'.$today_date},2);?></div></td>
    <td bgcolor="#FFFF99"><div align="right"><?=number_format($do_all,2);?></div></td>
  </tr>
<? }?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><div align="right"><?=number_format($A_total,2);?></div></td>
    <td><div align="right"><?=number_format($B_total,2);?></div></td>
    <td><div align="right"><?=number_format($C_total,2);?></div></td>
    <td><div align="right"><?=number_format($D_total,2);?></div></td>
    <td><div align="right"><?=number_format($E_total,2);?></div></td>
    <td bgcolor="#FFFF99"><div align="right"><?=number_format($mon_total,2);?></div></td>
    <td><div align="right"><?=number_format($X_total,2);?></div></td>
    <td bgcolor="#FFFF99"><div align="right"><?=number_format($do_total,2);?></div></td>
  </tr>
</table>
<?
}



// Collection group wise Report 6-Feb-2022
elseif($_REQUEST['report']==1993) {

echo $str;

$sql1 = "SELECT d.product_group, m.do_date, sum(m.rcv_amt) as amount
FROM sale_do_master m,dealer_info d
WHERE 
m.dealer_code=d.dealer_code and m.do_date between '".$f_date."' and '".$t_date."'
group by m.do_date,d.product_group";
$query1 = mysql_query($sql1);
while($info=mysql_fetch_object($query1)){
$collection[$info->product_group][$info->do_date]=$info->amount;
}

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#333333"><span class="style3">S/L-1993</span></td>
    <td bgcolor="#333333"><span class="style3">Date</span></td>
    <td bgcolor="#333333"><span class="style3">Group-A</span></td>
    <td bgcolor="#333333"><span class="style3">Group-B</span></td>
    <td bgcolor="#333333"><span class="style3">Group-C</span></td>
    <td width="1" bgcolor="#333333"><div align="right"><strong><span class="style5">Total DO<br />(A+B+C)</span></strong></div></td>
    <td width="1" bgcolor="#333333"><span class="style3">Mordern Trade</span></td>
    <td width="1" bgcolor="#333333"><span class="style3">Total</span></td>
  </tr>
<?  
$sql = "SELECT m.do_date FROM sale_do_master m
WHERE m.do_date between '".$f_date."' and '".$t_date."'
group by m.do_date order by m.do_date";
$query = mysql_query($sql);
while($data=mysql_fetch_object($query)){  ?>
  <tr bgcolor="#<?=(($i%2)==0)?'fff':'EBEBEB';?>">
    <td><?=++$j?></td>
    <td><?=$data->do_date;?></td>
    <td><div align="right"><?=$collection['A'][$data->do_date];?></div></td>
    <td><div align="right"><?=$collection['B'][$data->do_date];?></div></td>
    <td><div align="right"><?=$collection['C'][$data->do_date];?></div></td>
    <td bgcolor="#FFFF99"><div align="right">
	<? echo $dis = ($collection['A'][$data->do_date]+$collection['B'][$data->do_date]+$collection['C'][$data->do_date]);?></div></td>
    <td><div align="right"><? echo $modern =($collection[''][$data->do_date]);?></div></td>
    <td bgcolor="#FFFF99"><div align="right"><? echo $dtotal=$dis+$modern;?></div></td>
  </tr>
<? 
$total_a +=$collection['A'][$data->do_date];
$total_b +=$collection['B'][$data->do_date];
$total_c +=$collection['C'][$data->do_date];
$total_dis += $dis;
$total_m +=$modern;
$g_total += $dtotal;

$collection['A'][$data->do_date]=0;
$collection['B'][$data->do_date]=0;
$collection['C'][$data->do_date]=0;
$collection[''][$data->do_date]=0;

}?>
  <tr bgcolor="#<? echo (($i%2)==0)?'fff':'EBEBEB';?>">
    <td></td>
    <td><strong>Total</strong></td>
    <td><div align="right"><strong><?=$total_a;?></strong></div></td>
    <td><div align="right"><strong><?=$total_b;?></strong></div></td>
    <td><div align="right"><strong><?=$total_c;?></strong></div></td>
    <td bgcolor="#FFFF99"><div align="right"><strong><?=$total_dis;?></strong></div></td>
    <td><div align="right"><?=$total_m;?></div></td>
    <td bgcolor="#FFFF99"><div align="right"><strong><?=$g_total;?></strong></div></td>
  </tr>
</table>
<?
} // end 1993





// modify april 21
elseif($_REQUEST['report']==2000) 
{
if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';
$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if(isset($product_group)) 		{$pg_con=' and i.sales_item_type="'.$product_group.'"';} 
if($depot_id>0) 				{$dpt_con=' and d.depot="'.$depot_id.'"';} 


$sqlr = "select * from branch";
$queryr = mysql_query($sqlr);
while($region = mysql_fetch_object($queryr)){
$region_id = $region->BRANCH_ID;

$sql = "select i.finish_goods_code as code, sum(o.total_unit) as total_unit
from sale_do_master m,sale_do_details o, item_info i,dealer_info d, area a, zon z
where o.unit_price>0 and m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') and d.area_code=a.AREA_CODE and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=".$region_id."
".$dtype_con.$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.' 
group by i.finish_goods_code';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty[$info->code][$region_id] = $info->total_unit;
}}

if(isset($product_group)) 		{$pg_con=' and i.sales_item_type like "%'.$product_group.'%"';}
		$sql = "select 
		i.finish_goods_code as code, 
		i.item_name, i.item_brand, i.brand_category_type,
		i.sales_item_type as `group`,i.pack_size,i.d_price
		from 
		item_info i
		where i.finish_goods_code>0 and i.finish_goods_code not between 5000 and 6000 and i.finish_goods_code not between 2000 and 3000 ".$item_con.$item_brand_con.$pg_con.' 
		group by i.finish_goods_code';
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="16"><?=$str?></td></tr><tr>
<th height="20">S/L</th>
<th>Code</th>
<th>Item Name</th>
<th>Grp</th>
<th>Brand</th>
<th>Sub-Brand</th>
<th>DHK North</th>
<th>DHK South</th>
<th>Ctg</th>
<th>Comilla</th>
<th>Sylhet</th>
<th><!--Barisal--> South Bengal</th>
<th><!--Bogra--> North Bengal</th>
<th>Total</th>
</tr></thead>
<tbody>
<?
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

$total_item = 
(int)($do_qty[$info->code][13]/$info->pack_size)+
(int)($do_qty[$info->code][12]/$info->pack_size)+
(int)($do_qty[$info->code][3]/$info->pack_size)+
(int)($do_qty[$info->code][4]/$info->pack_size)+
(int)($do_qty[$info->code][5]/$info->pack_size)+
(int)($do_qty[$info->code][9]/$info->pack_size)+
(int)($do_qty[$info->code][10]/$info->pack_size)+
(int)($do_qty[$info->code][8]/$info->pack_size);

if($total_item>0){
?>
<tr><td><?=++$i;?></td>
  <td><?=$info->code;?></td>
  <td><?=$info->item_name;?></td>
  <td><?=$info->group?></td>
  <td><?=$info->item_brand?></td>
  <td style="text-align:center"><?=$info->brand_category_type?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][13]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][12]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][3]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][4]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][5]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][10]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][8]/$info->pack_size)?></td>
  <td style="text-align:right"><?=number_format($total_item,0);?></td></tr>
<?
}}
?>
</tbody></table>
<?
		$str = '';

}

// modify jun 22
elseif($_REQUEST['report']==2010) 
{
if(isset($t_date)) 	
{$to_date=$t_date; $fr_date=$f_date; $date_con=' and m.do_date between \''.$fr_date.'\' and \''.$to_date.'\'';
$cdate_con=' and do_date between \''.$fr_date.'\' and \''.$to_date.'\'';}

if(isset($product_group)) 		{$pg_con=' and i.sales_item_type="'.$product_group.'"';} 
if($depot_id>0) 				{$dpt_con=' and d.depot="'.$depot_id.'"';} 


$sqlr = "select * from branch";
$queryr = mysql_query($sqlr);
while($region = mysql_fetch_object($queryr)){
$region_id = $region->BRANCH_ID;

 $sql = "select i.finish_goods_code as code, sum(o.total_unit) as total_unit
from ss_do_master m,ss_do_details o, item_info i,dealer_info d, area a, zon z
where o.unit_price>0 and m.do_no=o.do_no and m.dealer_code=d.dealer_code and i.item_id=o.item_id  and m.status in ('CHECKED','COMPLETED') and d.area_code=a.AREA_CODE and a.ZONE_ID=z.ZONE_CODE and z.REGION_ID=".$region_id."
".$dtype_con.$date_con.$item_con.$item_brand_con.$pg_con.$dpt_con.' 
group by i.finish_goods_code';
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){
$do_qty[$info->code][$region_id] = $info->total_unit;
}}

if(isset($product_group)) 		{$pg_con=' and i.sales_item_type like "%'.$product_group.'%"';}
		$sql = "select 
		i.finish_goods_code as code, 
		i.item_name, i.item_brand, i.brand_category_type,
		i.sales_item_type as `group`,i.pack_size,i.d_price
		from 
		item_info i
		where i.finish_goods_code>0 and i.finish_goods_code not between 5000 and 6000 and i.finish_goods_code not between 2000 and 3000 ".$item_con.$item_brand_con.$pg_con.' 
		group by i.finish_goods_code';
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0"><thead>
<tr><td style="border:0px;" colspan="16"><?=$str?></td></tr><tr>
<th height="20">S/L</th>
<th>Code</th>
<th>Item Name</th>
<th>Grp</th>
<th>Brand</th>
<th>Sub-Brand</th>
<th>DHK North</th>
<th>DHK South</th>
<th>Ctg</th>
<th>Comilla</th>
<th>Sylhet</th>
<th><!--Barisal--> South Bengal</th>
<th><!--Bogra--> North Bengal</th>
<th>Total</th>
</tr></thead>
<tbody>
<?
$query = mysql_query($sql);
while($info = mysql_fetch_object($query)){

 $total_item = 
(int)($do_qty[$info->code][13]/$info->pack_size)+
(int)($do_qty[$info->code][12]/$info->pack_size)+
(int)($do_qty[$info->code][3]/$info->pack_size)+
(int)($do_qty[$info->code][4]/$info->pack_size)+
(int)($do_qty[$info->code][5]/$info->pack_size)+
(int)($do_qty[$info->code][9]/$info->pack_size)+
(int)($do_qty[$info->code][10]/$info->pack_size)+
(int)($do_qty[$info->code][8]/$info->pack_size);

if($total_item>0){
?>
<tr><td><?=++$i;?></td>
  <td><?=$info->code;?></td>
  <td><?=$info->item_name;?></td>
  <td><?=$info->group?></td>
  <td><?=$info->item_brand?></td>
  <td style="text-align:center"><?=$info->brand_category_type?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][13]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][12]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][3]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][4]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][5]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][10]/$info->pack_size)?></td>
  <td style="text-align:right"><?=(int)($do_qty[$info->code][8]/$info->pack_size)?></td>
  <td style="text-align:right"><?=number_format($total_item,0);?></td></tr>
<?
}}
?>
</tbody></table>
<?
		$str = '';

}

elseif($_REQUEST['report']==2012) 
{ 
if(isset($dealer_code)) 
$sqlbranch 	= "select * from dealer_info where dealer_type like 'Distributor' and dealer_code = ".$dealer_code;
else
$sqlbranch 	= "select * from dealer_info where dealer_type like 'Distributor' ";
$querybranch = mysql_query($sqlbranch);
while($branch=mysql_fetch_object($querybranch)){
	$rp=0;
	echo '<div>';
	
$op_sql = "select sum(item_in-item_ex) as stock , warehouse_id, item_id from journal_item where warehouse_id = ".$branch->dealer_depo." group by item_id ";
$op_query= mysql_query($op_sql);
while($opqr = mysql_fetch_object($op_query)){
	$depo_op[$opqr->warehouse_id][$opqr->item_id] = $opqr->stock;
}

$op_sql1 = "select sum(total_unit) as stock , dealer_code, item_id from sale_do_chalan where dealer_code = ".$branch->dealer_code." and chalan_date<'".$_POST['f_date']."' group by item_id ";
$op_query1= mysql_query($op_sql1);
while($opqr1 = mysql_fetch_object($op_query1)){
	$depo_chalan[$opqr1->dealer_code][$opqr1->item_id] = $opqr1->stock;
}

 $op_sql2 = "select sum(total_unit) as stock , dealer_code, item_id from sale_do_chalan where dealer_code = ".$branch->dealer_code." and chalan_date between '".$_POST['f_date']."' and '".$_POST['t_date']."' group by item_id ";
$op_query2= mysql_query($op_sql2);
while($opqr2 = mysql_fetch_object($op_query2)){
	 $chalan[$opqr2->dealer_code][$opqr2->item_id] = $opqr2->stock;
}
//if(isset($zone_id)) 
//$sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID." and ZONE_CODE=".$zone_id;
//else
// $sqlzone 	= "select * from zon where REGION_ID=".$branch->BRANCH_ID;
//
//	$queryzone = mysql_query($sqlzone);
//	while($zone=mysql_fetch_object($queryzone)){
if($area_id>0) 
$area_con = "and a.AREA_CODE=".$area_id;

$sql = "select i.item_id,i.finish_goods_code as code,i.item_name,i.item_brand,i.sales_item_type as `group`,
floor(sum(o.total_unit)/i.pack_size) as crt,
mod(sum(o.total_unit),i.pack_size) as pcs, 
sum(o.total_unit) as total_unit,
sum(o.total_amt) as DP,
sum(o.total_unit*o.t_price) as TP
from 
ss_do_master m,ss_do_details o, item_info i, warehouse w, dealer_info d, area a
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot and i.item_id=o.item_id and a.AREA_CODE=d.area_code 
and m.status in ('CHECKED','COMPLETED') and m.dealer_code=".$branch->dealer_code." and o.unit_price>0
".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.' 
group by i.finish_goods_code';

 $sqlt="select sum(o.t_price*o.total_unit) as total,sum(total_amt) as dp_total, sum(total_unit) as unit_total
from 
ss_do_master m,ss_do_details o, item_info i, warehouse w, dealer_info d, area a
where m.do_no=o.do_no and m.dealer_code=d.dealer_code and w.warehouse_id=d.depot 
and i.item_id=o.item_id and a.AREA_CODE=d.area_code  and m.status in ('CHECKED','COMPLETED') 
and o.unit_price>0
and m.dealer_code=".$branch->dealer_code." ".$date_con.$item_con.$item_brand_con.$pg_con.$area_con.'';

		$queryt = mysql_query($sqlt);
		$t= mysql_fetch_object($queryt);
		if($t->dp_total>0)
		{
			if($rp==0) {$reg_total=0;$dp_total=0; 
			$str .= '<p style="width:100%"><strong>Dealer Name: '.$branch->dealer_name_e.' Dealer Code: '.$branch->dealer_code.' </strong></p>';$rp++;}
			$str .= '<p style="width:100%">Address: '.$branch->address_e. ' <strong>Mobile: </strong>'.$branch->mobile_no.' </p>';
		?>	
		
		<table width="100%" border="0" cellpadding="2" cellspacing="0">
		<thead>
			<tr>
				<td colspan="10" style="border:0px;"><div class="header">
				 <?=$str;?>
				</td>
			</tr>
			<tr>
				<th>S/L</th>
				<th>Code</th>
				<th>Item Name</th>
				<th>Item Brand</th>
				<th>Group</th>
				<th>Opening Stock</th>
				<th>Chalan Qty</th>
				<th>Total Stock</th>
				<th>Sales Qty</th>
				<th>Present Stock</th>
				<th>DP</th>
				<th>TP</th>
			</tr>
		</thead>
		<tbody>
		<?
		
			$unit_total1 = 0;
			$reg_total1  = 0;
			$dp_total1   = 0;
		
		 $squery=mysql_query($sql);
			$sl =1;
			while($res = mysql_fetch_object($squery)){ ?>
			<tr>
				<td><?=$sl++;?></td>
				<td align="center"><?=$res->code;?></td>
				<td><?=$res->item_name;?></td>
				<td><?=$res->item_brand;?></td>
				<td><?=$res->group;?></td>
				<td align="right"><?=$op_stock = ($depo_op[$branch->dealer_depo][$res->item_id]+$depo_chalan[$branch->dealer_code][$res->item_id]);?></td>
				<td align="right"><?=$chalan1 = $chalan[$branch->dealer_code][$res->item_id];?></td>
				<td align="right"><?=$stock=($op_stock+$chalan1);?></td>
				<td align="right"><?=$res->total_unit;?></td>
				<td align="right"><?=$present_stock = ($stock - $res->total_unit);?></td>
				<td align="right"><?=$res->DP;?></td>
				<td align="right"><?=$res->TP;?></td>
			</tr>
		 
		<?
		}
			//echo report_create($sql,1,$str);
			$str = '';
			
			$unit_total1 = $unit_total1+$t->unit_total;
			$reg_total1  = $reg_total1+$t->total;
			$dp_total1   = $dp_total1+$t->dp_total;
			
		}

	//}
?>
			<tr>
				<td></td>
				<td colspan="7" align="right">Total:</td>
				<td align="right"><strong><?=number_format($unit_total1,2);?></strong></td>
				<td align="right"></td>
				<td align="right"><strong><?=number_format($dp_total1,2);?></strong></td>
				<td align="right"><strong><?=number_format($reg_total1,2);?></strong></td>
			</tr>
		  </tbody>
</table>
<?	
}
}


elseif(isset($sql)&&$sql!='') {echo report_create($sql,1,$str);}
?>
</div>
</body>
</html>