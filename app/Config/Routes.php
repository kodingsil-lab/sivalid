<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->post('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    $routes->post('instruments/reorder', 'Admin\Instruments::reorder');
    $routes->post('instruments/(:num)/move/(:segment)', 'Admin\Instruments::move/$1/$2');
    $routes->resource('instruments', ['controller' => 'Admin\Instruments']);
    $routes->get('instrument-types', 'Admin\InstrumentTypes::index');
    $routes->post('instrument-types', 'Admin\InstrumentTypes::create');
    $routes->delete('instrument-types/(:num)', 'Admin\InstrumentTypes::delete/$1');
    $routes->post('instrument-aspects/import', 'Admin\InstrumentAspects::import');
    $routes->get('instrument-aspects/import-template', 'Admin\InstrumentAspects::importTemplate');
    $routes->resource('instrument-aspects', ['controller' => 'Admin\InstrumentAspects', 'only' => ['index', 'new', 'create', 'edit', 'update', 'delete']]);
    $routes->resource('instrument-indicators', ['controller' => 'Admin\InstrumentIndicators', 'only' => ['index', 'new', 'create', 'edit', 'update', 'delete']]);
    $routes->post('instrument-items/import', 'Admin\InstrumentItems::import');
    $routes->get('instrument-items/import-template', 'Admin\InstrumentItems::importTemplate');
    $routes->resource('instrument-items', ['controller' => 'Admin\InstrumentItems', 'only' => ['index', 'new', 'create', 'edit', 'update', 'delete']]);
    $routes->get('product-types', 'Admin\ProductTypes::index');
    $routes->post('product-types', 'Admin\ProductTypes::create');
    $routes->delete('product-types/(:num)', 'Admin\ProductTypes::delete/$1');
    $routes->resource('instrument-links', ['controller' => 'Admin\InstrumentLinks', 'only' => ['index', 'new', 'create', 'edit', 'update', 'delete']]);
    $routes->get('instrument-bundles/(:num)/sessions', 'Admin\InstrumentBundles::sessions/$1');
    $routes->get('instrument-bundles/(:num)/sessions/(:num)', 'Admin\InstrumentBundles::sessionDetail/$1/$2');
    $routes->post('instrument-bundles/(:num)/duplicate', 'Admin\InstrumentBundles::duplicate/$1');
    $routes->post('instrument-bundles/(:num)/revoke-token', 'Admin\InstrumentBundles::revokeToken/$1');
    $routes->post('instrument-bundles/(:num)/activate-token', 'Admin\InstrumentBundles::activateToken/$1');
    $routes->resource('instrument-bundles', ['controller' => 'Admin\InstrumentBundles']);
    $routes->get('hasil-validasi-instrumen', 'Admin\InstrumentValidationResults::index');
    $routes->get('hasil-validasi-instrumen/(:num)', 'Admin\InstrumentValidationResults::show/$1');
    $routes->get('hasil-validasi-instrumen/(:num)/excel', 'Admin\InstrumentValidationResults::export/$1');
    $routes->post('hasil-validasi-instrumen/(:num)/instrumen/(:num)/tetapkan-valid', 'Admin\InstrumentValidationResults::setInstrumentValid/$1/$2');

    $routes->get('instrumen-valid', 'Admin\InstrumentValidation::valid');
    $routes->post('instrumen-valid/pilih-master', 'Admin\InstrumentValidation::chooseFromMaster');
    $routes->delete('instrumen-valid/(:num)', 'Admin\InstrumentValidation::delete/$1');

    $routes->get('respondent-links', 'Admin\RespondentLinks::index');
    $routes->get('respondent-links/new', 'Admin\RespondentLinks::new');
    $routes->post('respondent-links', 'Admin\RespondentLinks::create');
    $routes->get('respondent-links/(:num)/edit', 'Admin\RespondentLinks::edit/$1');
    $routes->put('respondent-links/(:num)', 'Admin\RespondentLinks::update/$1');
    $routes->delete('respondent-links/(:num)', 'Admin\RespondentLinks::delete/$1');

    $routes->get('submissions', 'Admin\SubmissionResults::index');
    $routes->get('submissions/export', 'Admin\SubmissionResults::export');
    $routes->get('submissions/export/excel', 'Admin\SubmissionResults::exportExcel');
    $routes->get('submissions/export/word', 'Admin\SubmissionResults::exportWord');
    $routes->get('submissions/export/pdf', 'Admin\SubmissionResults::exportPdf');
    $routes->get('submissions/(:num)', 'Admin\SubmissionResults::show/$1');
    $routes->delete('submissions/(:num)', 'Admin\SubmissionResults::delete/$1');

    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings/profile', 'Admin\Settings::saveProfile');
    $routes->post('settings/category', 'Admin\Settings::saveCategory');
    $routes->post('settings/application', 'Admin\Settings::saveApplication');

    $routes->get('admin-users', 'Admin\AdminUsers::index');
    $routes->get('admin-users/new', 'Admin\AdminUsers::new');
    $routes->post('admin-users', 'Admin\AdminUsers::create');
    $routes->get('admin-users/(:num)/edit', 'Admin\AdminUsers::edit/$1');
    $routes->put('admin-users/(:num)', 'Admin\AdminUsers::update/$1');
    $routes->post('admin-users/(:num)/toggle-status', 'Admin\AdminUsers::toggleStatus/$1');

    $routes->get('backup', 'Admin\Backup::index');
    $routes->get('backup/export-database', 'Admin\Backup::exportDatabase');
    $routes->get('backup/export-files', 'Admin\Backup::exportFiles');
});

$routes->get('isi/(:segment)', 'PublicForm::show/$1');
$routes->post('isi/(:segment)', 'PublicForm::submit/$1');
$routes->get('terima-kasih', 'PublicForm::thanks');

$routes->get('paket/(:segment)', 'PublicBundle::show/$1');
$routes->post('paket/(:segment)/mulai', 'PublicBundle::startSession/$1');
$routes->get('paket/(:segment)/isi/(:num)', 'PublicBundle::showInstrument/$1/$2');
$routes->post('paket/(:segment)/isi/(:num)', 'PublicBundle::saveInstrument/$1/$2');
$routes->post('paket/(:segment)/isi/(:num)/autosave', 'PublicBundle::autosave/$1/$2');
$routes->get('paket/(:segment)/ringkasan', 'PublicBundle::summary/$1');
