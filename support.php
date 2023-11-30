<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="icon" href="https://img.icons8.com/?size=80&id=4eDoX2N2balr&format=png" type="image/x-icon">
	<link rel="stylesheet" href="support.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support</title>
</head>
<body>
	<br>
		<h2 style="text-decoration: overline black;">Support erhalten...</h2>
		<form id="myForm" action="support-proceed.php" method="post">
    	<label for="userId">ID:</label>
    	<input type="text" id="userId" name="userId" required>
    	<br>
    	<label for="email">E-Mail:</label>
    	<input type="email" id="email" name="email" required>

    	<br>
    	<label for="grund">Grund:</label>
    	<select id="grund" name="grund" onchange="showHideGrundAndere()" required>
        	<option value="Passwort Änderungen">Passwort Änderungen</option>
        	<option value="Account Änderung">Account Änderung</option>
        	<option value="unrechtmäßiges hinzufügen">unrechtmäßiges hinzufügen</option>
       		<!-- Add more options as needed -->
    	</select>
	
    	<label for="weiteres">Weiteres:</label>
    	<input type="weiteres" id="weiteres" name="weiteres">

    	<br>
    	<input type="submit" value="Absenden">
		<a href="/" style="color: black;">Abbrechen</a>
	</form>

	<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
    	$servername = "45.83.245.57";
    	$username = "vsc_users";
    	$password = "***********";
    	$dbname = "userdb";

    	$conn = new mysqli($servername, $username, $password, $dbname);

    	if ($conn->connect_error) {
        	die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
    	}

    	$email = $_POST['email'];
    	$user_id = $_POST['userId'];
    	$weiteres = $_POST['weiteres'];

    	// Validierung (hier einfachheitshalber nur grundlegende Validierung)
    	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        	echo "Ungültige E-Mail-Adresse";
    	} else {
        	$sql = "INSERT INTO support (email, id, grund, weiteres) VALUES (?, ?, ?, ?)";

        	$stmt = $conn->prepare($sql);
        	$stmt->bind_param("ssss", $email, $user_id, $grund, $weiteres);

        	if ($stmt->execute()) {
            	echo "Daten erfolgreich in die Datenbank eingefügt";
        	} else {
            	echo "Fehler beim Einfügen der Daten: " . $stmt->error;
        	}

        	$stmt->close();
    	}

    	$conn->close();
	}
	?>

	</body>
</html>
