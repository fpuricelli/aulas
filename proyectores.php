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

// Construct the query to retrieve entries for the next five days
$start_time = time();
$end_time = strtotime('+4 days', $start_time);
echo $end_time;
$query = "SELECT name,start_time,proyector FROM mrbs_entry WHERE start_time BETWEEN $start_time AND $end_time";
echo $query;
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
        "day" => date("l",$start_time),
        "proyector" => $row['proyector']
    );
    echo $entry[0]." ".$entry[1]." ".$entry[2]." ".$entry[3]." ".$entry[4];
    $entries[] = $entry;
}



// Close the database connection
$conn->close();

?>
