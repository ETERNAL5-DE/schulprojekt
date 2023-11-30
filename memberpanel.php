<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['benutzername'])) {
    // Benutzer ist nicht angemeldet, auf die Login-Seite weiterleiten
    header("Location: index.php");
    exit();
}
$servername = "45.83.245.57";
$username = "vsc_users";
$password = "***********";
$dbname = "userdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Überprüfen, ob der Benutzer die Berechtigung zum Löschen hat
    $deleteSql = "DELETE FROM users WHERE ID = $userId";
    $conn->query($deleteSql);

    echo "<script>alert('Benutzer wurde gelöscht.'); window.location.href = './memberpanel.php';</script>";
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'toggle' && isset($_GET['id'])) {
    $userId = $_GET['id'];

    $toggleSql = "UPDATE users SET Blocked = CASE WHEN Blocked = 'yes' THEN 'no' ELSE 'yes' END WHERE ID = $userId";
    $conn->query($toggleSql);
    echo "<script>alert('Blockierungsstatus wurde geändert.'); window.location.href = './memberpanel.php';</script>";
    exit();
}

if (isset($_POST['create'])) {
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    $newEmail = $_POST['new_email'];
    $newId = $_POST['new_id'];
    $newServer = $_POST['new_server'];
    $newServerId = $_POST['new_server_id'];
    $newLink = $_POST['new_link'];

    $createSql = "INSERT INTO users (username, password, email, ID, Server, Server_ID, LINK) VALUES ('$newUsername', '$newPassword', '$newEmail', '$newId', '$newServer', '$newServerId', '$newLink')";
    $conn->query($createSql);

    echo "<script>alert('Benutzer wurde erstellt.'); window.location.href = './memberpanel.phpv';</script>";
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : "";
$sql = "SELECT username, password, email, ID, Server, Server_ID, LINK, Blocked FROM users WHERE username LIKE '%$search%' AND Stellung != 'Admin'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="icon" rel="image/x-icon" href="https://cdn-icons-png.flaticon.com/128/10337/10337558.png">
    <link rel="stylesheet" type="text/css" href="memberpanel.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VERTEX</title>
    <h3 id="countdown" style="color: #CFDBDA; text-decoration: overline white;">5:00</h3>
</head>
<script>
    var minutes = 15.0;
    var seconds = minutes * 60;

    function updateCountdown() {
        var minutesDisplay = Math.floor(seconds / 60);
        var secondsDisplay = seconds % 60;

        secondsDisplay = secondsDisplay < 10 ? '0' + secondsDisplay : secondsDisplay;

        document.getElementById('countdown').innerHTML = minutesDisplay + ':' + secondsDisplay;

        if (seconds === 0) {
            alert("Sie wurden abgemeldet!");
            window.location.href = 'index.php';
        } else {
            seconds--;
            setTimeout(updateCountdown, 1000);
        }
    }

    updateCountdown();
</script>
<body>
    <h2>ETERNAL5</h2>
    <h2 class="centered">VertexData</h2>
    <form action="" method="GET">
        <input type="text" name="search" id="search" placeholder="Benutzer suchen...">
        <button type="submit">Suchen</button>
        <a href="memberpanel.php" class="cancel">Abbrechen</a>
        <br>
		<a href="dashboard.php">Zurück zum Dashboard</a>
        <div class="breadcrumb">
            <a href="/">Startseite</a>
            <span> ▹ </span>
            <span>Adminpanel</span>
            <span> ▹ </span>
            <span>Member Panel</span>
        </div>
    </form>

    <table border="1">
        <tr>
            <th>Username</th>
            <th>Password</th>
            <th>eMail</th>
            <th>ID</th>
            <th>Server</th>
            <th>Server ID</th>
            <th>LINK</th>
            <th>Blocked</th>
            <th>Aktionen</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["username"] . "</td><td>" . "*************" . "</td><td>" . $row["email"] . "</td><td>" .$row["ID"] . "</td><td>" . $row["Server"] . "</td><td>" . $row["Server_ID"] . "</td><td>" . $row["LINK"] ."</td><td>" . $row["Blocked"] . "</td>";
                echo "<td><a href='?action=delete&id=" . $row['ID'] . "'>Löschen</a>";
                echo " | <a href='?action=toggle&id=" . $row['ID'] . "'>".($row['Blocked'] == 'yes' ? 'Entblockieren' : 'Blockieren')."</a></td></tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Keine Ergebnisse gefunden.</td></tr>";
        }
        ?>
    </table>

    <h2>Neuen Benutzer erstellen</h2>
    <form action="" method="POST">
        <label for="new_username">Benutzername:</label>
        <input type="text" name="new_username" required placeholder="Max">
        
        <label for="new_password">Passwort:</label>
        <input type="password" name="new_password" required placeholder="********">
        
        <label for="new_email">Email:</label>
        <input type="email" name="new_email" required placeholder="max@example.com">
        
        <label for="new_id">ID:</label>
        <input type="text" name="new_id" required placeholder="xxxxxxxxxxxxxxxxxx">
        
        <label for="new_server">Server:</label>
        <input type="text" name="new_server" required placeholder="Max Community">
        
        <label for="new_server_id">Server-ID:</label>
        <input type="text" name="new_server_id" required placeholder="123456789">
        
        <label for="new_link">LINK:</label>
        <input type="text" name="new_link" required placeholder="discord.gg/xxxxxxx">
        
        <button type="submit" name="create">Erstellen</button>
    </form>

    <button id="scrollToBottom">Zum Seitenende</button>
    <button id="reload" onclick="reloadPage()">Neu Laden</button>

    <script>
        function reloadPage() {
            location.reload();
        }
    </script>

    <script>
        document.getElementById("scrollToBottom").addEventListener("click", function() {
            window.scrollTo(0, document.body.scrollHeight);
        });
    </script>
</body>
</html>
