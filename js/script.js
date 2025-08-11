function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-bs-theme');
    html.setAttribute('data-bs-theme', currentTheme === 'dark' ? 'light' : 'dark');
    // Optionally, save preference to localStorage
    localStorage.setItem('theme', html.getAttribute('data-bs-theme'));
}

// On page load, set theme from localStorage if available
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    }
});

