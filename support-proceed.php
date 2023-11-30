<?php
// Datenbankverbindungsdaten
$servername = "45.83.245.57";
$username = "vsc_users";
$password = "***********";
$dbname = "userdb";

// Formulardaten abrufen
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $user_id = $_POST['userId'];
    $grund = $_POST['grund'];
	$weiteres = $_POST['weiteres'];
    // Datenbankverbindung herstellen
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Überprüfen, ob die Verbindung erfolgreich hergestellt wurde
    if ($conn->connect_error) {
        die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
    }

    // Validierung (hier einfachheitshalber nur grundlegende Validierung)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Ungültige E-Mail-Adresse";
    } else {
        // SQL-Abfrage zum Einfügen der Daten in die Tabelle
        $sql = "INSERT INTO support (email, id, grund, weiteres) VALUES (?, ?, ?, ?)";

        // Vorbereitete Anweisung verwenden, um SQL-Injektionen zu verhindern
        $stmt = $conn->prepare($sql);

        // Überprüfen, ob die vorbereitete Anweisung korrekt erstellt wurde
        if ($stmt === false) {
            echo "Fehler beim Vorbereiten der SQL-Anweisung: " . $conn->error;
        } else {
            // Parameter binden
            $stmt->bind_param("ssss", $email, $user_id, $grund, $weiteres);

            // Überprüfen, ob das Einfügen erfolgreich war
            if ($stmt->execute()) {
                ?>
				<script>
					alert("Daten gespeichert!")
					window.location.href="https://discord.gg/eYfB9Dq5Du";
				</script>
			<?php
            } else {
                echo "Fehler beim Einfügen der Daten: " . $stmt->error;
            }

            // Vorbereitete Anweisung schließen
            $stmt->close();
        }
    }

    // Datenbankverbindung schließen
    $conn->close();
}
?>

