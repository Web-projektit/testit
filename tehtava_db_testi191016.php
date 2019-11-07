<?php
if (!session_id()) session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>PHP-tehtävät, sessio</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<style>
body,select,textarea {font-family:Arial;}
fieldset {width:70%;}
label {
  display:inline-block;
  width:150px;
  color:blue;
  vertical-align:top;
  }
input,textarea,select {margin-bottom:4px;font-size:16px;}  
.teksti {width:200px;}
.lukema {width:50px;}
.virhe_p {color:red;}
.virhe_missing,.virhe_numeric {display:inline-block;width:20px;color:red;
             vertical-align:top;}
.virhe_numeric::after {content:'*';}
.tyhja {display:inline-block;width:20px;visibility:hidden;}
</style>
<body>

<?php
//var_export($_POST);

function tulostasessio(){
echo "Session-parametrit:<br>";	
/*echo "post:<br>";
var_export($_POST);
echo "<p></p>";*/
if (!empty($_POST['avain'])) {
  $avain = $_POST['avain'];
  $arvo = $_POST['arvo'];  
  //echo "parametri $avain:$arvo<br>";
  $_SESSION[$avain] = $arvo;	
  }	
echo "<ul>";	
foreach ($_SESSION as $key => $value) {	
  echo "<li>$key:$value</li>";
  }
echo "</ul>";
return;
}

function kielet(){
global $db;	
$query = "SELECT language_id,name FROM language ORDER BY name";
$result = $db->query($query);
echo "<select name=\"language_id\">";
echo "<option value=\"\"></option><br>";
while (list($id,$name) = $result->fetch_row()){
  if (isset($_POST['language_id']) and $_POST['language_id'] == $id)
	  $selected = " SELECTED=\"SELECTED\"";
  else $selected = "";
  echo "<option value=\"$id\"$selected>$name</option><br>";
  }
echo "</select>";	
}

function specialfeatures(){
global $db;	
$query = "SHOW COLUMNS FROM film LIKE 'special_features'";
$result = $db->query($query);
$row = $result->fetch_assoc();
$str = trim(substr($row['Type'],3),'()');
$strArr = explode(",",$str);
//var_export($strArr);
//echo "str:$str<br>";
//var_dump($result);
//var_export($_POST['special_features']);
foreach ($strArr AS $feature){
  $f = trim($feature,"'");
  $box_set = isset($_POST['special_features']);
  $feature_set = ($box_set and in_array($f,$_POST['special_features']));  
  $checked = ($feature_set) ? "checked=\"checked\"" : "";
  //echo "$feature,checked:$checked,feature_set:$feature_set,box_set:$box_set<br>";
  echo "<label>$f</label><span class=\"tyhja\"></span><input type=\"checkbox\" name=\"special_features[]\" 
        value=$feature $checked><br>";
  }	
}


function poistasessio(){
session_unset();
session_destroy();
echo "sessio on suljettu,session_id:".session_id()."<br>";	
return;
}

function virhe($kentta){
$classes = array();
$errors_r = $_POST['error_required'];
$errors_n = $_POST['error_numeric'];
if (isset($errors_r) and array_search($kentta,$errors_r) !== false){
  $classes[] = "virhe_missing";	
  }
if (isset($errors_n) and array_search($kentta,$errors_n) !== false){
  $classes[] = "virhe_numeric";	
  }
if (!$classes) $classes[] = "tyhja";	
$class = implode(" ",$classes);
echo $class;
return;
}

function nayta($kentta){
echo isset($_POST[$kentta]) ? $_POST[$kentta] : ""; 	
return;
}

$db = new mysqli('localhost','root','','sakila');
if (mysqli_connect_errno()){
   echo "Virhe tietokantayhteydessä.<br>";
  }
/*
$query = "SELECT f.title,c.name FROM film f,film_category fc,category c WHERE
          fc.film_id = f.film_id AND c.category_id = fc.category_id AND f.title LIKE 'A%'";
$query = "SELECT f.title,c.name FROM film f INNER JOIN film_category fc 
  ON fc.film_id = f.film_id INNER JOIN category c 
  ON c.category_id = fc.category_id 
  WHERE f.title LIKE 'A%' AND c.name LIKE 'Horror'";
$result = $db->query($query);
$num_results = $result->num_rows;
echo "Sakila film:$num_results riviä.<br>"; 
while ($row = $result->fetch_assoc()){
  echo $row['title']." ".$row['name']."<br>";	
  }
*/

?>
<form action="tehtava_lomakekasittelija.php" method="post">
<fieldset>
<legend>Uusi elokuva</legend>
<label>Nimi:</label><span class="<?php virhe('title');?>">*</span><input class="teksti" type="text" name="title" value = "<?php nayta('title');?>"><br>
<label>Kuvaus:</label><span class="<?php virhe('description');?>">*</span><textarea class="teksti" rows="4" cols="40" name="description"><?php nayta('description');?></textarea><br>
<label>Julkaisuvuosi:</label><span class="<?php virhe('release_year');?>">*</span><input class="lukema" type="text" name="release_year" value="<?php nayta('release_year');?>"><br>
<label>Kieli:</label><span class="<?php virhe('language_id');?>">*</span><?php echo kielet();?><br>
<label>Vuokra-aika:</label><span class="<?php virhe('rental_duration');?>">*</span><input class="lukema" type="text" name="rental_duration" value="<?php nayta('rental_duration');?>"><br>
<label>Vuokrahinta:</label><span class="<?php virhe('rental_rate');?>">*</span><input class="lukema" type="text" name="rental_rate" value="<?php nayta('rental_rate');?>"><br>
<label>Pituus:</label><span class="<?php virhe('length');?>">*</span><input class="lukema" type="text" name="length"><br>
<label>Korvaushinta:</label><span class="<?php virhe('replacement_cost');?>">*</span><input class="lukema" type="text" name="replacement_cost"><br>
<label>Ikäraja:</label><span class="<?php virhe('rating');?>">*</span><input class="teksti" type="text" name="rating"><br>
<label style="color:#333333;padding-bottom:4px;">Special features:</label><br><?php specialfeatures();?>
<input type="submit" value="Tallenna" style="margin-left:170px;margin-top:10px;"><br>
</fieldset>
</form>
<!--<p>session:<?php echo session_id();?></p>-->
<?php
//$_SESSION['testi'] = 'Testi';
//if (isset($_GET['ulos'])) poistasessio();
//else tulostasessio();
if (isset($_POST['error_required'])) echo "<li class=\"virhe_p\"><span class=\"virhe_missing\">*</span>Täytä tyhjät kentät.</li>";
if (isset($_POST['error_numeric'])) echo "<li class=\"virhe_p\"><span class=\"virhe_numeric\">*</span>Korjaa numeeriset kentät.</li>";
?>
<!--<a href="tehtava_sessio.php?ulos=k">Poista sessioparametrit</a>-->
<!--<iframe name="tulokset" frameborder=0 width="600" height="600"></frame>-->
</body>
</html>

