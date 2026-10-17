<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sach extends Model
{
    use HasFactory;

    protected $table = 'saches';

    protected $fillable = [
        'ten_sach',
        'the_loai_id',
        'nam_xuat_ban',
        'nha_xuat_ban',
        'so_luong',
        'hinh_anh',
        'mo_ta',
    ];

    public function theLoai(): BelongsTo
    {
        return $this->belongsTo(TheLoai::class);
    }

    public function tacGias(): BelongsToMany
    {
        return $this->belongsToMany(TacGia::class, 'sach_tac_gia');
    }

    public function phieuMuons(): HasMany
    {
        return $this->hasMany(PhieuMuon::class);
    }

    public function phieuMuonsDangMuon(): HasMany
    {
        return $this->phieuMuons()->whereIn('trang_thai', ['dang_muon', 'qua_han']);
    }

    public function scopeTimKiem(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);

        if (class_exists(\Normalizer::class)) {
            $keyword = \Normalizer::normalize($keyword, \Normalizer::FORM_C) ?: $keyword;
        }

        if ($keyword === '') {
            return $query;
        }

        $words = array_values(array_filter(preg_split('/\s+/', $keyword) ?: []));

        return $query->where(function (Builder $query) use ($keyword, $words): void {
            $query->where(function (Builder $subQuery) use ($keyword): void {
                $this->applySearchTerm($subQuery, $keyword);
            });

            if (count($words) > 1) {
                $query->orWhere(function (Builder $subQuery) use ($words): void {
                    foreach ($words as $word) {
                        $subQuery->where(function (Builder $wordQuery) use ($word): void {
                            $this->applySearchTerm($wordQuery, $word);
                        });
                    }
                });
            }
        });
    }

    protected function applySearchTerm(Builder $query, string $term): void
    {
        $term = trim($term);
        if ($term === '') {
            return;
        }

        $nfc = class_exists(\Normalizer::class) ? (\Normalizer::normalize($term, \Normalizer::FORM_C) ?: $term) : $term;
        $nfd = class_exists(\Normalizer::class) ? (\Normalizer::normalize($term, \Normalizer::FORM_D) ?: $term) : $term;

        $baseTerms = array_unique(array_filter([$term, $nfc, $nfd]));
        $variants = [];
        foreach ($baseTerms as $bt) {
            $variants[] = $bt;
            $variants[] = str_replace(['đ', 'Đ'], ['d', 'D'], $bt);
            $variants[] = str_replace(['d', 'D'], ['đ', 'Đ'], $bt);
        }

        $variants = array_values(array_unique(array_filter($variants)));

        $query->where(function (Builder $subQuery) use ($variants): void {
            foreach ($variants as $variant) {
                $subQuery->orWhere('ten_sach', 'like', "%{$variant}%")
                    ->orWhere('nha_xuat_ban', 'like', "%{$variant}%")
                    ->orWhereHas('theLoai', fn (Builder $q) => $q->where('ten_the_loai', 'like', "%{$variant}%"))
                    ->orWhereHas('tacGias', fn (Builder $q) => $q->where('ten_tac_gia', 'like', "%{$variant}%"));
            }
        });
    }

    public function getSoLuongConLaiAttribute(): int
    {
        $dangMuon = array_key_exists('dang_muon_count', $this->attributes)
            ? (int) $this->attributes['dang_muon_count']
            : $this->phieuMuonsDangMuon()->count();

        return max(0, $this->so_luong - $dangMuon);
    }
}
