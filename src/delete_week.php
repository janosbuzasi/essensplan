<?php
require_once '../config/db.php';  // Pfad anpassen

$db = new Database();
$conn = $db->getConnection();

$week_plan_id = $_GET['week_plan_id'];

// Löschen des Wochenplans
$stmt = $conn->prepare("DELETE FROM week_plan WHERE id = ?");
$stmt->execute([$week_plan_id]);

echo "Wochenplan erfolgreich gelöscht!";
header('Location: index.php');
