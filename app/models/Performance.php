<?php
/**
 * 绩效模型
 */

namespace App\Models;

class Performance extends BaseModel
{
    protected $table = 'performance';

    /**
     * 获取教师绩效
     */
    public function getByTeacher($userId, $academicYear = null, $semester = null)
    {
        $data = $this->where('user_id', $userId);
        
        if ($academicYear !== null && $semester !== null) {
            $data = array_filter($data, function($item) use ($academicYear, $semester) {
                return $item['academic_year'] == $academicYear && $item['semester'] == $semester;
            });
            return count($data) > 0 ? reset($data) : null;
        }
        
        return $data;
    }

    /**
     * 获取部门绩效汇总
     */
    public function getDepartmentSummary($departmentId, $academicYear, $semester)
    {
        $allPerformance = $this->all();
        $result = [];
        
        foreach ($allPerformance as $perf) {
            if ($perf['academic_year'] == $academicYear && $perf['semester'] == $semester) {
                $result[] = $perf;
            }
        }
        
        return $result;
    }

    /**
     * 获取待审批绩效
     */
    public function getPending($academicYear, $semester)
    {
        $data = $this->all();
        return array_filter($data, function($item) use ($academicYear, $semester) {
            return $item['academic_year'] == $academicYear && 
                   $item['semester'] == $semester && 
                   $item['status'] == 'submitted';
        });
    }

    /**
     * 批准绩效
     */
    public function approve($performanceId, $approverId, $comments = '')
    {
        $data = [
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => date('Y-m-d H:i:s'),
            'notes' => $comments
        ];
        return $this->update($data, $performanceId);
    }
}
