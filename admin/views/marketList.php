<?php
$market=new Market();
$market_name='';$market_image=''; $market_details='';
 if (isset($_GET['markets']) && $_GET['markets'] != '') {
    $market->find_market($_GET['markets']);
    if ($market->market_exists) {
        $market_name = $market->market_name;
        $market_details = $market->market_details;
        if (isset($_POST['market_name'])) {

            $market->update_market();
            header('location:index.php?marketskjjknjkn=' . $market->market_id);
        }
    }
}

if(isset($_POST['market_name'])){
			    $market->market_id= $market->add_market();
                            header('location:index.php?markets='.$market->market_id);
}

?>

<?php

$collection=new Market();
$collection=$collection->getCollection();

foreach($collection as $market){
?>
<div width="200" style="display: block;width:100%; height:50px ">
<a href="index.php?markets=<?php echo $market->market_id;?>"><span  style="float: left"><?php echo $market->market_name?></span><br>
<span  style="float: left"><?php echo $market->market_details;?></span>
</a><br>
</div>    
    <?php
}
