/**
 * SIVALID - Custom JavaScript
 * Tambahan interaksi ringan di atas Tabler UI.
 */

document.addEventListener('DOMContentLoaded', function () {
    var nativeAlert = window.alert.bind(window);
    var nativeConfirm = window.confirm.bind(window);

    // Auto-dismiss alert setelah 5 detik
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        }, 5000);
    });

    var tablerDialog = createTablerDialog(nativeAlert, nativeConfirm);

    // Tangkap konfirmasi inline: onsubmit="return confirm('...')"
    document.addEventListener('submit', function (event) {
        var form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        var handler = form.getAttribute('onsubmit') || '';
        var message = extractConfirmMessage(handler);

        if (!message) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        tablerDialog.confirm({
            title: 'Konfirmasi',
            message: message,
            okText: 'Ya, lanjutkan',
            cancelText: 'Batal',
            danger: true,
        }).then(function (confirmed) {
            if (!confirmed) {
                return;
            }

            var original = form.getAttribute('onsubmit');
            form.removeAttribute('onsubmit');
            form.submit();

            if (original !== null) {
                form.setAttribute('onsubmit', original);
            }
        });
    }, true);

    // Tangkap konfirmasi inline: onclick="return confirm('...')"
    document.addEventListener('click', function (event) {
        var trigger = event.target instanceof Element
            ? event.target.closest('[onclick]')
            : null;

        if (!trigger) {
            return;
        }

        var handler = trigger.getAttribute('onclick') || '';
        var message = extractConfirmMessage(handler);

        if (!message) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        tablerDialog.confirm({
            title: 'Konfirmasi',
            message: message,
            okText: 'Ya, lanjutkan',
            cancelText: 'Batal',
            danger: true,
        }).then(function (confirmed) {
            if (!confirmed) {
                return;
            }

            var original = trigger.getAttribute('onclick');
            trigger.removeAttribute('onclick');

            if (trigger instanceof HTMLAnchorElement && trigger.href) {
                window.location.href = trigger.href;
            } else if (trigger instanceof HTMLButtonElement && trigger.type === 'submit' && trigger.form) {
                trigger.form.submit();
            } else {
                trigger.click();
            }

            if (original !== null) {
                trigger.setAttribute('onclick', original);
            }
        });
    }, true);

    // Alert global gaya Tabler
    window.alert = function (message) {
        if (!window.bootstrap || !document.getElementById('sv-global-dialog')) {
            nativeAlert(message);
            return;
        }

        tablerDialog.alert({
            title: 'Informasi',
            message: String(message || ''),
            okText: 'Tutup',
        });
    };
});

function extractConfirmMessage(handler) {
    if (typeof handler !== 'string' || handler.indexOf('confirm') === -1) {
        return null;
    }

    var match = handler.match(/confirm\s*\(\s*(["'`])([\s\S]*?)\1\s*\)/i);

    if (!match || !match[2]) {
        return null;
    }

    return match[2]
        .replace(/\\n/g, '\n')
        .replace(/\\'/g, "'")
        .replace(/\\"/g, '"')
        .trim();
}

function createTablerDialog(nativeAlert, nativeConfirm) {
    if (!window.bootstrap || !window.bootstrap.Modal) {
        return {
            confirm: function (options) {
                return Promise.resolve(nativeConfirm(options && options.message ? options.message : 'Lanjutkan?'));
            },
            alert: function (options) {
                nativeAlert(options && options.message ? options.message : '');
                return Promise.resolve(true);
            },
        };
    }

    var modalEl = document.createElement('div');
    modalEl.className = 'modal modal-blur fade';
    modalEl.id = 'sv-global-dialog';
    modalEl.tabIndex = -1;
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.innerHTML = '' +
        '<div class="modal-dialog modal-dialog-centered" role="document">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<h5 class="modal-title" id="sv-global-dialog-title">Konfirmasi</h5>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                '</div>' +
                '<div class="modal-body" id="sv-global-dialog-message"></div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-light" data-role="cancel">Batal</button>' +
                    '<button type="button" class="btn btn-primary" data-role="ok">OK</button>' +
                '</div>' +
            '</div>' +
        '</div>';

    document.body.appendChild(modalEl);

    var titleEl = modalEl.querySelector('#sv-global-dialog-title');
    var messageEl = modalEl.querySelector('#sv-global-dialog-message');
    var okBtn = modalEl.querySelector('[data-role="ok"]');
    var cancelBtn = modalEl.querySelector('[data-role="cancel"]');
    var modal = new window.bootstrap.Modal(modalEl, {
        backdrop: 'static',
        keyboard: false,
    });

    function openDialog(options) {
        options = options || {};

        titleEl.textContent = options.title || 'Konfirmasi';
        messageEl.textContent = options.message || 'Lanjutkan aksi ini?';
        okBtn.textContent = options.okText || 'OK';
        cancelBtn.textContent = options.cancelText || 'Batal';
        cancelBtn.style.display = options.showCancel === false ? 'none' : '';
        okBtn.className = options.danger
            ? 'btn btn-danger'
            : 'btn btn-primary';

        return new Promise(function (resolve) {
            var resolved = false;

            function cleanup() {
                okBtn.removeEventListener('click', onOk);
                cancelBtn.removeEventListener('click', onCancel);
                modalEl.removeEventListener('hidden.bs.modal', onHidden);
            }

            function finish(value) {
                if (resolved) {
                    return;
                }
                resolved = true;
                cleanup();
                resolve(value);
            }

            function onOk() {
                finish(true);
                modal.hide();
            }

            function onCancel() {
                finish(false);
                modal.hide();
            }

            function onHidden() {
                finish(false);
            }

            okBtn.addEventListener('click', onOk);
            cancelBtn.addEventListener('click', onCancel);
            modalEl.addEventListener('hidden.bs.modal', onHidden);

            modal.show();
        });
    }

    return {
        confirm: function (options) {
            return openDialog(options);
        },
        alert: function (options) {
            options = options || {};
            options.showCancel = false;
            return openDialog(options);
        },
    };
}
