<?php
session_start();
if (isset($_SESSION['name'])) {
    // store the username in a variable
    $username = $_SESSION['name'];
    // connect to the database
    $db = new PDO("mysql:dbname=rasheeja_db;host=localhost", "root", "***REMOVED***");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // select all the available animals from the database
    $query = "SELECT * FROM animals WHERE available=1";
    $result = $db->query($query);
    include_once('display_animal.php');
    
} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Aston Animal Sanctuary: Available Animals</title>
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
                    <h3>Available Animals</h3>
                    <?php
                    // display information on all available animals
                    while ($animal_info = $result -> fetch()) {
                        parse_animal_info($animal_info, "Adopt", null, "make_request.php", "get");
                    }
                    ?>
                </div>
			</div>
		</div>
	</body>
</html>