<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['benutzername'])) {
    header("Location: index.php");
    exit();
}

// Verbindung zur Datenbank herstellen
$servername = "45.83.245.57";
$username = "vsc";
$password = "***********";
$dbname = "vertexde_";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

if (isset($_POST['create'])) {
    // Erstellungslogik
    $newUsername = $_POST['new_username'];
    $newVerbrechen = $_POST['new_verbrechen'];
    $newBeweis = $_POST['new_beweis'];
    $newServer = $_POST['new_server'];
    $newLink = $_POST['new_link'];
    $newId = $_POST['new_id'];

    $createSql = "INSERT INTO discord (benutzername, Verbrechen, Beweis, Server, LINK, ID) VALUES ('$newUsername', '$newVerbrechen', '$newBeweis', '$newServer', '$newLink', '$newId')";
    $conn->query($createSql);

    // Weiterleitung oder Meldung nach dem Erstellen
    echo "<script>alert('Benutzer wurde erstellt.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

// Verarbeitung der Suchanfrage
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT benutzername, ID, Verbrechen, Beweis, Server, LINK FROM discord WHERE benutzername LIKE '%$search%'";
} else {
    $sql = "SELECT benutzername, ID, Verbrechen, Beweis, Server, LINK FROM discord";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="icon" rel="image/x-icon" href="https://cdn-icons-png.flaticon.com/128/10337/10337558.png">
    <link rel="stylesheet" type="text/css" href="userpanel.css">
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
    <h2>ETERNAL5</h2>
    <h2 class="centered">VertexData</h2>
    <form action="" method="GET">
        <input type="text" name="search" id="search" placeholder="Benutzer suchen...">
        <button type="submit">Suchen</button>
        <a href="userpanel.php" class="cancel">Abbrechen</a>
		<br>
		<a href="dashboard.php">Zurück zum Dashboard</a>
        <div class="breadcrumb">
            <a href="/">Startseite</a>
            <span> ▹ </span>
            <span>Adminpanel</span>
            <span> ▹ </span>
            <span>userpanel</span>
        </div>
    </form>

    <table border="1">
        <tr>
            <th>Benutzername</th>
            <th>ID</th>
            <th>Verbrechen</th>
            <th>Beweis</th>
            <th>Server</th>
            <th>LINK</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["benutzername"] . "</td><td>" . $row["ID"] . "</td><td>" . $row["Verbrechen"] . "</td><td>" .$row["Beweis"] . "</td><td>" . $row["Server"] . "</td><td>" . $row["LINK"] . "</td>";
                // Aktionen (Bearbeiten/Löschen) wurden entfernt
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Keine Ergebnisse gefunden.</td></tr>";
        }
        ?>
    </table>

    <h2>Neuen Benutzer erstellen</h2>
    <form action="" method="POST">
        <label for="new_username">Benutzername:</label>
        <input type="text" name="new_username" required placeholder="Max">

        <label for="new_id">ID:</label>
        <input type="text" name="new_id" required placeholder="xxxxxxxxxxxxxxxxxx">

        <label for="new_verbrechen">Verbrechen:</label>
        <input type="text" name="new_verbrechen" required placeholder="Scam / Betrug / Diebstahl">

        <label for="new_beweis">Beweis:</label>
        <input type="text" name="new_beweis" required placeholder="http/s: Link">

        <label for="new_server">Server:</label>
        <input type="text" name="new_server" required placeholder="Max Community">

        <label for="new_link">LINK:</label>
        <input type="text" name="new_link" required placeholder="discord.gg/xxxxxxx">

        <input type="checkbox" name="button" required> Ich habe den Nutzer Informiert!
        <br>
        <br>
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
