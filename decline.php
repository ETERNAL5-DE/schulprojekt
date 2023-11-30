<?php
// Annahme: Du hast eine MySQL-Datenbank mit einer Tabelle namens "normal" und den Spalten "username", "password", "email", "ID", "Server", "Server_ID" und "LINK".

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

// Verarbeitung der GET-Parameter
if (isset($_GET['username'])) {
    $username = $_GET['username'];

    // SQL-Abfrage für den abzulehnenden Benutzer
    $rejectSQL = "INSERT INTO users SELECT * FROM normal WHERE username = '$username'";
    $deleteSQL = "DELETE FROM normal WHERE username = '$username'";

    // Ausführen der SQL-Statements
    $conn->query($rejectSQL);
    $conn->query($deleteSQL);
	?>	
<script>
	alert("Benutzer wurde abgelehnt");
</script>
	<?php
} else {
    echo "Fehler: Benutzername nicht angegeben.";
}

// Verbindung zur Datenbank schließen
$conn->close();
?>
