<?php

namespace App\Models;

use App\Traits\IsNftRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nft extends Model
{
    use HasFactory, IsNftRecord;

    protected $guarded = [];
}
