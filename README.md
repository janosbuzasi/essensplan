
# Essensplan für Mahlzeiten

Dieses Projekt ermöglicht die Verwaltung eines wöchentlichen Essensplans, einschließlich der Möglichkeit, Rezepte hinzuzufügen, zu bearbeiten und zu löschen.<br>
Benutzer können zwischen verschiedenen CSS-Stilen wechseln und jederzeit zur ursprünglichen Version zurückkehren.

## Projektstruktur

```
essensplan/
│
├── assets/
│   ├── style.css             # Standard-CSS-Stil
│   ├── classic_style.css     # Classic-CSS-Stil
│   └── yellow_style.css      # Gelber CSS-Stil
│
├── config/
│   └── db.php                # Datenbankverbindungsdatei
│
├── src/
│   ├── index.php             # Hauptseite zur Essensplan-Verwaltung
│   ├── view_recipes.php      # Seite zum Anzeigen aller Rezepte
│   ├── add_recipe.php        # Seite zum Hinzufügen neuer Rezepte
│   ├── edit_recipe.php       # Seite zum Bearbeiten bestehender Rezepte
│   └── delete_recipe.php     # Seite zum Löschen von Rezepten
│
├── admin/
│   └── setup.php             # Initiale Datenbankeinrichtung
│
├── change_style.php          # Seite zum Ändern des CSS-Stils
├── contact.php               # Kontaktseite
├── about_us.php              # Über uns Seite
├── header.php                # Gemeinsamer Header für alle Seiten
├── footer.php                # Gemeinsamer Footer für alle Seiten
└── README.md                 # Diese Datei
```

## Installation

1. **Projekt klonen:**
   ```bash
   git clone <repository-url>
   ```
2. **Datenbank einrichten:**
   Führe `admin/setup.php` aus, um die Datenbanktabellen zu erstellen.
3. **Datenbankverbindung konfigurieren:**
   Passe die Datei `config/db.php` an deine Datenbankeinstellungen an.
4. **CSS-Stile anpassen:**
   Füge deine eigenen CSS-Stile in das Verzeichnis `assets/` hinzu oder passe die vorhandenen an.

## Nutzung

- **Essensplan erstellen:** Über die `index.php` kannst du neue Wochenpläne erstellen, anzeigen, bearbeiten und löschen.
- **Rezepte verwalten:** Auf der Seite `view_recipes.php` kannst du alle vorhandenen Rezepte anzeigen, neue hinzufügen oder bestehende bearbeiten und löschen.
- **CSS-Stil ändern:** Besuche `change_style.php`, um den Stil der Webseite zu ändern.

## Rückkehr zum Standardstil

Falls du zu `style.css` zurückkehren möchtest, kannst du auf der Seite `change_style.php` die Option "Zurücksetzen" wählen.

## Bekannte Probleme

- **CSS-Anzeigeprobleme:** Falls der Essensplan nicht korrekt angezeigt wird, überprüfe die Einstellungen in `style.css` und stelle sicher, dass keine anderen Stile die Anzeige beeinflussen.

## Lizenz

Dieses Projekt ist unter der MIT-Lizenz lizenziert.
