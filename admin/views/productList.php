<?php
$product=new Product();
$product_name='';$product_image=''; $product_short_details='';
if(isset($_GET['products']) && $_GET['products'] !=''){
    $product->find_product($_GET['products']);
    if($product->product_exists){
        $product_name=$product->product_name;
        $product_short_details=$product->product_short_details;
        $product_image=$product->product_image;
        if(isset($_POST['product_name'])){
		    	if($product->product_image==''){
			    	$rand=round(rand()*10000,4);
    			    $product->product_image= preg_replace('/[^a-zA-Z0-9\']/','_',$_POST['product_name']).$rand;
    			    $product->product_image="images/product/".$product->product_image.'.jpg';
			    }
			    if(isset($_FILES['product_image_new']) && $_FILES['product_image_new']['name']!='' 
			    && ($_FILES["product_image_new"]["type"] == "image/jpeg") ){    
			   	    move_uploaded_file($_FILES["product_image_new"]["tmp_name"],'../'.$product->product_image);
			     }
				 
			     $product->update_product();	
                            header('location:index.php?productskjjknjkn='.$product->product_id);
            
        }

    }
}

if(isset($_POST['product_name'])){
		    	if($product->product_image==''){
			    	$rand=round(rand()*10000,4);
    			    $product->product_image= preg_replace('/[^a-zA-Z0-9\']/','_',$_POST['product_name']).$rand;
    			    $product->product_image="images/product/".$product->product_image.'.jpg';
			    }
			    if(isset($_FILES['product_image_new']) && $_FILES['product_image_new']['name']!='' 
			    && ($_FILES["product_image_new"]["type"] == "image/jpeg") ){
			   	    move_uploaded_file($_FILES["product_image_new"]["tmp_name"],'../'.$product->product_image);
			     }
			
			    $product->product_id= $product->add_product();
                            header('location:index.php?products='.$product->product_id);
    
}

?>

<?php

$collection=new Product();
$collection=$collection->getCollection();

foreach($collection as $product){
?>
<div width="200" style="display: block;width:100%; height:100px ">
<a href="index.php?products=<?php echo $product->product_id;?>">
<img src="../<?php echo $product->product_image;?>" height="80px"  style="float: left"><span  style="float: left"><?php echo $product->product_name?></span><br>
<span  style="float: left"><?php echo $product->product_details;?></span>
</a><br>
</div>    
    <?php
}
