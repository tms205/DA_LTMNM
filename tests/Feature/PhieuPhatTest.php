<?php

namespace Tests\Feature;

use App\Models\PhieuMuon;
use App\Models\PhieuPhat;
use App\Models\Sach;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhieuPhatTest extends TestCase
{
    use RefreshDatabase;

    public function test_tra_sach_qua_han_tu_dong_tao_phieu_phat(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create(['ten_sach' => 'Lão Hạc']);

        $phieuMuon = PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->subDays(10),
            'ngay_tra_du_kien' => today()->subDays(3),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.phieu-muons.tra-sach', $phieuMuon));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('phieu_muons', [
            'id' => $phieuMuon->id,
            'trang_thai' => 'da_tra',
        ]);

        $this->assertDatabaseHas('phieu_phats', [
            'phieu_muon_id' => $phieuMuon->id,
            'user_id' => $user->id,
            'so_ngay_qua_han' => 3,
            'tien_phat_moi_ngay' => 3000,
            'tong_tien_phat' => 9000,
            'trang_thai' => PhieuPhat::TRANG_THAI_CHUA_NOP,
        ]);
    }

    public function test_tra_sach_qua_han_tren_10_ngay_tinh_gia_tang_cuong(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create(['ten_sach' => 'Tắt đèn']);

        $phieuMuon = PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->subDays(25),
            'ngay_tra_du_kien' => today()->subDays(12),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.phieu-muons.tra-sach', $phieuMuon));

        // 12 ngày quá hạn: 10 ngày đầu * 3.000 + 2 ngày vượt * 5.000 = 40.000đ
        $this->assertDatabaseHas('phieu_phats', [
            'phieu_muon_id' => $phieuMuon->id,
            'user_id' => $user->id,
            'so_ngay_qua_han' => 12,
            'tien_phat_moi_ngay' => 5000,
            'tong_tien_phat' => 40000,
        ]);
    }

    public function test_tra_sach_qua_han_tren_20_ngay_chuyen_sang_cam_muon(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create(['ten_sach' => 'Số Đỏ']);

        $phieuMuon = PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->subDays(35),
            'ngay_tra_du_kien' => today()->subDays(21),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.phieu-muons.tra-sach', $phieuMuon));

        // 21 ngày quá hạn: 10 * 3000 + 11 * 5000 = 30000 + 55000 = 85000đ
        $this->assertDatabaseHas('phieu_phats', [
            'phieu_muon_id' => $phieuMuon->id,
            'user_id' => $user->id,
            'so_ngay_qua_han' => 21,
            'tien_phat_moi_ngay' => 5000,
            'tong_tien_phat' => 85000,
        ]);

        $freshUser = $user->fresh();
        $this->assertTrue((bool) $freshUser->khoa_muon_sach);
        $this->assertSame('cam_muon', $freshUser->danhGiaLyLich()['muc_do']);
    }

    public function test_sach_chua_tra_qua_han_khong_sinh_phieu_phat(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        $phieuMuon = PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->subDays(20),
            'ngay_tra_du_kien' => today()->subDays(5),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        // Truy cập danh sách phiếu phạt (trước đây tự quét sinh phiếu phạt)
        $this->actingAs($admin)->get(route('admin.phieu-phats.index'));

        // Chưa trả sách thì KHÔNG sinh phiếu phạt
        $this->assertDatabaseMissing('phieu_phats', [
            'phieu_muon_id' => $phieuMuon->id,
        ]);
    }

    public function test_tra_sach_dung_han_khong_tao_phieu_phat(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->docGia()->create();
        $sach = Sach::factory()->create();

        $phieuMuon = PhieuMuon::factory()->create([
            'user_id' => $user->id,
            'sach_id' => $sach->id,
            'ngay_muon' => today()->subDays(2),
            'ngay_tra_du_kien' => today()->addDays(5),
            'ngay_tra_thuc_te' => null,
            'trang_thai' => 'dang_muon',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.phieu-muons.tra-sach', $phieuMuon));

        $response->assertRedirect();

        $this->assertDatabaseMissing('phieu_phats', [
            'phieu_muon_id' => $phieuMuon->id,
        ]);
    }

    public function test_admin_can_view_phieu_phats_list(): void
    {
        $admin = User::factory()->admin()->create();
        $phieuPhat = PhieuPhat::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.phieu-phats.index'));

        $response->assertOk();
        $response->assertSee('#PP-'.$phieuPhat->id);
        $response->assertSee($phieuPhat->user->name);
    }

    public function test_admin_can_view_single_phieu_phat_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $phieuPhat = PhieuPhat::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.phieu-phats.show', $phieuPhat));

        $response->assertOk();
        $response->assertSee('#PP-'.$phieuPhat->id);
        $response->assertSee(number_format($phieuPhat->tong_tien_phat, 0, ',', '.'));
    }

    public function test_admin_can_confirm_fine_payment(): void
    {
        $admin = User::factory()->admin()->create();
        $phieuPhat = PhieuPhat::factory()->create([
            'trang_thai' => PhieuPhat::TRANG_THAI_CHUA_NOP,
            'ngay_nop' => null,
            'nguoi_thu_id' => null,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.phieu-phats.xac-nhan', $phieuPhat), [
                'ghi_chu' => 'Thu tiền mặt tại quầy',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $phieuPhat->fresh();
        $this->assertSame(PhieuPhat::TRANG_THAI_DA_NOP, $fresh->trang_thai);
        $this->assertSame($admin->id, $fresh->nguoi_thu_id);
        $this->assertNotNull($fresh->ngay_nop);
        $this->assertSame('Thu tiền mặt tại quầy', $fresh->ghi_chu);
    }

    public function test_admin_can_delete_phieu_phat(): void
    {
        $admin = User::factory()->admin()->create();
        $phieuPhat = PhieuPhat::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.phieu-phats.destroy', $phieuPhat));

        $response->assertRedirect(route('admin.phieu-phats.index'));
        $this->assertDatabaseMissing('phieu_phats', [
            'id' => $phieuPhat->id,
        ]);
    }

    public function test_reader_can_view_own_penalties(): void
    {
        $user1 = User::factory()->docGia()->create();
        $user2 = User::factory()->docGia()->create();

        $phieuPhat1 = PhieuPhat::factory()->create(['user_id' => $user1->id]);
        $phieuPhat2 = PhieuPhat::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)
            ->get(route('user.phieu-phats.index'));

        $response->assertOk();
        $response->assertSee('#PP-'.$phieuPhat1->id);
        $response->assertDontSee('#PP-'.$phieuPhat2->id);
    }

    public function test_reader_cannot_access_admin_phieu_phats(): void
    {
        $user = User::factory()->docGia()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.phieu-phats.index'));

        $response->assertForbidden();
    }
}
