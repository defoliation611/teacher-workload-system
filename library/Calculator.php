<?php
/**
 * 工作量计算引擎
 */

namespace Library;

class Calculator
{
    private $config;

    public function __construct($formulaConfig)
    {
        $this->config = $formulaConfig;
    }

    /**
     * 计算教学工作量
     * @param float $hours 标准学时
     * @param string $courseType 课程类型：theory/experiment/practice/seminar
     * @param int $students 学生人数
     * @param string $position 职称
     */
    public function calculateTeachingWorkload($hours, $courseType, $students, $position = 'lecturer')
    {
        // 获取课程系数
        $courseCoeff = $this->config['teaching']['course_coefficient'][$courseType] ?? 1.0;

        // 获取人数系数
        $studentCoeff = $this->getStudentCoefficient($students);

        // 获取职称系数
        $positionCoeff = $this->config['teaching']['position_coefficient'][$position] ?? 1.0;

        // 计算工作量 = 学时 × 课程系数 × 人数系数 × 职称系数
        $workload = $hours * $courseCoeff * $studentCoeff * $positionCoeff;

        return round($workload, 2);
    }

    /**
     * 根据学生人数获取系数
     */
    private function getStudentCoefficient($students)
    {
        $coefficients = $this->config['teaching']['student_coefficient'];
        foreach ($coefficients as $item) {
            if ($students <= $item['max']) {
                return $item['rate'];
            }
        }
        return end($coefficients)['rate'];
    }

    /**
     * 计算论文工作量
     * @param string $level 论文级别
     */
    public function calculatePaperWorkload($level)
    {
        $scores = $this->config['research']['paper_score'];
        return $scores[$level] ?? 0;
    }

    /**
     * 计算项目工作量
     * @param string $level 项目级别
     */
    public function calculateProjectWorkload($level)
    {
        $scores = $this->config['research']['project_score'];
        return $scores[$level] ?? 0;
    }

    /**
     * 计算专利工作量
     * @param string $type 专利类型
     */
    public function calculatePatentWorkload($type)
    {
        $scores = $this->config['research']['patent_score'];
        return $scores[$type] ?? 0;
    }

    /**
     * 计算学生指导工作量
     * @param string $type 学生类型：postgraduate/undergraduate/internship
     */
    public function calculateStudentGuidanceWorkload($type)
    {
        $scores = $this->config['research']['student_guidance_score'];
        return $scores[$type] ?? 0;
    }

    /**
     * 计算竞赛指导工作量
     * @param string $level 竞赛级别
     */
    public function calculateCompetitionWorkload($level)
    {
        $scores = $this->config['research']['competition_score'];
        return $scores[$level] ?? 0;
    }

    /**
     * 计算社会服务工作量
     * @param string $type 服务类型
     */
    public function calculateServiceWorkload($type)
    {
        $scores = $this->config['service'];
        return $scores[$type] ?? 0;
    }

    /**
     * 计算总工作量
     */
    public function calculateTotalWorkload($teachingWorkload, $researchWorkload, $serviceWorkload = 0)
    {
        return round($teachingWorkload + $researchWorkload + $serviceWorkload, 2);
    }

    /**
     * 计算超课时酬金
     * @param float $totalWorkload 总工作量
     * @param float $baseline 基准工作量
     * @param float $overtimeRate 超课时费率
     */
    public function calculateOvertimePay($totalWorkload, $baseline, $overtimeRate)
    {
        if ($totalWorkload > $baseline) {
            $excess = $totalWorkload - $baseline;
            return round($excess * $overtimeRate, 2);
        }
        return 0;
    }

    /**
     * 计算完成率
     */
    public function calculateCompletionRate($workload, $baseline)
    {
        if ($baseline == 0) return 0;
        return round(($workload / $baseline) * 100, 2);
    }
}
