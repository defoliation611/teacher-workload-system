<?php
/**
 * 报表控制器
 */

namespace App\Controllers;

use Library\Exporter;

class ReportController extends BaseController
{
    /**
     * 报表首页
     */
    public function index()
    {
        $this->checkPermission(['admin', 'teaching_manager', 'research_manager', 'dean']);
        
        $academicYear = $_GET['year'] ?? date('Y');
        $semester = $_GET['semester'] ?? 1;
        
        echo $this->view('report/index', [
            'academicYear' => $academicYear,
            'semester' => $semester
        ]);
    }

    /**
     * 部门报表
     */
    public function department()
    {
        $this->checkPermission(['admin', 'teaching_manager', 'dean']);
        
        $departmentId = $_GET['department_id'] ?? null;
        $academicYear = $_GET['year'] ?? date('Y');
        $semester = $_GET['semester'] ?? 1;
        
        // 获取部门信息
        $department = $this->db->table('departments')->find($departmentId);
        
        if (!$department) {
            $this->error('部门不存在');
        }
        
        // 获取部门教师列表
        $teachers = $this->db->table('users')->where('department_id', $departmentId);
        
        $reportData = [];
        foreach ($teachers as $teacher) {
            $performance = $this->db->table('performance')
                ->where('user_id', $teacher['id']);
            
            foreach ($performance as $perf) {
                if ($perf['academic_year'] == $academicYear && $perf['semester'] == $semester) {
                    $reportData[] = [
                        'teacher_name' => $teacher['name'],
                        'position' => $teacher['position'],
                        'teaching_workload' => $perf['teaching_workload'],
                        'research_workload' => $perf['research_workload'],
                        'total_workload' => $perf['total_workload'],
                        'completion_rate' => $perf['completion_rate'],
                        'overtime_pay' => $perf['overtime_pay'],
                        'total_salary' => $perf['total_salary']
                    ];
                }
            }
        }
        
        echo $this->view('report/department', [
            'department' => $department,
            'academicYear' => $academicYear,
            'semester' => $semester,
            'reportData' => $reportData
        ]);
    }

    /**
     * 导出报表
     */
    public function export()
    {
        $this->checkPermission(['admin', 'teaching_manager', 'dean']);
        
        $type = $_GET['type'] ?? 'department';
        $format = $_GET['format'] ?? 'csv';  // csv, excel, json
        $departmentId = $_GET['department_id'] ?? null;
        $academicYear = $_GET['year'] ?? date('Y');
        $semester = $_GET['semester'] ?? 1;
        
        if ($type === 'department' && !$departmentId) {
            $this->error('参数错误');
        }
        
        // 收集导出数据（这里简化处理）
        $exportData = [];
        
        if ($format === 'csv') {
            Exporter::exportCsv($exportData, '工作量报表_' . date('YmdHis') . '.csv');
        } elseif ($format === 'excel') {
            Exporter::exportExcel($exportData, '工作量报表_' . date('YmdHis') . '.xlsx');
        } elseif ($format === 'json') {
            Exporter::exportJson($exportData, '工作量报表_' . date('YmdHis') . '.json');
        }
        
        $this->error('导出格式错误');
    }

    /**
     * 统计数据API
     */
    public function stats()
    {
        $this->checkPermission(['admin', 'teaching_manager', 'dean']);
        
        $academicYear = $_GET['year'] ?? date('Y');
        $semester = $_GET['semester'] ?? 1;
        
        // 统计总工作量
        $performances = $this->db->table('performance')->all();
        $totalWorkload = 0;
        $avgWorkload = 0;
        $completionCount = 0;
        
        $count = 0;
        foreach ($performances as $perf) {
            if ($perf['academic_year'] == $academicYear && $perf['semester'] == $semester) {
                $totalWorkload += $perf['total_workload'];
                if ($perf['completion_rate'] >= 100) {
                    $completionCount++;
                }
                $count++;
            }
        }
        
        $avgWorkload = $count > 0 ? round($totalWorkload / $count, 2) : 0;
        $completionRate = $count > 0 ? round(($completionCount / $count) * 100, 2) : 0;
        
        $this->json([
            'totalWorkload' => $totalWorkload,
            'avgWorkload' => $avgWorkload,
            'completionRate' => $completionRate,
            'totalTeachers' => $count
        ]);
    }
}
