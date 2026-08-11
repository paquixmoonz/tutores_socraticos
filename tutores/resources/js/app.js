const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Menú móvil: se pliega y despliega con el accordion de .menu-collapse
document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-menu-toggle]');
    if (!toggle) return;

    const menu = document.querySelector('[data-mobile-menu]');
    if (!menu) return;

    const isOpen = menu.toggleAttribute('data-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
});

// Los avisos de éxito se descartan solos pasados unos segundos
const dismissFlash = (flash) => {
    flash.style.opacity = '0';
    flash.style.transform = 'translateY(-6px)';
    flash.addEventListener('transitionend', () => flash.remove(), { once: true });
    // Red de seguridad por si la transición nunca dispara (pestaña en segundo plano)
    setTimeout(() => flash.remove(), 500);
};

document.addEventListener('click', (event) => {
    const closer = event.target.closest('[data-flash-close]');
    if (closer) dismissFlash(closer.closest('[data-flash]'));
});

document.querySelectorAll('[data-flash]').forEach((flash) => {
    setTimeout(() => dismissFlash(flash), 6000);
});

// Zona de subida punteada: muestra el nombre del archivo elegido
document.addEventListener('change', (event) => {
    const input = event.target;
    if (input.type !== 'file') return;

    const text = input.closest('div')?.querySelector('[data-dropzone-text]');
    const file = input.files?.[0];
    if (!text || !file) return;

    text.textContent = file.name;
    text.classList.add('font-semibold', 'text-unicaes-ink');
});

// Copiar el contenido del agente al portapapeles
const CHECK_ICON =
    '<svg class="size-4 animate-pop-in" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>';

// Escribe en el portapapeles. La API asíncrona solo existe en contexto seguro
// (HTTPS o localhost) y puede quedarse colgada si el documento no tiene el
// foco, así que va acotada por tiempo y con un plan B por selección.
const writeToClipboard = async (text) => {
    if (navigator.clipboard?.writeText) {
        try {
            await Promise.race([
                navigator.clipboard.writeText(text),
                new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 1500)),
            ]);
            return true;
        } catch {
            // Cae al método de respaldo
        }
    }

    const area = document.createElement('textarea');
    area.value = text;
    area.setAttribute('readonly', '');
    area.style.cssText = 'position:fixed;top:0;left:0;opacity:0;pointer-events:none';
    document.body.appendChild(area);
    area.select();

    let copied = false;
    try {
        copied = document.execCommand('copy');
    } catch {
        copied = false;
    }
    area.remove();
    return copied;
};

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-agent]');
    if (!button) return;

    const label = button.querySelector('[data-copy-label]');
    const icon = button.querySelector('[data-copy-icon]');
    if (!label || button.disabled) return;

    const originalLabel = label.textContent;
    const originalIcon = icon?.innerHTML;

    const restore = () => {
        label.textContent = originalLabel;
        if (icon && originalIcon) icon.innerHTML = originalIcon;
        button.classList.remove('is-copied');
        button.disabled = false;
    };

    const fail = () => {
        label.textContent = 'Error al copiar';
        if (icon && originalIcon) icon.innerHTML = originalIcon;
        button.classList.remove('is-copied');
        setTimeout(restore, 2500);
    };

    button.disabled = true;
    label.textContent = 'Copiando…';

    try {
        const response = await fetch(button.dataset.copyAgent, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('No disponible');

        const { content } = await response.json();
        if (!(await writeToClipboard(content))) throw new Error('Portapapeles no disponible');

        label.textContent = 'Copiado';
        if (icon) icon.innerHTML = CHECK_ICON;
        button.classList.add('is-copied');
        setTimeout(restore, 2000);
    } catch {
        fail();
    }
});

// Contador de los KPIs del panel: los dígitos suben hasta su valor final.
// El número definitivo ya está en el HTML, así que sin JS se ve igual de bien.
const runTicker = (el) => {
    const raw = el.textContent.trim();
    const target = Number(raw.replace(/[^\d]/g, ''));
    if (!Number.isFinite(target) || target === 0) return;

    const duration = Math.min(480, 260 + target);
    const start = performance.now();
    const format = (n) => n.toLocaleString('es-SV');

    const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        // Ease-out cúbico: arranca rápido y frena al acercarse al valor final
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = format(Math.round(target * eased));
        if (progress < 1) requestAnimationFrame(tick);
        else el.textContent = raw;
    };

    el.textContent = format(0);
    requestAnimationFrame(tick);
};

if (!prefersReducedMotion()) {
    document.querySelectorAll('[data-ticker]').forEach(runTicker);
}
