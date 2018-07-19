<?php

if($_SERVER['REQUEST_METHOD']=='POST')
{
include('admin/dbcon.php');
$school_id=$_POST['school_id'];

$common_tables=array("term","subjects");
$unique_tables_sch_id=array("school","class","login","progress","review_answers");
$unique_tables_class_name=array("class_subject","review_questions","lessonmodificationlog","lessons");


if($conn)
{
    
   
    function filterString($st)
    {
        $re_find=array("int(11) NOT NULL AUTO_INCREMENT","timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP","COMMENT '0=inactive, 1= active'","COMMENT '1=govt,2=corp,3=govt_aided'","COMMENT '0=not covered, 1= covered'");
        $re_reply=array("integer AUTO_INCREMENT NOT NULL","NOT NULL DEFAULT CURRENT_TIMESTAMP","","","");
        $pos=strpos($st,"ENGINE=InnoDB");
        $str_input=substr($st,0,$pos);
        
        $s1=preg_replace('/int\([0-9]*\)/','integer',str_ireplace($re_find,$re_reply,$str_input)).';';
        return preg_replace('/AUTO_INCREMENT/','',$s1);
    }

    function export_tables($tables,$query_sel=null)
    {        
        
        
        $conn=$GLOBALS['conn'];
       
        $return='';
        foreach($tables as $table)
        {
            $result='';

            if($query_sel)
                $querry="SELECT * FROM ".$table." WHERE ".$query_sel;
            else
                $querry="SELECT * FROM ".$table;
            
            $result=mysqli_query($conn,$querry);
            
            $num_fields=mysqli_num_fields($result);

            $return.='DROP TABLE IF EXISTS '.$table.";\n\n";
            $row2=mysqli_fetch_row(mysqli_query($conn,'SHOW CREATE TABLE '.$table));
            $create_table=filterString($row2[1]);
            $return.=$create_table."\n\n";

            for($i=0;$i<$num_fields;$i++)
                {
                while($row=mysqli_fetch_row($result))
                    {
                    $return.='INSERT INTO '.$table.' VALUES(';
                    for($j=0;$j<$num_fields;$j++)
                        {
                            $row[$j]=addslashes($row[$j]);
                            if(isset($row[$j]))
                                $return.='"'.$row[$j].'"';
                            else
                                $return.='""';
                            if($j<$num_fields-1)
                            $return.=',' ;
                        }
                        $return.=");\n";
                    }
                }

                $return.="\n\n\n";
               
        }


        return $return;

    }
     
}

$return_common=export_tables($common_tables);
$return_uniq_sch=export_tables($unique_tables_sch_id,"school_id='$school_id'");
$return_unique_class_name=export_tables($unique_tables_class_name,"class_name IN (SELECT class_name FROM class WHERE school_id='$school_id')");

header("Content-type:text/sql");
header("Content-Disposition:attachment;filename=Export.sql");


print $return_common.$return_uniq_sch.$return_unique_class_name;

}
?>