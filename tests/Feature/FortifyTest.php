<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FortifyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_正しい認証情報でログインできる(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        // Act
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        // Assert
        $this->assertAuthenticated();
    }

    /** @test */
    public function login_誤った認証情報ではログインできない(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        // Act
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);
        // Assert
        $this->assertGuest();
    }

    /** @test */
    public function logout_ログアウトできる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        // Act
        $response = $this->post('/logout');
        // Assert
        $this->assertGuest();
    }

    /** @test */
    public function register_ユーザーを登録できる(): void
    {
        // Arrange
        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        // Act
        $response = $this->post('/register', $data);
        // Assert
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}
