<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

$routes->get('/', 'Home::index');

// Static Images

$routes->get('uploads/(.*)', 'Image::index/$1');

// Test Routes

$routes->get('test', 'Product::testSuccess');

$routes->get('test/error', 'Product::testError');

$routes->get("test/tags", 'Product::getAllTags');
// Warehouse routes

$routes->get('warehouse', 'Warehouse::index');

$routes->get('warehouse/(:num)', 'Warehouse::showSingle/$1');

$routes->get('warehouse/edit/(:num)', 'Warehouse::showEdit/$1');

$routes->post('warehouse/edit/(:num)', 'Warehouse::saveEdit/$1');

$routes->post('warehouse/delete/(:num)', 'Warehouse::delete/$1');

$routes->get('warehouse/create', 'Warehouse::show');

$routes->post('warehouse/create', 'Warehouse::create');

// Product routes

$routes->get('/product', 'Product::index');

$routes->get('product/(:num)', 'Product::showSingle/$1');

$routes->get('product/create', 'Product::showCreate');

$routes->post('product/create', 'Product::create');

$routes->get('product/edit/(:num)', 'Product::showEdit/$1');

$routes->post('product/edit/(:num)', 'Product::saveEdit/$1');

$routes->post('product/delete/(:num)', 'Product::delete/$1');

$routes->post("/product/(:num)/images/delete", 'Product::deleteImages/$1');

$routes->post("/product/(:num)/images/(:any)", 'Product::deleteSingleImage/$1/$2');

// WarehouseProduct routes

$routes->get('warehouse/(:num)/product', 'WarehouseProduct::index/$1');

$routes->get('warehouse/(:num)/product/add', 'WarehouseProduct::show/$1');

$routes->post('warehouse/(:num)/product/add', 'WarehouseProduct::create/$1');

$routes->post('warehouse/(:num)/product/(:num)/delete', 'WarehouseProduct::destroy/$1/$2');

$routes->get('warehouse/(:num)/product/(:num)/edit', 'WarehouseProduct::showEdit/$1/$2');

$routes->post('warehouse/(:num)/product/(:num)/edit', 'WarehouseProduct::edit/$1/$2');
