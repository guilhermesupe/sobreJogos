<?php


  




    require_once("lib/raelgc/view/Template.php");
    use raelgc\view\Template;
    
    $tpl = new Template("main.html");

       $dbhost = 'localhost:8888';
        $conn = mysql_connect($dbhost, 'root', 'root');
        if (! $conn) {
            die('Could not connect: ' . mysql_error());
        }
        else {
            //echo "connected";
        }

        $password = $_POST['user_password'];
        
        $email = $_POST['user_email'];
        $name = $_POST['user_name'];
        $_SESSION['user_name'] = $name;
        $_SESSION['user_password'] = $password;
        //echo $password;
        //echo $email;
        //echo $name;
        mysql_select_db('MeteorNotes');




        $sql = "SELECT * FROM users WHERE user_name ='" .$name. "' AND user_password ='" .$password. "' ";


          $retval = mysql_query( $sql, $conn );
            if(! $retval )
                            {
                              echo $sql;
                die('Could not enter data: ' . mysql_error());
                }

                //echo mysql_result($retval,0);
                $user_id = mysql_result($retval,0);
                echo $user_id;

              
                $sql = "SELECT * FROM fields WHERE user_id ='" .$user_id ."'";
        

            $retval = mysql_query( $sql, $conn );
            if(! $retval )
                            {
                              echo $sql;
                die('Could not enter data: ' . mysql_error());
                }
                $count =0;




                while ($row = mysql_fetch_array($retval, MYSQL_NUM)) {
                      $tpl->CAMPO = $row[2];
                       $tpl->block("BLOCK_FIELDS");
                       
                      $count++;
                    }



              if($count == 0){
                $tpl->block("BLOCK_VAZIO");

              }
              else{
                $tpl->block("BLOCK_ADDFIELD");
              }


     



                mysql_close($conn);

                


              $_SESSION['user_id'] = $user_id;




                //echo $user_id;
                //$tpl->USER_ID = $user_id;
                
                $tpl->USER_ID = $_SESSION['user_id'];
                $tpl->user_name = $_SESSION['user_name'];
                $tpl->show();

                
               //echo file_get_contents("main.html");

              



?>