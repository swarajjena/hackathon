<style>
.menu_bar a{ text-decoration: none; margin:10px;color:#000; font-size: 17px}    
.menu_bar a:hover{ color:#00f; }    
.menu_bar a.selected{ color:#00f; }    
</style>


<div align="center">
<table border="0" width="1000">
    <tr height="70" style="background: #ccc">
        <td colspan="2" align="center">
            <b>Admin Panel</b><br><a href="?logout">Logout</a>
        </td>
    </tr>
    <tr height="30" style="background: #eee">
        <td colspan="2" align="center" class="menu_bar">
<!--            <a href="index.php"> Home </a>-->
            <a href="?customers" <?php if(isset($_GET['customers'])){echo 'class="selected"';}?> > Customers </a>
            <a href="?requests" <?php if(isset($_GET['requests'])){echo 'class="selected"';}?> > Quotation Requests & approved</a>
<?php if($login->admin_role=='NH'){ ?><a href="?managers" <?php if(isset($_GET['managers'])) {echo 'class="selected"';}?> >Managers</a><?php } ?>
<?php if($login->admin_role=='NH'){ ?>  <a href="?products" <?php if(isset($_GET['products'])){echo 'class="selected"';}?> >Products</a><?php } ?>
<?php if($login->admin_role=='NH'){ ?> <a href="?markets" <?php if(isset($_GET['markets'])){echo 'class="selected"';}?> >Markets</a><?php } ?>
        </td>
    </tr>
    <tr>
        <td width="200">
        <?php 
        if(isset($_GET['customers'])){
//                        include 'views/customersList.php';                        
        }elseif(isset($_GET['managers'])){
                        include 'views/managersList.php';                        
        }elseif(isset($_GET['products'])){
                        include 'views/productList.php';                                                
        }elseif(isset($_GET['markets'])){
                        include 'views/marketList.php';                                    
        }
        ?>
        
        </td>
        <td>
        <?php 
        if(isset($_GET['customers'])){
                        include 'views/customersList.php';                        
        }elseif(isset($_GET['requests'])){
                        include 'views/QuotationList.php';                        
        }elseif(isset($_GET['managers'])){
                        include 'views/addEditManager.php';                        
        }elseif(isset($_GET['products'])){
                        include 'views/addEditProduct.php';                                                
        }elseif(isset($_GET['markets'])){
                        include 'views/addEditMarket.php';                                    
        }
        ?>
            
        </td>
    </tr>
</table>
</div>