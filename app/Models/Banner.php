<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'image_url',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    /**
     * Get the public URL for the banner image, falling back to default.
     */
    public function getImageAttribute(): string
    {
        if ($this->image_url && Storage::disk('public')->exists($this->image_url)) {
            return Storage::disk('public')->url($this->image_url);
        }
        return asset('storage/default.jpeg');
    }
}
