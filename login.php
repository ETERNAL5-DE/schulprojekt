<?php
session_start();
// Verbindung zur Datenbank herstellen
$servername = "45.83.245.57";
$username = "vsc_users";
$password = "***********";
$dbname = "userdb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Initialisiere Meldungsvariablen
$erfolgsmeldung = "";
$fehlermeldung = "";

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Die eingegebenen Daten aus dem Formular abrufen
    $eingegebener_benutzername = isset($_POST["benutzername"]) ? $_POST["benutzername"] : null;
    $eingegebener_code = isset($_POST["code"]) ? $_POST["code"] : null;

    // Überprüfen, ob der eingegebene Benutzername und Code korrekt sind
    $sql = "SELECT * FROM users WHERE username = '$eingegebener_benutzername' AND password = '$eingegebener_code'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // Benutzer gefunden, überprüfe die Stellung
            $row = $result->fetch_assoc();

            if ($row['Stellung'] == 'Admin') {
                // Benutzer hat die Stellung 'Admin', weiterleiten zur Admin-Seite
                $_SESSION["benutzername"] = $eingegebener_benutzername;
                $erfolgsmeldung = "Zugriff gewährt! :: Umleitung auf forum.php";
                // JavaScript zum Umleiten nach 5 Sekunden einfügen
                echo '<script>
                        setTimeout(function() {
                            window.location.href = "./index.php";
                        }, 2500);
                      </script>';
            } else {
                // Benutzer hat eine andere Stellung, weiterleiten zur anderen Seite
                $fehlermeldung = "Zugriff verweigert!";
            }
        } else {
            // Benutzer nicht gefunden, hier kannst du eine Fehlermeldung oder Weiterleitung einfügen
            $fehlermeldung = "Zugriff verweigert!";
        }
    } else {
        // Fehler bei der SQL-Abfrage, hier kannst du eine Fehlermeldung oder Weiterleitung einfügen
        $fehlermeldung = "Fehler bei der Anmeldung.";
    }
}

// Datenbankverbindung schließen
$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="icon" rel="image/x-icon" href="https://cdn-icons-png.flaticon.com/128/4618/4618413.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code</title>
    <link rel="stylesheet" href="./style_files/login.css">
</head>
<body>
    <!-- Anzeigen der Meldungen als <h2>, wenn vorhanden -->
    <?php
    if ($erfolgsmeldung !== "") {
        echo "<h2 class='erfolgsmeldung'>$erfolgsmeldung</h2>";
    } elseif ($fehlermeldung !== "") {
        echo "<h2 class='fehlermeldung'>$fehlermeldung</h2>";
    }
    ?>
	<br>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="benutzername">Benutzername:</label>
        <input type="text" id="benutzername" name="benutzername" required>

        <label for="code">Passwort:</label>
        <input type="password" id="code" name="code" required>

        <button type="submit">Überprüfen</button>
		<br>
		<br>
		<button onclick="window.location.href = 'http://vertex.deinweb.space';" style="background-color: #BBC6C8;">Abbrechen</button>
    </form>
</body>
</html>