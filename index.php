<?php

session_start();

$fmsg = "";

$_SESSION['tag'] = "meme";

if(empty($_SESSION['page'])){
	$_SESSION['page'] = "memes";
	$_SESSION['loadNum'] = 10;
}

if(isset($_POST['goUpload'])){

	$_SESSION['page'] = "upload";

}

if(isset($_POST['load'])){

	$_SESSION['loadNum'] += $_POST['load'];

}

if(isset($_POST['tag'])){

	$newTag = $_POST['tag'];

	$_SESSION['tag'] = $newTag;
	$_SESSION['page'] = "memes";

	require('pages/connect.php');

	$inc = 1;

	$query = "UPDATE tags SET hits=hits+'$inc' WHERE name='$newTag'";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

}

//file uploading
if(isset($_POST['uploadFile']) && $_FILES['userfile']['size'] > 0){

	//file details
	$fileName = $_FILES['userfile']['name'];
	$tmpName = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];

	$tag1 = strtolower($_POST['tag1']);
	$tag2 = strtolower($_POST['tag2']);
	$tag3 = strtolower($_POST['tag3']);

	$tag1 = str_replace(' ','',$tag1);
	$tag2 = str_replace(' ','',$tag2);
	$tag3 = str_replace(' ','',$tag3);

	//file data manipulation
	$fp = fopen($tmpName, 'r');
	$content = fread($fp, filesize($tmpName));
	$content = addslashes($content);
	fclose($fp);

	$typeDot = strrpos($fileName, ".");
	$extensionType = substr($fileName, $typeDot);
	$extensionType = strtolower($extensionType);

	if($extensionType == ".png" || $extensionType == ".jpg" || $extensionType == ".jpeg" || $extensionType == ".gif"){

		if(!get_magic_quotes_gpc()){

			$fileName = addslashes($fileName);

		}

		require('pages/connect.php');

		if(!empty($tag1)){
		$query = "INSERT IGNORE INTO tags (name) VALUES ('$tag1')";
		$result = mysqli_query($link, $query);
		if (!$result){
			die('Error: ' . mysqli_error($link));
		}}

		if(!empty($tag2)){
		$query = "INSERT IGNORE INTO tags (name) VALUES ('$tag2')";
		$result = mysqli_query($link, $query);
		if (!$result){
			die('Error: ' . mysqli_error($link));
		}}

		if(!empty($tag3)){
		$query = "INSERT IGNORE INTO tags (name) VALUES ('$tag3')";
		$result = mysqli_query($link, $query);
		if (!$result){
			die('Error: ' . mysqli_error($link));
		}}

		require('pages/connect.php');
		$query = "INSERT INTO files (name, type, size, content, tag1, tag2, tag3, date) VALUES ('$fileName', '$fileType', '$fileSize', '$content', '$tag1', '$tag2', '$tag3', now())";

		$result = mysqli_query($link, $query);

		if (!$result){
			die('Error: ' . mysqli_error($link));
		}

		mysqli_close($link);

		$fmsg =  "File ".$fileName." Uploaded Successfully!";

	}
	else{

		$fmsg =  "Must be .png, .gif, or .jpg!";

	}

}

?>

<html>

<!--Technical Stuff, Imports, Sidebar Script-->
<script src="js/scripts.js"></script>

<head>

	<title>FamousMemes</title>
	<link href="css/main.css" rel="stylesheet" type="text/css" />
	<link href="https://fonts.googleapis.com/css?family=Kanit:400,600,800" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Carter+One" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

</head>

<!--Page Content-->
<body>

	<div class="wrapper">

		<div class="sidebar" style="display:none; width: 0%;" id="sidebar">
			<form method="post">
				<input type="submit" name="goUpload" class="playButton" value="Upload">
			</form>
			<form method='post'>
				<input type='hidden' name='tag' value='meme'>
				<input type='submit' class='sidebarItem' value='Show All'>
			</form>

			<p class="sidebarText1">
	        	Tags
	      	</p>
	      	<?php
	      	require('pages/connect.php');

	      	$loadNum = $_SESSION['loadNum'];

	      	$query = "SELECT name FROM tags ORDER BY hits DESC LIMIT $loadNum";

	      	$result = mysqli_query($link, $query);

			if (!$result){
				die('Error: ' . mysqli_error($link));
			}

			while(list($name) = mysqli_fetch_array($result)){

		      	echo "<form method='post'><input type='hidden' name='tag' value='" . $name . "'><input type='submit' class='sidebarItem' value='" . $name . "'></form>";

	      	}

	      	$query = "SELECT * FROM tags";

	      	$result = mysqli_query($link, $query);

			if (!$result){
				die('Error: ' . mysqli_error($link));
			}

			if(mysqli_num_rows($result) > $loadNum){
	      	echo "<form method='post'><input type='hidden' name='load' value='10'><input type='submit' class='sidebarItem' value='*More*'></form>";
			}
			?>
		</div>

		<div class="titlebar" style="width: 70%; margin-left: 10%;" id="titlebar">


			<img class="sidebarButton" src="img/openSidebar.png" width="30" height="30" onclick="sidebarToggle();"/>
			<p class="titleText1">
				Famous Memes
			</p>
			<br />
			<p class="titleText2">
				Famous Memes &#8226 For Famous People
			</p>

		</div>

		<div class="main" style="width: 70%; margin-left: 10%;" id="main">
			
		<?php

		//memes
		if($_SESSION['page'] == "memes"){

			require('pages/connect.php');

			$ctag = $_SESSION['tag'];

			if($_SESSION['tag'] == "meme"){
				$query = "SELECT content FROM files ORDER BY hits DESC LIMIT 10";
			}
			else{
	      		$query = "SELECT content FROM files WHERE tag1='$ctag' OR tag2='$ctag' OR tag3='$ctag' ORDER BY hits DESC";
	  		}

	      	$result = mysqli_query($link, $query);

			if (!$result){
				die('Error: ' . mysqli_error($link));
			}

			while($memesResult = mysqli_fetch_array($result)){

				echo '<img width="50%" src="data:image/jpeg;base64,'.base64_encode( $memesResult['content'] ).'"/><br><br>';

			}	

		}
		//upload
		else{ ?>

			<p class="subtitleText1">
				Upload
			</p>

			<p class="bodyText1">
				Upload memes anonymously here.
			</p>
			<br>

			<?php
			echo $fmsg;
			?>

			<form method="post" enctype="multipart/form-data" class="uploadForm">
				<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
				<input style="font-size:16px; border:1px solid black;" name="userfile" type="file" id="userfile" accept="image/*"><br>
				Tag : <input type="text" name="tag1" / ><br>
				Tag : <input type="text" name="tag2" / ><br>
				Tag : <input type="text" name="tag3" / ><br>
				<input class="submitButton" style="width:100px;height:30px;font-size:16px;" name="uploadFile" type="submit" class="box" id="uploadFile" value="Upload">
			</form>

		<?php }

		?>

		</div>

	</div>

</body>

</html>