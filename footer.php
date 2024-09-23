<footer>
    <div class="footer-content">
        <p>&copy; <?php echo date("Y"); ?> Essensplan by Janos Buzasi - Alle Rechte vorbehalten.</p>
        <ul class="social-links">
            <li><a href="https://github.com/janosbuzasi/essensplan">_github</a></li>
            <li>
                <a href="javascript:void(0);" onclick="toggleDarkMode()">Dark Mode umschalten</a> <!-- Dark Mode Umschaltung -->
            </li>
        </ul>
    </div>
</footer>

<script>
    // Funktion zum Umschalten des Dark Mode
    function toggleDarkMode() {
        var element = document.body;
        element.classList.toggle("dark-mode");

        // Zustand in einem Cookie speichern
        var darkMode = element.classList.contains("dark-mode") ? "enabled" : "disabled";
        document.cookie = "darkMode=" + darkMode + ";path=/"; // Cookie für das gesamte Verzeichnis setzen
    }

    // Überprüfen, ob der Dark Mode aktiviert ist
    function checkDarkMode() {
        var darkMode = getCookie("darkMode");
        if (darkMode === "enabled") {
            document.body.classList.add("dark-mode");
        }
    }

    // Cookie-Wert abrufen
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Dark Mode beim Laden der Seite überprüfen und anwenden
    checkDarkMode();
</script>

</body>
</html>
