// Tamanho do texto e alto contraste, guardados no navegador do visitante.
const root = document.documentElement;
const MIN = -2;
const MAX = 5;
let level = Number(localStorage.getItem('fa_font')) || 0;

const applyFont = () => {
    root.style.zoom = level ? String(1 + level * 0.1) : '';
    localStorage.setItem('fa_font', String(level));
};

const syncContrast = () => {
    const on = root.classList.contains('alto-contraste');
    document.querySelectorAll('[data-a11y-contrast]').forEach((b) => b.setAttribute('aria-pressed', String(on)));
};

document.addEventListener('click', (e) => {
    const font = e.target.closest('[data-a11y-font]');
    if (font) {
        const delta = Number(font.dataset.a11yFont);
        level = delta === 0 ? 0 : Math.min(MAX, Math.max(MIN, level + delta));
        applyFont();
    }
    if (e.target.closest('[data-a11y-contrast]')) {
        const on = root.classList.toggle('alto-contraste');
        localStorage.setItem('fa_contrast', on ? '1' : '0');
        syncContrast();
    }
});

syncContrast();
