<?php
/**
 * 工作量控制器
 */

namespace App\Controllers;

use App\Models\TeachingWorkload;
use App\Models\ResearchWorkload;
use Library\Calculator;

class WorkloadController extends BaseController
{
    /**
     * 工作量总览
     */
    public function index()
    {
        $this->checkLogin();
        $user = $this->auth->getUser();
        
        $teachingModel = new TeachingWorkload($this->db);
        $researchModel = new ResearchWorkload($this->db);
        
        $academicYear = $_GET['year'] ?? date('Y');
        $semester = $_GET['semester'] ?? 1;
        
        $teachingWorkloads = $teachingModel->getByTeacher($user['id'], $academicYear, $semester);
        $researchWorkloads = $researchModel->getByTeacher($user['id'], $academicYear, $semester);
        
        $teachingTotal = 0;
        foreach ($teachingWorkloads as $item) {
            $teachingTotal += $item['workload'];
        }
        
        $researchTotal = 0;
        foreach ($researchWorkloads as $item) {
            $researchTotal += $item['total_workload'];
        }
        
        echo $this->view('workload/index', [
            'academicYear' => $academicYear,
            'semester' => $semester,
            'teachingWorkloads' => $teachingWorkloads,
            'researchWorkloads' => $researchWorkloads,
            'teachingTotal' => $teachingTotal,
            'researchTotal' => $researchTotal,
            'totalWorkload' => $teachingTotal + $researchTotal
        ]);
    }

    /**
     * 教学工作量详情
     */
    public function teachingDetail()
    {
        $this->checkLogin();
        $user = $this->auth->getUser();
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->error('参数错误');
        }
        
        $workload = $this->db->table('teaching_workload')->find($id);
        
        if (!$workload || $workload['user_id'] != $user['id']) {
            $this->error('无权限访问');
        }
        
        echo $this->view('workload/teaching_detail', ['workload' => $workload]);
    }

    /**
     * 科研工作量详情
     */
    public function researchDetail()
    {
        $this->checkLogin();
        $user = $this->auth->getUser();
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->error('参数错误');
        }
        
        $workload = $this->db->table('research_workload')->find($id);
        
        if (!$workload || $workload['user_id'] != $user['id']) {
            $this->error('无权限访问');
        }
        
        echo $this->view('workload/research_detail', ['workload' => $workload]);
    }

    /**
     * 提交工作量
     */
    public function submit()
    {
        $this->checkLogin();
        $user = $this->auth->getUser();
        
        $workloadId = $_POST['workload_id'] ?? null;
        $type = $_POST['type'] ?? null;  // teaching or research
        
        if (!$workloadId || !$type) {
            $this->error('参数错误');
        }
        
        $table = $type === 'teaching' ? 'teaching_workload' : 'research_workload';
        $workload = $this->db->table($table)->find($workloadId);
        
        if (!$workload || $workload['user_id'] != $user['id']) {
            $this->error('无权限操作');
        }
        
        $this->db->table($table)->update(['status' => 'submitted'], $workloadId);
        $this->logger->info('用户 ' . $user['id'] . ' 提交了' . $type . '工作量: ' . $workloadId);
        
        $this->success('工作量已提交');
    }
}
