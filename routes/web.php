<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\RoutingTimelineController;
use App\Http\Controllers\ViewFilePdfController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\InterOfficeController;

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

Route::group(['middleware'=>['guest']],function(){
    Route::get('/', function () {
        return view('.login.login');
    });


//login
Route::get('/login', [LoginAuthController::class, 'getLogin'])->name('getLogin');
Route::post('/login', [LoginAuthController::class, 'postLogin'])->name('postLogin');

    

});

Route::group(['middleware'=>['login_auth']],function(){
    //Main page
    Route::get('/', [PagesController::class, 'dashboard'])->name('dashboard');

    // Pending Page
    Route::get('/pending', [PagesController::class, 'pending'])->name('pending');


    // Routing Timeline Page
    Route::get('/routing-timeline/{id}', [RoutingTimelineController::class, 'routingTimeline'])->name('routingTimeline');
    Route::get('/routing-timeline-search', [RoutingTimelineController::class, 'searchTimeline'])->name('routingTimelineSearch');


    Route::post('/reroute-log', [RoutingTimelineController::class, 'rerouteLog'])->name('rerouteLog');

    Route::post('/acknowledge-log', [RoutingTimelineController::class, 'acknowledgeLog'])->name('acknowledgeLog');



    // Inter-Office Transaction Page
    Route::get('/inter-office', [InterOfficeController::class, 'interOffice'])->name('interOffice');
    Route::post('/inter-office/store', [InterOfficeController::class, 'storeInterOffice'])->name('storeInterOffice');
    route::get('/inter-office/view/{id}', [InterOfficeController::class, 'viewInterOffice'])->name('viewInterOffice');
    Route::post('/inter-office/add-entry', [InterOfficeController::class, 'addEntry'])->name('interOffice.addEntry');

    // Log actions
    Route::put('/inter-office/log/{id}/update', [InterOfficeController::class, 'updateStatus'])->name('interOffice.updateStatus');
    Route::post('/inter-office/log/{id}/return', [InterOfficeController::class, 'returnLog'])->name('interOffice.return');


    // Routing Page
    Route::get('/routing', [PagesController::class, 'routing'])->name('routing');
    Route::get('/routing-to-pres', [PagesController::class, 'routingToPres'])->name('routingToPres');
    Route::get('/routing-pending', [PagesController::class, 'routingPending'])->name('routingPending');

    // Add Transaction Modal
    Route::post('/add-routing-pres', [RoutingController::class, 'addRoutingPres'])->name('addRoutingPres');
    Route::post('/add-routing-personnel', [RoutingController::class, 'addRoutingPersonnel'])->name('addRoutingPersonnel');

    // Edit Transaction to President's Office
    Route::get('/routed-to-pres/edit-pres/{id}', [RoutingController::class, 'editRoutingPres'])->name('editRoutingPres');
    Route::put('/routed-to-pres/update-pres/{id}', [RoutingController::class, 'updateRoutingPres'])->name('updateRoutingPres');

    // Edit Transaction to be Route to Personnel
    Route::get('/routed-entry/edit-trans-entry/{id}', [RoutingController::class, 'editRoutingEntry'])->name('editRoutingEntry');
    Route::put('/routed-entry/update-trans-entry/{id}', [RoutingController::class, 'updateRoutingEntry'])->name('updateRoutingEntry');

    // Recall Transaction from Personnel 
    Route::get('/routed-pending-entry/edit-recall/{id}', [RoutingController::class, 'editRecall'])->name('editRecall');
    Route::put('/routed-pending-entry/update-recall/{id}', [RoutingController::class, 'updateRecall'])->name('updateRecall');

    // Route Back To President's Office
    Route::put('/route-back/{id}', [RoutingController::class, 'routeBackToPresident'])->name('routeBackToPresident');

    // Edit Transaction to be Reroute to Personnel
    Route::get('/rerouted-entry/edit-reroute-entry/{id}', [RoutingController::class, 'editRerouteEntry'])->name('editRerouteEntry');
    Route::put('/rerouted-entry/update-reroute-entry/{id}', [RoutingController::class, 'updateRerouteEntry'])->name('updateRerouteEntry');

    // view Routed document slip
    Route::get('/routing-slip/view/{slip}',[ViewFilePdfController::class, 'viewRouteSlip'])->name('viewRouteSlip');
    // Route::get('/pdf-slip/view/{id}', [ViewFilePdfController::class, 'pdfSlip'])->name('pdfSlip');
    Route::get('/routing-slip/pdf/{id}',[ViewFilePdfController::class, 'pdfSlip'])->name('pdfSlip');


    // User Pages
    Route::get('/users-page', [PagesController::class, 'usersView'])->name('usersView');
    Route::post('/users/create', [PagesController::class, 'createUser'])->name('createUser');
    Route::put('/users/edit/{id}', [PagesController::class, 'userEdit'])->name('userEdit');
    Route::post('/users/Group', [PagesController::class, 'addGroup'])->name('users.addGroup');
    Route::post('/update-dpa', [PagesController::class, 'updateDpa'])->name('update.dpa');


    // Distribution list PDF
    Route::get('/distribution-list-pres/view/',[ViewFilePdfController::class, 'viewistListPres'])->name('viewistListPres');
    Route::get('/distribution-list-pres/pdf/{id}',[ViewFilePdfController::class, 'distListPresPdf'])->name('distListPresPdf');

    Route::get('/distribution-list-direct/view/',[ViewFilePdfController::class, 'viewDirectList'])->name('viewDirectList');
    Route::get('/distribution-list-direct/pdf/{id}',[ViewFilePdfController::class, 'distListDrirectPdf'])->name('distListDrirectPdf');


    // All category of Logs Pages
    Route::get('/logs-page', [PagesController::class, 'logsView'])->name('logsView');
    Route::get('/transaction-logs-page', [PagesController::class, 'tranLogsView'])->name('tranLogsView');
    Route::get('/management-log-page', [PagesController::class, 'managementLogs'])->name('managementLogs');


    // Offices
    Route::get('/offices', [PagesController::class, 'offices'])->name('offices');
    Route::put('/offices/{office}', [PagesController::class, 'update'])->name('offices.update');
    Route::delete('/offices/{office}', [PagesController::class, 'destroy'])->name('offices.destroy');
    Route::post('/offices', [PagesController::class, 'store'])->name('offices.store');

    // user groups
    Route::get('/userGroups', [PagesController::class, 'userGroups'])->name('userGroups');
    Route::put('/groups/{group}', [PagesController::class, 'updateGroup'])->name('groups.update');
    Route::delete('/groups/{group}', [PagesController::class, 'destroyGroup'])->name('groups.destroy');
    Route::post('/groups', [PagesController::class, 'storeGroup'])->name('groups.store');

    // Archive Logbook
    Route::get('/archive-logbook', [ArchiveController::class, 'archiveLogbook'])->name('archiveLogbook');
    Route::get('/archived-history', [ArchiveController::class, 'archivedHistory'])->name('archivedHistory');
    Route::post('/archive-logbook/archive', [ArchiveController::class, 'archiveByYear'])->name('archiveLogbook.archive');

    //logout
    Route::get('/logout', [LoginAuthController::class, 'logout'])->name('logout');
});
