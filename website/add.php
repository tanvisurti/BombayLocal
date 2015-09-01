<?php
$path = $_SERVER['DOCUMENT_ROOT'];
$path .= "/sqlconnect.php";
include_once($path);

$result = mysql_query("SELECT * FROM places WHERE verified=0 AND duplicate=0 ORDER BY name ASC");
$places = mysql_num_rows($result);
$result3 = mysql_query("SELECT * FROM places WHERE verified=1");
$unverifiedplaces = mysql_num_rows($result3);
while($row = mysql_fetch_array($result)){
	$placelist .= $row['name'].'<br>';
}

$result2 = mysql_query("SELECT * FROM events  WHERE verified=0 AND duplicate=0  ORDER BY name ASC");
$events = mysql_num_rows($result2);
$result4 = mysql_query("SELECT * FROM events  WHERE verified=1");
$unverifiedevents = mysql_num_rows($result4);
while($row2 = mysql_fetch_array($result2)){
	$eventlist .= $row2['name'].'<br>';
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Add Local Bars, Restaurants, Clubs, Coffeeshops, Culture, Movies etc. in Mumbai</title>
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
<div class="title"></div>
<div id="form2">
	<h3 align=center>Don't see your favourite place or event on here? Help us out by adding it!</h3>
	<table><tr valign=top><td width=450>
		<h3>Place List</h3>
		<p>Places include all recreational establishments in the city - Coffee shops, restaurants, bars, malls, theatres, beaches etc.</p>
		<p><a href='place.php'>Add a place</a></p><br>
		<p><? echo $placelist; ?></p>
	</td><td width=450>
		<h3>Event List</h3>
		<p>Events include all scheduled things to do. An event can be a one-time or recurring affair. Any recreational gathering can be listed here - book reading,
		plays, food festivals, happy hours and so forth.</p>
		<p><a href='event.php'>Add an event</a></p><br>
		<p><? echo $eventlist; ?></p>
	</td></tr></table>
</div>
</div>
</body>
</html>