<?php
$db_name="Export.db";
include('admin/dbcon.php');
class LiteDB extends SQLite3
{
    private $conn=null;
    private $db_name="Export.db";
    function __construct()
        {
            $this->conn=new SQLite3($this->db_name);
   
        }
   function go($x)
        {
            $this->conn->exec($x);
        }

   function __destruct()
        {
            $this->conn=null;
            unlink($this->db_name);
        }

   //Filter SQL DATA Types
   function filterString($st)
    {
        $re_find=array("int(11) NOT NULL AUTO_INCREMENT","timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP","COMMENT '0=inactive, 1= active'","COMMENT '1=govt,2=corp,3=govt_aided'","COMMENT '0=not covered, 1= covered'");
        $re_reply=array("integer AUTO_INCREMENT NOT NULL","NOT NULL DEFAULT CURRENT_TIMESTAMP","","","");
        $pos=strpos($st,"ENGINE=InnoDB");
        $str_input=substr($st,0,$pos);
        
        $s1=preg_replace('/int\([0-9]*\)/','integer',str_ireplace($re_find,$re_reply,$str_input)).';';
        return preg_replace('/AUTO_INCREMENT/','',$s1);
    }

// Parse Data From Data Base
function export_tables($tables,$query_sel=null)
    {               
        
        $conn=$GLOBALS['conn'];
       
        
        foreach($tables as $table)
        {
            $return='';
            $result='';

            if($query_sel)
                $querry="SELECT * FROM ".$table." WHERE ".$query_sel;
            else
                $querry="SELECT * FROM ".$table;
            
            $result=mysqli_query($conn,$querry);
            
            $num_fields=mysqli_num_fields($result);

            $return.='DROP TABLE IF EXISTS '.$table.";\n\n";
            $row2=mysqli_fetch_row(mysqli_query($conn,'SHOW CREATE TABLE '.$table));
            $create_table=$this->filterString($row2[1]);
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

               // $return.="\n\n\n"; ///each iteration goes here 
               $this->go($return);
               
        }


        //return $return;

    }



}
///Class End Here
if(isset($_POST['school_id']))
{
    
   $school_id=$_POST['school_id'];
    $common_tables=array("term","subjects");
    $unique_tables_sch_id=array("school","class","login","progress","review_answers");
    $unique_tables_class_name=array("class_subject","review_questions","lessonmodificationlog","lessons");
    $obj=new LiteDB();
    $obj->export_tables($common_tables);
    $obj->export_tables($unique_tables_sch_id,"school_id='$school_id'");
    $obj->export_tables($unique_tables_class_name,"class_name IN (SELECT class_name FROM class WHERE school_id='$school_id')");
    
    //set header for Download

    header("Content-type:application/octet-stream");
    header("Content-Description:File Transfer");
    header("Content-Disposition:attachment;filename='$db_name'");
    header("Expires:0");
    header("Content-Length:".filesize($db_name));
    readfile($db_name);
    unlink($db_name);
}
else
    echo "No Response";







?>