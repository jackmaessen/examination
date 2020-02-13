<?php 
session_start();
$cookie_name = "user";
if (!isset($_COOKIE[$cookie_name]) ) {	
	header('Location: form.php');
}

include('settings.php');

?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Examination</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<!-- Bootstrap css-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<style>
#countdowntimer {
	height: 20px;	
}
.results {
	background: #f3f3f3;
	padding: 10px;
	margin-bottom: 20px;
}
</style>
</head>

<body>
<section class="section">
	<div class="container">
		<div class="row">
	
			<div class="col-12 col-lg-12">

				<div class="questionswrapper">

				<?php
				
				
				// if expiration is set to true
				if($expire) {
					include 'countdown.php';
					echo '<span class="text-danger">Examination expires in:<br /></span>';
					echo '<div class="text-danger" id="countdowntimer"></div><br />';
					if($currenttime > $expirationtime) {
						$_SESSION['finished'] = 1;
					}
				}
				
				if($_SERVER['REQUEST_METHOD'] == 'POST') { 
														
					// set session for questions
					if( !isset($_SESSION['i']) )  {
						$_SESSION['i'] = 1;												
					}

					$session = $_SESSION['i'];					
					$user = $_COOKIE[$cookie_name];	
					
					// finish and results session
					$count_questions = count($questions); // number of questions
					$finish_session = $count_questions + 1;
					$results_session = $count_questions + 2;
					
					// submitting radio value		
					if ( isset($_POST['radio']) && $session < $finish_session && $session > 0) {	// check if session is set, at least 1 radio is selected and session between 0 and finish session
							
						if($_POST['next'] || $_POST['previous']) { // both PREV and NEXT do submit							
							$file = 'data/'.$user.'/'.$session.'.txt';												
							$data = $_POST['radio'];
										
							// Write the contents back to the file if not finished 
							if( !isset($_SESSION['finished']) )  {
																
								if($expire) {
									if($currenttime < $expirationtime) {
										file_put_contents($file, $data);										
									}																			
								}
								else {
									file_put_contents($file, $data);
								}
								
							}									
						}
							
					}
				
					// incrment/decrement session after submit 
					if($_POST['previous']) {
						if( $_SESSION['i'] > 1) { // prevent to go back to start or negative session
							$_SESSION['i']--;	
						}			
					}
					if($_POST['next']) {
						if($_SESSION['i'] != $finish_session) { // prevent to create session higher then finish page 						
							$_SESSION['i']++;							
						}			
					}
					if($_POST['start']) {						
						$_SESSION['i'] = 1;															
					}
					
					
																			
					// new value for session question after increment or decrement
					$session = $_SESSION['i'];
					
					// output user and status
					echo 'Name: <b>'.$user.'</b>&nbsp;&nbsp;';
					if( !isset($_SESSION['finished']) && $session != $results_session )  {
						echo 'Status: <b class="text-success">Active</b>';
					}
					else {
						echo 'Status: <b class="text-danger">Finished</b>';
					}
								
																				
					// question nr quick navigate
					if(isset($_POST['goto'])) {
						$goto = $_POST['goto'];
						if($goto > 0 && $goto < $finish_session) {
							$_SESSION['i'] = $goto;
							$session = $_SESSION['i'];
						}
						elseif(!empty($goto)) {
							echo '<div class="text-danger">Question '.$goto.' does not exist!</div>';
						}
						
					}
					
					// back button on results page
					if(isset($_POST['back'])) {
						$_SESSION['i'] = 1;
						$session = $_SESSION['i'];
					}
					// results button on every page when finished
					if(isset($_POST['results'])) {
						$_SESSION['i'] = $results_session;
						$session = $_SESSION['i'];
					}
					
					
					// bind questions and options to a session	
					$question = $questions[$session];
					$options = $options[$session];
					
					
					// grab data from file and select the radio that was selected (via jquery)
					$file = 'data/'.$user.'/'.$session.'.txt';				
					$current = file_get_contents($file);
					$chosen_answer = substr($current, 0,1); // chosen answer by client
					
										
					// progress
					$progress = $session / $finish_session * 100;	
					
					// header each question number	
					if($session <= $count_questions && $session != 0) {
						echo '<div class="number">Question: '.$session.'</div>';
					}
					
					// FINISHED PAGE
					// first collect data
					$dir = 'data/'.$user;
					$all_files = glob($dir.'/*.*');
					natsort($all_files);
					
					$empty = 0;				
					// count and show questions that have not been answered yet
					if($session == $finish_session) {																												
						foreach($all_files as $file) {
							$each_file = file_get_contents($file);							
							if( strlen($each_file) > 1) { // check if user has chosen an answer for each question (empty file contains NULL, so > 1)
									$empty++;
									echo '<div class="text-danger">Question '.str_replace('.txt','',basename($file)).' not answered!';		
							}
						}						
						if($empty == 0) {
							echo '<div class="number text-success"><h5>All questions answered!</h5></div>';
						}
						
					}
									
					
					// RESULTS 	PAGE
					if( $session == $results_session ) {
						$results = true;	
						if ($empty == 0) { // all questions have been filled in
					
							// compare the answers with admins' answers	(correct answers)
							$dir = 'data/admin';
							$admin_files = glob($dir.'/*.*');
							natsort($admin_files);
							
							// files from admin with the correct answer
							foreach($admin_files as $file) {
								$each_file = file_get_contents($file);								
								$correct_answer = substr($each_file, 0,1);
								$correct_answers[] = $correct_answer; // create array for the correct answers										
							} 
							// files from users 
							foreach($all_files as $file) {
								$each_file = file_get_contents($file);								
								$chosen_answer = substr($each_file, 0,1);
								$chosen_answers[] = $chosen_answer;	// create array for the chosen answers																											
							} 
						
							echo '<br /><br />';
														
						    $compare_values_admin = array_diff_assoc($correct_answers, $chosen_answers) ;	// compare admin array with user array
							$count_wrong = 	count($compare_values_admin);						
							
							$perc_wrong = ( $count_wrong / count($all_files) * 100);
							$perc_correct = (100 - round($perc_wrong, 2) ).' %';
						
											
							?>
							<!-- GENERAL RESULTS -->
							<div class="results">
								<h5>Results</h5>
								<table class="table">
									<tr>
										<td>Questions:</td>
										<td><b><?php echo count($all_files); ?></b></td>
									</tr>
									<tr>
										<td>Fault:</td>
										<td class="text-danger"><b><?php echo $count_wrong; ?></b></td>
									</tr>
									<tr>
										<td>Correct:</td>
										<td class="text-success"><b><?php echo $perc_correct; ?></b></td>
									</tr>					
								</table>
						
								
								
								<?php
								if($count_wrong > 0) { // show faults if questions have been wrong answered
								?>
								<!-- FAULTS -->							
								<table class="table">
								<th class="text-danger">Incorrectly answered questions</th><th>Correct answer</th>
								
								<?php	
								foreach($compare_values_admin as $key => $value) {
									echo '<tr>';
									echo '<td class="text-danger"><b>'.($key + 1).'</b></td>'; // question nr
									echo '<td class="text-success"><b>'.($value + 1).'</b></td>'; // chosen answer
									echo '</tr>';																																				
								}
								?>
								</table>																																						
							</div>
							<?php
							} // end if wrong > 0
							
							// create session; make correct answers green; jquery later
							if( !isset($_SESSION['finished']) )  {
								$_SESSION['finished'] = 1;												
							}
						}
						else {
							echo '<div class="text-danger">Please go back and fill in all questions!</div>';
						}	
						
					
					
					} // end results session				    			   		   
									
				} // end server request POST
								
				
				if(!$results && $session != 0 && $empty == 0) {
				?>
					<!-- progressbar -->
					<div class="progress">
					  <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<br />
				<?php
				}

				// start button
				if($session < 1) {				
				?>					
				<form class="start" method="post" action="index.php">											
					<button type="submit" class="btn btn-primary" name="start" value="start">START EXAMINATION</button>
				</form
				
				<?php
				
				}
				elseif($session > 0 && $session <= $count_questions) {					
				?>
					
					
					<div class="question"><b><?php echo $question; ?></b></div>	
					<br />
					<form class="options" method="post" id="question" action="index.php">												   																											
						   
						<?php						
						// options 
						foreach ($options as $key => $value) {
						  
							?> 
							<div class="form-check form-group"> 
								<label class="form-check-label">
									<input class='radio form-check-input' type='radio' name='radio' value='<?php echo $key; ?>' /><?php echo $value; ?>
								</label>
							</div>
							
							<?php
						} 
						?>
						<div class="form-group">			
							<input type="text" class="form-control" name="goto" placeholder="Question nr + NEXT" />
						</div>
						<button type="submit" class="btn btn-default" name="previous" value="previous">PREV</button>
						<button type="submit" class="btn btn-success" name="next" value="next">NEXT</button>
						<?php
						if( isset($_SESSION['finished']) )  {
							echo '<button type="submit" class="btn btn-primary" name="results" value="results">RESULTS</button>';
						}
						?>
						
																	  
					</form>
					<div class="clearfix"></div>
					<br />
					
					<?php
				
				}
				elseif($session == $finish_session) {
					?>
					<form class="finish" method="post" action="index.php">
						<button type="submit" class="btn btn-default surveysubmit" name="previous" value="previous">PREV</button>
						<?php 
						if($empty == 0) { // show button only if all questions filled in
							echo '<button type="submit" class="btn btn-primary surveysubmit" name="results" value="results">RESULTS</button>';
						}
						?>
					</form
					
					<?php
				}
				elseif($results) {
					?>
					<form class="finish" method="post" action="index.php">
						<button type="submit" class="btn btn-default surveysubmit" name="back" value="back">BACK</button>		
					</form
					<?php
				}

					?>

				   
				</div>	<!-- end questionwrapper -->	
				
				<?php										
				// grab the correct answers from admin for each question
				$admin_file = 'data/admin/'.$session.'.txt';
				$admin_content = file_get_contents($admin_file);
				$admin_value = substr($admin_content, 0,1);
										
				?>
				<script>
				// select the radio that has been chosen
					$('input').filter('[value=<?php echo $chosen_answer; ?>]').attr('checked', 'checked');
				</script>
				<?php
				// mark the correct option of each question 
				if( isset($_SESSION['finished']) )  {
				?>
				<script>
				// show the correct answer 
					$('.form-group:has(input[value=<?php echo $admin_value; ?>])').addClass('alert-success');
				</script>
				<?php	
				}
				?>
				</script>
								
				
			</div><!-- end col -->
		</div><!-- end row -->
	</div><!-- end container -->
</section>

</body>
</html>


