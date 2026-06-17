<?php
/**
 * 数据导出类
 */

namespace Library;

class Exporter
{
    /**
     * 导出为CSV
     */
    public static function exportCsv($data, $filename = 'export.csv')
    {
        header('Content-Type: application/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        
        if (!empty($data)) {
            // 输出表头
            fputcsv($output, array_keys($data[0]));
            
            // 输出数据
            foreach ($data as $row) {
                fputcsv($output, array_values($row));
            }
        }
        
        fclose($output);
        exit;
    }

    /**
     * 导出为Excel（简单版本，生成HTML表格）
     */
    public static function exportExcel($data, $filename = 'export.xlsx')
    {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        echo '<table border="1">';
        
        if (!empty($data)) {
            // 输出表头
            echo '<tr>';
            foreach (array_keys($data[0]) as $header) {
                echo '<td>' . htmlspecialchars($header) . '</td>';
            }
            echo '</tr>';
            
            // 输出数据
            foreach ($data as $row) {
                echo '<tr>';
                foreach (array_values($row) as $value) {
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                }
                echo '</tr>';
            }
        }
        
        echo '</table>';
        exit;
    }

    /**
     * 导出为JSON
     */
    public static function exportJson($data, $filename = 'export.json')
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
