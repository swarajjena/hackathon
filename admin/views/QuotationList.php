<style>
    .CSSTableGenerator {
	margin:0px;padding:0px;
	width:90%;
	box-shadow: 10px 10px 5px #888888;
	border:1px solid #000000;
	
	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;
	
	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;
	
	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;
	
	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}.CSSTableGenerator table{
    border-collapse: collapse;
        border-spacing: 0;
	width:100%;
	height:100%;
	margin:0px;padding:0px;
}.CSSTableGenerator tr:last-child td:last-child {
	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;
}
.CSSTableGenerator table tr:first-child td:first-child {
	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}
.CSSTableGenerator table tr:first-child td:last-child {
	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;
}.CSSTableGenerator tr:last-child td:first-child{
	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;
}.CSSTableGenerator tr:hover td{
	
}
.CSSTableGenerator tr:nth-child(odd){ background-color:#e5e5e5; }
.CSSTableGenerator tr:nth-child(even)    { background-color:#ffffff; }.CSSTableGenerator td{
	vertical-align:middle;
	
	
	border:1px solid #000000;
	border-width:0px 1px 1px 0px;
	text-align:left;
	padding:7px;
	font-size:15px;
	font-family:Arial;
	font-weight:normal;
	color:#000000;
}.CSSTableGenerator tr:last-child td{
	border-width:0px 1px 0px 0px;
}.CSSTableGenerator tr td:last-child{
	border-width:0px 0px 1px 0px;
}.CSSTableGenerator tr:last-child td:last-child{
	border-width:0px 0px 0px 0px;
}
.CSSTableGenerator tr:first-child td{
		background:-o-linear-gradient(bottom, #cccccc 5%, #b2b2b2 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #b2b2b2) );
	background:-moz-linear-gradient( center top, #cccccc 5%, #b2b2b2 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#b2b2b2");	background: -o-linear-gradient(top,#cccccc,b2b2b2);

	background-color:#cccccc;
	border:0px solid #000000;
	text-align:center;
	border-width:0px 0px 1px 1px;
	font-size:14px;
	font-family:Arial;
	font-weight:bold;
	color:#000000;
}
.CSSTableGenerator tr:first-child:hover td{
	background:-o-linear-gradient(bottom, #cccccc 5%, #b2b2b2 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #b2b2b2) );
	background:-moz-linear-gradient( center top, #cccccc 5%, #b2b2b2 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#b2b2b2");	background: -o-linear-gradient(top,#cccccc,b2b2b2);

	background-color:#cccccc;
}
.CSSTableGenerator tr:first-child td:first-child{
	border-width:0px 0px 1px 0px;
}
.CSSTableGenerator tr:first-child td:last-child{
	border-width:0px 0px 1px 1px;
}
</style>



<br><br><br>

<div class="CSSTableGenerator" ><table border="0" >
<?php





$collection=new Quotation();
if($login->admin_role=="SM"){
$collection=$collection->getCollection($login->admin_id,'SM');
}else{
$collection=$collection->getCollection($login->admin_id,'SM');
    echo '<h3>Approval Requested</h3>';
}

if(isset($_GET['requests'])){
    $request=new Quotation($_GET['requests']);
    if(isset($_GET['approve'])){
        $request->approve();
    header('location:index.php?requests');
    }elseif(isset($_GET['cancel'])){
        $request->cancel();
    header('location:index.php?requests');
    }elseif(isset($_GET['reconsider'])){
        $request->reconsider();
    header('location:index.php?requests');
    }elseif(isset($_GET['request_approval'])){
        $request->requestApproval();
    header('location:index.php?requests');
    }
}
?>


<table>
    <tr><td>Request Id</td><td>Date Applied</td><td>Expected Discount</td><td>User id and name</td><td>Status</td><td>Action</td></tr>
<?php
foreach($collection as $quotation){
?>
    <tr><td>#<?php echo $quotation->quote_id; ?></td>
        <td><?php echo $quotation->created_at; ?></td>
        <td><?php $discount=$quotation->discount; echo $quotation->discount; ?>%</td>
        <td><?php $user=new User($quotation->user_created); echo "#".$user->user_id." ".$user->user_fullname ?></td>
        <td><?php echo $quotation->status;?></td>
        <td><?php 
        $status=$quotation->status; 
                if ($status == "pending") {
                    $manager = new Admin($_SESSION['admin_id']);
                    if ($manager->admin_threshold_limit > $discount) {
                        echo '<a href="index.php?requests='.$quotation->quote_id.'&approve">Approve</a><br>'
                                . '<a href="index.php?requests='.$quotation->quote_id.'&cancel">Cancel</a>';
                    }else{
                        echo '<a href="index.php?requests='.$quotation->quote_id.'&request_approval">Request Aproval</a><br>'
                                . '<a href="index.php?requests='.$quotation->quote_id.'&cancel">Cancel</a>';
                    }
                } elseif ($status == "cancelled") {
                        echo '<a href="index.php?requests='.$quotation->quote_id.'&reconsider">Reconsider</a>';
                } elseif ($status == "approved") {
                    echo '<a href="index.php?requests='.$quotation->quote_id.'&cancel">Cancel</a>';
                }
                ?></td>
    </tr>
    
<?php    
}
?>
</table>

</div>















<br><br><br>

<div class="CSSTableGenerator" ><table border="0" >
<?php





$collection=new Quotation();
if($login->admin_role=="SM"){
$collection=array();
}else{
    echo "<h3>Other Requests</h3>";
$collection=$collection->getCollection();
}

if(isset($_GET['requests'])){
    $request=new Quotation($_GET['requests']);
    if(isset($_GET['approve'])){
        $request->approve();
    header('location:index.php?requests');
    }elseif(isset($_GET['cancel'])){
        $request->cancel();
    header('location:index.php?requests');
    }elseif(isset($_GET['reconsider'])){
        $request->reconsider();
    header('location:index.php?requests');
    }elseif(isset($_GET['request_approval'])){
        $request->requestApproval();
    header('location:index.php?requests');
    }
}
?>


<table>
    <tr><td>Request Id</td><td>Date Applied</td><td>Expected Discount</td><td>User id and name</td><td>Status</td><td>Action</td></tr>
<?php
foreach($collection as $quotation){
?>
    <tr><td>#<?php echo $quotation->quote_id; ?></td>
        <td><?php echo $quotation->created_at; ?></td>
        <td><?php $discount=$quotation->discount; echo $quotation->discount; ?>%</td>
        <td><?php $user=new User($quotation->user_created); echo "#".$user->user_id." ".$user->user_fullname ?></td>
        <td><?php echo $quotation->status;?></td>
        <td><?php 
        $status=$quotation->status; 
                if ($status == "pending") {
                    $manager = new Admin($_SESSION['admin_id']);
                    if ($manager->admin_threshold_limit > $discount) {
                        echo '<a href="index.php?requests='.$quotation->quote_id.'&approve">Approve</a><br>'
                                . '<a href="index.php?requests='.$quotation->quote_id.'&cancel">Cancel</a>';
                    }else{
                        echo '<a href="index.php?requests='.$quotation->quote_id.'&request_approval">Request Aproval</a><br>'
                                . '<a href="index.php?requests='.$quotation->quote_id.'&cancel">Cancel</a>';
                    }
                } elseif ($status == "cancelled") {
                        echo '<a href="index.php?requests='.$quotation->quote_id.'&reconsider">Reconsider</a>';
                } elseif ($status == "approved") {
                    echo '<a href="index.php?requests='.$quotation->quote_id.'&cancel">Cancel</a>';
                }
                ?></td>
    </tr>
    
<?php    
}
?>
</table>

</div>
