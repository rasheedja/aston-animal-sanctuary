<?php
function print_animal_info($animal, $db, $has_button) {
    $animal_id = $animal['animal_id'];
    $_GET['animal_id'] = $animal_id;
    $query = "SELECT * FROM animals WHERE id = $animal_id";
    $result = $db->query($query);
    while ($animal_info = $result -> fetch()) {
        parse_animal_info($animal_info, $has_button);
    }
}

function parse_animal_info($animal_info, $has_button) {
    $name = $animal_info['name'];
    $dob = $animal_info['date_of_birth'];
    $age = date_diff(date_create($dob), date_create('today'))->y;
    $description = $animal_info['description'];
    $photo = $animal_info['photo'];
    $animal_id = $animal_info['id'];
    echo "<br />";
    echo "<img class='animal-picture' src=$photo />";
    echo "<ul class='animal-info'>";
    echo "<li>$name</li>";
    echo "<li>$age</li>";
    echo "<li>$dob</li>";
    echo "<li>$description</li>";
    if ($has_button == true) {
        //echo "<li><a class='button' href='make_request.php' get='1'>Adopt</a></li>";
        echo "<li><form action='make_request.php' method='get'><button class='button' name='adopt' type='submit' value='$animal_id'>Adopt</button></form></li>";
    }
    echo "</ul>";
}
?>