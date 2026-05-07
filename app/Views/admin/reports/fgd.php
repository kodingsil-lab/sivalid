<?= view('admin/reports/respon_mahasiswa', [
    'title'     => $title ?? 'Laporan FGD',
    'link'      => $link,
    'responses' => $responses,
    'summary'   => $summary,
    'items'     => $items,
    'comments'  => $comments,
]) ?>