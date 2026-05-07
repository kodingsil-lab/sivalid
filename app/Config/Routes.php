<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    $routes->resource('instruments', ['controller' => 'Admin\Instruments']);
    $routes->resource('instrument-aspects', ['controller' => 'Admin\InstrumentAspects']);
    $routes->resource('instrument-indicators', ['controller' => 'Admin\InstrumentIndicators']);
    $routes->resource('instrument-items', ['controller' => 'Admin\InstrumentItems']);
    $routes->resource('instrument-revisions', ['controller' => 'Admin\InstrumentRevisions']);
    $routes->get('products/download/(:num)', 'Admin\Products::download/$1');
    $routes->resource('products', ['controller' => 'Admin\Products']);
    $routes->resource('instrument-links', ['controller' => 'Admin\InstrumentLinks']);

    $routes->get('validasi-instrumen', 'Admin\InstrumentValidation::index');
    $routes->post('validasi-instrumen/proses/(:num)', 'Admin\InstrumentValidation::process/$1');
    $routes->get('validasi-instrumen/hasil/(:num)', 'Admin\InstrumentValidation::show/$1');
    $routes->get('validasi-instrumen/analisis/(:num)', 'Admin\InstrumentValidation::analysis/$1');
    $routes->post('validasi-instrumen/tetapkan-valid/(:num)', 'Admin\InstrumentValidation::setValid/$1');
    $routes->get('instrumen-valid', 'Admin\InstrumentValidation::valid');

    $routes->get('validasi-produk', 'Admin\ProductValidation::index');
    $routes->get('validasi-produk/new', 'Admin\ProductValidation::new');
    $routes->post('validasi-produk', 'Admin\ProductValidation::create');
    $routes->get('validasi-produk/(:num)/edit', 'Admin\ProductValidation::edit/$1');
    $routes->put('validasi-produk/(:num)', 'Admin\ProductValidation::update/$1');
    $routes->delete('validasi-produk/(:num)', 'Admin\ProductValidation::delete/$1');
    $routes->post('validasi-produk/proses/(:num)', 'Admin\ProductValidation::process/$1');
    $routes->get('validasi-produk/hasil/(:num)', 'Admin\ProductValidation::show/$1');
    $routes->get('validasi-produk/analisis/(:num)', 'Admin\ProductValidation::analysis/$1');

    $routes->get('respondent-links', 'Admin\RespondentLinks::index');
    $routes->get('respondent-links/new', 'Admin\RespondentLinks::new');
    $routes->post('respondent-links', 'Admin\RespondentLinks::create');
    $routes->get('respondent-links/(:num)/edit', 'Admin\RespondentLinks::edit/$1');
    $routes->put('respondent-links/(:num)', 'Admin\RespondentLinks::update/$1');
    $routes->delete('respondent-links/(:num)', 'Admin\RespondentLinks::delete/$1');

    $routes->get('reports', 'Admin\Reports::index');
    $routes->get('reports/validasi-instrumen/(:num)', 'Admin\Reports::validasiInstrumen/$1');
    $routes->get('reports/validasi-produk/(:num)', 'Admin\Reports::validasiProduk/$1');
    $routes->get('reports/revisi-butir', 'Admin\Reports::revisiButir');
    $routes->get('reports/respon-mahasiswa/(:num)', 'Admin\Reports::responMahasiswa/$1');
    $routes->get('reports/observasi/(:num)', 'Admin\Reports::observasi/$1');
    $routes->get('reports/fgd/(:num)', 'Admin\Reports::fgd/$1');
    $routes->get('reports/tes-kinerja/(:num)', 'Admin\Reports::tesKinerja/$1');
    $routes->get('reports/validasi-instrumen/(:num)/print', 'Admin\Reports::printValidasiInstrumen/$1');
    $routes->get('reports/validasi-produk/(:num)/print', 'Admin\Reports::printValidasiProduk/$1');
    $routes->get('reports/validasi-instrumen/(:num)/pdf', 'Admin\ReportPdf::validasiInstrumen/$1');
    $routes->get('reports/validasi-instrumen/(:num)/pdf-preview', 'Admin\ReportPdf::previewValidasiInstrumen/$1');
    $routes->get('reports/validasi-produk/(:num)/pdf', 'Admin\ReportPdf::validasiProduk/$1');
    $routes->get('reports/validasi-produk/(:num)/pdf-preview', 'Admin\ReportPdf::previewValidasiProduk/$1');

    $routes->get('submissions', 'Admin\SubmissionResults::index');
    $routes->get('submissions/export', 'Admin\SubmissionResults::export');
    $routes->get('submissions/(:num)', 'Admin\SubmissionResults::show/$1');
    $routes->delete('submissions/(:num)', 'Admin\SubmissionResults::delete/$1');

    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings/profile', 'Admin\Settings::saveProfile');
    $routes->post('settings/category', 'Admin\Settings::saveCategory');

    $routes->get('analysis', 'Admin\Analysis::index');
});

$routes->get('isi/(:segment)', 'PublicForm::show/$1');
$routes->post('isi/(:segment)', 'PublicForm::submit/$1');
$routes->get('terima-kasih', 'PublicForm::thanks');
