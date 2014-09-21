<?php

function encry($string){
	$key='s';
	$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
    return $encrypted;
}

function decry($string){
	$key='s';
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	return $decrypted;

}

?>
<?php 
function imageResize($width, $height, $target) {

//takes the larger size of the width and height and applies the
//formula accordingly...this is so this script will work
//dynamically with any size image

if ($width > $height) {
$percentage = ($target / $width);
} else {
$percentage = ($target / $height);
}

//gets the new value and applies the percentage, then rounds the value
$width = round($width * $percentage);
$height = round($height * $percentage);

//returns the new sizes in html image tag format...this is so you
//can plug this function inside an image tag and just get the

return 'width='.$width.' height='.$height;

}

 
function sanitize_text($string){
	$comment = $string;
    // sanitize comment
    $comment = filter_var($comment, FILTER_SANITIZE_STRING);
    return $comment;
}
?>