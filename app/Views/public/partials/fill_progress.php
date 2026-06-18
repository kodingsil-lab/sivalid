<div class="fill-progress" data-fill-progress>
    <div class="fill-progress-head">
        <span>Progress Pengisian</span>
        <strong data-fill-progress-text>0%</strong>
    </div>
    <div class="fill-progress-track">
        <div class="fill-progress-bar" data-fill-progress-bar></div>
    </div>
</div>

<style>
    .fill-progress {
        border: 1px solid #d7e3ef;
        border-radius: 8px;
        background: #f8fafc;
        padding: .75rem .85rem;
        margin: .65rem 0 1rem;
    }

    .fill-progress-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        color: #1f2a3d;
        font-size: .95rem;
        font-weight: 650;
        margin-bottom: .45rem;
    }

    .fill-progress-track {
        height: 10px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .fill-progress-bar {
        width: 0%;
        height: 100%;
        background: #22c55e;
        transition: width .18s ease;
    }

    tr.instrument-item-row.is-answered > td {
        background: #ecfdf3;
    }

    tr.instrument-item-row.is-answered .item-number,
    tr.instrument-item-row.is-answered td:first-child {
        color: #166534;
        font-weight: 700;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var rows = Array.prototype.slice.call(document.querySelectorAll('.instrument-item-row'));
        var progressText = document.querySelector('[data-fill-progress-text]');
        var progressBar = document.querySelector('[data-fill-progress-bar]');

        if (!rows.length || !progressText || !progressBar) {
            return;
        }

        function isRowAnswered(row) {
            var checked = row.querySelector('input[type="radio"][name^="answers["]:checked');
            if (checked) {
                return true;
            }

            var fields = Array.prototype.slice.call(row.querySelectorAll('textarea[name^="answers["][name$="[jawaban_teks]"], input[type="text"][name^="answers["][name$="[jawaban_teks]"]'));
            return fields.some(function (field) {
                return field.value.trim() !== '';
            });
        }

        function updateProgress() {
            var answered = 0;

            rows.forEach(function (row) {
                var done = isRowAnswered(row);
                row.classList.toggle('is-answered', done);
                if (done) {
                    answered++;
                }
            });

            var percent = rows.length ? Math.round((answered / rows.length) * 100) : 0;
            progressText.textContent = answered + '/' + rows.length + ' (' + percent + '%)';
            progressBar.style.width = percent + '%';
        }

        rows.forEach(function (row) {
            row.addEventListener('change', updateProgress);
            row.addEventListener('input', updateProgress);
        });

        updateProgress();
    });
</script>
