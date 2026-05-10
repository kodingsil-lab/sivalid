<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Kisi-Kisi Instrumen</h2>
            <div class="text-muted mt-1">Kelola aspek, indikator, dan struktur kisi-kisi untuk instrumen terpilih.</div>
        </div>
        <?php if (!empty($instrumentId)): ?>
            <?php $canAddIndicator = !empty($aspects); ?>
            <div class="col-auto ms-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAspek">
                    + Tambah Aspek
                </button>

                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalImportKisi">
                    Import Excel
                </button>

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahIndikator"
                    <?= $canAddIndicator ? '' : 'disabled' ?>
                    title="<?= $canAddIndicator ? '' : 'Buat aspek terlebih dahulu.' ?>"
                >
                    + Tambah Indikator
                </button>

                <a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrumentId) ?>" class="btn btn-light">
                    Kelola Butir
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-info">
        <?= esc(session()->getFlashdata('info')) ?>
    </div>
<?php endif; ?>

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
    <div class="card-body">
    <form action="<?= base_url('admin/instrument-aspects') ?>" method="get" class="search-form search-form-wide">
        <select name="instrument_id" class="form-control" style="min-width: 420px;">
            <option value="">-- Pilih Instrumen --</option>
            <?php foreach ($instruments as $instrument): ?>
                <option value="<?= $instrument['id'] ?>" <?= (int) ($instrumentId ?? 0) === (int) $instrument['id'] ? 'selected' : '' ?>>
                    <?= esc($instrument['kode']) ?> - <?= esc($instrument['judul']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>
    </div>
</div>

<?php if (empty($instrumentId)): ?>
    <div class="empty-state">
        Silakan pilih instrumen terlebih dahulu untuk menampilkan kisi-kisi.
    </div>
<?php else: ?>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Daftar Aspek Instrumen</h3>
        </div>
        <div class="card-body p-0">

        <?php if (empty($aspects)): ?>
            <div class="empty-state">
                Belum ada aspek pada instrumen ini.
            </div>
        <?php else: ?>
            <?php
            $aspectRows = isset($aspectList) && is_array($aspectList) ? $aspectList : $aspects;
            $currentPage = isset($pager) ? $pager->getCurrentPage($pagerGroup) : 1;
            $perPage = isset($pager) ? $pager->getPerPage($pagerGroup) : 0;
            $total = isset($pager) ? $pager->getTotal($pagerGroup) : count($aspectRows);
            $offset = $perPage > 0 ? (($currentPage - 1) * $perPage) : 0;
            $firstItem = $total > 0 && $perPage > 0 ? $offset + 1 : 0;
            $lastItem = $total > 0 && $perPage > 0 ? min($currentPage * $perPage, $total) : $total;
            ?>
            <div class="table-responsive">
            <table class="table table-vcenter table-sm table-no-hover">
                <thead>
                    <tr>
                        <th style="width: 70px;">Urutan</th>
                        <th>Aspek</th>
                        <th>Deskripsi</th>
                        <th class="table-actions-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aspectRows as $aspect): ?>
                        <tr>
                            <td><?= esc($aspect['urutan']) ?></td>
                            <td><?= esc($aspect['nama_aspek']) ?></td>
                            <td><?= nl2br(esc($aspect['deskripsi'] ?: '-')) ?></td>
                            <td class="table-actions-cell">
                                <div class="table-actions">
                                    <button
                                        type="button"
                                        class="btn btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditAspek<?= (int) $aspect['id'] ?>"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapusAspek<?= (int) $aspect['id'] ?>"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php if (isset($pager) && !empty($pagerGroup)): ?>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 px-3 py-3 border-top">
                    <div class="text-muted small">
                        Menampilkan <?= esc((string) $firstItem) ?> sampai <?= esc((string) $lastItem) ?> dari <?= esc((string) $total) ?> entri
                    </div>
                    <div><?= $pager->links($pagerGroup, 'default_full') ?></div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Tampilan Kisi-Kisi Instrumen</h3>
        </div>
        <div class="card-body p-0">

        <?php if (empty($aspects)): ?>
            <div class="empty-state">
                Kisi-kisi belum dapat ditampilkan karena aspek belum dibuat.
            </div>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-vcenter table-sm table-no-hover">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th style="width: 240px;">Aspek</th>
                        <th>Indikator</th>
                        <th class="table-actions-cell">Aksi Indikator</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aspects as $aspectIndex => $aspect): ?>
                        <?php
                        $aspectIndicators = array_values(array_filter($indicators, function ($indicator) use ($aspect) {
                            return (int) $indicator['aspect_id'] === (int) $aspect['id'];
                        }));
                        ?>

                        <?php if (empty($aspectIndicators)): ?>
                            <tr>
                                <td><?= $aspectIndex + 1 ?></td>
                                <td><?= esc($aspect['nama_aspek']) ?></td>
                                <td><em>Belum ada indikator.</em></td>
                                <td class="table-actions-cell">
                                    <div class="table-actions">
                                        <button
                                            type="button"
                                            class="btn btn-light js-open-indicator-modal"
                                            data-aspect-id="<?= (int) $aspect['id'] ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTambahIndikator"
                                        >
                                            Tambah
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($aspectIndicators as $indicatorIndex => $indicator): ?>
                                <tr>
                                    <?php if ($indicatorIndex === 0): ?>
                                        <td rowspan="<?= count($aspectIndicators) ?>"><?= $aspectIndex + 1 ?></td>
                                        <td rowspan="<?= count($aspectIndicators) ?>"><?= esc($aspect['nama_aspek']) ?></td>
                                    <?php endif; ?>

                                    <td>
                                        <?= esc($indicator['urutan']) ?>.
                                        <?= nl2br(esc($indicator['indikator'])) ?>
                                    </td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions">
                                            <button
                                                type="button"
                                                class="btn btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditIndikator<?= (int) $indicator['id'] ?>"
                                            >
                                                Edit
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapusIndikator<?= (int) $indicator['id'] ?>"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<?php if (!empty($instrumentId)): ?>
    <?php
    $nextAspectOrder = 1;
    foreach ($aspects as $aspect) {
        $nextAspectOrder = max($nextAspectOrder, (int) ($aspect['urutan'] ?? 0) + 1);
    }

    $nextIndicatorOrders = [];
    foreach ($aspects as $aspect) {
        $nextIndicatorOrders[(int) $aspect['id']] = 1;
    }
    foreach ($indicators as $indicator) {
        $aspectId = (int) ($indicator['aspect_id'] ?? 0);
        if ($aspectId > 0) {
            $nextIndicatorOrders[$aspectId] = max($nextIndicatorOrders[$aspectId] ?? 1, (int) ($indicator['urutan'] ?? 0) + 1);
        }
    }
    ?>

    <div class="modal modal-blur fade" id="modalTambahAspek" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aspek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('admin/instrument-aspects') ?>" method="post">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="instrument_id" value="<?= (int) $instrumentId ?>">
                        <input type="hidden" name="modal_target" value="aspect">

                        <div class="mb-3">
                            <label class="form-label">Instrumen</label>
                            <div class="form-control bg-light">
                                <?php
                                $selectedInstrument = null;
                                foreach ($instruments as $instrument) {
                                    if ((int) $instrument['id'] === (int) $instrumentId) {
                                        $selectedInstrument = $instrument;
                                        break;
                                    }
                                }
                                ?>
                                <?= esc(($selectedInstrument['kode'] ?? '-') . ' - ' . ($selectedInstrument['judul'] ?? '-')) ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="modal_nama_aspek">Nama Aspek</label>
                            <input
                                type="text"
                                name="nama_aspek"
                                id="modal_nama_aspek"
                                class="form-control"
                                value="<?= old('modal_target') === 'aspect' ? esc(old('nama_aspek')) : '' ?>"
                                placeholder="Contoh: Kelayakan Isi Materi"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="modal_urutan_aspek">Urutan</label>
                            <input
                                type="number"
                                name="urutan"
                                id="modal_urutan_aspek"
                                class="form-control"
                                value="<?= old('modal_target') === 'aspect' ? esc(old('urutan', (string) $nextAspectOrder)) : esc((string) $nextAspectOrder) ?>"
                                min="1"
                                required
                            >
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="modal_deskripsi_aspek">Deskripsi Aspek</label>
                            <textarea
                                name="deskripsi"
                                id="modal_deskripsi_aspek"
                                class="form-control"
                                placeholder="Tuliskan deskripsi aspek jika diperlukan."
                            ><?= old('modal_target') === 'aspect' ? esc(old('deskripsi')) : '' ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Aspek</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modalImportKisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Kisi-Kisi dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('admin/instrument-aspects/import') ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="instrument_id" value="<?= (int) $instrumentId ?>">

                        <div class="mb-3">
                            <label class="form-label">File Excel (.xlsx)</label>
                            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                        </div>

                        <div class="mb-3">
                            <a href="<?= base_url('admin/instrument-aspects/import-template') ?>" class="btn btn-light">
                                Download Template Excel
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No Aspek</th>
                                        <th>Aspek</th>
                                        <th>Deskripsi Aspek</th>
                                        <th>No Indikator</th>
                                        <th>Indikator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Pendahuluan</td>
                                        <td>-</td>
                                        <td>1</td>
                                        <td>Kejelasan latar belakang dan urgensi pengembangan model pembelajaran.</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Pendahuluan</td>
                                        <td>-</td>
                                        <td>2</td>
                                        <td>Kesesuaian tujuan model pembelajaran dengan kebutuhan pembelajaran.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-muted small mt-2">
                            Aspek yang sudah ada akan digabung berdasarkan nama aspek. Indikator yang sama pada aspek yang sama akan dilewati.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modalTambahIndikator" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Indikator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('admin/instrument-indicators') ?>" method="post">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="instrument_id" value="<?= (int) $instrumentId ?>">
                        <input type="hidden" name="modal_target" value="indicator">

                        <div class="mb-3">
                            <label class="form-label">Instrumen</label>
                            <div class="form-control bg-light">
                                <?= esc(($selectedInstrument['kode'] ?? '-') . ' - ' . ($selectedInstrument['judul'] ?? '-')) ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="modal_aspect_id">Aspek</label>
                            <select name="aspect_id" id="modal_aspect_id" class="form-control" required>
                                <option value="">-- Pilih Aspek --</option>
                                <?php $selectedAspect = old('modal_target') === 'indicator' ? (int) old('aspect_id', 0) : 0; ?>
                                <?php foreach ($aspects as $aspect): ?>
                                    <option
                                        value="<?= (int) $aspect['id'] ?>"
                                        data-next-order="<?= (int) ($nextIndicatorOrders[(int) $aspect['id']] ?? 1) ?>"
                                        <?= $selectedAspect === (int) $aspect['id'] ? 'selected' : '' ?>
                                    >
                                        <?= esc((string) $aspect['urutan']) ?>. <?= esc((string) $aspect['nama_aspek']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="modal_indikator">Indikator</label>
                            <textarea
                                name="indikator"
                                id="modal_indikator"
                                class="form-control"
                                placeholder="Contoh: Kesesuaian isi instrumen dengan aspek yang diukur."
                                required
                            ><?= old('modal_target') === 'indicator' ? esc(old('indikator')) : '' ?></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="modal_urutan_indikator">Urutan</label>
                            <input
                                type="number"
                                name="urutan"
                                id="modal_urutan_indikator"
                                class="form-control"
                                value="<?= old('modal_target') === 'indicator' ? esc(old('urutan', (string) ($nextIndicatorOrders[$selectedAspect] ?? 1))) : esc((string) ($nextIndicatorOrders[$selectedAspect] ?? 1)) ?>"
                                min="1"
                                required
                            >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" <?= empty($aspects) ? 'disabled' : '' ?>>Simpan Indikator</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php foreach ($aspects as $aspect): ?>
        <div class="modal modal-blur fade" id="modalEditAspek<?= (int) $aspect['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Aspek</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= base_url('admin/instrument-aspects/' . (int) $aspect['id']) ?>" method="post">
                        <div class="modal-body">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="instrument_id" value="<?= (int) $instrumentId ?>">
                            <input type="hidden" name="modal_target" value="aspect_edit_<?= (int) $aspect['id'] ?>">

                            <?php $isAspectOld = old('modal_target') === 'aspect_edit_' . (int) $aspect['id']; ?>

                            <div class="mb-3">
                                <label class="form-label">Instrumen</label>
                                <div class="form-control bg-light">
                                    <?= esc(($selectedInstrument['kode'] ?? '-') . ' - ' . ($selectedInstrument['judul'] ?? '-')) ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="modal_edit_nama_aspek_<?= (int) $aspect['id'] ?>">Nama Aspek</label>
                                <input
                                    type="text"
                                    name="nama_aspek"
                                    id="modal_edit_nama_aspek_<?= (int) $aspect['id'] ?>"
                                    class="form-control"
                                    value="<?= $isAspectOld ? esc(old('nama_aspek')) : esc((string) ($aspect['nama_aspek'] ?? '')) ?>"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="modal_edit_urutan_aspek_<?= (int) $aspect['id'] ?>">Urutan</label>
                                <input
                                    type="number"
                                    name="urutan"
                                    id="modal_edit_urutan_aspek_<?= (int) $aspect['id'] ?>"
                                    class="form-control"
                                    value="<?= $isAspectOld ? esc(old('urutan', '1')) : esc((string) ($aspect['urutan'] ?? '1')) ?>"
                                    min="1"
                                    required
                                >
                            </div>

                            <div class="mb-0">
                                <label class="form-label" for="modal_edit_deskripsi_aspek_<?= (int) $aspect['id'] ?>">Deskripsi Aspek</label>
                                <textarea
                                    name="deskripsi"
                                    id="modal_edit_deskripsi_aspek_<?= (int) $aspect['id'] ?>"
                                    class="form-control"
                                    placeholder="Tuliskan deskripsi aspek jika diperlukan."
                                ><?= $isAspectOld ? esc(old('deskripsi')) : esc((string) ($aspect['deskripsi'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($indicators as $indicator): ?>
        <div class="modal modal-blur fade" id="modalEditIndikator<?= (int) $indicator['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Indikator</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= base_url('admin/instrument-indicators/' . (int) $indicator['id']) ?>" method="post">
                        <div class="modal-body">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="instrument_id" value="<?= (int) $instrumentId ?>">
                            <input type="hidden" name="modal_target" value="indicator_edit_<?= (int) $indicator['id'] ?>">

                            <?php $isIndicatorOld = old('modal_target') === 'indicator_edit_' . (int) $indicator['id']; ?>

                            <div class="mb-3">
                                <label class="form-label">Instrumen</label>
                                <div class="form-control bg-light">
                                    <?= esc(($selectedInstrument['kode'] ?? '-') . ' - ' . ($selectedInstrument['judul'] ?? '-')) ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="modal_edit_aspect_id_<?= (int) $indicator['id'] ?>">Aspek</label>
                                <select
                                    name="aspect_id"
                                    id="modal_edit_aspect_id_<?= (int) $indicator['id'] ?>"
                                    class="form-control"
                                    required
                                >
                                    <option value="">-- Pilih Aspek --</option>
                                    <?php $selectedAspectId = $isIndicatorOld ? (int) old('aspect_id', 0) : (int) ($indicator['aspect_id'] ?? 0); ?>
                                    <?php foreach ($aspects as $aspectOption): ?>
                                        <option value="<?= (int) $aspectOption['id'] ?>" <?= $selectedAspectId === (int) $aspectOption['id'] ? 'selected' : '' ?>>
                                            <?= esc((string) $aspectOption['urutan']) ?>. <?= esc((string) $aspectOption['nama_aspek']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="modal_edit_indikator_<?= (int) $indicator['id'] ?>">Indikator</label>
                                <textarea
                                    name="indikator"
                                    id="modal_edit_indikator_<?= (int) $indicator['id'] ?>"
                                    class="form-control"
                                    required
                                ><?= $isIndicatorOld ? esc(old('indikator')) : esc((string) ($indicator['indikator'] ?? '')) ?></textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label" for="modal_edit_urutan_indikator_<?= (int) $indicator['id'] ?>">Urutan</label>
                                <input
                                    type="number"
                                    name="urutan"
                                    id="modal_edit_urutan_indikator_<?= (int) $indicator['id'] ?>"
                                    class="form-control"
                                    value="<?= $isIndicatorOld ? esc(old('urutan', '1')) : esc((string) ($indicator['urutan'] ?? '1')) ?>"
                                    min="1"
                                    required
                                >
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($aspects as $aspect): ?>
        <div class="modal modal-blur fade" id="modalHapusAspek<?= (int) $aspect['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Aspek</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Yakin ingin menghapus aspek berikut?</p>
                        <div class="fw-semibold"><?= esc((string) ($aspect['nama_aspek'] ?? '-')) ?></div>
                        <div class="text-muted small mt-1">Semua indikator di bawah aspek ini juga akan terhapus.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <form action="<?= base_url('admin/instrument-aspects/' . (int) $aspect['id']) ?>" method="post" class="action-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($indicators as $indicator): ?>
        <div class="modal modal-blur fade" id="modalHapusIndikator<?= (int) $indicator['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Indikator</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Yakin ingin menghapus indikator ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <form action="<?= base_url('admin/instrument-indicators/' . (int) $indicator['id']) ?>" method="post" class="action-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var indicatorAspectSelect = document.getElementById('modal_aspect_id');
            var indicatorOrderInput = document.getElementById('modal_urutan_indikator');
            var openIndicatorButtons = document.querySelectorAll('.js-open-indicator-modal');
            var nextIndicatorOrders = <?= json_encode($nextIndicatorOrders, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

            function setIndicatorOrder(aspectId) {
                if (!indicatorOrderInput) {
                    return;
                }

                indicatorOrderInput.value = nextIndicatorOrders[aspectId] || 1;
            }

            if (indicatorAspectSelect) {
                indicatorAspectSelect.addEventListener('change', function () {
                    setIndicatorOrder(indicatorAspectSelect.value);
                });
            }

            openIndicatorButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var aspectId = button.getAttribute('data-aspect-id');

                    if (indicatorAspectSelect && aspectId) {
                        indicatorAspectSelect.value = aspectId;
                        setIndicatorOrder(aspectId);
                    }
                });
            });

            var openModalTarget = '<?= esc((string) old('modal_target'), 'js') ?>';

            if (openModalTarget === 'aspect') {
                var aspectModalElement = document.getElementById('modalTambahAspek');

                if (aspectModalElement && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(aspectModalElement).show();
                }
            }

            if (openModalTarget === 'indicator') {
                var indicatorModalElement = document.getElementById('modalTambahIndikator');

                if (indicatorModalElement && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(indicatorModalElement).show();
                }
            }

            if (openModalTarget.indexOf('aspect_edit_') === 0) {
                var aspectEditModalElement = document.getElementById('modalEditAspek' + openModalTarget.replace('aspect_edit_', ''));

                if (aspectEditModalElement && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(aspectEditModalElement).show();
                }
            }

            if (openModalTarget.indexOf('indicator_edit_') === 0) {
                var indicatorEditModalElement = document.getElementById('modalEditIndikator' + openModalTarget.replace('indicator_edit_', ''));

                if (indicatorEditModalElement && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(indicatorEditModalElement).show();
                }
            }
        });
    </script>
<?php endif; ?>

<?= $this->endSection() ?>
