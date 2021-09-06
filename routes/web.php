<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\ElementsController;
use App\Http\Controllers\ExportProjectToFTPController;
use App\Http\Controllers\ProjectDownloadController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ProjectSettingsController;
use App\Http\Controllers\ProjectThumbnailController;
use App\Http\Controllers\TemplatesController;

Route::group(['prefix' => 'secure'], function () {

    //templates
    Route::get('templates', [TemplatesController::class, 'index']);
    Route::get('templates/{name}', [TemplatesController::class, 'show']);
    Route::post('templates', [TemplatesController::class, 'store']);
    Route::put('templates/{name}', [TemplatesController::class, 'update']);
    Route::delete('templates', [TemplatesController::class, 'destroy']);

    //projects
    Route::get('projects', [ProjectsController::class, 'index']);
    Route::post('projects/{project}/export/ftp', [ExportProjectToFTPController::class, 'export']);
    Route::post('projects', [ProjectsController::class, 'store']);
    Route::get('projects/{id}', [ProjectsController::class, 'show']);
    Route::put('projects/{id}', [ProjectsController::class, 'update']);
    Route::put('projects/{project}/toggle-state', [ProjectsController::class, 'toggleState']);
    Route::delete('projects', [ProjectsController::class, 'destroy']);
    Route::post('projects/{id}/generate-thumbnail', [ProjectThumbnailController::class, 'store']);
    Route::get('projects/{project}/download', [ProjectDownloadController::class, 'download']);

    // project settings
    Route::post('projects/{project}/settings', [ProjectSettingsController::class, 'store']);

    //elements
    Route::get('elements/custom.js', [ElementsController::class, 'custom']);
});

Route::get('sites/{name}/{page?}', 'UserSiteController@show')->name('user-site-regular');

//FRONT-END ROUTES THAT NEED TO BE PRE-RENDERED
Route::get('/', '\Common\Core\Controllers\HomeController@show')
    ->middleware('prerenderIfCrawler:homepage');

Route::get('{all}', '\Common\Core\Controllers\HomeController@show')->where('all', '.*');
