<div class="container">
    <div class="d-flex justify-content-center">
        <div>
            <h4 class="text-center mt-5">Gửi lại mã otp</h4>
            <form class="was-validated mb-3" method="POST" action="/document-sharing/user/resendotp">
                <div class="row">
                    <input type="hidden" class="form-control" name="data" value="<?=$userEncryptRegisterInfo?>">
                </div><br />
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Gửi lại mã otp</button>
                </div>
            </form>
            <div class="text-center mt-3">
                Không nhận được mã? <a href="/document-sharing/user/register">Đăng ký lại</a>
            </div>
        </div>
    </div>
</div>