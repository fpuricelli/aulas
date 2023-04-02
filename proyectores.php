<?php
// Database connection details
$host = "localhost";
$username = "puricell_mrbs";
$password = "$mrbs%151";
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
$query = "SELECT name,start_time,proyector FROM mrbs_entry WHERE start_time BETWEEN $start_time AND $end_time";

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
        "proyector" => $row['proyector']
    );
    $entries[] = $entry;
}

// Encode the results as JSON and output them
echo json_encode($entries);

// Close the database connection
$conn->close();

?>
