<?php include 'select_list.php'; ?>
<?php
$path = $_SERVER['DOCUMENT_ROOT'];
$path .= "/sqlconnect.php";
include_once($path);

$thepassword = 'mainsharabi';
if( !isset( $_COOKIE['bombaylocal']) || $_COOKIE['bombaylocal'] != $thepassword) $verified=1; else $verified=0; 

//Generate tags
$tags = '<table><tr  width=150><td>';
$result = mysql_query("SELECT * FROM tags WHERE type='place'");
while($row = mysql_fetch_array($result)){
	$tags .= '<p><input type="checkbox" name="tag[]" value="'.$row["ID"].'" id="radio">'.$row["tag"].'</input></p>';
	$counter += 1;
	if ($counter == 5) { 
		$tags .= '</td><td>';
		$counter = 0;
	}
}
$tags .= '</td></tr></table>';

//Save details from the form
if(isset($_POST['Send'])){

	//Grab all the variables
	$name = $_POST['name'];
	$photo = $_POST['photo'];
	$typeA = $_POST['type'];
	$description = $_POST['description'];	
	$musttry = $_POST['musttry'];
	$budget = $_POST['budget'];
	$tagA = $_POST['tag'];
	$area1 = $_POST['area1'];
	$area2 = $_POST['area2'];
	$area3 = $_POST['area3'];
	$address = $_POST['address'];
	$contact = $_POST['contact'];	
	$googlemaps = $_POST['googlemaps'];
	$link1 = $_POST['link1'];
	$link2 = $_POST['link2'];
	$link3 = $_POST['link3'];
	
	//Save tags
	if(!empty($tagA)){
		$N = count($tagA);
		for($i=0; $i < $N; $i++)
		{
		  $tag .= $tagA[$i] . ",";
		}
	}
	
	//Save type
	if(!empty($typeA)){
		$M = count($typeA);
		for($i=0; $i < $M; $i++)
		{
		  $type .= $typeA[$i] . ",";
		}
	}

	//Retrieve Area ID
	$result2 = mysql_query("SELECT * FROM areas WHERE area1='$area1' AND area2='$area2'");
	if (mysql_num_rows($result2) == 1){
		$row2 = mysql_fetch_array($result2);
		$areaID = $row2['ID'];
	}
	else {
		while($row2 = mysql_fetch_array($result2)){
			if ($row2['area3'] == $area3) $areaID = $row2['ID'];
		}
	}

	//Save the photo
	if ($photo != null) $photo2 = 1;
	

	mysql_query("INSERT INTO places (timestamp, name, photo, type, area, address, contact, description, googlemaps, link1, link2, link3, tags, budget, musttry, verified) 
	VALUES (NOW(), '$name','$photo2','$type','$areaID','$address','$contact','$description','$googlemaps','$link1','$link2','$link3','$tag','$budget','$musttry', '$verified')");

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
			$img = 'images/'.$id.'.'.$extension;
			file_put_contents($img, file_get_contents($url));
		}
	}

	//Let the user know that a task has been submitted
	$URL="place.php"; 
	header ("Location: $URL");
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Add a result for Local Bars, Restaurants, Clubs, Coffeeshops, Culture, Movies etc. in Mumbai</title>
<link rel="stylesheet" href="style1.css" type="text/css" media="screen" />
<script type="text/javascript">
function removeLists(colid) {
  var z = 0;
  // removes data in elements with the id stored in the "ar_cols" variable
  // starting with the element with the id value passed in colid
  for(var i=1; i<ar_cols.length; i++) {
    if(ar_cols[i]==null) continue;
    if(ar_cols[i]==colid) z = 1;
    if(z==1) document.getElementById(preid+ar_cols[i]).innerHTML = '';
  }
}

// create the XMLHttpRequest object, according browser
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;

  if(window.XMLHttpRequest) { xmlHttp = new XMLHttpRequest(); }     // for Forefox, IE7+, Opera, Safari
  else if(window.ActiveXObject) { xmlHttp = new ActiveXObject("Microsoft.XMLHTTP"); }      // IE5 or 6

  return xmlHttp;
}

// sends data to a php file, via POST, and displays the received answer
function ajaxReq(col, wval) {
  removeLists(col);           // removes the already next selects displayed

  // if the value of wval is not '- - -' and '' (the first option)
  if(wval!='- - -' && wval!='') {
    var request =  get_XmlHttp();		      // call the function with the XMLHttpRequest instance
    var php_file = 'select_list.php';     // path and name of the php file

    // create pairs index=value with data that must be sent to server
    var  data_send = 'col='+col+'&wval='+wval;

    request.open("POST", php_file, true);			// set the request

    // adds a header to tell the PHP script to recognize the data as is sent via POST
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(data_send);	      	// calls the send() method with data_send

    // Check request status
    // If the response is received completely, will be added into the tag with id value of "col"
    request.onreadystatechange = function() {
      if (request.readyState==4) {
        document.getElementById(preid+col).innerHTML = request.responseText;
      }
    }
  }
}
</script>

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
	<h2>Add a place</h2>
	<form action="" method="post" name="myform" onsubmit="">
	<h3>Name: <font color="red">*</font> <input type="text" name="name" /><br />
	<input type="hidden" name="MAX_FILE_SIZE" value="500" /> 
	<h3>URL of Photo (jpg, png, gif): <font color="red">*</font> <input type="text" name="photo" id="photo" /> <br />
	<h3>Type of place: <font color="red">*</font> </h3>
	<table><tr><td>
	<p><input type="checkbox" name="type[]" value="restaurant" />restaurant</p>
	<p><input type="checkbox" name="type[]" value="bar" />bar</p>
	<p><input type="checkbox" name="type[]" value="club" />club</p>
	<p><input type="checkbox" name="type[]" value="theatre" />theatre</p>
	<p><input type="checkbox" name="type[]" value="lounge" />lounge</p>
	</td><td>
	<p><input type="checkbox" name="type[]" value="cinema hall" />cinema hall</p>
	<p><input type="checkbox" name="type[]" value="mall" />mall</p>
	<p><input type="checkbox" name="type[]" value="public place" />public place</p>
	<p><input type="checkbox" name="type[]" value="coffee shop" />coffee shop</p>
	<p><input type="checkbox" name="type[]" value="misc." />misc.</p>
	</td></tr></table>
	<hr style="background-color='white';"/><br>
	<h3>Description (<160char): <font color="red">*</font> </h3><textarea name="description" cols="35" rows="4"></textarea>
	<h3>Must try/ Must do:<font color="red">*</font> <input type="text" name="musttry" />
	<h3>Budget: <font color="red">*</font> </h3>
	<p><input type="radio" name="budget" value="1" id="radio">Will get by with loose change</input>
	<p><input type="radio" name="budget" value="2" id="radio">Reasonably priced. Common man's price range.</input>
	<p><input type="radio" name="budget" value="3" id="radio">Ouch, this hurt.</input>
	<p><input type="radio" name="budget" value="4" id="radio">Had to sell my kidney to pay</input><br><br>
	<h3>Tags (max. 5): <font color="red">*</font> </h3><?php echo $tags; ?>
	<hr style="background-color='white';"/><br>
	<h3>Area: <font color="red">*</font> </h3><?php echo $re_html; ?>
	<h3>Address: <font color="red">*</font> </h3><textarea name="address" cols="35" rows="4"></textarea>
	<h3>Contact no. (just 1): <font color="red">*</font> <input type="text" name="contact" />
	<hr style="background-color='white';"/><br>
	<h3>Google Maps link: <input type="text" name="googlemaps" />
	<h3>Links to reviews/menus/website: <font color="red">*</font> <input type="text" name="link1" />
	<h3>Links to reviews/menus/website: <input type="text" name="link2" />
	<h3>Links to reviews/menus/website: <input type="text" name="link3" />
	<h3><input  name="Send" class='button' type="submit" value="Save" onmouseout="this.style.color='white';" onmouseover="this.style.color='grey';"/></h3>	
	</form>
	
</div>
</body>
</html>