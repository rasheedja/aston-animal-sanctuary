<!DOCTYPE html>
<html>
    <head>
        <title>Aston Animal Sanctuary: Register</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
        <link rel="stylesheet" type="text/css" href="stylesheet.css" />
    </head>
	<body>
        <div id="main">
            <h1>Aston Animal Sanctuary</h1>
            <div id="inner-center">
                <h2>Create an account</h2>
                <form action = "register.php" method = "post">
                    <p>User Name: <input type="text" name="username" size="15" maxlength="32" /></p>
                    <p>Password: <input type="password" name="password" size="15" maxlength="64" /></p>
                    <p><input class = "button" type="submit" name="submit" value="Submit" /></p>
                    <input type="hidden" name="submitted" value="TRUE" />
                </form>
                <?php
                session_start();
                if (isset($_POST['submitted'])) {
                    // connect to the database.
                    $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    // clean any malicious input
                    $safe_username = $db->quote($_POST['username']);
                    // hash and salt all passwords for security
                    $hashed_and_salted_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    // check if username exists
                    $query = "SELECT username FROM users WHERE username = $safe_username";
                    $result = $db->query($query);
                    if ($result->rowCount() > 0) {
                        echo "<p class='error'>Username already exists</p>";
                    } else {
                        // insert values into the database
                        $query = "INSERT INTO users VALUES (default, $safe_username, 0, '$hashed_and_salted_password')";
                        $db->exec($query);
                        echo "<p>Welcome, $safe_username</p>";
                    }
                }
                ?>
                <p><a href = "index.php">Return to home page</a></p>
            </div>
        </div>
	</body>
</html>