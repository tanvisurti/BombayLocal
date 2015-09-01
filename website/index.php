<?php include 'select_list.php'; ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Bombay Local - Your Guide to Local Bars, Restaurants, Clubs, Coffeeshops, Culture, Movies etc.</title>
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
    var php_file = 'select_list2.php';     // path and name of the php file

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
//Verification
function validateForm()
{
	error = true; 
	var area1=document.forms["myform"]["area1"].value;
	var category=document.forms["myform"]["category"].value;
	if (area1 =="null") error = false;
	if (category == null) error = false';			
	return error;
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
<div id="logo"></div>
<div id="form">
	<form action="results.php" method="get" name="myform" onsubmit="">
	<h2>where are you?</h2>
	<?php echo $re_html; ?><br><br>
	<h2>what are you looking for?</h2>
	<h3><input type="radio" name="category" value="Food" id="radio">food</input>
	<h3><input type="radio" name="category" value="Booze" id="radio">booze</input>
	<h3><input type="radio" name="category" value="Culture" id="radio">culture</input>
	<h3><input type="radio" name="category" value="Timepass" id="radio">timepass</input><br><br>
	<h3><input  name="Send" class='button' type="submit" value="find stuff to do" onmouseout="this.style.color='white';" onmouseover="this.style.color='grey';"/></h3>
	</form>
</div>
<div class="footer"><p align=center>Created by <a href="https://twitter.com/tanvisurti" target="_blank">Tanvi Surti</a> | info@bombaylocal.com | <a href="add.php" target="_blank">Add a listing</a></p></div>
</body>
</html>