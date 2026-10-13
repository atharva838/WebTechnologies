
<?php
$conn = new mysqli("localhost", "root", "", "student_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Insert student
if (isset($_POST['insert'])) {
    $name = trim($_POST['name']);
    $pen = trim($_POST['pen']);
    $gender = $_POST['gender'] ?? "";
    $branch = $_POST['branch'] ?? "";
    $semester = (int)($_POST['semester'] ?? 0);
    $contact = $_POST['contact'];
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);

    if (!preg_match('/^\d{10}$/', $contact)) {
        $message = "Contact number must contain exactly 10 digits.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO students
            (name, pen, gender, branch, semester, contact, address, email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssssisss",
            $name, $pen, $gender, $branch,
            $semester, $contact, $address, $email
        );

        $message = $stmt->execute()
            ? "Student inserted successfully."
            : "Insert failed. PEN may already exist.";
        $stmt->close();
    }
}

// Update student using PEN
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $pen = trim($_POST['pen']);
    $gender = $_POST['gender'] ?? "";
    $branch = $_POST['branch'] ?? "";
    $semester = (int)($_POST['semester'] ?? 0);
    $contact = $_POST['contact'];
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);

    if (!preg_match('/^\d{10}$/', $contact)) {
        $message = "Contact number must contain exactly 10 digits.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE students SET
            name=?, gender=?, branch=?, semester=?,
            contact=?, address=?, email=?
            WHERE pen=?"
        );
        $stmt->bind_param(
            "sssissss",
            $name, $gender, $branch, $semester,
            $contact, $address, $email, $pen
        );

        $stmt->execute();
        $message = $stmt->affected_rows > 0
            ? "Student updated successfully."
            : "No changes made or PEN not found.";
        $stmt->close();
    }
}

// Delete student using PEN
if (isset($_POST['delete'])) {
    $pen = trim($_POST['pen']);

    $stmt = $conn->prepare("DELETE FROM students WHERE pen=?");
    $stmt->bind_param("s", $pen);
    $stmt->execute();

    $message = $stmt->affected_rows > 0
        ? "Student deleted successfully."
        : "Student PEN not found.";
    $stmt->close();
}

// Retrieve student using PEN
$selected = null;

if (isset($_POST['retrieve'])) {
    $pen = trim($_POST['pen']);

    $stmt = $conn->prepare("SELECT * FROM students WHERE pen=?");
    $stmt->bind_param("s", $pen);
    $stmt->execute();
    $selected = $stmt->get_result()->fetch_assoc();

    $message = $selected
        ? "Student record found."
        : "Student PEN not found.";

    $stmt->close();
}

// Retrieve all records for display
$students = $conn->query("SELECT * FROM students ORDER BY id DESC");

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Information System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 25px;
        }
        h2 { color: #23395d; }
        form, .records {
            background: white;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        input, select, textarea {
            padding: 8px;
            margin: 6px 0 12px;
            width: 95%;
            box-sizing: border-box;
        }
        input[type=radio] { width: auto; }
        button {
            padding: 9px 15px;
            margin: 4px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 8px;
            text-align: left;
        }
        th { background: #dce6f1; }
        .message { color: #174f2a; font-weight: bold; }
    </style>
</head>
<body>

<h2>Student Information System</h2>

<p class="message"><?= e($message) ?></p>

<form method="post" action="">
    <label>Name:</label>
    <input type="text" name="name"
        value="<?= e($selected['name'] ?? '') ?>" required>

    <label>PEN:</label>
    <input type="text" name="pen"
        value="<?= e($selected['pen'] ?? '') ?>" required>

    <label>Gender:</label><br>
    <input type="radio" name="gender" value="Male"
        <?= (($selected['gender'] ?? '') === 'Male') ? 'checked' : '' ?>>
    Male
    <input type="radio" name="gender" value="Female"
        <?= (($selected['gender'] ?? '') === 'Female') ? 'checked' : '' ?>>
    Female
    <input type="radio" name="gender" value="Other"
        <?= (($selected['gender'] ?? '') === 'Other') ? 'checked' : '' ?>>
    Other
    <br><br>

    <label>Branch:</label>
    <select name="branch" required>
        <option value="">Select Branch</option>
        <?php
        $branches = ["IT", "Computer", "Mechanical", "Civil", "Electrical"];
        foreach ($branches as $branch) {
            $sel = (($selected['branch'] ?? '') === $branch)
                ? 'selected' : '';
            echo "<option value='" . e($branch) . "' $sel>"
                . e($branch) . "</option>";
        }
        ?>
    </select>

    <label>Semester:</label>
    <select name="semester" required>
        <option value="">Select Semester</option>
        <?php for ($i = 1; $i <= 8; $i++): ?>
            <option value="<?= $i ?>"
                <?= ((int)($selected['semester'] ?? 0) === $i)
                    ? 'selected' : '' ?>>
                <?= $i ?>
            </option>
        <?php endfor; ?>
    </select>

    <label>Contact Number (10 digits):</label>
    <input type="text" name="contact"
        inputmode="numeric" maxlength="10"
        pattern="[0-9]{10}"
        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
        value="<?= e($selected['contact'] ?? '') ?>" required>

    <label>Address:</label>
    <textarea name="address" required><?= e($selected['address'] ?? '') ?></textarea>

    <label>Email (new column):</label>
    <input type="email" name="email"
        value="<?= e($selected['email'] ?? '') ?>">

    <button type="submit" name="insert">Insert</button>
    <button type="submit" name="update">Update</button>
    <button type="submit" name="delete"
        onclick="return confirm('Delete this student?')">Delete</button>
    <button type="submit" name="retrieve">Retrieve by PEN</button>
    <button type="button"
        onclick="window.location.href=window.location.pathname">
        Clear
    </button>
</form>

<div class="records">
    <h2>All Student Records</h2>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>PEN</th><th>Gender</th>
            <th>Branch</th><th>Semester</th><th>Contact</th>
            <th>Address</th><th>Email</th>
        </tr>
        <?php while ($row = $students->fetch_assoc()): ?>
        <tr>
            <td><?= e($row['id']) ?></td>
            <td><?= e($row['name']) ?></td>
            <td><?= e($row['pen']) ?></td>
            <td><?= e($row['gender']) ?></td>
            <td><?= e($row['branch']) ?></td>
            <td><?= e($row['semester']) ?></td>
            <td><?= e($row['contact']) ?></td>
            <td><?= e($row['address']) ?></td>
            <td><?= e($row['email']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
<?php $conn->close(); ?>