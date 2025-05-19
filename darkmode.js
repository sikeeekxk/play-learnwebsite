// 1. Create a file named darkmode.js with this content
// Add this to a new file: js/darkmode.js

function applyDarkMode() {
    if (localStorage.getItem("dark-mode") === "enabled") {
        document.body.classList.add("dark-mode");
    } else {
        document.body.classList.remove("dark-mode");
    }
}

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("dark-mode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
}

// Apply dark mode settings when the page loads
document.addEventListener('DOMContentLoaded', function() {
    applyDarkMode();
});

// 2. Add this script to your common header or footer file
// Include in all page headers or in a common file that all pages include:
<script src="js/darkmode.js"></script>

// 3. Update the toggle button in settings.php
<button type="button" class="btn btn-dark float-end" onclick="toggleDarkMode()">Toggle Dark Mode</button>

// 4. Add a dark mode toggle button to your header for all pages (optional)
// Add this to your header.php or similar:
<button class="btn btn-sm btn-outline-light ms-2" onclick="toggleDarkMode()">
    <i class="fas fa-moon"></i>
</button>