<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Touhidurabir\ModelSoftCascade\HasSoftCascade;

class Album extends Model
{
    use HasFactory, SoftDeletes, HasSoftCascade;

    protected $fillable = [
        'user_id',
        'title',
        'deletedByCascade'
    ];

    public function cascadable() : array {

        return [
            'media'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}