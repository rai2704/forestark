import Quill from 'quill';
import './a11y';

const mountEditors = (root = document) => {
    root.querySelectorAll('textarea[data-html]:not([data-ready])').forEach((area) => {
        area.dataset.ready = '1';
        area.classList.add('hidden');
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
        el.classList.toggle('hidden', el.dataset.langField !== activeLang);
    });
    document.querySelectorAll('[data-lang-tab]').forEach((tab) => {
        const on = tab.dataset.langTab === activeLang;
        tab.classList.toggle('bg-emerald-700', on);
        tab.classList.toggle('text-white', on);
    });
};

document.addEventListener('click', (e) => {
    const tab = e.target.closest('[data-lang-tab]');
    if (tab) {
        activeLang = tab.dataset.langTab;
        applyLang();
        return;
    }

    const add = e.target.closest('[data-repeater-add]');
    if (add) {
        const box = add.closest('[data-repeater]');
        const list = box.querySelector(':scope > [data-repeater-list]');
        const html = box.querySelector(':scope > template').innerHTML.replaceAll('__i__', `n${Date.now()}`);
        list.insertAdjacentHTML('beforeend', html);
        const item = list.lastElementChild;
        applyLang(item);
        mountEditors(item);
        return;
    }

    const item = e.target.closest('[data-repeater-item]');
    if (item) {
        if (e.target.closest('[data-remove]') && confirm('Remover este item?')) {
            item.remove();
        } else if (e.target.closest('[data-up]') && item.previousElementSibling) {
            item.previousElementSibling.before(item);
        } else if (e.target.closest('[data-down]') && item.nextElementSibling) {
            item.nextElementSibling.after(item);
        }
    }

    const clear = e.target.closest('[data-clear-media]');
    if (clear) {
        const box = clear.closest('[data-media]');
        box.querySelector('input[type=hidden]').value = '';
        box.querySelector('[data-preview]')?.remove();
        clear.remove();
    }
});

document.addEventListener('submit', (e) => {
    const message = e.target.dataset?.confirm;
    if (message && !confirm(message)) {
        e.preventDefault();
    }
});

applyLang();
mountEditors();
