<?php
$cookie_name = "user";
if( isset($_COOKIE[$cookie_name]) ){
	header('Location: index.php');
}
include 'settings.php';

$count_questions = count($questions);
if($_SERVER['REQUEST_METHOD'] == 'POST') { 
	if( $_POST['username'] == NULL) {
		$error = true;		
		$error = 'Please fill in a username';				
	}							
	elseif( is_dir('data/'.$_POST['username']) ) {
		$error = true;
		$error = 'Username already exists';			
	}
	else {		
		$cookie_value = $_POST['username']; // set cookie for user												
		setcookie($cookie_name, $cookie_value, time() + 86400, "/"); // 86400 = 1 day																									
		mkdir('data/'.$cookie_value, 0755, true);
		
		for ($x = 1; $x <= $count_questions; $x++) {
			$file = 'data/'.$cookie_value.'/'.$x.'.txt';
			file_put_contents($file, 'NULL');
		}
		
		header('Location: index.php');
	}
}
?>
<!DOCTYPE html>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Survey</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<!-- Bootstrap css-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

</head>

<body>
<section class="section">
	<div class="container">
		<div class="row">	
			<div class="col-12 col-lg-12">
				<?php 
				if($error) {
					echo '<div class="alert alert-danger">'.$error.'</div>'; 
				}	
				?>
				<form class="start" method="post" action="form.php">			
					<input type="text" class="form-control" name="username" placeholder="Fill in your name" />
					<br />					
					<button type="submit" class="btn btn-primary surveysubmit" name="start" value="start">SUBMIT</button>
				</form>
			</div>
		</div>
	</div>
</section>

</body>
</html>