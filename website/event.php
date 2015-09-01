<?php include 'select_list.php'; ?>
<?php
$path = $_SERVER['DOCUMENT_ROOT'];
$path .= "/sqlconnect.php";
include_once($path);

$thepassword = 'mainsharabi';
if( !isset( $_COOKIE['bombaylocal']) || $_COOKIE['bombaylocal'] != $thepassword) $verified=1; else $verified=0; 

//Populate time dropdown
$html="";
$hour = 9; //because we want to start at 
$AM = 'AM';
for($i=1; $i<=24 ; $i++)
{
	if ($hour >12){$hour=1;}
	if ($hour ==12){if ($AM == 'AM'){ $AM = 'PM';} else $AM = 'AM';}
	$time = $hour.':00 '.$AM;
	$html.= "<option value='".$time."'>".$time."</option>";
	$time = $hour.':30 '.$AM;
	$html.= "<option value='".$time."'>".$time."</option>";
	$hour++;
}
$html .= "</select>";

//Populate day
$day="<select name='date' class='text_input'>";
for($i=1; $i<=31 ; $i++) {
	$day.= "<option value='".$i."'>".$i."</option>";}
$day.="</select>";
	
//Populate month
$month="<select name='month' class='text_input'>";
$month.= "<option value='1'>Jan</option>";
$month.= "<option value='2'>Feb</option>";
$month.= "<option value='3'>March</option>";
$month.= "<option value='4'>April</option>";
$month.= "<option value='5'>May</option>";
$month.= "<option value='6'>Jun</option>";
$month.= "<option value='7'>Jul</option>";
$month.= "<option value='8'>Aug</option>";
$month.= "<option value='9'>Sep</option>";
$month.= "<option value='10'>Oct</option>";
$month.= "<option value='11'>Nov</option>";
$month.= "<option value='12'>Dec</option>";
$month.= "</select>";

//Populate yeAR
$year="<select name='year' class='text_input'>";
$year.= "<option value='2012'>2012</option>";
$year.= "<option value='2013'>2013</option>";
$year.= "</select>";

//Generate tags
$tags = '<table><tr  width=150><td>';
$result = mysql_query("SELECT * FROM tags WHERE type='event'");
while($row = mysql_fetch_array($result)){
	$tags .= '<p><input type="radio" name="tags" value="'.$row["ID"].'" id="radio">'.$row["tag"].'</input></p>';
	$counter += 1;
	if ($counter == 5) { 
		$tags .= '</td><td>';
		$counter = 0;
	}
}
$tags .= '</td></tr></table>';

//List of places
$place_list = '';
$result = mysql_query("SELECT ID, name FROM places");
while($row = mysql_fetch_array($result)){
	$place_list .= '<option value="'.$row["ID"].'">'.$row["name"].'</option>';
}

//Save details from the form
if(isset($_POST['Send'])){

	//Grab all the variables
	$place = $_POST['place'];	
	$name = $_POST['name'];
	$photo = $_POST['photo'];
	$cost = $_POST['cost'];
	$description = $_POST['description'];	
	$tags = $_POST['tags'];
	$link = $_POST['link'];
	$repeat = $_POST['repeat'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$date = $_POST['month'].'/'.$_POST['date'].'/'.$_POST['year'];
	$daysA = $_POST['day'];
	$upto = $_POST['upto'];
	
	//Save days of the week
	if(!empty($daysA)){
		$N = count($daysA);
		for($i=0; $i < $N; $i++)
		{
		  $days .= $daysA[$i] . ",";
		}
	}

	mysql_query("INSERT INTO events (name, place, photo, type, description, cost, starttime, endtime, recurrence, date, days, weeks, link, verified) 
							 VALUES ('$name', '$place', '$photo', '$tags', '$description', '$cost', '$from', '$to', '$repeat', '$date', '$days', '$upto', '$link', '$verified')");
	$id=mysql_insert_id();
	
	//Save the photo
	if ($photo != null) {
		$photo2 = 1;
		$url = $photo;
		if (strpos($url, 'jpeg') || strpos($url, 'JPEG')) $extension = 'jpeg';
		if (strpos($url, 'jpg') || strpos($url, 'JPG'))  $extension = 'jpg';
		if (strpos($url, 'png') || strpos($url, 'PNG')) $extension = 'png';
		if (strpos($url, 'gif') || strpos($url, 'GIF')) $extension = 'gif';
		if ($extension != '') {
			$img = 'images/events/'.$id.'.'.$extension;
			file_put_contents($img, file_get_contents($url));
		}

	}
	
	//Let the user know that a task has been submitted
	$URL="event.php"; 
	header ("Location: $URL");
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Add a result for Local Bars, Restaurants, Clubs, Coffeeshops, Culture, Movies etc. in Mumbai</title>
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
<div class="content"></div>
<div id="logo2"></div>
<div id="form2">
	<h2>Add an event</h2>
	<hr style="background-color='white';"/><br>
	
	<form action="" method="post" name="myform" onsubmit="">
	<h3>Where is the event?: <font color="red">*</font><select name="place"><? echo $place_list; ?></select></h3>
	<h3>Event name: <font color="red">*</font> <input type="text" name="name" /><br />
	<input type="hidden" name="MAX_FILE_SIZE" value="500" /> 
	<h3>URL of Photo (jpg, png, gif): <font color="red">*</font> <input type="text" name="photo" /><br />
	<h3>Cost (approx): <font color="red">*</font> <input type="text" name="cost" /></h3>
	<h3>Description (<160char): <font color="red">*</font> </h3><textarea name="description" cols="35" rows="4"></textarea>
	<h3>Type of event: <font color="red">*</font> </h3><?php echo $tags; ?>
	<h3>Links to more info: <input type="text" name="link" />

	<hr style="background-color='white';"/><br>
	<h3>Recurrence: <font color="red">*</font> <br>
	<input type="radio" name="repeat" value="0">No repeats, one-day only</input><br>
	<input type="radio" name="repeat" value="1">More than once</input>
	<h3>From: <select name='from' class='text_input'><? echo $html; ?></h3>
	<h3>To: <select name='to' class='text_input'><? echo $html; ?></h3>
	<table><tr valign=top><td width=400>
	<h2>If onetime event - </h2>	
	<h3>Date: <? echo $day, $month, $year; ?></h3>
	</td><td width=400>
	<h2>If recurring event - </h2>
	<h3>Days of week:</h3>
		<table><tr><td>
		<p><input type="checkbox" name="day[]" value="mon" />Monday</p>
		<p><input type="checkbox" name="day[]" value="tue" />Tuesday</p>
		<p><input type="checkbox" name="day[]" value="wed" />Wednesday</p>
		<p><input type="checkbox" name="day[]" value="thu" />Thursday</p>
		</td><td>
		<p><input type="checkbox" name="day[]" value="fri" />Friday</p>
		<p><input type="checkbox" name="day[]" value="sat" />Saturday</p>
		<p><input type="checkbox" name="day[]" value="sun" />Sunday</p>
		</td></tr></table>
	<h3>Until:</h3>
		<p><input type="radio" name="upto" value="1" />this week only</p>
		<p><input type="radio" name="upto" value="infinity" />until the end of time</p>
		<p><input type="radio" name="upto" value="2" />2 more weeks</p>
		<p><input type="radio" name="upto" value="3" />3 more weeks</p>
		<p><input type="radio" name="upto" value="4" />4 more weeks</p>
	</h3>
	</td></tr></table>
	<h3><input  name="Send" class='button' type="submit" value="Save" onmouseout="this.style.color='white';" onmouseover="this.style.color='grey';"/></h3>	
	</form>
	
</div>
</body>
</html>