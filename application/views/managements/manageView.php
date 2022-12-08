<div class="advancedSearch">
    <table class="searchPlayerTableInput mt-5 mb-5">
        <tr>
            <td><input type="text" id="email" placeholder="nhập email"></td>
            <td><input type="text" id="name" placeholder="nhập tên"></td>
            <td><input type="text" id="share" placeholder="đã gửi (0=không hoặc 1=có)"></td>
            <td><input type="text" id="openned_mail" placeholder="đã xem (0 hoặc 1)"></td>
            <td><input type="text" id="downloaded" placeholder="đã tải (0 hoặc 1)"></td>
            <td><input type="button" value="Lọc" id="filterButton"></td>
        </tr>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('#filterButton').click(function () {
            let email = encodeURI($('#email').val());
            let name = encodeURI($('#name').val());
            let share = encodeURI($('#share').val());
            let openned_mail = encodeURI($('#openned_mail').val());
            let downloaded = encodeURI($('#downloaded').val());
            let url = '/document-sharing/manage/user/filter';
            let data = {
                email,
                name,
                share,
                openned_mail,
                downloaded
            };
            $.ajax({
                url,
                type: 'GET',
                data: { data },
                async: true,
            })
                .done(function (data) {
                    console.log('khoa', data)
                    $('#filterUserTable').html(data);
                })
                .fail(function (jqXHR, data) {
                    console.log('khoa', data, jqXHR)
                    alert(jqXHR.responseJSON.message);
                });
        });
    });
</script>

<div id=filterUserTable>
<?php
$result = "<table class='table table-hover'>
<tr>
    <th scope='col'>Email</th>
    <th scope='col'>Tên</th>
    <th scope='col'>Đã gửi</th>
    <th scope='col'>Đã xem</th>
    <th scope='col'>Đã tải</th>
    <th >Chi tiết</th>
</tr>";

foreach ($listUser as $u) {
    $sendTime = '';
    $opennedMailTime = '';
    $downloadedTime = '';
    
    if ((empty($u['share']) && !empty($u['share']['send_time']))) {
        $sendTime = $u['share']['send_time'];
    }
    if ((empty($u['share']) && !empty($u['share']['openned_mail_time']))) {
        $opennedMailTime = $u['share']['openned_mail_time'];
    }
    if ((empty($u['share']) && !empty($u['share']['downloaded_time']))) {
        $downloadedTime = $u['share']['downloaded_time'];
    }

    $result = $result . "<tr>
        <td>" .
        $u['email']
        . "</td>
        <td>" .
        $u['name']
        . "</td>
        <td>" .
        $sendTime
        . "</td>
        <td>" .
        $opennedMailTime
        . "</td>
        <td>" .
        $downloadedTime
        . "</td>
        <td>";
    $result .= "<a href='/document-sharing/user/" . (string) $u['_id']
        . "'>xem</a></td></tr>";
}
$result .= "</table>";
echo $result;

?>

</div>