<?php

require_once('../config.php');
require_once($CFG->dirroot.'/enrol/enrol.class.php');
require_once('lib.php');
require_once($CFG->libdir.'/blocklib.php');
require_once('edit_form.php');

require_once('../lang/zh_cn_utf8/feedback.php');


if (!isloggedin()) {
  $wwwroot = $CFG->wwwroot;
  if (!empty($CFG->loginhttps)) {
     $wwwroot = str_replace('http:','https:', $wwwroot);
  }
  // do not use require_login here because we are usually comming from it
  redirect($wwwroot.'/login/index.php');
}
function recordset_to_menu_charles($rs)
{
    global $CFG;
    global $score;
    $menu = array();
    if ($rs && !rs_EOF($rs))
    {
        $keys = array_keys($rs->fields);
        $key0=$keys[0];
        $key1=$keys[1];
        while (!$rs->EOF)
        {
		$user_id = (int)$rs->fields[$key0];
		$assignment_id = (int)$rs->fields[$key1];
            $sql = "SELECT section
                    FROM mdl_course_modules
                    WHERE instance = $assignment_id";
            $level = (int)get_field_sql($sql);

            $sql ="SELECT grade
                  FROM mdl_assignment_submissions
                  WHERE userid = $user_id AND assignment = $assignment_id";
            $grade =(int) get_field_sql($sql);
            if(array_key_exists($level,$score[$rs->fields[$key0]]))
            {
                if($score[$rs->fields[$key0]][$level]<$grade)
                {
                    $score[$rs->fields[$key0]][$level]=$grade;
                  }
            }
            else
            {
              $score[$rs->fields[$key0]][$level]=$grade;
            }
            $rs->MoveNext();
        }
        /// Really DIRTY HACK for Oracle, but it's the only way to make it work
        /// until we got all those NOT NULL DEFAULT '' out from Moodle
        if ($CFG->dbfamily == 'oracle') {
            array_walk($menu, 'onespace2empty');
        }
        /// End of DIRTY HACK
        return true;
    }
    else
    {
        return false;
    }
}


function get_records_sql_menu_charles($sql, $limitfrom='', $limitnum='') {
    $rs = get_recordset_sql($sql, $limitfrom, $limitnum);
    return recordset_to_menu_charles($rs);
}


require_login();
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
if (has_capability('moodle/course:create', $systemcontext)){

  	$course_cat = (int)$_GET["course_cat"];
	$score = array();
/*
  	$sql = "SELECT mas.userid,mas.assignment FROM (mdl_assignment AS ma INNER JOIN mdl_course AS mc
		ON ma.course = mc.id)INNER JOIN mdl_assignment_submissions AS mas
		ON ma.id = mas.assignment
		WHERE mc.category = $course_cat";
*/
	$sql ="SELECT userid, assignment
		FROM mdl_assignment_submissions ";

	$rs = get_records_sql_menu_charles($sql);

	array_walk($rs,"myfunction");
	$keys = array_keys($score);
	sort($keys);
	//echo "<br />";
	$num = count($score);


	//header("Content-type:application/vnd.ms-excel");
	header("Content-type:application/vnd.ms-excel;charset=UTF-8");
	header("Content-Disposition:filename=test.xls");
	$html = "<tr><td>id</td><td>class</td><td>name</td><td>level_one</td><td>level_two</td><td>level_three</td><td>level_four</td><td>totalscore</td><td colspan = '8'></td><td>";

	for($x = 0;$x < $num;$x++)
	{
		$html .="<tr>";
		$html .="<td>$keys[$x]</td>";
		$sql = "SELECT institution
			FROM mdl_user
			WHERE id = $key[$x]";
		$userclass = get_field_sql($sql);
		$html .="<td>$userclass</td>";
		$sql = "SELECT firstname
			FROM mdl_user
			WHERE id = $keys[$x]";
		$string['first']=get_field_sql($sql);

		$sql = "SELECT lastname
			FROM mdl_user
			WHERE id = $keys[$x]";
		$string['last']=get_field_sql($sql);
		$html .= "<td>".$string['last'].$string['first']."</td>";
	 	 //get the four levels of test
		//echo gettype($keys[$x]);
			$sql = "SELECT level_one
			FROM mdl_course_level
			WHERE course = $keys[$x]";
		$level_one = (int)get_field_sql($sql);
		//echo gettype($level_one);
		//echo $level_one;

		$sql = "SELECT level_two
		FROM mdl_course_level
			WHERE course = $keys[$x]";
			$level_two = (int)get_field_sql($sql);

		$sql = "SELECT level_three
			FROM mdl_course_level
			WHERE course = $keys[$x]";
		$level_three =(int) get_field_sql($sql);

		$sql = "SELECT level_four
			FROM mdl_course_level
			WHERE course = $keys[$x]";
		$level_four = (int)get_field_sql($sql);


		$total = array_sum($score[$keys[$x]]);

		if(array_key_exists($level_one,$score[$keys[$x]]))
		{
			$temp = $score[$keys[$x]][$level_one];
			$html.="<td>".$temp."</td>";
		}
		else
		{
			$html.="<td>0</td>";
		}

		if(array_key_exists($level_two,$score[$keys[$x]]))
		{
			$temp = $score[$keys[$x]][$level_two];
			$html.="<td>".$temp."</td>";
		}
		else
		{
			$html.="<td>0</td>";
		}
		if(array_key_exists($level_three,$score[$keys[$x]]))
		{
			$temp = $score[$keys[$x]][$level_three];
				$html.="<td>".$temp."</td>";
		}
		else
		{
			$html.="<td>0</td>";
		}
		if(array_key_exists($level_four,$score[$keys[$x]]))
		{
			$temp = $score[$keys[$x]][$level_four];
			$html.="<td>".$temp."</td>";
		}
		else
		{
			$html.="<td>0</td>";
		}
		$html.="<td>".$total."</td>";
	  $html.="<br>";

	}

	echo "<table border=1>$html</table>";

}
else
{
	$wwwroot = $CFG->wwwroot;
	//$wwwroot .="/course/"; 
	header("Location: $wwwroot");
}

?>
