<?php
require_once('main/main_scripts.php');
require_once('classes/user.php');
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


#account_section{
	overflow:hidden;
	height:0px;
}

</style>

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
})
</script>
</head>
<body>
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
            <div class="Account-btn" style="margin-top:20px;margin-right:20px;color:#fff;font-size:15px;float:right"><a href="#" style="color:#fff; text-decoration:none" id="account-btn">MY ACCOUNT</a></div>
	</div>
</div>

<?php include("views/register.php"); ?>


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