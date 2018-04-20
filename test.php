<?php
ob_start();
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

	$data = new Spreadsheet_Excel_Reader();

	$data->setOutputEncoding('utf-8');
		
	$data->read('./upload/test.xls');



//*******************initialize the assignment table in difficult order

$sql = "UPDATE mdl_my_assignment
	SET var5 = id";
$zzz = execute_sql($sql,false);
if($zzz)
	echo "it works"."<br />";
else echo "sorry"."<br />";

$sql = "INSERT INTO mdl_assignment_order(name,description,assignmenttype,resubmit,maxbytes,grade,var5)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade,var5
	 FROM mdl_my_assignment AS mma 
	WHERE SUBSTR(mma.name,1,1) = 1
	ORDER BY id";
$www = execute_sql($sql,false);
if($www)
	echo "it works";
else echo "sorry";
echo "<br />";


$sql = "SELECT LAST_INSERT_ID()";
$ASS_1 = get_field_sql($sql);
echo $ASS_1."<br />";


$sql = "INSERT INTO mdl_assignment_order(name,description,assignmenttype,resubmit,maxbytes,grade,var5)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade,var5
	 FROM mdl_my_assignment AS mma
	WHERE SUBSTR(mma.name,1,1) = 2";
$www = execute_sql($sql,false);
if($www)
	echo "it works";
else echo "sorry";
echo "<br />";

$sql = "SELECT LAST_INSERT_ID()";
$ASS_3  = get_field_sql($sql);
echo $ASS_3."<br />";

$ASS_2 = $ASS_3 - 1;

$sql = "INSERT INTO mdl_assignment_order(name,description,assignmenttype,resubmit,maxbytes,grade,var5)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade,var5
	 FROM mdl_my_assignment AS mma
	WHERE SUBSTR(mma.name,1,1) = 3";
$www = execute_sql($sql,false);
if($www)
	echo "it works";
else echo "sorry";
echo "<br />";

$sql = "SELECT LAST_INSERT_ID()";
$ASS_5 = get_field_sql($sql);
echo $ASS_5."<br />";

$ASS_4 = $ASS_5 - 1;

$sql = "INSERT INTO mdl_assignment_order(name,description,assignmenttype,resubmit,maxbytes,grade,var5)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade,var5
	 FROM mdl_my_assignment AS mma
	WHERE SUBSTR(mma.name,1,1) = 4";
$www = execute_sql($sql,false);
if($www)
	echo "it works";
else echo "sorry";
echo "<br />";

$sql = "SELECT LAST_INSERT_ID()";
$ASS_7 = get_field_sql($sql);
echo $ASS_7."<br />";

$ASS_6 = $ASS_7 - 1;

$sql = "SELECT MAX(id)
	FROM mdl_assignment_order";
$ASS_8= get_field_sql($sql);
echo $ASS_8."<br />";

$sql = "INSERT INTO mdl_assignment_oj_order_tests(assignment,input,output,subgrade)
	SELECT mao.id,mmaot.input,mmaot.output,mmaot.subgrade
	FROM mdl_my_assignment_oj_tests AS mmaot,mdl_assignment_order AS mao
	WHERE mmaot.assignment = mao.var5";
$xxx = execute_sql($sql,false);
if($xxx)
	echo "it works"."<br />";
else echo "sorry"."<br />";



//**********create a new test (course category)

$testname = $data->sheets[0]['cells'][1][1];
echo $testname;


$sql = "INSERT INTO mdl_course_categories(sortorder,depth,description)
	VALUE(1,1,'')";
$cate = execute_sql($sql,false);

$sql = "SELECT LAST_INSERT_ID()";
$category_id = get_field_sql($sql);
echo $category_id."<br />";

$sql = "UPDATE mdl_course_categories
		SET name = '$testname $category_id',
			path = '/$category_id'
		WHERE id = $category_id";
$cate = execute_sql($sql,false);


$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
		VALUE(40,$category_id,2)";
$cate = execute_sql($sql,false);


$sql = "SELECT LAST_INSERT_ID()";
$catecontext = get_field_sql($sql,false);
//make sure it is right 
$sql = "UPDATE mdl_context
		SET path = '/1/$catecontext'
		WHERE id = $catecontext";
$cate =execute_sql($sql,false);


//************* start reading student information


for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
	//for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
		$newcourse = (int)$data->sheets[0]['cells'][$i][1];
		$class =  $data->sheets[0]['cells'][$i][2];
		$wholename = $data->sheets[0]['cells'][$i][3];
		//echo $newcourse."<br />".$wholename."<br />";
	if((!$newcourse) || (!$wholename) || (!$class))
		continue;





//*********create a new course


$sql = "INSERT INTO mdl_course
	SELECT *
	FROM mdl_my_course
	WHERE id = 10000";
$aaa = execute_sql($sql,false);
if($aaa)
{
	echo "a works";
	echo "<br />";
}
else
{
	echo "a sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course
		SET category = $category_id
		WHERE id = 10000";
$catcourse = execute_sql($sql,false);
if($catcourse)
{
	echo "category of course update works!"."<br />";
}
else 
{
	echo "category of course update sorry!"."<br />";
}


$sql = "SELECT MAX(sortorder)
	FROM mdl_course";
$bbb = get_field_sql($sql);
$bbb = $bbb+1;//new sortorder
$sql = "UPDATE mdl_course
	SET id = $newcourse,
	fullname = '$wholename',
        shortname='test$newcourse',
	sortorder = $bbb
	WHERE id = 10000";
$ccc = execute_sql($sql,false);
if($ccc)
{
	echo "c works";
	echo "<br />";
}
else
{
	echo "c sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_categories
	SET coursecount = coursecount+1
	WHERE id = $category_id";
$ddd = execute_sql($sql,false);
if($ddd)
{
	echo "d works";
	echo "<br />";
}
else
{
	echo "d sorry";
	echo "<br />";
}

//create the connection
$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(50,$newcourse,3)";
$fff = execute_sql($sql,false);
if($fff)
{
	echo "f works";
	echo "<br />";
}
else
{
	echo "f sorry";
	echo "<br />";

}
$sql = "SELECT LAST_INSERT_ID()";
$ggg = get_field_sql($sql);
if($ggg)
{
	echo "g works";
	echo "<br />";
}
else
{
	echo "g sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg'
	WHERE id = $ggg";
$hhh = execute_sql($sql,false);
if($hhh)
{
	echo "h works";
	echo "<br />";
}
else
{
	echo "h sorry";
	echo "<br />";
}






/////***********************set up assignment





//difficulty one
$random_1_one = rand($ASS_1,$ASS_2);
do{
	$random_1_two = rand($ASS_1,$ASS_2);
}while($random_1_one == $random_1_two);
do{
	$random_1_three = rand($ASS_1,$ASS_2);
}while(($random_1_three == $random_1_two)||($random_1_three == $random_1_one));
do{
	$random_1_four = rand($ASS_1,$ASS_2);
}while(($random_1_four == $random_1_two)||($random_1_four == $random_1_one)||($random_1_four == $random_1_three));

//assignment_1_one

$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_1_one";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i11 works";
	echo "<br />";
}
else
{
	echo "i11 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_1_one = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_1_one";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j11 works";
	echo "<br />";
}
else
{
	echo "j11 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_1_one,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k11 works";
	echo "<br />";
}
else
{
	echo "k11 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_1_one";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l11 works";
	echo "<br />";
}
else
{
	echo "l11 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_1_one
	WHERE assignment = $random_1_one";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v11 works";
	echo "<br />";
}
else
{
	echo "v11 sorry";
	echo "<br />";
}

//assignment_1_two

$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_1_two";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i12 works";
	echo "<br />";
}
else
{
	echo "i12 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_1_two = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_1_two";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j12 works";
	echo "<br />";
}
else
{
	echo "j12 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_1_two,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k12 works";
	echo "<br />";
}
else
{
	echo "k12 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_1_two";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l12 works";
	echo "<br />";
}
else
{
	echo "l12 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_1_two
	WHERE assignment = $random_1_two";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v12 works";
	echo "<br />";
}
else
{
	echo "v12 sorry";
	echo "<br />";
}


//assignment_1_three
$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_1_three";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i13 works";
	echo "<br />";
}
else
{
	echo "i13 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_1_three = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_1_three";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j13 works";
	echo "<br />";
}
else
{
	echo "j13 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_1_three,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k13 works";
	echo "<br />";
}
else
{
	echo "k13 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_1_three";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l13 works";
	echo "<br />";
}
else
{
	echo "l13 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_1_three
	WHERE assignment = $random_1_three";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v13 works";
	echo "<br />";
}
else
{
	echo "v13 sorry";
	echo "<br />";
}

//$assignment_1_four
$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_1_four";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i14 works";
	echo "<br />";
}
else
{
	echo "i14 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_1_four = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_1_four";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j14 works";
	echo "<br />";
}
else
{
	echo "j14 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_1_four,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k14 works";
	echo "<br />";
}
else
{
	echo "k14 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_1_four";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l14 works";
	echo "<br />";
}
else
{
	echo "l14 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_1_four
	WHERE assignment = $random_1_four";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v14 works";
	echo "<br />";
}
else
{
	echo "v14 sorry";
	echo "<br />";
}

// difficulty two

$random_2_one = rand($ASS_3,$ASS_4);
do{
	$random_2_two = rand($ASS_3,$ASS_4);
}while($random_2_one == $random_2_two);
do{
	$random_2_three = rand($ASS_3,$ASS_4);
}while(($random_2_three == $random_2_two)||($random_2_three == $random_2_one));
do{
	$random_2_four = rand($ASS_3,$ASS_4);
}while(($random_2_four == $random_2_two)||($random_2_four == $random_2_one)||($random_2_four == $random_2_three));

//assignment_2_one

$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_2_one";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i21 works";
	echo "<br />";
}
else
{
	echo "i21 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_2_one = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_2_one";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j21 works";
	echo "<br />";
}
else
{
	echo "j21 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_2_one,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k21 works";
	echo "<br />";
}
else
{
	echo "k21 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_2_one";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l12 works";
	echo "<br />";
}
else
{
	echo "l12 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_2_one
	WHERE assignment = $random_2_one";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v21 works";
	echo "<br />";
}
else
{
	echo "v21 sorry";
	echo "<br />";
}

//assignment_2_two

$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_2_two";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i22 works";
	echo "<br />";
}
else
{
	echo "i22 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_2_two = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_2_two";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j22 works";
	echo "<br />";
}
else
{
	echo "j22 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_2_two,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k22 works";
	echo "<br />";
}
else
{
	echo "k22 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_2_two";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l22 works";
	echo "<br />";
}
else
{
	echo "l22 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_2_two
	WHERE assignment = $random_2_two";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v22 works";
	echo "<br />";
}
else
{
	echo "v22 sorry";
	echo "<br />";
}


//assignment_2_three
$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_2_three";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i23 works";
	echo "<br />";
}
else
{
	echo "i23 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_2_three = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_2_three";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j23 works";
	echo "<br />";
}
else
{
	echo "j23 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_2_three,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k23 works";
	echo "<br />";
}
else
{
	echo "k23 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_2_three";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l23 works";
	echo "<br />";
}
else
{
	echo "l23 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_2_three
	WHERE assignment = $random_2_three";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v23 works";
	echo "<br />";
}
else
{
	echo "v23 sorry";
	echo "<br />";
}

//$assignment_2_four
$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_2_four";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i24 works";
	echo "<br />";
}
else
{
	echo "i24 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_2_four = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_2_four";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j24 works";
	echo "<br />";
}
else
{
	echo "j24 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_2_four,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k24 works";
	echo "<br />";
}
else
{
	echo "k24 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_2_four";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l24 works";
	echo "<br />";
}
else
{
	echo "l24 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_2_four
	WHERE assignment = $random_2_four";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v24 works";
	echo "<br />";
}
else
{
	echo "v24 sorry";
	echo "<br />";
}


// difficulty 3

$random_3_one = rand($ASS_5,$ASS_6);
do{
	$random_3_two = rand($ASS_5,$ASS_6);
}while($random_3_one == $random_3_two);
do{
	$random_3_three = rand($ASS_5,$ASS_6);
}while(($random_3_three == $random_3_two)||($random_3_three == $random_3_one));
do{
	$random_3_four = rand($ASS_5,$ASS_6);
}while(($random_3_four == $random_3_two)||($random_3_four == $random_3_one)||($random_3_four == $random_3_three));

//assignment_3_one

$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_3_one";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i31 works";
	echo "<br />";
}
else
{
	echo "i31 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_3_one = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_3_one";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j31 works";
	echo "<br />";
}
else
{
	echo "j31 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_3_one,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k31 works";
	echo "<br />";
}
else
{
	echo "k31 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_3_one";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l32 works";
	echo "<br />";
}
else
{
	echo "l32 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_3_one
	WHERE assignment = $random_3_one";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v31 works";
	echo "<br />";
}
else
{
	echo "v31 sorry";
	echo "<br />";
}

//assignment_3_two

$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_3_two";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i32 works";
	echo "<br />";
}
else
{
	echo "i32 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_3_two = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_3_two";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j32 works";
	echo "<br />";
}
else
{
	echo "j32 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_3_two,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k32 works";
	echo "<br />";
}
else
{
	echo "k32 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_3_two";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l32 works";
	echo "<br />";
}
else
{
	echo "l32 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_3_two
	WHERE assignment = $random_3_two";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v32 works";
	echo "<br />";
}
else
{
	echo "v32 sorry";
	echo "<br />";
}


//assignment_3_three
$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_3_three";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i33 works";
	echo "<br />";
}
else
{
	echo "i33 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_3_three = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_3_three";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j33 works";
	echo "<br />";
}
else
{
	echo "j33 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_3_three,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k33 works";
	echo "<br />";
}
else
{
	echo "k33 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_3_three";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l33 works";
	echo "<br />";
}
else
{
	echo "l33 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_3_three
	WHERE assignment = $random_3_three";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v33 works";
	echo "<br />";
}
else
{
	echo "v33 sorry";
	echo "<br />";
}

//$assignment_3_four
$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_3_four";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i34 works";
	echo "<br />";
}
else
{
	echo "i34 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_3_four = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_3_four";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j34 works";
	echo "<br />";
}
else
{
	echo "j34 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_3_four,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k34 works";
	echo "<br />";
}
else
{
	echo "k34 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_3_four";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l34 works";
	echo "<br />";
}
else
{
	echo "l34 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_3_four
	WHERE assignment = $random_3_four";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v34 works";
	echo "<br />";
}
else
{
	echo "v34 sorry";
	echo "<br />";
}

//difficulty four

$random_4_one = rand($ASS_7,$ASS_8);
do{
	$random_4_two = rand($ASS_7,$ASS_8);
}while($random_4_one == $random_4_two);
do{
	$random_4_three = rand($ASS_7,$ASS_8);
}while(($random_4_three == $random_4_two)||($random_4_three == $random_4_one));
do{
	$random_4_four = rand($ASS_7,$ASS_8);
}while(($random_4_four == $random_4_two)||($random_4_four == $random_4_one)||($random_4_four == $random_4_three));

//assignment_4_one

$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_4_one";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i41 works";
	echo "<br />";
}
else
{
	echo "i41 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_4_one = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_4_one";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j41 works";
	echo "<br />";
}
else
{
	echo "j41 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_4_one,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k41 works";
	echo "<br />";
}
else
{
	echo "k41 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_4_one";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l42 works";
	echo "<br />";
}
else
{
	echo "l42 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_4_one
	WHERE assignment = $random_4_one";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v41 works";
	echo "<br />";
}
else
{
	echo "v41 sorry";
	echo "<br />";
}

//assignment_4_two

$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_4_two";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i42 works";
	echo "<br />";
}
else
{
	echo "i42 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_4_two = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_4_two";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j42 works";
	echo "<br />";
}
else
{
	echo "j42 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_4_two,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k42 works";
	echo "<br />";
}
else
{
	echo "k42 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_4_two";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l42 works";
	echo "<br />";
}
else
{
	echo "l42 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_4_two
	WHERE assignment = $random_4_two";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v42 works";
	echo "<br />";
}
else
{
	echo "v42 sorry";
	echo "<br />";
}


//assignment_4_three
$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_4_three";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i43 works";
	echo "<br />";
}
else
{
	echo "i43 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_4_three = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_4_three";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j43 works";
	echo "<br />";
}
else
{
	echo "j43 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_4_three,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k43 works";
	echo "<br />";
}
else
{
	echo "k43 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_4_three";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l43 works";
	echo "<br />";
}
else
{
	echo "l43 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_4_three
	WHERE assignment = $random_4_three";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v43 works";
	echo "<br />";
}
else
{
	echo "v43 sorry";
	echo "<br />";
}

//$assignment_4_four
$sql = "INSERT INTO mdl_assignment(name,description,assignmenttype,resubmit,maxbytes,grade)
	SELECT name,description,assignmenttype,resubmit,maxbytes,grade
	FROM mdl_assignment_order
	WHERE id = $random_4_four";
$iii = execute_sql($sql,false);
if($iii)
{
	echo "i44 works";
	echo "<br />";
}
else
{
	echo "i44 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$assignment_4_four = get_field_sql($sql);
$sql = "UPDATE mdl_assignment
	SET course = $newcourse
	WHERE id = $assignment_4_four";
$jjj = execute_sql($sql,false);
if($jjj)
{
	echo "j44 works";
	echo "<br />";
}
else
{
	echo "j44 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj(assignment,language,memlimit)
	VALUES($assignment_4_four,'c',268435456)";
$kkk = execute_sql($sql,false);
if($kkk)
{
	echo "k44 works";
	echo "<br />";
}
else
{
	echo "k44 sorry";
	echo "<br />";
}


$sql = "INSERT INTO mdl_assignment_oj_tests(assignment,input,output,subgrade)
	SELECT assignment,input,output,subgrade
	FROM mdl_assignment_oj_order_tests
	WHERE assignment = $random_4_four";
$lll = execute_sql($sql,false);
if($lll)
{
	echo "l44 works";
	echo "<br />";
}
else
{
	echo "l44 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_assignment_oj_tests
	SET assignment = $assignment_4_four
	WHERE assignment = $random_4_four";
$vvv = execute_sql($sql,false);
if($vvv)
{
	echo "v44 works";
	echo "<br />";
}
else
{
	echo "v44 sorry";
	echo "<br />";
}


//***************************link the assignment and the course
// create a big section
$sql = "INSERT INTO mdl_course_sections(course,section)
	VALUE($newcourse,0)";
$nnn = execute_sql($sql,false);
if($nnn)
{
	echo "n works";
	echo "<br />";
}
else
{
	echo "n sorry";
	echo "<br />";
} 

$sql = "SELECT LAST_INSERT_ID()";
$level_id =get_field_sql($sql);

$sql = "INSERT INTO mdl_course_level(course,level_one,level_two,level_three,level_four)
VALUE($newcourse,$level_id+1,$level_id+2,$level_id+3,$level_id+4)";
$nnn = execute_sql($sql,false);


//first section

$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_1_one)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_1_one = get_field_sql($sql);
if($mmm)
{
	echo "m11 works";
	echo "<br />";
}
else
{
	echo "m11 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_1_two)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_1_two = get_field_sql($sql);
if($mmm)
{
	echo "m12 works";
	echo "<br />";
}
else
{
	echo "m12 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_1_three)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_1_three = get_field_sql($sql);
if($mmm)
{
	echo "m13 works";
	echo "<br />";
}
else
{
	echo "m13 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_1_four)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_1_four = get_field_sql($sql);
if($mmm)
{
	echo "m14 works";
	echo "<br />";
}
else
{
	echo "m14 sorry";
	echo "<br />";
}


$sql ="INSERT INTO mdl_course_sections(course,section,summary,sequence)
	VALUE($newcourse,1,'','$module_1_one,$module_1_two,$module_1_three,$module_1_four')";
$ooo = execute_sql($sql,false);
if($ooo)
{
	echo "o1 works";
	echo "<br />";
}
else
{
	echo "o1 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$section_id_one = get_field_sql($sql);


$sql = "UPDATE mdl_course_modules
	SET section = $section_id_one
	WHERE id = $module_1_one";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p11 works";
	echo "<br />";
}
else
{
	echo "p11 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_course_modules
	SET section = $section_id_one
	WHERE id = $module_1_two";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p12 works";
	echo "<br />";
}
else
{
	echo "p12 sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_modules
	SET section = $section_id_one
	WHERE id = $module_1_three";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p13 works";
	echo "<br />";
}
else
{
	echo "p13 sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_modules
	SET section = $section_id_one
	WHERE id = $module_1_four";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p14 works";
	echo "<br />";
}
else
{
	echo "p14 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_1_one,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r11 works";
	echo "<br />";
}
else
{
	echo "r11 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t11 works";
	echo "<br />";
}
else
{
	echo "t11 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_1_two,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r12 works";
	echo "<br />";
}
else
{
	echo "r12 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t12 works";
	echo "<br />";
}
else
{
	echo "t12 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_1_three,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r13 works";
	echo "<br />";
}
else
{
	echo "r13 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t13 works";
	echo "<br />";
}
else
{
	echo "t13 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_1_four,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r14 works";
	echo "<br />";
}
else
{
	echo "r14 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t14 works";
	echo "<br />";
}
else
{
	echo "t14 sorry";
	echo "<br />";
}

// second section


$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_2_one)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_2_one = get_field_sql($sql);
if($mmm)
{
	echo "m21 works";
	echo "<br />";
}
else
{
	echo "m21 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_2_two)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_2_two = get_field_sql($sql);
if($mmm)
{
	echo "m22 works";
	echo "<br />";
}
else
{
	echo "m22 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_2_three)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_2_three = get_field_sql($sql);
if($mmm)
{
	echo "m23 works";
	echo "<br />";
}
else
{
	echo "m23 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_2_four)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_2_four = get_field_sql($sql);
if($mmm)
{
	echo "m24 works";
	echo "<br />";
}
else
{
	echo "m24 sorry";
	echo "<br />";
}


$sql ="INSERT INTO mdl_course_sections(course,section,summary,sequence)
	VALUE($newcourse,2,'','$module_2_one,$module_2_two,$module_2_three,$module_2_four')";
$ooo = execute_sql($sql,false);
if($ooo)
{
	echo "o2 works";
	echo "<br />";
}
else
{
	echo "o2 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$section_id_two = get_field_sql($sql);


$sql = "UPDATE mdl_course_modules
	SET section = $section_id_two
	WHERE id = $module_2_one";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p21 works";
	echo "<br />";
}
else
{
	echo "p21 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_course_modules
	SET section = $section_id_two
	WHERE id = $module_2_two";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p22 works";
	echo "<br />";
}
else
{
	echo "p22 sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_modules
	SET section = $section_id_two
	WHERE id = $module_2_three";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p23 works";
	echo "<br />";
}
else
{
	echo "p23 sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_modules
	SET section = $section_id_two
	WHERE id = $module_2_four";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p24 works";
	echo "<br />";
}
else
{
	echo "p24 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_2_one,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r21 works";
	echo "<br />";
}
else
{
	echo "r21 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t21 works";
	echo "<br />";
}
else
{
	echo "t21 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_2_two,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r22 works";
	echo "<br />";
}
else
{
	echo "r22 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t22 works";
	echo "<br />";
}
else
{
	echo "t22 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_2_three,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r23 works";
	echo "<br />";
}
else
{
	echo "r23 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t23 works";
	echo "<br />";
}
else
{
	echo "t23 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_2_four,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r24 works";
	echo "<br />";
}
else
{
	echo "r24 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t24 works";
	echo "<br />";
}
else
{
	echo "t24 sorry";
	echo "<br />";
}

// third section


$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_3_one)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_3_one = get_field_sql($sql);
if($mmm)
{
	echo "m31 works";
	echo "<br />";
}
else
{
	echo "m31 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_3_two)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_3_two = get_field_sql($sql);
if($mmm)
{
	echo "m32 works";
	echo "<br />";
}
else
{
	echo "m32 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_3_three)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_3_three = get_field_sql($sql);
if($mmm)
{
	echo "m33 works";
	echo "<br />";
}
else
{
	echo "m33 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_3_four)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_3_four = get_field_sql($sql);
if($mmm)
{
	echo "m34 works";
	echo "<br />";
}
else
{
	echo "m34 sorry";
	echo "<br />";
}


$sql ="INSERT INTO mdl_course_sections(course,section,summary,sequence)
	VALUE($newcourse,3,'','$module_3_one,$module_3_two,$module_3_three,$module_3_four')";
$ooo = execute_sql($sql,false);
if($ooo)
{
	echo "o3 works";
	echo "<br />";
}
else
{
	echo "o3 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$section_id_three = get_field_sql($sql);


$sql = "UPDATE mdl_course_modules
	SET section = $section_id_three
	WHERE id = $module_3_one";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p31 works";
	echo "<br />";
}
else
{
	echo "p31 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_course_modules
	SET section = $section_id_three
	WHERE id = $module_3_two";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p32 works";
	echo "<br />";
}
else
{
	echo "p32 sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_modules
	SET section = $section_id_three
	WHERE id = $module_3_three";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p33 works";
	echo "<br />";
}
else
{
	echo "p33 sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_modules
	SET section = $section_id_three
	WHERE id = $module_3_four";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p34 works";
	echo "<br />";
}
else
{
	echo "p34 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_3_one,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r31 works";
	echo "<br />";
}
else
{
	echo "r31 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t31 works";
	echo "<br />";
}
else
{
	echo "t31 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_3_two,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r32 works";
	echo "<br />";
}
else
{
	echo "r32 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t32 works";
	echo "<br />";
}
else
{
	echo "t32 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_3_three,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r33 works";
	echo "<br />";
}
else
{
	echo "r33 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t33 works";
	echo "<br />";
}
else
{
	echo "t33 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_3_four,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r34 works";
	echo "<br />";
}
else
{
	echo "r34 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t34 works";
	echo "<br />";
}
else
{
	echo "t34 sorry";
	echo "<br />";
}


// forth section


$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_4_one)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_4_one = get_field_sql($sql);
if($mmm)
{
	echo "m41 works";
	echo "<br />";
}
else
{
	echo "m41 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_4_two)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_4_two = get_field_sql($sql);
if($mmm)
{
	echo "m42 works";
	echo "<br />";
}
else
{
	echo "m42 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_4_three)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_4_three = get_field_sql($sql);
if($mmm)
{
	echo "m43 works";
	echo "<br />";
}
else
{
	echo "m43 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_course_modules(course,module,instance)
	VALUE($newcourse,1,$assignment_4_four)";
$mmm = execute_sql($sql,false);
$sql = "SELECT LAST_INSERT_ID()";
$module_4_four = get_field_sql($sql);
if($mmm)
{
	echo "m44 works";
	echo "<br />";
}
else
{
	echo "m44 sorry";
	echo "<br />";
}


$sql ="INSERT INTO mdl_course_sections(course,section,summary,sequence)
	VALUE($newcourse,4,'','$module_4_one,$module_4_two,$module_4_three,$module_4_four')";
$ooo = execute_sql($sql,false);
if($ooo)
{
	echo "o4 works";
	echo "<br />";
}
else
{
	echo "o4 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$section_id_four = get_field_sql($sql);


$sql = "UPDATE mdl_course_modules
	SET section = $section_id_four
	WHERE id = $module_4_one";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p41 works";
	echo "<br />";
}
else
{
	echo "p41 sorry";
	echo "<br />";
}
$sql = "UPDATE mdl_course_modules
	SET section = $section_id_four
	WHERE id = $module_4_two";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p42 works";
	echo "<br />";
}
else
{
	echo "p42 sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_modules
	SET section = $section_id_four
	WHERE id = $module_4_three";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p43 works";
	echo "<br />";
}
else
{
	echo "p43 sorry";
	echo "<br />";
}

$sql = "UPDATE mdl_course_modules
	SET section = $section_id_four
	WHERE id = $module_4_four";
$ppp = execute_sql($sql,false);

if($ppp)
{
	echo "p44 works";
	echo "<br />";
}
else
{
	echo "p14 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_4_one,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r41 works";
	echo "<br />";
}
else
{
	echo "r41 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t41 works";
	echo "<br />";
}
else
{
	echo "t41 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_4_two,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r42 works";
	echo "<br />";
}
else
{
	echo "r42 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t42 works";
	echo "<br />";
}
else
{
	echo "t42 sorry";
	echo "<br />";
}

$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_4_three,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r43 works";
	echo "<br />";
}
else
{
	echo "r43 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t43 works";
	echo "<br />";
}
else
{
	echo "t43 sorry";
	echo "<br />";
}
$sql = "INSERT INTO mdl_context(contextlevel,instanceid,depth)
	VALUE(70,$module_4_four,4)";
$rrr = execute_sql($sql,false);
if($rrr)
{
	echo "r44 works";
	echo "<br />";
}
else
{
	echo "r44 sorry";
	echo "<br />";
}

$sql = "SELECT LAST_INSERT_ID()";
$sss = get_field_sql($sql);

$sql = "UPDATE mdl_context
	SET path = '/1/$catecontext/$ggg/$sss'
	WHERE id = $sss";
$ttt = execute_sql($sql,false);

if($ttt)
{
	echo "t44 works";
	echo "<br />";
}
else
{
	echo "t44 sorry";
	echo "<br />";
}



//   add user information 


$passwordorigin = (string)$newcourse;

$pass = hash_internal_user_password($passwordorigin);

$sql = "INSERT INTO mdl_user(id,confirmed,mnethostid,username,password,firstname,lastname,email,idnumber,institution,city,country,lang)
	VALUE($newcourse,1,1,'$newcourse','$pass','$newcourse','$wholename','111111@gmail.com',$newcourse,'$class','xian','CN','zh_cn_utf8')";
$create = execute_sql($sql,false);
if($create)
	echo "create user successfully ";
else echo "create user error";
echo "<br />";

$sql = "INSERT INTO mdl_user_preferences(userid,name,value)
	VALUE($newcourse,'auth_forcepasswordchange',1)";
$userprefer_one = execute_sql($sql,false);
if($userprefer_one)
{
	echo "userpreferone works";
}
else echo "userpreferone fails";
echo "<br />";

$sql = "INSERT INTO mdl_user_preferences(userid,name,value)
	VALUE($newcourse,'email_bounce_count',1)";
$userprefer_two = execute_sql($sql,false);
if($userprefer_two)
{
	echo "userprefertwo works";
}
else echo "userprefertwo fails";
echo "<br />";


$sql = "INSERT INTO mdl_user_preferences(userid,name,value)
	VALUE($newcourse,'email_send_count',1)";
$userprefer_three = execute_sql($sql,false);
if($userprefer_three)
{
	echo "userpreferthree works";
}
else echo "userpreferthree fails";
echo "<br />";

}










/// clear up transfer station 

$sql ="TRUNCATE TABLE mdl_assignment_oj_order_tests";
$delete = execute_sql($sql,false);
if($delete)
	echo "mdl_assignment_oj_order_tests success";
else echo "delete mdl_assignment_oj_order_tests fail";
echo "<br />";

$sql ="TRUNCATE TABLE mdl_assignment_order";
$delete = execute_sql($sql,false);
if($delete)
	echo "mdl_assignment_order success";
else echo "delete mdl_assignment_order fail";
echo "<br />";

if(file_exists("upload/test.xls")) 
{  
    unlink("upload/test.xls");
	echo "student menu deleted successfully";
}
else
{
	echo "Error!,can't delete file because it no longer exists";
}
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
