<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Instrumen Valid</h2>
            <div class="text-muted mt-1">
                Daftar instrumen master yang sudah direvisi dan ditetapkan valid secara manual.
            </div>
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPilihMaster">
                Pilih dari Master
            </button>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc((string) session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc((string) session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (empty($instruments)): ?>
    <div class="empty-state">
        Belum ada instrumen valid. Pilih instrumen dari master setelah butirnya selesai direvisi.
    </div>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-vcenter table-hover table-sm">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Kode</th>
                <th>Judul Instrumen</th>
                <th>Jenis</th>
                <th>Sasaran</th>
                <th>Skala</th>
                <th>Status</th>
                <th class="table-actions-cell">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($instruments as $index => $instrument): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($instrument['kode']) ?></td>
                    <td><?= esc($instrument['judul']) ?></td>
                    <td><?= esc(title_case_label((string) ($instrument['jenis'] ?? '-'))) ?></td>
                    <td><?= esc($instrument['sasaran'] ?: '-') ?></td>
                    <td><?= esc($instrument['skala_min']) ?> - <?= esc($instrument['skala_max']) ?></td>
                    <td>
                        <span class="<?= esc(status_badge_class($instrument['status'] ?? '')) ?>"><?= esc(status_display_label((string) ($instrument['status'] ?? ''))) ?></span>
                    </td>
                    <td class="table-actions-cell">
                        <div class="table-actions">
                            <a href="<?= base_url('admin/instruments/' . $instrument['instrument_id']) ?>" class="btn btn-light">
                                Detail
                            </a>

                            <a href="<?= base_url('admin/instrument-items?instrument_id=' . $instrument['instrument_id']) ?>" class="btn btn-light">
                                Butir
                            </a>

                            <form
                                action="<?= base_url('admin/instrumen-valid/' . $instrument['id']) ?>"
                                method="post"
                                class="action-inline"
                                onsubmit="return confirm('Hapus instrumen ini dari daftar Instrumen Valid? Data master instrumen tidak akan dihapus.')"
                            >
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<div class="modal fade" id="modalPilihMaster" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/instrumen-valid/pilih-master') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Pilih Instrumen dari Master</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($masterInstruments)): ?>
                    <div class="empty-state mb-0">
                        Semua master instrumen sudah masuk daftar Instrumen Valid.
                    </div>
                <?php else: ?>
                    <div class="form-row mb-0">
                        <label class="form-label">Master Instrumen</label>
                        <div class="master-choice-list">
                            <?php foreach ($masterInstruments as $master): ?>
                                <label class="master-choice">
                                    <input
                                        type="radio"
                                        name="instrument_id"
                                        value="<?= esc((string) $master['id']) ?>"
                                        required
                                    >
                                    <span>
                                        <strong><?= esc($master['kode']) ?></strong>
                                        <?= esc($master['judul']) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Instrumen tidak digandakan. Sistem hanya menandai master instrumen ini sebagai instrumen valid.</small>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" <?= empty($masterInstruments) ? 'disabled' : '' ?>>
                    Pilih
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .master-choice-list {
        max-height: 280px;
        overflow-y: auto;
        border: 1px solid #d9e2ef;
        border-radius: 6px;
        background: #fff;
    }

    .master-choice {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 12px;
        margin: 0;
        cursor: pointer;
        border-bottom: 1px solid #eef2f7;
        line-height: 1.35;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .master-choice:last-child {
        border-bottom: 0;
    }

    .master-choice:hover {
        background: #f6f8fb;
    }

    .master-choice input {
        flex: 0 0 auto;
        margin-top: 3px;
    }

    .master-choice span {
        min-width: 0;
    }
</style>

<?= $this->endSection() ?>
