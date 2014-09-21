<?php
$product=new Product();
$product_name='';$product_image=''; $product_short_details='';
if(isset($_GET['products']) && $_GET['products'] !=''){
    $product->find_product($_GET['products']);
    if($product->product_exists){
        $product_name=$product->product_name;
        $product_short_details=$product->product_short_details;
        $product_image=$product->product_image;
    }
}
$collection=new Market();
$collection=$collection->getCollection();


if(isset($_POST['assign'])){
foreach($collection as $market){
    if($_POST['price_'.$market->market_id]!='' && $_POST['price_'.$market->market_id] !=0){
    $product->update_market_value($market->market_id,$_POST['price_'.$market->market_id]);
    }
}    
header('location:index.php?products='.$product->product_id);
}

?>

<form action="" method="post" enctype="multipart/form-data">
    
    <table>
        <tr>
            <td>Product Name</td><td><input type="text" name="product_name" value="<?php echo $product_name?>"></td>
        </tr>
        <tr>
            <td>Product Image</td><td><input type="file" name="product_image_new"></td>
        </tr>
        <tr>
            <td>Product Details</td><td><textarea name="product_short_details" ><?php echo $product_short_details; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit"/></td>
        </tr>
    </table>
    
</form>


<style>
    .CSSTableGenerator {
	margin:0px;padding:0px;
	width:80%;
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


<?php
    if($product->product_exists){
?>
<br><br><br>
<form action="index.php?products=<?php echo $product->product_id;   ?>" method="post">    

<div class="CSSTableGenerator" ><table border="0" >
    <tr>
        <td>Market Name</td>
        <td>Market Details</td>
        <td>product price in the market</td>
    </tr>    
<?php

foreach($collection as $market){
    
$product_market_price=$product->getMarketPrice($market->market_id);
?>
    <tr>
        <td><?php echo $market->market_name;?></td>
        <td><?php echo $market->market_details;?></td>
        <td style="padding:0px">
            <input type="text" name="price_<?php echo $market->market_id;?>" value="<?php echo $product_market_price;?>">
        </td>
    </tr>    

<?php    
}
?>
</table>
</div>
    <input type="submit" name="assign" value="Assign" style="padding:10px;text-align: center">
<?php
    }
?>