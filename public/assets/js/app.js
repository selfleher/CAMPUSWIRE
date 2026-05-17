/**
 * CampusWire — Client-side JavaScript
 * Handles: flash dismissal, modals, reply toggles, mobile menu, likes
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Flash message auto-dismiss ──────────────────
    document.querySelectorAll('.flash').forEach(function (f) {
        setTimeout(function () {
            f.style.animation = 'fadeOut 0.4s ease forwards';
            setTimeout(() => f.remove(), 400);
        }, 4000);
    });

    // ── Mobile menu toggle ──────────────────────────
    const menuBtn = document.getElementById('mobileMenuBtn');
    const drawer  = document.getElementById('mobileDrawer');
    if (menuBtn && drawer) {
        menuBtn.addEventListener('click', function () {
            drawer.classList.toggle('open');
        });
    }

    // ── Close modal on backdrop click ───────────────
    document.querySelectorAll('.cw-modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    // ── ESC key closes any open modal ───────────────
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.cw-modal-overlay').forEach(function (o) {
                if (o.style.display !== 'none') {
                    o.style.display = 'none';
                    document.body.style.overflow = '';
                }
            });
        }
    });

    // ── Auto-scroll to anchor hash (reply links) ────
    if (window.location.hash) {
        const el = document.querySelector(window.location.hash);
        if (el) {
            setTimeout(() => {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                el.style.transition = 'box-shadow 0.5s ease';
                el.style.boxShadow = '0 0 0 3px rgba(79,70,229,0.4)';
                setTimeout(() => { el.style.boxShadow = ''; }, 2000);
            }, 300);
        }
    }

    // ── Smooth hover on news cards ──────────────────
    document.querySelectorAll('.news-card').forEach(function (card) {
        card.addEventListener('mouseenter', function () {
            this.style.boxShadow = '0 8px 24px rgba(0,0,0,0.1)';
        });
        card.addEventListener('mouseleave', function () {
            this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.05)';
        });
    });

    // ── Character counter for textareas ─────────────
    document.querySelectorAll('textarea[maxlength]').forEach(function (ta) {
        const max = parseInt(ta.getAttribute('maxlength'));
        const counter = document.createElement('div');
        counter.style.cssText = 'font-size:12px;color:#9CA3AF;text-align:right;margin-top:4px;';
        counter.textContent = `0 / ${max}`;
        ta.parentNode.insertBefore(counter, ta.nextSibling);
        ta.addEventListener('input', () => {
            counter.textContent = `${ta.value.length} / ${max}`;
            counter.style.color = ta.value.length > max * 0.9 ? '#EF4444' : '#9CA3AF';
        });
    });

    // ── Form submission loading state ───────────────
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.style.opacity = '0.7';
                const original = btn.innerHTML;
                btn.innerHTML = '⏳ ' + btn.textContent.trim();
                // Re-enable after 5 seconds as safety net
                setTimeout(() => {
                    btn.disabled = false;
                    btn.style.opacity = '';
                    btn.innerHTML = original;
                }, 5000);
            }
        });
    });

});
