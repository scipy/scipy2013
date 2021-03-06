<?php

//$requested_page = $_SERVER[REQUEST_URI];
//  USER AUTHORIZATION                         //
//===============================================
session_start();
if(!isset($_SESSION['formregusername'])) 
  {
    if(!isset($_SESSION['formusername']))

{

$_SESSION['requested_page'] = $_SERVER[REQUEST_URI];
header("location:registered_login.php");
}
}

//===============================================
// IF SUCCESSFUL PAGE CONTENT                  //
//===============================================


$yes_nos = array('1' => 'Yes','0' => 'No');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Just display the form if the request is a GET
    display_form(array());
} else {
    // The request is a POST, so validate the form
    $errors = validate_form();
    if (count($errors)) {
        // If there were errors, redisplay the form with the errors
        display_form($errors);
    } else {
        // The form data was valid, so update database and display success page

include('inc/db_conn.php');

// add registered user info here

$username = $_SESSION['formregusername'];

$sql = "SELECT clients.id, ";
$sql .= "first_name, "; 
$sql .= "last_name "; 
$sql .= "FROM clients "; 
$sql .= "LEFT JOIN participants ";
$sql .= "ON clients.id = client_id ";
$sql .= "WHERE username= \"$username\"";
//$sql .= "AND conference_id = 2";

$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {

$id=$row['id'];
$name = $row['first_name'] . " " . $row['last_name'];
}

$subject = htmlentities($_POST['subject']);
$content = htmlentities($_POST['content']);
$panelists = htmlentities($_POST['panelists']);
$will_moderate = htmlentities($_POST['will_moderate']);
$moderator = htmlentities($_POST['moderator']);

if ($will_moderate == 1)
  {$moderator = $name;}


$sql ="INSERT INTO open_agendas ";
$sql .="(subject, ";
$sql .="content, ";
$sql .="panelists, ";
$sql .="will_moderate, ";
$sql .="moderator, ";
$sql .="conference_id, ";
$sql .="type, ";
$sql .="accepted, ";
$sql .="created_by, ";
$sql .="updated_by, ";
$sql .="created_at, ";
$sql .="updated_at) ";
$sql .="VALUES ";
$sql .="(\"$subject\", ";
$sql .="\"$content\", ";
$sql .="\"$panelists\", ";
$sql .="\"$will_moderate\", ";
$sql .="\"$moderator\", ";
$sql .="2, ";
$sql .="\"bof\", ";
$sql .="0, ";
$sql .="\"$id\", ";
$sql .="\"$id\", ";
$sql .="NOW(), ";
$sql .="NOW())";

$result = @mysql_query($sql, $connection) or die("Error #". mysql_errno() . ": " . mysql_error());

?>

<!DOCTYPE html>
<html>
<?php $thisPage="BoFs"; ?>
<head>
<?php include_once "inc/markdown.php"; ?>
<?php include('inc/header.php') ?>

<link rel="shortcut icon" href="http://conference.scipy.org/scipy2013/favicon.ico" />
</head>

<body>

<div id="container">

<?php include('inc/page_headers.php') ?>

<section id="sidebar">
  <?php include("inc/sponsors.php") ?>
</section>

<section id="main-content">
<h1>BoF Submission Form</h1>

<p>Thank you for your submission. The following information has been recorded.</p>

<p>The suggestions are moderated and once approved will appear on the BoFs list page.</p>

<p><span class="data_field">Subject:</span> <?php echo $subject ?></p>
<p><span class="data_field">Description:</span> <?php echo Markdown($content) ?></p>
<p><span class="data_field">Panelists:</span> <?php echo $panelists ?></p>
<p><span class="data_field">Moderator:</span> <?php echo $moderator ?></p>
</section>
<div style="clear:both;"></div>
<footer id="page_footer">
<?php include('inc/page_footer.php') ?>
</footer>
</div>
</body>

</html>

<?php
            }
}





function display_form($errors) {

  global $yes_nos;
    
  $defaults['subject'] = isset($_POST['subject']) ? htmlentities($_POST['subject']) : '';
  $defaults['content'] = isset($_POST['content']) ? htmlentities($_POST['content']) : '';
  $defaults['moderator'] = isset($_POST['moderator']) ? htmlentities($_POST['moderator']) : '';
  if (!empty($errors)) {
                        $errors['overall'] = '<< Please see errors below >>';
                       }

    foreach ($yes_nos as $key => $yes_no) {
         if (isset($_POST['will_moderate']) && ($_POST['will_moderate'] == $key)) {
         $defaults['will_moderate'][$key] = "checked";
        } else {
            $defaults['will_moderate'][$key] = "unchecked";
        }
    } 


?>

<!DOCTYPE html>
<html>
<?php $thisPage="BoFs"; ?>
<head>

<?php
//force redirect to secure page
//if($_SERVER['SERVER_PORT'] != '443') { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
?>

        <link rel="stylesheet" href="inc/validationEngine.jquery.css" type="text/css"/>
        <!-- <link rel="stylesheet" href="inc/template.css" type="text/css"/> -->
        <script src="inc/jquery-1.6.min.js" type="text/javascript">
        </script>
        <script src="inc/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8">
        </script>
        <script src="inc/jquery.validationEngine.js" type="text/javascript" charset="utf-8">
        </script>
        <script>
            jQuery(document).ready(function(){
                // binds form submission and fields to the validation engine
                jQuery("#formID").validationEngine();
            });
        </script>


<!--<script type="text/javascript" src="../inc/jquery.js"></script> -->
<script type="text/javascript" src="inc/jquery.wordcount.js"></script>
<script type="text/javascript">
<!--//---------------------------------+
//  Developed by Roshan Bhattarai 
//  Visit http://roshanbh.com.np for this script and more.
//  This notice MUST stay intact for legal use
// --------------------------------->
$(document).ready(function()
{
	
	$('#content').wordCount();
	//alternatively you can use the below method to display count in element with id word_counter  
	//$('#word_count').wordCount({counterElement:"word_counter"});

	
});
</script>

<?php include('inc/header.php') ?>

<link rel="shortcut icon" href="http://conference.scipy.org/scipy2013/favicon.ico" />

</head>

<body>

<div id="container">

<?php include('inc/page_headers.php') ?>

<section id="sidebar">
  <?php include("inc/sponsors.php") ?>
</section>


<section id="main-content">
<h1>BoF Suggestion</h1>

<form method="post" name="SprintInfo" action="<?php echo $SERVER['SCRIPT_NAME'] ?>">

<div id="instructions">
<p>You just have to let us know a title and brief description of the BoF.</p>

<p>Program chairs: Kyle Mandli &amp; Matthew Turk</p>
</div>

<?php print_error('overall', $errors) ?>

<div class="row">
  <div class="cell" style="width: 20%;">
    <label for="subject">BoF Subject:<?php print_error('subject', $errors) ?></label> 
  </div>
  <div class="cell" style="width: 65%;">
    <input type="text" name="subject" id="subject" value="<?php echo $defaults['subject'] ?>" style="width: 100%;"/>
  </div>
</div>

<div class="row">

<div class="row">
  <div class="cell" style="width: 20%;">
    <span class="form_tips"><label for="content">BoF Summary:<?php print_error('content', $errors) ?><br /><span class="other_form_tips">~150 words</span></label></span> 
  </div>
  <div class="cell" style="width: 65%;">
    <textarea id="content" name="content" rows="5"><?php echo $defaults['content'] ?></textarea>
    <p><span class="other_form_tips">Word Count : <span id="display_count">0</span></span></p>
  </div>
</div>

<div class="row">
  <div class="cell" style="width: 20%;">
    <span class="form_tips"><label for="panelists">Panelist Suggestions:<?php print_error('panelists', $errors) ?></label></span> 
  </div>
  <div class="cell" style="width: 65%;">
    <textarea id="panelists" name="panelists" rows="3"><?php echo $defaults['panelists'] ?></textarea>
  </div>
</div>
<div class="row">
  <div class="cell" style="width: 20%;">
    <span class="form_tips"><label for="panelists">Are you willing to moderate:<?php print_error('will_moderate', $errors) ?></label></span> 
  </div>
  <div class="cell" style="width: 65%;">
            <?php foreach ($yes_nos as $key => $yes_no) {
            echo "<input type='radio' name='will_moderate' value='$key' {$defaults['will_moderate'][$key]} /> $yes_no \n";
            } ?>
    <br /><label for="moderator">if no, Suggested Moderator:<?php print_error('moderator', $errors) ?></label> <input type="text" name="moderator" id="moderator" value="<?php echo $defaults['moderator'] ?>" style="width: 50%;"/>
  </div>
</div>


<div align="center">
<input type="submit" name="submit" value="Suggest BoF">
</div>
</form>
</section>
<div style="clear:both;"></div>
<footer id="page_footer">
<?php include('inc/page_footer.php') ?>
</footer>
</div>
</body>

</html>


<?php }

// A helper function to make generating the HTML for an error message easier
function print_error($key, $errors) {
    if (isset($errors[$key])) {
        print "<br /><span class='error'>{$errors[$key]}</span>";
    }
}

function validate_form() {

    global $yes_nos;
    
    // Start out with no errors
    $errors = array();


    // title is required and must be at least 2 characters
    if (! (isset($_POST['subject']) && (strlen($_POST['subject']) > 1))) {
        $errors['subject'] = '<< Enter Subject >>';
    }


    // description is required and must be at least 2 characters
    if (! (isset($_POST['content']) && (strlen($_POST['content']) > 1))) {
        $errors['content'] = '<< Enter Summary >>';
    }


    // email is required and must be at least 2 characters
    if (! (isset($_POST['panelists']) && (strlen($_POST['panelists']) > 1))) {
        $errors['panelists'] = '<< Enter panelists >>';
    }



    return $errors;


}


?>