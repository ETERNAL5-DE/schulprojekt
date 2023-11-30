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
$username = "vsc";
$password = "***********";
$dbname = "vertexde_";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Bearbeiten, Löschen, Erstellen und Suchanfrage
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'edit' && isset($_GET['id'])) {
        $userId = $_GET['id'];
        $editSql = "SELECT * FROM discord WHERE ID = $userId";
        $editResult = $conn->query($editSql);
        $userData = $editResult->fetch_assoc();
    } elseif ($action == 'delete' && isset($_GET['id'])) {
        $userId = $_GET['id'];
        $deleteSql = "DELETE FROM discord WHERE ID = $userId";
        $conn->query($deleteSql);
        echo "<script>alert('Benutzer wurde gelöscht.'); window.location.href = 'dashboard.php';</script>";
    }
} elseif (isset($_POST['update'])) {
    // Aktualisierungslogik
    $userId = $_POST['user_id'];
    $newUsername = $_POST['new_username'];
    $newVerbrechen = $_POST['new_verbrechen'];
    $newBeweis = $_POST['new_beweis'];
    $newServer = $_POST['new_server'];
    $newLink = $_POST['new_link'];
    $newId = $_POST['new_id'];

    $updateSql = "UPDATE discord SET benutzername='$newUsername', Verbrechen='$newVerbrechen', Beweis='$newBeweis', Server='$newServer', LINK='$newLink', ID='$newId' WHERE ID=$userId";
    $conn->query($updateSql);
    echo "<script>alert('Benutzerdaten wurden aktualisiert.'); window.location.href = 'dashboard.php';</script>";
    exit();
} elseif (isset($_POST['create'])) {
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
    <link rel="stylesheet" type="text/css" href="dashboard.css">
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
			//session_destroy();
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
    <h2>ETERNAL5</h2>
    <h2 class="centered">VertexData</h2>
    <form action="" method="GET">
        <input type="text" name="search" id="search" placeholder="Benutzer suchen...">
        <button type="submit">Suchen</button>
        <a href="dashboard.php" class="cancel">Abbrechen</a>
		<br>
		<br>
		<a href="./registrations.php" class="register">Registrations Panel</a>
		<a href="./memberpanel.php" class="register">Member Panel</a>
		<a href="./supportpanel.php" class="register">Support Panel</a>
        <div class="breadcrumb">
            <a href="/">Startseite</a>
            <span> ▹ </span>
            <span>Adminpanel</span>
            <span> ▹ </span>
            <span>dashboard</span>
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
            <th>Aktionen</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["benutzername"] . "</td><td>" . $row["ID"] . "</td><td>" . $row["Verbrechen"] . "</td><td>" .$row["Beweis"] . "</td><td>" . $row["Server"] . "</td><td>" . $row["LINK"] . "</td>";
                echo "<td><a href='?action=edit&id=" . $row['ID'] . "'>Bearbeiten</a> | <a href='?action=delete&id=" . $row['ID'] . "'>Löschen</a></td></tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Keine Ergebnisse gefunden.</td></tr>";
        }
        ?>
    </table>

    <?php
    if (isset($userData)) {
        ?>
        <h3>Bearbeiten von Benutzer: <?php echo $userData['benutzername']; ?></h3>
        <form action="" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $userData['ID']; ?>">
            <label for="new_username" class="b">Neuer Benutzername:</label>
            <br>
            <input type="text" name="new_username" value="<?php echo $userData['benutzername']; ?>">
            <label for="new_id">Neue ID:</label>

            <input type="text" name="new_id" value="<?php echo $userData['ID']; ?>">
            <label for="new_verbrechen">Neues Verbrechen:</label>

            <input type="text" name="new_verbrechen" value="<?php echo $userData['Verbrechen']; ?>">
             <label for="new_beweis">Neuer Beweis:</label>

            <input type="text" name="new_beweis" value="<?php echo $userData['Beweis']; ?>">
            <label for="new_server">Neuer Server:</label>

            <input type="text" name="new_server" value="<?php echo $userData['Server']; ?>">
            <label for="new_link">Neuer LINK:</label>

            <input type="text" name="new_link" value="<?php echo $userData['LINK']; ?>">
            <button type="submit" name="update">Aktualisieren</button>

            <a href="dashboard.php" class="button">Abbrechen</a>
        </form>
        <?php
    } else {
        ?>
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
        <?php
    }
    $conn->close();
    ?>
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