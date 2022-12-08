<?php
use TheSeer\Tokenizer\Exception;

/**
 * Form rules for validate form requests
 */
defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

if (!function_exists('render_user_filter_view')) {
    /**
     * This is a sending mail helper.
     * @param array $userFilterResult
     * @return	string $userTableHtml
     */
    function render_user_filter_view($userFilterResult)
    {
        $userTable = "<table class='table table-hover'>
            <tr>
                <th scope='col'>Email</th>
                <th scope='col'>Tên</th>
                <th scope='col'>Đã gửi</th>
                <th scope='col'>Đã xem</th>
                <th scope='col'>Đã tải</th>
                <th >Chi tiết</th>
            </tr>";

        foreach ($userFilterResult as $u) {
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

            $userTable = $userTable . "<tr>
                <td>" . $u['email'] . "</td>
                <td>" . $u['name'] . "</td>
                <td>" . $sendTime . "</td>
                <td>" . $opennedMailTime . "</td>
                <td>" . $downloadedTime . "</td>
                <td>";
            $userTable .= "<a href='/document-sharing/user/" . (string) $u['_id'] . "'>xem</a></td></tr>";
        }
        $userTable .= "</table>";

        return $userTable;
    }
}

// ------------------------------------------------------------------------
