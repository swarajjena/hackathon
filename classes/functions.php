<?php

function date_to_days($date){
	$date=explode('-',$date);
	$total_days=0;
	if(isset($date[0]) && isset($date[1]) && isset($date[2])){
	$day=$date[0];
	$month=$date[1];
	$year=$date[2];
	//base year =2013
	
	$total_days=($year-2013)*365;
	$days_of_month=0;
	switch($month){
		case '01': $days_of_month=0; break;
		case '02': $days_of_month=31;break;
		case '03': $days_of_month=59;break;
		case '04': $days_of_month=90;break;
		case '05': $days_of_month=120;break;
		case '06': $days_of_month=151;break;
		case '07': $days_of_month=181;break;
		case '08': $days_of_month=212;break;
		case '09': $days_of_month=243;break;
		case '10': $days_of_month=273;break;
		case '11': $days_of_month=304;break;
		case '12': $days_of_month=334;break;
	}
	$total_days+=$days_of_month+$day;
	}
	return $total_days;
	
}
function day_to_date($days){
	$total_days=$days;
	$ex_yrs=$total_days/365;
	$years=2013+intval($ex_yrs,0);
	$total_days=$total_days-365*intval($ex_yrs,0);
	$month=0;$month_days=0;
	if($total_days>334){$month='december'; $month_days=334;}
	else if($total_days>304){$month='november'; $month_days=304;}
	else if($total_days>273){$month='october'; $month_days=273;}
	else if($total_days>243){$month='september'; $month_days=243;}
	else if($total_days>212){$month='august'; $month_days=212;}
	else if($total_days>181){$month='july'; $month_days=181;}
	else if($total_days>151){$month='june'; $month_days=151;}
	else if($total_days>120){$month='may'; $month_days=120;}
	else if($total_days>90){$month='april'; $month_days=90;}
	else if($total_days>59){$month='march'; $month_days=59;}
	else if($total_days>31){$month='february'; $month_days=31;}
	else{$month='january'; $month_days=0;}
	$total_days=$total_days-$month_days;
	if($total_days<10){$total_days='0'.$total_days;}
	$date=$total_days.' '.$month.' '.$years;
	
	return $date;
	
	
}

?>