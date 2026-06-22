<footer class="site-footer">
    <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> webtrash.ch</p>
        <button class="theme-toggle" type="button" onclick="toggleDarkMode()">Darstellung wechseln</button>
    </div>
</footer>

<script>
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const mode = document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled';
    document.cookie = 'darkMode=' + mode + ';path=/;SameSite=Lax';
}

function getCookie(name) {
    const prefix = name + '=';
    return document.cookie.split(';').map(function (value) {
        return value.trim();
    }).find(function (value) {
        return value.indexOf(prefix) === 0;
    })?.substring(prefix.length) || null;
}

if (getCookie('darkMode') === 'enabled') {
    document.body.classList.add('dark-mode');
}
</script>
</body>
</html>
