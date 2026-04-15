<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentAnnotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'annotation_type',
        'start_position',
        'end_position',
        'selected_text',
        'note',
        'color',
        'position_data',
        'link_url',
        'image_path',
        'is_visible',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_position' => 'integer',
            'end_position' => 'integer',
            'position_data' => 'array',
            'is_visible' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('annotation_type', $type);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }

        return '';
    }

    public function getPositionData(): array
    {
        return $this->position_data ?? [
            'x' => 0,
            'y' => 0,
            'width' => 0,
            'height' => 0,
        ];
    }

    public function updatePosition(int $x, int $y, int $width = 0, int $height = 0)
    {
        $this->position_data = compact('x', 'y', 'width', 'height');
        $this->save();
    }
}
