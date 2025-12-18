<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ThematicAreaController;
use App\Http\Controllers\Admin\CongressController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\EditorialController;
use App\Http\Controllers\Admin\PlagiarismController;
use App\Http\Controllers\Admin\PaperManagementController;
use App\Http\Controllers\Congress\MilestoneController;
use App\Http\Controllers\Congress\FeeController;
use App\Http\Controllers\Congress\CouponController;
use App\Http\Controllers\Paper\PaperController;
use App\Http\Controllers\Paper\CoauthorController;
use App\Http\Controllers\Paper\PaperFileController;
use App\Http\Controllers\Review\ReviewController;
use App\Http\Controllers\Review\ReviewAssignmentController;
use App\Http\Controllers\Registration\RegistrationController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Congress\BlogPostController;
use App\Http\Controllers\Congress\EmailCampaignController;
use App\Http\Controllers\Congress\SymposiumController;
use App\Http\Controllers\VirtualSessionController;
use App\Http\Controllers\SessionCommentController;
use App\Http\Controllers\Certificate\CertificateController;
use App\Http\Controllers\Admin\CertificateTemplateController;
use App\Http\Controllers\Admin\BookOfAbstractsController;
use App\Http\Controllers\Admin\EditorialDownloadController;
use App\Http\Controllers\User\BillingDataController;
use App\Http\Controllers\Public\CongressController as PublicCongressController;
use App\Http\Controllers\Public\NewsletterController;
use App\Http\Controllers\Public\EventoController;
use App\Http\Controllers\Public\PonenteController;
use App\Http\Controllers\Public\PatrocinadorController;
use App\Http\Controllers\Public\NoticiaController;
use App\Http\Controllers\Public\ContactoController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [PublicCongressController::class, 'home'])->name('home');
Route::get('/congresos', [PublicCongressController::class, 'index'])->name('public.congresses.index');
Route::get('/congresos/{slug}', [PublicCongressController::class, 'show'])->name('public.congresses.show');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.subscribe');

// Rutas del sitio público
Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
Route::get('/ponentes', [PonenteController::class, 'index'])->name('ponentes.index');
Route::get('/ponentes/{speaker}', [PonenteController::class, 'show'])->name('ponentes.show');
Route::get('/patrocinadores', [PatrocinadorController::class, 'index'])->name('patrocinadores.index');
Route::get('/patrocinadores/{sponsor}', [PatrocinadorController::class, 'show'])->name('patrocinadores.show');
Route::get('/patrocinadores', [PatrocinadorController::class, 'index'])->name('patrocinadores.index');
Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias.index');
Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas de administración (requieren autenticación)
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Redirigir dashboard antiguo al nuevo
    Route::get('/dashboard-old', function() {
        return redirect()->route('admin.dashboard');
    });
    
    // Áreas temáticas
    Route::resource('thematic-areas', ThematicAreaController::class);
    
    // Congresos
    Route::resource('congresses', CongressController::class);
    
    // Editoriales
    Route::resource('editorials', EditorialController::class);
    
    // Gestión de papers (solo para admins)
    Route::get('congresses/{congress}/papers', [PaperManagementController::class, 'index'])->name('congresses.papers.index');
    Route::post('congresses/{congress}/papers/{paper}/accept', [PaperManagementController::class, 'accept'])->name('congresses.papers.accept');
    Route::post('congresses/{congress}/papers/{paper}/reject', [PaperManagementController::class, 'reject'])->name('congresses.papers.reject');
    Route::post('congresses/{congress}/papers/{paper}/revision', [PaperManagementController::class, 'requestRevision'])->name('congresses.papers.revision');
    
    // Transacciones y reportes contables (solo para admins)
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::get('congresses/{congress}/transactions', [TransactionController::class, 'index'])->name('congresses.transactions.index');
    Route::get('congresses/{congress}/transactions/export', [TransactionController::class, 'export'])->name('congresses.transactions.export');
    
    // Blog del evento (solo para admins)
    Route::get('congresses/{congress}/blog', [BlogPostController::class, 'index'])->name('congresses.blog.index');
    Route::get('congresses/{congress}/blog/create', [BlogPostController::class, 'create'])->name('congresses.blog.create');
    Route::post('congresses/{congress}/blog', [BlogPostController::class, 'store'])->name('congresses.blog.store');
    Route::get('congresses/{congress}/blog/{post}/edit', [BlogPostController::class, 'edit'])->name('congresses.blog.edit');
    Route::put('congresses/{congress}/blog/{post}', [BlogPostController::class, 'update'])->name('congresses.blog.update');
    Route::delete('congresses/{congress}/blog/{post}', [BlogPostController::class, 'destroy'])->name('congresses.blog.destroy');
    
    // Simposios (solo para admins)
    Route::get('congresses/{congress}/symposia', [SymposiumController::class, 'index'])->name('congresses.symposia.index');
    Route::get('congresses/{congress}/symposia/create', [SymposiumController::class, 'create'])->name('congresses.symposia.create');
    Route::post('congresses/{congress}/symposia', [SymposiumController::class, 'store'])->name('congresses.symposia.store');
    Route::get('congresses/{congress}/symposia/{symposium}/edit', [SymposiumController::class, 'edit'])->name('congresses.symposia.edit');
    Route::put('congresses/{congress}/symposia/{symposium}', [SymposiumController::class, 'update'])->name('congresses.symposia.update');
    Route::delete('congresses/{congress}/symposia/{symposium}', [SymposiumController::class, 'destroy'])->name('congresses.symposia.destroy');
    
    // Sesiones virtuales (solo para admins)
    Route::get('congresses/{congress}/virtual-sessions', [VirtualSessionController::class, 'index'])->name('congresses.virtual-sessions.index');
    Route::get('congresses/{congress}/virtual-sessions/create', [VirtualSessionController::class, 'create'])->name('congresses.virtual-sessions.create');
    Route::post('congresses/{congress}/virtual-sessions', [VirtualSessionController::class, 'store'])->name('congresses.virtual-sessions.store');
    Route::get('congresses/{congress}/virtual-sessions/{session}/edit', [VirtualSessionController::class, 'edit'])->name('congresses.virtual-sessions.edit');
    Route::put('congresses/{congress}/virtual-sessions/{session}', [VirtualSessionController::class, 'update'])->name('congresses.virtual-sessions.update');
    Route::delete('congresses/{congress}/virtual-sessions/{session}', [VirtualSessionController::class, 'destroy'])->name('congresses.virtual-sessions.destroy');
    Route::post('congresses/{congress}/virtual-sessions/{session}/start', [VirtualSessionController::class, 'start'])->name('congresses.virtual-sessions.start');
    Route::post('congresses/{congress}/virtual-sessions/{session}/end', [VirtualSessionController::class, 'end'])->name('congresses.virtual-sessions.end');
    
    // Plantillas de certificados (solo para admins)
    Route::resource('certificate-templates', CertificateTemplateController::class);
    
    // Libro de resúmenes (solo para admins)
    Route::get('congresses/{congress}/book-of-abstracts', [BookOfAbstractsController::class, 'index'])->name('congresses.book-of-abstracts.index');
    Route::post('congresses/{congress}/book-of-abstracts/generate', [BookOfAbstractsController::class, 'generate'])->name('congresses.book-of-abstracts.generate');
    Route::get('congresses/{congress}/book-of-abstracts/{book}', [BookOfAbstractsController::class, 'show'])->name('congresses.book-of-abstracts.show');
    Route::get('congresses/{congress}/book-of-abstracts/{book}/download', [BookOfAbstractsController::class, 'download'])->name('congresses.book-of-abstracts.download');
    
    // Descargas editoriales (solo para admins)
    Route::post('congresses/{congress}/editorials/{editorial}/download/generate', [EditorialDownloadController::class, 'generate'])->name('congresses.editorials.download.generate');
    Route::get('congresses/{congress}/editorials/{editorial}/downloads/{download}', [EditorialDownloadController::class, 'show'])->name('congresses.editorials.downloads.show');
    Route::get('congresses/{congress}/editorials/{editorial}/downloads/{download}/download', [EditorialDownloadController::class, 'download'])->name('congresses.editorials.downloads.download');
    
    // Suplantación (solo para admins)
    Route::post('/impersonate/{user}', [ImpersonationController::class, 'start'])->name('impersonate.start');
    Route::post('/impersonate/stop', [ImpersonationController::class, 'stop'])->name('impersonate.stop');
    Route::get('/impersonate/logs', [ImpersonationController::class, 'logs'])->name('impersonate.logs');
    
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de configuración del congreso (requieren autenticación y ser admin del congreso)
Route::middleware(['auth', 'verified'])->prefix('congress/{congress}')->name('congress.')->group(function () {
    // Milestones (Hitos)
    Route::resource('milestones', MilestoneController::class);
    
    // Fees (Tarifas)
    Route::resource('fees', FeeController::class);
    
    // Coupons (Cupones)
    Route::resource('coupons', CouponController::class);
    Route::post('coupons/validate', [CouponController::class, 'validate'])->name('coupons.validate');
    
    // Papers (Propuestas/Ponencias)
    Route::resource('papers', PaperController::class);
    Route::post('papers/{paper}/submit', [PaperController::class, 'submit'])->name('papers.submit');
    
    // Coautores
    Route::post('papers/{paper}/coauthors', [CoauthorController::class, 'store'])->name('papers.coauthors.store');
    Route::delete('papers/{paper}/coauthors/{coauthor}', [CoauthorController::class, 'destroy'])->name('papers.coauthors.destroy');
    Route::get('coauthors/search', [CoauthorController::class, 'search'])->name('coauthors.search');
    
    // Archivos de papers
    Route::post('papers/{paper}/files', [PaperFileController::class, 'store'])->name('papers.files.store');
    Route::delete('papers/{paper}/files/{file}', [PaperFileController::class, 'destroy'])->name('papers.files.destroy');
    Route::get('papers/{paper}/files/{file}/download', [PaperFileController::class, 'download'])->name('papers.files.download');
    
    // Revisión de papers
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('papers/{paper}/review/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::get('papers/{paper}/review/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('papers/{paper}/review/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    
    // Asignación de revisores
    Route::get('review-assignments', [ReviewAssignmentController::class, 'index'])->name('review-assignments.index');
    Route::post('papers/{paper}/review-assignments', [ReviewAssignmentController::class, 'store'])->name('review-assignments.store');
    Route::post('papers/{paper}/review-assignments/auto', [ReviewAssignmentController::class, 'assignAuto'])->name('review-assignments.assign-auto');
    Route::get('papers/{paper}/review-assignments/suggest', [ReviewAssignmentController::class, 'suggestReviewers'])->name('review-assignments.suggest');
    Route::post('review-assignments/{assignment}/accept', [ReviewAssignmentController::class, 'accept'])->name('review-assignments.accept');
    Route::post('review-assignments/{assignment}/reject', [ReviewAssignmentController::class, 'reject'])->name('review-assignments.reject');
    Route::delete('review-assignments/{assignment}', [ReviewAssignmentController::class, 'destroy'])->name('review-assignments.destroy');
    
    // Plagio
    Route::post('papers/{paper}/plagiarism/mark', [PlagiarismController::class, 'mark'])->name('papers.plagiarism.mark');
    Route::post('papers/{paper}/plagiarism/unmark', [PlagiarismController::class, 'unmark'])->name('papers.plagiarism.unmark');
    
    // Simposios
    Route::get('symposia', [SymposiumController::class, 'index'])->name('symposia.index');
    Route::get('symposia/{symposium}', [SymposiumController::class, 'show'])->name('symposia.show');
    
    // Sesiones virtuales
    Route::get('agenda', [\App\Http\Controllers\AgendaController::class, 'index'])->name('agenda');
    Route::get('virtual-sessions', [VirtualSessionController::class, 'index'])->name('virtual-sessions.index');
    Route::get('virtual-sessions/{session}', [VirtualSessionController::class, 'show'])->name('virtual-sessions.show');
    
    // Comentarios en sesiones
    Route::post('virtual-sessions/{session}/comments', [SessionCommentController::class, 'store'])->name('session-comments.store');
    Route::put('virtual-sessions/{session}/comments/{comment}', [SessionCommentController::class, 'update'])->name('session-comments.update');
    Route::delete('virtual-sessions/{session}/comments/{comment}', [SessionCommentController::class, 'destroy'])->name('session-comments.destroy');
    Route::post('virtual-sessions/{session}/comments/{comment}/answered', [SessionCommentController::class, 'markAnswered'])->name('session-comments.answered');
    Route::post('virtual-sessions/{session}/comments/{comment}/like', [SessionCommentController::class, 'like'])->name('session-comments.like');
    
    // Inscripciones
    Route::get('register', [RegistrationController::class, 'create'])->name('register');
    Route::post('register', [RegistrationController::class, 'store'])->name('register.store');
    Route::get('registration/{registration}', [RegistrationController::class, 'show'])->name('registration.show');
    
    // Pagos
    Route::get('registration/{registration}/payment', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('registration/{registration}/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('payment/{payment}', [PaymentController::class, 'show'])->name('payment.show');
    Route::get('payment/{payment}/receipt', [PaymentController::class, 'generateReceipt'])->name('payment.receipt');
    Route::get('payment/{payment}/receipt/download', [PaymentController::class, 'downloadReceipt'])->name('payment.receipt.download');
    Route::post('payment/webhook/{provider}', [PaymentController::class, 'webhook'])->name('payment.webhook');
    
    // Blog del evento
    Route::get('blog', [BlogPostController::class, 'index'])->name('blog.index');
    Route::get('blog/{post}', [BlogPostController::class, 'show'])->name('blog.show');
    
    // Campañas de email (solo admins)
    Route::get('email-campaigns', [EmailCampaignController::class, 'index'])->name('email-campaigns.index');
    Route::get('email-campaigns/create', [EmailCampaignController::class, 'create'])->name('email-campaigns.create');
    Route::post('email-campaigns', [EmailCampaignController::class, 'store'])->name('email-campaigns.store');
    Route::get('email-campaigns/{campaign}', [EmailCampaignController::class, 'show'])->name('email-campaigns.show');
    Route::post('email-campaigns/{campaign}/send', [EmailCampaignController::class, 'send'])->name('email-campaigns.send');
    Route::get('email-campaigns/{campaign}/stats', [EmailCampaignController::class, 'updateStats'])->name('email-campaigns.stats');
    
    // Certificados
    Route::post('certificate/validate', [CertificateController::class, 'validateAndGenerate'])->name('certificate.validate');
    Route::get('certificate/{certificate}', [CertificateController::class, 'show'])->name('certificate.show');
    Route::get('certificate/{certificate}/download', [CertificateController::class, 'download'])->name('certificate.download');
});

// Rutas de usuario (requieren autenticación)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard para usuarios regulares
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    // Datos de facturación
    Route::resource('billing-data', BillingDataController::class);
});

// Botón para dejar de suplantar (accesible cuando se está suplantando)
Route::middleware('auth')->post('/stop-impersonate', [ImpersonationController::class, 'stop'])->name('stop-impersonate');
