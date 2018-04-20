




<?php
require_once('../config.php');
require_once($CFG->dirroot.'/enrol/enrol.class.php');
require_once('lib.php');
require_once($CFG->libdir.'/blocklib.php');
require_once('edit_form.php');
require_once 'Excel/reader.php';
require_once('../lib/moodlelib.php');
echo "<meta charset=\"utf-8\">";

if (!isloggedin()) {
  $wwwroot = $CFG->wwwroot;
  if (!empty($CFG->loginhttps)) {
     $wwwroot = str_replace('http:','https:', $wwwroot);
  }
  // do not use require_login here because we are usually comming from it
  redirect($wwwroot.'/login/index.php');
}
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
if (has_capability('moodle/course:create', $systemcontext)){

	$course_cat = (int)$_GET["course_cat"];

	$sql = "TRUNCATE TABLE mdl_assignment_oj_submissions";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "delete mdl_course_modules success";
	else echo "delete mdl_course_modules fail";
	echo "<br />";
	
	$sql = "DELETE mct FROM (mdl_assignment AS ma INNER JOIN mdl_course AS mc
		ON ma.course = mc.id)INNER JOIN mdl_context AS mct 
		ON ma.id = mct.instanceid  
		WHERE mc.category = $course_cat AND mct.contextlevel = 70";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";

	$sql = "DELETE mct FROM mdl_context AS mct INNER JOIN mdl_course AS mc
		ON mct.instanceid = mc.id
		WHERE mc.category = $course_cat AND mct.contextlevel = 50";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";
	
	$sql = "DELETE FROM mdl_context 
		WHERE instanceid = $course_cat AND contextlevel = 40";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";



	$sql = "DELETE mao FROM (mdl_assignment AS ma INNER JOIN mdl_course AS mc
		ON ma.course = mc.id)INNER JOIN mdl_assignment_oj AS mao 
		ON ma.id = mao.assignment  
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";

		$sql = "DELETE maot FROM (mdl_assignment AS ma INNER JOIN mdl_course AS mc
		ON ma.course = mc.id)INNER JOIN mdl_assignment_oj_tests AS maot 
		ON ma.id = maot.assignment  
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";

	$sql = "DELETE mas FROM (mdl_assignment AS ma INNER JOIN mdl_course AS mc
		ON ma.course = mc.id)INNER JOIN mdl_assignment_submissions AS mas 	
		ON ma.id = mas.assignment  
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";


	$sql = "DELETE ma FROM mdl_assignment AS ma INNER JOIN mdl_course AS mc
		ON ma.course = mc.id
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";

	$sql = "DELETE mcl FROM mdl_course_level AS mcl INNER JOIN mdl_course AS mc
		ON mcl.course = mc.id
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";


	$sql = "DELETE mcm FROM mdl_course_modules AS mcm INNER JOIN mdl_course AS mc
		ON mcm.course = mc.id
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";


	$sql = "DELETE mcs FROM mdl_course_sections AS mcs INNER JOIN mdl_course AS mc
		ON mcs.course = mc.id
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";


	$sql = "DELETE mgi FROM mdl_grade_items AS mgi INNER JOIN mdl_course AS mc
		ON mgi.courseid = mc.id
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";
	
	$sql = "DELETE mu FROM mdl_user AS mu INNER JOIN mdl_course AS mc
		ON mu.id = mc.id
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";
	
	$sql = "DELETE mup FROM mdl_user_preferences AS mup INNER JOIN mdl_course AS mc
		ON mup.userid = mc.id
		WHERE mc.category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";
	

	$sql = "DELETE FROM mdl_course
		WHERE category = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";
	
	$sql = "DELETE FROM mdl_course_categories
		WHERE id = $course_cat";
	$delete = execute_sql($sql,false);
	if($delete)
		echo "it works"."<br />";
	else echo "it fails"."<br />";
	

	echo "<br />";
	$wwwroot = $CFG->wwwroot;
$wwwroot .="/course";
header("refresh:5;url=$wwwroot");
	print('正在加载，请稍等...<br>五秒后自动跳转。');
}
else  
{
	$wwwroot = $CFG->wwwroot;
	//$wwwroot .="/course/"; 
	header("Location: $wwwroot");
}	
?>
	

