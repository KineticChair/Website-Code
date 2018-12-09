<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div id="feedback">
            <?php
            $nameErr = $emailErr = $messageErr = "";
            $name = $email = $message = "";
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                require ("/mysqli_connect.php");
                if (empty($_POST["name"])) {
                    $nameErr = "*required";
                } else if(!preg_match("/^[a-zA-Z ]*$/", $name)) {
                        $nameErr = "Only letters and white spaces allowed";
                } else {
                    $name = test_input($_POST["name"]);
                    $name = mysqli_real_escape_string($dbc, trim($_POST['name']));
                }
                if (empty($_POST["email"])) {
                    $emailErr = "*required";
                } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emailErr = "Invalid email format";
                } else {
                    $email = test_input($_POST["email"]);
                    $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
                }
                if (empty($_POST["message"])) {
                    $commentErr = "*required";
                } else {
                    $comment = test_input($_POST["message"]);
                    $comment = mysqli_real_escape_string($dbc, trim($_POST['comment']));
                }
            }
            function test_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }
            ?>
            
            <hr><h3>Contact Us</h3>
            <p>Let us know of your questions / feedback</p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                Name: <input type="text" name="name" value="<?php echo $name;?>" placeholder="Name">
                <span class="error"><?php echo $nameErr;?></span>
                <br><br>
                Email: <input type="text" name="email" value="<?php echo $email;?>" placeholder="Email">
                <span class="error"><?php echo $emailErr;?></span>
                <br><br>
                Message: <textarea name="message" rows="5" cols="40"><?php echo $message;?></textarea>
                <span class="error"><?php echo $messageErr;?></span>
                <br><br>
                <input type="submit" name="submit" value="Submit">
                <br><br>
            </form>
            
        </div>
        <div id="copyright">
            <p>&copy Zero Energy Garden</p>
        </div>
    </body>
</html>