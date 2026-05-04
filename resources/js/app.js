import './bootstrap';

// ═══════════════════════════════════════════════════════════════
// REW Supply Chain — Global JS Enhancements
// ═══════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {

    // ── Keyboard Shortcuts ──────────────────────────────────────
    document.addEventListener('keydown', (e) => {
        // Focus global search: Ctrl+K / Cmd+K
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const search = document.getElementById('global-search');
            if (search) { search.focus(); search.select(); }
        }

        // Alt+F → Forecast page
        if (e.altKey && e.key === 'f') {
            e.preventDefault();
            const link = document.querySelector('a[href*="forecast"]');
            if (link) link.click();
        }

        // Alt+P → Products list
        if (e.altKey && e.key === 'p') {
            e.preventDefault();
            const link = document.querySelector('a[href*="products"]');
            if (link) link.click();
        }

        // Alt+S → Record Sale
        if (e.altKey && e.key === 's') {
            e.preventDefault();
            const link = document.querySelector('a[href*="sales"]');
            if (link) link.click();
        }

        // Alt+A → Alerts
        if (e.altKey && e.key === 'a') {
            e.preventDefault();
            const link = document.querySelector('a[href*="alerts"]');
            if (link) link.click();
        }

        // Escape → close any open Swal modal
        if (e.key === 'Escape' && typeof Swal !== 'undefined') {
            if (Swal.isVisible()) Swal.close();
        }
    });

    // ── Auto-dismiss flash messages after 5s ───────────────────
    document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
        const ms = parseInt(el.dataset.autoDismiss) || 5000;
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, ms);
    });

    // ── Loading state helper: attach to all wire:click submit forms ─
    // Adds disabled state to form submit buttons while Livewire is loading
    document.addEventListener('livewire:request', () => {
        document.querySelectorAll('[data-loading-disable]').forEach(btn => {
            btn.disabled = true;
            btn.setAttribute('data-original-text', btn.innerHTML);
            btn.innerHTML = `<span class="btn-spinner mr-2"></span> Processing…`;
        });
    });
    document.addEventListener('livewire:response', () => {
        document.querySelectorAll('[data-loading-disable]').forEach(btn => {
            btn.disabled = false;
            const orig = btn.getAttribute('data-original-text');
            if (orig) btn.innerHTML = orig;
        });
    });

    // ── Table row click → navigate to detail if data-href present ─
    document.addEventListener('click', (e) => {
        const row = e.target.closest('tr[data-href]');
        if (row) window.location.href = row.dataset.href;
    });

});
