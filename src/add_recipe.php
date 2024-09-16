<?php
require_once '../config/db.php';
if ($_POST) {
  $db = new Database();
  $conn = $db->getConnection();
  
  $stmt = $conn->prepare("INSERT INTO recipes (title, description, ingredients, instructions) VALUES (?, ?, ?, ?)");
  $stmt->execute([$_POST['title'], $_POST['description'], $_POST['ingredients'], $_POST['instructions']]);
  
  header('Location: ../public/index.php');
}
?>
