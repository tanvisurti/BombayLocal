<?php
$path = $_SERVER['DOCUMENT_ROOT'];
$path .= "/sqlconnect.php";
include_once($path);
date_default_timezone_set('Asia/Kolkata');

///////////////////////******************* functions ********************///////////////////
function addworkinday($timestamp,$daystoadd){ 
     
     $dayoftheweek = date("N",$timestamp);
     $sum =$dayoftheweek +$daystoadd; 
     
 while ($sum >= 6) { 
     
     $daystoadd=$daystoadd+1; 
    $sum=$sum-1; 
} 
 return $timestamp +(60*60*24*$daystoadd); 

 } 

///////////////////////******************* find all variables ********************///////////////////
//find latitude and logitude
$area1=$_GET['area1'];
$area2=$_GET['area2'];
$area3=$_GET['area3'];
if ($area3=='' || $area3=='null'){
	if ($area2=='' || $area2=='null'){
		if ($area1=='' || $area1=='null'){
			$titlearea = ' in Mumbai';
			$place='none';
		}
		else {
			$titlearea = " in ".$area1;
			$result_area = mysql_query("SELECT AVG(x), AVG(y) FROM areas WHERE area1='$area1'");
		}
	}
	else {
		$titlearea = " at ".$area2;
		$result_area = mysql_query("SELECT AVG(x), AVG(y) FROM areas WHERE area1='$area1' AND area2='$area2'");
	}
}
else { 
	$titlearea = " at ".$area2;
	$result_area = mysql_query("SELECT AVG(x), AVG(y)  FROM areas WHERE area1='$area1' AND area2='$area2' AND area3='$area3'");
	}

if ($place !='none'){
	while($row_area = mysql_fetch_array($result_area)){
		$x = $row_area['AVG(x)'];
		$y = $row_area['AVG(y)'];
	}
}

//find type
$category =$_GET['category'];

//Generate Title
$entiretime = getdate();
$weekdate = $entiretime['weekday'];
$minutes = $entiretime['minutes'];
if ($minutes < 10) $minutes = '0'.$minutes;
$time = $entiretime['hours'];
$day = strtolower(substr($entiretime['weekday'], 0, 3));
$today = strtotime(date("Y-m-d"));
if ($time > 12) $titletime = ($time-12).':'.$minutes.' PM'; else $titletime = ($time).':'.$minutes.' AM';

$title = "Stuff to do at ".$titletime." on ".$weekdate.$titlearea;

///////////////////////******************* ***************** ********************///////////////////
///////////////////////******************* ***************** ********************///////////////////
//STEP 1 - CALCULATE DISTANCES
$placearray = '';
$x = $x; $y = $y;
$result = mysql_query("SELECT * FROM places");
while($row = mysql_fetch_array($result)){
	if ($place !='none'){
		//first find x and y coordinates of thee area
		$areaID= $row['area'];
		$result2 = mysql_query("SELECT x, y FROM areas WHERE ID='$areaID'");
		while($row2 = mysql_fetch_array($result2)){ $placex = $row2['x']; $placey = $row2['y'];}
	
		//second use these x and y coordinates to find the difference
		$placex = $placex; $placey = $placey; 
		$dif = pow(pow(($x - $placex),2) + pow(($y - $placey),2),0.5);
		if ($dif==0) {$score==100;} else $score = 100/$dif;
		//echo $score.'<br>';
	}
	else $score = 10;
	
	//insert into array
	$id = $row['ID'];
	$type = $row['type'];
	$placearray[] = array('score' => $score, 'id' => $id, 'type' => $type);
}

//STEP3 - TIME MATCHING - later, integrate with above 
for ($c = 0; $c < sizeof($placearray); $c++){
	
	$type = $placearray[$c]['type'];
	if ($time >=  19 || $time <= 2) $type = str_replace('bar','1.7',$type);
	if ($time >=  22 || $time <= 3) $type = str_replace('club','1.7',$type);
	if ($time >=  17 || $time <= 2) $type = str_replace('theatre','1.7',$type);
	if ($time >=  8 && $time <= 10) $type = str_replace('coffeeshop','1.7',$type);
	elseif ($time >=  15 && $time <= 18) $type = str_replace('coffeeshop','1.7',$type);
	elseif ($time >=  8 && $time <= 23) $type = str_replace('coffeeshop','1.5',$type);
	if ($time >=  22 || $time <= 3) $type = str_replace('lounge','1.7',$type);
	$type = str_replace('public place','1.3',$type);	
	$type = str_replace('misc.','1.3',$type);
	if ($time >= 11 && $time <= 23) $type = str_replace('cinema hall','1.5',$type);
	if ($time >=  9 && $time <= 10) $type = str_replace('restaurant','1.7',$type);
	elseif ($time >=  11 && $time <= 14) $type = str_replace('restaurant','1.7',$type);
	elseif ($time >=  20 && $time <= 23) $type = str_replace('restaurant','1.7',$type);
	elseif ($time >=  8 && $time <= 24) $type = str_replace('restaurant','1.5',$type);
	
	$type = explode(',',$type);
	array_multisort($type, SORT_NUMERIC, SORT_DESC);
	$score = $score * $type[0];
}

//STEP 5 - EVENTS
$eventarray = '';
$result = mysql_query("SELECT * FROM events");
while($row = mysql_fetch_array($result)){
	//See if day matches
	if ($row['recurrence'] == 1){
		if ($row['weeks'] != 'infinity'){
			$date = strtotime($row['date']); //convert date part to timestamp //NEED TO FIX THIS DATE SHIT
			$date = addworkinday($date, $row['weeks']*7); //add days to the timestamp
			$today = strtotime(date("Y-m-d"));
			if ($date >= $today) {
				if (stristr($row['days'],$day) != '') $valid = "yes"; else $valid = "no";
			}
		}
		else {
			if (stristr($row['days'],$day) != '') $valid = "yes"; else $valid = "no";
		}
	}
	else {
		$date = strtotime($row['date']);
		if ($date == $today) $valid = "yes"; else $valid = "no";
	}
	
	//See if time matches
	if ($valid == "yes"){
		//convert start time and end time to int
		$starttime = explode(":",$row['starttime']);
		$starttime = $starttime[0];
		if (substr($row['starttime'], -2) == 'PM') $starttime = $starttime + 12;
		$endtime = explode(":",$row['endtime']);
		$endtime = $endtime[0];
		if (substr($row['endtime'], -2) == 'PM') $endtime = $endtime + 12;

		//check if current time is in this range
		if ($starttime < $endtime){
			if ($time >= ($starttime-1) && $time <= $endtime) $validtime = "yes";
		}
		elseif ($endtime < $starttime){
			if ($time >= ($starttime-1) || $time <= $endtime) $validtime = "yes";
		}
		elseif ($endtime == $starttime){
			if ($time >= ($starttime-1) && $time <= $starttime) $validtime = "yes";
		}
	}
	
	//Add those events to an array and give them scores
	if ($valid== "yes" && $validtime == "yes"){
		$id = $row['ID'];
		$place = $row['place'];
		$type = $row['type'];
		$eventarray[] = array('score' => 500, 'id' => $id, 'place' => $place, 'type' => $type);
	}
}

//STEP 6 - BE TYPE SPECIFIC
$category=$_GET['category'];
if ($category != ''){
	for ($b = 0; $b < sizeof($placearray); $b++ ){
		$type = $placearray[$b]['type'];
		$type = explode(",", $type);
		$food = array('restaurant','coffee shop','mall');
		$booze = array('bar','club','lounge');
		$culture = array('cinema hall','mall','theatre', 'public place');
		$timepass = array('cinema hall','mall','theatre', 'public place', 'coffee shop', 'bar');
		if ($category == 'Food' && sizeof(array_intersect($type, $food)) == 0){
			unset($placearray[$b]);}
		if ($category == 'Booze' && sizeof(array_intersect($type, $booze)) == 0){
			unset($placearray[$b]);}
		if ($category == 'Culture' && sizeof(array_intersect($type, $culture)) > 0){
			$placearray[$b]['score'] = $placearray[$b]['score']*1.5;}
		if ($category == 'timepass' && sizeof(array_intersect($type, $timepass)) > 0){
			$placearray[$b]['score'] = $placearray[$b]['score']*1.5;}
	}

	if ((sizeof($eventarray) > 1) && ($category == 'Timepass' || $category == 'Culture')){
		for ($c = 0; $c < sizeof($eventarray); $c++ ){
			$eventarray[$c]['score'] = $eventarray[$c]['score']*1.5;
		}
	}
}

//STEP 7 - COMBINE PLACE AND EVENT ARRAYS
$masterarray = '';
if ($eventarray != ''){
	foreach ($eventarray as $key2 => $row2) {
		$placeIDarray[$key2]  = $row2['place'];
	}
	array_multisort($placeIDarray, SORT_ASC, $eventarray);
}


foreach ($placearray as $key => $row) {
    $IDarray[$key]  = $row['id'];
}
array_multisort($IDarray, SORT_ASC, $placearray);

for ($x=0; $x< sizeof($placearray); $x++){
	$y = 0; $entered = false;
	if ($eventarray != ''){
		while ($y< sizeof($eventarray)){
			if ($placearray[$x]['id'] == $eventarray[$y]['place']){
				$masterarray[] = array('score' => ($placearray[$x]['score'] + $eventarray[$y]['score']), 'placeid' => $placearray[$x]['id'], 'eventid' => $eventarray[$y]['id']);
				$entered = true;
				$y += 1; 
			}
			else if ($placearray[$x]['id'] > $eventarray[$y]['place']) $y += 1; 
			else $y = sizeof($eventarray);
		}
	}
	if (!$entered){
		$masterarray[] = array('score' => $placearray[$x]['score'], 'placeid' => $placearray[$x]['id'], 'eventid' => '');
	}
}

//STEP 8 - SORT ARRAY
foreach ($masterarray as $key => $row) {
    $masterarray_score[$key]  = $row['score'];
}
array_multisort($masterarray_score, SORT_DESC, $masterarray);

///////////////////////******************* actually print the results ********************///////////////////
$count = 16;
if (sizeof($masterarray) < $count) $count = sizeof($masterarray);
for ($a = 0; $a < $count; $a++){

	if ($masterarray[$a]['eventid'] == ''){	
		//Grab the right result
		$ID = $masterarray[$a]['placeid'];
		$result = mysql_query("SELECT * FROM places WHERE ID='$ID'"); 
		$row = mysql_fetch_array($result);
		
		//if Place is valid
		if ($row['duplicate'] == 0 && $row['verified'] == 0){

			//grab all fields
			$ID = $row['ID'];
			$name = $row['name'];
			$photo = $row['photo'];
			$type =  substr($row['type'], 0, -1);
			$areaID =  $row['area'];
			$address =  $row['address'];	
			$phone =  $row['contact'];
			$description =  $row['description'];
			$googlemaps =  $row['googlemaps'];
			$link1 =  $row['link1'];
			$link2 =  $row['link2'];
			$link3 =  $row['link3'];
			$tagsID =  $row['tags'];	
			$budgetID =  $row['budget'];	
			$musttry =  $row['musttry'];
			
			//Retrieve Area name
			$result2 = mysql_query("SELECT * FROM areas WHERE ID='$areaID'");
			if (mysql_num_rows($result2) == 1){
				$row2 = mysql_fetch_array($result2);
				$area2 = $row2['area2'];
				$area3 = $row2['area3'];
				if ($area2 == $area3) {
					$area = $area2;
				}
				else $area = $area3. ', '.$area2;
			}
			
			//assign budget value
			if ($budgetID == 0) $budget = 'unknown';
			elseif ($budgetID == 1) $budget = 'cheap';
			elseif ($budgetID == 2) $budget = 'affordable';
			elseif ($budgetID == 3) $budget = 'pricey';
			elseif ($budgetID == 4) $budget = 'ouch';
			
			//find tags
			$tagArray = '';
			$tag = '';
			$limit = 0; $i = 0;
			$tagArray = explode( ',', $tagsID);
			if (sizeof($tagArray) < 5) $limit = sizeof($tagArray); else $limit = 5;
			for ($i ==0; $i< $limit; $i++){
				$result3 = mysql_query("SELECT tag FROM tags WHERE ID='$tagArray[$i]' AND type='place'");
				if (mysql_num_rows($result3) == 1){
					$row3 = mysql_fetch_array($result3);
					$tag .= $row3['tag'].', ';
				}
			}
			$tag = strtoupper(substr($tag, 0, -2));
			
			//find photo file
			$file = 'images/'.$ID.'.';
			if (file_exists($file.'jpg')) $file = $file.'jpg';
			elseif (file_exists($file.'gif')) $file = $file.'gif';
			elseif (file_exists($file.'png')) $file = $file.'png';
			elseif (file_exists($file.'jpeg')) $file = $file.'jpeg';
			else $file = 'images/null.jpg';
			
			//find links
			$links = '';
			if ($googlemaps != '') $links .= '<a href="'.$googlemaps.'">google map</a> |';
			if ($link1 != '') $links .= '<a href="'.$link1.'">more info</a> |';
			if ($link2 != '') $links .= '<a href="'.$link1.'">website</a> |';
			if ($link3 != '') $links .= '<a href="'.$link3.'">review</a> |';
			$links = substr($links, 0, -2);
			
			//print cell
			$recommendations .= '<td class=main><div class=eventtag></div>
								<div class=name><h3>'.$name.'</h3></div>
								<div class=primary><h2>'.$type.'<br><h3> in '.$area.'</h3></div>
								<div class=secondary><p>'.$description.'<br>';
			if ($musttry != '') $recommendations .= 'must try: '.$musttry.'<br>';
			$recommendations .= 'budget: '.$budget.'</p></div>
								<div class=tag><p>'.$tag.'</p></div>
								<div class=picture><img style="float:center" src="'.$file.'" width="195" height="180" /></div>
								<div class=link1><p>'.$address.'<br>';
			if ($phone != '') $recommendations .= 'ph: '.$phone.'</div>'; else $recommendations .= '</div>';
			$recommendations .= '<div class=link2><p align=right>'.$links.'</p></div>
								</div></td>';
		}
	}
	else {
		//if Event
		$ID = $masterarray[$a]['placeid'];
		$eventID = $masterarray[$a]['eventid'];
		$result = mysql_query("SELECT * FROM places WHERE ID='$ID'"); 
		$row = mysql_fetch_array($result);
		$result2 = mysql_query("SELECT * FROM events WHERE ID='$eventID'"); 
		$row2 = mysql_fetch_array($result2);

		
		//if Place is valid
		if ($row['duplicate'] == 0 && $row['verified'] == 0){
			//grab all fields
			$name = $row['name'];
			$type =  substr($row['type'], 0, -1);
			$areaID =  $row['area'];
			$address =  $row['address'];	
			$contact =  $row['contact'];
			$googlemaps =  $row['googlemaps'];
			$link1 =  $row['link1'];
			$link2 =  $row['link2'];
			$link3 =  $row['link3'];
			$tagsID =  $row['tags'];	
			$budgetID =  $row['budget'];	
			$musttry =  $row['musttry'];
			$eventname = $row2['name'];
			$eventtype = $row2['type'];
			$eventstart = $row2['starttime'];
			$eventend = $row2['endtime'];
			if ($row2['description'] == '') $description =  $row['description']; else $description =  $row2['description'];
			if ($row2['link'] == '') $link1 =  $row['link1']; else $link1 =  $row2['link'];
			
			//Retrieve Area name
			$result2 = mysql_query("SELECT * FROM areas WHERE ID='$areaID'");
			if (mysql_num_rows($result2) == 1){
				$row2 = mysql_fetch_array($result2);
				$area2 = $row2['area2'];
				$area3 = $row2['area3'];
				if ($area2 == $area3) {
					$area = $area2;
				}
				else $area = $area2;
			}
			
			//assign budget value
			if ($row2['cost'] == '') { 
			if ($budgetID == 0) $budget = 'unknown';
			elseif ($budgetID == 1) $budget = 'cheap';
			elseif ($budgetID == 2) $budget = 'affordable';
			elseif ($budgetID == 3) $budget = 'pricey';
			elseif ($budgetID == 4) $budget = 'ouch'; }
			else $budget =  $row2['cost'];
			
			//find tags
			$tagArray = '';
			$tag = '';
			$limit = 0; $i = 0;
			$tagArray = explode( ',', $tagsID);
			if (sizeof($tagArray) < 5) $limit = sizeof($tagArray); else $limit = 5;
			for ($i ==0; $i< $limit; $i++){
				$result3 = mysql_query("SELECT tag FROM tags WHERE ID='$tagArray[$i]' AND type='place'");
				if (mysql_num_rows($result3) == 1){
					$row3 = mysql_fetch_array($result3);
					$tag .= $row3['tag'].', ';
				}
			}
			$tag = strtoupper(substr($tag, 0, -2));
			
			//find photo file
			$file =  'images/events/'.$eventID.'.';
			if (file_exists($file.'jpg')) $file = $file.'jpg';
			elseif (file_exists($file.'gif')) $file = $file.'gif';
			elseif (file_exists($file.'png')) $file = $file.'png';
			elseif (file_exists($file.'jpeg')) $file = $file.'jpeg';
			else {
				$file = 'images/'.$ID.'.';
				if (file_exists($file.'jpg')) $file = $file.'jpg';
				elseif (file_exists($file.'gif')) $file = $file.'gif';
				elseif (file_exists($file.'png')) $file = $file.'png';
				elseif (file_exists($file.'jpeg')) $file = $file.'jpeg';
				else $file = 'images/null.jpg';
			}
			
			//find links
			$links = '';
			if ($googlemaps != '') $links .= '<a href="'.$googlemaps.'">google map</a> |';
			if ($link1 != '') $links .= '<a href="'.$link1.'">more info</a> |';
			if ($link2 != '') $links .= '<a href="'.$link1.'">website</a> |';
			if ($link3 != '') $links .= '<a href="'.$link3.'">review</a> |';
			$links = substr($links, 0, -2);

			//find type
			$result4 = mysql_query("SELECT tag FROM tags WHERE ID='$eventtype' AND type='event'");
			$row4 = mysql_fetch_array($result4);
			$eventtype = $row4['tag'];
			
			//print cell
			$recommendations .= '<td class=main><div class=eventtag><div class=eventtaginside><p>'.$eventtype.'</p></div></div>
								<div class=name><h3>'.$name.'</h3></div>
								<div class=primary><h2>'.$eventname.' from '.$eventstart.' to '.$eventend.'<h3> at '.$type.' in '.$area.'</h3></div>
								<div class=secondary><p>'.$description.'<br>';
			if ($musttry != '') $recommendations .= 'must try: '.$musttry.'<br>';
			$recommendations .= 'budget: '.$budget.'</p></div>
								<div class=tag><p>'.$tag.'</p></div>
								<div class=picture><img style="float:center" src="'.$file.'" width="195" height="180" /></div>
								<div class=link1><p>'.$address.'<br>';
			if ($phone != '') $recommendations .= 'ph: '.$phone.'</div>'; else $recommendations .= '</div>';
			$recommendations .= '<div class=link2><p align=right>'.$links.'</p></div>
								</div></td>';


		}

		
	}

	//Construct Table
	$counter +=1; //increment counter
	if ($counter == 4) {
		$recommendations .= '</tr><tr valign=top>';
		$counter = 0;
	}
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Results for Local Bars, Restaurants, Clubs, Coffeeshops, Culture, Movies etc. in Mumbai</title>
<link rel="stylesheet" href="style1.css" type="text/css" media="screen" />

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-33910357-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>

<body class=subpage>
<div class="all">
<div class="content"></div>
<div id="logo2"></div>
<div class="title"><h2><? echo $title;?></h2></div>
<div id="form2">
	<table cellspacing="15"><tr valign=top ><?php echo $recommendations; ?></tr></table>
</div>
</div>
</body>
</html>