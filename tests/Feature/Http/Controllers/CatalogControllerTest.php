<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Sach;
use App\Models\TacGia;
use App\Models\TheLoai;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CatalogControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_search_returns_book_matching_author_name(): void
    {
        $nguyenDu = TacGia::factory()->create(['ten_tac_gia' => 'Nguyễn Du']);
        $truyenKieu = Sach::factory()->create(['ten_sach' => 'Truyện Kiều']);
        $truyenKieu->tacGias()->attach($nguyenDu);
        $otherBook = Sach::factory()->create(['ten_sach' => 'Sách khoa học']);

        $response = $this->get(route('catalog.index', ['q' => 'Nguyễn Du']));

        $response->assertOk()
            ->assertSeeText('Truyện Kiều')
            ->assertDontSeeText($otherBook->ten_sach);
    }

    public function test_ajax_search_returns_rendered_results_and_total(): void
    {
        Sach::factory()->create(['ten_sach' => 'Mắt biếc']);
        Sach::factory()->create(['ten_sach' => 'Sách khác']);

        $response = $this->getJson(route('catalog.index', ['q' => 'Mắt biếc']));

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Mắt biếc'));
    }

    public function test_search_finds_book_from_a_later_unfiltered_page(): void
    {
        $laterPageBook = Sach::factory()->create(['ten_sach' => 'Tác phẩm nằm ở trang sau']);
        Sach::factory()->count(9)->create();

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertDontSeeText($laterPageBook->ten_sach);

        $this->getJson(route('catalog.index', [
            'q' => 'Tác phẩm nằm ở trang sau',
        ]))
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('html', fn (string $html): bool => str_contains($html, $laterPageBook->ten_sach));
    }

    public function test_catalog_paginates_books_nine_per_page(): void
    {
        Sach::factory()->count(10)->create();

        $response = $this->get(route('catalog.index'));

        $response->assertOk()
            ->assertViewHas('saches', fn ($saches): bool => $saches->count() === 9 && $saches->total() === 10);
    }

    public function test_catalog_escapes_untrusted_book_name(): void
    {
        $dangerousName = '<script>alert("xss")</script>';
        Sach::factory()->create(['ten_sach' => $dangerousName]);

        $response = $this->get(route('catalog.index'));

        $response->assertOk()
            ->assertSee($dangerousName)
            ->assertDontSee($dangerousName, false);
    }

    public function test_search_returns_books_matching_category_name(): void
    {
        $theLoai = TheLoai::factory()->create(['ten_the_loai' => 'Khoa học viễn tưởng']);
        $book = Sach::factory()->create(['the_loai_id' => $theLoai->id, 'ten_sach' => 'Vũ trụ huyền bí']);
        $otherBook = Sach::factory()->create(['ten_sach' => 'Truyện cổ tích']);

        $response = $this->getJson(route('catalog.index', ['q' => 'Khoa học']));

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Vũ trụ huyền bí'));
    }

    public function test_search_supports_multi_word_title_and_author(): void
    {
        $author = TacGia::factory()->create(['ten_tac_gia' => 'Nguyễn Nhật Ánh']);
        $book1 = Sach::factory()->create(['ten_sach' => 'Mắt biếc']);
        $book1->tacGias()->attach($author);

        $book2 = Sach::factory()->create(['ten_sach' => 'Kính vạn hoa']);
        $book2->tacGias()->attach($author);

        $response = $this->getJson(route('catalog.index', ['q' => 'Mắt biếc Nguyễn Nhật Ánh']));

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Mắt biếc') && ! str_contains($html, 'Kính vạn hoa'));
    }

    public function test_search_supports_vietnamese_d_stroke_variants(): void
    {
        Sach::factory()->create(['ten_sach' => 'Dan gian']);

        $response = $this->getJson(route('catalog.index', ['q' => 'Đan gian']));

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Dan gian'));
    }

    public function test_search_supports_nfd_decomposed_vietnamese_characters(): void
    {
        Sach::factory()->create(['ten_sach' => 'Lão Hạc']);

        $nfdLao = class_exists(\Normalizer::class)
            ? (\Normalizer::normalize('lão', \Normalizer::FORM_D) ?: 'lão')
            : 'lão';

        $response = $this->getJson(route('catalog.index', ['q' => $nfdLao]));

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Lão Hạc'));
    }
}
