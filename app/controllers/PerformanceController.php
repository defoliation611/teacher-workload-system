<?php
/**
 * 绩效控制器
 */

namespace App\Controllers;

use App\Models\Performance;
use App\Models\TeachingWorkload;
use App\Models\ResearchWorkload;
use Library\Calculator;

class PerformanceController extends BaseController
{
    /**
     * 绩效总览
     */
    public function index()
    {
        $this->checkLogin();
        $user = $this->auth->getUser();
        
        $performanceModel = new Performance($this->db);
        $academicYear = $_GET['year'] ?? date('Y');
        $semester = $_GET['semester'] ?? 1;
        
        $performance = $performanceModel->getByTeacher($user['id'], $academicYear, $semester);
        
        echo $this->view('performance/index', [
            'academicYear' => $academicYear,
            'semester' => $semester,
            'performance' => $performance
        ]);
    }

    /**
     * 绩效详情
     */
    public function detail()
    {
        $this->checkLogin();
        $user = $this->auth->getUser();
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->error('参数错误');
        }
        
        $performance = $this->db->table('performance')->find($id);
        
        if (!$performance || $performance['user_id'] != $user['id']) {
            $this->error('无权限访问');
        }
        
        echo $this->view('performance/detail', ['performance' => $performance]);
    }

    /**
     * 审批列表（管理员）
     */
    public function approval()
    {
        $this->checkPermission(['admin', 'teaching_manager', 'dean']);
        
        $academicYear = $_GET['year'] ?? date('Y');
        $semester = $_GET['semester'] ?? 1;
        
        $performanceModel = new Performance($this->db);
        $pending = $performanceModel->getPending($academicYear, $semester);
        
        echo $this->view('performance/approval', [
            'academicYear' => $academicYear,
            'semester' => $semester,
            'pending' => $pending
        ]);
    }

    /**
     * 审批操作
     */
    public function approveAction()
    {
        $this->checkPermission(['admin', 'teaching_manager', 'dean']);
        $user = $this->auth->getUser();
        
        $performanceId = $_POST['performance_id'] ?? null;
        $action = $_POST['action'] ?? null;  // approve or reject
        $comments = $_POST['comments'] ?? '';
        
        if (!$performanceId || !$action) {
            $this->error('参数错误');
        }
        
        $performanceModel = new Performance($this->db);
        
        if ($action === 'approve') {
            $performanceModel->approve($performanceId, $user['id'], $comments);
            $this->logger->info('管理员 ' . $user['id'] . ' 批准了绩效: ' . $performanceId);
            $this->success('绩效已批准');
        } elseif ($action === 'reject') {
            $this->db->table('performance')->update([
                'status' => 'rejected',
                'notes' => $comments
            ], $performanceId);
            $this->logger->info('管理员 ' . $user['id'] . ' 驳回了绩效: ' . $performanceId);
            $this->success('绩效已驳回');
        }
        
        $this->error('操作参数错误');
    }
}
