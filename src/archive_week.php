<?php
$title = "Essensplan archivieren"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plan) {
            if (isset($_POST['confirm_archive'])) {
                $stmt = $conn->prepare("UPDATE essensplan SET status = 'archiviert' WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                echo "<p>Essensplan wurde archiviert.</p>";
            } else {
                ?>
                <form action="archive_week.php?id=<?php echo $plan['id']; ?>" method="post">
                    <p>Bist du sicher, dass du den Essensplan 'Woche <?php echo $plan['week_number']; ?> im Jahr <?php echo $plan['year']; ?>' archivieren möchtest?</p>
                    <input type="submit" name="confirm_archive" value="Ja, archivieren">
                    <a href="index.php">Abbrechen</a>
                </form>
                <?php
            }
        } else {
            echo "<p>Essensplan nicht gefunden.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
