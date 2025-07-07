/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80012
 Source Host           : localhost:3306
 Source Schema         : madong_db

 Target Server Type    : MySQL
 Target Server Version : 80012
 File Encoding         : 65001

 Date: 07/07/2025 16:51:26
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
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_cache
-- ----------------------------

-- ----------------------------
-- Table structure for ma_mt_db_setting
-- ----------------------------
DROP TABLE IF EXISTS `ma_mt_db_setting`;
CREATE TABLE `ma_mt_db_setting`  (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '链接名',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '描述',
  `driver` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '驱动类型',
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'ip',
  `port` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '端口',
  `database` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '数据库名称',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '用户密码',
  `prefix` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'ma_' COMMENT '前缀',
  `variable` json NULL COMMENT '变量',
  `is_default` tinyint(1) NULL DEFAULT 0 COMMENT '是否默认数据源',
  `enabled` tinyint(3) UNSIGNED NULL DEFAULT 1 COMMENT '状态',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建人',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新人',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '数据中心配置' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_mt_db_setting
-- ----------------------------
INSERT INTO `ma_mt_db_setting` VALUES (1, 'xxxx 有限公司', '默认数据库', 'mysql', '127.0.0.1', '3306', 'madong_db', 'root', 'root', 'ma_', NULL, 1, 1, 1751877946, 1, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for ma_mt_tenant
-- ----------------------------
DROP TABLE IF EXISTS `ma_mt_tenant`;
CREATE TABLE `ma_mt_tenant`  (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `db_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '数据源名称',
  `code` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '租户编号',
  `type` tinyint(1) NULL DEFAULT NULL COMMENT '0其他  1 企业',
  `contact_person` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '联系电话',
  `company_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '企业名称',
  `license_number` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '统一社会信用代码',
  `address` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '地址',
  `description` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '企业简介',
  `domain` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '域名',
  `enabled` tinyint(1) NULL DEFAULT 1 COMMENT '状态（1正常 0停用）',
  `isolation_mode` tinyint(4) NULL DEFAULT 2 COMMENT '隔离模式（1字段隔离  2库隔离）',
  `is_default` tinyint(1) NULL DEFAULT 0 COMMENT '是否默认',
  `expired_at` bigint(20) UNSIGNED ZEROFILL NULL DEFAULT NULL COMMENT '过期时间',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '删除标志',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '租户\r\n' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_mt_tenant
-- ----------------------------
INSERT INTO `ma_mt_tenant` VALUES (1, 'madong_db', 'platform', 0, 'admin', '18888888888', 'xxxx 有限公司', '', '中国', '内置账号', 'https://www.madong.tech', 1, 2, 1, NULL, NULL, 1751877946, 1, NULL, NULL);

-- ----------------------------
-- Table structure for ma_mt_tenant_package
-- ----------------------------
DROP TABLE IF EXISTS `ma_mt_tenant_package`;
CREATE TABLE `ma_mt_tenant_package`  (
  `tenant_id` bigint(20) UNSIGNED NOT NULL COMMENT '租户ID',
  `subscription_id` bigint(20) UNSIGNED NOT NULL COMMENT '套餐ID'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '租户-订阅关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_mt_tenant_package
-- ----------------------------

-- ----------------------------
-- Table structure for ma_mt_tenant_session
-- ----------------------------
DROP TABLE IF EXISTS `ma_mt_tenant_session`;
CREATE TABLE `ma_mt_tenant_session`  (
  `id` bigint(20) NOT NULL COMMENT '雪花ID',
  `key` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'key',
  `admin_id` bigint(20) NOT NULL COMMENT '管理员ID',
  `tenant_id` bigint(20) NOT NULL COMMENT '租户ID',
  `token` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '会话token',
  `expire_at` bigint(20) NOT NULL COMMENT '过期时间戳',
  `created_at` bigint(20) NOT NULL COMMENT '创建时间戳',
  `updated_at` bigint(20) NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_token`(`token`) USING BTREE,
  INDEX `idx_admin_tenant`(`admin_id`, `tenant_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '租户-会话表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_mt_tenant_session
-- ----------------------------
INSERT INTO `ma_mt_tenant_session` VALUES (401307464169168896, '127.0.0.1-686b8959a66197.54288079', 1, 1, 'd9b09a19548d50a54a67b67fd8f4d60a', 1751885177, 1751877977, 1751877977);

-- ----------------------------
-- Table structure for ma_mt_tenant_subscription
-- ----------------------------
DROP TABLE IF EXISTS `ma_mt_tenant_subscription`;
CREATE TABLE `ma_mt_tenant_subscription`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '雪花ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '套餐ID',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '描述',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `start_time` bigint(20) NULL DEFAULT NULL COMMENT '开始时间戳',
  `end_time` bigint(20) NULL DEFAULT NULL COMMENT '结束时间戳',
  `enabled` tinyint(4) NULL DEFAULT 1 COMMENT '状态:0-已过期,1-有效',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间戳',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间戳',
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '租户-套餐订阅表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_mt_tenant_subscription
-- ----------------------------

-- ----------------------------
-- Table structure for ma_mt_tenant_subscription_casbin
-- ----------------------------
DROP TABLE IF EXISTS `ma_mt_tenant_subscription_casbin`;
CREATE TABLE `ma_mt_tenant_subscription_casbin`  (
  `subscription_id` bigint(20) UNSIGNED NOT NULL COMMENT '套餐主键',
  `subscription_casbin_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'casb 策略关联ID'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '租户-套餐订阅策略关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_mt_tenant_subscription_casbin
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_admin
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_admin`;
CREATE TABLE `ma_sys_admin`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID,主键',
  `user_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '账号',
  `real_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户',
  `nick_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '昵称',
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `is_super` tinyint(4) NULL DEFAULT 2 COMMENT '用户类型:(1系统用户 0普通用户)',
  `mobile_phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '手机',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户邮箱',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户头像',
  `signed` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '个人签名',
  `dashboard` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '后台首页类型',
  `dept_id` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '部门ID',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `login_ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '最后登陆IP',
  `login_time` int(11) NULL DEFAULT NULL COMMENT '最后登陆时间',
  `backend_setting` json NULL COMMENT '后台设置数据',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
  `deleted_at` bigint(11) UNSIGNED ZEROFILL NULL DEFAULT NULL COMMENT '删除时间',
  `sex` tinyint(1) NULL DEFAULT 0 COMMENT '0=未知  1=男 2=女',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  `birthday` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '生日',
  `tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '座机',
  `is_locked` smallint(6) NULL DEFAULT 0 COMMENT '是否锁定（1是 0否）',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`user_name`) USING BTREE,
  INDEX `dept_id`(`dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_admin
-- ----------------------------
INSERT INTO `ma_sys_admin` VALUES (1, 'admin', '超级管理员', '超级管理员', '$2y$10$ZzkWCNkv8jNfxmOqKHMCU.g5jTpIx0mXZYcQCD5AS/w9pxZbrNwHa', 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, '127.0.0.1', 1751877977, NULL, NULL, NULL, NULL, 1751877977, NULL, 0, NULL, NULL, NULL, 0);

-- ----------------------------
-- Table structure for ma_sys_admin_casbin
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_admin_casbin`;
CREATE TABLE `ma_sys_admin_casbin`  (
  `admin_id` bigint(20) UNSIGNED NOT NULL COMMENT '管理员主键',
  `admin_casbin_id` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '对应casbin策略表'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与策略关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_admin_casbin
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_admin_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_admin_dept`;
CREATE TABLE `ma_sys_admin_dept`  (
  `admin_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`admin_id`, `dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与部门关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_admin_dept
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_admin_post
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_admin_post`;
CREATE TABLE `ma_sys_admin_post`  (
  `admin_id` bigint(20) UNSIGNED NOT NULL COMMENT '管理员主键',
  `post_id` bigint(20) UNSIGNED NOT NULL COMMENT '岗位主键',
  PRIMARY KEY (`admin_id`, `post_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与岗位关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_admin_post
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_admin_role`;
CREATE TABLE `ma_sys_admin_role`  (
  `admin_id` bigint(20) UNSIGNED NOT NULL COMMENT '管理员主键',
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与角色关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_admin_role
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_admin_tenant
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_admin_tenant`;
CREATE TABLE `ma_sys_admin_tenant`  (
  `id` bigint(20) NOT NULL COMMENT '雪花ID',
  `admin_id` bigint(20) NOT NULL COMMENT '管理员ID',
  `tenant_id` bigint(20) NOT NULL COMMENT '租户ID',
  `is_super` tinyint(4) NOT NULL DEFAULT 2 COMMENT '角色:1-管理员,2-普通用户',
  `is_default` tinyint(4) NULL DEFAULT 0 COMMENT '是否默认租户',
  `priority` int(11) NULL DEFAULT 0 COMMENT '优先级(数值越小优先级越高)',
  `created_at` bigint(20) NOT NULL COMMENT '创建时间戳',
  `updated_at` bigint(20) NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_admin_tenant`(`admin_id`, `tenant_id`) USING BTREE,
  INDEX `idx_tenant_id`(`tenant_id`) USING BTREE,
  INDEX `idx_admin_primary`(`admin_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '租户-管理员关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_admin_tenant
-- ----------------------------
INSERT INTO `ma_sys_admin_tenant` VALUES (401307202268438528, 1, 1, 1, 1, -1, 0, 0);

-- ----------------------------
-- Table structure for ma_sys_casbin_rule
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_casbin_rule`;
CREATE TABLE `ma_sys_casbin_rule`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID',
  `ptype` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '策略类型',
  `v0` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '主体(subject)',
  `v1` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '域(domain)',
  `v2` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '资源(resource)',
  `v3` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '动作(action)',
  `v4` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '扩展字段1',
  `v5` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '扩展字段2',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_ptype`(`ptype`) USING BTREE,
  INDEX `idx_v0`(`v0`) USING BTREE,
  INDEX `idx_v1`(`v1`) USING BTREE,
  INDEX `idx_v2`(`v2`) USING BTREE,
  INDEX `idx_v3`(`v3`) USING BTREE,
  INDEX `idx_v4`(`v4`) USING BTREE,
  INDEX `idx_v5`(`v5`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Casbin策略规则表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_casbin_rule
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_config
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_config`;
CREATE TABLE `ma_sys_config`  (
  `id` bigint(20) NOT NULL COMMENT '配置ID',
  `tenant_id` bigint(20) NULL DEFAULT NULL COMMENT '租户ID',
  `group_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分组编码',
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '唯一编码',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置名称',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置内容',
  `is_sys` tinyint(1) NULL DEFAULT 0 COMMENT '是否系统',
  `enabled` tinyint(1) NULL DEFAULT 1 COMMENT '是否启用',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '是否删除',
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_config_code`(`code`) USING BTREE,
  INDEX `idx_config_group_code`(`group_code`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_config
-- ----------------------------
INSERT INTO `ma_sys_config` VALUES (401307201698013184, 1, 'local', 'root', '', 'public', 0, 1, 1732029810, 0, 1732029878, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201706401792, 1, 'local', 'dirname', '', 'upload', 0, 1, 1732029810, 0, 1732029878, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201706401793, 1, 'local', 'domain', '', 'http=>>//43.138.153.216=>>8899/', 0, 1, 1732029810, 0, 1732029878, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201714790400, 1, 'oss', 'accessKeyId', '', '1', 0, 1, 1732030257, 0, 1732030257, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201714790401, 1, 'oss', 'accessKeySecret', '', '2', 0, 1, 1732030257, 0, 1732030257, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201714790402, 1, 'oss', 'bucket', '', '3', 0, 1, 1732030257, 0, 1732030257, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201714790403, 1, 'oss', 'dirname', '', '4', 0, 1, 1732030257, 0, 1732030257, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201714790404, 1, 'oss', 'domain', '', '5', 0, 1, 1732030257, 0, 1732030257, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201723179008, 1, 'oss', 'endpoint', '', '6', 0, 1, 1732030257, 0, 1732030257, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201723179009, 1, 'oss', 'remark', '', '7', 0, 1, 1732030257, 0, 1732030257, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201723179010, 1, 'cos', 'secretId', '', '11', 0, 1, 1732030301, 0, 1732030301, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201723179011, 1, 'cos', 'secretKey', '', '22', 0, 1, 1732030301, 0, 1732030301, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201723179012, 1, 'cos', 'bucket', '', '33', 0, 1, 1732030301, 0, 1732030301, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201731567616, 1, 'cos', 'dirname', '', '44', 0, 1, 1732030301, 0, 1732030301, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201731567617, 1, 'cos', 'domain', '', '55', 0, 1, 1732030301, 0, 1732030301, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201731567618, 1, 'cos', 'region', '', '66', 0, 1, 1732030301, 0, 1732030301, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201731567619, 1, 'cos', 'remark', '', '77', 0, 1, 1732030301, 0, 1732030301, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201731567620, 1, 'qiniu', 'accessKey', '', '99', 0, 1, 1732030319, 0, 1732030319, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201739956224, 1, 'qiniu', 'secretKey', '', '88', 0, 1, 1732030319, 0, 1732030319, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201739956225, 1, 'qiniu', 'bucket', '', '7', 0, 1, 1732030319, 0, 1732030319, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201739956226, 1, 'qiniu', 'dirname', '', '78', 0, 1, 1732030319, 0, 1732030319, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201739956227, 1, 'qiniu', 'domain', '', '8', 0, 1, 1732030319, 0, 1732030319, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201739956228, 1, 'qiniu', 'region', '', '', 0, 1, 1732030319, 0, 1732030319, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201748344832, 1, 'qiniu', 'remark', '', '897', 0, 1, 1732030319, 0, 1732030319, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201748344833, 1, 's3', 'key', '', '12', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201748344834, 1, 's3', 'secret', '', '12', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201748344835, 1, 's3', 'bucket', '', '12', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201748344836, 1, 's3', 'dirname', '', '12', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201748344837, 1, 's3', 'domain', '', '12', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201756733440, 1, 's3', 'endpoint', '', '12', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201756733441, 1, 's3', 'region', '', '12', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201756733442, 1, 's3', 'acl', '', '6', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201756733443, 1, 's3', 'remark', '', '4', 0, 1, 1732030332, 0, 1732030332, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201756733444, 1, 'email_setting', 'SMTPSecure', '', 'ssl', 0, 1, 1732031603, 0, 1732429949, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201765122048, 1, 'email_setting', 'Host', '', 'smtp.qq.com', 0, 1, 1732031603, 0, 1732429949, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201765122049, 1, 'email_setting', 'Port', '', '465', 0, 1, 1732031603, 0, 1732429949, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201765122050, 1, 'email_setting', 'Username', '', 'kzhzjdyw888@qq.com', 0, 1, 1732031603, 0, 1732429949, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201765122051, 1, 'email_setting', 'Password', '', '', 0, 1, 1732031603, 0, 1732429949, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201773510656, 1, 'email_setting', 'From', '', '', 0, 1, 1732031603, 0, 1732429949, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201773510657, 1, 'email_setting', 'FromName', '', 'kzhzjdyw888@qq.com', 0, 1, 1732031603, 0, 1732429949, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201773510658, 1, 'basic_upload_setting', 'mode', '上传模式', 'local', 0, 1, 1732415050, 0, 1732429734, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201773510659, 1, 'basic_upload_setting', 'single_limit', '上传大小', '1024', 0, 1, 1732415050, 0, 1732429734, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201773510660, 1, 'basic_upload_setting', 'total_limit', '文件限制', '1024', 0, 1, 1732415050, 0, 1732429734, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201773510661, 1, 'basic_upload_setting', 'nums', '数量限制', '10', 0, 1, 1732415050, 0, 1732429734, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201781899264, 1, 'basic_upload_setting', 'exclude', '不允许文件类型', 'php,ext,exe', 0, 1, 1732415050, 0, 1732429734, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201781899265, 1, 'site_setting', 'site_open', '站点开启', '1', 0, 1, 1732429827, 0, 1732430264, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201781899266, 1, 'site_setting', 'site_url', '网站地址', 'http://127.0.0.1:8998', 0, 1, 1732429827, 0, 1732430264, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201781899267, 1, 'site_setting', 'site_name', '站点名称', 'madong-admin', 0, 1, 1732429827, 0, 1732430264, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201790287872, 1, 'site_setting', 'site_logo', '站点Logo', 'http://127.0.0.1:8998/upload/084926cafbc46ae315d34a25e4971da6.jpeg', 0, 1, 1732429827, 0, 1751878000, 1, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201790287873, 1, 'site_setting', 'site_network_security', '网备案号', '2024042441号-2', 0, 1, 1732429827, 0, 1732430264, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201790287874, 1, 'site_setting', 'site_description', '网站描述', '快速开发框架', 0, 1, 1732429827, 0, 1732430264, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201798676480, 1, 'site_setting', 'site_record_no', '网站ICP', '2024042442', 0, 1, 1732429827, 0, 1732430264, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201798676481, 1, 'site_setting', 'site_icp_url', 'ICP URL', 'https://beian.miit.gov.cn/', 0, 1, 1732429827, 0, 1732430264, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201798676482, 1, 'site_setting', 'site_network_security_url', '网安备案链接', '', 0, 1, 1732429827, 0, 1732430264, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201798676483, 1, 'sms_setting', 'enable', '是否开启', '1', 0, 1, 1732429997, 0, 1732430097, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201798676484, 1, 'sms_setting', 'access_key_id', 'access_key_id', '234813346262818816', 0, 1, 1732429997, 0, 1732430097, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201807065088, 1, 'sms_setting', 'access_key_secret', 'access_key_secret', '238164553517768704', 0, 1, 1732429997, 0, 1732430097, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307201807065089, 1, 'sms_setting', 'sign_name', 'sign_name', '【码动开源】，你的验证码是{code}，有效期5分钟。', 0, 1, 1732429997, 0, 1732430097, 0, 0, '');
INSERT INTO `ma_sys_config` VALUES (401307654263414784, 1, 'site_setting', 'site_keywords', '关键字', '', 0, 1, 1751878000, 1, 1751878000, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for ma_sys_crontab
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_crontab`;
CREATE TABLE `ma_sys_crontab`  (
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
  `created_at` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建人',
  `deleted_at` bigint(20) NOT NULL DEFAULT 0 COMMENT '软删除时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新人',
  `singleton` tinyint(1) NULL DEFAULT 1 COMMENT '是否循环执行1 ',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `title`(`title`) USING BTREE,
  INDEX `status`(`enabled`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时器任务表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_crontab
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_crontab_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_crontab_log`;
CREATE TABLE `ma_sys_crontab_log`  (
  `id` bigint(20) UNSIGNED NOT NULL,
  `crontab_id` bigint(20) UNSIGNED NOT NULL COMMENT '任务id',
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务调用目标字符串',
  `log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '任务执行日志',
  `return_code` tinyint(1) NOT NULL DEFAULT 1 COMMENT '执行返回状态,1成功,0失败',
  `running_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '执行所用时间',
  `created_at` bigint(20) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`created_at`) USING BTREE,
  INDEX `crontab_id`(`crontab_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时器任务执行日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_crontab_log
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_dept`;
CREATE TABLE `ma_sys_dept`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `tenant_id` bigint(20) NULL DEFAULT NULL COMMENT '租户隔离ID',
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
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '修改时间',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`pid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_dept
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_dept_leader
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_dept_leader`;
CREATE TABLE `ma_sys_dept_leader`  (
  `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '部门主键',
  `admin_id` bigint(20) UNSIGNED NOT NULL COMMENT '管理员主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门领导关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_dept_leader
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_dict
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_dict`;
CREATE TABLE `ma_sys_dict`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `group_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典类型',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典名称',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标示',
  `sort` bigint(20) NULL DEFAULT 0 COMMENT '排序',
  `data_type` smallint(6) NULL DEFAULT 1 COMMENT '数据类型',
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '描述',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '修改时间',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典类型表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_dict
-- ----------------------------
INSERT INTO `ma_sys_dict` VALUES (401307201530241025, 'default', '所属分组', 'sys_dict_group_code', 1, 1, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201555406849, 'default', '字典类型', 'sys_dict_data_type', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201563795456, 'default', '是否', 'yes_no', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201563795459, 'default', '性别', 'sex', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201572184064, 'default', '菜单类型', 'sys_menu_type', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201572184069, 'default', '菜单打开类型', 'sys_menu_open_type', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201580572676, 'default', '是否超级管理员', 'sys_user_admin_type', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201588961281, 'default', '角色类型', 'sys_role_role_type', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201588961284, 'default', '请求类型', 'request_mode', 1, 1, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201614127105, 'default', '定时任务模式', 'monitor_crontab_mode', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201614127108, 'default', '定时任务类型', 'monitor_crontab_type', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201622515714, 'default', '定时任务执行周期', 'monitor_crontab_cycle', 1, 2, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201630904325, 'default', 'CPU监控', 'monitor_server_cpu', 1, 1, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201647681536, 'default', '内存监控', 'monitor_server_memory', 1, 1, '', 1, 1, 1, NULL, NULL, NULL);
INSERT INTO `ma_sys_dict` VALUES (401307201656070145, 'default', '配置分组', 'sys_group', 1, 1, '', 1, 1, 1, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for ma_sys_dict_item
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_dict_item`;
CREATE TABLE `ma_sys_dict_item`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `dict_id` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '字典类型ID',
  `label` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标签',
  `value` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典值',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标示',
  `color` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'tag颜色',
  `other_class` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '修改时间',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `dict_id`(`dict_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典数据表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_dict_item
-- ----------------------------
INSERT INTO `ma_sys_dict_item` VALUES (401307201547018240, 401307201530241025, '默认分组', 'value1', 'default', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201555406848, 401307201530241025, '其他', 'value2', 'other', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201555406850, 401307201555406849, '字符串', '1', 'sys_dict_data_type', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201555406851, 401307201555406849, '整型', '2', 'sys_dict_data_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201563795457, 401307201563795456, '是', '1', 'yes_no', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201563795458, 401307201563795456, '否', '0', 'yes_no', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201563795460, 401307201563795459, '男', '1', 'sex', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201563795461, 401307201563795459, '女', '2', 'sex', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201572184065, 401307201572184064, '目录', '1', 'sys_menu_type', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201572184066, 401307201572184064, '菜单', '2', 'sys_menu_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201572184067, 401307201572184064, '按钮', '3', 'sys_menu_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201572184068, 401307201572184064, '接口', '4', 'sys_menu_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201580572672, 401307201572184069, '无', '0', 'sys_menu_open_type', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201580572673, 401307201572184069, '组件', '1', 'sys_menu_open_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201580572674, 401307201572184069, '内链', '2', 'sys_menu_open_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201580572675, 401307201572184069, '外链', '3', 'sys_menu_open_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201580572677, 401307201580572676, '超级管理员', '1', 'sys_user_admin_type', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201588961280, 401307201580572676, '普通管理员', '2', 'sys_user_admin_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201588961282, 401307201588961281, '普通角色', '1', 'sys_role_role_type', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201588961283, 401307201588961281, '数据角色', '2', 'sys_role_role_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201597349888, 401307201588961284, 'GET', 'GET', 'request_mode', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201597349889, 401307201588961284, 'POST', 'POST', 'request_mode', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201597349890, 401307201588961284, 'PUT', 'PUT', 'request_mode', NULL, NULL, 3, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201614127104, 401307201588961284, 'DELETE', 'DELETE', 'request_mode', NULL, NULL, 4, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201614127106, 401307201614127105, '单次', '0', 'monitor_crontab_mode', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201614127107, 401307201614127105, '循环', '1', 'monitor_crontab_mode', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201614127109, 401307201614127108, 'url', '1', 'monitor_crontab_type', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201622515712, 401307201614127108, 'eval', '2', 'monitor_crontab_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201622515713, 401307201614127108, 'shell', '3', 'monitor_crontab_type', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201622515715, 401307201622515714, '每天', '1', 'monitor_crontab_cycle', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201622515716, 401307201622515714, '每小时', '2', 'monitor_crontab_cycle', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201622515717, 401307201622515714, 'N小时', '3', 'monitor_crontab_cycle', NULL, NULL, 3, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201630904320, 401307201622515714, 'N分钟', '4', 'monitor_crontab_cycle', NULL, NULL, 4, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201630904321, 401307201622515714, 'N秒', '5', 'monitor_crontab_cycle', NULL, NULL, 5, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201630904322, 401307201622515714, '每星期', '6', 'monitor_crontab_cycle', NULL, NULL, 6, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201630904323, 401307201622515714, '每年', '7', 'monitor_crontab_cycle', NULL, NULL, 7, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201630904324, 401307201622515714, '每年', '8', 'monitor_crontab_cycle', NULL, NULL, 8, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201639292928, 401307201630904325, '型号', 'cpu_name', 'monitor_server_cpu', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201639292929, 401307201630904325, '物理核心数', 'physical_cores', 'monitor_server_cpu', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201639292930, 401307201630904325, '逻辑核心数', 'logical_cores', 'monitor_server_cpu', NULL, NULL, 3, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201639292931, 401307201630904325, '缓存大小(MB)', 'cache_size_mb', 'monitor_server_cpu', NULL, NULL, 4, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201639292932, 401307201630904325, 'CPU使用百分比', 'cpu_usage_percentage', 'monitor_server_cpu', NULL, NULL, 5, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201639292933, 401307201630904325, '空闲 CPU 百分比', 'free_cpu_percentage', 'monitor_server_cpu', NULL, NULL, 6, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201647681537, 401307201647681536, '内存使用率', 'memory_usage_rate', 'monitor_server_memory', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201647681538, 401307201647681536, '总内存', 'total_memory', 'monitor_server_memory', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201647681539, 401307201647681536, '可用内存', 'available_memory', 'monitor_server_memory', NULL, NULL, 3, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201647681540, 401307201647681536, '已用内存', 'used_memory', 'monitor_server_memory', NULL, NULL, 4, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201656070144, 401307201647681536, 'PHP内存使用', 'php_memory_usage', 'monitor_server_memory', NULL, NULL, 5, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201656070146, 401307201656070145, '系统配置', 'system_config', 'sys_group', NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, '');
INSERT INTO `ma_sys_dict_item` VALUES (401307201664458752, 401307201656070145, '上传配置', 'system_storage', 'sys_group', NULL, NULL, 2, 1, 1, 1, NULL, NULL, NULL, '');

-- ----------------------------
-- Table structure for ma_sys_login_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_login_log`;
CREATE TABLE `ma_sys_login_log`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `tenant_id` bigint(20) NULL DEFAULT NULL COMMENT '租户id',
  `user_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户名',
  `ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '登录IP地址',
  `ip_location` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'IP所属地',
  `os` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '操作系统',
  `browser` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '浏览器',
  `status` smallint(6) NULL DEFAULT 1 COMMENT '登录状态 (1成功 2失败)',
  `message` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '提示消息',
  `login_time` bigint(20) NULL DEFAULT NULL COMMENT '登录时间',
  `key` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'key',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `expires_at` bigint(20) NULL DEFAULT NULL COMMENT '过期时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`user_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '登录日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_login_log
-- ----------------------------
INSERT INTO `ma_sys_login_log` VALUES (401307464060116992, 1, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '', 1751877977, 'd9b09a19548d50a54a67b67fd8f4d60a', 1751877977, 1751885177, 1751877977, NULL, NULL);

-- ----------------------------
-- Table structure for ma_sys_menu
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_menu`;
CREATE TABLE `ma_sys_menu`  (
  `id` bigint(20) NOT NULL COMMENT '菜单ID',
  `pid` bigint(20) NOT NULL DEFAULT 0 COMMENT '父ID',
  `app` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用编码',
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单名称',
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '唯一编码',
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '父ID集合',
  `type` int(11) NULL DEFAULT NULL COMMENT '菜单类型1=>目录  2>菜单 3=>按钮 4=>接口 5=>内链 6=>外链',
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
  `is_global` tinyint(1) NULL DEFAULT 0 COMMENT '是否全局公共菜单 ',
  `variable` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '额外参数JSON',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `deleted_at` bigint(20) UNSIGNED ZEROFILL NULL DEFAULT NULL COMMENT '是否删除',
  `methods` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'get' COMMENT '请求方法',
  `is_frame` tinyint(1) NULL DEFAULT NULL COMMENT '是否外链',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_sys_menu_code`(`code`) USING BTREE,
  INDEX `idx_sys_menu_app_code`(`app`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_menu
-- ----------------------------
INSERT INTO `ma_sys_menu` VALUES (401307201312137216, 0, 'admin', '首页', 'Dashboard', NULL, 1, -1, '/', 'BasicLayout', '/workspace', 'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201328914432, 401307201312137216, 'admin', '分析页', 'Analytics', NULL, 1, -1, '/analytics', '/dashboard/analytics/index', NULL, 'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201328914433, 401307201312137216, 'admin', '工作台', 'Workspace', NULL, 1, -1, '/workspace', '/dashboard/workspace/index', NULL, 'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201337303040, 0, 'admin', '系统监控', 'monitors', NULL, 1, 999, '/monitors', 'BasicLayout', NULL, 'ant-design:video-camera-filled', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201337303041, 401307201337303040, 'admin', 'Redis监控', 'monitors:redis', NULL, 2, 999, '/monitors/redis', '/monitor/redis/index', NULL, 'ant-design:trademark-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201337303042, 401307201337303040, 'admin', '性能监控', 'monitors:monitor:server', NULL, 2, 999, '/monitors/server', '/monitor/server/index', NULL, 'ant-design:line-chart-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201337303043, 401307201337303040, 'admin', '操作日志', 'system:logs_operate', NULL, 2, 999, '/monitor/logs/operate', '/system/logs/operate/index', NULL, 'ant-design:schedule-filled', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201337303044, 401307201337303043, 'admin', '删除', 'system:logs_operate:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201345691648, 401307201337303043, 'admin', '详情', 'system:logs_operate:detail', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201345691649, 401307201337303040, 'admin', '登录日志', 'system:logs:login', NULL, 2, 999, '/monitor/logs/login', '/system/logs/login/index', NULL, 'ant-design:credit-card-filled', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201345691650, 401307201345691649, 'admin', '删除', 'system:logs_login:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201345691651, 0, 'admin', '平台管理', 'platform', NULL, 1, 999, '/platform', NULL, NULL, 'clarity-thin-client-line', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201345691652, 401307201345691651, 'admin', '账套管理', 'platform:tenant', NULL, 1, 999, '/platform/tenant', '', NULL, 'ant-design:code-sandbox-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201354080256, 401307201345691652, 'admin', '账套中心', 'platform:tenant:list', NULL, 2, 999, '/platform/tenant/list', '/platform/tenant/index', NULL, 'ant-design:code-sandbox-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201354080257, 401307201354080256, 'admin', '新增', 'platform:tenant:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201354080258, 401307201354080256, 'admin', '详情', 'platform:tenant:detail', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201354080259, 401307201354080256, 'admin', '删除', 'platform:tenant:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201354080260, 401307201354080256, 'admin', '编辑', 'platform:tenant:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201354080261, 401307201345691652, 'admin', '多数据源', 'platform:db', NULL, 2, 999, '/platform/db', '/platform/db/index', NULL, 'ant-design:database-filled', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201362468864, 401307201354080261, 'admin', '新增', 'platform:db:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201362468865, 401307201354080261, 'admin', '编辑', 'platform:db:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201362468866, 401307201354080261, 'admin', '详情', 'platform:db:detail', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201362468867, 401307201354080261, 'admin', '删除', 'platform:db:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201362468868, 401307201345691652, 'admin', '套餐订阅', 'platform:tenant:subscription', NULL, 2, 999, '/platform/tenant_subscription', '/platform/tenant-subscription/index', NULL, 'ant-design:carry-out-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201370857472, 401307201362468868, 'admin', '新增', 'platform:tenant:subscription:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201370857473, 401307201362468868, 'admin', '编辑', 'platform:tenant:subscription:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201370857474, 401307201362468868, 'admin', '删除', 'platform:tenant:subscription:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201379246080, 401307201345691652, 'admin', '成员管理', 'platform:tenant:member', NULL, 2, 999, '/platform/tenant-member', '/platform/tenant-member/index', NULL, 'ant-design:user-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201379246081, 401307201379246080, 'admin', '租户管理', 'platform:tenant_member:managed_tenants', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201379246082, 401307201379246080, 'admin', '新增', 'platform:tenant_member:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201379246083, 401307201379246080, 'admin', '删除', 'platform:tenant_member:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201379246084, 401307201379246080, 'admin', '编辑', 'platform:tenant_member:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201387634688, 401307201379246080, 'admin', '租户-新增', 'platform:tenant_admin:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201387634689, 401307201379246080, 'admin', '租户-删除', 'platform:tenant_admin:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201387634690, 401307201379246080, 'admin', '租户-编辑', 'platform:tenant_admin:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201387634691, 0, 'admin', '开发平台', 'dev', NULL, 1, 999, '/dev', NULL, NULL, 'ant-design:appstore-add-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201387634692, 401307201387634691, 'admin', '定时任务', 'dev:crontab', NULL, 2, 999, '/dev/crontab', '/dev/crontab/index', NULL, 'arcticons:jobstreet', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201396023296, 401307201387634692, 'admin', '删除', 'dev:crontab:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201396023297, 401307201387634692, 'admin', '执行', 'dev:crontab:execute', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201396023298, 401307201387634692, 'admin', '编辑', 'dev:crontab:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201404411904, 401307201387634692, 'admin', '新增', 'dev:crontab:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201404411905, 401307201387634692, 'admin', '停止', 'dev:crontab:pause', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201404411906, 401307201387634692, 'admin', '日志', 'dev:crontab:logs', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201404411907, 401307201387634692, 'admin', '恢复', 'dev:crontab:resume', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201404411908, 401307201387634691, 'admin', '网关管理', 'dev:gateway', NULL, 1, 999, '/dev/gateway', NULL, NULL, 'ant-design:gateway-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201412800512, 401307201404411908, 'admin', '限流规则', 'dev:gateway:limit', NULL, 2, 999, '/dev/gateway/limiter', '/dev/gateway/limiter/index', NULL, 'carbon-rule', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201412800513, 401307201412800512, 'admin', '删除', 'dev:gateway_limiter:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201412800514, 401307201412800512, 'admin', '编辑', 'dev:gateway_limiter:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201412800515, 401307201412800512, 'admin', '新增', 'dev:gateway_limiter:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201421189120, 401307201412800512, 'admin', '状态', 'dev:gateway_limiter:status', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201421189121, 401307201404411908, 'admin', '限访名单', 'dev:gateway:blacklist', NULL, 2, 999, '/dev/gateway/blacklist', '/dev/gateway/blacklist/index', NULL, 'carbon-ai-status-rejected', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201421189122, 401307201421189121, 'admin', '新增', 'dev:gateway_blacklist:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201421189123, 401307201421189121, 'admin', '状态', 'dev:gateway_blacklist:status', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201429577728, 401307201421189121, 'admin', '删除', 'dev:gateway_blacklist:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201429577729, 401307201421189121, 'admin', '编辑', 'dev:gateway_blacklist:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201429577730, 0, 'admin', '人工智能', 'ai', NULL, 1, 999, '/ai', NULL, NULL, 'carbon:ai-recommend', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201429577731, 401307201429577730, 'admin', '智能问答', 'ai:chat', NULL, 2, 999, '/ai/agent', '/ai/agent/index', NULL, 'mingcute:kakao-talk-line', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201429577732, 0, 'admin', '系统设置', 'system', NULL, 1, 1000, '/system', 'BasicLayout', '', 'ant-design:setting-outlined', 1, 0, NULL, 1, 0, 1, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201437966336, 401307201429577732, 'admin', '附件管理', 'system:files', NULL, 2, 999, '/system/files', '/system/files/index', NULL, 'ant-design:folder-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201437966337, 401307201437966336, 'admin', '删除', 'system:files:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201437966338, 401307201437966336, 'admin', '下载', 'system:files:download', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201437966339, 401307201429577732, 'admin', '通知公告', 'system:notice', NULL, 2, 999, '/system/notice', '/system/notice/index', NULL, 'ant-design:comment-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201446354944, 401307201437966339, 'admin', '编辑', 'system:notice:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201446354945, 401307201437966339, 'admin', '删除', 'system:notice:delete', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201446354946, 401307201437966339, 'admin', '新增', 'system:notice:create', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201446354947, 401307201429577732, 'admin', '菜单管理', 'system:menu', NULL, 2, 1000, '/system/menu', '/system/menu/index', NULL, 'ant-design:menu-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201446354948, 401307201446354947, 'admin', '新增', 'system:menu:create', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201454743552, 401307201446354947, 'admin', '删除', 'system:menu:delete', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201454743553, 401307201446354947, 'admin', '编辑', 'system:menu:edit', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201454743554, 401307201429577732, 'admin', '部门管理', 'system:dept', NULL, 2, 1000, '/system/dept', '/system/dept/index', NULL, 'ant-design:facebook-filled', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201454743555, 401307201454743554, 'admin', '新增', 'system:dept:create', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201463132160, 401307201454743554, 'admin', '删除', 'system:dept:delete', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201463132161, 401307201454743554, 'admin', '编辑', 'system:dept:edit', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201463132162, 401307201429577732, 'admin', '职位管理', 'system:post', NULL, 2, 1000, '/system/post', '/system/post/index', NULL, 'ant-design:database-filled', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201463132163, 401307201463132162, 'admin', '添加', 'system:post:create', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201463132164, 401307201463132162, 'admin', '删除', 'system:post:delete', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201471520768, 401307201463132162, 'admin', '编辑', 'system:post:edit', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201471520769, 401307201429577732, 'admin', '用户管理', 'system:user', NULL, 2, 1000, '/system/user', '/system/user/index', NULL, 'ant-design:user-add-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201471520770, 401307201471520769, 'admin', '添加', 'system:user:create', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201471520771, 401307201471520769, 'admin', '删除', 'system:user:delete', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201479909376, 401307201471520769, 'admin', '编辑', 'system:user:edit', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201479909377, 401307201471520769, 'admin', '重置密码', 'system:user:reset_password', NULL, 3, 50, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201479909378, 401307201471520769, 'admin', '冻结用户', 'system:user:locked', NULL, 3, 70, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201479909379, 401307201471520769, 'admin', '取消冻结', 'system:user:un_locked', NULL, 3, 80, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201479909380, 401307201429577732, 'admin', '角色管理', 'system:role', NULL, 2, 1000, '/system/role', '/system/role/index', NULL, 'ant-design:team-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201488297984, 401307201479909380, 'admin', '新增', 'system:role:create', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201488297985, 401307201479909380, 'admin', '删除', 'system:role:delete', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201488297986, 401307201479909380, 'admin', '编辑', 'system:rbac:edit', NULL, 3, 50, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201488297987, 401307201479909380, 'admin', '分配用户', 'system:role:user', NULL, 3, 60, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201496686592, 401307201479909380, 'admin', '添加用户', 'system:auth:user_role', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201496686593, 401307201479909380, 'admin', '移除用户', 'system:auth:remove_user_role', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201496686594, 401307201429577732, 'admin', '数据字典', 'system:dict', NULL, 2, 1000, '/system/dict', '/system/dict/index', NULL, 'ant-design:profile-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201496686595, 401307201496686594, 'admin', '新增', 'system:dict:create', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201505075200, 401307201496686594, 'admin', '删除', 'system:dict:delete', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201505075201, 401307201496686594, 'admin', '编辑', 'system:dict:edit', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201505075202, 401307201496686594, 'admin', '枚举字典', 'system:dict:enum', NULL, 3, 40, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201505075203, 401307201496686594, 'admin', '字典项列表', 'system:dict_item:list', NULL, 3, 50, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201513463808, 401307201496686594, 'admin', '字典项添加', 'system:dict_item:create', NULL, 3, 60, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201513463809, 401307201496686594, 'admin', '字典项删除', 'system:dict_item:delete', NULL, 3, 70, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201513463810, 401307201496686594, 'admin', '字典项编辑', 'system:dict_item:edit', NULL, 3, 80, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201521852416, 401307201429577732, 'admin', '系统参数', 'system:config', NULL, 2, 999999, '/systen/config', '/system/config/index', NULL, 'ant-design:tool-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201521852417, 401307201429577732, 'admin', '数据回收', 'system:recycle_bin', NULL, 2, 999999999, '/system/recycle-bin', '/system/recycle-bin/index', NULL, 'ant-design:rest-filled', 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201521852418, 401307201521852417, 'admin', '详情', 'system:recycle_bin:detail', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201521852419, 401307201521852417, 'admin', '恢复', 'system:recycle_bin:recover', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);
INSERT INTO `ma_sys_menu` VALUES (401307201530241024, 401307201521852417, 'admin', '删除', 'system:recycle_bin:remove', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'get', NULL);

-- ----------------------------
-- Table structure for ma_sys_message
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_message`;
CREATE TABLE `ma_sys_message`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `type` tinyint(4) NOT NULL COMMENT '消息类型参考枚举类',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '消息标题（日志类消息可为空）',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '消息内容（支持富文本存储）',
  `sender_id` bigint(20) NULL DEFAULT 0 COMMENT '发送者ID（0表示系统发送）',
  `receiver_id` bigint(20) NOT NULL COMMENT '接收者ID（可关联用户表）',
  `status` enum('unread','read') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'unread' COMMENT '消息状态',
  `priority` tinyint(1) NULL DEFAULT 3 COMMENT '优先级（1紧急 2急迫 3普通）',
  `channel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'message' COMMENT '发送渠道',
  `related_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '关联业务ID（如订单号、日志ID等）',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `expired_at` bigint(20) NULL DEFAULT NULL COMMENT '过期时间',
  `read_at` bigint(20) NULL DEFAULT NULL COMMENT '已读时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '系统消息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_message
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_notice
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_notice`;
CREATE TABLE `ma_sys_notice`  (
  `id` bigint(20) NOT NULL COMMENT '公告ID',
  `tenant_id` bigint(20) NULL DEFAULT NULL COMMENT '租户id',
  `type` enum('announcement','notice') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'announcement' COMMENT '公告类型（notice=>通知 announcement=>公告）',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '公告标题',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '公告内容',
  `sort` int(11) NULL DEFAULT 10 COMMENT '排序',
  `enabled` tinyint(1) NULL DEFAULT 0 COMMENT '公告状态（0正常 1关闭）',
  `created_dept` bigint(20) NULL DEFAULT NULL COMMENT '创建部门',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '通知公告表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_notice
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_operate_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_operate_log`;
CREATE TABLE `ma_sys_operate_log`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `tenant_id` bigint(20) NULL DEFAULT NULL COMMENT '租户id',
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
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '操作时间',
  `updated_at` bigint(20) NULL DEFAULT NULL,
  `user_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作账号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统操作日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_operate_log
-- ----------------------------
INSERT INTO `ma_sys_operate_log` VALUES (401307411195109376, NULL, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/get-captcha-open-flag', 'app\\admin\\controller\\LoginController', 'getCaptchaOpenFlag', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"flag\":false}}', 1751877971, 1751877971, '');
INSERT INTO `ma_sys_operate_log` VALUES (401307412159799296, NULL, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/captcha', 'app\\admin\\controller\\LoginController', 'captcha', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"uuid\":\"7e72dc0c-ebd3-4250-8142-2d7354c92d9a\",\"base64\":\"data:image\\/png;base64,\\/9j\\/4AAQSkZJRgABAQEAYABgAAD\\/\\/gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2ODApLCBxdWFsaXR5ID0gOTAK\\/9sAQwADAgIDAgIDAwMDBAMDBAUIBQUEBAUKBwcGCAwKDAwLCgsLDQ4SEA0OEQ4LCxAWEBETFBUVFQwPFxgWFBgSFBUU\\/9sAQwEDBAQFBAUJBQUJFA0LDRQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQU\\/8AAEQgAJAB4AwEiAAIRAQMRAf\\/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC\\/\\/EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29\\/j5+v\\/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC\\/\\/EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29\\/j5+v\\/aAAwDAQACEQMRAD8A\\/TOiiirIEBBJGeR1pa+PvgfqWq+Hf2v\\/AIm+Cta1jUbyO9jGo6e1xdu5VAQSF3E4GHAx\\/sivpXTviHos3iHUdAbW9NudV07H2uCG5Xz4AQCGli6ouGHzcj6dKiMrm06bi7LXqdU7rGjMzBVUZLE4AFeMfEr9sL4WfC55YNR8RJqGoR8Gw0pftEv6EKPxYVg3\\/wAYtT+IX7SGvfB2Gz06fwemhk6neqrm6zNDnajh9gBWVP4T3rvfh7+z98Ofg9b\\/AGjQfDtjYTQoWfUZ1DzYAyWMjdMe2KV3L4R8sYfGeID\\/AIKOeFP7W0+1l8F+JbK2vpFSG5vY449+SBkKGOeo719awTLcQRypnbIoYZ9CM18L\\/DXTJf2xP2odW8eahG0ngLwnKLXS4ZB8krKSU4PdjmQ+mVHpX3WAFAAGAOABU023dsqtGMWlFWfUWivC\\/ip+0JqXgr4\\/fD\\/4baXpdteHxDsmurmZ2DwRb2DFQOM7Y3PNe0ahrFhpEJmvr62sogM+ZcSrGuPqTWiadzFxas31LlFZkvifR4LCG9k1WySzmIWK4a4QRyH0Vs4P4VUuvFsMk72ulQtq96pwyQECOP3aQ\\/LwewJPBGM1SV9iHpuampanbaRZvc3TmOFcD5UZ2JJwAFUEk+wGaybe0vtfuIru+NzptnGwaPTQyBpCDkNKy5PUAhVYDru3ZwJtN0OVrtNR1SQXGogEJHHI5t4BjHyIeN2CRvI3YYjIBxW1VXtsTvuFFFFSUFFFU9W1nT9AsZL3U76202zj+\\/cXcyxRr9WYgCgZ8k\\/tKTwfCb9qz4XfESZxbabfK+l6hMeBjBC5Ptuz+Fee\\/BE6ho\\/xN8KfGzUpHhtvH\\/iHU9PnSQ4SOByfs5PsGjZR7ba9N\\/ai+IHwG+Nfhq28Na18UbDT5bW5FxFeaXG18FOCCN0asuDn1q\\/4m0fwF8Zf2dbH4cfDbxxoN7rGlW9r\\/ZIN6sU3nQbcOycyKWwTnb1NcrXvNpnenaCTXk\\/Q579iuyl8f\\/Ez4xfEkStB9v1h7SzkUAqYg7PtIPUBfKH+Fdl+2n8Rda8KfDu18H6NcR3fiTxlONLs4oYykoRiBIfvHj5gue278a6X9mH4fp+zv8EdN0TxXe2Gl6xJPcXt+8t0gjMjSEAhyQDiMRjNfHPxD\\/a50iP9qzVPG9xpD+J9O8ORvpnh+1juljh3KSrXJcBgQzb2BA5BX0ob5IJPqEU6lVuKul\\/SPuz4C\\/Diw+Cfw00PwhDbmCaCPfc3JwVubluZH3D1PAB6AAc4rV+MHw6vPij4Ml0Ow8S33hS5aWOZdR08ZlUqc46jg9+RXwzJ\\/wAFQ\\/Ec8rrL4G0l7RzgxPcyN8vp0wfyr6E+E37bXw78Y+FrK7vtf03wtqrBvtOg6lO6CDDsF8udkVCCoVgoyBu25GKuNSDXKmZzo1Yvna1PmOH4B+IvF\\/7Yk3gPVPiZreq3ujaf57eJAGS5iQxK4jQGRio\\/fbeG7niovhR+znD43\\/aG8feB9bvdS1u00bIU3movFvyRiSRlyzEA5CgDJ4JXrVvwn+0R4U8K\\/tN\\/F3x7qWtSw2lzDLaab\\/ZyCee9USJtSFwGjXcIV+djgKTg5II8\\/wBG\\/a+n8FfHPxB8QfDHhmGCHVYPs503ULt5sdPnZwASxx3z16msPcVm+52WqyvFaaL7z0v9nv8AZ40zx78VfHvwy8b6jqOpaP4SaRNPtorl4kRmkKtKoyeyrwSRzzmvZv2GNa1Dwl4n+JHwm1G8lvIfC2oN9geY5PlF2Ugegyob\\/gdfN\\/wV\\/axutO\\/aR8SeP7jwheaiuvWLR3Wk6FmV4ivlkzKCOQNjEgkff68VueEP2l4fBP7Wvizx1J4K8RxaZ4ksAbXRvswF7IjCJo59hIBVwjN8pIw\\/BbGSRlFWa7k1ITlzRfZfefplRXm3wO+MF18ZtB1DVp\\/COr+EYbe58iCLWIyklwuwN5igqPlyccE8g816TXandXR5bTi7MKKKKZIV+ds\\/wI0P9oL9rn4h6b4m1HV4ra21FxGLG4RSFCKdvzo+B9MUUVz1uh2YbRyt2PrbwT+yb8JvAuiDTbXwPo+pruLtda1Zx31w5IAOZJVJA4HyjC9eOTWX40\\/Yr+DnjZXafwZaaTcMMLPorNZlPcJGRGfxU0UVpyxtsYqpPmvdnk2o\\/wDBPPwDqWv22l3fijxnc2cKEwpNqMD+UCMkLmAgDgdPSvX\\/AIafsdfCn4Vaha6lpPhv7Vq9scx6jqVw9xIDjG4KTsU8nlVFFFZU4q+x01pyStc9b1jQdM8Q2bWmq6daanaMMGC8gWVCP91gRXjvin9ij4MeLLl7i48EWtjO38WlzS2aD6Rxsqf+O0UVu0nujjjKUXo7Hw5+1P8As5eEfhb8Rm03w99vtrCS1inEElwJAjElSAWXdj5c8k8k9uK+qf2Y\\/wBkbwb4UXwv4\\/tdQ1ubW4IWZI5riLyPmBUgqsQJGD69hRRXFBLnPUqSfslqdR8Rfhzpdl+1R8OvHUElxDrEscunSxIyiCSPyZxuZdu4th+u7Hyrxxz5r8aD\\/ZP\\/AAUR+FF1bfJLdaPsl9GB+2Jz+GPyFFFby\\/U54br\\/AAv9T7NoooroOAKKKKAP\\/9k=\"}}', 1751877971, 1751877971, '');
INSERT INTO `ma_sys_operate_log` VALUES (401307412394680320, NULL, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/platform/account-sets', 'app\\admin\\controller\\platform\\TenantController', 'accountSets', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"list\":[{\"id\":\"1\",\"tenant_id\":\"1\",\"name\":\"xxxx \\u6709\\u9650\\u516c\\u53f8\"}],\"tenant_enabled\":true}}', 1751877971, 1751877971, '');
INSERT INTO `ma_sys_operate_log` VALUES (401307416068890624, NULL, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/get-captcha-open-flag', 'app\\admin\\controller\\LoginController', 'getCaptchaOpenFlag', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"flag\":false}}', 1751877971, 1751877971, '');
INSERT INTO `ma_sys_operate_log` VALUES (401307416731590656, NULL, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/captcha', 'app\\admin\\controller\\LoginController', 'captcha', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"uuid\":\"bab29a95-be2e-4f72-8c79-d937af6518e1\",\"base64\":\"data:image\\/png;base64,\\/9j\\/4AAQSkZJRgABAQEAYABgAAD\\/\\/gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2ODApLCBxdWFsaXR5ID0gOTAK\\/9sAQwADAgIDAgIDAwMDBAMDBAUIBQUEBAUKBwcGCAwKDAwLCgsLDQ4SEA0OEQ4LCxAWEBETFBUVFQwPFxgWFBgSFBUU\\/9sAQwEDBAQFBAUJBQUJFA0LDRQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQU\\/8AAEQgAJAB4AwEiAAIRAQMRAf\\/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC\\/\\/EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29\\/j5+v\\/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC\\/\\/EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29\\/j5+v\\/aAAwDAQACEQMRAD8A\\/TOiivGviT+0NcfCvVlttc8I3a2kxP2a8gu4nEwHfb1X6E1hiMTSwkPaVnZd7N\\/lsRex7LRXkU37T3g630Kxv2kuJbm6hE32C3j82WPI6Pt4B\\/GqPgr9q7wn4x8QQaSILzT5rhtkMlwoKMfQkHg1y\\/2nguaMPaq721FdHPftZReKtD8OjWtP8U31pp7XCW7adaHyVCkH5i6kM3OBg8c16B+zbfvqXwU8NTyyvNKUmV3kYsxInkHJP0rK\\/avs\\/tXwW1YgZaKWBx+Ei5\\/TNeT\\/ALPuh+OPiD8Pf7P0zxQ3hTQ9PmeKOe0h3zTSMd7ZORgDcOhHWvCnUlhc5ahFy5oaK\\/W\\/m7JaC6n1zUF7cNZ2c86wvcNGhcQxD5nIGdo9zXy94Q+Kfjb4YfGCHwP4t1P+3tPuJVijuZEAcB\\/uSK3XHqDnocV9Syv5UTvtZ9oJ2qMk+w96+gweNhjYy5E4yi7NPdMadz5+1D9sLTdP1640l\\/Cmr\\/a7eVoJYSUMiupIYYUnoRXY+Fv2kvBXiS7jspr2XRNQfgW+pxGHn03H5f1rwj4DXQ1T9pDxPrX2eaVBNfXG2NQzIJJWGTz6N2zX0T45+H3g\\/wCLml3On3UNq2oCMtFcxoEuYD2bs2M4yDwa8XAVcyxVGWIhNSSbSTW6XmtvuJTPQFYOoZSGUjII6Gs\\/xDrtp4Y0O+1a+fy7SziaaRh1wB2r5g\\/Zn8e614R+IV98NtauGubWN5YrYSHJgljzlVP91gDx2OMdTXqP7QWopqa+GvBguEgOvXwN0zsFCWsRDSEntk7R78130szjXwcsTBWktLPpLZL72ir6C\\/C79ozTfid4xu9Ai0u50yaOEyxNcsMyYIyMdjg5\\/A169XyN8SpLD4a\\/tG+GfE2lXEEml6gI0m+zSKwUjEUgODx8pQ\\/ia+uFYOoYHIIyCO9GWYmrW9rRryvOErX7p6pgmLRRRXuDCvkP9tDxDd3eqWGkS6JcW9pasJIdWbd5VwWT5kX5cZUnnDH6Cvryvlb9tHxPp2pWekaJaXSXOo2dy73NvHktCCild3pkHNfOcQf8i+fvW2+eu366diZbGP8ABr9nfW\\/F\\/hCzu73xEul6Der5ot9PT9\\/KOo3uQMEHjHPHeuP+JvgfS\\/hv8edF0rRY5ILET2cqrJIXIzIAeT9D+dfTX7M3iCw1X4SaHaQXkEt9aROlxbJKDJF+8bG5c5GRjrXiX7RfhfX9a+PNpcaHo17qj29vbSE2sDOqlWLfMwGB26185i8DQp5bRrUY3k3HXd7apdvRCa0Pef2iYhdfBfxGx6LbeZ+orif2LroSfDS\\/tweYtQdyP95V\\/wDia7vxj4Y174p+BI9KkK+FYr1WS9gnVbibYCNoUqdoJxk+mcetZ3wi+Bs3whuJlsfEM15YXB3T2s0CgMwGAQRyK+inRrTzOnioQfIo2b0W+uz1\\/Arrc8a\\/aXH2D4\\/+FLwcF1t1z7CX\\/wCyr6y1C\\/j0\\/Tbm9kI8qCFpmP8AsqCT\\/KvI\\/jV8Abj4r+J9J1a31lNKNjF5fMJkYtv3AjkV0vivwx4svvADeHLK\\/s768vYJbW71S6Uw+WjAgFI1zk4OOvbPOaMNSr4XEYqo4O0mnHbV2+8Fuzwr9iSya51zxRqknzOYkiLH1Ztx\\/lX1VqlrZ3NqzXyoYYsyb3ONmB94Htxnmvm7wt+yn4u8Gh5tF+IH9lXL4LpbW77HI9TuH8qteIvhD8ZfE9s2lX\\/jWzl0pxtkZMozj3wuT9CQK5MtqYvLsJGjLDyclfZx3b9dCVtaxwPwhY+Of2pb3V7JS1lHc3N2XHOEwVUk+5IH413uneENM\\/aL+L3i7VNY8+bQNEWPTLJYJTGHYFix3Dtncf8AgYrr\\/CfwRu\\/hV4GvLDwm9pd+I9RHl3Wp6g7RhFwfuBVP3c8DjPUntWr8CvhZq\\/wp0W606\\/1W11CCeVrkrBblHErYDFnLHdwoHQVjhMvrLko4iF1KTnPtdrRefd2vqNI8d\\/aH\\/Z28N+B\\/h++veHLe4t7m0nj84PMXBjY7eh7glTn617z8E\\/F48b\\/DHQtTLh5\\/IEM\\/tInyt\\/KrPxT8F3\\/j\\/wAJXWi2WpRaatypSYy24lEikdOoKkHByPSuD+Avw58X\\/B+a60HUIrXVdCu5jOl\\/a3GPszbOdyMASG2qPlzgn05rup4Z4LMeahTtSnGzstE+jsvu2C1me2UUUV9OUFVrzTbPUV23drBdL6TRhx+ooopNJ6MCHTtB03SJJHsdPtrJ5Bhzbwqm764FXgoBJwMnqaKKSioqyQC0UUVQBRRRQAUUUUAFFFFABRRRQAUUUUAf\\/9k=\"}}', 1751877972, 1751877972, '');
INSERT INTO `ma_sys_operate_log` VALUES (401307416907751424, NULL, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/platform/account-sets', 'app\\admin\\controller\\platform\\TenantController', 'accountSets', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"list\":[{\"id\":\"1\",\"tenant_id\":\"1\",\"name\":\"xxxx \\u6709\\u9650\\u516c\\u53f8\"}],\"tenant_enabled\":true}}', 1751877972, 1751877972, '');
INSERT INTO `ma_sys_operate_log` VALUES (401307464328552448, NULL, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/login', 'app\\admin\\controller\\LoginController', 'login', 'POST', '{\"tenant_id\":\"1\",\"user_name\":\"admin\",\"password\":\"******\"}', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"token_type\":\"Bearer\",\"expires_in\":7200,\"access_token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJtYWRvbmcudGVjaCIsImF1ZCI6Im1hZG9uZy50ZWNoIiwiaWF0IjoxNzUxODc3OTc3LCJuYmYiOjE3NTE4Nzc5NzcsImV4cCI6MTc1MTg4NTE3NywiZXh0ZW5kIjp7ImlkIjoiMSIsInVzZXJfbmFtZSI6ImFkbWluIiwicmVhbF9uYW1lIjoiXHU4ZDg1XHU3ZWE3XHU3YmExXHU3NDA2XHU1NDU4Iiwibmlja19uYW1lIjoiXHU4ZDg1XHU3ZWE3XHU3YmExXHU3NDA2XHU1NDU4IiwiaXNfc3VwZXIiOjEsIm1vYmlsZV9waG9uZSI6bnVsbCwiZW1haWwiOm51bGwsImF2YXRhciI6bnVsbCwic2lnbmVkIjpudWxsLCJkYXNoYm9hcmQiOm51bGwsImRlcHRfaWQiOm51bGwsImVuYWJsZWQiOjEsImxvZ2luX2lwIjoiMTI3LjAuMC4xIiwibG9naW5fdGltZSI6MTc1MTg3Nzk3NywidXBkYXRlZF9hdCI6IjIwMjUtMDctMDdUMDg6NDY6MTcuMDAwMDAwWiIsInNleCI6MCwiYmlydGhkYXkiOm51bGwsInRlbCI6bnVsbCwiaXNfbG9ja2VkIjowLCJkZXB0cyI6W10sInBvc3RzIjpbXSwidGVuYW50cyI6W3siaWQiOiIxIiwiZGJfbmFtZSI6Im1hZG9uZ19kYiIsImNvZGUiOiJwbGF0Zm9ybSIsInR5cGUiOjAsImNvbnRhY3RfcGVyc29uIjoiYWRtaW4iLCJjb250YWN0X3Bob25lIjoiMTg4ODg4ODg4ODgiLCJjb21wYW55X25hbWUiOiJ4eHh4IFx1NjcwOVx1OTY1MFx1NTE2Y1x1NTNmOCIsImxpY2Vuc2VfbnVtYmVyIjoiIiwiYWRkcmVzcyI6Ilx1NGUyZFx1NTZmZCIsImRlc2NyaXB0aW9uIjoiXHU1MTg1XHU3ZjZlXHU4ZDI2XHU1M2Y3IiwiZG9tYWluIjoiaHR0cHM6Ly93d3cubWFkb25nLnRlY2giLCJlbmFibGVkIjoxLCJpc29sYXRpb25fbW9kZSI6MiwiaXNfZGVmYXVsdCI6MSwiZXhwaXJlZF9hdCI6bnVsbCwiZGVsZXRlZF9hdCI6bnVsbCwiY3JlYXRlZF9hdCI6IjIwMjUtMDctMDdUMDg6NDU6NDYuMDAwMDAwWiIsImNyZWF0ZWRfYnkiOjEsInVwZGF0ZWRfYnkiOm51bGwsInVwZGF0ZWRfYXQiOm51bGwsImNyZWF0ZWRfZGF0ZSI6IjIwMjUtMDctMDcgMTY6NDU6NDYiLCJ1cGRhdGVkX2RhdGUiOm51bGwsInBpdm90Ijp7ImFkbWluX2lkIjoxLCJ0ZW5hbnRfaWQiOjEsImNyZWF0ZWRfZGF0ZSI6bnVsbCwidXBkYXRlZF9kYXRlIjpudWxsfX1dLCJjYXNiaW4iOltdfX0.PwvmPVVWbKcE0Kxb6vNQ9o8FTfsIoJlB1S6owZ9BNzE\",\"refresh_token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJtYWRvbmcudGVjaCIsImF1ZCI6Im1hZG9uZy50ZWNoIiwiaWF0IjoxNzUxODc3OTc3LCJuYmYiOjE3NTE4Nzc5NzcsImV4cCI6MTc1MjQ4Mjc3NywiZXh0ZW5kIjp7ImlkIjoiMSIsInVzZXJfbmFtZSI6ImFkbWluIiwicmVhbF9uYW1lIjoiXHU4ZDg1XHU3ZWE3XHU3YmExXHU3NDA2XHU1NDU4Iiwibmlja19uYW1lIjoiXHU4ZDg1XHU3ZWE3XHU3YmExXHU3NDA2XHU1NDU4IiwiaXNfc3VwZXIiOjEsIm1vYmlsZV9waG9uZSI6bnVsbCwiZW1haWwiOm51bGwsImF2YXRhciI6bnVsbCwic2lnbmVkIjpudWxsLCJkYXNoYm9hcmQiOm51bGwsImRlcHRfaWQiOm51bGwsImVuYWJsZWQiOjEsImxvZ2luX2lwIjoiMTI3LjAuMC4xIiwibG9naW5fdGltZSI6MTc1MTg3Nzk3NywidXBkYXRlZF9hdCI6IjIwMjUtMDctMDdUMDg6NDY6MTcuMDAwMDAwWiIsInNleCI6MCwiYmlydGhkYXkiOm51bGwsInRlbCI6bnVsbCwiaXNfbG9ja2VkIjowLCJkZXB0cyI6W10sInBvc3RzIjpbXSwidGVuYW50cyI6W3siaWQiOiIxIiwiZGJfbmFtZSI6Im1hZG9uZ19kYiIsImNvZGUiOiJwbGF0Zm9ybSIsInR5cGUiOjAsImNvbnRhY3RfcGVyc29uIjoiYWRtaW4iLCJjb250YWN0X3Bob25lIjoiMTg4ODg4ODg4ODgiLCJjb21wYW55X25hbWUiOiJ4eHh4IFx1NjcwOVx1OTY1MFx1NTE2Y1x1NTNmOCIsImxpY2Vuc2VfbnVtYmVyIjoiIiwiYWRkcmVzcyI6Ilx1NGUyZFx1NTZmZCIsImRlc2NyaXB0aW9uIjoiXHU1MTg1XHU3ZjZlXHU4ZDI2XHU1M2Y3IiwiZG9tYWluIjoiaHR0cHM6Ly93d3cubWFkb25nLnRlY2giLCJlbmFibGVkIjoxLCJpc29sYXRpb25fbW9kZSI6MiwiaXNfZGVmYXVsdCI6MSwiZXhwaXJlZF9hdCI6bnVsbCwiZGVsZXRlZF9hdCI6bnVsbCwiY3JlYXRlZF9hdCI6IjIwMjUtMDctMDdUMDg6NDU6NDYuMDAwMDAwWiIsImNyZWF0ZWRfYnkiOjEsInVwZGF0ZWRfYnkiOm51bGwsInVwZGF0ZWRfYXQiOm51bGwsImNyZWF0ZWRfZGF0ZSI6IjIwMjUtMDctMDcgMTY6NDU6NDYiLCJ1cGRhdGVkX2RhdGUiOm51bGwsInBpdm90Ijp7ImFkbWluX2lkIjoxLCJ0ZW5hbnRfaWQiOjEsImNyZWF0ZWRfZGF0ZSI6bnVsbCwidXBkYXRlZF9kYXRlIjpudWxsfX1dLCJjYXNiaW4iOltdfX0.a8iupRMZsKWlWRC9DS_lyZTPcaxKYGknYvjB9Gnjb2o\",\"client_id\":\"127.0.0.1-686b8959a66197.54288079\",\"expires_time\":1751885177}}', 1751877977, 1751877977, '');
INSERT INTO `ma_sys_operate_log` VALUES (401307464680873984, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/auth/user-info', 'app\\admin\\controller\\system\\SysAuthController', 'getUserInfo', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"id\":\"1\",\"user_name\":\"admin\",\"real_name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"nick_name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"is_super\":1,\"mobile_phone\":null,\"email\":null,\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":\"\",\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1751877977,\"created_by\":null,\"updated_by\":null,\"created_at\":null,\"updated_at\":\"2025-07-07T08:46:17.000000Z\",\"deleted_at\":null,\"sex\":0,\"remark\":null,\"birthday\":null,\"tel\":null,\"is_locked\":0,\"role_id_list\":[],\"post_id_list\":[],\"created_date\":null,\"updated_date\":\"2025-07-07 16:46:17\",\"depts\":[],\"posts\":[],\"casbin\":[],\"tenant\":{\"id\":\"401307202268438528\",\"admin_id\":1,\"tenant_id\":1,\"is_super\":1,\"is_default\":1,\"priority\":-1,\"created_at\":0,\"updated_at\":0,\"created_date\":null,\"updated_date\":null},\"managed_tenants\":[{\"id\":\"1\",\"db_name\":\"madong_db\",\"code\":\"platform\",\"type\":0,\"contact_person\":\"admin\",\"contact_phone\":\"18888888888\",\"company_name\":\"xxxx \\u6709\\u9650\\u516c\\u53f8\",\"license_number\":\"\",\"address\":\"\\u4e2d\\u56fd\",\"description\":\"\\u5185\\u7f6e\\u8d26\\u53f7\",\"domain\":\"https:\\/\\/www.madong.tech\",\"enabled\":1,\"isolation_mode\":2,\"is_default\":1,\"expired_at\":null,\"deleted_at\":null,\"created_at\":\"2025-07-07T08:45:46.000000Z\",\"created_by\":1,\"updated_by\":null,\"updated_at\":null,\"created_date\":\"2025-07-07 16:45:46\",\"updated_date\":null,\"pivot\":{\"admin_id\":1,\"tenant_id\":1,\"created_date\":null,\"updated_date\":null}}]}}', 1751877977, 1751877977, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307465041584128, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/auth/perm-code', 'app\\admin\\controller\\system\\SysAuthController', 'getUserCodes', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":[\"admin\"]}', 1751877977, 1751877977, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307465570066432, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/auth/user-menus', 'app\\admin\\controller\\system\\SysAuthController', 'getPermissionsMenu', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":[{\"id\":\"401238699771437056\",\"pid\":0,\"app\":\"admin\",\"title\":\"\\u9996\\u9875\",\"code\":\"Dashboard\",\"level\":null,\"type\":1,\"sort\":-1,\"path\":\"\\/\",\"component\":\"BasicLayout\",\"redirect\":\"\\/workspace\",\"icon\":\"ant-design:home-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"Dashboard\",\"meta\":{\"icon\":\"ant-design:home-outlined\",\"title\":\"\\u9996\\u9875\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null,\"children\":[{\"id\":\"401238699788214272\",\"pid\":\"401238699771437056\",\"app\":\"admin\",\"title\":\"\\u5de5\\u4f5c\\u53f0\",\"code\":\"Workspace\",\"level\":null,\"type\":1,\"sort\":-1,\"path\":\"\\/workspace\",\"component\":\"\\/dashboard\\/workspace\\/index\",\"redirect\":null,\"icon\":\"ant-design:home-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"Workspace\",\"meta\":{\"icon\":\"ant-design:home-outlined\",\"title\":\"\\u5de5\\u4f5c\\u53f0\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699779825664\",\"pid\":\"401238699771437056\",\"app\":\"admin\",\"title\":\"\\u5206\\u6790\\u9875\",\"code\":\"Analytics\",\"level\":null,\"type\":1,\"sort\":-1,\"path\":\"\\/analytics\",\"component\":\"\\/dashboard\\/analytics\\/index\",\"redirect\":null,\"icon\":\"ant-design:home-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"Analytics\",\"meta\":{\"icon\":\"ant-design:home-outlined\",\"title\":\"\\u5206\\u6790\\u9875\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null}]},{\"id\":\"401238699888877568\",\"pid\":0,\"app\":\"admin\",\"title\":\"\\u4eba\\u5de5\\u667a\\u80fd\",\"code\":\"ai\",\"level\":null,\"type\":1,\"sort\":999,\"path\":\"\\/ai\",\"component\":null,\"redirect\":null,\"icon\":\"carbon:ai-recommend\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"ai\",\"meta\":{\"icon\":\"carbon:ai-recommend\",\"title\":\"\\u4eba\\u5de5\\u667a\\u80fd\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null,\"children\":[{\"id\":\"401238699888877569\",\"pid\":\"401238699888877568\",\"app\":\"admin\",\"title\":\"\\u667a\\u80fd\\u95ee\\u7b54\",\"code\":\"ai:chat\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/ai\\/agent\",\"component\":\"\\/ai\\/agent\\/index\",\"redirect\":null,\"icon\":\"mingcute:kakao-talk-line\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"ai:chat\",\"meta\":{\"icon\":\"mingcute:kakao-talk-line\",\"title\":\"\\u667a\\u80fd\\u95ee\\u7b54\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null}]},{\"id\":\"401238699788214273\",\"pid\":0,\"app\":\"admin\",\"title\":\"\\u7cfb\\u7edf\\u76d1\\u63a7\",\"code\":\"monitors\",\"level\":null,\"type\":1,\"sort\":999,\"path\":\"\\/monitors\",\"component\":\"BasicLayout\",\"redirect\":null,\"icon\":\"ant-design:video-camera-filled\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"monitors\",\"meta\":{\"icon\":\"ant-design:video-camera-filled\",\"title\":\"\\u7cfb\\u7edf\\u76d1\\u63a7\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null,\"children\":[{\"id\":\"401238699796602880\",\"pid\":\"401238699788214273\",\"app\":\"admin\",\"title\":\"\\u6027\\u80fd\\u76d1\\u63a7\",\"code\":\"monitors:monitor:server\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/monitors\\/server\",\"component\":\"\\/monitor\\/server\\/index\",\"redirect\":null,\"icon\":\"ant-design:line-chart-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"monitors:monitor:server\",\"meta\":{\"icon\":\"ant-design:line-chart-outlined\",\"title\":\"\\u6027\\u80fd\\u76d1\\u63a7\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699796602881\",\"pid\":\"401238699788214273\",\"app\":\"admin\",\"title\":\"\\u64cd\\u4f5c\\u65e5\\u5fd7\",\"code\":\"system:logs_operate\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/monitor\\/logs\\/operate\",\"component\":\"\\/system\\/logs\\/operate\\/index\",\"redirect\":null,\"icon\":\"ant-design:schedule-filled\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:logs_operate\",\"meta\":{\"icon\":\"ant-design:schedule-filled\",\"title\":\"\\u64cd\\u4f5c\\u65e5\\u5fd7\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699788214274\",\"pid\":\"401238699788214273\",\"app\":\"admin\",\"title\":\"Redis\\u76d1\\u63a7\",\"code\":\"monitors:redis\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/monitors\\/redis\",\"component\":\"\\/monitor\\/redis\\/index\",\"redirect\":null,\"icon\":\"ant-design:trademark-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"monitors:redis\",\"meta\":{\"icon\":\"ant-design:trademark-outlined\",\"title\":\"Redis\\u76d1\\u63a7\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699796602884\",\"pid\":\"401238699788214273\",\"app\":\"admin\",\"title\":\"\\u767b\\u5f55\\u65e5\\u5fd7\",\"code\":\"system:logs:login\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/monitor\\/logs\\/login\",\"component\":\"\\/system\\/logs\\/login\\/index\",\"redirect\":null,\"icon\":\"ant-design:credit-card-filled\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:logs:login\",\"meta\":{\"icon\":\"ant-design:credit-card-filled\",\"title\":\"\\u767b\\u5f55\\u65e5\\u5fd7\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null}]},{\"id\":\"401238699804991489\",\"pid\":0,\"app\":\"admin\",\"title\":\"\\u5e73\\u53f0\\u7ba1\\u7406\",\"code\":\"platform\",\"level\":null,\"type\":1,\"sort\":999,\"path\":\"\\/platform\",\"component\":null,\"redirect\":null,\"icon\":\"clarity-thin-client-line\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"platform\",\"meta\":{\"icon\":\"clarity-thin-client-line\",\"title\":\"\\u5e73\\u53f0\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null,\"children\":[{\"id\":\"401238699804991490\",\"pid\":\"401238699804991489\",\"app\":\"admin\",\"title\":\"\\u8d26\\u5957\\u7ba1\\u7406\",\"code\":\"platform:tenant\",\"level\":null,\"type\":1,\"sort\":999,\"path\":\"\\/platform\\/tenant\",\"component\":\"\",\"redirect\":null,\"icon\":\"ant-design:code-sandbox-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"platform:tenant\",\"meta\":{\"icon\":\"ant-design:code-sandbox-outlined\",\"title\":\"\\u8d26\\u5957\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null,\"children\":[{\"id\":\"401238699830157314\",\"pid\":\"401238699804991490\",\"app\":\"admin\",\"title\":\"\\u6210\\u5458\\u7ba1\\u7406\",\"code\":\"platform:tenant:member\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/platform\\/tenant-member\",\"component\":\"\\/platform\\/tenant-member\\/index\",\"redirect\":null,\"icon\":\"ant-design:user-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"platform:tenant:member\",\"meta\":{\"icon\":\"ant-design:user-outlined\",\"title\":\"\\u6210\\u5458\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699804991491\",\"pid\":\"401238699804991490\",\"app\":\"admin\",\"title\":\"\\u8d26\\u5957\\u4e2d\\u5fc3\",\"code\":\"platform:tenant:list\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/platform\\/tenant\\/list\",\"component\":\"\\/platform\\/tenant\\/index\",\"redirect\":null,\"icon\":\"ant-design:code-sandbox-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"platform:tenant:list\",\"meta\":{\"icon\":\"ant-design:code-sandbox-outlined\",\"title\":\"\\u8d26\\u5957\\u4e2d\\u5fc3\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699821768707\",\"pid\":\"401238699804991490\",\"app\":\"admin\",\"title\":\"\\u5957\\u9910\\u8ba2\\u9605\",\"code\":\"platform:tenant:subscription\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/platform\\/tenant_subscription\",\"component\":\"\\/platform\\/tenant-subscription\\/index\",\"redirect\":null,\"icon\":\"ant-design:carry-out-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"platform:tenant:subscription\",\"meta\":{\"icon\":\"ant-design:carry-out-outlined\",\"title\":\"\\u5957\\u9910\\u8ba2\\u9605\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699813380099\",\"pid\":\"401238699804991490\",\"app\":\"admin\",\"title\":\"\\u591a\\u6570\\u636e\\u6e90\",\"code\":\"platform:db\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/platform\\/db\",\"component\":\"\\/platform\\/db\\/index\",\"redirect\":null,\"icon\":\"ant-design:database-filled\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"platform:db\",\"meta\":{\"icon\":\"ant-design:database-filled\",\"title\":\"\\u591a\\u6570\\u636e\\u6e90\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null}]}]},{\"id\":\"401238699846934531\",\"pid\":0,\"app\":\"admin\",\"title\":\"\\u5f00\\u53d1\\u5e73\\u53f0\",\"code\":\"dev\",\"level\":null,\"type\":1,\"sort\":999,\"path\":\"\\/dev\",\"component\":null,\"redirect\":null,\"icon\":\"ant-design:appstore-add-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"dev\",\"meta\":{\"icon\":\"ant-design:appstore-add-outlined\",\"title\":\"\\u5f00\\u53d1\\u5e73\\u53f0\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null,\"children\":[{\"id\":\"401238699855323136\",\"pid\":\"401238699846934531\",\"app\":\"admin\",\"title\":\"\\u5b9a\\u65f6\\u4efb\\u52a1\",\"code\":\"dev:crontab\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/dev\\/crontab\",\"component\":\"\\/dev\\/crontab\\/index\",\"redirect\":null,\"icon\":\"arcticons:jobstreet\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"dev:crontab\",\"meta\":{\"icon\":\"arcticons:jobstreet\",\"title\":\"\\u5b9a\\u65f6\\u4efb\\u52a1\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699863711747\",\"pid\":\"401238699846934531\",\"app\":\"admin\",\"title\":\"\\u7f51\\u5173\\u7ba1\\u7406\",\"code\":\"dev:gateway\",\"level\":null,\"type\":1,\"sort\":999,\"path\":\"\\/dev\\/gateway\",\"component\":null,\"redirect\":null,\"icon\":\"ant-design:gateway-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"dev:gateway\",\"meta\":{\"icon\":\"ant-design:gateway-outlined\",\"title\":\"\\u7f51\\u5173\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null,\"children\":[{\"id\":\"401238699880488960\",\"pid\":\"401238699863711747\",\"app\":\"admin\",\"title\":\"\\u9650\\u8bbf\\u540d\\u5355\",\"code\":\"dev:gateway:blacklist\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/dev\\/gateway\\/blacklist\",\"component\":\"\\/dev\\/gateway\\/blacklist\\/index\",\"redirect\":null,\"icon\":\"carbon-ai-status-rejected\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"dev:gateway:blacklist\",\"meta\":{\"icon\":\"carbon-ai-status-rejected\",\"title\":\"\\u9650\\u8bbf\\u540d\\u5355\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699863711748\",\"pid\":\"401238699863711747\",\"app\":\"admin\",\"title\":\"\\u9650\\u6d41\\u89c4\\u5219\",\"code\":\"dev:gateway:limit\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/dev\\/gateway\\/limiter\",\"component\":\"\\/dev\\/gateway\\/limiter\\/index\",\"redirect\":null,\"icon\":\"carbon-rule\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"dev:gateway:limit\",\"meta\":{\"icon\":\"carbon-rule\",\"title\":\"\\u9650\\u6d41\\u89c4\\u5219\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null}]}]},{\"id\":\"401238699888877570\",\"pid\":0,\"app\":\"admin\",\"title\":\"\\u7cfb\\u7edf\\u8bbe\\u7f6e\",\"code\":\"system\",\"level\":null,\"type\":1,\"sort\":1000,\"path\":\"\\/system\",\"component\":\"BasicLayout\",\"redirect\":\"\",\"icon\":\"ant-design:setting-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":1,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system\",\"meta\":{\"icon\":\"ant-design:setting-outlined\",\"title\":\"\\u7cfb\\u7edf\\u8bbe\\u7f6e\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null,\"children\":[{\"id\":\"401238699897266178\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u901a\\u77e5\\u516c\\u544a\",\"code\":\"system:notice\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/system\\/notice\",\"component\":\"\\/system\\/notice\\/index\",\"redirect\":null,\"icon\":\"ant-design:comment-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:notice\",\"meta\":{\"icon\":\"ant-design:comment-outlined\",\"title\":\"\\u901a\\u77e5\\u516c\\u544a\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699888877571\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u9644\\u4ef6\\u7ba1\\u7406\",\"code\":\"system:files\",\"level\":null,\"type\":2,\"sort\":999,\"path\":\"\\/system\\/files\",\"component\":\"\\/system\\/files\\/index\",\"redirect\":null,\"icon\":\"ant-design:folder-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:files\",\"meta\":{\"icon\":\"ant-design:folder-outlined\",\"title\":\"\\u9644\\u4ef6\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699947597825\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u89d2\\u8272\\u7ba1\\u7406\",\"code\":\"system:role\",\"level\":null,\"type\":2,\"sort\":1000,\"path\":\"\\/system\\/role\",\"component\":\"\\/system\\/role\\/index\",\"redirect\":null,\"icon\":\"ant-design:team-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:role\",\"meta\":{\"icon\":\"ant-design:team-outlined\",\"title\":\"\\u89d2\\u8272\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699922432002\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u804c\\u4f4d\\u7ba1\\u7406\",\"code\":\"system:post\",\"level\":null,\"type\":2,\"sort\":1000,\"path\":\"\\/system\\/post\",\"component\":\"\\/system\\/post\\/index\",\"redirect\":null,\"icon\":\"ant-design:database-filled\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:post\",\"meta\":{\"icon\":\"ant-design:database-filled\",\"title\":\"\\u804c\\u4f4d\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699930820610\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"code\":\"system:user\",\"level\":null,\"type\":2,\"sort\":1000,\"path\":\"\\/system\\/user\",\"component\":\"\\/system\\/user\\/index\",\"redirect\":null,\"icon\":\"ant-design:user-add-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:user\",\"meta\":{\"icon\":\"ant-design:user-add-outlined\",\"title\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699905654787\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u83dc\\u5355\\u7ba1\\u7406\",\"code\":\"system:menu\",\"level\":null,\"type\":2,\"sort\":1000,\"path\":\"\\/system\\/menu\",\"component\":\"\\/system\\/menu\\/index\",\"redirect\":null,\"icon\":\"ant-design:menu-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:menu\",\"meta\":{\"icon\":\"ant-design:menu-outlined\",\"title\":\"\\u83dc\\u5355\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699955986435\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u6570\\u636e\\u5b57\\u5178\",\"code\":\"system:dict\",\"level\":null,\"type\":2,\"sort\":1000,\"path\":\"\\/system\\/dict\",\"component\":\"\\/system\\/dict\\/index\",\"redirect\":null,\"icon\":\"ant-design:profile-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:dict\",\"meta\":{\"icon\":\"ant-design:profile-outlined\",\"title\":\"\\u6570\\u636e\\u5b57\\u5178\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699914043395\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u90e8\\u95e8\\u7ba1\\u7406\",\"code\":\"system:dept\",\"level\":null,\"type\":2,\"sort\":1000,\"path\":\"\\/system\\/dept\",\"component\":\"\\/system\\/dept\\/index\",\"redirect\":null,\"icon\":\"ant-design:facebook-filled\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:dept\",\"meta\":{\"icon\":\"ant-design:facebook-filled\",\"title\":\"\\u90e8\\u95e8\\u7ba1\\u7406\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699972763652\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u7cfb\\u7edf\\u53c2\\u6570\",\"code\":\"system:config\",\"level\":null,\"type\":2,\"sort\":999999,\"path\":\"\\/systen\\/config\",\"component\":\"\\/system\\/config\\/index\",\"redirect\":null,\"icon\":\"ant-design:tool-outlined\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:config\",\"meta\":{\"icon\":\"ant-design:tool-outlined\",\"title\":\"\\u7cfb\\u7edf\\u53c2\\u6570\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null},{\"id\":\"401238699981152256\",\"pid\":\"401238699888877570\",\"app\":\"admin\",\"title\":\"\\u6570\\u636e\\u56de\\u6536\",\"code\":\"system:recycle_bin\",\"level\":null,\"type\":2,\"sort\":999999999,\"path\":\"\\/system\\/recycle-bin\",\"component\":\"\\/system\\/recycle-bin\\/index\",\"redirect\":null,\"icon\":\"ant-design:rest-filled\",\"is_show\":1,\"is_link\":0,\"link_url\":null,\"enabled\":1,\"open_type\":0,\"is_cache\":0,\"is_sync\":1,\"is_affix\":0,\"is_global\":0,\"variable\":null,\"created_at\":null,\"created_by\":null,\"updated_at\":null,\"updated_by\":null,\"deleted_at\":null,\"methods\":\"get\",\"is_frame\":null,\"name\":\"system:recycle_bin\",\"meta\":{\"icon\":\"ant-design:rest-filled\",\"title\":\"\\u6570\\u636e\\u56de\\u6536\",\"menuVisibleWithForbidden\":true,\"keepAlive\":true},\"created_date\":null,\"updated_date\":null}]}]}', 1751877977, 1751877977, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307465830113280, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/auth/perm-code', 'app\\admin\\controller\\system\\SysAuthController', 'getUserCodes', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":[\"admin\"]}', 1751877977, 1751877977, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307465972719616, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/config/info', 'app\\admin\\controller\\system\\SysConfigController', 'getConfigInfo', 'GET', '{\"group_code\":\"site_setting\"}', '{\"code\":0,\"msg\":\"\\u64cd\\u4f5c\\u6210\\u529f\",\"data\":{\"site_open\":\"1\",\"site_url\":\"http:\\/\\/127.0.0.1:8998\",\"site_name\":\"madong-admin\",\"site_logo\":\"https:\\/\\/madong.tech\\/assets\\/images\\/logo.svg\",\"site_network_security\":\"2024042441\\u53f7-2\",\"site_description\":\"\\u5feb\\u901f\\u5f00\\u53d1\\u6846\\u67b6\",\"site_record_no\":\"2024042442\",\"site_icp_url\":\"https:\\/\\/beian.miit.gov.cn\\/\",\"site_network_security_url\":\"\"}}', 1751877977, 1751877977, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307468866789376, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/dict/get-by-dict-type', 'app\\admin\\controller\\system\\SysDictController', 'getByDictType', 'GET', '{\"dict_type\":\"app.common.enum.system.YesNoStatus\"}', '{\"code\":0,\"msg\":\"ok\",\"data\":[{\"label\":\"\\u662f\",\"value\":1,\"color\":\"#4CAF50\",\"ext\":[]},{\"label\":\"\\u5426\",\"value\":0,\"color\":\"#FF5252\",\"ext\":[]}]}', 1751877978, 1751877978, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307476710137856, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/message/notify-on-first-login-to-all', 'app\\admin\\controller\\system\\SysMessageController', 'notifyOnFirstLoginToAll', 'POST', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":[]}', 1751877979, 1751877979, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307554078269440, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/config/info', 'app\\admin\\controller\\system\\SysConfigController', 'getConfigInfo', 'GET', '{\"group_code\":\"site_setting\"}', '{\"code\":0,\"msg\":\"\\u64cd\\u4f5c\\u6210\\u529f\",\"data\":{\"site_open\":\"1\",\"site_url\":\"http:\\/\\/127.0.0.1:8998\",\"site_name\":\"madong-admin\",\"site_logo\":\"https:\\/\\/madong.tech\\/assets\\/images\\/logo.svg\",\"site_network_security\":\"2024042441\\u53f7-2\",\"site_description\":\"\\u5feb\\u901f\\u5f00\\u53d1\\u6846\\u67b6\",\"site_record_no\":\"2024042442\",\"site_icp_url\":\"https:\\/\\/beian.miit.gov.cn\\/\",\"site_network_security_url\":\"\"}}', 1751877988, 1751877988, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307598470782976, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/files/upload-image', 'app\\admin\\controller\\system\\SysUploadController', 'uploadImage', 'POST', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"platform\":\"local\",\"original_filename\":\"6c6515466de37629f9b67a959c6c8ed5.jpeg\",\"filename\":\"084926cafbc46ae315d34a25e4971da6.jpeg\",\"hash\":\"084926cafbc46ae315d34a25e4971da6\",\"content_type\":\"image\\/jpeg\",\"base_path\":\"\\/upload\\/084926cafbc46ae315d34a25e4971da6.jpeg\",\"ext\":\"jpeg\",\"size\":185786,\"size_info\":\"181.43 KB\",\"url\":\"\\/\\/127.0.0.1:8998http=>>\\/\\/43.138.153.216=>>8899\\/upload\\/084926cafbc46ae315d34a25e4971da6.jpeg\",\"path\":\"C:\\/DATA\\/MyMotion\\/dev\\/madong\\/enterprise\\/server\\/public\\/upload\\/084926cafbc46ae315d34a25e4971da6.jpeg\",\"id\":\"401307597724196864\",\"created_by\":\"1\",\"updated_at\":\"2025-07-07T08:46:33.000000Z\",\"created_at\":\"2025-07-07T08:46:33.000000Z\",\"created_date\":\"2025-07-07 16:46:33\",\"updated_date\":\"2025-07-07 16:46:33\"}}', 1751877993, 1751877993, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307630792089600, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/dict/get-by-dict-type', 'app\\admin\\controller\\system\\SysDictController', 'getByDictType', 'GET', '{\"dict_type\":\"app.common.enum.system.CloudStorage\"}', '{\"code\":0,\"msg\":\"ok\",\"data\":[{\"label\":\"\\u79c1\\u6709\\u4e91(\\u672c\\u5730)\",\"value\":\"local\",\"color\":\"hsl(345, 70%, 50%)\",\"ext\":[]},{\"label\":\"\\u963f\\u91cc\\u4e91\",\"value\":\"oss\",\"color\":\"hsl(76, 70%, 50%)\",\"ext\":[]},{\"label\":\"\\u817e\\u8baf\\u4e91\",\"value\":\"cos\",\"color\":\"hsl(108, 70%, 50%)\",\"ext\":[]},{\"label\":\"\\u4e03\\u725b\\u4e91\",\"value\":\"qiniu\",\"color\":\"hsl(180, 70%, 50%)\",\"ext\":[]},{\"label\":\"\\u4e9a\\u9a6c\\u900a(S3)\",\"value\":\"s3\",\"color\":\"hsl(271, 70%, 50%)\",\"ext\":[]}]}', 1751877997, 1751877997, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307631421235200, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/config/info', 'app\\admin\\controller\\system\\SysConfigController', 'getConfigInfo', 'GET', '{\"group_code\":\"basic_upload_setting\"}', '{\"code\":0,\"msg\":\"\\u64cd\\u4f5c\\u6210\\u529f\",\"data\":{\"mode\":\"local\",\"single_limit\":\"1024\",\"total_limit\":\"1024\",\"nums\":\"10\",\"exclude\":\"php,ext,exe\"}}', 1751877997, 1751877997, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307634634072064, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/config/info', 'app\\admin\\controller\\system\\SysConfigController', 'getConfigInfo', 'GET', '{\"group_code\":\"email_setting\"}', '{\"code\":0,\"msg\":\"\\u64cd\\u4f5c\\u6210\\u529f\",\"data\":{\"SMTPSecure\":\"ssl\",\"Host\":\"smtp.qq.com\",\"Port\":\"465\",\"Username\":\"kzhzjdyw888@qq.com\",\"Password\":\"\",\"From\":\"\",\"FromName\":\"kzhzjdyw888@qq.com\"}}', 1751877998, 1751877998, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307638945816576, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/config/info', 'app\\admin\\controller\\system\\SysConfigController', 'getConfigInfo', 'GET', '{\"group_code\":\"sms_setting\"}', '{\"code\":0,\"msg\":\"\\u64cd\\u4f5c\\u6210\\u529f\",\"data\":{\"enable\":\"1\",\"access_key_id\":\"234813346262818816\",\"access_key_secret\":\"238164553517768704\",\"sign_name\":\"\\u3010\\u7801\\u52a8\\u5f00\\u6e90\\u3011\\uff0c\\u4f60\\u7684\\u9a8c\\u8bc1\\u7801\\u662f{code}\\uff0c\\u6709\\u6548\\u671f5\\u5206\\u949f\\u3002\"}}', 1751877998, 1751877998, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307654406021120, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/config', 'app\\admin\\controller\\system\\SysConfigController', 'store', 'POST', '[{\"code\":\"site_open\",\"content\":1,\"name\":\"\\u7ad9\\u70b9\\u5f00\\u542f\",\"group_code\":\"site_setting\"},{\"code\":\"site_url\",\"content\":\"http:\\/\\/127.0.0.1:8998\",\"name\":\"\\u7f51\\u7ad9\\u5730\\u5740\",\"group_code\":\"site_setting\"},{\"code\":\"site_name\",\"content\":\"madong-admin\",\"name\":\"\\u7ad9\\u70b9\\u540d\\u79f0\",\"group_code\":\"site_setting\"},{\"code\":\"site_logo\",\"content\":\"http:\\/\\/127.0.0.1:8998\\/upload\\/084926cafbc46ae315d34a25e4971da6.jpeg\",\"name\":\"\\u7ad9\\u70b9Logo\",\"group_code\":\"site_setting\"},{\"code\":\"site_network_security\",\"content\":\"2024042441\\u53f7-2\",\"name\":\"\\u7f51\\u5907\\u6848\\u53f7\",\"group_code\":\"site_setting\"},{\"code\":\"site_keywords\",\"content\":\"\",\"name\":\"\\u5173\\u952e\\u5b57\",\"group_code\":\"site_setting\"},{\"code\":\"site_description\",\"content\":\"\\u5feb\\u901f\\u5f00\\u53d1\\u6846\\u67b6\",\"name\":\"\\u7f51\\u7ad9\\u63cf\\u8ff0\",\"group_code\":\"site_setting\"},{\"code\":\"site_record_no\",\"content\":2024042442,\"name\":\"\\u7f51\\u7ad9ICP\",\"group_code\":\"site_setting\"},{\"code\":\"site_icp_url\",\"content\":\"https:\\/\\/beian.miit.gov.cn\\/\",\"name\":\"ICP URL\",\"group_code\":\"site_setting\"},{\"code\":\"site_network_security_url\",\"content\":\"\",\"name\":\"\\u7f51\\u5b89\\u5907\\u6848\\u94fe\\u63a5\",\"group_code\":\"site_setting\"}]', '{\"code\":0,\"msg\":\"\\u4fdd\\u5b58\\u6210\\u529f\"}', 1751878000, 1751878000, 'admin');
INSERT INTO `ma_sys_operate_log` VALUES (401307654615736320, 1, '未知', 'admin', '127.0.0.1', '未知', 'Chrome', 'Windows', '/system/config/info', 'app\\admin\\controller\\system\\SysConfigController', 'getConfigInfo', 'GET', '{\"group_code\":\"site_setting\"}', '{\"code\":0,\"msg\":\"\\u64cd\\u4f5c\\u6210\\u529f\",\"data\":{\"site_open\":\"1\",\"site_url\":\"http:\\/\\/127.0.0.1:8998\",\"site_name\":\"madong-admin\",\"site_logo\":\"http:\\/\\/127.0.0.1:8998\\/upload\\/084926cafbc46ae315d34a25e4971da6.jpeg\",\"site_network_security\":\"2024042441\\u53f7-2\",\"site_description\":\"\\u5feb\\u901f\\u5f00\\u53d1\\u6846\\u67b6\",\"site_record_no\":\"2024042442\",\"site_icp_url\":\"https:\\/\\/beian.miit.gov.cn\\/\",\"site_network_security_url\":\"\",\"site_keywords\":\"\"}}', 1751878000, 1751878000, 'admin');

-- ----------------------------
-- Table structure for ma_sys_post
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_post`;
CREATE TABLE `ma_sys_post`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `tenant_id` bigint(20) NULL DEFAULT NULL COMMENT '租户id',
  `dept_id` bigint(20) NULL DEFAULT NULL COMMENT '部门id',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '岗位代码',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '岗位名称',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '修改时间',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '岗位信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_post
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_rate_limiter
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_rate_limiter`;
CREATE TABLE `ma_sys_rate_limiter`  (
  `id` bigint(20) NOT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '规则名称',
  `match_type` enum('allow','exact') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'exact' COMMENT '匹配类型',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT 'ip地址',
  `priority` int(11) NULL DEFAULT 100 COMMENT '优先级',
  `methods` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'GET' COMMENT '请求方法',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '/' COMMENT '请求路径',
  `limit_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'count' COMMENT '限制类型',
  `limit_value` int(11) NULL DEFAULT 0 COMMENT '限制值',
  `period` int(11) NULL DEFAULT 60 COMMENT '统计周期(秒)',
  `enabled` tinyint(1) NULL DEFAULT 1 COMMENT '状态',
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '提示信息',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建人',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '修改人',
  `created_at` bigint(20) NULL DEFAULT NULL,
  `updated_at` bigint(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '限流规则' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_rate_limiter
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_rate_restrictions
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_rate_restrictions`;
CREATE TABLE `ma_sys_rate_restrictions`  (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `enabled` tinyint(1) NULL DEFAULT 1 COMMENT '规则状态(0-禁用,1-启用)',
  `priority` int(10) UNSIGNED NULL DEFAULT 100 COMMENT '规则优先级(数字越小优先级越高)',
  `methods` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '0' COMMENT '限制值',
  `path` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '/' COMMENT '路径',
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '提示信息',
  `start_time` bigint(20) NULL DEFAULT NULL,
  `end_time` bigint(20) NULL DEFAULT NULL,
  `created_at` bigint(20) NULL DEFAULT NULL,
  `updated_at` bigint(20) NULL DEFAULT NULL,
  `created_by` bigint(20) NULL DEFAULT NULL,
  `updated_by` bigint(20) NULL DEFAULT NULL,
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '限制访问名单' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_rate_restrictions
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_recycle_bin
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_recycle_bin`;
CREATE TABLE `ma_sys_recycle_bin`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '回收的数据',
  `table_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '数据表',
  `table_prefix` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '表前缀',
  `enabled` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已还原',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '操作者IP',
  `operate_by` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作管理员',
  `created_at` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '数据回收记录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_recycle_bin
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_role
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_role`;
CREATE TABLE `ma_sys_role`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `tenant_id` bigint(20) NULL DEFAULT NULL COMMENT '租户id 默认null',
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
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '修改时间',
  `deleted_at` bigint(20) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_role
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_role_casbin
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_role_casbin`;
CREATE TABLE `ma_sys_role_casbin`  (
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '管理员主键',
  `role_casbin_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '对应casbin策略表'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与策略关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_role_casbin
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_role_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_role_dept`;
CREATE TABLE `ma_sys_role_dept`  (
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`role_id`, `dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与部门关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_role_dept
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_role_menu`;
CREATE TABLE `ma_sys_role_menu`  (
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
  `menu_id` bigint(20) UNSIGNED NOT NULL COMMENT '菜单主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与菜单关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_role_menu
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_role_scope_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_role_scope_dept`;
CREATE TABLE `ma_sys_role_scope_dept`  (
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与部门关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_role_scope_dept
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_route
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_route`;
CREATE TABLE `ma_sys_route`  (
  `id` bigint(20) NOT NULL,
  `cate_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '分组id',
  `app_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'api' COMMENT '应用名',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '路由名称',
  `describe` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '功能描述',
  `path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '路由路径',
  `method` enum('POST','GET','DELETE','PUT','*') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'GET' COMMENT '路由请求方式',
  `file_path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '文件路径',
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '方法名称',
  `query` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'get请求参数',
  `header` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'header',
  `request` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '请求数据',
  `request_type` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '请求类型',
  `response` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '返回数据',
  `request_example` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '请求示例',
  `response_example` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '返回示例',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '添加时间',
  `updated_at` bigint(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `path`(`path`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '路由规则表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_route
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_route_cate
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_route_cate`;
CREATE TABLE `ma_sys_route_cate`  (
  `id` bigint(20) NOT NULL,
  `pid` bigint(20) NOT NULL DEFAULT 0 COMMENT '上级id',
  `app_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '应用名',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `sort` int(11) NULL DEFAULT 0,
  `enabled` tinyint(1) NULL DEFAULT 1,
  `created_at` bigint(20) NULL DEFAULT 0 COMMENT '添加时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `app_name`(`app_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '路由规则分组表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_route_cate
-- ----------------------------

-- ----------------------------
-- Table structure for ma_sys_upload
-- ----------------------------
DROP TABLE IF EXISTS `ma_sys_upload`;
CREATE TABLE `ma_sys_upload`  (
  `id` bigint(20) NOT NULL COMMENT '文件信息ID',
  `tenant_id` bigint(20) NULL DEFAULT NULL COMMENT '租户id',
  `url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文件访问地址',
  `size` bigint(20) NULL DEFAULT NULL COMMENT '文件大小，单位字节',
  `size_info` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件大小，有单位',
  `hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件hash',
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件名称',
  `original_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '原始文件名',
  `base_path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '基础存储路径',
  `path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '存储路径',
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
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件信息' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_sys_upload
-- ----------------------------
INSERT INTO `ma_sys_upload` VALUES (401307597724196864, 1, '//127.0.0.1:8998http=>>//43.138.153.216=>>8899/upload/084926cafbc46ae315d34a25e4971da6.jpeg', 185786, '181.43 KB', '084926cafbc46ae315d34a25e4971da6', '084926cafbc46ae315d34a25e4971da6.jpeg', '6c6515466de37629f9b67a959c6c8ed5.jpeg', '/upload/084926cafbc46ae315d34a25e4971da6.jpeg', 'C:/DATA/MyMotion/dev/madong/enterprise/server/public/upload/084926cafbc46ae315d34a25e4971da6.jpeg', 'jpeg', 'image/jpeg', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1751877993, 1751877993, 1, NULL);

-- ----------------------------
-- Table structure for ma_tool_crud
-- ----------------------------
DROP TABLE IF EXISTS `ma_tool_crud`;
CREATE TABLE `ma_tool_crud`  (
  `id` bigint(20) NOT NULL,
  `pid` bigint(20) NOT NULL DEFAULT 0 COMMENT '菜单上级id',
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单名称',
  `model_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块名',
  `table_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '表明',
  `make_path` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件路径',
  `table_collation` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '字符集',
  `table_comment` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '表备注',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '添加时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建人',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新人',
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'crud生成记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_tool_crud
-- ----------------------------

-- ----------------------------
-- Table structure for ma_tool_crud_columns
-- ----------------------------
DROP TABLE IF EXISTS `ma_tool_crud_columns`;
CREATE TABLE `ma_tool_crud_columns`  (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `pid` bigint(20) NULL DEFAULT NULL COMMENT '管理crud表',
  `field` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字段名称',
  `field_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字段类型',
  `default_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '默认值',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '自动描述',
  `required` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '是否必填',
  `is_table` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '是否',
  `limit` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '长度',
  `primary_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '是否主键',
  `from_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '表单类型',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `created_at` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建人',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_tool_crud_columns
-- ----------------------------

-- ----------------------------
-- Table structure for ma_tool_crud_filepath
-- ----------------------------
DROP TABLE IF EXISTS `ma_tool_crud_filepath`;
CREATE TABLE `ma_tool_crud_filepath`  (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `pid` bigint(20) NULL DEFAULT NULL COMMENT '关联crud',
  `module_path` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '模块路径',
  `module_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '模块名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_tool_crud_filepath
-- ----------------------------

-- ----------------------------
-- Table structure for ma_tool_crud_form
-- ----------------------------
DROP TABLE IF EXISTS `ma_tool_crud_form`;
CREATE TABLE `ma_tool_crud_form`  (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `pid` bigint(20) NULL DEFAULT NULL COMMENT 'pid',
  `field` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `required` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `option` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `default_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_tool_crud_form
-- ----------------------------

-- ----------------------------
-- Table structure for ma_tool_crud_search
-- ----------------------------
DROP TABLE IF EXISTS `ma_tool_crud_search`;
CREATE TABLE `ma_tool_crud_search`  (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `pid` bigint(20) NULL DEFAULT NULL COMMENT 'pid',
  `field` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `search` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `options` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_tool_crud_search
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_cc_instance
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_cc_instance`;
CREATE TABLE `ma_wf_process_cc_instance`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `process_instance_id` bigint(20) NOT NULL COMMENT '流程实例ID',
  `process_task_id` bigint(20) NULL DEFAULT NULL COMMENT '任务ID',
  `actor_id` bigint(20) NOT NULL COMMENT '被抄送人ID',
  `state` int(11) NULL DEFAULT 0 COMMENT '抄送状态(1:已读；0：未读)',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_pccins_pinsid`(`process_instance_id`) USING BTREE,
  INDEX `idx_pccins_actor_id`(`actor_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程实例抄送' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_cc_instance
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_define
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_define`;
CREATE TABLE `ma_wf_process_define`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `type_id` bigint(20) NULL DEFAULT NULL COMMENT '流程分类',
  `icon` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'icon',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '唯一编码',
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '显示名称',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '流程描述',
  `enabled` int(11) NULL DEFAULT 0 COMMENT '流程是否可用(1可用；0不可用)',
  `is_active` tinyint(1) NULL DEFAULT 0 COMMENT '是否活跃版本(1是 0否)',
  `content` json NULL COMMENT '流程模型定义',
  `version` float(3, 1) UNSIGNED NULL DEFAULT 1.0 COMMENT '版本',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_user` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `delete_time` bigint(20) NULL DEFAULT NULL COMMENT '是否删除',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_define_name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程定义' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_define
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_define_favorite
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_define_favorite`;
CREATE TABLE `ma_wf_process_define_favorite`  (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `user_id` bigint(20) NULL DEFAULT NULL COMMENT '用户ID',
  `process_define_id` bigint(20) NULL DEFAULT NULL COMMENT '流程定义ID',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `remark` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '流程收藏表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_define_favorite
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_design
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_design`;
CREATE TABLE `ma_wf_process_design`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '唯一编码',
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '显示名称',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '描述',
  `type_id` bigint(20) NULL DEFAULT NULL COMMENT '流程分类',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图标',
  `is_deployed` tinyint(1) NULL DEFAULT 0 COMMENT '是否已部署',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_user` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `delete_time` bigint(20) NULL DEFAULT NULL COMMENT '是否删除',
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_designer_name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程设计' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_design
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_design_history
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_design_history`;
CREATE TABLE `ma_wf_process_design_history`  (
  `id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '主键',
  `process_design_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '流程设计ID',
  `content` json NULL COMMENT '流程模型定义',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `version` float(3, 1) NULL DEFAULT 1.0 COMMENT '版本',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_design_his_pdid`(`process_design_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程设计历史' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_design_history
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_form
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_form`;
CREATE TABLE `ma_wf_process_form`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '唯一编码',
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '显示名称',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '描述',
  `type_id` bigint(20) NULL DEFAULT NULL COMMENT '流程分类',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图标',
  `enabled` tinyint(1) NULL DEFAULT 0 COMMENT '是否禁用',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_user` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `delete_time` bigint(20) NULL DEFAULT NULL COMMENT '是否删除',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_form_name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '表单设计' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_form
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_form_history
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_form_history`;
CREATE TABLE `ma_wf_process_form_history`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `process_form_id` bigint(20) NOT NULL COMMENT 'ID',
  `content` json NULL COMMENT '模型定义',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '创建用户',
  `version` float(3, 1) NULL DEFAULT 1.0 COMMENT '版本',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_design_his_pdid`(`process_form_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '表单设计历史' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_form_history
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_instance
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_instance`;
CREATE TABLE `ma_wf_process_instance`  (
  `id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '主键',
  `parent_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '父流程ID，子流程实例才有值',
  `process_define_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '流程定义ID',
  `state` int(11) NULL DEFAULT NULL COMMENT '流程实例状态(10：进行中；20：已完成；30：已撤回；40：强行中止；50：挂起；99：已废弃)',
  `parent_node_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '父流程依赖的节点名称',
  `business_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务编号',
  `operator` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '流程发起人',
  `variable` json NULL COMMENT '附属变量json存储',
  `expire_time` int(11) NULL DEFAULT NULL COMMENT '期望完成时间',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `update_user` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_instance_pfid`(`process_define_id`) USING BTREE,
  INDEX `idx_process_instance_operator`(`operator`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程实例' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_instance
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_instance_history
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_instance_history`;
CREATE TABLE `ma_wf_process_instance_history`  (
  `id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '主键',
  `parent_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '父流程ID，子流程实例才有值',
  `process_define_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '流程定义ID',
  `state` int(11) NULL DEFAULT NULL COMMENT '流程实例状态(10：进行中；20：已完成；30：已撤回；40：强行中止；50：挂起；99：已废弃)',
  `parent_node_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '父流程依赖的节点名称',
  `business_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务编号',
  `operator` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '流程发起人',
  `variable` json NULL COMMENT '附属变量json存储',
  `expire_time` int(11) NULL DEFAULT NULL COMMENT '期望完成时间',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `update_user` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_instance_pfid`(`process_define_id`) USING BTREE,
  INDEX `idx_process_instance_operator`(`operator`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程实例' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_instance_history
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_surrogate
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_surrogate`;
CREATE TABLE `ma_wf_process_surrogate`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `process_define_id` bigint(20) NULL DEFAULT NULL COMMENT '流程定义id',
  `operator` bigint(20) NOT NULL COMMENT '授权人',
  `surrogate` bigint(20) NOT NULL COMMENT '代理人',
  `start_time` bigint(20) NULL DEFAULT NULL COMMENT '授权开始时间',
  `end_time` bigint(20) NULL DEFAULT NULL COMMENT '授权结束时间',
  `enabled` tinyint(1) NULL DEFAULT 1 COMMENT '是否启用',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程委托代理' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_surrogate
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_task
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_task`;
CREATE TABLE `ma_wf_process_task`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `process_instance_id` bigint(20) NOT NULL COMMENT '流程实例ID',
  `task_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务名称编码',
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务显示名称',
  `task_type` int(11) NULL DEFAULT NULL COMMENT '任务类型(0：主办任务；1：协办任务)',
  `perform_type` int(11) NULL DEFAULT NULL COMMENT '参与类型(0：普通参与；1：会签参与)',
  `task_state` int(11) NULL DEFAULT NULL COMMENT '任务状态(10：进行中；20：已完成；30：已撤回；40：强行中止；50：挂起；99：已废弃)',
  `operator` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务处理人',
  `finish_time` bigint(20) NULL DEFAULT NULL COMMENT '任务完成时间',
  `expire_time` bigint(20) NULL DEFAULT NULL COMMENT '任务期待完成时间',
  `form_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务处理表单KEY',
  `task_parent_id` bigint(20) NULL DEFAULT NULL COMMENT '父任务ID',
  `variable` json NULL COMMENT '附属变量json存储',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_task_piid`(`process_instance_id`) USING BTREE,
  INDEX `idx_process_task_name`(`task_name`) USING BTREE,
  INDEX `idx_process_task_operator`(`operator`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程任务' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_task
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_task_actor
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_task_actor`;
CREATE TABLE `ma_wf_process_task_actor`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `process_task_id` bigint(20) NOT NULL COMMENT '流程任务ID',
  `actor_id` bigint(20) NOT NULL COMMENT '参与者ID',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_task_actor_ptid`(`process_task_id`) USING BTREE,
  INDEX `idx_process_task_actor_aid`(`actor_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程任务和参与人关系' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_task_actor
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_task_actor_history
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_task_actor_history`;
CREATE TABLE `ma_wf_process_task_actor_history`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `process_task_id` bigint(20) NOT NULL COMMENT '流程任务ID',
  `actor_id` bigint(20) NOT NULL COMMENT '参与者ID',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_task_actor_ptid`(`process_task_id`) USING BTREE,
  INDEX `idx_process_task_actor_aid`(`actor_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程任务和参与人关系' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_task_actor_history
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_task_history
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_task_history`;
CREATE TABLE `ma_wf_process_task_history`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `process_instance_id` bigint(20) NOT NULL COMMENT '流程实例ID',
  `task_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务名称编码',
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务显示名称',
  `task_type` int(11) NULL DEFAULT NULL COMMENT '任务类型(0：主办任务；1：协办任务)',
  `perform_type` int(11) NULL DEFAULT NULL COMMENT '参与类型(0：普通参与；1：会签参与)',
  `task_state` int(11) NULL DEFAULT NULL COMMENT '任务状态(10：进行中；20：已完成；30：已撤回；40：强行中止；50：挂起；99：已废弃)',
  `operator` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务处理人',
  `finish_time` bigint(20) NULL DEFAULT NULL COMMENT '任务完成时间',
  `expire_time` bigint(20) NULL DEFAULT NULL COMMENT '任务期待完成时间',
  `form_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务处理表单KEY',
  `task_parent_id` bigint(20) NULL DEFAULT NULL COMMENT '父任务ID',
  `variable` json NULL COMMENT '附属变量json存储',
  `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
  `create_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_process_task_piid`(`process_instance_id`) USING BTREE,
  INDEX `idx_process_task_name`(`task_name`) USING BTREE,
  INDEX `idx_process_task_operator`(`operator`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程任务' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_task_history
-- ----------------------------

-- ----------------------------
-- Table structure for ma_wf_process_type
-- ----------------------------
DROP TABLE IF EXISTS `ma_wf_process_type`;
CREATE TABLE `ma_wf_process_type`  (
  `id` bigint(20) NOT NULL COMMENT '主键',
  `pid` bigint(20) NULL DEFAULT 0 COMMENT '父id',
  `icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'icon',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组名称',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `enabled` tinyint(4) NULL DEFAULT 1 COMMENT '1启用 0禁用',
  `create_time` bigint(20) NOT NULL COMMENT '创建时间',
  `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
  `update_user` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `create_user` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `delete_time` bigint(20) NULL DEFAULT NULL COMMENT '是否删除',
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '模型分组' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_wf_process_type
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
