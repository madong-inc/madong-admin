/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80012
 Source Host           : localhost:3306
 Source Schema         : madong

 Target Server Type    : MySQL
 Target Server Version : 80012
 File Encoding         : 65001

 Date: 12/11/2024 17:50:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ma_cache
-- ----------------------------
DROP TABLE IF EXISTS `ma_cache`;
CREATE TABLE `ma_cache`  (
  `key` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `result` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `expire_time` int(11) NULL DEFAULT 0,
  `create_time` int(11) NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_config
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_config`;
CREATE TABLE `ma_system_config`  (
  `id` bigint(20) NOT NULL COMMENT '配置ID',
  `group_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分组编码',
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '唯一编码',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置名称',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置内容',
  `is_sys` tinyint(1) NULL DEFAULT 0 COMMENT '是否系统',
  `enabled` tinyint(1) NULL DEFAULT 1 COMMENT '是否启用',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_user` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `delete_time` timestamp(0) NULL DEFAULT NULL COMMENT '是否删除',
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_config_code`(`code`) USING BTREE,
  INDEX `idx_config_group_code`(`group_code`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_crontab
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_crontab`;
CREATE TABLE `ma_system_crontab`  (
  `id` bigint(20) UNSIGNED NOT NULL,
  `biz_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务标题',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务类型1 url,2 eval,3 shell',
  `task_cycle` tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务周期',
  `cycle_rule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务周期规则',
  `rule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '任务表达式',
  `target` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '调用任务字符串',
  `running_times` int(11) NOT NULL DEFAULT 0 COMMENT '已运行次数',
  `last_running_time` int(11) NOT NULL DEFAULT 0 COMMENT '上次运行时间',
  `enabled` tinyint(4) NOT NULL DEFAULT 0 COMMENT '任务状态状态0禁用,1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '软删除时间',
  `singleton` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否单次执行0是,1不是',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `title`(`title`) USING BTREE,
  INDEX `status`(`enabled`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时器任务表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_crontab_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_crontab_log`;
CREATE TABLE `ma_system_crontab_log`  (
  `id` bigint(20) UNSIGNED NOT NULL,
  `crontab_id` bigint(20) UNSIGNED NOT NULL COMMENT '任务id',
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务调用目标字符串',
  `log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '任务执行日志',
  `return_code` tinyint(1) NOT NULL DEFAULT 1 COMMENT '执行返回状态,1成功,0失败',
  `running_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '执行所用时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `crontab_id`(`crontab_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时器任务执行日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dept`;
CREATE TABLE `ma_system_dept`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `pid` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '父ID',
  `level` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '组级集合',
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '部门唯一编码',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '部门名称',
  `main_leader_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '负责人',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '联系电话',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`pid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_dept_leader
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dept_leader`;
CREATE TABLE `ma_system_dept_leader`  (
  `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '部门主键',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门领导关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_dict
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dict`;
CREATE TABLE `ma_system_dict`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `group_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典类型',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典名称',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标示',
  `sort` bigint(20) NULL DEFAULT NULL COMMENT '排序',
  `data_type` smallint(6) NULL DEFAULT 1 COMMENT '数据类型',
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '描述',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `created_by` int(11) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int(11) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典类型表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_dict_item
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dict_item`;
CREATE TABLE `ma_system_dict_item`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `dict_id` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '字典类型ID',
  `label` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标签',
  `value` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典值',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标示',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `created_by` int(11) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int(11) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `dict_id`(`dict_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典数据表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_login_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_login_log`;
CREATE TABLE `ma_system_login_log`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `user_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户名',
  `ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '登录IP地址',
  `ip_location` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'IP所属地',
  `os` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '操作系统',
  `browser` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '浏览器',
  `status` smallint(6) NULL DEFAULT 1 COMMENT '登录状态 (1成功 2失败)',
  `message` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '提示消息',
  `login_time` int(11) NULL DEFAULT NULL COMMENT '登录时间',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`user_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '登录日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_menu`;
CREATE TABLE `ma_system_menu`  (
  `id` bigint(20) NOT NULL COMMENT '菜单ID',
  `pid` bigint(20) NOT NULL DEFAULT 0 COMMENT '父ID',
  `app` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用编码',
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单名称',
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '唯一编码',
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '父ID集合',
  `type` int(11) NULL DEFAULT NULL COMMENT '菜单类型1=>目录  2>菜单 3=>按钮 4=>接口',
  `sort` bigint(20) NULL DEFAULT 999 COMMENT '排序',
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '路由地址',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组件地址',
  `redirect` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '重定向',
  `icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单图标',
  `is_show` tinyint(1) NULL DEFAULT 1 COMMENT '是否显示 0=>否   1=>是',
  `is_link` tinyint(1) NULL DEFAULT 0 COMMENT '是否外链 0=>否   1=>是',
  `link_url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '外部链接地址',
  `enabled` tinyint(1) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `open_type` int(11) NULL DEFAULT 0 COMMENT '是否外链 1=>是    0=>否',
  `is_cache` tinyint(1) NULL DEFAULT 0 COMMENT '是否缓存 1=>是    0=>否',
  `is_sync` tinyint(1) NULL DEFAULT 1 COMMENT '是否同步',
  `is_affix` tinyint(1) NULL DEFAULT 0 COMMENT '是否固定tags无法关闭',
  `variable` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '额外参数JSON',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `create_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `update_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '是否删除',
  `methods` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'get' COMMENT '请求方法',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_sys_menu_code`(`code`) USING BTREE,
  INDEX `idx_sys_menu_app_code`(`app`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_operate_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_operate_log`;
CREATE TABLE `ma_system_operate_log`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '内容',
  `app` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用名称',
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求ip',
  `ip_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求ip归属地',
  `browser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '浏览器',
  `os` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作系统',
  `url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求地址',
  `class_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类名称',
  `action` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '方法名称',
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求方式（GET POST PUT DELETE)',
  `param` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求参数',
  `result` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '返回结果',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '操作时间',
  `user_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作账号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统操作日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_post
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_post`;
CREATE TABLE `ma_system_post`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '岗位代码',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '岗位名称',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '岗位信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_role
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_role`;
CREATE TABLE `ma_system_role`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `pid` bigint(20) NULL DEFAULT 0 COMMENT '父级id',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '角色名称',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '角色代码',
  `is_super_admin` tinyint(1) NULL DEFAULT 0 COMMENT '是否超级管理员 1=是   0=否',
  `role_type` tinyint(4) NULL DEFAULT NULL COMMENT '角色类型',
  `data_scope` smallint(6) NULL DEFAULT 1 COMMENT '数据范围(1:全部数据权限 2:自定义数据权限 3:本部门数据权限 4:本部门及以下数据权限 5:本人数据权限)',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_role_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_role_dept`;
CREATE TABLE `ma_system_role_dept`  (
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`role_id`, `dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与部门关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_role_menu`;
CREATE TABLE `ma_system_role_menu`  (
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
  `menu_id` bigint(20) UNSIGNED NOT NULL COMMENT '菜单主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与菜单关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_upload
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_upload`;
CREATE TABLE `ma_system_upload`  (
  `id` bigint(20) NOT NULL COMMENT '文件信息ID',
  `url` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文件访问地址',
  `size` bigint(20) NULL DEFAULT NULL COMMENT '文件大小，单位字节',
  `size_info` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件大小，有单位',
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件名称',
  `original_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '原始文件名',
  `base_path` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '基础存储路径',
  `path` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '存储路径',
  `ext` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件扩展名',
  `content_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'MIME类型',
  `platform` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '存储平台',
  `th_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图访问路径',
  `th_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图大小，单位字节',
  `th_size` bigint(20) NULL DEFAULT NULL COMMENT '缩略图大小，单位字节',
  `th_size_info` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图大小，有单位',
  `th_content_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图MIME类型',
  `object_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件所属对象id',
  `object_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件所属对象类型，例如用户头像，评价图片',
  `attr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '附加属性',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_user` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_user
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user`;
CREATE TABLE `ma_system_user`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID,主键',
  `user_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `real_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户昵称',
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `is_super` tinyint(4) NULL DEFAULT 2 COMMENT '用户类型:(1系统用户 2普通用户)',
  `mobile_phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '手机',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户邮箱',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户头像',
  `signed` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '个人签名',
  `dashboard` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '后台首页类型',
  `dept_id` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '部门ID',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `login_ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '最后登陆IP',
  `login_time` int(11) NULL DEFAULT NULL COMMENT '最后登陆时间',
  `backend_setting` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '后台设置数据',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `sex` tinyint(1) NULL DEFAULT 0 COMMENT '0=未知  1=男 2=女',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  `birthday` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '生日',
  `tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '座机',
  `is_locked` smallint(6) NULL DEFAULT 0 COMMENT '是否锁定（1是 0否）',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`user_name`) USING BTREE,
  INDEX `dept_id`(`dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_user_post
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user_post`;
CREATE TABLE `ma_system_user_post`  (
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `post_id` bigint(20) UNSIGNED NOT NULL COMMENT '岗位主键',
  PRIMARY KEY (`user_id`, `post_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与岗位关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ma_system_user_role
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user_role`;
CREATE TABLE `ma_system_user_role`  (
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与角色关联表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
