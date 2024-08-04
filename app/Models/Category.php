<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Category extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        "name",
    ];
    public $incrementing = false;
    protected $keyType = "string";
}