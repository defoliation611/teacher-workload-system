<?php
/**
 * 系统配置文件
 */

return [
    // 系统基本信息
    'app_name'    => '教师工作量管理系统',
    'app_version' => '1.0.0',
    'app_url'     => 'http://localhost/teacher-workload-system',
    
    // 时区
    'timezone'    => 'Asia/Shanghai',
    
    // 日期格式
    'date_format'     => 'Y-m-d',
    'datetime_format' => 'Y-m-d H:i:s',
    
    // 每页显示数
    'per_page' => 20,
    
    // 上传配置
    'upload' => [
        'max_size' => 10 * 1024 * 1024,  // 10MB
        'allowed_types' => ['xlsx', 'xls', 'csv', 'pdf'],
        'path' => 'uploads/',
    ],
    
    // 学年学期配置
    'academic_year'   => 2025,
    'semester'        => 2,  // 1=春季 2=秋季
    
    // 工作量基准线（教学）
    'teaching_baseline' => 200,  // 每学年基准学时
    
    // 超课时酬金（元/学时）
    'overtime_rate' => 50,
    
    // 日志配置
    'log' => [
        'enabled' => true,
        'level'   => 'info',  // debug, info, warning, error
        'path'    => 'logs/',
    ],
];
