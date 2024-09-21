
<?php
// Datenbankkonfigurationsdatei für essensplan

define('DB_SERVER', 'localhost');
define('DB_NAME', 'essensplan');  // Name der Datenbank auf "essensplan" ändern
define('DB_USER', 'essensplan');  // Benutzername für die Datenbank
define('DB_PASSWORD', 'essensplan');  // Passwort für die Datenbankverbindung

class Database {
    private $host = DB_SERVER;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASSWORD;
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Datenbankverbindungsfehler: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
