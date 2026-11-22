<?php

use App\Http\Controllers\AsprakController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

// Halaman Utama: Redirect cerdas
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->getDashboardRoute());
    }

    return redirect()->route('login');
});

// Autentikasi Publik
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

// Area Terproteksi Autentikasi
Route::middleware('auth')->group(function () {

    // Fitur Role Switcher (Mahasiswa <-> Asisten Dosen)
    Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('role.switch');
    Route::get('/switch-role/{role}', [AuthController::class, 'switchRoleDirect'])->name('role.switch.direct');

    // 1. Role: Dosen
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->controller(DosenController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::post('/matkul', 'createMatkul')->name('matkul.create');
        Route::post('/asprak/{id}/acc', 'accAsprak')->name('asprak.acc');
        Route::post('/asprak/{id}/reject', 'rejectAsprak')->name('asprak.reject');
        Route::get('/monitoring/{idMatkul}', 'monitoring')->name('monitoring');
        Route::post('/supervisi', 'addSupervisi')->name('supervisi.add');
        Route::delete('/supervisi/{idSupervisi}/{idMatkul}', 'deleteSupervisi')->name('supervisi.delete');
    });

    // 2. Role: Asisten Dosen (Asprak)
    Route::middleware('role:asisten_dosen')->prefix('asprak')->name('asprak.')->controller(AsprakController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::post('/gabung', 'gabungPraktikum')->name('gabung');
        Route::get('/penilaian/{idMatkul}', 'penilaian')->name('penilaian');
        Route::post('/bobot', 'updateBobot')->name('bobot.update');
        Route::post('/nilai', 'simpanNilai')->name('nilai.simpan');
        Route::post('/supervisi/tanggapi', 'tanggapiSupervisi')->name('supervisi.tanggapi');
    });

    // 3. Role: Mahasiswa (Praktikan)
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->controller(MahasiswaController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::post('/join', 'joinKelas')->name('join');
        Route::get('/nilai/{idMatkul}', 'nilai')->name('nilai');
    });

    // 4. Forum & Tugas Praktikum (Akses Lintas Role)
    Route::prefix('forum')->name('forum.')->controller(ForumController::class)->group(function () {
        Route::get('/{idMatkul}', 'index')->name('index');
        Route::post('/post', 'createPost')->name('post.create');
        Route::put('/post/{idPost}', 'updatePost')->name('post.update');
        Route::delete('/post/{idPost}', 'deletePost')->name('post.delete');
        Route::post('/comment', 'addComment')->name('comment.add');
        Route::delete('/comment/{idKomentar}', 'deleteComment')->name('comment.delete');
        Route::post('/lock/{idPost}/{idMatkul}', 'toggleLock')->name('lock.toggle');
        Route::post('/submit', 'submitTugas')->name('tugas.submit');
        Route::put('/submit/{idPengumpulan}', 'updateTugas')->name('tugas.update');
        Route::delete('/submit/{idPengumpulan}', 'deleteTugas')->name('tugas.delete');
        Route::get('/tugas/{idPost}', 'detailTugas')->name('detail_tugas');
        Route::post('/tugas/grade', 'gradeSubmission')->name('tugas.grade');
    });
});
