<?php
include('includes/config.php');

$sql = "SELECT  id, classroom FROM classroom";
$result = $bd->query($sql);

$classrooms = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classrooms[] = $row['classroom']; // Remplacez `classroom_name` par le nom exact de votre colonne
    }
}

header('Content-Type: application/json');

if (!empty($classrooms)) {
    echo json_encode($classrooms); // Renvoie un tableau JSON des noms des salles
} else {
    echo json_encode([]); // Renvoie un tableau vide si aucune salle n'a été trouvée
}

?>