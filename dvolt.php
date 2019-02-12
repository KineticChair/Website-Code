<?php
    require ('mysqli_connect.php');
    $sql="SELECT time, voltage FROM dashboard WHERE date=CAST(CURRENT_TIMESTAMP as DATE) ORDER BY no";   //newest at the top
    
    $query = mysqli_query($dbconnect, $sql);
    
    if ( ! $query ) {
        echo mysqli_error();
        die;
    }
    
    $data = array();
    
    for ($x = 0; $x < mysqli_num_rows($query); $x++) {
        $data[] = mysqli_fetch_assoc($query);
    }
    
    echo json_encode($data);     
     
    mysqli_close($dbconnect);
?>