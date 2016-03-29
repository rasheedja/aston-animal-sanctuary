<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Log In</title>
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
        <link rel="stylesheet" type="text/css" href="stylesheet.css" />
    </head>
    <body>
        <div id="main">
            <h1>Aston Animal Sanctuary</h1>
            <div id = "inner-center">
                <h2>Log In</h2>
                <form action="index.php" method="post">
                    <p>User Name: <input type="text" name="username" size="15" maxlength="32" /></p>
                    <p>Password: <input type="password" name="password" size="15" maxlength="64" /></p>
                    <p><input class = "button" type="submit" name="submit" value="Submit" /></p>
                    <input type="hidden" name="submitted" value="TRUE" />
                </form>
                <p>Don't have an account? <a href = "register.php">Register</a></p>
            </div>
            <?php
            session_start();
            if (isset($_POST['submitted'])) {
                //retrieve user input from the HTML form
                $username = $_POST['username'];
                $password = $_POST['password'];
                //connect to the database
                $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //clean any malicious input
                $clean_username = $db->quote($username);
                //retrieve users information from the database
                $query = "SELECT * FROM users WHERE username = $clean_username";
                $result = $db->query($query);
                $user_info = $result->fetch();

                if (!empty($user_info)) {
                    if (password_verify($password, $user_info['password'])) {
                        $_SESSION['name'] = $user_info['username'];
                        header("Location: home.php");
                        exit();
                    } else {
                        echo "<p class='error'>Error logging in, incorrect password</p>";
                    }
                } else {
                    echo "<p class='error'>Error logging in, Username not found </p>";
                }
            }
            ?>
        </div>
    </body>
</html>