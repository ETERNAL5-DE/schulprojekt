<?php
session_start();
error_reporting(E_ALL);
$servername = "45.83.245.57";
$username = "vsc_forum_";
$password = "***********";
$dbname = "forum_";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$benutzername = isset($_SESSION["benutzername"]) ? $_SESSION["benutzername"] : null;
$erfolgsmeldung = $fehlermeldung = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $benutzername) {
    $postText = isset($_POST["post_text"]) ? $_POST["post_text"] : null;

    if ($postText) {
        $checkSql = "SELECT * FROM posts WHERE message='$postText'";
        $checkResult = $conn->query($checkSql);

        if ($checkResult->num_rows == 0) {
            $insertSql = "INSERT INTO posts (username, message, date) VALUES ('$benutzername', '$postText', NOW())";
            $result = $conn->query($insertSql);

            if ($result) {
                $erfolgsmeldung = "Beitrag erfolgreich veröffentlicht!";
            } else {
                $fehlermeldung = "Fehler beim Veröffentlichen des Beitrags: " . $conn->error;
            }
        } else {
            $fehlermeldung = "Nachricht bereits vorhanden.";
        }
    }
}

function displayPosts($conn) {
    echo "<h2>Forum-Beiträge</h2>";

    $selectSql = "SELECT * FROM posts ORDER BY date DESC";
    $result = $conn->query($selectSql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='post'>";
            echo "<p><strong>{$row['username']}</strong> - {$row['date']}</p>";
            echo "<p>{$row['message']}</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Es gibt noch keine Beiträge.</p>";
    }
}

if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    header("Location: forum.php");
    exit();
}

// RSS-Feed generieren
header("Content-Type: application/xml; charset=UTF-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<rss version="2.0">';
echo '<channel>';
echo '<title>Forum Feed</title>';
echo '<description>Neuester Beitrag im Forum</description>';
echo '<link>https://vertexcloud.de/index.php</link>'; // Ändere dies entsprechend deiner Domain

$selectSql = "SELECT * FROM posts ORDER BY date DESC LIMIT 1"; // Änderung: Limit auf 1 für den neuesten Beitrag
$result = $conn->query($selectSql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo '<item>';
    echo '<title>' . htmlspecialchars($row['username']) . '</title>';
    echo '<description>' . htmlspecialchars($row['message']) . '</description>';
    echo '<link>https://vertexcloud.de/forum/index.php</link>';
    echo '<pubDate>' . date("D, d M Y H:i:s O", strtotime($row['date'])) . '</pubDate>';
    echo '</item>';
}

echo '</channel>';
echo '</rss>';
$conn->close();
?>
