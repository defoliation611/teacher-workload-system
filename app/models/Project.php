<?php
/**
 * 项目模型
 */

namespace App\Models;

class Project extends BaseModel
{
    protected $table = 'projects';

    /**
     * 获取教师项目
     */
    public function getByTeacher($userId)
    {
        return $this->where('user_id', $userId);
    }

    /**
     * 获取已完成的项目
     */
    public function getCompleted($userId = null)
    {
        $data = $this->all();
        $projects = array_filter($data, function($item) {
            return $item['status'] == 'completed';
        });
        
        if ($userId !== null) {
            $projects = array_filter($projects, function($item) use ($userId) {
                return $item['user_id'] == $userId;
            });
        }
        
        return $projects;
    }

    /**
     * 获取进行中的项目
     */
    public function getOngoing($userId = null)
    {
        $data = $this->all();
        $projects = array_filter($data, function($item) {
            return $item['status'] == 'ongoing';
        });
        
        if ($userId !== null) {
            $projects = array_filter($projects, function($item) use ($userId) {
                return $item['user_id'] == $userId;
            });
        }
        
        return $projects;
    }
}
