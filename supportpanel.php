<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['benutzername'])) {
    // Benutzer ist nicht angemeldet, auf die Login-Seite weiterleiten
    header("Location: index.php");
    exit();
}
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
    $sql = "SELECT email, id, grund FROM support WHERE id LIKE '%$search%'";
} else {
    // SQL-Abfrage für alle Benutzerdaten, falls keine Suchanfrage vorliegt
    $sql = "SELECT email, id, grund FROM support";
}

$result = $conn->query($sql);

// Funktion zum Ablehnen eines Benutzers und Entfernen aus der Tabelle
function ablehnenBenutzer($conn, $id) {
    $loeschenSQL = "DELETE FROM support WHERE id = '$id'";
    $conn->query($loeschenSQL);
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="icon" href="/images/favicon.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="supportpanel.css">
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
        <a href="supportpanel.php" class="cancel">Abbrechen</a>
		<br>
		<a href="dashboard.php">Zurück zum Dashboard</a>
        <div class="breadcrumb">
            <a href="/">Startseite</a>
            <span> ▹ </span>
            <a>Adminpanel</a>
            <span> ▹ </span>
            <a>Support Panel</a>
        </div>
    </form>

    <!-- Tabelle für Benutzerdaten -->
    <table border="1">
        <tr>
            <th>Email</th>
            <th>ID</th>
            <th>Grund</th>
            <th>Aktionen</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Daten aus der Datenbank abrufen und in die Tabelle einfügen
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["email"] . "</td><td>" . $row["id"] . "</td><td>".$row["grund"] ."</td><td>";
                echo "<button onclick=\"ablehnenBenutzer('" . $row["id"] . "')\">Fertig</button></td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Keine Ergebnisse gefunden.</td></tr>";
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
        function ablehnenBenutzer(id) {
            window.location.href = "./actions/finish.php?id=" + id;
        }
    </script>

    <script>
        document.getElementById("scrollToBottom").addEventListener("click", function() {
            window.scrollTo(0, document.body.scrollHeight);
        });
    </script>
</body>
</html>
