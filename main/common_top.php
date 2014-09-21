<?php if(isset($_GET['clean'])){
	unset($_SESSION['pending_cart']);
	}
	
	if(isset($_GET['submit']) && isset($_GET['discount'])){
$file = fopen("test.txt","w");
echo fwrite($file,"Hello World. Testing!".$discount);
fclose($file);		$quotation=new Quotation();
                if(isset($_GET['discount']) && $_GET['discount']<100){$discount=intval($_GET['discount']);}
                else{$discount=10;}
		$quotation->generateQuotation($discount);
		header('location:index.php?qsuccess');
	}
	?>
<style>
@media screen and (max-width:720px){	
.Account-btn{
	position:absolute !important;top:0px !important;right:0px;margin-top:15px !important;
}
#login_box{
	display:block;
}
#registration_box{
	display:block;
}

}

#cart_tbl td{padding:5px; font-size:17px}

#account_section{
	overflow:hidden;
	height:0px;
}

</style>


<div class="header_bg">
<div class="container">
	<div class="row header">
		<div class="logo navbar-left">
			<h1><a href="index.html">SureWaves</a></h1>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
</div>
<div class="container">
	<div class="row h_menu">
		<nav class="navbar navbar-default navbar-left" role="navigation">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		    </div>
		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav">
		        <li class="active"><a href="index.php">Home</a></li>
		        <li><a href="#">About Us</a></li>
		        <li><a href="#">In the news</a></li>
		        <li><a href="#">Contact Us</a></li>
		      </ul>
		    </div><!-- /.navbar-collapse -->
            
            
		    <!-- start soc_icons -->
		</nav>
            <div class="Account-btn" style="margin-top:20px;margin-right:20px;color:#fff;font-size:15px;float:right"><a href="#" style="color:#fff; text-decoration:none" id="account-btn">
            <?php if($login->isUserLoggedIn()){?>
            MY ACCOUNT
            <?php }else{
				echo 'LOGIN / REGISTER';
		    }?>
        </a></div>
	</div>
</div>


<div id="account_section" align="center">

<?php 
if($login->isUserLoggedIn()){
include_once('main/profile.php');
   if(isset($_SESSION['pending_cart'])){
	   echo '
	   <h3>Pending Request</h3>
	   <table border="1" id="cart_tbl">';
	   echo '<tr><td><strong>Product</strong></td><td><strong>Market</strong></td><td><strong>Price</strong></td></tr>';
	   foreach($_SESSION['pending_cart'] as $k=>$prd){
		   $product=new Product($k);
		   if($product->product_exists){
		       echo '<tr><td style="font-size:18px">'.$product->product_name.'</td><td style=" font-size:14px" title="">';
			   $markets=$prd;
			   foreach($markets as $market=>$price){
				   $market=new Market($market);
				   if($market->market_exists){
					   echo '<span title="'.$market->market_details.'">'.$market->market_name.'</span><br>';
				   }
			   }
			   echo '</td><td>';
			   foreach($markets as $market=>$price){
				   $market=new Market($market);
				   if($market->market_exists){
					   echo '<span title="'.$market->market_details.'">'.$price.'</span><br>';
				   }
			   }
			   echo '</td></tr>';
			   
		   }
	   }
	   
	   echo '</table>';
	   ?>
       <form action="" method="get">
       <h4>Expected Discount : <input type="text" size="3"  value="10" name="discount" style="font-size:16px; display:inline"/>%</h4>
       <input type="submit" value="Submit Request for Quotation" name="submit"> <a href="?clean"><button type="button">Clear all</button></a>
       </form>
       <?php
	   
   }else{
	   
	   if(isset($_GET['qsuccess'])){
		   echo '<h4 style="color:#0a0">Successfully submitted request for Quotation.Thank you</h4>';
	   }
	   $collection=new Quotation();
	   $collection=$collection->getCollection($_SESSION['user_id']);
	   if(sizeof($collection)==0){
		   echo '<h3>No Quotation Requests,Please add one</h3>';
	   }else{
	   }
	   ?>
       
       <h3>My Quotation Requests</h3>
       <table border="1" id="cart_tbl">
  <tr>
    <td>Request Id</td>
    <td>Date Applied</td>
    <td>Details</td>
    <td>Expected Discount</td>
    <td>Status</td>
  </tr>
       <?php
	   foreach($collection as $quotation){
?>
  <tr>
    <td>#<?php echo $quotation->quote_id;?></td>
    <td><?php echo $quotation->created_at;?></td>
    <td><a href="#view">View</a></td>
    <td><?php echo $quotation->discount;?>%</td>
    <td><?php echo $quotation->status;?></td>
  </tr>

<?php		   
	   }
	   
	   
	   
	   ?>
</table>

       <?php
   }
}else{
include_once('main/login_reg_html.php');
}
?>
</div>


<script type="text/javascript">
$(function(){
	
	$('#account-btn').hover(function(){
    if($('#account_section').attr('opened')!='opened'){
	$('#account_section').stop().animate({'height':'100px'});
	}
    },function(){
    if($('#account_section').attr('opened')!='opened'){		
	$('#account_section').stop().animate({'height':'0px'});
	}
    })
	$('#account-btn').click(function(){
    if($('#account_section').attr('opened')=='opened'){
	$('#account_section').stop().animate({'height':'0px'});
	$('#account_section').removeAttr('opened');
	}else{
	$('#account_section').stop().animate({'height':'500px'});
	$('#account_section').attr('opened','opened');
	}
    })
	$('#registration_box').css('display','none');
	$('#open_register').click(function(){
		$('#registration_box').css('display','inline-block !important');
		$('#login_box').css('display','none');
    })
	$('#open_login').click(function(){
		$('#login_box').css('display','inline-block !important');
		$('#registration_box').css('display','none');
    })
    if(window.location.href.indexOf("register") > -1) {
		$('#registration_box').css('display','inline-block !important');
		$('#login_box').css('display','none');
	    $('#account_section').css('height','500px');
	    $('#account_section').attr('opened','opened');
    }
    if(window.location.href.indexOf("login") > -1) {
		$('#login_box').css('display','inline-block !important');
		$('#registration_box').css('display','none');
	    $('#account_section').css('height','500px');
	    $('#account_section').attr('opened','opened');
    }
    if(window.location.href.indexOf("show") > -1) {
	    $('#account_section').css('height','500px');
	    $('#account_section').attr('opened','opened');
    }
    if(window.location.href.indexOf("qsuccess") > -1) {
	    $('#account_section').css('height','500px');
	    $('#account_section').attr('opened','opened');
    }
	
	
	})
</script>
