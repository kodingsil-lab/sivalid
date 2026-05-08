/**
 * SIVALID - Custom JavaScript
 * Tambahan interaksi ringan di atas Tabler UI.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss alert setelah 5 detik
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        }, 5000);
    });
});
