<?php

use CodeIgniter\Pager\PagerRenderer;

/** @var PagerRenderer $pager */
$pager->setSurroundCount(0);
?>
<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="pagination justify-content-end mb-0">
        <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->getPrevious() ?? '#' ?>" aria-label="<?= lang('Pager.previous') ?>">
                <?= lang('Pager.newer') ?>
            </a>
        </li>
        <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->getNext() ?? '#' ?>" aria-label="<?= lang('Pager.next') ?>">
                <?= lang('Pager.older') ?>
            </a>
        </li>
    </ul>
</nav>