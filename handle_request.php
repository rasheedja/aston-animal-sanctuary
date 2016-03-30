<?php
session_start();
if (isset($_SESSION['name'])) {
    // store the username in a variable
    $username = $_SESSION['name'];
    // connect to the database
    $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // store user information in a variable
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $db->query($query);
    $user = $result->fetch();
    $user_id = $user['id'];
    // redirect non staff users to standard home page
    if ($user['staff'] == 0) {
        header("Location: home.php");
    }
} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Aston Animal Sanctuary: Request Handled</title>
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
    <link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body>
<div id="main">
    <div id = "nav-bar">
        <ul id="nav">
            <li>
                <a href="staff_home.php">Home</a>
            </li>
            <li>
                <a href="staff_animals.php">Animals</a>
            </li>
            <li>
                <a href="staff_requests.php">Adoption Requests</a>
            </li>
            <li id="search">
                <form action="search.php" method="get">
                    <input type="text" name="search_bar" id="search_bar">
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
        <?php
        if (isset($_GET['approve'])) {
            // set the adoption request to approved
            $adoption_id = $_GET['approve'];
            $query = "UPDATE adoption_request SET approved=1 WHERE adoption_id=$adoption_id";
            $db->query($query);
            // switch the owner for the pet
            $query = "SELECT * FROM adoption_request WHERE adoption_id = $adoption_id";
            $result = $db->query($query);
            $adoption_info = $result->fetch();
            $user_id = $adoption_info['user_id'];
            $animal_id = $adoption_info['animal_id'];
            $query = "UPDATE owns SET user_id=$user_id WHERE animal_id=$animal_id";
            $db->query($query);
            echo "<h3>Adoption Request Approved</h3>";
        } else if (isset($_GET['deny'])) {
            // set the adoption request to denied
            $adoption_id = $_GET['deny'];
            $query = "UPDATE adoption_request SET approved=2 WHERE adoption_id=$adoption_id";
            $db->query($query);
            // switch the pet back to available
            $query = "SELECT animal_id FROM adoption_request WHERE adoption_id = $adoption_id";
            $result = $db->query($query);
            $animal_id = $result->fetch();
            $animal_id = $animal_id['animal_id'];
            $query = "UPDATE animals SET available=1 WHERE id=$animal_id";
            $db->query($query);
            echo "<h3>Adoption Request Denied</h3>";
        }
        ?>
    </div>
</div>
</body>
</html>
