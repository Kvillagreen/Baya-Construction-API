<?php

namespace App\Models\Documents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Document extends Model
{
    use HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'document_type',
        'disk',
        'storage_path',
        'original_name',
        'mime_type',
        'size_bytes',
        'uploaded_by',
    ];
}
