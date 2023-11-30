<?php
session_start();

// Verbindung zur Datenbank herstellen
$servername = "45.83.245.57";
$username = "vsc_forum_";
$password = "***********";
$dbname = "forum_";
$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Überprüfen, ob der Benutzer angemeldet ist
$benutzername = isset($_SESSION["benutzername"]) ? $_SESSION["benutzername"] : null;

// Funktion zum Formatieren des Texts
function formatiereText($text) {
    // Fett: [b]Text[/b]
    $text = preg_replace("/\[b\](.*?)\[\/b\]/", "<span class='bold-text'>$1</span>", $text);

    // Kursiv: [i]Text[/i]
    $text = preg_replace("/\[i\](.*?)\[\/i\]/", "<em>$1</em>", $text);

    // Code: [code]Text[/code]
    $text = preg_replace("/\[code\](.*?)\[\/code\]/", "<code>$1</code>", $text);

    // Farbe: [color=#RRGGBB]Text[/color]
    $text = preg_replace("/\[color=([#a-fA-F0-9]{6})\](.*?)\[\/color\]/", "<span style='color: #$1;'>$2</span>", $text);

    return $text;
}

// Löschen eines Beitrags, wenn der Benutzer angemeldet ist
if (isset($_POST["delete_post"]) && $benutzername) {
    $postID = isset($_POST["post_id"]) ? $_POST["post_id"] : null;

    if ($postID) {
        // Beitrag aus der Datenbank löschen
        $deleteSql = "DELETE FROM posts WHERE id='$postID' AND username='$benutzername'";
        $deleteResult = $conn->query($deleteSql);

        if ($deleteResult) {
            // Beitrag erfolgreich gelöscht
            $erfolgsmeldung = "Beitrag erfolgreich gelöscht!";
        } else {
            // Fehler beim Löschen des Beitrags
            $fehlermeldung = "Fehler beim Löschen des Beitrags.";
        }
    }
}

// Beitrag hinzufügen, wenn der Benutzer angemeldet ist
if ($_SERVER["REQUEST_METHOD"] == "POST" && $benutzername) {
    $postText = isset($_POST["post_text"]) ? $_POST["post_text"] : null;

    if ($postText) {
        // Text formatieren
        $postText = formatiereText($postText);

        // Überprüfen, ob die Nachricht bereits existiert
        $checkSql = "SELECT * FROM posts WHERE message='$postText'";
        $checkResult = $conn->query($checkSql);

        if ($checkResult->num_rows == 0) {
            // Beitrag in die Datenbank einfügen
            $insertSql = "INSERT INTO posts (username, message, date) VALUES ('$benutzername', '$postText', NOW())";
            $result = $conn->query($insertSql);

            if ($result) {
                // Beitrag erfolgreich in die Datenbank eingefügt
                $erfolgsmeldung = "Beitrag erfolgreich veröffentlicht!";
            } else {
                // Fehler beim Einfügen
                $fehlermeldung = "Fehler beim Veröffentlichen des Beitrags.";
            }
        } else {
            // Nachricht bereits vorhanden
            $fehlermeldung = "Nachricht bereits vorhanden.";
        }
    }
}

// Beispiel: Anzeige der neuesten Beiträge aus der Datenbank
function displayPosts($conn, $benutzername) {
    echo "<h2>Forum-Beiträge</h2>";

    // Beiträge aus der Datenbank abrufen
    $selectSql = "SELECT * FROM posts ORDER BY date DESC";
    $result = $conn->query($selectSql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='post'>";
            echo "<p><strong>{$row['username']}</strong> - {$row['date']}</p>";
            echo "<p>" . formatiereText($row['message']) . "</p>";

            // Löschen-Button nur für den Verfasser anzeigen
            if ($benutzername && $benutzername === $row['username']) {
                echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' class='delete-form'>";
                echo "<input type='hidden' name='post_id' value='{$row['id']}'>";
                echo "<button type='submit' name='delete_post' style='background-color: #ED413E;'>Löschen</button>";
                echo "</form>";
            }

            echo "</div>";
        }
    } else {
        echo "<div style='text-align: center;'>";
        echo "<p>Es gibt noch keine Beiträge.</p>";
        echo "</div>";
    }
}

// Beispiel: Abmelden
if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="icon" href="https://img.icons8.com/?size=48&id=pj6wp6z7skPd&format=png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
    <link rel="stylesheet" href="./style_files/forum-style.css">
    <style>
        .bold-text {
            font-weight: bold;
        }

        .delete-form {
            display: inline-block;
            margin-left: 10px;
        }
    </style>
    <div class="breadcrumb">
        <a href="/">Startseite</a>
        <span> ▹ </span>
        <span>Forum</span>
    </div>
</head>
<body>
    <?php if ($benutzername) : ?>
        <p>Eingeloggt als: <strong><?= $benutzername ?></strong></p>
        <a href="?logout">Abmelden</a>
    <?php else : ?>
        <p>Nicht eingeloggt. <a href="./login.php">Anmelden</a></p>
        <a href="./rss.php" target="_blank">RSS</a>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="post-form">
        <label for="post_text">Neuer Beitrag:</label>
        <textarea id="post_text" name="post_text" <?php if (!$benutzername) echo "disabled"; ?> required></textarea>
        
        <!-- Buttons für Textdekoration -->
        <div class="format-buttons">
            <button type="button" onclick="insertTag('b')" style="background-color: #36363f; border-radius: 100px;">Fett</button>
            <button type="button" onclick="insertTag('i')" style="background-color: black; border-radius: 100px;">Kursiv</button>
            <button type="button" onclick="insertTag('code')" style="background-color: black; border-radius: 100px;">Code</button>
            <input type="color" id="colorPicker" onchange="insertColor()" title="Farbauswahl">
        </div>
        <br>
        <button type="submit" <?php if (!$benutzername) echo "disabled"; ?>>Veröffentlichen</button>
        <script>
            function insertTag(tag) {
                var textarea = document.getElementById('post_text');
                var start = textarea.selectionStart;
                var end = textarea.selectionEnd;
                var selectedText = textarea.value.substring(start, end);
                var replacement = '[' + tag + ']' + selectedText + '[/' + tag + ']';
                textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
            }

            function insertColor() {
                var colorPicker = document.getElementById('colorPicker');
                var selectedColor = colorPicker.value;
                var textarea = document.getElementById('post_text');
                var start = textarea.selectionStart;
                var end = textarea.selectionEnd;
                var selectedText = textarea.value.substring(start, end);
                var replacement = '[color=' + selectedColor + ']' + selectedText + '[/color]';
                textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
            }
        </script>
    </form>

    <?php displayPosts($conn, $benutzername); ?>

</body>
</html>