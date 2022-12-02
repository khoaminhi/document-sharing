<div class="container">
    <div class="d-flex justify-content-center">
        <div>
            <h4 class="text-center mt-5">Xác minh đăng ký</h4>
            <form class="was-validated mb-3" method="POST" action="/document-sharing/user/verifyregister">
                <div class="row">
                    <div class="col-md-12">
                        <label for="code">Mã (*)</label>
                        <input type="input" class="form-control" id="code" name="code" oninvalid="this.setCustomValidity('Vui lòng điền thông tin!')" onchange="this.setCustomValidity('')" required>
                    </div>
                    <input type="hidden" class="form-control" name="data" value="<?=$userEncryptRegisterInfo?>">
                </div><br />
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Xác Minh</button>
                </div>
            </form>
            <div class="text-center mt-3">
                Không nhận được mã? <a href="/document-sharing/user/register">Đăng ký lại</a>
            </div>
        </div>
    </div>
</div>