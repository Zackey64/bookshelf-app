<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        // Act
        $response = $this->actingAs($user)->post(route('genres.store'), []);
        // Assert
        $response->assertSessionHasErrors([
            'name',
        ]);
    }

    /** @test */
    public function store_他のジャンルと同じ名前の場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $data = [
            'name' => $genre->name,
        ];
        // Act
        $response = $this->actingAs($user)->post(route('genres.store'), $data);
        // Assert
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function update_必須項目がない場合(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        // Act
        $response = $this->actingAs($user)->put(route('genres.update', $genre), []);
        // Assert
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function update_他のジャンルと同じ名前の場合はエラー(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $otherGenre = Genre::factory()->create();
        $data = [
            'name' => $otherGenre->name,
        ];
        // Act
        $response = $this->actingAs($user)->put(route('genres.update', $genre), $data);
        // Assert
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function update_元のジャンルと同じ名前の場合はエラーにならない(): void
    {
        // Arrange
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $data = [
            'name' => $genre->name,
        ];
        // Act
        $response = $this->actingAs($user)->put(route('genres.update', $genre), $data);
        // Assert
        $response->assertSessionDoesntHaveErrors();
    }
}
