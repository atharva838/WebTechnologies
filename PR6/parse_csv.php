
<?php
$filename = "students.csv";

if (($file = fopen($filename, "r")) !== false) {

    echo "<h2>Student Details from CSV File</h2>";

    echo "<table border='1' cellpadding='8'>";
    
    // Read each row from the CSV file
    while (($data = fgetcsv($file)) !== false) {

        echo "<tr>";

        // Display each field
        foreach ($data as $field) {
            echo "<td>" . htmlspecialchars($field) . "</td>";
        }

        echo "</tr>";
    }

    echo "</table>";

    fclose($file);

} else {
    echo "Unable to open CSV file.";
}
?>
