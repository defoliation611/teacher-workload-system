-- 教师工作量管理系统数据库脚本

CREATE DATABASE IF NOT EXISTS teacher_workload DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE teacher_workload;

-- 用户表
CREATE TABLE `tw_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100),
  `phone` varchar(20),
  `id_card` varchar(18),
  `role` enum('admin','teaching_manager','research_manager','dean','teacher') NOT NULL DEFAULT 'teacher',
  `department_id` int(11),
  `position` varchar(50),
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` datetime,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_role` (`role`),
  KEY `idx_department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 院系表
CREATE TABLE `tw_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50),
  `description` text,
  `dean_id` int(11),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 职称表
CREATE TABLE `tw_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(20),
  `workload_coefficient` decimal(5,2) DEFAULT 1.0,
  `salary_standard` decimal(10,2),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 课程表
CREATE TABLE `tw_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50),
  `credit` decimal(3,1),
  `hours` int(11),
  `type` enum('theory','experiment','practice','seminar') DEFAULT 'theory',
  `department_id` int(11),
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_code` (`code`),
  KEY `idx_department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 教学计划/班级表
CREATE TABLE `tw_teaching_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_name` varchar(50),
  `year` int(4),
  `semester` tinyint(1),
  `student_count` int(11),
  `schedule` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_teacher_id` (`teacher_id`),
  KEY `idx_course_id` (`course_id`),
  FOREIGN KEY (`course_id`) REFERENCES `tw_courses`(`id`),
  FOREIGN KEY (`teacher_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 教学工作量记录
CREATE TABLE `tw_teaching_workload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `teaching_class_id` int(11) NOT NULL,
  `course_hours` decimal(10,2),
  `student_count` int(11),
  `course_coefficient` decimal(5,2),
  `student_coefficient` decimal(5,2),
  `position_coefficient` decimal(5,2),
  `workload` decimal(10,2),
  `academic_year` int(4),
  `semester` tinyint(1),
  `status` enum('draft','submitted','approved','rejected') DEFAULT 'draft',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_academic_year_semester` (`academic_year`, `semester`),
  FOREIGN KEY (`user_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 论文表
CREATE TABLE `tw_papers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `authors` text,
  `publication` varchar(100),
  `level` enum('top_tier','first_level','second_level','third_level','conf_international','conf_national') DEFAULT 'third_level',
  `publish_date` date,
  `workload_score` decimal(10,2),
  `notes` text,
  `status` enum('draft','submitted','approved','rejected') DEFAULT 'draft',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 项目/课题表
CREATE TABLE `tw_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `level` enum('national','provincial','municipal','university') DEFAULT 'university',
  `funding` decimal(15,2),
  `start_date` date,
  `end_date` date,
  `status` enum('ongoing','completed','abandoned') DEFAULT 'ongoing',
  `workload_score` decimal(10,2),
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 专利表
CREATE TABLE `tw_patents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('invention','utility','design') DEFAULT 'utility',
  `patent_number` varchar(50),
  `issue_date` date,
  `workload_score` decimal(10,2),
  `status` enum('applied','granted','rejected') DEFAULT 'applied',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 学生指导记录
CREATE TABLE `tw_student_guidance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `student_name` varchar(100),
  `type` enum('postgraduate','undergraduate','internship') DEFAULT 'undergraduate',
  `title` varchar(255),
  `guidance_hours` int(11),
  `workload_score` decimal(10,2),
  `status` enum('ongoing','completed') DEFAULT 'ongoing',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 竞赛指导记录
CREATE TABLE `tw_competitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `competition_name` varchar(255) NOT NULL,
  `level` enum('international','national','provincial','university') DEFAULT 'university',
  `student_count` int(11),
  `award` varchar(100),
  `guidance_hours` int(11),
  `workload_score` decimal(10,2),
  `competition_date` date,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 科研工作量记录
CREATE TABLE `tw_research_workload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `academic_year` int(4),
  `semester` tinyint(1),
  `paper_workload` decimal(10,2) DEFAULT 0,
  `project_workload` decimal(10,2) DEFAULT 0,
  `patent_workload` decimal(10,2) DEFAULT 0,
  `guidance_workload` decimal(10,2) DEFAULT 0,
  `competition_workload` decimal(10,2) DEFAULT 0,
  `total_workload` decimal(10,2),
  `status` enum('draft','submitted','approved','rejected') DEFAULT 'draft',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_year_semester` (`academic_year`, `semester`),
  FOREIGN KEY (`user_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 绩效核算表
CREATE TABLE `tw_performance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `academic_year` int(4),
  `semester` tinyint(1),
  `teaching_workload` decimal(10,2),
  `research_workload` decimal(10,2),
  `service_workload` decimal(10,2) DEFAULT 0,
  `total_workload` decimal(10,2),
  `baseline_workload` decimal(10,2),
  `completion_rate` decimal(5,2),
  `overtime_workload` decimal(10,2),
  `overtime_pay` decimal(10,2),
  `bonus_deduction` decimal(10,2) DEFAULT 0,
  `total_salary` decimal(10,2),
  `status` enum('draft','submitted','approved','paid') DEFAULT 'draft',
  `approved_by` int(11),
  `approved_at` datetime,
  `notes` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_year_semester` (`academic_year`, `semester`),
  FOREIGN KEY (`user_id`) REFERENCES `tw_users`(`id`),
  FOREIGN KEY (`approved_by`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 津贴标准表
CREATE TABLE `tw_salary_standards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(50),
  `base_salary` decimal(10,2),
  `teaching_baseline` decimal(10,2),
  `overtime_rate` decimal(10,2),
  `bonus_standard` decimal(10,2),
  `deduction_standard` decimal(10,2),
  `effective_date` date,
  `end_date` date,
  `notes` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_effective_date` (`effective_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 审批流程表
CREATE TABLE `tw_approval_workflow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_type` enum('teaching_workload','research_workload','performance') DEFAULT 'performance',
  `record_id` int(11),
  `applicant_id` int(11),
  `approver_id` int(11),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `comments` text,
  `approved_at` datetime,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_applicant_id` (`applicant_id`),
  KEY `idx_approver_id` (`approver_id`),
  FOREIGN KEY (`applicant_id`) REFERENCES `tw_users`(`id`),
  FOREIGN KEY (`approver_id`) REFERENCES `tw_users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 操作日志表
CREATE TABLE `tw_operation_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11),
  `action` varchar(100),
  `table_name` varchar(100),
  `record_id` int(11),
  `old_value` text,
  `new_value` text,
  `ip_address` varchar(45),
  `user_agent` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 系统配置表
CREATE TABLE `tw_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL UNIQUE,
  `config_value` text,
  `description` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入默认职称数据
INSERT INTO `tw_positions` (`name`, `code`, `workload_coefficient`, `salary_standard`) VALUES
('教授', 'professor', 1.0, 8000),
('副教授', 'associate_professor', 0.95, 6500),
('讲师', 'lecturer', 0.9, 5000),
('助教', 'assistant', 0.85, 3500);

-- 插入默认系统配置
INSERT INTO `tw_config` (`config_key`, `config_value`, `description`) VALUES
('academic_year', '2025', '当前学年'),
('semester', '2', '当前学期'),
('teaching_baseline', '200', '教学工作量基准线'),
('overtime_rate', '50', '超课时酬金率'),
('system_version', '1.0.0', '系统版本');
