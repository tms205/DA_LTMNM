<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP kích hoạt tài khoản - Quản Lý Thư Viện</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.6;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 40px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(15, 39, 68, 0.1), 0 8px 10px -6px rgba(15, 39, 68, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f2744 0%, #1e3a8a 100%); padding: 32px 30px; text-align: center;">
                            <div style="display: inline-block; width: 48px; height: 48px; line-height: 48px; background: rgba(255, 255, 255, 0.15); border-radius: 12px; margin-bottom: 12px;">
                                <span style="font-size: 26px; color: #f59e0b;">&#128218;</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">HỆ THỐNG QUẢN LÝ THƯ VIỆN</h1>
                            <p style="margin: 6px 0 0; color: #94a3b8; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Kích hoạt tài khoản độc giả mới</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 32px 28px;">
                            <p style="margin: 0 0 16px; font-size: 16px; color: #334155;">
                                Xin chào <strong>{{ $userName }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px; font-size: 15px; color: #475569; line-height: 1.6;">
                                Cảm ơn bạn đã đăng ký tài khoản độc giả tại <strong>Hệ Thống Quản Lý Thư Viện</strong>. Để hoàn tất kích hoạt tài khoản, vui lòng sử dụng mã xác thực <strong>OTP</strong> dưới đây:
                            </p>

                            <!-- OTP Box -->
                            <div style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); border: 2px dashed #93c5fd; border-radius: 14px; padding: 24px 20px; text-align: center; margin: 24px 0;">
                                <div style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;">
                                    Mã kích hoạt OTP của bạn
                                </div>
                                <div style="font-family: 'Courier New', Courier, monospace; font-size: 36px; font-weight: 800; color: #1d4ed8; letter-spacing: 10px; padding: 8px 0;">
                                    {{ $otpCode }}
                                </div>
                                <div style="font-size: 13px; color: #dc2626; margin-top: 8px; font-weight: 500;">
                                    &#9201; Mã có hiệu lực trong vòng <strong>{{ $validMinutes }} phút</strong>
                                </div>
                            </div>

                            <!-- Security Warning -->
                            <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 14px 16px; border-radius: 6px; margin: 24px 0;">
                                <p style="margin: 0; font-size: 13px; color: #166534; line-height: 1.5;">
                                    <strong>Gợi ý:</strong> Bạn cũng có thể kích hoạt nhanh bằng cách quét mã QR <strong>Google Authenticator</strong> trên trang xác thực để bảo vệ tài khoản 2 lớp ngay từ đầu.
                                </p>
                            </div>

                            <p style="margin: 24px 0 0; font-size: 14px; color: #64748b;">
                                Trân trọng,<br>
                                <strong style="color: #0f2744;">Ban Quản Trị Hệ Thống Thư Viện</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 32px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                Email này được gửi tự động để xác nhận đăng ký tài khoản thư viện.<br>
                                Người gửi: {{ config('mail.from.name') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
