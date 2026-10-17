<?php

namespace Tests\Feature;

use App\Models\PhieuMuon;
use App\Models\PhieuPhat;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_reader_has_binh_thuong_credit_status(): void
    {
        $user = User::factory()->docGia()->create();

        $eval = $user->danhGiaLyLich();

        $this->assertSame('binh_thuong', $eval['muc_do']);
        $this->assertTrue($eval['co_the_muon']);
        $this->assertNull($eval['ly_do_khong_the_muon']);
    }

    public function test_reader_with_clean_history_has_uy_tin_status(): void
    {
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        // 3 returned loans on time, 0 fines
        for ($i = 0; $i < 3; $i++) {
            PhieuMuon::factory()->create([
                'user_id' => $user->id,
                'sach_id' => $sach->id,
                'ngay_muon' => today()->subDays(20 - $i * 5),
                'ngay_tra_du_kien' => today()->subDays(10 - $i * 5),
                'ngay_tra_thuc_te' => today()->subDays(12 - $i * 5),
                'trang_thai' => 'da_tra',
            ]);
        }

        $eval = $user->danhGiaLyLich();

        $this->assertSame('uy_tin', $eval['muc_do']);
        $this->assertTrue($eval['co_the_muon']);
    }

    public function test_reader_with_returned_on_time_loans_has_binh_thuong_status(): void
    {
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        // 3 returned loans on the exact due date (not early)
        for ($i = 0; $i < 3; $i++) {
            PhieuMuon::factory()->create([
                'user_id' => $user->id,
                'sach_id' => $sach->id,
                'ngay_muon' => today()->subDays(20 - $i * 5),
                'ngay_tra_du_kien' => today()->subDays(10 - $i * 5),
                'ngay_tra_thuc_te' => today()->subDays(10 - $i * 5),
                'trang_thai' => 'da_tra',
            ]);
        }

        $eval = $user->danhGiaLyLich();

        $this->assertSame('binh_thuong', $eval['muc_do']);
        $this->assertTrue($eval['co_the_muon']);
    }

    public function test_reader_with_overdue_loan_1_to_4_days_has_canh_bao_status(): void
    {
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->subDays(10),
            'ngay_tra_du_kien' => today()->subDays(3), // 3 days overdue (1 - 4 days)
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        $eval = $user->danhGiaLyLich();

        $this->assertSame('canh_bao', $eval['muc_do']);
        $this->assertTrue($eval['co_the_muon']);
        $this->assertNull($eval['ly_do_khong_the_muon']);
    }

    public function test_reader_with_overdue_loan_has_vi_pham_status_and_cannot_borrow(): void
    {
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->subDays(20),
            'ngay_tra_du_kien' => today()->subDays(5),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        $eval = $user->danhGiaLyLich();

        $this->assertSame('vi_pham', $eval['muc_do']);
        $this->assertFalse($eval['co_the_muon']);
        $this->assertNotNull($eval['ly_do_khong_the_muon']);
    }

    public function test_reader_with_unpaid_fine_cannot_borrow(): void
    {
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        $loan = PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_tra_thuc_te' => today(),
            'trang_thai' => 'da_tra',
        ]);

        PhieuPhat::factory()->create([
            'phieu_muon_id' => $loan->id,
            'user_id' => $user->id,
            'trang_thai' => PhieuPhat::TRANG_THAI_CHUA_NOP,
            'tong_tien_phat' => 15000,
        ]);

        $eval = $user->danhGiaLyLich();

        $this->assertSame('vi_pham', $eval['muc_do']);
        $this->assertFalse($eval['co_the_muon']);
    }

    public function test_admin_cannot_create_loan_for_violating_reader(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create();
        $sach1 = Sach::factory()->create(['so_luong' => 5]);
        $sach2 = Sach::factory()->create(['so_luong' => 5]);

        // Existing overdue loan
        PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach1->id,
            'ngay_muon' => today()->subDays(20),
            'ngay_tra_du_kien' => today()->subDays(5),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        // Attempt to create a new loan for this user
        $response = $this->actingAs($admin)->post(route('admin.phieu-muons.store'), [
            'user_id' => $user->id,
            'sach_id' => $sach2->id,
            'ngay_muon' => today()->format('Y-m-d'),
            'ngay_tra_du_kien' => today()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['user_id']);
        $this->assertDatabaseMissing('phieu_muons', [
            'user_id' => $user->id,
            'sach_id' => $sach2->id,
        ]);
    }

    public function test_admin_can_view_users_list_and_filter_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create(['name' => 'Nguyễn Độc Giả Mới']);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'muc_do' => 'binh_thuong',
        ]));

        $response->assertOk();
        $response->assertSee('Nguyễn Độc Giả Mới');
        $response->assertSee('Bình thường');
    }

    public function test_admin_user_list_displays_complete_address_and_librarian_note(): void
    {
        $admin = User::factory()->admin()->create();
        $address = '300 Xô Viết Nghệ Tĩnh, Phường 21, Quận Bình Thạnh, Thành phố Hồ Chí Minh';
        $note = 'Đã trả sách sau khi trễ hạn, cần nhắc kiểm tra hạn trả trong lần mượn tiếp theo.';
        User::factory()->docGia()->create([
            'dia_chi' => $address,
            'ghi_chu_thu_thu' => $note,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk()
            ->assertSeeText($address)
            ->assertSeeText($note)
            ->assertSee('admin-users-table', false)
            ->assertSee('admin-user-address', false)
            ->assertSee('admin-user-note', false)
            ->assertDontSee('class="small text-muted text-truncate" style="max-width: 220px;"', false)
            ->assertDontSee('border-warning-subtle text-truncate" style="max-width: 180px;"', false);
    }

    public function test_admin_can_view_user_detail_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create(['name' => 'Trần Văn Minh']);

        $response = $this->actingAs($admin)->get(route('admin.users.show', $user));

        $response->assertOk();
        $response->assertSee('Trần Văn Minh');
        $response->assertSee('Hồ sơ lý lịch');
    }

    public function test_admin_can_update_librarian_note_and_lock_borrowing(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.update-note', $user), [
            'ghi_chu_thu_thu' => 'Độc giả mượn sách cần chú ý giữ gìn bìa',
            'khoa_muon_sach' => 1,
            'ly_do_khoa' => 'Tạm khóa để đối soát sách cũ',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $user->fresh();
        $this->assertSame('Độc giả mượn sách cần chú ý giữ gìn bìa', $fresh->ghi_chu_thu_thu);
        $this->assertTrue($fresh->khoa_muon_sach);
        $this->assertSame('Tạm khóa để đối soát sách cũ', $fresh->ly_do_khoa);

        // Verification: credit rating is now cam_muon
        $eval = $fresh->danhGiaLyLich();
        $this->assertSame('cam_muon', $eval['muc_do']);
        $this->assertFalse($eval['co_the_muon']);
    }

    public function test_reader_with_loan_overdue_more_than_20_days_has_cam_muon_status(): void
    {
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->subDays(40),
            'ngay_tra_du_kien' => today()->subDays(25),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        $eval = $user->danhGiaLyLich();

        $this->assertSame('cam_muon', $eval['muc_do']);
        $this->assertFalse($eval['co_the_muon']);
        $this->assertStringContainsString('CẤM MƯỢN', $eval['ly_do_khong_the_muon']);
    }

    public function test_regular_user_cannot_access_admin_user_management(): void
    {
        $user = User::factory()->docGia()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertForbidden();
    }
}
