# PHP教师工作量管理系统

## 📋 系统简介

完整的PHP教师工作量管理系统，涵盖数据采集、工作量计算、绩效核算、审批、报表等全流程管理。

## 🎯 核心功能

### 1. 数据采集与集成
- ✅ 基础信息管理（教职工、职称、院系）
- ✅ 多源数据对接（教务系统、科研系统）
- ✅ 课表与选课人数导入
- ✅ 论文、课题、专利等成果管理

### 2. 计算模型与规则引擎
- ✅ 教学工作量计算：`工作量 = 标准学时 × 课程系数 × 人数系数`
- ✅ 科研与社会服务折算
- ✅ 论文积分量化
- ✅ 竞赛指导折算

### 3. 绩效与津贴核算
- ✅ 定额完成率计算
- ✅ 超课时酬金计算
- ✅ 职称系数调整
- ✅ 津贴标准管理

### 4. 查询、审批与报表
- ✅ 教师自助查询平台
- ✅ 工作量明细查看
- ✅ 多维度报表导出
- ✅ 审批流程管理
- ✅ 数据汇总统计

## 🏗️ 系统架构

```
teacher-workload-system/
├── public/                 # 前端文件
│   ├── index.php          # 入口文件
│   ├── css/               # 样式表
│   ├── js/                # JavaScript
│   ├── images/            # 图片资源
│   └── uploads/           # 上传文件目录
├── app/                   # 应用核心
│   ├── controllers/       # 控制器
│   ├── models/            # 数据模型
│   ├── views/             # 视图模板
│   └── middleware/        # 中间件
├── config/                # 配置文件
│   ├── database.php       # 数据库配置
│   ├── system.php         # 系统配置
│   └── formula.php        # 计算规则配置
├── library/               # 类库
│   ├── Database.php       # 数据库操作类
│   ├── Calculator.php     # 计算引擎
│   ├── Exporter.php       # 数据导出
│   └── Auth.php           # 认证类
├── api/                   # API接口
│   ├── workload.php       # 工作量接口
│   ├── performance.php    # 绩效接口
│   └── report.php         # 报表接口
├── sql/                   # 数据库脚本
│   └── schema.sql         # 表结构
├── logs/                  # 日志目录
├── cache/                 # 缓存目录
└── composer.json          # Composer配置
```

## 🚀 快速开始

### 1. 环境要求
- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- Apache/Nginx Web服务器
- Composer（可选）

### 2. 安装步骤

```bash
# 克隆项目
git clone https://github.com/defoliation611/teacher-workload-system.git
cd teacher-workload-system

# 配置数据库
cp config/database.example.php config/database.php
# 编辑 config/database.php，填入数据库信息

# 导入数据库
mysql -u root -p < sql/schema.sql

# 配置Web服务器指向 public 目录
```

### 3. 默认登录

- **管理员账号**: admin
- **默认密码**: admin123456
- **系统地址**: http://localhost/teacher-workload-system/public

## 📊 数据库设计

### 核心表结构

| 表名 | 说明 |
|------|------|
| users | 用户表（教职工、管理员） |
| departments | 院系表 |
| positions | 职称表 |
| courses | 课程表 |
| teaching_workload | 教学工作量记录 |
| research_workload | 科研工作量记录 |
| papers | 论文表 |
| projects | 项目/课题表 |
| patents | 专利表 |
| student_guidance | 学生指导记录 |
| competitions | 竞赛指导记录 |
| performance | 绩效核算表 |
| salary_standards | 津贴标准表 |
| approval_workflow | 审批流程表 |

## 🔧 工作量计算公式

### 教学工作量

```
教学工作量 = 学时数 × 课程系数 × 人数系数

课程系数:
- 理论课: 1.0
- 实验课: 1.5
- 实践课: 2.0

人数系数:
- ≤30人: 1.0
- 31-50人: 1.1
- 51-100人: 1.2
- >100人: 1.3
```

### 科研工作量折算

```
论文积分:
- 顶级期刊: 10分
- 一级期刊: 8分
- 二级期刊: 6分
- 三级期刊: 4分

项目积分:
- 国家级: 50分
- 省级: 30分
- 市级: 20分
- 校级: 10分

专利积分:
- 发明专利: 20分
- 实用新型: 10分
```

## 👥 用户角色

- **管理员**: 系统管理、参数配置、数据审核
- **教务管理员**: 工作量初审、报表生成
- **科研管理员**: 科研成果审核
- **院系主任**: 院系数据审核
- **教师**: 自助查询、数据申报

## 🔐 安全特性

- ✅ 用户身份认证
- ✅ 角色权限控制(RBAC)
- ✅ SQL注入防护
- ✅ XSS防护
- ✅ CSRF防护
- ✅ 操作日志记录
- ✅ 数据加密存储

## 📝 API文档

详见 `api/README.md`

## 🐛 常见问题

### Q: 如何导入教务系统数据？
A: 使用 `api/import.php` 接口，支持Excel/CSV格式数据导入。

### Q: 如何自定义工作量计算规则？
A: 修改 `config/formula.php` 配置文件，或在后台系统管理中动态配置。

### Q: 报表如何导出？
A: 在报表中心选择导出格式（Excel/PDF/CSV），一键导出。

## 📞 技术支持

如有问题或建议，请提交Issue或联系开发者。

## 📄 许可证

MIT License

---

**最后更新**: 2026年6月
