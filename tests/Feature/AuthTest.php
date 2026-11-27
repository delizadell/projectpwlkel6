<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertDontSee('Masuk Sebagai:');
        $response->assertDontSee('1-Click Demo Akun:');
        $response->assertSee('NPM / NIDN / Username');
    }

    public function test_dosen_can_login_without_specifying_role(): void
    {
        $response = $this->post('/login', [
            'username' => 'dosen1',
            'password' => 'admin',
        ]);

        $response->assertRedirect(route('dosen.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_asprak_can_login_without_specifying_role(): void
    {
        $response = $this->post('/login', [
            'username' => 'asprak1',
            'password' => 'asprak',
        ]);

        $response->assertRedirect(route('asprak.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_mahasiswa_can_login_without_specifying_role(): void
    {
        $response = $this->post('/login', [
            'username' => '230101001',
            'password' => 'mhs',
        ]);

        $response->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_invalid_credentials_cannot_login(): void
    {
        $response = $this->post('/login', [
            'username' => 'dosen1',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_dosen_registration_fails_if_nip_not_18_digits(): void
    {
        $response = $this->post('/register', [
            'nama_lengkap' => 'Dr. Budi Santoso',
            'username' => '1234567890', // hanya 10 digit, harusnya 18 digit
            'role' => 'dosen',
            'password' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    public function test_dosen_registration_succeeds_with_18_digits_nip(): void
    {
        $nip = '198501042012011002';
        User::where('username', $nip)->delete();

        $response = $this->post('/register', [
            'nama_lengkap' => 'Dr. Budi Santoso, M.Kom',
            'username' => $nip,
            'role' => 'dosen',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'username' => $nip,
            'role' => 'dosen',
        ]);
    }

    public function test_mahasiswa_registration_fails_if_npm_not_10_digits(): void
    {
        $response = $this->post('/register', [
            'nama_lengkap' => 'Ahmad Praktikan',
            'username' => '230101', // kurang dari 10 digit
            'role' => 'mahasiswa',
            'password' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    public function test_mahasiswa_registration_fails_without_prodi(): void
    {
        $response = $this->post('/register', [
            'nama_lengkap' => 'Ahmad Praktikan',
            'username' => '2301010098',
            'role' => 'mahasiswa',
            'password' => 'rahasia123',
            // prodi sengaja tidak diisi
        ]);

        $response->assertSessionHasErrors(['prodi']);
    }

    public function test_mahasiswa_registration_succeeds_with_10_digits_npm(): void
    {
        $npm = '2301010099';
        User::where('username', $npm)->delete();

        $response = $this->post('/register', [
            'nama_lengkap' => 'Ahmad Praktikan Baru',
            'username' => $npm,
            'prodi' => 'S1 Sistem Informasi',
            'role' => 'mahasiswa',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'username' => $npm,
            'role' => '(mahasiswa, asisten_dosen)',
            'prodi' => 'S1 Sistem Informasi',
        ]);

        $createdUser = User::where('username', $npm)->first();
        $this->assertEquals('(mahasiswa, asisten_dosen)', $createdUser->getRawRole());
        $this->assertEquals('mahasiswa', $createdUser->getBaseRole());
        $this->assertTrue($createdUser->hasDualRole());
        $this->assertEquals(['mahasiswa', 'asisten_dosen'], $createdUser->getRolesList());
    }

    public function test_asisten_dosen_registration_is_rejected(): void
    {
        $response = $this->post('/register', [
            'nama_lengkap' => 'Calon Asdos',
            'username' => '2301010077',
            'role' => 'asisten_dosen',
            'password' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    public function test_non_dosen_user_can_switch_role_to_asprak_and_mahasiswa(): void
    {
        $user = User::where('username', '230101001')->first();

        // 1. Switch to asisten_dosen
        $response = $this->actingAs($user)->post('/switch-role', [
            'role' => 'asisten_dosen',
        ]);

        $response->assertRedirect(route('asprak.dashboard'));
        $this->assertEquals('asisten_dosen', session('active_role'));

        // 2. Direct get switch back to mahasiswa
        $response2 = $this->actingAs($user)->get('/switch-role/mahasiswa');
        $response2->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertEquals('mahasiswa', session('active_role'));
    }

    public function test_dosen_cannot_switch_role(): void
    {
        $dosen = User::where('role', 'dosen')->first();

        $response = $this->actingAs($dosen)->post('/switch-role', [
            'role' => 'asisten_dosen',
        ]);

        $response->assertSessionHas('error');
        $this->assertNull(session('active_role'));
    }

    public function test_role_middleware_auto_switches_role_for_non_dosen(): void
    {
        $user = User::where('username', '230101001')->first();

        // User is mahasiswa base, accessing asprak dashboard triggers auto-switch
        $response = $this->actingAs($user)->get('/asprak');
        $response->assertStatus(200);
        $this->assertEquals('asisten_dosen', session('active_role'));

        // Accessing mahasiswa dashboard triggers auto-switch
        $response2 = $this->actingAs($user)->get('/mahasiswa');
        $response2->assertStatus(200);
        $this->assertEquals('mahasiswa', session('active_role'));
    }
}
