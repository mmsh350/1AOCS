<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'title',
        'message',
    ];

    protected $dates = ['deleted_at'];
}
