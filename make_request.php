<?php
session_start();
if (isset($_SESSION['name'])) {
    // store the username in a variable
    $username = $_SESSION['name'];
    // connect to the database
    $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // store user information in a variable
    try {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $db->query($query);
        $user = $result->fetch();
        $user_id = $user['id'];
        // store the animal name and id in a variable
        $animal_id = $_GET['adopt'];
        $query = "SELECT name FROM animals WHERE id='$animal_id'";
        $result = $db->query($query);
        $animal_name = $result->fetch();
        $animal_name = $animal_name['name'];
        // create the adoption request if animal is available
        $adoption_request_made = false;
        $query = "SELECT available FROM animals WHERE id='$animal_id'";
        $result = $db->query($query);
        $availability = $result->fetch();
        $availability = $availability['available'];
        if ($availability != 0) {
            $query = "INSERT INTO adoption_request (adoption_id, user_id, animal_id, approved) VALUES (NULL, '$user_id', '$animal_id', '0')";
            $db->exec($query);
            // make the animal unavailable
            $query = "UPDATE animals SET available = '0' WHERE id = '$animal_id' ";
            $db->exec($query);
            $adoption_request_made = true;
        }
    } catch (PDOException $e) {
        echo "<p class='error'> Database Error Occurred: . $e->getMessage()</p>";
    }

    include_once('display_animal.php');
} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Aston Animal Sanctuary: Make Adoption Request</title>
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
    <link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body>
<div id="main">
    <div id = "nav-bar">
        <ul id="nav">
            <li>
                <a href="home.php">Home</a>
            </li>
            <li>
                <a href="animals.php">Available Animals</a>
            </li>
            <li>
                <a href="requests.php">Adoption Requests</a>
            </li>
            <li id="search">
                <form action="search.php" method="get">
                    <input type="search" name="search_bar" id="search_bar">
                </form>
            </li>
            <li id="greeting">
                <a href="#">Hello, <?php echo $username; ?></a>
            </li>
            <li id="logout">
                <a href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
    <div id="inner-center">
        <div class="animals">
            <?php
            if ($adoption_request_made) {
                echo "<h3>You have made an adoption request for the $animal_name</h3>";
            } else {
                echo "<h3>$animal_name is unavailable</h3>";
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
