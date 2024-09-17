# Wochenplan für Essen

Dies ist eine dynamische PHP-Webanwendung, mit der du einen Wochenplan für Rezepte anzeigen und verwalten kannst. Das Projekt nutzt PHP und MySQL als Datenbank, und alle Änderungen können über Git versioniert werden.

## Ordnerstruktur

Die Anwendung hat folgende Ordnerstruktur:

```
/wochenplan
├── /admin          (Verzeichnis für administrative Aufgaben, z. B. Datenbank-Setup)
│   └── setup.php   (Skript für das einmalige Setup der Datenbank)
├── /src            (PHP-Quellcode und Logik)
│   └── add_recipe.php (Script zum Hinzufügen eines Rezepts)
├── /config         (Konfigurationsdateien, z. B. für die Datenbank)
│   └── db.php      (Datenbankverbindungs-Skript)
├── /templates      (HTML-Templates für das Frontend)
│   └── add_recipe.html (Formular für das Hinzufügen eines Rezepts)
├── /assets         (CSS, JS und andere Ressourcen für das Frontend)
│   └── style.css   (CSS-Datei für das Styling der Seite)
└── index.php       (Startseite, die den Wochenplan anzeigt)
```

## Voraussetzungen

Um die Anwendung lokal oder auf einem Server auszuführen, benötigst du:

- PHP (Version 7.4 oder höher)
- MySQL-Datenbank
- Webserver (Apache, Nginx, etc.)
- Composer (falls du zusätzliche PHP-Bibliotheken installieren möchtest)
- Git zur Versionierung des Projekts

## Installation

### 1. Repository klonen

Klonen des Repositories in ein lokales Verzeichnis:

```bash
git clone https://github.com/dein-benutzername/wochenplan.git
```

### 2. Datenbank einrichten

- Stelle sicher, dass MySQL auf deinem Rechner läuft.
- Führe das Setup-Skript aus, um die Datenbank und die notwendigen Tabellen zu erstellen:

   1. Öffne die Datei `/config/db.php` und passe die Datenbankkonfiguration (Benutzername, Passwort, etc.) an.
   
   2. Führe das Setup-Skript über deinen Browser aus:
   
   ```bash
   http://localhost/wochenplan/admin/setup.php
   ```
   
   Dies erstellt die MySQL-Datenbank und die notwendigen Tabellen (`recipes` und `meal_plan`).

### 3. Website starten

1. Stelle sicher, dass dein Webserver auf das Verzeichnis `/wochenplan` verweist. Wenn du `localhost` verwendest, solltest du die Seite unter `http://localhost/wochenplan` aufrufen können.

2. Du solltest den Wochenplan sehen. Wenn du Rezepte hinzufügen möchtest, erstelle eine entsprechende Logik (siehe `add_recipe.php` und `add_recipe.html`).

### 4. Styling anpassen

Im Ordner `/assets` findest du die Datei `style.css`, die das grundlegende Styling der Seite definiert. Passe diese nach deinen Bedürfnissen an.

## Deployment

Wenn du die Anwendung live schalten möchtest, folge diesen Schritten:

1. Lade alle Dateien auf deinen Webserver.
2. Achte darauf, dass die Datenbank-Informationen in `config/db.php` korrekt sind.
3. Stelle sicher, dass der Server die PHP-Dateien ausführt und MySQL-Verbindungen erlaubt.
4. Optional: Richte HTTPS auf deinem Server ein, um die Sicherheit der Datenübertragung zu gewährleisten.

## Mitwirken

Beiträge sind willkommen! Forke das Repository, erstelle einen neuen Branch, führe deine Änderungen durch und erstelle eine Pull-Request.

### Beispiel:

1. Forke das Repository
2. Erstelle einen Feature-Branch (`git checkout -b feature-neues-feature`)
3. Committe deine Änderungen (`git commit -am 'Füge ein neues Feature hinzu'`)
4. Pushe zum Branch (`git push origin feature-neues-feature`)
5. Erstelle eine Pull-Request

## Lizenz

Dieses Projekt steht unter der MIT-Lizenz – weitere Informationen findest du in der `LICENSE`-Datei.
