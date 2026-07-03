/**
 * Searchable Select - Lightweight vanilla JS autocomplete for <select> elements.
 * Auto-initializes on any <select class="searchable-select">.
 */
(function () {
    function init() {
        document.querySelectorAll('select.searchable-select').forEach(setup);
    }

    function setup(select) {
        if (select.dataset.ssInit) return;
        select.dataset.ssInit = '1';
        select.style.display = 'none';
        select.removeAttribute('required');

        const wrapper = document.createElement('div');
        wrapper.className = 'ss-wrapper relative';
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);

        // Trigger button
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'ss-trigger w-full border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm text-left bg-white text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors';
        trigger.textContent = getSelectedText(select);
        wrapper.appendChild(trigger);

        // Dropdown panel
        const dropdown = document.createElement('div');
        dropdown.className = 'ss-dropdown hidden absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden';
        wrapper.appendChild(dropdown);

        // Search input
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Rechercher...';
        searchInput.className = 'ss-search w-full px-3 py-2 border-b border-slate-200 text-xs focus:outline-none';
        dropdown.appendChild(searchInput);

        // Options list
        const list = document.createElement('div');
        list.className = 'ss-list max-h-60 overflow-y-auto';
        dropdown.appendChild(list);

        let highlighted = -1;

        function getOptions() {
            return Array.from(select.options).map((opt, i) => ({
                value: opt.value,
                text: opt.textContent,
                index: i
            }));
        }

        function render(filter) {
            const query = (filter || '').toLowerCase();
            const options = getOptions();
            const filtered = options.filter(o => o.text.toLowerCase().includes(query));
            list.innerHTML = '';
            highlighted = -1;

            if (filtered.length === 0) {
                list.innerHTML = '<div class="px-3 py-2 text-xs text-slate-400">Aucun resultat</div>';
                return;
            }

            filtered.forEach((opt, i) => {
                const item = document.createElement('div');
                item.className = 'ss-option px-3 py-2 text-xs cursor-pointer transition-colors ' +
                    (opt.value === select.value ? 'bg-primary-50 text-primary-700 font-medium' : 'text-slate-700 hover:bg-slate-50');
                item.textContent = opt.text;
                item.dataset.value = opt.value;
                item.dataset.idx = i;
                item.addEventListener('click', () => pick(opt.value, opt.text));
                item.addEventListener('mouseenter', () => {
                    highlightItem(i);
                });
                list.appendChild(item);
            });
        }

        function highlightItem(idx) {
            const items = list.querySelectorAll('.ss-option');
            items.forEach((el, i) => {
                el.classList.toggle('bg-slate-100', i === idx);
            });
            highlighted = idx;
        }

        function pick(value, text) {
            select.value = value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            trigger.textContent = text || getSelectedText(select);
            close();
        }

        function open() {
            dropdown.classList.remove('hidden');
            searchInput.value = '';
            render('');
            searchInput.focus();
        }

        function close() {
            dropdown.classList.add('hidden');
            highlighted = -1;
        }

        function isOpen() {
            return !dropdown.classList.contains('hidden');
        }

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            isOpen() ? close() : open();
        });

        searchInput.addEventListener('input', () => {
            render(searchInput.value);
        });

        searchInput.addEventListener('keydown', (e) => {
            const items = list.querySelectorAll('.ss-option');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                highlightItem(Math.min(highlighted + 1, items.length - 1));
                if (items[highlighted]) items[highlighted].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                highlightItem(Math.max(highlighted - 1, 0));
                if (items[highlighted]) items[highlighted].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (highlighted >= 0 && items[highlighted]) {
                    items[highlighted].click();
                }
            } else if (e.key === 'Escape') {
                close();
                trigger.focus();
            }
        });

        // Click outside
        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target) && isOpen()) {
                close();
            }
        });

        // Sync if select value changes externally
        select.addEventListener('change', () => {
            trigger.textContent = getSelectedText(select);
        });
    }

    function getSelectedText(select) {
        const opt = select.options[select.selectedIndex];
        return opt ? opt.textContent : '';
    }

    // Initialize on DOMContentLoaded and on any future dynamic additions
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose reinit for dynamic content
    window.SearchableSelect = { init };
})();
