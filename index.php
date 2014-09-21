<?php
require_once('main/main_scripts.php');
require_once('classes/user.php');
require_once('classes/product.php');
require_once('classes/market.php');
require_once('classes/quotation.php');
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
<style> 
    .lazyOwl{
        max-height:150px;
    }
</style>
</head>
<body>
<div  style="margin-left:auto; margin-right:auto; ">
<?php include_once('main/common_top.php');?>
</div>
<div class="main_btm"><!-- start main_btm -->
	<div class="container">
				<!----start-img-cursual---->
					<div id="owl-demo" class="owl-carousel text-center">
                        <?php
						$collection=new Product();
						$collection=$collection->getCollection();
						foreach($collection as $product){
							?>
						<div class="item">
							<div class="cau_left">
								<a href="product.php?p=<?php echo $product->product_id;?>"><img class="lazyOwl" data-src="<?php echo $product->product_image;?>" alt="<?php echo $product->product_name;?>"></a>
							</div>
							<div class="cau_left">
								<h4><a href="product.php?p=<?php echo $product->product_id;?>"><?php echo $product->product_name;?></a></h4>
								<p>
                                <?php echo $product->product_short_details;?>
								</p>
							</div>
						</div>
                            <?php
						}
						?>
                        
					</div>
					<!----//End-img-cursual---->
	</div>
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
            <?php if(!$login->isUserLoggedIn()){?>

<script>
$(function(){
	$('.owl-carousel .item').click(function(){
	$('#account_section').stop().animate({'height':'500px'});
	$('#account_section').attr('opened','opened');
    return false;
    });
})
</script>
<?php }?>
</body>
</html>