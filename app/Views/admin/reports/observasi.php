<?= view('admin/reports/respon_mahasiswa', [
    'title'     => $title ?? 'Laporan Observasi',
    'link'      => $link,
    'responses' => $responses,
    'summary'   => $summary,
    'items'     => $items,
    'comments'  => $comments,
]) ?>