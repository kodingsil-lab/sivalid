<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$selectedIndicator = 0;
$indicatorOptions = [];
$itemLayout = isset($itemLayout) && is_array($itemLayout) ? $itemLayout : instrument_item_entry_layout($selectedInstrument['jenis'] ?? '');
$layoutType = (string) ($itemLayout['type'] ?? 'standard');
?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title"><?= esc($title) ?></h2>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Form <?= esc((string) ($itemLayout['item_label'] ?? 'Butir Pernyataan')) ?></h3>
    </div>
    <div class="card-body">
    <?php if (empty($instruments)): ?>
        <div class="empty-state">
            Belum ada instrumen. Silakan buat data instrumen terlebih dahulu.
        </div>

    <?php elseif (empty($aspects)): ?>
        <div class="empty-state">
            Belum ada aspek untuk instrumen ini. Silakan buat kisi-kisi aspek terlebih dahulu.
            <br><br>
            <a href="<?= base_url('admin/instrument-aspects' . (!empty($instrumentId) ? '?instrument_id=' . $instrumentId : '')) ?>" class="btn btn-primary">
                Tambah Aspek
            </a>
        </div>

    <?php else: ?>
        <?php
        $selectedAspect = (int) old('aspect_id', $item['aspect_id'] ?? 0);
        $selectedIndicator = (int) old('indicator_id', $item['indicator_id'] ?? 0);
        $indicatorOptions = array_map(static function (array $indicator): array {
            return [
                'id'        => (int) ($indicator['id'] ?? 0),
                'aspect_id' => (int) ($indicator['aspect_id'] ?? 0),
                'label'     => trim((string) ($indicator['urutan'] ?? '') . '. ' . (string) ($indicator['indikator'] ?? '')),
            ];
        }, $indicators ?? []);
        ?>

        <form action="<?= esc($action) ?>" method="post">
            <?= csrf_field() ?>

            <?php if ($method === 'put'): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="form-row">
                <label class="form-label" for="instrument_id">Instrumen</label>
                <select name="instrument_id" id="instrument_id" class="form-control" required>
                    <option value="">-- Pilih Instrumen --</option>
                    <?php foreach ($instruments as $instrument): ?>
                        <?php
                        $selectedInstrument = old('instrument_id', $item['instrument_id'] ?? $instrumentId ?? '');
                        ?>
                        <option value="<?= $instrument['id'] ?>" <?= (int) $selectedInstrument === (int) $instrument['id'] ? 'selected' : '' ?>>
                            <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>
                    Untuk tahap sederhana, jika ingin mengganti instrumen, kembali ke daftar butir lalu pilih instrumen.
                </small>
            </div>

            <div class="form-row">
                <label class="form-label" for="aspect_id">Aspek</label>
                <select name="aspect_id" id="aspect_id" class="form-control" required>
                    <option value="">-- Pilih Aspek --</option>
                    <?php foreach ($aspects as $aspect): ?>
                        <option value="<?= $aspect['id'] ?>" <?= $selectedAspect === (int) $aspect['id'] ? 'selected' : '' ?>>
                            <?= esc($aspect['urutan']) ?>. <?= esc($aspect['nama_aspek']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="indicator_id"><?= esc((string) ($itemLayout['indicator_label'] ?? 'Indikator')) ?></label>
                <select name="indicator_id" id="indicator_id" class="form-control">
                    <option value="">-- Tanpa Indikator / Pilih Indikator --</option>

                    <?php foreach ($indicators as $indicator): ?>
                        <?php if ((int) ($indicator['aspect_id'] ?? 0) !== $selectedAspect) {
                            continue;
                        } ?>
                        <option value="<?= $indicator['id'] ?>" <?= $selectedIndicator === (int) $indicator['id'] ? 'selected' : '' ?>>
                            <?= esc($indicator['urutan']) ?>. <?= esc($indicator['indikator']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>
                    Indikator digunakan untuk memastikan butir sesuai dengan kisi-kisi.
                </small>
            </div>

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="nomor">Nomor Butir</label>
                    <input
                        type="number"
                        name="nomor"
                        id="nomor"
                        class="form-control"
                        value="<?= old('nomor', $item['nomor'] ?? $nextNumber ?? 1) ?>"
                        min="1"
                        required
                    >
                </div>

                <div class="form-row">
                    <label class="form-label" for="urutan">Urutan Tampil</label>
                    <input
                        type="number"
                        name="urutan"
                        id="urutan"
                        class="form-control"
                        value="<?= old('urutan', $item['urutan'] ?? $nextNumber ?? 1) ?>"
                        min="1"
                        required
                    >
                </div>
            </div>

            <div class="form-row">
                <label class="form-label" for="pernyataan"><?= esc((string) ($itemLayout['item_label'] ?? 'Butir Pernyataan')) ?></label>
                <textarea
                    name="pernyataan"
                    id="pernyataan"
                    class="form-control"
                    style="min-height: 130px;"
                    placeholder="<?= esc((string) ($itemLayout['item_placeholder'] ?? 'Tuliskan butir pernyataan instrumen.')) ?>"
                    required
                ><?= old('pernyataan', $item['pernyataan'] ?? '') ?></textarea>
            </div>

            <?php if (!empty($itemLayout['show_source_document'])): ?>
            <div class="form-row">
                <label class="form-label" for="sumber_dokumen">Sumber Dokumen</label>
                <input
                    type="text"
                    name="sumber_dokumen"
                    id="sumber_dokumen"
                    class="form-control"
                    value="<?= esc(old('sumber_dokumen', $item['sumber_dokumen'] ?? '')) ?>"
                    maxlength="150"
                    placeholder="Contoh: RPS, bahan ajar, panduan teknis"
                >
                <small class="text-muted">Dipakai pada layout instrumen telaah/dokumentasi. Boleh dikosongkan untuk jenis instrumen lain.</small>
            </div>
            <?php else: ?>
                <input type="hidden" name="sumber_dokumen" value="<?= esc(old('sumber_dokumen', $item['sumber_dokumen'] ?? '')) ?>">
            <?php endif; ?>

            <?php if (!empty($itemLayout['show_rubric_scores'])): ?>
                <div class="form-row">
                    <label class="form-label">Deskripsi Skor Rubrik</label>
                    <div class="form-grid">
                        <?php for ($score = 1; $score <= 5; $score++): ?>
                            <?php $field = 'skor_' . $score . '_deskripsi'; ?>
                            <div class="form-row">
                                <label class="form-label" for="<?= esc($field) ?>">Skor <?= $score ?></label>
                                <textarea
                                    name="<?= esc($field) ?>"
                                    id="<?= esc($field) ?>"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Deskripsi kriteria untuk skor <?= $score ?>"
                                ><?= old($field, $item[$field] ?? '') ?></textarea>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php for ($score = 1; $score <= 5; $score++): ?>
                    <?php $field = 'skor_' . $score . '_deskripsi'; ?>
                    <input type="hidden" name="<?= esc($field) ?>" value="<?= esc(old($field, $item[$field] ?? '')) ?>">
                <?php endfor; ?>
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-row">
                    <label class="form-label" for="tipe_butir">Tipe Butir</label>
                    <select name="tipe_butir" id="tipe_butir" class="form-control" required>
                        <?php
                        $tipeOptions = [
                            'skala'    => 'Skala',
                            'komentar' => 'Komentar',
                            'isian'    => 'Isian',
                            'pilihan'  => 'Pilihan',
                            'catatan'  => 'Catatan',
                        ];

                        $selectedTipe = old('tipe_butir', $item['tipe_butir'] ?? ($itemLayout['default_item_type'] ?? 'skala'));
                        ?>

                        <?php foreach ($tipeOptions as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= $selectedTipe === $value ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label class="form-label" for="wajib">Wajib Diisi</label>
                    <?php $selectedWajib = old('wajib', $item['wajib'] ?? 1); ?>
                    <select name="wajib" id="wajib" class="form-control" required>
                        <option value="1" <?= (int) $selectedWajib === 1 ? 'selected' : '' ?>>Ya</option>
                        <option value="0" <?= (int) $selectedWajib === 0 ? 'selected' : '' ?>>Tidak</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <label class="form-label" for="status">Status Butir</label>
                <?php
                $statusOptions = [
                    'Aktif',
                    'Perlu Revisi',
                    'Direvisi',
                    'Tidak Aktif',
                ];

                $selectedStatus = old('status', $item['status'] ?? 'Aktif');
                ?>

                <select name="status" id="status" class="form-control" required>
                    <?php foreach ($statusOptions as $status): ?>
                        <option value="<?= esc($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>>
                            <?= esc($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/instrument-items' . (!empty($instrumentId) ? '?instrument_id=' . $instrumentId : '')) ?>" class="btn btn-light">
                Kembali
            </a>
        </form>
    <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const aspectSelect = document.getElementById('aspect_id');
    const indicatorSelect = document.getElementById('indicator_id');

    if (!aspectSelect || !indicatorSelect) {
        return;
    }

    const indicators = <?= json_encode($indicatorOptions ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const initialIndicator = '<?= esc((string) $selectedIndicator, 'js') ?>';

    function createOption(value, label, selected) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        option.selected = selected;
        return option;
    }

    function renderIndicators(keepCurrent) {
        const aspectId = parseInt(aspectSelect.value || '0', 10);
        const currentValue = keepCurrent ? indicatorSelect.value : initialIndicator;
        const matchedIndicators = indicators.filter(function (indicator) {
            return indicator.aspect_id === aspectId;
        });

        indicatorSelect.innerHTML = '';
        indicatorSelect.appendChild(createOption('', '-- Tanpa Indikator / Pilih Indikator --', currentValue === ''));

        matchedIndicators.forEach(function (indicator) {
            indicatorSelect.appendChild(createOption(String(indicator.id), indicator.label, String(indicator.id) === String(currentValue)));
        });

        if (!matchedIndicators.some(function (indicator) {
            return String(indicator.id) === String(currentValue);
        })) {
            indicatorSelect.value = '';
        }
    }

    aspectSelect.addEventListener('change', function () {
        renderIndicators(true);
    });

    renderIndicators(false);
});
</script>

<?= $this->endSection() ?>
