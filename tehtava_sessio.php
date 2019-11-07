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
body {font-family:Arial;}
fieldset {width:50%;}
label {display:inline-block;width:100px;color:blue;padding:4px;}
</style>
<body>

<?php
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

function poistasessio(){
session_unset();
session_destroy();
echo "sessio on suljettu,session_id:".session_id()."<br>";	
return;
}


/*$nimi = trim($_POST["nimi"]);
$email = trim($_POST["email"]);
$ssana = trim($_POST["ssana"]);
*/
?>
<form action="tehtava_sessio.php" method="post">
<fieldset>
<legend>session-lomake</legend>
<label>Avain:</label><input type="text" name="avain"><br>
<label>Arvo:</label><input type="text" name="arvo"><br>
<input type="submit" value="Tallenna" style="margin-left:108px;"><br>
</fieldset>
</form>
<p>session:<?php echo session_id();?></p>
<?php
//$_SESSION['testi'] = 'Testi';
if (isset($_GET['ulos'])) poistasessio();
else tulostasessio();
?>
<a href="tehtava_sessio.php?ulos=k">Poista sessioparametrit</a>
</body>
</html>
