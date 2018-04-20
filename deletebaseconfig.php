
<html>
<head>
<meta charset="utf-8">
<title>get提交值</title>
</head>
<body>
PLEASE INPUT THE ID OF THE COURSE YOU WANT TO DELETE.<br />
 <form action="<?php $_PHP_SELF ?>" method="GET">
  TEST_ID: <input type="text" name="test_id" />
  <input type="submit" />
 </form>
</body>
</html>

<?php
require_once('../config.php');
require_once($CFG->dirroot.'/enrol/enrol.class.php');
require_once('lib.php');
require_once($CFG->libdir.'/blocklib.php');
require_once('edit_form.php');
require_once 'Excel/reader.php';
require_once('../lib/moodlelib.php');

if (!isloggedin()) {
  $wwwroot = $CFG->wwwroot;
  if (!empty($CFG->loginhttps)) {
     $wwwroot = str_replace('http:','https:', $wwwroot);
  }
  // do not use require_login here because we are usually comming from it
  redirect($wwwroot.'/login/index.php');
}
require_login();
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
if (has_capability('moodle/course:create', $systemcontext)){
	echo "<meta charset=\"utf-8\">";
	if($_GET["test_id"])
	{
		$test_id = $_GET["test_id"]; 
	
		if (!ereg("^[0-9]+$",$test_id)){
			echo "please insert numeric id of the course";
			$wwwroot = $CFG->wwwroot;
			$wwwroot .="/course/deletebaseconfig.php";  
			header("Location: $wwwroot");
		}
		else
		{
			$course_cat = (int)$test_id;
			$sql = "SELECT name FROM mdl_course_categories
				WHERE id = $course_cat";
			$www = get_field_sql($sql,false);
			if($www)
			{
				echo	"ARE YOU SURE TO DELETE THE COURSE ".$www."?"."<br />";
				if(!empty($_POST['submit1'])){
				$wwwroot = $CFG->wwwroot;
				$wwwroot .="/course/deletebase.php?course_cat=$course_cat"; 
				header("Location: $wwwroot");
				}	
				if(!empty($_POST['submit2'])){
				$wwwroot = $CFG->wwwroot;
				$wwwroot .="/course/"; 
					header("Location: $wwwroot");
				}
			}
			else 
			{
				echo "THE COURSE DOESN'T EXIST,TRY AGAIN";
			}
		} 

	}
}
else  
{
	$wwwroot = $CFG->wwwroot;
	//$wwwroot .="/course/"; 
	header("Location: $wwwroot");
}
?>

<?php

error_reporting(E_ALL   &   ~E_NOTICE); //屏蔽未设置参数运用的报错信息

?> 

<form    method="post"  name="myForm"> 
 <input type="submit" name = "submit1" value="Click here to confirm" />
<input type="submit" name = "submit2" value="No,i will do it later" />

