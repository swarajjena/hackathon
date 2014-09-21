<?php
require_once('main/main_scripts.php');
require_once('classes/user.php');
require_once('classes/product.php');
require_once('classes/market.php');
require_once('classes/quotation.php');

if(isset($_POST['product_market'])){
   if(isset($_GET['p'])){
      $product=new Product(intval($_GET['p']));
      if($product->product_exists){
          $markets=$product->getMarkets();
		  foreach($markets as $market){
			  if($_POST['market_'.$market->market_id]!=-1){
				  if(isset($_SESSION['pending_cart'][$product->product_id])){
					  if(!isset($_SESSION['pending_cart'][$product->product_id][$market->market_id])){
					  $_SESSION['pending_cart'][$product->product_id][$market->market_id]=$product->getMarketPrice($market->market_id);
					  }
				  }else{
					  $_SESSION['pending_cart'][$product->product_id][$market->market_id]=$product->getMarketPrice($market->market_id);;
				  }
			  }
		  }
		  header('location:index.php?show');
	  }
	  
   }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>SureWaves</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/main_layout.css"  rel="stylesheet" type="text/css"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
 <!--[if lt IE 9]>
     <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
     <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<!-- start plugins -->
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- start slider -->
<link href="css/slider.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="js/modernizr.custom.28468.js"></script>
<script type="text/javascript" src="js/jquery.cslider.js"></script>
	<script type="text/javascript">
			$(function() {

				$('#da-slider').cslider({
					autoplay : true,
					bgincrement : 450
				});

			});
		</script>
<!-- Owl Carousel Assets -->
<link href="css/owl.carousel.css" rel="stylesheet">
<script src="js/owl.carousel.js"></script>
		<script>
			$(document).ready(function() {

				$("#owl-demo").owlCarousel({
					items : 4,
					lazyLoad : true,
					autoPlay : true,
					navigation : true,
					navigationText : ["", ""],
					rewindNav : false,
					scrollPerPage : false,
					pagination : false,
					paginationNumbers : false,
				});

			});
		</script>
		<!-- //Owl Carousel Assets -->
<!----font-Awesome----->
   	<link rel="stylesheet" href="fonts/css/font-awesome.min.css">
<!----font-Awesome----->
</head>
<body>


<?php include_once('main/common_top.php');?>


<style>

#markets{}
#markets_btn{ background:#700;color:#fff; border:none; padding:5px 10px 5px 10px; font-size:18px}
#markets .market_pr{ padding:5px; border:1px solid #000; cursor:pointer}
#markets .market_pr:hover{ background:#ccc;}
#markets .market_name{float:left;font-size:18px;font-weight:bold}
#markets .selected{background:#aaa !important}
#markets .price{float:right;font-size:18px}
#markets .details{clear:both;font-size:16px;float:left}
.lazyOwl{max-height:200px}
</style>
<div align="center">
<?php
if(isset($_GET['p'])){
$product=new Product(intval($_GET['p']));
if($product->product_exists){
?>
						<div class="item" style="width:300px; display:inline-block; margin-top:30px">
							<div class="cau_left">
								<a href="product.php?p=<?php echo $product->product_id;?>"><img class="lazyOwl" src="<?php echo $product->product_image;?>" alt="<?php echo $product->product_name;?>"></a>
							</div>
							<div class="cau_left">
								<h4><a href="product.php?p=<?php echo $product->product_id;?>"><?php echo $product->product_name;?></a></h4>
								<p>
                                <?php echo $product->product_short_details;?>
								</p>
							</div>
						</div>
                        
                        
                        <div id="markets" style="display:inline-block; vertical-align:top; margin-top:30px;width:300px;">
                        <h3>Please Select Market</h3>
                        <form action="product.php?p=<?php echo $product->product_id;?>" method="post">
                        <?php
                        $markets=$product->getMarkets();
						foreach($markets as $market){
						?>
                        <div class="market_pr" >
                        <div class="market_name"><?php echo $market->market_name;?></div>
                        <div class="price">Rs. <?php echo $product->getMarketPrice($market->market_id);?></div>
                        <br>
                        <div class="details">Details : <?php echo $market->market_details;?></div><br>
                        <input type="hidden" id="" class="hidden_fld" name="market_<?php echo $market->market_id;?>" value="-1"/>
<br>
<br>                        
                        </div>
                        	
                        <?php    
						}
						?>
                        <br>

                        <input type="submit" value="Continue" id="markets_btn" name="product_market">
                        
                        </form>

                        </div>
                        
<script>
$(function(){
	$('.market_pr').click(function(){
		if($(this).hasClass('selected')){
			$(this).children('.hidden_fld').val('-1');
			$(this).removeClass('selected')
		}else{
			$(this).addClass('selected');
			$(this).children('.hidden_fld').val('1');
		}
	})
})
</script>                        
<?php	
}else{
	echo '<h2 align="center"><br><br>Product does not exist<br><br><br><br><br><br><br><br><br></h2>';
}}else{
	echo '<h2 align="center"><br><br>Product does not exist<br><br><br><br><br><br><br><br><br></h2>';
}

?>

</div>
<div class="footer_bg"><!-- start footer -->
	<div class="container">
		<div class="row  footer">
			<div class="copy text-center">
				<p class="link"><span>Design by Swaraj</span></p>
			</div>
		</div>
	</div>
</div>
</body>
</html>