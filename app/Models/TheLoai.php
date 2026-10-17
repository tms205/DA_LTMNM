<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TheLoai extends Model
{
    use HasFactory;

    protected $table = 'the_loais';

    protected $fillable = [
        'ten_the_loai',
        'mo_ta',
    ];

    public function saches(): HasMany
    {
        return $this->hasMany(Sach::class);
    }
}
