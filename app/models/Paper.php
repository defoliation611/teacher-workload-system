<?php
/**
 * 论文模型
 */

namespace App\Models;

class Paper extends BaseModel
{
    protected $table = 'papers';

    /**
     * 获取教师论文
     */
    public function getByTeacher($userId)
    {
        return $this->where('user_id', $userId);
    }

    /**
     * 获取已批准的论文
     */
    public function getApproved($userId = null)
    {
        $data = $this->all();
        $papers = array_filter($data, function($item) {
            return $item['status'] == 'approved';
        });
        
        if ($userId !== null) {
            $papers = array_filter($papers, function($item) use ($userId) {
                return $item['user_id'] == $userId;
            });
        }
        
        return $papers;
    }

    /**
     * 统计教师论文数量
     */
    public function countByTeacher($userId)
    {
        $papers = $this->getByTeacher($userId);
        return count($papers);
    }
}
