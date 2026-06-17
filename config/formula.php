<?php
/**
 * 工作量计算公式配置
 */

return [
    // 教学工作量参数
    'teaching' => [
        // 课程系数
        'course_coefficient' => [
            'theory'      => 1.0,   // 理论课
            'experiment'  => 1.5,   // 实验课
            'practice'    => 2.0,   // 实践课
            'seminar'     => 1.2,   // 研讨课
        ],
        
        // 人数系数
        'student_coefficient' => [
            ['max' => 30,   'rate' => 1.0],
            ['max' => 50,   'rate' => 1.1],
            ['max' => 100,  'rate' => 1.2],
            ['max' => 10000, 'rate' => 1.3],  // >100人
        ],
        
        // 职称系数
        'position_coefficient' => [
            'professor'           => 1.0,
            'associate_professor' => 0.95,
            'lecturer'            => 0.9,
            'assistant'           => 0.85,
        ],
    ],
    
    // 科研工作量折算
    'research' => [
        // 论文积分（折合工作量）
        'paper_score' => [
            'top_tier'     => 10,  // 顶级期刊
            'first_level'  => 8,   // 一级期刊
            'second_level' => 6,   // 二级期刊
            'third_level'  => 4,   // 三级期刊
            'conf_international' => 8,   // 国际会议
            'conf_national'      => 5,   // 国内会议
        ],
        
        // 项目/课题积分
        'project_score' => [
            'national'  => 50,  // 国家级
            'provincial' => 30,  // 省级
            'municipal' => 20,  // 市级
            'university' => 10,  // 校级
        ],
        
        // 专利积分
        'patent_score' => [
            'invention'    => 20,  // 发明专利
            'utility'      => 10,  // 实用新型
            'design'       => 8,   // 外观设计
        ],
        
        // 学生指导积分
        'student_guidance_score' => [
            'postgraduate'  => 5,   // 研究生
            'undergraduate' => 2,   // 本科生
            'internship'    => 1,   // 实习生
        ],
        
        // 竞赛指导积分
        'competition_score' => [
            'international' => 10,
            'national'      => 8,
            'provincial'    => 5,
            'university'    => 2,
        ],
    ],
    
    // 社会服务工作量
    'service' => [
        'consultation'  => 2,   // 咨询服务
        'training'      => 3,   // 培训
        'technical_service' => 5,  // 技术服务
    ],
];
