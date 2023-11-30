<?php
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

// Überprüfen, ob das Registrierungsformular gesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Benutzerdaten aus dem Formular erhalten
    $benutzername = $_POST["benutzername"];
    $passwort = $_POST["passwort"];
    $email = $_POST["email"];
    $id = $_POST["id"];
    $server = $_POST["server"];
    $server_id = $_POST["server_id"];
    $link = $_POST["link"];

    // SQL-Befehl für die Datenbank vorbereiten und ausführen
    $sql = "INSERT INTO normal (username, password, email, id, server, server_id, link) VALUES ('$benutzername', '$passwort', '$email', '$id', '$server', '$server_id', '$link')";

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Daten übermittelt, das Team wird sich bei dir melden.");</script>';
    } else {
        echo "Fehler: " . $sql . "<br>" . $conn->error;
    }
}
// Verbindung zur Datenbank schließen
$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
	<link rel="icon" href="https://cdn-icons-png.flaticon.com/128/2921/2921222.png" type="image/png">
	<link rel="stylesheet" ref="text/css" href="register.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerregistrierung</title>
</head>
<body>

<h2>Benutzerregistrierung</h2>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <label for="benutzername">Benutzername:</label>
    <input type="text" name="benutzername" required placeholder="Max Mustermann"><br>

    <label for="passwort">Passwort:</label>
    <input type="password" name="passwort" required placeholder="abcdefg12345!"><br>

    <label for="email">E-Mail:</label>
    <input type="email" name="email" required placeholder="max@musterman.de"><br>

    <label for="id">ID:</label>
    <input type="text" name="id" required placeholder="xxxxxxxxxxxxxxxxxx"><br>

    <label for="server">Server:</label>
    <input type="text" name="server" required placeholder="Max´s Community"><br>

    <label for="server_id">Server ID:</label>
    <input type="text" name="server_id" required placeholder="xxxxxxxxxxxxxxxxxx"><br>

    <label for="link">LINK:</label>
    <input type="text" name="link" required placeholder="https://discord.gg/xxxxxxx"><br>
	
    <input type="checkbox" name="button" required> Ich stimme zu, das meine Daten gespeichert werden
    <br>
    <br>
    <input type="submit" value="Registrieren">
</form>

</body>
</html>