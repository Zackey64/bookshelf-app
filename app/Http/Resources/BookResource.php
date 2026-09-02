<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    //
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_date' => $this->published_date,
            'image_url' => $this->image_url,
            'description' => $this->description,
            // わざわざGenreResourceつくるの嫌なんでラムダで
            'genres' => $this->whenLoaded(
                'genres',
                fn () => $this->genres->map(fn ($genre) => [
                    'id' => $genre->id,
                    'name' => $genre->name,
                ])
            ),
        ];
    }
}
