<?php

$result = "<script>
$(document).ready(function(){
    $('.findUserByEmail').click(function(){
        
        $.ajax({
            url,
            type: 'POST',
            data,
            success: function (data) {
                window.location.reload();
            }
        });
    });
});
</script>";

$result .= "<table class='table table-hover'>
<tr>
    <th scope='col'>Email</th>
    <th scope='col'>Tên</th>
    <th scope='col'>Đã gửi</th>
    <th scope='col'>Đã xem</th>
    <th scope='col'>Đã tải</th>
    <th >Chi tiết</th>
</tr>";

foreach($listUser as $u) {
    $sendTime = (empty($u['share']) ? false : (empty($u['share']['send_time']))) ? '' : (string)$u['share']['send_time'];
    $opennedMailTime = (empty($u['share']) ? false : (empty($u['share']['openned_mail_time']))) ? '' : (string)$u['share']['openned_mail_time'];
    $downloadedTime = (empty($u['share']) ? false : (empty($u['share']['downloaded_time']))) ? '' : (string)$u['share']['downloaded_time'];

    $result = $result . "<tr>
        <td>" .
            $u['email']
        . "</td>
        <td>".
            $u['name']
        ."</td>
        <td>".
            $sendTime
        ."</td>
        <td>".
            $opennedMailTime
        ."</td>
        <td>".
            $downloadedTime
        ."</td>
        <td>";
    $result .= "<a href='/document-sharing/user/" . (string)$u['_id'] 
        . "'>xem</a></td>";
}
$result .= "</table>";
echo $result;