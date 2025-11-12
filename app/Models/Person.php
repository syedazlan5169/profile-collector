<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'people';
    protected $fillable = [
        'name',
        'nric',
        'date_of_birth',
        'address',
        'phone',
        'email',
        'gender',
        'rank',
        'pk_number',
        'union_number',
        'department',
        'branch',
        'car',
    ];
    protected $casts = [
        'car' => 'json',
        'date_of_birth' => 'date',
    ];
}
