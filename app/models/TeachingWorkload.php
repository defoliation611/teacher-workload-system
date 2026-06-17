<?php
/**
 * 教学工作量模型
 */

namespace App\Models;

class TeachingWorkload extends BaseModel
{
    protected $table = 'teaching_workload';

    /**
     * 获取教师的教学工作量
     */
    public function getByTeacher($userId, $academicYear = null, $semester = null)
    {
        $data = $this->where('user_id', $userId);
        
        if ($academicYear !== null) {
            $data = array_filter($data, function($item) use ($academicYear) {
                return $item['academic_year'] == $academicYear;
            });
        }
        
        if ($semester !== null) {
            $data = array_filter($data, function($item) use ($semester) {
                return $item['semester'] == $semester;
            });
        }
        
        return $data;
    }

    /**
     * 获取已提交的工作量
     */
    public function getSubmitted($academicYear, $semester)
    {
        $data = $this->all();
        return array_filter($data, function($item) use ($academicYear, $semester) {
            return $item['academic_year'] == $academicYear && 
                   $item['semester'] == $semester && 
                   $item['status'] != 'draft';
        });
    }

    /**
     * 计算教师总工作量
     */
    public function getTotalByTeacher($userId, $academicYear, $semester)
    {
        $workloads = $this->getByTeacher($userId, $academicYear, $semester);
        $total = 0;
        foreach ($workloads as $item) {
            $total += $item['workload'];
        }
        return $total;
    }

    /**
     * 获取部门工作量统计
     */
    public function getDepartmentStats($departmentId, $academicYear, $semester)
    {
        // 需要联合查询，这里简化处理
        return [];
    }
}
