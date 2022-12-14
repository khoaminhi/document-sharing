<div class="container">
    <div class="d-flex justify-content-center">
        <div>
            <div id="append"></div>
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
            <div>
                <h4 class="text-center mt-5">Xác minh đăng ký</h4>
                <form class="was-validated mb-3" method="POST" action="/document-sharing/user/verifyregister">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="code">Mã (*)</label>
                            <input type="input" class="form-control" id="code" name="code"
                                oninvalid="this.setCustomValidity('Vui lòng điền thông tin!')"
                                onchange="this.setCustomValidity('')" required>
                        </div>
                        <input type="hidden" class="form-control" id="data" name="data" value="<?php if (!empty($userEncryptRegisterInfo))
                                echo $userEncryptRegisterInfo ?>">
                    </div><br />
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Xác Minh</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    Không nhận được mã? <a href="#" id="resendotp">Gửi lại mã otp</a>
                </div>
                <div class="text-center mt-3">
                    Đăng ký lại? <a href="/document-sharing/user/register">Đăng ký</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const noticeModel1 = `<div class='modal fade' id='resendotpBootstrapModal' tabindex='-1' role='dialog'
                                aria-labelledby='exampleModalCenterTitle' aria-hidden='true'>
                                <div class='modal-dialog modal-dialog-centered' role='document'>
                                    <div class='modal-content text-center'>
                                        <div class='modal-header d-flex justify-content-center text-warning'>
                                            <h5 class='modal-title' id='exampleModalLongTitle'>Thông Báo</h5>
                                        </div>
                                        <div class='modal-body'>
                            `
        const noticeModel2 =
            `
                                        </div>
                                        <div class='modal-footer'>
                                            <button id='hideNoticeModel' type='button' class='btn btn-secondary'
                                                data-dismiss='modal'>Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
            `;

        const noticeModel3 = 
        "<script" + `>
                                $(document).ready(() => {
                                    $('#hideNoticeModel').click(() => {
                                        $('#resendotpBootstrapModal').modal('hide');
                                    });
                                });</`
                            + "script>"
                            ;

        $(document).ready(function () {
            $('#resendotp').click(function () {
                const data = encodeURI($('#data').val());
                console.log(data);
                if (!data) {
                    const noticeModel = noticeModel1 +
                        `<div class="alert alert-danger" role="alert">
                            Dữ liệu đã bị thay đổi. Quý khách vui lòng đăng ký lại
                        </div>`
                        + noticeModel2 + noticeModel3;
                    $('#append').html(noticeModel);
                    $('#resendotpBootstrapModal').modal('show');
                    return;
                }
                const url = '/document-sharing/user/resendotp'

                $.ajax({
                    url,
                    type: 'POST',
                    data: { data },
                    async: true,
                })
                    .done(function (data) {
                        console.log('khoa', data)
                        const noticeModel = noticeModel1 +
                            `<div class="alert alert-success" role="alert">
                                ${data?.message}
                            </div>`
                            + noticeModel2 + noticeModel3;
                        $('#append').html(noticeModel);
                        $('#resendotpBootstrapModal').modal('show');
                    })
                    .fail(function (jqXHR, data) {
                        console.log('khoa', data)
                        const noticeModel = noticeModel1 +
                            `<div class="alert alert-danger" role="alert">
                                Gửi mã otp thất bại.
                                Error: ${data?.message || data}
                            </div>`
                            + noticeModel2 + noticeModel3;
                        $('#append').html(noticeModel);
                        $('#resendotpBootstrapModal').modal('show');
                    });
            });
        });

        $(document).ready(() => {
            $('#hideNoticeModel').click(() => {
                $('#resendotpBootstrapModal').modal('hide');
            });
        });
    </script>



<div class="container">
    <h4 class="text-center mt-5">Kendo UI</h4>
    <div class="d-flex justify-content-center">

        <div class="w-50 border p-1 rounded shadow p-3 mb-5 bg-white">
            <form id="form" method="POST" action="/document-sharing/user/verifyregister"></form>
        </div>
    </div>
</div>

<style>
    .data-hidden {
        display: none;
    }
</style>
<script>
    $(document).ready(function () {
        $("#form").kendoForm({
            validatable: true,//{ validationSummary: true },
            orientation: "vertical",
            formData: {
                code: '',
                data: "<?php if (!empty($userEncryptRegisterInfo)) echo $userEncryptRegisterInfo ?>",
            },
            items: [
                {
                    field: "code",
                    label: "Mã:",
                    title: 'mã',
                    validation: {
                        required: { message: 'Bạn phải nhập trường này' },
                    }
                },
                {
                    field: "data",
                    label: '',
                    attributes: {
                        class: "data-hidden"
                    },
                    validation: { required: { message: 'Bạn phải nhập trường này' }, }
                },
            ]
        });
    });
</script>
