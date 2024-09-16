# wochenplan
Ein dynamisches Website-Projekt mit PHP, MySQL und Git aufzusetzen. Der Plan umfasst folgende Schritte:

## 1. Projektstruktur aufsetzen

Zuerst erstellen wir die Grundstruktur für deine Website:

Ordnerstruktur:

/wochenplan<br>
├── /public (Für die öffentlich zugänglichen Dateien)<br>
├── /src (PHP-Quellcode)<br>
├── /config (Datenbank- und andere Konfigurationen)<br>
├── /templates (HTML-Templates für Frontend)<br>
├── /assets (CSS/JS für Frontend)<br>
└── /sql (Datenbankschema)<br>

## 2. Datenbankdesign

eine MySQL-Datenbank wird erstellt, welche die folgende Tabellen enthalten:

recipes (Rezept-Daten)

''
CREATE TABLE recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  ingredients TEXT,
  instructions TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
''

meal_plan (Verknüpfung der Rezepte mit Tagen)

''
CREATE TABLE meal_plan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT,
  day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id)
);
''

## 3. PHP-Backend

Für das PHP-Backend wird eine einfache CRUD (Create, Read, Update, Delete)-Funktionen angewendet:

Datenbankverbindung (/config/db.php):

<?php
class Database {
  private $host = "localhost";
  private $db_name = "wochenplan";
  private $username = "root";
  private $password = "";
  public $conn;
  
  public function getConnection() {
    $this->conn = null;
    try {
      $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
      $this->conn->exec("set names utf8");
    } catch (PDOException $exception) {
      echo "Connection error: " . $exception->getMessage();
    }
    return $this->conn;
  }
}
?>

Rezepte hinzufügen (/src/add_recipe.php):

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

## 4. Frontend

HTML-Formular zum Hinzufügen eines Rezepts (/templates/add_recipe.html):

<form method="POST" action="../src/add_recipe.php">
  <label for="title">Titel:</label>
  <input type="text" name="title" required>
  <br>
  <label for="description">Beschreibung:</label>
  <textarea name="description"></textarea>
  <br>
  <label for="ingredients">Zutaten:</label>
  <textarea name="ingredients"></textarea>
  <br>
  <label for="instructions">Anleitung:</label>
  <textarea name="instructions"></textarea>
  <br>
  <input type="submit" value="Rezept hinzufügen">
</form>

Wochenansicht der Mahlzeiten (/public/index.php):

<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->query("SELECT * FROM meal_plan INNER JOIN recipes ON meal_plan.recipe_id = recipes.id");
$meal_plan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Wochenplan für Essen</h1>
<table>
  <tr>
    <th>Tag</th>
    <th>Rezept</th>
  </tr>
  <?php foreach ($meal_plan as $meal): ?>
  <tr>
    <td><?php echo $meal['day_of_week']; ?></td>
    <td><?php echo $meal['title']; ?></td>
  </tr>
  <?php endforeach; ?>
</table>

## 5. Git-Versionierung

Du kannst dein Projekt versionieren, indem du ein Git-Repository erstellst:

Initialisiere das Git-Repository:

git init
git add .
git commit -m "Initial commit"

GitHub Repository hinzufügen:
1.	Erstelle ein neues Repository auf GitHub.
2.	Füge das Remote-Repository hinzu:

git remote add origin https://github.com/username/repository.git
git push -u origin master

## 6. Deployment (z. B. auf einem Webserver wie Heroku oder AWS)

Um die Seite live zu stellen, kannst du entweder einen Webhoster wie Heroku, AWS oder einen eigenen Webserver verwenden.

Stelle sicher, dass die Datenbankkonfiguration auf dem Server korrekt ist.

Für das Deployment auch Git-Hooks verwenden, um automatisch von GitHub zu deployen.

