		</div>	
	</div>
<div id="footer">
<div style=" display:none">
     <div id="about_us" class="height100" align="center">
     <div style="width:500px; text-align:justify">
     <?php include_once('views/about_us.php');?>
     </div>
     </div>

     <div id="contact_us" class="height100" align="center">
     <div style="width:500px; text-align:justify">
     <?php include_once('views/contact_us.php');?>
     </div>
     </div>

     <div id="disclaimer" class="height100" align="center">
     <div style="width:500px; text-align:justify">
     <?php include_once('views/disclaimer.php');?>
     </div>
     </div>

     <div id="suggestions" class="height100" align="center">
     <div style="width:500px; text-align:justify">
     <?php include_once('views/suggestions.php');?>
     </div>
     </div>
</div>
    <div id="footer_content">
	    <a href="#about_us" class="inline">About us</a> &nbsp;&nbsp;&nbsp; 
        <a href="#contact_us" class="inline">Contact us</a> &nbsp;&nbsp;&nbsp;  
        <a href="#disclaimer" class="inline">Disclaimer</a> &nbsp;&nbsp;&nbsp; 
        <a href="#suggestions" class="inline">Suggestions / Feedback</a> &nbsp;&nbsp;&nbsp;
<br />
		<div class="line2"></div>
<h2>
<?php
if($login->user_rank >=10 ){
	echo '<a href="audomin">ADmin Panel</a><br />';
}
?>
</h2>			
<div style="color:#000">&copy; Copyright 2013  &minus; Indiaspolitics </div>    


     </div>
</div>	

