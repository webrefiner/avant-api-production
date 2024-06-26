<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubjectScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_subject_id',
        'user_id',
        'marks_secured',
        'exam_subject_state_id',
        'evaluated_by',
    ];

    public function examSubject(){
        return $this->belongsTo(ExamSubject::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function examSubjectState(){
        return $this->belongsTo(ExamSubjectState::class);
    }
}
