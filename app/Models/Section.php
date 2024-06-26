<?php

namespace App\Models;

use App\Models\Standard;
use App\Models\ChapterProgression;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function standards(){
        return $this->belongsToMany(Standard::class)->withPivot('id')->withTimestamps();
    }

    public function chapterProgressions(){
        return $this->hasMany(ChapterProgression::class);
    }
}
