<?php //echo validation_errors(); ?>
<div class="container">
    <div class="d-flex justify-content-center">
        <div>
            <?php
            if (isset($resultForModal)) {
                $notice = "<div class='modal fade' id='warningBootstrapModal' tabindex='-1' role='dialog'
                            aria-labelledby='exampleModalCenterTitle' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-centered' role='document'>
                                <div class='modal-content text-center'>
                                    <div class='modal-header d-flex justify-content-center text-warning'>
                                        <h5 class='modal-title' id='exampleModalLongTitle'>Thông Báo</h5>
                                    </div>
                                    <div class='modal-body'>
                                        $resultForModal
                                    </div>
                                    <div class='modal-footer'>
                                        <button id='hideNoticeRelPerson' type='button' class='btn btn-secondary'
                                            data-dismiss='modal'>Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
                echo $notice;
            }
            ?>
        </div>
        <div>
            <h4 class="text-center mt-5">Đăng ký tài liệu</h4>
            <form class="was-validated mb-3" method="POST">
                <div class="row">
                    <div class="col-md-12">
                        <label for="email">Email (*)</label>
                        <input type="input" class="form-control" id="email" name="email" oninvalid="this.setCustomValidity('Vui lòng điền thông tin!')" onchange="this.setCustomValidity('')" required>
                        <?php echo form_error('email', '<div class="alert alert-danger">', '</div>')?>
                    </div>
                </div><br />
                <div class="row">
                    <div class="col-md-12">
                        <label for="name">Tên (*)</label>
                        <input type="input" class="form-control" id="name" name="name" oninvalid="this.setCustomValidity('Vui lòng điền thông tin!')" onchange="this.setCustomValidity('')" required>
                        <?php echo form_error('name', '<div class="alert alert-danger">', '</div>')?>
                    </div>
                </div><br />
                <div class="row">
                    <div class="col-md-6">
                        <label for="name">Tuổi (*)</label>
                        <input type="number" min="1" class="form-control" id="age" name="age" oninvalid="this.setCustomValidity('Vui lòng điền thông tin!')" onchange="this.setCustomValidity('')" required>
                        <?php echo form_error('age', '<div class="alert alert-danger">', '</div>')?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Giới tính (*)</label>
                            <div class="custom-control custom-radio from-check">
                                <input type="radio" class="custom-control-input form-check-input" id="customControlValidation2" value="M" name="gender" oninvalid="this.setCustomValidity('Vui lòng điền thông tin!')" onchange="this.setCustomValidity('')" required>
                                <label class="custom-control-label" for="customControlValidation2">Nam</label>
                            </div>
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" class="custom-control-input form-check-input" id="customControlValidation3" value="F" name="gender" required>
                                <label class="custom-control-label" for="customControlValidation3">Nữ</label>
                            </div>
                        </div>
                    </div>
                </div><br />
                <div class="row">
                    <div class="col-md-12">
                        <label for="occupation">Nghề nghiệp (*)</label>
                        <input type="input" class="form-control" id="occupation" name="occupation" oninvalid="this.setCustomValidity('Vui lòng điền thông tin!')" onchange="this.setCustomValidity('')" required>
                    </div>
                </div><br />
                <div class="row">
                    <div class="col-md-12">
                        <label for="address">Địa chỉ (*)</label>
                        <input type="input" class="form-control" id="address" name="address" oninvalid="this.setCustomValidity('Vui lòng điền thông tin!')" onchange="this.setCustomValidity('')" required>
                    </div>
                </div><br />
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Đăng ký</button>
                </div>
            </form>
            <!-- <div class="text-center mt-3">
                Không nhận được mã? <a href="/document-sharing/user/resendotp">Gửi lại mã</a>
            </div> -->
        </div>

    </div>
</div>
<script>    
    
</script>