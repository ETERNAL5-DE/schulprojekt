<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['benutzername'])) {
    // Benutzer ist nicht angemeldet, auf die Login-Seite weiterleiten
    header("Location: index.php");
    exit();
}
// Annahme: Du hast eine MySQL-Datenbank mit einer Tabelle namens "normal" und den Spalten "username", "password", "email", "ID", "Server", "Server_ID" und "LINK".
// Du hast auch eine andere Tabelle namens "abgelehnte_benutzer" mit den gleichen Spalten.

// Verbindung zur Datenbank herstellen
$servername = "45.83.245.57";
$username = "vsc_users";
$password = "***********";
$dbname = "userdb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen der Verbindung
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Verarbeitung der Suchanfrage
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "Stellung, username, password, email, ID, Server, Server_ID, LINK FROM normal WHERE username LIKE '%$search%'";
} else {
    // SQL-Abfrage für alle Benutzerdaten, falls keine Suchanfrage vorliegt
    $sql = "SELECT username, password, email, ID, Server, Server_ID, LINK FROM normal";
}

$result = $conn->query($sql);

// Funktion zum Bestätigen eines Benutzers und Verschieben in die andere Tabelle
function bestaetigenBenutzer($conn, $username, $password, $email, $ID, $Server, $Server_ID, $LINK) {
    $bestaetigenSQL = "INSERT INTO users (Stellung, username, password, email, ID, Server, Server_ID, LINK) VALUES ('Standart', '$username', '$password', '$email', '$ID', '$Server', '$Server_ID', '$LINK')";
    $conn->query($bestaetigenSQL);
}

// Funktion zum Ablehnen eines Benutzers und Entfernen aus der Tabelle
function ablehnenBenutzer($conn, $username) {
    $loeschenSQL = "DELETE FROM normal WHERE username = '$username'";
    $conn->query($ablehnenSQL);
    $conn->query($loeschenSQL);
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="icon" href="/images/favicon.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="registrations.css">
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

        // Füge eine führende Null hinzu, wenn die Sekunden weniger als 10 sind.
        secondsDisplay = secondsDisplay < 10 ? '0' + secondsDisplay : secondsDisplay;

        document.getElementById('countdown').innerHTML = minutesDisplay + ':' + secondsDisplay;

        if (seconds === 0) {
            // Wenn der Countdown abgelaufen ist, leite auf die gewünschte Seite weiter.
            alert("Sie wurden abgemeldet!");
			<?php
			session_destroy();
			?>
            window.location.href = 'index.php';

        } else {
            // Reduziere die verbleibende Zeit und aktualisiere den Countdown.
            seconds--;
            setTimeout(updateCountdown, 1000);
        }
    }

    // Starte den Countdown, wenn die Seite geladen ist.
    updateCountdown();
</script>
<body>
    <h2> ETERNAL5 </h2>
    <h2 class="centered">VertexData</h2>

    <!-- Suchformular -->
    <form action="" method="GET">
        <input type="text" name="search" id="search" placeholder="Benutzer suchen...">
        <button type="submit">Suchen</button>
        <a href="registrations.php" class="cancel">Abbrechen</a>
		<br>
		<a href="dashboard.php">Zurück zum Dashboard</a>
        <div class="breadcrumb">
            <a href="/">Startseite</a>
            <span> ▹ </span>
            <a>users</a>
            <span> ▹ </span>
            <a>Registration Panel</a>
        </div>
    </form>

    <!-- Tabelle für Benutzerdaten -->
    <table border="1">
        <tr>
            <th>Benutzername</th>
            <th>Passwort</th>
            <th>Email</th>
            <th>ID</th>
            <th>Server</th>
            <th>Server ID</th>
            <th>Link</th>
            <th>Aktionen</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Daten aus der Datenbank abrufen und in die Tabelle einfügen
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["username"] . "</td><td>" . $row["password"] . "</td><td>".$row["email"] ."</td><td>". $row["ID"] . "</td><td>" .$row["Server"] . "</td><td>" . $row["Server_ID"] . "</td><td>" . $row["LINK"] . "</td>";
                echo "<td><button onclick=\"bestaetigenBenutzer('" . $row["username"] . "','" . $row["password"] . "','" . $row["email"] . "','" . $row["ID"] . "','" . $row["Server"] . "','" . $row["Server_ID"] . "','" . $row["LINK"] . "')\">Freigeben</button>";
                echo "<button onclick=\"ablehnenBenutzer('" . $row["username"] . "')\">Ablehnen</button></td></tr>";
            }
        } else {
            echo "<tr><td colspan='8'>Keine Ergebnisse gefunden.</td></tr>";
        }
        ?>
    </table>

    <?php
    // Verbindung zur Datenbank schließen
    $conn->close();
    ?>
    
    <button id="scrollToBottom">Scroll to Bottom</button>
    <button id="reload" onclick="reloadPage()">Reload</button>

    <script>
        function reloadPage() {
            location.reload();
        }

        function bestaetigenBenutzer(username, password, email, ID, Server, Server_ID, LINK) {
            window.location.href = "./actions/accept.php?username=" + username + "&password=" + password + "&email=" + email + "&ID=" + ID + "&Server=" + Server + "&Server_ID=" + Server_ID + "&LINK=" + LINK;
        }

        function ablehnenBenutzer(username) {
            window.location.href = "./actions/decline.php?username=" + username + "&ID="+ID;
        }
    </script>

    <script>
        document.getElementById("scrollToBottom").addEventListener("click", function() {
            window.scrollTo(0, document.body.scrollHeight);
        });
    </script>
</body>
</html>
