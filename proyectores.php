<!DOCTYPE html>
<!--[if lte IE 9]>
<html lang="es" class="unsupported_browser">
<![endif]-->
<!--[if (!IE)|(gt IE 9)]><!-->
<html lang="es">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf_token" content="3384942bff2b3919f1545193a58214710ea34743c144f6f99fd8189ab20a8a1e">
<meta name="robots" content="noindex, nofollow, noarchive">
<meta http-equiv="refresh" content="30">
<title>Proyectores</title>
<style>
		body {
			background-color: #333333;
			color: white;
		}

		table {
			border-collapse: collapse;
			margin: auto;
		}

		td {
			border: 1px solid white;
			width: 160px;
			height: 80px;
			padding: 0;
			text-align: center;
			font-size: 16px;
			font-weight: bold;
			line-height: 20px;
		}

		.chip {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			border-radius: 50px;
			padding: 8px;
			margin: 4px;
			font-size: 12px;
			font-weight: bold;
			text-transform: uppercase;
		}

		.red {
			background-color: #FF4136;
		}

		.green {
			background-color: #2ECC40;
            color: black;
		}
	</style>
</head>
<body>
<?php

// Database connection details
$host = "localhost";
$username = "puricell_mrbs";
$password = '$mrbs%151';
$dbname = "puricell_mrbs";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");



// Check for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Construct the query to retrieve entries for the next five days
$start_time = strtotime('today 8:00:00');
$end_time = strtotime('+4 days', $start_time);
//echo $end_time;
$query = "SELECT id,name,start_time,proyector FROM mrbs_entry WHERE (start_time BETWEEN $start_time AND $end_time) and proyector IS NOT null";
//echo $query;
// Execute the query and store the results
$result = $conn->query($query);

// Check for errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

$max_proyectores=8;
// Create an array to store the results
$entries = array();

// Loop through the results and add each entry's name, date, and proyector to the array
while ($row = $result->fetch_assoc()) {
    $entry = array(
        "id" => $row['id'],
        "name" => $row['name'],
        "date" => date("Y-m-d", $row['start_time']),
        "time" => date("H", $row['start_time']),
        "day" => date("l",$row['start_time']),
        "proyector" => $row['proyector']
    );
    //echo $entry['name']." ".$entry['date']." ".$entry['time']." ".$entry['day']." ".$entry['proyector'];
    $entries[] = $entry;
}
//echo count($entries);
// Create an empty array for the table data
$table_data = array();

// Loop through each entry and add it to the table data array
foreach ($entries as $entry) {
    //echo $entry['time']." ",$entry['day'];
    $table_data[$entry['time']][$entry['day']][] = array(
        'id' => $entry['id'],
        'name' => $entry['name'],
        'proyector' => $entry['proyector']
    );
}

// Output the HTML table
echo "<table>";
// Output the header row with the day labels
echo "<thead><tr><th>Hora</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th><th>Domingo</th></tr></thead>";
// Output the table body with the time labels and data
echo "<tbody>";
for ($i = 8; $i < 22; $i+=4) {
    echo "<tr>";
    // Output the time label for this row
    echo "<td>" . $i . ":00" . "</td>";
    // Output the data for each day column in this row
    for ($j = 0; $j < 7; $j++) {
        $day = date('l', strtotime('Monday +'.$j.' days'));
        $i=str_pad($i, 2, "0", STR_PAD_LEFT);
        //echo $i;
        if (isset($table_data["$i"][$day])) {
            echo "<td>";
            $proyector_in_use=array();
            foreach ($table_data["$i"][$day] as $entry) {
                $proyector_in_use[substr($entry['proyector'], -1)]=$entry['id'];
            }
            // completa en verde los proyectores que están libres
            for ($p = 1; $p<=$max_proyectores; $p++){
                if (!isset($proyector_in_use["$p"])){
                    echo "<div class='chip green'>P".$p."</div>";
                } else {
                    echo "<div class='chip red'><a href='/aulas-iti/view_entry.php?id=".$proyector_in_use["$p"]."' target='_blank'>P".$p."</a></div>";
                }
            }
            echo "</td>";
        } else {
            echo "<td><div>&nbsp;</div></td>";
        }


        //$data = isset($table_data["$i"][$day]) ? $table_data["$i"][$day] : array('name' => '', 'proyector' => '');
        //echo "<td><strong>Name:</strong> " . $data['name'] . "<br><strong>Proyector:</strong> " . $data['proyector'] . "</td>";
    }
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";

// Close the database connection
$conn->close();

?>
</body>
</html>