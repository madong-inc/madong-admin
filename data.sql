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

 Date: 25/10/2024 00:02:15
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
  `expire_time` int(10) NULL DEFAULT 0,
  `create_time` int(10) NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_cache
-- ----------------------------

-- ----------------------------
-- Table structure for ma_system_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dept`;
CREATE TABLE `ma_system_dept`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `pid` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '父ID',
  `level` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '组级集合',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '部门名称',
  `leader` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '负责人',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '联系电话',
  `enable` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`pid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_dept
-- ----------------------------
INSERT INTO `ma_system_dept` VALUES (2, 0, '0,1', '上海分公司', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723247873, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (3, 1, '0,1', '厦门总公司', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723247885, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (4, 2, '0,1,2', '市场部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219992, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (5, 2, '0,1,2', '财务部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219992, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (6, 3, '0,1,3', '研发部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219253, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (7, 3, '0,1,3', '市场部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219234, NULL, NULL);

-- ----------------------------
-- Table structure for ma_system_dept_leader
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dept_leader`;
CREATE TABLE `ma_system_dept_leader`  (
  `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '部门主键',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门领导关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_dept_leader
-- ----------------------------

-- ----------------------------
-- Table structure for ma_system_dict
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dict`;
CREATE TABLE `ma_system_dict`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典名称',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标示',
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '描述',
  `enable` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `created_by` int(11) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int(11) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典类型表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_dict
-- ----------------------------
INSERT INTO `ma_system_dict` VALUES (3, '数据状态', 'data_status', NULL, 1, 1, 1, NULL, 1725694101, NULL);
INSERT INTO `ma_system_dict` VALUES (88578904129933312, '性别', 'sex', '', 1, 1, 1, 1725157256, 1725762595, NULL);
INSERT INTO `ma_system_dict` VALUES (88591570181427200, '是否', 'yes_no', '', 1, 1, 1, 1725160276, 1725717069, NULL);
INSERT INTO `ma_system_dict` VALUES (90849132335468544, '状态', 'data_status', '', 1, 1, 1, 1725698521, 1725717047, NULL);
INSERT INTO `ma_system_dict` VALUES (90856624532623360, '菜单类型', 'menu_type', NULL, 1, 1, 1, 1725700307, 1725711307, NULL);
INSERT INTO `ma_system_dict` VALUES (90906778199527424, '请求类型', 'request_mode', '', 1, 1, 1, 1725712264, 1725716652, NULL);

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
  `ext` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '扩展',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `enable` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `created_by` int(11) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int(11) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `dict_id`(`dict_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典数据表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_dict_item
-- ----------------------------
INSERT INTO `ma_system_dict_item` VALUES (88578995842584576, 88578904129933312, '男', '1', 'sex', NULL, 1, 1, 1, 1, 1725157278, 1725157278, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (88579062112587776, 88578904129933312, '女', '2', 'sex', NULL, 1, 1, 1, 1, 1725157294, 1725157294, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (88591661264932864, 88591570181427200, '是', '1', 'yes_no', NULL, 1, 1, 1, 1, 1725160297, 1725698842, '12', NULL);
INSERT INTO `ma_system_dict_item` VALUES (88591702603993088, 88591570181427200, '否', '0', 'yes_no', NULL, 1, 1, 1, 1, 1725160307, 1725698849, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90849849347543040, 90849132335468544, '启用', '1', 'data_status', NULL, 1, 1, 1, 1, 1725698691, 1725699368, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90849915600769024, 90849132335468544, '禁用', '0', 'data_status', NULL, 1, 1, 1, 1, 1725698707, 1725698825, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90856804183052288, 90856624532623360, '目录', '1', 'menu_type', NULL, 1, 1, 1, 1, 1725700350, 1725700350, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90856848336490496, 90856624532623360, '菜单', '2', 'menu_type', NULL, 1, 1, 1, 1, 1725700360, 1725700369, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90856942716719104, 90856624532623360, '按钮', '3', 'menu_type', NULL, 1, 1, 1, 1, 1725700383, 1725700383, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90856988103282688, 90856624532623360, '接口', '4', 'menu_type', NULL, 1, 1, 1, 1, 1725700394, 1725700394, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90906894809567232, 90906778199527424, 'GET', 'GET', 'request_mode', NULL, 1, 1, 1, 1, 1725712292, 1725712292, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90906939730563072, 90906778199527424, 'POST', 'POST', 'request_mode', NULL, 1, 1, 1, 1, 1725712303, 1725712303, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90906983342936064, 90906778199527424, 'PUT', 'PUT', 'request_mode', NULL, 1, 1, 1, 1, 1725712313, 1725712313, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (90907033418731520, 90906778199527424, 'DELETE', 'DELETE', 'request_mode', NULL, 1, 1, 1, 1, 1725712325, 1725712325, NULL, NULL);

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
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`user_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '登录日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_login_log
-- ----------------------------
INSERT INTO `ma_system_login_log` VALUES (107526264562257920, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674659, NULL, 1729674659, 1729674659, NULL);
INSERT INTO `ma_system_login_log` VALUES (107526302969499648, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674668, NULL, 1729674668, 1729674668, NULL);
INSERT INTO `ma_system_login_log` VALUES (107526588165394432, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674736, NULL, 1729674736, 1729674736, NULL);
INSERT INTO `ma_system_login_log` VALUES (107526843699171328, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674797, NULL, 1729674797, 1729674797, NULL);
INSERT INTO `ma_system_login_log` VALUES (107527009500008448, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674836, NULL, 1729674836, 1729674836, NULL);
INSERT INTO `ma_system_login_log` VALUES (107527043712946176, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674844, NULL, 1729674844, 1729674844, NULL);
INSERT INTO `ma_system_login_log` VALUES (107527119814397952, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674863, NULL, 1729674863, 1729674863, NULL);
INSERT INTO `ma_system_login_log` VALUES (107527497444364288, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674953, NULL, 1729674953, 1729674953, NULL);
INSERT INTO `ma_system_login_log` VALUES (107527684472573952, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729674997, NULL, 1729674997, 1729674997, NULL);
INSERT INTO `ma_system_login_log` VALUES (107527713597820928, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 0, '账号或密码错误，请重新输入!', 1729675004, NULL, 1729675004, 1729675004, NULL);
INSERT INTO `ma_system_login_log` VALUES (107527844330082304, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 0, '账号或密码错误，请重新输入!', 1729675035, NULL, 1729675035, 1729675035, NULL);
INSERT INTO `ma_system_login_log` VALUES (107527858062233600, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729675039, NULL, 1729675039, 1729675039, NULL);
INSERT INTO `ma_system_login_log` VALUES (107559070575235072, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729682480, NULL, 1729682480, 1729682480, NULL);
INSERT INTO `ma_system_login_log` VALUES (107559707505463296, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729682632, NULL, 1729682632, 1729682632, NULL);
INSERT INTO `ma_system_login_log` VALUES (107559870559031296, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729682671, NULL, 1729682671, 1729682671, NULL);
INSERT INTO `ma_system_login_log` VALUES (107560110766821376, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729682728, NULL, 1729682728, 1729682728, NULL);
INSERT INTO `ma_system_login_log` VALUES (107560600057548800, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729682845, NULL, 1729682845, 1729682845, NULL);
INSERT INTO `ma_system_login_log` VALUES (107560999258820608, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729682940, NULL, 1729682940, 1729682940, NULL);
INSERT INTO `ma_system_login_log` VALUES (107584054777483264, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729688437, NULL, 1729688437, 1729688437, NULL);
INSERT INTO `ma_system_login_log` VALUES (107585870072254464, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729688870, NULL, 1729688870, 1729688870, NULL);
INSERT INTO `ma_system_login_log` VALUES (107599888149254144, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729692212, NULL, 1729692212, 1729692212, NULL);
INSERT INTO `ma_system_login_log` VALUES (107600604599291904, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729692383, NULL, 1729692383, 1729692383, NULL);
INSERT INTO `ma_system_login_log` VALUES (107600799638622208, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729692429, NULL, 1729692429, 1729692429, NULL);
INSERT INTO `ma_system_login_log` VALUES (107605884670185472, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729693642, NULL, 1729693642, 1729693642, NULL);
INSERT INTO `ma_system_login_log` VALUES (107605911232712704, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729693648, NULL, 1729693648, 1729693648, NULL);
INSERT INTO `ma_system_login_log` VALUES (107606461110161408, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729693779, NULL, 1729693779, 1729693779, NULL);
INSERT INTO `ma_system_login_log` VALUES (107606772793085952, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729693853, NULL, 1729693853, 1729693853, NULL);
INSERT INTO `ma_system_login_log` VALUES (107607877082025984, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729694117, NULL, 1729694117, 1729694117, NULL);
INSERT INTO `ma_system_login_log` VALUES (107932118469971968, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729771422, NULL, 1729771422, 1729771422, NULL);

-- ----------------------------
-- Table structure for ma_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_menu`;
CREATE TABLE `ma_system_menu`  (
  `id` bigint(20) NOT NULL COMMENT '菜单ID',
  `pid` bigint(20) NOT NULL DEFAULT 0 COMMENT '父ID',
  `app` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用编码',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '路由名称',
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
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '外部链接地址',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `open_type` int(11) NULL DEFAULT 0 COMMENT '是否外链 1=>是    0=>否',
  `keepalive` tinyint(1) NULL DEFAULT 0 COMMENT '是否缓存 1=>是    0=>否',
  `is_sync` tinyint(1) NULL DEFAULT 1 COMMENT '是否同步',
  `is_affix` tinyint(1) NULL DEFAULT 0 COMMENT '是否固定tags无法关闭',
  `variable` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '额外参数JSON',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `create_by` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `update_by` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '是否删除',
  `methods` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'get' COMMENT '请求方法',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_sys_menu_code`(`code`) USING BTREE,
  INDEX `idx_sys_menu_app_code`(`app`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_menu
-- ----------------------------
INSERT INTO `ma_system_menu` VALUES (78068183881355264, 1704792844296437762, 'admin', NULL, '删除用户', 'system:account:delete', NULL, 3, 999, NULL, NULL, NULL, '', 0, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722651305, NULL, 1723266475, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78069043831115776, 1704792844636176385, 'admin', NULL, '添加菜单', 'system:menu:add', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722651510, NULL, 1722652507, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78069326623674368, 1704792844636176385, 'admin', NULL, '删除菜单', 'system:menu:delete', NULL, 3, 999, NULL, NULL, NULL, '', 0, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722651577, NULL, 1722651577, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78069636763095040, 1704792844636176385, 'admin', NULL, '编辑菜单', 'system:menu:edit', NULL, 3, 999, NULL, NULL, NULL, '', 0, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722651651, NULL, 1722651651, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78947904798199808, 1704792844447432706, NULL, NULL, '角色权限', 'system:role:auth', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722861047, NULL, 1722861267, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78948086344454144, 1704792844447432706, NULL, NULL, '编辑角色', 'system:role:edit', NULL, 3, 999, NULL, NULL, NULL, '', 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722861090, NULL, 1723266319, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78948308638371840, 1704792844447432706, NULL, NULL, '删除角色', 'system:role:delete', NULL, 3, 999, NULL, NULL, NULL, '', 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722861143, NULL, 1723266382, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78948506085232640, 1704792844447432706, NULL, NULL, '角色用户', 'system:role:user', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722861190, NULL, 1722861190, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78948669558231040, 1704792844447432706, NULL, NULL, '数据权限', 'system:role:data_auth', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722861229, NULL, 1722861229, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (78948762147491840, 1704792844447432706, NULL, NULL, '工单权限', 'system:role:order_auth', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722861251, NULL, 1722861251, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (80645434934169600, 1704792844862668802, NULL, NULL, '新增职位', 'system:post:delete', NULL, 3, 100, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723265770, NULL, 1723265770, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (80645641495252992, 1704792844862668802, NULL, NULL, '删除职位', 'system:post:delete', NULL, 3, 100, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723265819, NULL, 1723265819, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (80645897154859008, 1704792844862668802, NULL, NULL, '编辑职位', 'system:post:edit', NULL, 3, 100, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723265880, NULL, 1723265880, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (80647139008581632, 1704792844787171329, NULL, NULL, '新增部门', 'system:dept:add', NULL, 3, 100, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723266176, NULL, 1723266176, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (80647407381123072, 1704792844787171329, NULL, NULL, '编辑部门', 'system:dept:edit', NULL, 3, 100, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723266240, NULL, 1723266240, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (80647574457028608, 1704792844787171329, NULL, NULL, '删除部门', 'system:dept:delete', NULL, 3, 100, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723266280, NULL, 1723266280, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (80648211005575168, 1704792844447432706, NULL, NULL, '添加角色', 'system:role:add', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723266431, NULL, 1723266431, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (80648539532824576, 1704792844296437762, NULL, NULL, '编辑用户', 'system:account:edit', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723266510, NULL, 1723266510, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (82955468976295936, 1704792844296437762, NULL, NULL, '冻结用户', 'system:user:lock', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723816525, NULL, 1723816999, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (82955893146259456, 1704792844296437762, NULL, NULL, '设置代理人', 'system:user:agent', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723816626, NULL, 1723816626, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (82957133343232000, 1704792844296437762, NULL, NULL, '解冻用户', 'system:user:unlock', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723816921, NULL, 1723816921, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (82957325467521024, 1704792844296437762, NULL, NULL, '查看详情', 'system:user:detail', NULL, 3, 999, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723816967, NULL, 1723816967, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (83696466383736832, 1704792844271271937, 'admin', 'SystemDicts', '数据字典', '', NULL, 2, 999, '/system/dict', '/system/dict/index', NULL, 'ant-design:profile-outlined', 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1723993192, NULL, 1724934955, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (91204237668782080, 0, NULL, NULL, '系统监控', NULL, NULL, 1, 999, '/monitor', NULL, NULL, 'ant-design:video-camera-filled', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1725783184, NULL, 1725783184, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (91205708317593600, 91204237668782080, NULL, NULL, '日志管理', NULL, NULL, 1, 999, '/logs', NULL, NULL, 'ant-design:profile-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1725783535, NULL, 1725783535, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (91206355020550144, 91205708317593600, NULL, NULL, '操作日志', NULL, NULL, 2, 999, '/monitor/logs/operate', NULL, NULL, 'ant-design:profile-twotone', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1725783689, NULL, 1725783819, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (91207128378904576, 91205708317593600, NULL, NULL, '登录日志', NULL, NULL, 2, 999, '/monitor/logs/login', NULL, NULL, 'ant-design:profile-twotone', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1725783873, NULL, 1725783873, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (91207899979845632, 91204237668782080, NULL, NULL, '定时任务', NULL, NULL, 2, 999, '/monitor/quartz', NULL, NULL, 'ant-design:field-time-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1725784057, NULL, 1725784320, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (93724200543588352, 0, NULL, NULL, '开发工具', NULL, NULL, 1, 999, '/dev', 'LAYOUT', NULL, 'ant-design:tool-twotone', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1726383990, NULL, 1726384082, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (93725484076109824, 93724200543588352, NULL, NULL, '数据模型', NULL, NULL, 2, 999, '/dev/schema', '/dev/crud/components/generation/index', NULL, 'ant-design:database-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1726384296, NULL, 1727063909, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551681, 0, 'admin', 'Dashboard', '首页', '', NULL, 1, 10, '/dashboard', 'LAYOUT', '/dashboard/analysis', 'ant-design:home-outlined', 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722348622, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551682, 1704792844212551681, 'admin', 'Analysis', '首页', '', NULL, 1, 10, '/dashboard/analysis', '/dashboard/analysis/index', NULL, 'ant-design:home-outlined', 0, 0, NULL, 1, 1, 0, 1, 1, NULL, 1722348622, 1567738052492341249, 1722348622, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844271271937, 0, 'admin', NULL, '系统设置', '', NULL, 1, 100, '/system', 'LAYOUT', '/system/account', 'ant-design:setting-outlined', 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722348622, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844296437762, 1704792844271271937, 'admin', 'AccountManagement', '用户管理', '', NULL, 2, 210, '/system/user', '/system/user/index', NULL, 'ant-design:user-add-outlined', 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722650392, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844296437862, 1704792844296437762, 'admin', NULL, '新增用户', 'system:account:add', NULL, 3, 999, NULL, NULL, NULL, NULL, 1, NULL, NULL, 1, 1, 0, 1, 0, NULL, 1722348622, NULL, 1723266464, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844447432706, 1704792844271271937, 'admin', 'RoleManagement', '角色管理', '', NULL, 2, 220, '/system/role', '/system/role/index', NULL, 'ant-design:solution', 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722653160, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844636176385, 1704792844271271937, 'admin', 'MenuManagement', '菜单管理', '', NULL, 2, 222, '/system/menu', '/system/menu/index', NULL, 'ant-design:menu-outlined', 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722653097, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844787171329, 1704792844271271937, 'admin', 'DeptManagement', '部门管理', '', NULL, 2, 230, '/system/dept', '/system/dept/index', NULL, 'ant-design:team', 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722653235, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844862668802, 1704792844271271937, 'admin', 'PostManagement', '职位管理', '', NULL, 2, 240, '/system/post', '/system/post/index', NULL, 'ant-design:database-filled', 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1723265516, 1567738052492341249, NULL, 'GET');

-- ----------------------------
-- Table structure for ma_system_operate_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_operate_log`;
CREATE TABLE `ma_system_operate_log`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户名',
  `app` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '应用名称',
  `method` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '请求方式',
  `router` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '请求路由',
  `service_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '业务名称',
  `ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '请求IP地址',
  `ip_location` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'IP所属地',
  `request_data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '请求数据',
  `created_by` int(11) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int(11) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(10) UNSIGNED ZEROFILL NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '操作日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_operate_log
-- ----------------------------

-- ----------------------------
-- Table structure for ma_system_post
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_post`;
CREATE TABLE `ma_system_post`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '主键',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '岗位代码',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '岗位名称',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `status` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '岗位信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_post
-- ----------------------------

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
  `data_scope` smallint(6) NULL DEFAULT 1 COMMENT '数据范围(1:全部数据权限 2:自定义数据权限 3:本部门数据权限 4:本部门及以下数据权限 5:本人数据权限)',
  `status` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` bigint(20) NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` bigint(20) NULL DEFAULT NULL COMMENT '更新者',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_role
-- ----------------------------

-- ----------------------------
-- Table structure for ma_system_role_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_role_dept`;
CREATE TABLE `ma_system_role_dept`  (
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`role_id`, `dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与部门关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_role_dept
-- ----------------------------
INSERT INTO `ma_system_role_dept` VALUES (2, 2);
INSERT INTO `ma_system_role_dept` VALUES (2, 4);
INSERT INTO `ma_system_role_dept` VALUES (2, 5);

-- ----------------------------
-- Table structure for ma_system_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_role_menu`;
CREATE TABLE `ma_system_role_menu`  (
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
  `menu_id` bigint(20) UNSIGNED NOT NULL COMMENT '菜单主键',
  PRIMARY KEY (`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与菜单关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_role_menu
-- ----------------------------
INSERT INTO `ma_system_role_menu` VALUES (1, 80645434934169600);
INSERT INTO `ma_system_role_menu` VALUES (1, 80645641495252992);
INSERT INTO `ma_system_role_menu` VALUES (1, 80645897154859008);
INSERT INTO `ma_system_role_menu` VALUES (1, 1704792844212551681);
INSERT INTO `ma_system_role_menu` VALUES (1, 1704792844212551682);
INSERT INTO `ma_system_role_menu` VALUES (1, 1704792844271271937);
INSERT INTO `ma_system_role_menu` VALUES (1, 1704792844862668802);
INSERT INTO `ma_system_role_menu` VALUES (2, 1704792844212551681);
INSERT INTO `ma_system_role_menu` VALUES (2, 1704792844212551682);
INSERT INTO `ma_system_role_menu` VALUES (3, 78068183881355264);
INSERT INTO `ma_system_role_menu` VALUES (3, 80648539532824576);
INSERT INTO `ma_system_role_menu` VALUES (3, 82955468976295936);
INSERT INTO `ma_system_role_menu` VALUES (3, 82955893146259456);
INSERT INTO `ma_system_role_menu` VALUES (3, 82957133343232000);
INSERT INTO `ma_system_role_menu` VALUES (3, 82957325467521024);
INSERT INTO `ma_system_role_menu` VALUES (3, 1704792844212551681);
INSERT INTO `ma_system_role_menu` VALUES (3, 1704792844212551682);
INSERT INTO `ma_system_role_menu` VALUES (3, 1704792844271271937);
INSERT INTO `ma_system_role_menu` VALUES (3, 1704792844296437762);
INSERT INTO `ma_system_role_menu` VALUES (3, 1704792844296437862);
INSERT INTO `ma_system_role_menu` VALUES (4, 1704792844212551681);
INSERT INTO `ma_system_role_menu` VALUES (4, 1704792844212551682);
INSERT INTO `ma_system_role_menu` VALUES (5, 78068183881355264);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844212551681);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844212551682);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844271271937);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844296437762);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844296437862);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844447432706);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844787171329);

-- ----------------------------
-- Table structure for ma_system_user
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user`;
CREATE TABLE `ma_system_user`  (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID,主键',
  `user_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `real_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户昵称',
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `is_super` tinyint(3) NULL DEFAULT 2 COMMENT '用户类型:(1系统用户 2普通用户)',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '手机',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户邮箱',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户头像',
  `signed` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '个人签名',
  `dashboard` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '后台首页类型',
  `dept_id` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '部门ID',
  `status` smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
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
  `telephone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '座机',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`user_name`) USING BTREE,
  INDEX `dept_id`(`dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_user
-- ----------------------------
INSERT INTO `ma_system_user` VALUES (1, 'admin', 'Mesh Admin', '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 1, '13888888888', 'admin@admin.com', '', 'Today is very good！', 'statistics', 4, 1, '127.0.0.1', 1729771422, '{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"}', 1, 1, NULL, 1729771422, NULL, 2, NULL, '2024-08-15 23:52:01', NULL);
INSERT INTO `ma_system_user` VALUES (73421010136862720, 'test', '测试用户', '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 4, 1, '127.0.0.1', 1723990671, NULL, NULL, NULL, 1721543332, 1723990730, NULL, 1, NULL, '2024-08-11', NULL);
INSERT INTO `ma_system_user` VALUES (73421384377831424, '12对对对4', NULL, '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, NULL, NULL, NULL, NULL, NULL, 4, 1, NULL, NULL, NULL, NULL, NULL, 1721543422, 1723646485, 1723646485, 1, NULL, '2024-08-11', NULL);
INSERT INTO `ma_system_user` VALUES (73421690444582912, '12对对对45', NULL, '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, NULL, NULL, NULL, NULL, NULL, 5, 1, NULL, NULL, NULL, NULL, NULL, 1721543495, 1723646489, 1723646489, 1, NULL, '2024-08-11', NULL);
INSERT INTO `ma_system_user` VALUES (73421839434649600, '12对对对45f', NULL, '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, NULL, NULL, NULL, NULL, NULL, 6, 1, NULL, NULL, NULL, NULL, NULL, 1721543530, 1723383053, 1723383053, 1, NULL, '2024-08-11', NULL);
INSERT INTO `ma_system_user` VALUES (73422563472183296, '1288', NULL, '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, NULL, NULL, NULL, NULL, NULL, 7, 1, NULL, NULL, NULL, NULL, NULL, 1721543703, 1723301523, 1723301523, 1, NULL, '2024-08-11', NULL);
INSERT INTO `ma_system_user` VALUES (81893691261849600, 'dfdvdf', 'dfsdasdddfsd', '$2y$10$gyCkjXCBZai.wRbBaQ0puOoPkNkoAqMmJeztOCahAWS0h.7pdPsaq', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 3, 1, NULL, NULL, NULL, NULL, NULL, 1723563377, 1723563377, NULL, 1, NULL, NULL, '0592-8830352');
INSERT INTO `ma_system_user` VALUES (81896469954695168, 'dfdvdf5', 'dfsdasdddfsd', '$2y$10$B93./KRnkeqOWjyMHvAHwudprYrxSG6s8WfbATzx3os3KHThoEQn6', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 3, 1, NULL, NULL, NULL, NULL, NULL, 1723564040, 1723564040, NULL, 1, NULL, NULL, '0592-8830352');
INSERT INTO `ma_system_user` VALUES (81896735693213696, 'dfdvdf59999', 'dfsdasdddfsd', '$2y$10$u7tz0xplLKr8CsN7ERoSjuV2hjMPLKdAF6Rym.cMhh7GMyWirH/12', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 3, 1, NULL, NULL, NULL, NULL, NULL, 1723564103, 1723564103, NULL, 1, NULL, NULL, '0592-8830352');
INSERT INTO `ma_system_user` VALUES (81897438595649536, 'dfdvdf599999', 'dfsdasdddfsd', '$2y$10$E.zO40xGpS4ePUrS5bougOWS9jKgPyHw/PoZU7pvFTUjkhcNIkojy', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 3, 1, NULL, NULL, NULL, NULL, NULL, 1723564270, 1723736038, NULL, 1, NULL, '2024-08-15 23:33:39', '0592-8830352');
INSERT INTO `ma_system_user` VALUES (82228916915408896, 'test1', '测试用户', '$2y$10$eppqmaeRs.J7cpOJK8dnOOse6j5imOey6WTprsOiAkvgJI0V4JMM2', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 3, 1, NULL, NULL, NULL, NULL, NULL, 1723643301, 1723643301, NULL, 1, NULL, NULL, '0592-8830352');
INSERT INTO `ma_system_user` VALUES (82257579824254976, 'llll', '测试用户', '$2y$10$hyD10dt53c.kFxlnTeToJ.SvpyO8q7oknK1th.7YdkKq5dR2Ni8E6', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 2, 1, NULL, NULL, NULL, NULL, NULL, 1723650135, 1723650135, NULL, 1, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for ma_system_user_post
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user_post`;
CREATE TABLE `ma_system_user_post`  (
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `post_id` bigint(20) UNSIGNED NOT NULL COMMENT '岗位主键',
  PRIMARY KEY (`user_id`, `post_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与岗位关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_user_post
-- ----------------------------
INSERT INTO `ma_system_user_post` VALUES (73421010136862720, 80632926714335232);
INSERT INTO `ma_system_user_post` VALUES (80634467957477376, 80634467957477376);
INSERT INTO `ma_system_user_post` VALUES (81896469954695168, 2);
INSERT INTO `ma_system_user_post` VALUES (81896735693213696, 2);
INSERT INTO `ma_system_user_post` VALUES (81896735693213696, 3);
INSERT INTO `ma_system_user_post` VALUES (81897438595649536, 2);
INSERT INTO `ma_system_user_post` VALUES (81897438595649536, 3);
INSERT INTO `ma_system_user_post` VALUES (82228916915408896, 2);
INSERT INTO `ma_system_user_post` VALUES (82228916915408896, 80634467957477376);

-- ----------------------------
-- Table structure for ma_system_user_role
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user_role`;
CREATE TABLE `ma_system_user_role`  (
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`user_id`, `role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与角色关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_user_role
-- ----------------------------
INSERT INTO `ma_system_user_role` VALUES (1, 1);
INSERT INTO `ma_system_user_role` VALUES (73421010136862720, 1);
INSERT INTO `ma_system_user_role` VALUES (73421384377831424, 1);
INSERT INTO `ma_system_user_role` VALUES (73421384377831424, 4);
INSERT INTO `ma_system_user_role` VALUES (73421690444582912, 4);
INSERT INTO `ma_system_user_role` VALUES (73421839434649600, 1);
INSERT INTO `ma_system_user_role` VALUES (73421839434649600, 4);
INSERT INTO `ma_system_user_role` VALUES (73422563472183296, 4);
INSERT INTO `ma_system_user_role` VALUES (81896469954695168, 2);
INSERT INTO `ma_system_user_role` VALUES (81896469954695168, 3);
INSERT INTO `ma_system_user_role` VALUES (81896735693213696, 2);
INSERT INTO `ma_system_user_role` VALUES (81896735693213696, 3);
INSERT INTO `ma_system_user_role` VALUES (81897438595649536, 2);
INSERT INTO `ma_system_user_role` VALUES (81897438595649536, 3);
INSERT INTO `ma_system_user_role` VALUES (82228916915408896, 2);

SET FOREIGN_KEY_CHECKS = 1;
