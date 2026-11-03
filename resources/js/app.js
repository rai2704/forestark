import './a11y';

document.documentElement.classList.add('js');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// A faixa fixa do rodapé reserva o próprio espaço no fim da página
const footerBar = document.querySelector('[data-footer-bar]');
if (footerBar) {
    const fitBar = () => {
        const h = footerBar.offsetHeight;
        document.documentElement.style.setProperty('--bar-h', `${h}px`);
        document.body.style.paddingBottom = `${h}px`;
    };
    new ResizeObserver(fitBar).observe(footerBar);
    fitBar();
}

// Revela elementos com animação ao entrarem na tela
const wow = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
        if (e.isIntersecting) {
            e.target.classList.add('is-in');
            wow.unobserve(e.target);
        }
    });
});
document.querySelectorAll('[data-wow]').forEach((el) => wow.observe(el));

// Menu fixo e botão de voltar ao topo
const menu = document.querySelector('[data-menu]');
const toTop = document.querySelector('[data-top]');
const onScroll = () => {
    const y = window.scrollY;
    if (menu) {
        menu.toggleAttribute('data-fixed', y >= 190);
    }
    toTop?.classList.toggle('hidden', y <= 200);
};
window.addEventListener('scroll', onScroll, { passive: true });
onScroll();
toTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

document.querySelector('[data-menu-toggle]')?.addEventListener('click', (e) => {
    const hidden = document.querySelector('[data-menu-list]')?.classList.toggle('max-lg:hidden');
    e.currentTarget.setAttribute('aria-expanded', String(!hidden));
});

// Carrossel: rotaciona os itens em loop, como o Owl Carousel do tema
document.querySelectorAll('[data-carousel]').forEach((root) => {
    const track = root.querySelector('[data-track]');
    if (!track || !track.children.length) {
        return;
    }
    const visible = () => parseInt(getComputedStyle(root).getPropertyValue('--n'), 10) || 1;
    if (track.children.length <= visible()) {
        [...track.children].forEach((c) => track.appendChild(c.cloneNode(true)));
    }
    const gap = () => parseFloat(getComputedStyle(root).getPropertyValue('--g')) || 0;
    let busy = false;

    const slide = (dir) => {
        if (busy) {
            return;
        }
        busy = true;
        const step = track.children[0].offsetWidth + gap();
        const finish = () => {
            track.style.transition = 'none';
            track.style.transform = '';
            if (dir > 0) {
                track.appendChild(track.children[0]);
            }
            busy = false;
        };
        if (dir < 0) {
            track.style.transition = 'none';
            track.prepend(track.lastElementChild);
            track.style.transform = `translateX(${-step}px)`;
            track.getBoundingClientRect();
        }
        track.style.transition = 'transform .25s ease';
        track.style.transform = dir > 0 ? `translateX(${-step}px)` : 'translateX(0)';
        track.addEventListener('transitionend', finish, { once: true });
    };

    root.querySelector('[data-next]')?.addEventListener('click', () => slide(1));
    root.querySelector('[data-prev]')?.addEventListener('click', () => slide(-1));
    let paused = false;
    root.addEventListener('mouseenter', () => { paused = true; });
    root.addEventListener('mouseleave', () => { paused = false; });
    root.addEventListener('focusin', () => { paused = true; });
    root.addEventListener('focusout', () => { paused = false; });
    if (!reducedMotion) {
        setInterval(() => { if (!paused && !document.hidden) slide(1); }, 4000);
    }
});

// Banner: transição em mosaico, como o Camera Slider do tema
document.querySelectorAll('[data-hero]').forEach((hero) => {
    const slides = [...hero.querySelectorAll('[data-slide]')];
    if (slides.length < 2) {
        return;
    }
    let current = 0;
    let busy = false;
    const COLS = 8;
    const ROWS = 6;

    const show = (next) => {
        if (busy || next === current) {
            return;
        }
        busy = true;
        const target = slides[next];
        const image = target.dataset.image;
        const grid = document.createElement('div');
        grid.className = 'absolute inset-0 z-10 grid';
        grid.style.gridTemplate = `repeat(${ROWS}, 1fr) / repeat(${COLS}, 1fr)`;

        const cells = [];
        const width = hero.offsetWidth;
        const height = hero.offsetHeight;
        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS; c++) {
                const cell = document.createElement('div');
                cell.style.cssText = `background:linear-gradient(rgba(30,30,30,.5),rgba(30,30,30,.5)),url("${image}");background-size:${width}px ${height}px;background-position:${-c * width / COLS}px ${-r * height / ROWS}px;opacity:0;transition:opacity .6s ease`;
                grid.appendChild(cell);
                cells.push(cell);
            }
        }
        hero.appendChild(grid);
        cells.forEach((cell) => setTimeout(() => { cell.style.opacity = '1'; }, Math.random() * 1000));

        setTimeout(() => {
            slides[current].classList.add('invisible');
            target.classList.remove('invisible');
            grid.remove();
            current = next;
            busy = false;
        }, 1650);
    };

    hero.querySelector('[data-next]')?.addEventListener('click', () => show((current + 1) % slides.length));
    hero.querySelector('[data-prev]')?.addEventListener('click', () => show((current - 1 + slides.length) % slides.length));
    if (!reducedMotion) {
        setInterval(() => { if (!document.hidden) show((current + 1) % slides.length); }, 5650);
    }
});

// Galeria: abre a foto ampliada, com navegação por botões e teclado
const photos = [...document.querySelectorAll('[data-lightbox]')];
if (photos.length) {
    const labels = document.body.dataset;
    const box = document.createElement('div');
    box.setAttribute('role', 'dialog');
    box.setAttribute('aria-modal', 'true');
    box.hidden = true;
    box.style.cssText = 'position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.92);align-items:center;justify-content:center;padding:50px 60px;';
    const img = document.createElement('img');
    img.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;';
    const button = (html, label, css) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.innerHTML = html;
        b.setAttribute('aria-label', label);
        b.style.cssText = `position:absolute;color:#fff;font-size:32px;line-height:1;padding:12px;cursor:pointer;${css}`;
        return b;
    };
    const close = button('&times;', labels.lbClose, 'top:8px;right:12px;');
    const prev = button('&#8249;', labels.lbPrev, 'left:8px;top:50%;transform:translateY(-50%);font-size:48px;');
    const next = button('&#8250;', labels.lbNext, 'right:8px;top:50%;transform:translateY(-50%);font-size:48px;');
    box.append(img, close, prev, next);
    document.body.appendChild(box);

    let current = 0;
    let opener = null;
    const show = (i) => {
        current = (i + photos.length) % photos.length;
        img.src = photos[current].href;
        img.alt = photos[current].getAttribute('aria-label') || '';
    };
    const hide = () => {
        box.hidden = true;
        box.style.display = 'none';
        opener?.focus();
    };
    photos.forEach((link, i) => link.addEventListener('click', (e) => {
        e.preventDefault();
        opener = link;
        show(i);
        box.hidden = false;
        box.style.display = 'flex';
        close.focus();
    }));
    close.addEventListener('click', hide);
    prev.addEventListener('click', () => show(current - 1));
    next.addEventListener('click', () => show(current + 1));
    box.addEventListener('click', (e) => { if (e.target === box) hide(); });
    document.addEventListener('keydown', (e) => {
        if (box.hidden) return;
        if (e.key === 'Escape') hide();
        if (e.key === 'ArrowLeft') show(current - 1);
        if (e.key === 'ArrowRight') show(current + 1);
    });
}
