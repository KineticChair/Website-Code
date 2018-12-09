<!DOCTYPE html>
<html>
    <head>
        <title>View Comments</title>
        <link rel="stylesheet" href="includes/style.css" />
    </head>
    
    <body>
        <?php
            include ('includes/header.php');
            require ('mysqli_connect.php');
            session_start();
            $tbl_name="feedback";   // Table Name
            $sql="SELECT * FROM $tbl_name ORDER BY feedback_id DESC";   //newest at the top
            $result=mysqli_query($dbc, $sql);
        ?>
        
        <!-- TABLE -->
            <div class="wrapperA">
                <table>
                            <thead>
				<tr>
                                    <th colspan="2">Name</th>
                                    <th colspan="2">Email</th>
                                    <th colspan="4">Feedback</th>
                                    <th colspan="1.5">Time</th>
                                    <th colspan="1">Edit</th>
				</tr>
                            </thead>
                            <tbody>
                                <?php
                                    while($rows=mysqli_fetch_array($result)){   // Start looping table row
                                ?>
                                        <tr>
                                            <td colspan="2"><?php echo $rows['first_name']; ?></td>
                                            <td colspan="2"><?php echo $rows['email']; ?></td>
                                            <td colspan="4"><?php echo $rows['message']; ?></td>
                                            <td colspan="1.5"><?php echo $rows['time_submitted']; ?></td>
                                            <td colspan="1"></td>
                                        </tr>
                                        
                                <?php
                                    }
                                    mysqli_close($dbc); //close connection
                                ?>
                            </tbody>
                        </table>
                    <br>
                </div>
            </section>

            <div id="copyright">
                    <p>&copy Zero Energy Garden</p>
            </div>
    </body>
</html>