
<?php

// Include Simple HTML DOM Parser
include('simple_html_dom.php');

// Website URL
$url = "index.html";

// Load the HTML page
$html = file_get_html($url);

if ($html !== false) {

    echo "<h2>Web Scraping Results</h2>";

    // Get website title
    echo "<h3>Website Title:</h3>";
    echo $html->find('title', 0)->plaintext;

    // Get all headings
    echo "<h3>All Headings:</h3>";

    foreach ($html->find('h1') as $heading) {
        echo $heading->plaintext . "<br>";
    }

    // Get all links
    echo "<h3>Links:</h3>";

    foreach ($html->find('a') as $link) {
        echo $link->plaintext . " - ";
        echo $link->href . "<br>";
    }

} else {
    echo "Unable to load the website.";
}

?>
