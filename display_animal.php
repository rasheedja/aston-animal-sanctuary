<?php
function print_animal_info($animal, $db, $button_text_1, $button_text_2, $action, $method, $value, $user_id) {
    $animal_id = $animal['animal_id'];
    $_GET['animal_id'] = $animal_id;
    $query = "SELECT * FROM animals WHERE id = $animal_id";
    $result = $db->query($query);
    while ($animal_info = $result -> fetch()) {
        parse_animal_info($animal_info, $button_text_1, $button_text_2, $action, $method, $value, $user_id);
    }
}

function parse_animal_info($animal_info, $button_text_1, $button_text_2, $action, $method, $value, $user_id) {
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
    echo "<li>Adopter's ID: $user_id</li>";
    print_buttons($animal_id, $button_text_1, $button_text_2, $action, $method, $value);
    echo "</ul>";
}

function print_buttons($animal_id, $button_text_1, $button_text_2, $action, $method, $value) {
    if ($value == null) {
        $value = $animal_id;
    }
    if ($button_text_1 != null) {
        $name = strtolower($button_text_1);
        echo "<li><form action=$action method=$method><button class='button' name=$name type='submit' value=$value>$button_text_1</button></form></li>";
    }
    if ($button_text_2 != null) {
        $name = strtolower($button_text_2);
        echo "<li><form action=$action method=$method><button class='button' name=$name type='submit' value=$value>$button_text_2</button></form></li>";
    }
}
?>