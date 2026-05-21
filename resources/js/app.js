import './bootstrap';

// Scroll the active about-subnav tab into view on load (mobile horizontal scroll)
document.addEventListener('DOMContentLoaded', () => {
    const activeTab = document.querySelector('[data-subnav-active]');
    if (activeTab && typeof activeTab.scrollIntoView === 'function') {
        activeTab.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'instant' });
    }
});
