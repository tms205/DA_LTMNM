<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TacGia extends Model
{
    use HasFactory;

    protected $table = 'tac_gias';

    protected $fillable = [
        'ten_tac_gia',
        'nam_sinh',
        'quoc_tich',
        'tieu_su',
    ];

    public function saches(): BelongsToMany
    {
        return $this->belongsToMany(Sach::class, 'sach_tac_gia');
    }
}
