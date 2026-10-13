
<!DOCTYPE html>
<html>
<head>
    <title>GET and POST Methods</title>
</head>
<body>

<h2>Demonstration of GET Method</h2>

<form method="get" action="">
    Enter Name:
    <input type="text" name="getname" required>
    <input type="submit" value="Submit using GET">
</form>

<?php
if (isset($_GET['getname'])) {
    echo "<p>GET Method: Welcome "
        . htmlspecialchars($_GET['getname'])
        . "</p>";
}
?>

<hr>

<h2>Demonstration of POST Method</h2>

<form method="post" action="">
    Enter Name:
    <input type="text" name="postname" required>
    <input type="submit" value="Submit using POST">
</form>

<?php
if (isset($_POST['postname'])) {
    echo "<p>POST Method: Welcome "
        . htmlspecialchars($_POST['postname'])
        . "</p>";
}
?>

</body>
</html>
