<form action="" enctype="multipart/form-data" method="post"  
      name="uploadfile">上传文件：<input type="file" name="upfile" /><br>  
    <input type="submit" value="上传" /></form>  
<?php  
echo "<meta charset=\"utf-8\">";  


if(is_uploaded_file($_FILES['upfile']['tmp_name'])){  
    $upfile=$_FILES["upfile"];  
    $name=$upfile["name"];
    $type=$upfile["type"];
    $size=$upfile["size"];  
    $tmp_name=$upfile["tmp_name"];  
	//if($type =)    
	$okType = true;

  
    if($okType){   
        $error=$upfile["error"];
        echo "================<br/>";  
        echo "上传文件名称是：".$name."<br/>";  
        echo "上传文件类型是：".$type."<br/>";  
        echo "上传文件大小是：".$size."<br/>";  
        echo "上传后系统返回的值是：".$error."<br/>";  
        echo "上传文件的临时存放路径是：".$tmp_name."<br/>";  
  
        echo "开始移动上传文件<br/>";    
	if(file_exists("upload/" . $name)) 
        {  
            echo $name . " already exists. ";  
        }
	else 
	{ 
	 
	        move_uploaded_file($tmp_name,"upload/".$name);  
	        $destination="upload/".$name;  
	        echo "================<br/>";  
	        echo "上传信息：<br/>";  
	        if($error==0){  
	            echo "文件上传成功啦！";   
		}
		elseif ($error==1){  
	            	echo "超过了文件大小，在php.ini文件中设置";  
	        }
		elseif ($error==2){  
	            	echo "超过了文件的大小MAX_FILE_SIZE选项指定的值";  
	        }
		elseif ($error==3){  
	 	           echo "文件只有部分被上传";  
        	}
		elseif ($error==4){  
        		    echo "没有文件被上传";  
        	}
		else
		{  
	            echo "上传文件大小为0";  
	        }

	}  
    }else{  
        echo "请上传xls等格式的图片！";  
    }  
	echo "<br />";
$wwwroot = $CFG->wwwroot;
$wwwroot .="/moodle/course";
header("refresh:5;url=$wwwroot");
print('正在加载，请稍等...<br>五秒后自动跳转。');
}  

?> 
  
