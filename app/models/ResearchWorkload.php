<?php
/**
 * 科研工作量模型
 */

namespace App\Models;

class ResearchWorkload extends BaseModel
{
    protected $table = 'research_workload';

    /**
     * 获取教师科研工作量
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
     * 更新科研工作量汇总
     */
    public function updateSummary($userId, $academicYear, $semester, $data)
    {
        $existing = $this->where('user_id', $userId);
        $record = null;
        
        foreach ($existing as $item) {
            if ($item['academic_year'] == $academicYear && $item['semester'] == $semester) {
                $record = $item;
                break;
            }
        }

        if ($record) {
            return $this->update($data, $record['id']);
        } else {
            $data['user_id'] = $userId;
            $data['academic_year'] = $academicYear;
            $data['semester'] = $semester;
            return $this->create($data);
        }
    }
}
