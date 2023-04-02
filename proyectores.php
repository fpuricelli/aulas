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
$start_time = time();
$end_time = strtotime('+4 days', $start_time);
//echo $end_time;
$query = "SELECT name,start_time,proyector FROM mrbs_entry WHERE start_time BETWEEN $start_time AND $end_time";
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

// Create an empty array for the table data
$table_data = array();

// Loop through each entry and add it to the table data array
foreach ($entries as $entry) {
    $table_data[$entry['time']][$entry['day']] = array(
        'name' => $entry['name'],
        'date' => $entry['date'],
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
        $day = date('l', strtotime('Sunday +'.$j.' days'));
        echo "del día".$day;
        $data = isset($table_data["$i"][$day]) ? $table_data["$i"][$day] : array('name' => '', 'proyector' => '');
        echo "<td><strong>Name:</strong> " . $data['name'] . "<br><strong>Proyector:</strong> " . $data['proyector'] ." ". $data['date'] . "</td>";
    }
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";

// Close the database connection
$conn->close();

?>
