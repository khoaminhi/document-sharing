<?php
/**
 * Form rules for validate form requests
 */
defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

if (!function_exists('form_rule_register_user')) {
    /**
     * Return the array of rules for validating register user form
     * return array(
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'required|valid_email',
                'errors' => array(
                    'required' => 'Bạn phải nhập %s.',
                    'valid_email' => 'Bạn phải nhập phải chính xác là email'
                ),
            ),
            array(...
     * @return	array
     */
    function form_rule_register_user()
    {
        return array(
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'required|valid_email',
                'errors' => array(
                    'required' => 'Bạn phải nhập %s.',
                    'valid_email' => 'Bạn phải nhập phải chính xác là email'
                ),
            ),
            array(
                'field' => 'name',
                'label' => 'Tên người dùng',
                'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn phải nhập tên của mình.',
                    'min_length' => 'Bạn phải nhập tên của mình.',
                ),
            ),
            array(
                'field' => 'age',
                'label' => 'Tuổi',
                'rules' => 'required|is_natural_no_zero',
                'errors' => array(
                    'required' => 'Bạn phải nhập tuổi của mình',
                    'is_natural_no_zero' => 'Tuổi của bạn phải là số nguyên và phải lớn hơn 0',
                ),
            ),
            array(
                'field' => 'gender',
                'label' => 'Giới tính',
                'rules' => 'required|in_list[M, F]',
                'errors' => array(
                    'required' => 'Bạn phải nhập giới của mình',
                    'in_list' => 'Giới tính của bạn phải là nam hoặc nữ (M hoặc F)'
                ),
            ),
            array(
                'field' => 'occupation',
                'label' => 'Nghề nghiệp',
                'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn phải nhập nghề nghiệp của mình',
                    'min_length' => 'Bạn chưa nhập nghề nghiệp'
                ),
            ),
            array(
                'field' => 'address',
                'label' => 'Địa chỉ',
                'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn phải nhập địa chỉ của mình',
                    'min_length' => 'Bạn chưa cung cấp địa chỉ'
                ),
            ),
        );
    }
}

// ------------------------------------------------------------------------

if (!function_exists('form_rule_verify_user')) {
    /**
     * Return the array of rules for validating otp form
     * return array(
            array(
                'field' => 'code',
                'label' => 'code',
                'rules' => 'required',
                'errors' => array(
                    'required' => 'Bạn phải nhập %s.',
                ),
            ),
            array(...
     * @return	array
     */
    function form_rule_verify_user()
    {
        return array(
            array(
                'field' => 'code',
                'label' => 'mã otp',
                'rules' => 'required|exact_length[6]',
                'errors' => array(
                    'required' => 'Bạn phải nhập %s.',
                    'exact_length' => 'Mã otp phải là 6 ký tự'
                ),
            ),
            array(
                'field' => 'data',
                'label' => 'Dữ liệu',
                'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn đã chỉnh sửa đoạn mã %s.',
                    'min_length' => 'Đoạn mã dữ liệu phải có'
                ),
            ),
        );
    }
}

// ------------------------------------------------------------------------

if (!function_exists('form_rule_resendotp')) {
    /**
     * Return the array of rules for validating otp form
     * return array(
            array(
                'field' => 'data',
                'label' => 'Dữ liệu',
                'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn đã chỉnh sửa đoạn mã %s.',
                    'min_length' => 'Đoạn mã dữ liệu phải có'
                ),
            ),
     * @return	array
     */
    function form_rule_resendotp()
    {
        return array(
            array(
                'field' => 'data',
                'label' => 'Dữ liệu',
                'rules' => 'required|min_length[1]',
                'errors' => array(
                    'required' => 'Bạn đã chỉnh sửa đoạn mã %s?.',
                    'min_length' => 'Đoạn mã dữ liệu phải có'
                ),
            ),
        );
    }
}

// ------------------------------------------------------------------------

if (!function_exists('form_mail_send')) {
    /**
     * Return the array of rules for validating otp form
     * return array(
            array(
                'field' => 'to',
                'label' => 'người nhận',
                'rules' => 'required|valid_email',
                'errors' => array(
                    'required' => 'Bạn phải truyền %s.',
                    'valid_email' => 'Bạn phải truyền chính xác là email'
                ),
            ),
            ...
     * @return	array
     */
    function form_mail_send()
    {
        return array(
            array(
                'field' => 'to',
                'label' => 'người nhận mail',
                'rules' => 'required|valid_email',
                'errors' => array(
                    'required' => 'Bạn phải truyền %s.',
                    'valid_email' => 'Bạn phải truyền chính xác là email'
                ),
            ),
        );
    }
}

// ------------------------------------------------------------------------

