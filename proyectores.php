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
<title>Proyectores</title>
<link rel="stylesheet" type="text/css" href="jquery/ui/css/jquery-ui.structure.min.css?v=1657486303">
<link rel="stylesheet" type="text/css" href="jquery/ui/css/sunny/jquery-ui.theme.min.css?v=1657486303">
<link rel="stylesheet" type="text/css" href="jquery/datatables/datatables.min.css?v=1657486303">
<link rel="stylesheet" type="text/css" href="js/flatpickr/css/flatpickr.min.css?v=1657486303">
<link rel="stylesheet" type="text/css" href="jquery/select2/dist/css/select2.min.css?v=1657486303">
<link rel="stylesheet" type="text/css" href="css/mrbs.css.php?v=1657486303">
<link rel="stylesheet" type="text/css" href="css/mrbs-print.css.php?v=1657486303" media="print">
</head>
<body class="index logged_in" data-view="day" data-view_all="1" data-area="2" data-room="14" data-page="index" data-page-date="2023-03-27" data-is-admin="true" data-is-book-admin="true" data-lang-prefs="[&quot;es-419&quot;,&quot;es&quot;,&quot;en&quot;,&quot;en&quot;]" data-username="fpuricelli">
<?php

// Database connection details
$host = "localhost";
$username = "puricell_mrbs";
$password = '$mrbs%151';
$dbname = "puricell_mrbs";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);



// Check for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Construct the query to retrieve entries for the next five days
$start_time = strtotime('today 8:00:00');
$end_time = strtotime('+4 days', $start_time);
//echo $end_time;
$query = "SELECT name,start_time,proyector FROM mrbs_entry WHERE (start_time BETWEEN $start_time AND $end_time) and proyector IS NOT null";
//echo $query;
// Execute the query and store the results
$result = $conn->query($query);

// Check for errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Create an array to store the results
$entries = array();

// Loop through the results and add each entry's name, date, and proyector to the array
while ($row = $result->fetch_assoc()) {
    $entry = array(
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
        'name' => $entry['name'],
        'proyector' => $entry['proyector']
    );
}

// Output the HTML table
echo "<table>";
// Output the header row with the day labels
echo "<thead><tr><th>Time</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th><th>Sunday</th></tr></thead>";
// Output the table body with the time labels and data
echo "<tbody>";
for ($i = 8; $i < 22; $i++) {
    echo "<tr>";
    // Output the time label for this row
    echo "<td>" . $i . ":00" . "</td>";
    // Output the data for each day column in this row
    for ($j = 0; $j < 7; $j++) {
        $day = date('l', strtotime('Monday +'.$j.' days'));
        $i=str_pad($i, 2, "0", STR_PAD_LEFT);
        //echo $i;
        if (isset($table_data["$i"][$day])) {
            echo "<td><ul>";
            foreach ($table_data["$i"][$day] as $entry) {
                echo "<li>".htmlentities($entry['name']."({$entry['proyector']})</li>";
            }
            echo "</ul></td>";
        } else {
            echo "<td>&nbsp;</td>";
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