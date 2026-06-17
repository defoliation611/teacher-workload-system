<?php
/**
 * 数据验证类
 */

namespace Library;

class Validator
{
    private $errors = [];

    /**
     * 验证邮箱
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * 验证URL
     */
    public static function validateUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * 验证整数
     */
    public static function validateInteger($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * 验证浮点数
     */
    public static function validateFloat($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * 验证字符串长度
     */
    public static function validateLength($value, $min = 0, $max = 255)
    {
        $len = strlen($value);
        return $len >= $min && $len <= $max;
    }

    /**
     * 验证日期格式
     */
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * 验证手机号
     */
    public static function validatePhone($phone)
    {
        return preg_match('/^1[3-9]\d{9}$/', $phone) === 1;
    }

    /**
     * 验证身份证号
     */
    public static function validateIdCard($idcard)
    {
        return preg_match('/^\d{17}[\dXx]$/', $idcard) === 1;
    }

    /**
     * 添加错误
     */
    public function addError($field, $message)
    {
        $this->errors[$field] = $message;
    }

    /**
     * 获取所有错误
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * 是否有错误
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }
}
