<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "u468158713_leads";
$password = "Honda@leads123";
$database = "u468158713_honda_leads";

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // echo "Connected successfully";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEADS Dashboard</title>
    <style>
        body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }

            .container {
                max-width: 92%;
                margin: 39px auto;
                padding: 9px background-color: #fff;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            h1,
            h2 {
                text-align: center;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                padding: 10px;
                border-bottom: 1px solid #ddd;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }

            tr:hover {
                background-color: #f9f9f9;
            }

           .filter {
    max-width: 74%;
    margin: 0 auto;
    padding: 6px;
    background-color: #f2f2f2;
    border-radius: 5px;
    /*box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);*/
    margin-left: 27rem;
}

.form-group {
    display: inline-block;
    margin-right: 10px; /* Adjust spacing between inputs */
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="date"],
input[type="submit"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    box-sizing: border-box;
}


        input[type="submit"] {
            background-color: #460202;
            color: #fff;
            cursor: pointer;
            font-size: 116%;
            width: 10%;
        }

        input[type="submit"]:hover {
            background-color: #021020;
        }
            .button {
    width: 30%;
    padding: 10px;
    font-size: 131%;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    box-sizing: border-box;
    /* justify-items: center; */
    /* justify-content: center; */
    margin-left:30rem;
    background-color: #770101;
    color: #fff;
    margin-top: 42px;
}
    </style>
</head>
<body>
     <div class="container">
    <h1>Dashboard</h1>
    
    <form action="leads.php" method="GET" class="filter">
        <div class="form-group">
            <label for="startfilter">From:</label>
            <input type="date" id="startfilter" name="startfilter">
        </div>
        <div class="form-group">
            <label for="endfilter">To:</label>
            <input type="date" id="endfilter" name="endfilter">
        </div>
        <input type="submit" value="Filter">
    </form>
    
    <?php
    if (!empty($_GET['startfilter']) && !empty($_GET['endfilter'])) {
        $start_date = date('Y-m-d', strtotime($_GET['startfilter']));
        echo "Start Date: $start_date <br><br>";
        $end_date = date('Y-m-d', strtotime($_GET['endfilter']));
        echo "End Date: $end_date <br><br>";
    }
    ?>

    <table id="headerTable">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Mobile</td>
                <td>Email</td>
                <td>Model</td>
                <td>Dealer</td>
                <td>Date</td>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($_GET['startfilter']) && !empty($_GET['endfilter'])) {
                $sql = "SELECT * FROM `Leads_info` WHERE Date BETWEEN '$start_date' AND '$end_date'";
            } else {
                $sql = "SELECT * FROM `Leads_info`";
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                    <tr>
                        <td><?= $row['ID'] ?></td>
                        <td><?= $row['NAME'] ?></td>
                        <td><?= $row['MOBILE'] ?></td>
                        <td><?= $row['EMAIL'] ?></td>
                        <td><?= $row['MODEL'] ?></td>
                        <td><?= $row['DEALER'] ?></td>
                        <td><?= $row['DATE'] ?></td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='7'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <button id="btnExport" class="button" onclick="fnExcelReport();">Download</button>
 </div>
    <script>
        function fnExcelReport() {
            // Excel export function
            var tab_text = "<table border='none'><tr bgcolor='#87AFC6'>";
            var textRange;
            var j = 0;
            var tab = document.getElementById('headerTable'); // id of table

            for (j = 0; j < tab.rows.length; j++) {
                tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) { // If Internet Explorer
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, "LEADS.xls");
            } else { //other browser not tested on IE 11
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
            }
            return (sa);
        }
    </script>
</body>
</html>

<?php
} else {
    // If not logged in, display the login form

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $input_username = $_POST['username'];
        $input_password = $_POST['password'];

        // Check if username and password match
        if ($input_username === 'hondasouth' && $input_password === 'honda@123') {
            // If credentials are correct, set session variables and redirect to dashboard
            $_SESSION['loggedin'] = true;
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            // If credentials are incorrect, display error message
            echo "<p style='color: red;'>Invalid username or password</p>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LEADS Dashboard</title>
    <style>
        /* Styles for login form */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            max-width: 300px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- Login form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
         <h1>Dashboard Login</h1>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Login">
    </form>
</body>
</html>

<?php
}
?>
