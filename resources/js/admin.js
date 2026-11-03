import Quill from 'quill';
import './a11y';

const mountEditors = (root = document) => {
    root.querySelectorAll('textarea[data-html]:not([data-ready])').forEach((area) => {
        area.dataset.ready = '1';
        area.classList.add('d-none');
        const holder = document.createElement('div');
        area.after(holder);
        const quill = new Quill(holder, {
            theme: 'snow',
            modules: { toolbar: [['bold', 'italic', 'underline'], ['link'], [{ list: 'ordered' }, { list: 'bullet' }], ['clean']] },
        });
        quill.clipboard.dangerouslyPasteHTML(area.value);
        const sync = () => { area.value = quill.getText().trim() ? quill.getSemanticHTML().replaceAll('&nbsp;', ' ') : ''; };
        quill.on('text-change', sync);
    });
};

let activeLang = document.querySelector('[data-lang-tab]')?.dataset.langTab;

const applyLang = (root = document) => {
    root.querySelectorAll('[data-lang-field]').forEach((el) => {
        el.classList.toggle('d-none', el.dataset.langField !== activeLang);
    });
    document.querySelectorAll('[data-lang-tab]').forEach((tab) => {
        tab.classList.toggle('active', tab.dataset.langTab === activeLang);
    });
};

document.addEventListener('click', (e) => {
    const tab = e.target.closest('[data-lang-tab]');
    if (tab) {
        activeLang = tab.dataset.langTab;
        applyLang();
        return;
    }

    const clear = e.target.closest('[data-clear-media]');
    if (clear) {
        const box = clear.closest('[data-media]');
        box.querySelector('input[type=hidden]').value = '';
        box.querySelector('[data-preview]')?.remove();
        clear.remove();
    }

    if (e.target.closest('#sidebarToggle')) {
        document.getElementById('adminSidebar')?.classList.toggle('show');
    }
});

document.addEventListener('submit', (e) => {
    const message = e.target.dataset?.confirm;
    if (message && !confirm(message)) {
        e.preventDefault();
    }
});

document.querySelectorAll('.alert-auto-dismiss').forEach((el) => {
    setTimeout(() => window.bootstrap?.Alert.getOrCreateInstance(el).close(), 5000);
});

// Imagem acima de 2 MB ou documento acima de 5 MB não podem ser enviados: avisa ao escolher o arquivo
const MAX_IMAGE_BYTES = 2 * 1024 * 1024;
const MAX_FILE_BYTES = 5 * 1024 * 1024;
document.addEventListener('change', (e) => {
    const input = e.target;
    if (!input.matches?.('input[type=file]')) {
        return;
    }
    const big = [...input.files].filter((f) => f.size > (f.type.startsWith('image/') ? MAX_IMAGE_BYTES : MAX_FILE_BYTES));
    const message = big.length
        ? `Limite de 2 MB por imagem e 5 MB por documento. Acima do limite: ${big.map((f) => `${f.name} (${(f.size / 1048576).toFixed(1).replace('.', ',')} MB)`).join(', ')}.`
        : '';
    input.setCustomValidity(message);

    let note = input.parentElement.querySelector(':scope > [data-limit-error]');
    if (!note) {
        note = document.createElement('div');
        note.dataset.limitError = '';
        note.className = 'text-danger fw-semibold small mt-1';
        input.after(note);
    }
    note.textContent = message;
});

// Compara a imagem escolhida com o tamanho ideal antes de enviar
document.addEventListener('change', (e) => {
    const input = e.target;
    if (!input.matches?.('input[type=file][data-ideal-w]')) {
        return;
    }
    const out = input.closest('[data-media]')?.querySelector('[data-size-check]');
    const file = input.files[0];
    if (!out) {
        return;
    }
    out.textContent = '';
    if (!file) {
        return;
    }
    const w = Number(input.dataset.idealW);
    const h = Number(input.dataset.idealH);
    const url = URL.createObjectURL(file);
    const img = new Image();
    img.onload = () => {
        const small = img.naturalWidth < w || img.naturalHeight < h;
        const shape = Math.abs(img.naturalWidth / img.naturalHeight - w / h) > (w / h) * 0.1;
        const size = `${img.naturalWidth} × ${img.naturalHeight} px`;
        let text = `Imagem escolhida: ${size}. Tamanho adequado.`;
        if (small) {
            text = `Imagem escolhida: ${size}. Menor que o ideal, pode perder nitidez.`;
        } else if (shape) {
            text = `Imagem escolhida: ${size}. Proporção diferente da ideal, pode ser cortada.`;
        }
        out.textContent = text;
        out.className = `form-text fw-semibold ${small || shape ? 'text-danger' : 'text-success'}`;
        URL.revokeObjectURL(url);
    };
    img.src = url;
});

// Grade de permissões do usuário
const perms = document.querySelector('[data-perms]');
if (perms) {
    const boxes = [...perms.querySelectorAll('.perm-cb')];
    const modules = [...perms.querySelectorAll('.perm-mod-toggle')];
    const all = perms.querySelector('#toggleAllPerms');
    const allLabel = perms.querySelector('#toggleAllLabel');
    const sync = () => {
        modules.forEach((m) => {
            m.checked = boxes.filter((b) => b.dataset.mod === m.dataset.mod).every((b) => b.checked);
        });
        all.checked = boxes.every((b) => b.checked);
        allLabel.textContent = all.checked ? 'Desmarcar todos' : 'Marcar todos';
    };
    all.addEventListener('change', () => {
        boxes.forEach((b) => { b.checked = all.checked; });
        sync();
    });
    modules.forEach((m) => m.addEventListener('change', () => {
        boxes.filter((b) => b.dataset.mod === m.dataset.mod).forEach((b) => { b.checked = m.checked; });
        sync();
    }));
    boxes.forEach((b) => b.addEventListener('change', sync));
    sync();

    // Nível com acesso total dispensa a grade
    const role = document.querySelector('[data-role-select]');
    const fullIds = perms.dataset.fullIds.split(',');
    const note = document.querySelector('[data-perms-full]');
    const toggleGrid = () => {
        const full = fullIds.includes(role.value);
        perms.classList.toggle('d-none', full);
        note.classList.toggle('d-none', !full);
    };
    role.addEventListener('change', toggleGrid);
    toggleGrid();
}

applyLang();
mountEditors();
