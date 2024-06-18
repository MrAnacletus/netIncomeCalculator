<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AFPs extends Model
{
    use HasFactory;
    protected $table = 'afps';
    protected $fillable = ['nombre', 'comision'];
}
