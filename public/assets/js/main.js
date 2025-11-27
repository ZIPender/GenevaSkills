// Theme Toggle
const initTheme = () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.body.classList.toggle('dark-theme', savedTheme === 'dark');
    updateThemeIcon();
};

const toggleTheme = () => {
    document.body.classList.toggle('dark-theme');
    const isDark = document.body.classList.contains('dark-theme');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeIcon();
};

const updateThemeIcon = () => {
    const isDark = document.body.classList.contains('dark-theme');
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.innerHTML = isDark ? '☀️' : '🌙';
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    console.log('GenevaSkills loaded');

    // Initialize theme
    initTheme();

    // Client-side password validation
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        if (input.name === 'password' && input.form) {
            input.form.addEventListener('submit', (e) => {
                const password = input.value;
                const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

                if (!pattern.test(password)) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
                }
            });
        }
    });

    // Delete confirmations
    const deleteButtons = document.querySelectorAll('a[href*="delete"], button[data-action="delete"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });

    // Make cards clickable
    const clickableCards = document.querySelectorAll('.card-clickable');
    clickableCards.forEach(card => {
        card.addEventListener('click', (e) => {
            // Don't trigger if clicking on a button or link inside the card
            if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON' && !e.target.closest('.btn')) {
                const link = card.querySelector('a[data-card-link]');
                if (link) {
                    window.location.href = link.href;
                }
            }
        });
    });
});
