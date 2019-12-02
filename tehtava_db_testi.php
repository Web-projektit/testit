<?php
/* Testaus */
if (!session_id()) session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>PHP-tehtävät, sessio</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!--
<link rel="stylesheet" href="bootstrap.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="bootstrap_media_queries.css">
</head>
<style>
.relative {position:relative;}    
body,select,textarea {font-family:Arial;}
/*fieldset {width:70%;}*/
#action_form label {
  /*display:inline-block;
  width:140px;
  vertical-align:top;*/
  color:blue;
  }  
.list-group-item label {white-space:nowrap;}
.list-group-item {border:0;}
.checkbox {margin-left:25px;}
/*input,textarea,select {font-size:16px;}*/  
/*.radio {display:inline;padding-right:4px;padding-left:4px;}*/
.yksikko {padding-left:4px;}
/*.teksti {display:inline-block;width:200px;}*/
.lukema {display:inline-block;width:50px;}
.ul-inline {flex-direction:row;}
.virhe_group {list-style-type:none;}
.virhe_p {color:red;list-style-type:none;}
.virhe_missing,.virhe_numeric {position:absolute;display:inline-block;width:20px;color:red;}
.virhe_missing_text,.virhe_numeric_text {display:inline-block;width:20px;color:red;}
.virhe_numeric::after {content:'*';}
.virhe_numeric_text::after {content:'*';}
.tyhja {position:absolute;display:inline-block;width:20px;visibility:hidden;}
#footer p{text-align:center;font-size:1.2rem;font-weight:bold;}
</style>

<script>
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>


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
echo "<select class=\"form-control\" name=\"language_id\">";
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
  //echo "<label>$f</label><span class=\"tyhja\"></span><input type=\"checkbox\" name=\"special_features[]\" 
  //      value=$feature $checked><br>";
  echo "<div class=\"checkbox\"><label class=\"form-check-label\"><input class=\"form-check-input\" type=\"checkbox\" name=\"special_features[]\" "
       . "value=$feature $checked>$f</label></div>";
  }	
}

function rating(){
global $db;	
$query = "SHOW COLUMNS FROM film LIKE 'rating'";
$result = $db->query($query);
$row = $result->fetch_assoc();
$str = $row['Type'];
preg_match('/enum\((.*)\)$/',$str,$matches);
$strArr = explode(",",$matches[1]);
//var_export($strArr);
//echo "str:$str<br>";
//var_dump($result);
//var_export($_POST['special_features']);
echo "<ul class=\"list-group list-group-horizontal\">";
foreach ($strArr AS $rating){
  $r = trim($rating,"'");
  $rating_set = (isset($_POST['rating']) and $r == $_POST['rating']);  
  $checked = ($rating_set) ? "checked=\"checked\"" : "";
  //echo "$feature,checked:$checked,feature_set:$feature_set,box_set:$box_set<br>";
  //echo "<li class=\"list-group-item\"><label class=\"radio\">$r</label><input type=\"radio\" name=\"rating\" 
  //      value=$rating $checked></li>";
   echo "<li class=\"list-group-item\"><input class=\"form-check-input\" type=\"radio\" name=\"rating\" 
        value=$rating $checked><label class=\"form-check-label\">$r</label></li>";

  }	
echo "</ul>";  
}


function poistasessio(){
session_unset();
session_destroy();
echo "sessio on suljettu,session_id:".session_id()."<br>";	
return;
}

function virhe($kentta){
$classes = array();
$errors_r = isset($_POST['error_required']) ? $_POST['error_required'] : array();
$errors_n = isset($_POST['error_numeric']) ? $_POST['error_numeric'] : array();
if (array_search($kentta,$errors_r) !== false){
  $classes[] = "virhe_missing";	
  }
if (array_search($kentta,$errors_n) !== false){
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
	
//var_export($_SERVER);
$remote = in_array($_SERVER['REMOTE_ADDR'],array('127.0.0.1','REMOTE_ADDR' => '::1'));
if (!$remote) {	
  $password = "6#vWHD_$";
  $user = "azure";
  $server = "localhost:49492";
  }
else {
  $password = "";
  $user = "root";
  $server = "localhost";	
  }
//echo "server:$server,user:$user";
//exit;

$db = new mysqli($server,$user,$password,'sakila');
if (mysqli_connect_errno()){
   echo "Virhe tietokantayhteydessä.<br>";
  }
  
  /*
$query = "SELECT f.title,c.name FROM film f,film_category fc,category c WHERE
  fc.film_id = f.film_id AND c.category_id = fc.category_id
  AND c.name LIKE '".$_POST['genre']."'";
$query = "SELECT f.title,c.name FROM film f INNER JOIN film_category fc 
  ON fc.film_id = f.film_id INNER JOIN category c 
  ON c.category_id = fc.category_id 
  WHERE c.name LIKE '".$_POST['genre']."'";
$result = $db->query($query);
$num_results = $result->num_rows;
echo "Sakila film:$num_results riviä.<br>"; 
while ($row = $result->fetch_assoc()){
  echo $row['title']." ".$row['name']."<br>";	
  }
*/

?>
  
 
<nav class="navbar navbar-expand-md navbar-light bg-light">
  <a class="navbar-brand" href="#"><img class="img-responsive" src="company_logo.png" width="120" height="39" alt="company_logo"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>    
    
<div class="container">
<?php
/*$error = true;  
try {
  if ($error)  
    throw new Exception("Tässä on aluksi esimerkki virheestä.", 42);
  }
catch (Exception $e) {
  echo "Exception ". $e->getCode(). ": ". $e->getMessage()."".
  " tiedostossa ". $e->getFile(). " rivillä ". $e->getLine(). "<br />";
  }*/
?>
<!-- Kopioitu malli -->
<!--  <form class="form-horizontal">
  <div class="form-group">
    <label class="control-label col-sm-2" for="formGroupExampleInput">Example label</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Example input">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-sm-2" for="formGroupExampleInput2">Another label</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Another input">
    </div>
    </div>
      <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <div class="checkbox">
          <label><input type="checkbox"> Remember me</label>
        </div>
      </div>
      </div>    
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
      </div>
    </div>   
    
</form>-->
    
<form class="" id="action_form" action="tehtava_lomakekasittelija.php" method="post">
<fieldset>
<legend>Uusi elokuva</legend>
<div class="form-group row">
<label class="control-label col-sm-2 relative">Nimi:</label><span class="<?php virhe('title');?>">*</span>
<div class="col-sm-10">
<input class="form-control teksti" type="text" name="title" value = "<?php nayta('title');?>">
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2">Kuvaus:</label><span class="<?php virhe('description');?>">*</span>
<div class="col-sm-10">
<textarea class="form-control teksti" rows="4" cols="40" name="description"><?php nayta('description');?></textarea>
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2">Julkaisuvuosi:</label><span class="<?php virhe('release_year');?>">*</span>
<div class="col-sm-10">
<input class="form-control lukema" type="text" name="release_year" value="<?php nayta('release_year');?>">
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2">Kieli:</label><span class="<?php virhe('language_id');?>">*</span>
<div class="col-sm-5">
<?php echo kielet();?>
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2">Vuokra-aika:</label><span class="<?php virhe('rental_duration');?>">*</span>
<div class="col-sm-10">
    <input class="form-control lukema" type="text" name="rental_duration" value="<?php nayta('rental_duration');?>"><span class="yksikko">pv</span><br>
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2">Vuokrahinta:</label><span class="<?php virhe('rental_rate');?>">*</span>
<div class="col-sm-10">
<input class="form-control lukema" type="text" name="rental_rate" value="<?php nayta('rental_rate');?>"><span class="yksikko">€</span><br>
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2">Pituus:</label><span class="<?php virhe('length');?>">*</span>
<div class="col-sm-10">
<input class="form-control lukema" type="text" name="length"><span class="yksikko">min</span><br>
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2">Korvaushinta:</label><span class="<?php virhe('replacement_cost');?>">*</span>
<div class="col-sm-10">
<input class="form-control lukema" type="text" name="replacement_cost"><span class="yksikko">€</span><br>
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2">Ikäraja:</label><span class="<?php virhe('rating');?>">*</span>
<div class="col-sm-10">    
<?php rating();?>
</div></div>
<div class="form-group row">
<label class="control-label col-sm-2" style="color:#333333;">Special features:</label>
<div class="col-sm-10">  
<?php specialfeatures();?>
</div></div>
<div class="form-group row">
<div class="col-sm-offset-2 col-sm-10">
<input class="btn btn-primary" type="submit" value="Tallenna"><br>
</div></div>
</fieldset>
</form>
<ul>
<!--<p>session:<?php echo session_id();?></p>-->
<?php
//$_SESSION['testi'] = 'Testi';
//if (isset($_GET['ulos'])) poistasessio();
//else tulostasessio();
if (isset($_POST['error_required'])) echo "<li class=\"virhe_p\"><span class=\"virhe_missing_text\">*</span>Täytä tyhjät kentät.</li>";
if (isset($_POST['error_numeric'])) echo "<li class=\"virhe_p\"><span class=\"virhe_numeric_text\">*</span>Korjaa numeeriset kentät.</li>";
?>
<!--<a href="tehtava_sessio.php?ulos=k">Poista sessioparametrit</a>-->
<!--<iframe name="tulokset" frameborder=0 width="600" height="600"></frame>-->
</ul>
</div>
<div id="footer">
    <p>Tämä on footer sivun lopussa.</p>    
</div>
</body>
</html>

