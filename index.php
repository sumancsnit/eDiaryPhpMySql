<?php

    session_start();

    $error = "";

    if (array_key_exists("logout", $_GET)) {
        
        unset($_SESSION);
        setcookie("id", "", time() - 60*60);
        $_COOKIE["id"] = "";
        
    } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
        
        header("Location: loggedinpage.php");
    }
        

    if (array_key_exists("submit", $_POST)){
        
        include("connection.php");
        
//        $query = "ALTER TABLE users AUTO_INCREMENT = 1";
//        mysqli_query($link, $query);
        
        if (!$_POST['email']){
            
            $error .= "Email id is required<br>";
        }
        
        if (!$_POST['password']){
            
            $error .= " A Password is required!<br>";
        }
        
        if ($error != ""){
            
            $error = "<p>There were error(s) in your form:</p>".$error;
        } else {
            
            if ($_POST['signUp'] == '1') {
            
                $query = "SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

                $result = mysqli_query($link, $query);

                if (mysqli_num_rows($result) > 0) {

                    $error = "That email address is taken.";
                } else {

                    $query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";

                    if (!mysqli_query($link, $query)) {

                        $error = "<p>Could not sign up - Try again.</p>";

                    } else {
                        $query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";

                        mysqli_query($link, $query);

                        $SESSION['id'] = mysqli_insert_id($link);

                        if ($_POST['stayLoggedIn'] == '1') {

                            setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);
                        } 

                        header("Location: loggedinpage.php");
                    }

                }

            } else {
                $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
                
                $result = mysqli_query($link, $query);
                
                $row = mysqli_fetch_array($result);
                
                if (isset($row)) {
                    
                    $hashedPassword = md5(md5($row['id']).$_POST['password']);
                    
                    if ($hashedPassword == $row['password']) {
                        
                        $_SESSION['id'] = $row['id'];
                        
                        if (isset($_POST['stayLoggedIn']) AND $_POST['stayLoggedIn'] == '1') {
                            
                            setcookie("id", $row['id'], time() + 60*60*5);
                        }
                           header("Location: loggedinpage.php");
                    } else {
                        $error = "That email/password combination could not be found.";
                    } 
                    
                } else {
                    $error = "That email/password combination could not be found.";
                }
            }
        
        }
        
    }
    

?>

<?php include("header.php"); ?>




    <div id="homePageContainer" class="container">

        <h1>SUMAN IT eDiary</h1>
        <p><strong>Write Tomorrow's Lessons Today!</strong></p>

        <div id="error">
            <?php
                if ($error != ""){
                    
                echo    '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                }
            
            
            ?>
        </div>

        <form id="signInForm" method="post">
            
            <p>Interested? Sign up now.</p>

            <fieldset class="form-group">
                <input class="form-control" name="email" type="email" placeholder="Your email">
            </fieldset>
            <fieldset class="form-group">
                <input class="form-control" name="password" type="password" placeholder="Password">
            </fieldset>
            <div class="checkbox">
                <label>
                    <input name="stayLoggedIn" type="checkbox" value=1>
                    Stay logged in
                </label>
            </div>
            <fieldset class="form-group">
                <input name="signUp" type="hidden" value="1">
                <input class="btn btn-success" name="submit" type="submit" value="Sign Up!">
            </fieldset>
            
            <p><a class="toggleForm">Log in</a></p>

        </form>

        <form id="logInForm" method="post">
            
            <p>Log in using your username and password.</p>

            <fieldset class="form-group">
                <input class="form-control" name="email" type="email" placeholder="Your email">
            </fieldset>
            <fieldset class="form-group">
                <input class="form-control" name="password" type="password" placeholder="Password">
            </fieldset>
            <div class="checkbox">
                <label>
                    <input name="stayLoggedIn" type="checkbox" value=1>
                    Stay logged in
                </label>
            </div>
            <fieldset class="form-group">
                <input name="signUp" type="hidden" value="0">
                <input class="btn btn-success" name="submit" type="submit" value=" Log In! ">
            </fieldset>
            <p><a class="toggleForm">Sign Up</a></p>

        </form>

    </div>

<?php include("footer.php"); ?>