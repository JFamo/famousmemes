<?php

session_start();

$outStr = "";

$outStr .= '<form method="post">';
$outStr .= '<input type="submit" name="goUpload" class="sidebarItem" value="Upload">';
$outStr .= '</form>';
$outStr .= "<form method='post'>";
$outStr .= "<input type='hidden' name='tag' value='meme'>";
$outStr .= "<input type='submit' class='sidebarItem' value='Show Random'>";
$outStr .= '</form>';

$outStr .= '<p class="sidebarText1">';
$outStr .= 'Tags';
$outStr .= '</p>';
	
require('pages/connect.php');

$loadNum = $_SESSION['loadNum'];

$query = "SELECT name FROM tags ORDER BY hits DESC LIMIT $loadNum";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

while(list($name) = mysqli_fetch_array($result)){

  	$outStr .= "<form method='post'><input type='hidden' name='tag' value='" . $name . "'><input type='submit' class='sidebarItem' value='" . $name . "'></form>";

}

$query = "SELECT * FROM tags";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

if(mysqli_num_rows($result) > $loadNum){
	$outStr .= "<form method='post'><input type='hidden' name='load' value='10' id='loadMore'><input type='submit' class='sidebarItem' value='*More*'></form>";
}

mysqli_close($link);

echo $outStr;

?>