<?php
if(!isset($_SESSION)) session_start();
$conn = new mysqli('localhost', 'bombajrm_tanvi', '3stupids', 'bombajrm_bombaylocal');     // connect to the MySQL database

// Here add the name of the table and columns that will be used for select lists, in their order
// Add null for 'col_description' if you don`t want to display their data too
$table = 'areas';
$ar_cols = array('area1', 'area2', 'area3', 'ID');

$preid = 'slo_';        // a prefix used for element's ID, in which Ajax will add <select>
$col = $ar_cols[0];     // the variable used for the column that wil be selected
$re_html = '';          // will store the returned html code

// if there is data sent via POST, with index 'col' and 'wval'
if(isset($_POST['col']) && isset($_POST['wval'])) {
  // set the $col that will be selected and the value for WHERE (delete tags and external spaces in $_POST)
  $col = trim(strip_tags($_POST['col']));
  $wval = "'".trim(strip_tags($_POST['wval']))."'";
}

$key = array_search($col, $ar_cols);            // get the key associated with the value of $col in $ar_cols
$wcol = $key===0 ? $col : $ar_cols[$key-1];     // gets the column for the WHERE clause
$_SESSION['ar_cols'][$wcol] = isset($wval) ? $wval : $wcol;    // store in SESSION the column and its value for WHERE
  
// gets the next element in $ar_cols (needed in the onchange() function in <select> tag)
$last_key = count($ar_cols)-1;
$next_col = $key<$last_key ? $ar_cols[$key+1] : '';

// sets an array with data of the WHERE condition (column=value) for SELECT query
for($i=1; $i<=$key; $i++) {
  $ar_where[] = '`'.$ar_cols[$i-1].'`='.$_SESSION['ar_cols'][$ar_cols[$i-1]];
}

// define a string with the WHERE condition, and then the SELECT query
$where = isset($ar_where) ? ' WHERE '. implode($ar_where, ' AND ') : '';
$sql = "SELECT DISTINCT `$col` FROM `$table`".$where;

$result = $conn->query($sql);// perform the query and store the result

// if the $result contains at least one row
if ($result->num_rows > 1) {
  // sets the "onchange" event, which is added in <select> tag
  $onchg = $next_col!==null ? " onchange=\"ajaxReq('$next_col', this.value);\"" : '';

  // sets the select tag list (and the first <option>), if it's not the last column
  if($col!=$ar_cols[$last_key]) {
	$re_html = '<select name="'. $col. '" id="area" '. $onchg. '>';
	if($col==$ar_cols[0]) $re_html .= '<option value="null">choose a zone</option>';
	if($col==$ar_cols[1]) $re_html .= '<option value="null">choose an area</option>';
	if($col==$ar_cols[2]) $re_html .= '<option value="null">choose a sub-area</option>';
}

  while($row = $result->fetch_assoc()) {
    // if its the last column, reurns its data, else, adds data in OPTION tags
    if($col==$ar_cols[$last_key]) $re_html .= '<br/>'. $row[$col];
    else $re_html .= '<option value="'. $row[$col]. '">'. $row[$col]. '</option>';
  }

  if($col!=$ar_cols[$last_key]) $re_html .= '</select><br> ';        // ends the Select list
}


// if the selected column, $col, is the first column in $ar_cols
if($col==$ar_cols[0]) {
  // adds html code with SPAN (or DIV for last item) where Ajax will add the select dropdown lists
  // with ID in each SPAN, according to the columns added in $ar_cols
  for($i=1; $i<count($ar_cols); $i++) {
    if($ar_cols[$i]===null) continue;
    if($i==$last_key) $re_html .= '<div id="'. $preid.$ar_cols[$i]. '"> </div>';
    else $re_html .= '<span id="'. $preid.$ar_cols[$i]. '"> </span>';
  }

// adds the columns in JS (used in removeLists() to remove the next displayed lists when makes other selects)
$re_html .= '<script type="text/javascript">var ar_cols = '.json_encode($ar_cols).'; var preid = "'. $preid. '";</script>';
}
else echo $re_html;
?>