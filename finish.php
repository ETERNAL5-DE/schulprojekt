<?php
// Annahme: Du hast eine MySQL-Datenbank mit einer Tabelle namens "support" und den Spalten "id", "email" und "grund".

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
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // SQL-Abfrage für den abzulehnenden Benutzer
    $deleteSQL = "DELETE FROM support WHERE id = '$id'";

    // Ausführen der SQL-Statements
    if ($conn->query($deleteSQL) === TRUE) {
        ?>
        <script>
            alert("Benutzer wurde abgelehnt");
            window.location.href = 'registrations.php'; // Hier die Seite angeben, zu der du weiterleiten möchtest
        </script>
        <?php
    } else {
        echo "Fehler beim Löschen des Benutzers: " . $conn->error;
    }
} else {
    echo "Fehler: Benutzer-ID nicht angegeben.";
}

// Verbindung zur Datenbank schließen
$conn->close();
?>
