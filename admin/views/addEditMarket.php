<?php
$market=new Market();
$market_name='';$market_image=''; $market_details='';
if(isset($_GET['markets']) && $_GET['markets'] !=''){
    $market->find_market($_GET['markets']);
    if($market->market_exists){
        $market_name=$market->market_name;
        $market_details=$market->market_details;
    }
}
?>

<form action="" method="post" enctype="multipart/form-data">
    
    <table>
        <tr>
            <td>Market Name</td><td><input type="text" name="market_name" value="<?php echo $market_name?>"></td>
        </tr>
        <tr>
            <td>Market Details</td><td><textarea name="market_details" ><?php echo $market_details; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit"/></td>
        </tr>
    </table>
    
</form>


