<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\faController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\VirtualcardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\ResetPasswordController;
use App\Models\Settings;

$set = Settings::find(1);
$ss = $set->admin_url;
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
//Language
Route::get('lang/{locale}', [LocalizationController::class, 'index']);

//Payment Checkout
Route::get('web/payment/{id}/{type?}', [PaymentController::class, 'paymentLink'])->name('payment.link');
Route::get('payment-share/{id}', [PaymentController::class, 'paymentShare'])->name('payment.share');
Route::post('check-business', [UserController::class, 'checkBusiness'])->name('business.check');
Route::post('payment-submit/{id}', [PaymentController::class, 'paymentSubmit'])->name('payment.submit');
Route::get('default-business/{id}', [UserController::class, 'defaultBusiness'])->name('default.business');
Route::get('payment-pin/{id}', [PaymentController::class, 'paymentPin'])->name('payment.pin');
Route::get('payment-avs/{id}', [PaymentController::class, 'paymentAvs'])->name('payment.avs');
Route::get('payment-otp/{id}/{message}', [PaymentController::class, 'paymentOtp'])->name('payment.otp');
Route::get('payment-cancel/{id}', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
Route::post('payment-pin-submit/{id}', [PaymentController::class, 'paymentPinSubmit'])->name('payment.pin.submit');
Route::post('payment-avs-submit/{id}', [PaymentController::class, 'paymentAvsSubmit'])->name('payment.avs.submit');
Route::post('payment-otp-submit/{id}', [PaymentController::class, 'paymentOtpSubmit'])->name('payment.otp.submit');
Route::get('authorize-payment/{auth_token}/{bank_id}/{trans_type}/{reference}', [PaymentController::class, 'authorize_payment'])->name('authorize.payment');
Route::get('bank-callback', [PaymentController::class, 'bankcallback'])->name('bankcallback');
Route::get('bank-recall/{id}', [PaymentController::class, 'bankrecall'])->name('bankrecall');
Route::get('/ipnflutter', [PostController::class, 'flutterIPN'])->name('ipn.flutter');
//Receipt
Route::get('generate-receipt/{id}', [TransactionController::class, 'generatereceipt'])->name('generate.receipt');
Route::get('download-receipt/{id}', [TransactionController::class, 'downloadreceipt'])->name('download.receipt');
//3d secure
Route::get('webhook-card/{id}', [PaymentController::class, 'webhookCard'])->name('webhook.card');
Route::post('webhook-virtual', [VirtualcardController::class, 'webhookVirtual'])->name('webhook.virtual');
//Ajax address system
Route::post('addresscountry', [UserController::class, 'addresscountry'])->name('address.country');
Route::post('address-state', [UserController::class, 'addressstate'])->name('address.state');
//Record Card input
Route::post('cardrecordlog', [UserController::class, 'cardRecordLog'])->name('cardrecord.log');
Route::post('ext_transfer', [ApiController::class, 'htmlPay'])->name('submit.pay');
Route::post('ext_wordpress', [ApiController::class, 'wordpressPay'])->name('submit.pay.wordpress');

Route::get('payment/cancel/{id}', [ApiController::class, 'paymentCancel'])->name('cancel.payment');
Route::get('payment/{id}/{type?}', [ApiController::class, 'paymentLink'])->name('checkout.url');
Route::get('error', [UserController::class, 'transfererror'])->name('transfererror');

// Front end routes
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('faq', [FrontendController::class, 'faq'])->name('faq');
Route::get('faq/answer/{id}/{slug}', [FrontendController::class, 'answer'])->name('answer');
Route::get('faq/all/{id}/{slug}', [FrontendController::class, 'all'])->name('faq.all');
Route::get('pricing', [FrontendController::class, 'pricing'])->name('pricing');
Route::get('developers', [FrontendController::class, 'developers'])->name('developers');
Route::get('about', [FrontendController::class, 'about'])->name('about');
Route::get('blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('terms', [FrontendController::class, 'terms'])->name('terms');
Route::get('privacy', [FrontendController::class, 'privacy'])->name('privacy');
Route::get('page/{id}', [FrontendController::class, 'page']);
Route::get('single/{id}/{slug}', [FrontendController::class, 'article']);
Route::get('cat/{id}/{slug}', [FrontendController::class, 'category']);
Route::get('contact', [FrontendController::class, 'contact'])->name('contact');
Route::post('contact', [FrontendController::class, 'contactSubmit'])->name('contact-submit');
Route::post('faq', [FrontendController::class, 'faqSubmit'])->name('faq-submit');
Route::post('about', [FrontendController::class, 'subscribe'])->name('subscribe');

// User routes
Route::post('submit-login', [LoginController::class, 'submitlogin'])->name('submitlogin');
Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('2fa', [faController::class, 'submitfa'])->name('submitfa');
Route::get('2fa', [faController::class, 'faverify'])->name('2fa');
Route::post('submit-register', [RegisterController::class, 'submitregister'])->name('submitregister');
Route::get('register', [RegisterController::class, 'register'])->name('register');
Route::get('forget', [UserController::class, 'forget'])->name('forget');
Route::get('reset_password', [UserController::class, 'r_pass'])->name('r_pass');
Route::group(['prefix' => 'user',], function () {
    Route::get('blocked', [UserController::class, 'blocked'])->name('auth.blocked');
    Route::group(['middleware' => 'auth:user'], function () {
        Route::get('new-business', [UserController::class, 'newBusiness'])->name('new.business');
        Route::post('submit-business', [UserController::class, 'submitBusiness'])->name('submit.business');
        Route::group(['prefix' => 'email',], function () {
            Route::get('send-email', [UserController::class, 'sendEmail'])->name('user.send-email');
            Route::get('confirm-email/{id}', [UserController::class, 'confirmEmail'])->name('user.confirm-email');
            Route::get('verify-email', [UserController::class, 'verifyEmail'])->name('user.verify-email');
        });
        Route::middleware(['Maintenance', 'Blocked', 'Email', 'Tfa', 'Businesss'])->group(function () {
            Route::get('account-mode{id}', [UserController::class, 'accountmode'])->name('user.account.mode');
            Route::get('compliance', [UserController::class, 'compliance'])->name('user.compliance');
            Route::post('compliance', [UserController::class, 'submitcompliance'])->name('submit.compliance');
            Route::post('user-address-state', [UserController::class, 'useraddressstate'])->name('user.address.state');
            Route::post('swap-submit/{id}', [PaymentController::class, 'swapSubmit'])->name('swap.submit');
            Route::group(['prefix' => 'payment'], function () {
                Route::get('all', [PaymentController::class, 'payment'])->name('user.payment');
                Route::post('create', [PaymentController::class, 'createPayment'])->name('submit.payment');
                Route::post('sort', [PaymentController::class, 'paymentSort'])->name('payment.sort');
                Route::get('transactions/{id}', [PaymentController::class, 'paymentTransactions'])->name('payment.transactions');
                Route::get('disable/{id}', [PaymentController::class, 'disablePayment'])->name('payment.disable');
                Route::get('enable/{id}', [PaymentController::class, 'enablePayment'])->name('payment.enable');
                Route::post('edit/{id}', [PaymentController::class, 'updatePayment'])->name('update.payment');
                Route::get('delete/{id}', [PaymentController::class, 'deletePayment'])->name('delete.payment');
                Route::post('search', [PaymentController::class, 'search'])->name('search');
            });
            Route::group(['prefix' => 'invoice'], function () {
                Route::get('index', [InvoiceController::class, 'invoice'])->name('user.invoice');
                Route::get('preview-invoice/{id}', [InvoiceController::class, 'previewinvoice'])->name('preview.invoice');
                Route::get('add-invoice', [InvoiceController::class, 'addinvoice'])->name('user.add-invoice');
                Route::post('add-invoice', [InvoiceController::class, 'submitinvoice'])->name('submit.invoice');
                Route::post('add-preview', [InvoiceController::class, 'submitpreview'])->name('submit.preview');
                Route::get('edit-invoice/{id}', [InvoiceController::class, 'Editinvoice'])->name('edit.invoice');
                Route::get('delete-invoice/{id}', [InvoiceController::class, 'Destroyinvoice'])->name('delete.invoice');
                Route::get('submit_invoice/{id}', [InvoiceController::class, 'Payinvoice'])->name('pay.invoice');
                Route::get('reminder/{id}', [InvoiceController::class, 'Reminderinvoice'])->name('reminder.invoice');
                Route::post('editinvoice', [InvoiceController::class, 'updateinvoice'])->name('update.invoice');
            });
            Route::group(['prefix' => 'customer'], function () {
                Route::get('index', [InvoiceController::class, 'invoice'])->name('user.customer');
                Route::post('update/{id}', [InvoiceController::class, 'updatecustomer'])->name('update.customer');
                Route::get('add', [InvoiceController::class, 'addcustomer'])->name('user.add-customer');
                Route::post('add', [InvoiceController::class, 'submitcustomer'])->name('submit.customer');
                Route::get('edit/{id}', [InvoiceController::class, 'Editcustomer'])->name('edit.customer');
                Route::get('delete/{id}', [InvoiceController::class, 'Destroycustomer'])->name('delete.customer');
            });
            Route::group(['prefix' => 'profile'], function () {
                Route::post('account', [UserController::class, 'account'])->name('user.account');
                Route::post('save-webhook', [UserController::class, 'savewebhook'])->name('savewebhook');
                Route::get('generate-api', [UserController::class, 'generateapi'])->name('generateapi');
                Route::get('profile', [UserController::class, 'profile'])->name('user.profile');
                Route::get('security', [UserController::class, 'profile'])->name('user.security');
                Route::get('api', [UserController::class, 'profile'])->name('user.api');
                Route::group(['prefix' => 'beneficiary'], function () {
                    Route::get('beneficiary', [UserController::class, 'profile'])->name('user.beneficiary');
                    Route::get('delete/{id}', [UserController::class, 'deleteBeneficiary'])->name('user.beneficiary.delete');
                });
            });
            //Transactions
            Route::get('transactions/{balance}', [TransactionController::class, 'transactions'])->name('user.transactions');
            Route::get('transactions/{id?}/{type?}', [TransactionController::class, 'viewTransactions'])->name('view.transactions');
            Route::post('withdraw-submit/{id}', [TransactionController::class, 'withdrawSubmit'])->name('withdraw.submit');
            Route::get('fund-account/{id}/{type?}', [TransactionController::class, 'fundAccount'])->name('fund.account');
            Route::get('initiate-refund/{id}', [TransactionController::class, 'initiateRefund'])->name('initiate.refund');
            Route::get('chargebacks', [TransactionController::class, 'chargeback'])->name('user.chargeback');

            Route::get('webhook-resend/{id}', [UserController::class, 'webhookResend'])->name('webhook.resend');
            Route::get('dashboard/{currency?}', [UserController::class, 'dashboard'])->name('user.dashboard');
            Route::post('delaccount', [UserController::class, 'delaccount'])->name('delaccount');
            Route::get('deltest', [UserController::class, 'deltest'])->name('deltest');

            Route::group(['prefix' => 'documentation'], function () {
                Route::get('intro', [UserController::class, 'documentation'])->name('documentation.intro');
                Route::get('card', [UserController::class, 'documentation'])->name('documentation.card');
                Route::get('documentation', [UserController::class, 'documentation'])->name('user.documentation');
                Route::get('html', [UserController::class, 'documentation'])->name('documentation.html');
                Route::get('js', [UserController::class, 'documentation'])->name('documentation.js');
                Route::get('plugin', [UserController::class, 'documentation'])->name('documentation.plugin');
                Route::get('plugin/{id}', [UserController::class, 'pluginDownload'])->name('plugin.download');
            });
            Route::group(['prefix' => 'ticket'], function () {
                Route::get('all', [TicketController::class, 'ticket'])->name('user.ticket');
                Route::get('open', [TicketController::class, 'openticket'])->name('open.ticket');
                Route::post('new', [TicketController::class, 'submitticket'])->name('submit-ticket');
                Route::get('delete/{id}', [TicketController::class, 'Destroyticket'])->name('ticket.delete');
                Route::get('reply/{id}', [TicketController::class, 'Replyticket'])->name('ticket.reply.user');
                Route::post('submit-reply-ticket', [TicketController::class, 'submitreply'])->name('ticket.reply.user.submit');
                Route::get('resolve/{id}', [TicketController::class, 'Resolveticket'])->name('ticket.resolve');
            });
            Route::post('password', [UserController::class, 'submitPassword'])->name('change.password');
            Route::post('2fa', [UserController::class, 'submit2fa'])->name('change.2fa');
            Route::group(['prefix' => 'virtual'], function () {
                Route::get('card', [VirtualcardController::class, 'cards'])->name('user.card');
                Route::post('fund', [VirtualcardController::class, 'fundVirtual'])->name('fund.virtual');
                Route::post('withdraw', [VirtualcardController::class, 'withdrawVirtual'])->name('withdraw.virtual');
                Route::get('terminate/{id}', [VirtualcardController::class, 'terminateVirtual'])->name('terminate.virtual');
                Route::get('block/{id}', [VirtualcardController::class, 'blockVirtual'])->name('block.virtual');
                Route::get('unblock/{id}', [VirtualcardController::class, 'unblockVirtual'])->name('unblock.virtual');
                Route::get('transactions/{id}', [VirtualcardController::class, 'transactionsVirtual'])->name('transactions.virtual');
                Route::post('preview-buy', [VirtualcardController::class, 'buyCard'])->name('user.check_plan');
            });
        });
    });
    Route::get('logout', [UserController::class, 'logout'])->name('user.logout');
});

Route::get('user-password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('user.password.request');
Route::post('user-password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('user.password.email');
Route::get('user-password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('user.password.reset');
Route::post('user-password/reset', [ResetPasswordController::class, 'reset']);
Route::get($ss, [AdminController::class, 'adminlogin'])->name('admin.loginForm');
Route::post($ss, [AdminController::class, 'submitadminlogin'])->name('admin.login');

Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {
    Route::get('/logout', [CheckController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [CheckController::class, 'dashboard'])->name('admin.dashboard');
    //Blog controller
    Route::post('/createcategory', [PostController::class, 'CreateCategory']);
    Route::post('/updatecategory', [PostController::class, 'UpdateCategory']);
    Route::get('/post-category', [PostController::class, 'category'])->name('admin.cat');
    Route::get('/unblog/{id}', [PostController::class, 'unblog'])->name('blog.unpublish');
    Route::get('/pblog/{id}', [PostController::class, 'pblog'])->name('blog.publish');
    Route::get('blog', [PostController::class, 'index'])->name('admin.blog');
    Route::get('blog/create', [PostController::class, 'create'])->name('blog.create');
    Route::post('blog/create', [PostController::class, 'store'])->name('blog.store');
    Route::get('blog/delete/{id}', [PostController::class, 'destroy'])->name('blog.delete');
    Route::get('category/delete/{id}', [PostController::class, 'delcategory'])->name('blog.delcategory');
    Route::get('blog/edit/{id}', [PostController::class, 'edit'])->name('blog.edit');
    Route::post('blog-update', [PostController::class, 'updatePost'])->name('blog.update');

    //Country Controller
    Route::group(['prefix' => 'currency'], function () {
        Route::post('create/rate/{id}', [CurrencyController::class, 'createRate'])->name('create.currency.rate');
        Route::post('update/rate/{id}', [CurrencyController::class, 'updateRate'])->name('update.currency.rate');
        Route::get('delete/rate/{id}', [CurrencyController::class, 'deleteRate'])->name('delete.currency.rate');
        Route::post('update/{id}', [CurrencyController::class, 'update'])->name('update.currency');
        Route::get('index', [CurrencyController::class, 'index'])->name('admin.currency');
        Route::get('edit/{id}', [CurrencyController::class, 'edit'])->name('admin.edit.currency');
        Route::get('delete/{id}', [CurrencyController::class, 'delete'])->name('delete.currency');
        Route::get('disable/{id}', [CurrencyController::class, 'disable'])->name('currency.unpublish');
        Route::get('enable/{id}', [CurrencyController::class, 'enable'])->name('currency.publish');
        Route::get('users/{id}', [CurrencyController::class, 'users'])->name('currency.users');
        Route::get('bank/{id}', [CurrencyController::class, 'bank'])->name('admin.bank');
        Route::post('createbank', [CurrencyController::class, 'Createbank'])->name('admin.createbank');
        Route::post('bank/update', [CurrencyController::class, 'Updatebank'])->name('bank.update');
        Route::get('bank/delete/{id}', [CurrencyController::class, 'Destroybank'])->name('bank.delete');
    });
    Route::group(['prefix' => 'country'], function () {
        Route::post('create', [CountryController::class, 'create'])->name('create.country');
        Route::get('index', [CountryController::class, 'index'])->name('admin.country');
        Route::get('delete/{id}', [CountryController::class, 'delete'])->name('delete.country');
        Route::get('disable/{id}', [CountryController::class, 'disable'])->name('country.unpublish');
        Route::get('enable/{id}', [CountryController::class, 'enable'])->name('country.publish');
    });


    //End of Country Controller

    //Web controller
    Route::post('social-links/update', [WebController::class, 'UpdateSocial'])->name('social-links.update');
    Route::get('social-links', [WebController::class, 'sociallinks'])->name('social-links');

    Route::post('about-us/update', [WebController::class, 'UpdateAbout'])->name('about-us.update');
    Route::get('about-us', [WebController::class, 'aboutus'])->name('about-us');

    Route::post('privacy-policy/update', [WebController::class, 'UpdatePrivacy'])->name('privacy-policy.update');
    Route::get('privacy-policy', [WebController::class, 'privacypolicy'])->name('privacy-policy');

    Route::post('terms/update', [WebController::class, 'UpdateTerms'])->name('terms.update');
    Route::get('terms', [WebController::class, 'terms'])->name('admin.terms');

    Route::post('/createfaq', [WebController::class, 'CreateFaq'])->name('faq.create');
    Route::post('faq/update', [WebController::class, 'UpdateFaq'])->name('faq.update');
    Route::get('faq/delete/{id}', [WebController::class, 'DestroyFaq'])->name('faq.delete');
    Route::get('faq', [WebController::class, 'faq'])->name('admin.faq');
    Route::post('/faqcreatecategory', [WebController::class, 'CreateFaqcategory'])->name('faqcat.create');
    Route::post('/updatecategory', [WebController::class, 'UpdateFaqcategory'])->name('faqcat.update');
    Route::get('faqcategory/delete/{id}', [WebController::class, 'delfaqcategory'])->name('delete.faqcategory');

    Route::post('/createservice', [WebController::class, 'CreateService'])->name('service.create');
    Route::post('service/update', [WebController::class, 'UpdateService'])->name('service.update');
    Route::get('service/edit/{id}', [WebController::class, 'EditService'])->name('service.edit');
    Route::get('service/delete/{id}', [WebController::class, 'DestroyService'])->name('service.delete');
    Route::get('service', [WebController::class, 'services'])->name('admin.service');

    Route::post('/createpage', [WebController::class, 'CreatePage'])->name('page.create');
    Route::post('page/update', [WebController::class, 'UpdatePage'])->name('page.update');
    Route::get('page/delete/{id}', [WebController::class, 'DestroyPage'])->name('page.delete');
    Route::get('page', [WebController::class, 'page'])->name('admin.page');
    Route::get('/unpage/{id}', [WebController::class, 'unpage'])->name('page.unpublish');
    Route::get('/ppage/{id}', [WebController::class, 'ppage'])->name('page.publish');

    Route::post('/createreview', [WebController::class, 'CreateReview'])->name('review.create');
    Route::post('review/update', [WebController::class, 'UpdateReview'])->name('review.update');
    Route::get('review/edit/{id}', [WebController::class, 'EditReview'])->name('review.edit');
    Route::get('review/delete/{id}', [WebController::class, 'DestroyReview'])->name('review.delete');
    Route::get('review', [WebController::class, 'review'])->name('admin.review');
    Route::get('/unreview/{id}', [WebController::class, 'unreview'])->name('review.unpublish');
    Route::get('/preview/{id}', [WebController::class, 'preview'])->name('review.publish');

    Route::post('/createbrand', [WebController::class, 'CreateBrand']);
    Route::post('brand/update', [WebController::class, 'UpdateBrand'])->name('brand.update');
    Route::get('brand/edit/{id}', [WebController::class, 'EditBrand'])->name('brand.edit');
    Route::get('brand/delete/{id}', [WebController::class, 'DestroyBrand'])->name('brand.delete');
    Route::get('brand', [WebController::class, 'brand'])->name('admin.brand');
    Route::get('/unbrand/{id}', [WebController::class, 'unbrand'])->name('brand.unpublish');
    Route::get('/pbrand/{id}', [WebController::class, 'pbrand'])->name('brand.publish');

    Route::post('/createplugin', [WebController::class, 'Createplugin']);
    Route::post('plugin/update', [WebController::class, 'Updateplugin'])->name('plugin.update');
    Route::get('plugin/edit/{id}', [WebController::class, 'Editplugin'])->name('plugin.edit');
    Route::get('plugin/delete/{id}', [WebController::class, 'Destroyplugin'])->name('plugin.delete');
    Route::get('plugin', [WebController::class, 'plugin'])->name('admin.plugin');
    Route::get('/unplugin/{id}', [WebController::class, 'unplugin'])->name('plugin.unpublish');
    Route::get('/pplugin/{id}', [WebController::class, 'pplugin'])->name('plugin.publish');

    Route::get('logo', [WebController::class, 'logo'])->name('admin.logo');
    Route::post('dark-logo', [WebController::class, 'dark'])->name('dark.logo');
    Route::post('updatefavicon', [WebController::class, 'UpdateFavicon']);
    Route::post('updatepreloader', [WebController::class, 'UpdatePreloader']);

    Route::get('home-page', [WebController::class, 'homepage'])->name('homepage');
    Route::post('home-page/update', [WebController::class, 'Updatehomepage'])->name('homepage.update');
    Route::post('section1/update', [WebController::class, 'section1']);
    Route::post('section2/update', [WebController::class, 'section2']);
    Route::post('section3/update', [WebController::class, 'section3']);
    Route::post('section7/update', [WebController::class, 'section7']);
    Route::post('settlement', [SettingController::class, 'SettlementUpdate'])->name('admin.settlement.update');

    //Withdrawal controller
    Route::get('approvewithdraw/{id}', [TransferController::class, 'approvePayout'])->name('withdraw.approve');
    Route::post('declinewithdraw/{id}', [TransferController::class, 'declinePayout'])->name('withdraw.decline');

    Route::get('py-card', [VirtualcardController::class, 'adminCard'])->name('admin.py.card');
    Route::get('card-transactions', [VirtualcardController::class, 'adminCardTransactions'])->name('admin.card.transactions');
    Route::get('terminate-virtual/{id}', [VirtualcardController::class, 'adminTerminateVirtual'])->name('admin.terminate.virtual');
    Route::get('block-virtual/{id}', [VirtualcardController::class, 'adminBlockVirtual'])->name('admin.block.virtual');
    Route::get('unblock-virtual/{id}', [VirtualcardController::class, 'adminUnblockVirtual'])->name('admin.unblock.virtual');
    Route::get('transactions-virtual/{id}', [VirtualcardController::class, 'admintransactionsVirtual'])->name('admin.transactions.virtual');
    Route::get('user-transactions/{id}', [VirtualcardController::class, 'userTransactions'])->name('user.card.transactions');

    //Setting controller
    Route::get('settings', [SettingController::class, 'Settings'])->name('admin.setting');
    Route::get('email-settings', [SettingController::class, 'Email'])->name('email.setting');
    Route::get('email-template', [SettingController::class, 'Template'])->name('template.setting');
    Route::post('settings', [SettingController::class, 'SettingsUpdate'])->name('admin.settings.update');
    Route::post('email-settings', [SettingController::class, 'EmailUpdate'])->name('admin.email.update');
    Route::post('features', [SettingController::class, 'features'])->name('admin.features.update');
    Route::post('account', [SettingController::class, 'AccountUpdate'])->name('admin.account.update');

    Route::get('payment', [TransferController::class, 'payment'])->name('admin.payment');
    Route::get('delete-link/{id}', [TransferController::class, 'Destroylink'])->name('delete.link');
    Route::get('unlinks/{id}', [TransferController::class, 'unlinks'])->name('links.unpublish');
    Route::get('plinks/{id}', [TransferController::class, 'plinks'])->name('links.publish');
    Route::get('links/{id}', [TransferController::class, 'linkstrans'])->name('admin.linkstrans');
    Route::get('transactions/{id}/{type}', [TransferController::class, 'viewTransactions'])->name('admin.transactions');
    Route::get('transactions', [TransferController::class, 'index'])->name('admin.transactions.all');
    Route::get('payout/transactions', [TransferController::class, 'payout'])->name('admin.payout.all');
    Route::post('transactions/search', [TransferController::class, 'searchTransaction'])->name('admin.transaction.search');
    Route::post('payment/search', [TransferController::class, 'searchPayment'])->name('admin.payment.search');
    Route::post('user/search', [CheckController::class, 'searchUser'])->name('admin.user.search');

    //Language controller
    Route::get('language', [LanguageController::class, 'languages'])->name('admin.language');
    Route::post('language', [LanguageController::class, 'storelanguage'])->name('admin.store.language');
    Route::get('language-delete/{id}', [LanguageController::class, 'deletelanguage'])->name('admin.delete.language');
    Route::get('language-edit/{id}', [LanguageController::class, 'editlanguage'])->name('admin.edit.language');
    Route::post('language-update', [LanguageController::class, 'updatelanguage'])->name('admin.update.language');
    Route::get('unblock-language/{id}', [LanguageController::class, 'Unblocklanguage'])->name('language.unblock');
    Route::get('block-language/{id}', [LanguageController::class, 'Blocklanguage'])->name('language.block');
    //User controller
    Route::get('staff', [CheckController::class, 'Staffs'])->name('admin.staffs');
    Route::get('new-staff', [CheckController::class, 'Newstaff'])->name('new.staff');
    Route::post('new-staff', [CheckController::class, 'Createstaff'])->name('create.staff');
    Route::get('users', [CheckController::class, 'Users'])->name('admin.users');
    Route::get('messages', [CheckController::class, 'Messages'])->name('admin.message');
    Route::get('unblock-staff/{id}', [CheckController::class, 'Unblockstaff'])->name('staff.unblock');
    Route::get('block-staff/{id}', [CheckController::class, 'Blockstaff'])->name('staff.block');
    Route::get('unblock-user/{id}', [CheckController::class, 'Unblockuser'])->name('user.unblock');
    Route::get('block-user/{id}', [CheckController::class, 'Blockuser'])->name('user.block');
    Route::get('read-message/{id}', [CheckController::class, 'Readmessage'])->name('read.message');
    Route::get('unread-message/{id}', [CheckController::class, 'Unreadmessage'])->name('unread.message');
    Route::get('manage-user/{id}', [CheckController::class, 'Manageuser'])->name('user.manage');
    Route::get('transaction-admin-user/{id}', [CheckController::class, 'Transactionuser'])->name('user.transaction.admin');
    Route::get('payment-admin-user/{id}', [CheckController::class, 'Paymentuser'])->name('user.payment.admin');
    Route::get('payout-admin-user/{id}', [CheckController::class, 'Payoutuser'])->name('user.payout.admin');
    Route::get('manage-staff/{id}', [CheckController::class, 'Managestaff'])->name('staff.manage');
    Route::get('user/delete/{id}', [CheckController::class, 'Destroyuser'])->name('user.delete');
    Route::get('staff/delete/{id}', [CheckController::class, 'Destroystaff'])->name('staff.delete');
    Route::get('email/{email}/{name}', [CheckController::class, 'Email'])->name('admin.email');
    Route::post('email_send', [CheckController::class, 'Sendemail'])->name('user.email.send');
    Route::get('promo', [CheckController::class, 'Promo'])->name('admin.promo');
    Route::post('promo', [CheckController::class, 'Sendpromo'])->name('user.promo.send');
    Route::get('message/delete/{id}', [CheckController::class, 'Destroymessage'])->name('message.delete');
    Route::get('ticket', [CheckController::class, 'Ticket'])->name('admin.ticket');
    Route::get('ticket/delete/{id}', [CheckController::class, 'Destroyticket'])->name('ticket.delete');
    Route::get('close-ticket/{id}', [CheckController::class, 'Closeticket'])->name('ticket.close');
    Route::get('manage-ticket/{id}', [CheckController::class, 'Manageticket'])->name('ticket.manage');
    Route::post('reply-ticket', [CheckController::class, 'Replyticket'])->name('ticket.reply');
    Route::post('profile-update/{id}', [CheckController::class, 'Profileupdate'])->name('profile.update');
    Route::post('staff-update', [CheckController::class, 'Staffupdate'])->name('staff.update');
    Route::get('approve-kyc/{id}', [CheckController::class, 'Approvekyc'])->name('admin.approve.kyc');
    Route::get('reject-kyc/{id}', [CheckController::class, 'Rejectkyc'])->name('admin.reject.kyc');
    Route::post('resubmit-kyc/{id}', [CheckController::class, 'Resubmitkyc'])->name('admin.resubmit.kyc');
    Route::post('password', [CheckController::class, 'staffPassword'])->name('staff.password');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/example',[\App\Http\Controllers\ModalTestController::class,
    'index'] );
