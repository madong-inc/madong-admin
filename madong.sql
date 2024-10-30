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

 Date: 30/10/2024 17:41:57
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
-- Table structure for ma_crontab
-- ----------------------------
DROP TABLE IF EXISTS `ma_crontab`;
CREATE TABLE `ma_crontab`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `biz_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务标题',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务类型1 url,2 eval,3 shell',
  `task_cycle` tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务周期',
  `cycle_rule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务周期规则',
  `rule` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务表达式',
  `target` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '调用任务字符串',
  `running_times` int(11) NOT NULL DEFAULT 0 COMMENT '已运行次数',
  `last_running_time` int(11) NOT NULL DEFAULT 0 COMMENT '上次运行时间',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '任务状态状态0禁用,1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '软删除时间',
  `singleton` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否单次执行0是,1不是',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `title`(`title`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时器任务表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_crontab
-- ----------------------------
INSERT INTO `ma_crontab` VALUES (2, NULL, '执行php方法', 2, 5, '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":null,\"second\":\"5\"}', '*/5 * * * * *', 'return 123;', 1611, 1713763995, 0, 1713752627, 0, 1);
INSERT INTO `ma_crontab` VALUES (3, NULL, '调用php类静态方法', 2, 5, '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":null,\"second\":\"10\"}', '*/10 * * * * *', '\\plugin\\wf\\app\\common\\Test::demo();', 1275, 1719049130, 0, 1713752627, 0, 1);
INSERT INTO `ma_crontab` VALUES (8, NULL, '调用远程链接', 1, 4, '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":\"10\",\"second\":null}', '*/10 * * * *', 'http://www.baidu.com', 16, 1713763800, 0, 1713749636, 0, 1);
INSERT INTO `ma_crontab` VALUES (9, NULL, '执行sheel', 2, 5, '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":null,\"second\":\"10\"}', '*/10 * * * * *', 'echo time();', 2, 1719192170, 0, 1713752627, 0, 1);
INSERT INTO `ma_crontab` VALUES (12, NULL, '测试', 2, 5, '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":null,\"second\":\"60\"}', '*/60 * * * * *', 'return 60;', 0, 1719286839, 0, 1719145969, 0, 0);
INSERT INTO `ma_crontab` VALUES (13, NULL, '', 1, 4, '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":\"800\",\"second\":null}', '*/800 * * * *', '', 0, 0, 0, 1719216880, 0, 1);
INSERT INTO `ma_crontab` VALUES (14, NULL, '', 1, 3, '{\"month\":null,\"week\":null,\"day\":null,\"hour\":\"0\",\"minute\":\"30\",\"second\":null}', '30 */0 * * *', '', 0, 0, 0, 1719218841, 0, 1);
INSERT INTO `ma_crontab` VALUES (16, NULL, '测试任务', 4, 4, '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":\"30\",\"second\":null}', '*/30 * * * *', '\\plugin\\wf\\app\\jobs\\ProcessAutoExecute', 0, 1719295338, 0, 1719291874, 0, 1);
INSERT INTO `ma_crontab` VALUES (20, 'fbdd8223-631c-42eb-bb21-fe3af5468f1b', '期望时间', 4, 9, '{\"month\":\"6\",\"week\":null,\"day\":\"25\",\"hour\":\"15\",\"minute\":\"13\",\"second\":\"11\"}', '11 13 15 25 6 2024', '\\plugin\\wf\\app\\jobs\\ProcessAutoExecute', 0, 0, 0, 1719299291, 0, 0);
INSERT INTO `ma_crontab` VALUES (21, '7e4df897-10b8-4ea1-bf7c-d086943939cd', '期望时间', 4, 9, '{\"month\":\"6\",\"week\":null,\"day\":\"25\",\"hour\":\"15\",\"minute\":\"27\",\"second\":\"51\"}', '51 27 15 25 6 2024', '\\plugin\\wf\\app\\jobs\\ProcessAutoExecute', 0, 0, 0, 1719300471, 0, 0);

-- ----------------------------
-- Table structure for ma_crontab_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_crontab_log`;
CREATE TABLE `ma_crontab_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `crontab_id` bigint(20) UNSIGNED NOT NULL COMMENT '任务id',
  `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务调用目标字符串',
  `log` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '任务执行日志',
  `return_code` tinyint(1) NOT NULL DEFAULT 0 COMMENT '执行返回状态,0成功,1失败',
  `running_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '执行所用时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `crontab_id`(`crontab_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 673 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时器任务执行日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_crontab_log
-- ----------------------------
INSERT INTO `ma_crontab_log` VALUES (537, 12, 'return 60;', '60', 0, '0.075856', 1719192000);
INSERT INTO `ma_crontab_log` VALUES (538, 9, 'echo time();', '--', 0, '0.045275', 1719192010);
INSERT INTO `ma_crontab_log` VALUES (539, 9, 'echo time();', '--', 0, '0.032454', 1719192020);
INSERT INTO `ma_crontab_log` VALUES (540, 9, 'echo time();', '--', 0, '0.032909', 1719192030);
INSERT INTO `ma_crontab_log` VALUES (541, 9, 'echo time();', '--', 0, '0.016619', 1719192040);
INSERT INTO `ma_crontab_log` VALUES (542, 9, 'echo time();', '--', 0, '0.033711', 1719192050);
INSERT INTO `ma_crontab_log` VALUES (543, 9, 'echo time();', '--', 0, '0.032793', 1719192060);
INSERT INTO `ma_crontab_log` VALUES (544, 9, 'echo time();', '--', 0, '0.017047', 1719192070);
INSERT INTO `ma_crontab_log` VALUES (545, 9, 'echo time();', '--', 0, '0.016203', 1719192080);
INSERT INTO `ma_crontab_log` VALUES (546, 9, 'echo time();', '--', 0, '0.048419', 1719192090);
INSERT INTO `ma_crontab_log` VALUES (547, 9, 'echo time();', '--', 0, '0.033179', 1719192100);
INSERT INTO `ma_crontab_log` VALUES (548, 9, 'echo time();', '--', 0, '0.032032', 1719192110);
INSERT INTO `ma_crontab_log` VALUES (549, 9, 'echo time();', '--', 0, '0.047037', 1719192120);
INSERT INTO `ma_crontab_log` VALUES (550, 9, 'echo time();', '--', 0, '0.047933', 1719192130);
INSERT INTO `ma_crontab_log` VALUES (551, 9, 'echo time();', '--', 0, '0.017023', 1719192140);
INSERT INTO `ma_crontab_log` VALUES (552, 9, 'echo time();', '--', 0, '0.016824', 1719192150);
INSERT INTO `ma_crontab_log` VALUES (553, 9, 'echo time();', '--', 0, '0.047817', 1719192160);
INSERT INTO `ma_crontab_log` VALUES (554, 9, 'echo time();', '--', 0, '0.017127', 1719192170);
INSERT INTO `ma_crontab_log` VALUES (589, 12, 'return 60;', '60', 0, '0.058926', 1719286839);
INSERT INTO `ma_crontab_log` VALUES (672, 15, '\\plugin\\wf\\app\\jobs\\ProcessAutoExecute', '--', 0, '0.10343', 1719291771);

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
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_dept
-- ----------------------------
INSERT INTO `ma_system_dept` VALUES (2, 0, '0,1', '00002', '上海分公司', '109227769325555712', NULL, 1, 1, 1, 1, 1721640326, 1730080847, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (3, 1, '0,1', '00001', '厦门总公司', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723247885, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (4, 2, '0,1,2', '00003', '市场部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219992, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (5, 2, '0,1,2', '00004', '财务部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219992, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (6, 3, '0,1,3', '00005', '研发部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219253, NULL, NULL);
INSERT INTO `ma_system_dept` VALUES (7, 3, '0,1,3', '00006', '市场部门', '81896469954695168', NULL, 1, 1, 1, 1, 1721640326, 1730035124, NULL, NULL);

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
INSERT INTO `ma_system_dept_leader` VALUES (7, 73421010136862720);
INSERT INTO `ma_system_dept_leader` VALUES (2, 73421384377831424);
INSERT INTO `ma_system_dept_leader` VALUES (2, 73421690444582912);

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
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典类型表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_dict
-- ----------------------------
INSERT INTO `ma_system_dict` VALUES (108318632626491392, 'default', '字典类型', 'sys_dict_data_type', NULL, 2, NULL, 1, 1, 1, 1729863574, 1729863574, NULL);
INSERT INTO `ma_system_dict` VALUES (108318797395529728, 'default', '是否', 'yes_no', NULL, 2, NULL, 1, 1, 1, 1729863613, 1729863613, NULL);
INSERT INTO `ma_system_dict` VALUES (108327199312056320, 'default', '性别', 'sex', NULL, 2, NULL, 1, 1, 1, 1729865616, 1729865616, NULL);
INSERT INTO `ma_system_dict` VALUES (108327562568142848, 'default', '菜单类型', 'sys_menu_type', NULL, 2, NULL, 1, 1, 1, 1729865703, 1729865703, NULL);
INSERT INTO `ma_system_dict` VALUES (108328403966496768, 'default', '菜单打开类型', 'sys_menu_open_type', NULL, 2, NULL, 1, 1, 1, 1729865904, 1729865904, NULL);
INSERT INTO `ma_system_dict` VALUES (108329631148544000, 'default', '是否超级管理员', 'sys_user_admin_type', NULL, 2, NULL, 1, 1, 1, 1729866196, 1729866196, NULL);
INSERT INTO `ma_system_dict` VALUES (108339891250794496, 'default', '所属分组', 'sys_dict_group_code', NULL, 1, NULL, 1, 1, 1, 1729868642, 1729868642, NULL);
INSERT INTO `ma_system_dict` VALUES (108352455238094848, 'default', '角色类型', 'sys_role_role_type', NULL, 2, NULL, 1, 1, 1, 1729871638, 1729871638, NULL);
INSERT INTO `ma_system_dict` VALUES (108523964137082880, 'default', '请求类型', 'request_mode', NULL, 1, NULL, 1, 1, 1, 1729912529, 1729912529, NULL);

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
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典数据表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_dict_item
-- ----------------------------
INSERT INTO `ma_system_dict_item` VALUES (108322740880150528, 108339891250794496, '默认分组', 'default', 'sys_dict_group_code', 1, 1, 1, 1, 1729864553, 1729864553, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108323904434606080, 108339891250794496, '其他', 'other', 'sys_dict_group_code', 2, 1, 1, 1, 1729864831, 1729864831, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108325217297895424, 108318797395529728, '是', '1', 'yes_no', 0, 1, 1, 1, 1729865144, 1729865144, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108325255969378304, 108318797395529728, '否', '0', 'yes_no', 0, 1, 1, 1, 1729865153, 1729865153, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108326613028048896, 108318632626491392, '字符串', '1', 'sys_dict_data_type', 1, 1, 1, 1, 1729865477, 1729865477, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108326693793566720, 108318632626491392, '整型', '2', 'sys_dict_data_type', 0, 1, 1, 1, 1729865496, 1729865496, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108327272594935808, 108327199312056320, '男', '1', 'sex', 0, 1, 1, 1, 1729865634, 1729865634, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108327327527735296, 108327199312056320, '女', '2', 'sex', 0, 1, 1, 1, 1729865647, 1729865647, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108327812343140352, 108327562568142848, '目录', '1', 'sys_menu_type', 0, 1, 1, 1, 1729865763, 1729865763, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108327862620262400, 108327562568142848, '菜单', '2', 'sys_menu_type', 0, 1, 1, 1, 1729865775, 1729865775, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108327897684643840, 108327562568142848, '按钮', '3', 'sys_menu_type', 0, 1, 1, 1, 1729865783, 1729865783, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108327956966936576, 108327562568142848, '接口', '4', 'sys_menu_type', 0, 1, 1, 1, 1729865797, 1729865797, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108329132017979392, 108328403966496768, '无', '0', 'sys_menu_open_type', 1, 1, 1, 1, 1729866077, 1729866126, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108329204327780352, 108328403966496768, '组件', '1', 'sys_menu_open_type', 2, 1, 1, 1, 1729866094, 1729866134, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108329252830711808, 108328403966496768, '内链', '2', 'sys_menu_open_type', 3, 1, 1, 1, 1729866106, 1729866146, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108329303904751616, 108328403966496768, '外链', '3', 'sys_menu_open_type', 4, 1, 1, 1, 1729866118, 1729866152, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108329785582817280, 108329631148544000, '超级管理员', '1', 'sys_user_admin_type', 1, 1, 1, 1, 1729866233, 1729866233, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108329829593649152, 108329631148544000, '普通管理员', '2', 'sys_user_admin_type', 2, 1, 1, 1, 1729866244, 1729866244, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108353068097212416, 108352455238094848, '普通角色', '1', 'sys_role_role_type', 1, 1, 1, 1, 1729871784, 1729871784, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108353350323539968, 108352455238094848, '数据角色', '2', 'sys_role_role_type', 2, 1, 1, 1, 1729871851, 1729871851, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108524077190352896, 108523964137082880, 'GET', 'GET', 'request_mode', 1, 1, 1, 1, 1729912556, 1729912556, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108524119187918848, 108523964137082880, 'POST', 'POST', 'request_mode', 2, 1, 1, 1, 1729912566, 1729912566, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108524162666074112, 108523964137082880, 'PUT', 'PUT', 'request_mode', 3, 1, 1, 1, 1729912576, 1729912576, NULL, NULL);
INSERT INTO `ma_system_dict_item` VALUES (108524230089510912, 108523964137082880, 'DELETE', 'DELETE', 'request_mode', 4, 1, 1, 1, 1729912592, 1729912592, NULL, NULL);

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
  `enabled` smallint(6) NULL DEFAULT 1 COMMENT '登录状态 (1成功 2失败)',
  `message` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '提示消息',
  `login_time` int(11) NULL DEFAULT NULL COMMENT '登录时间',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
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
INSERT INTO `ma_system_login_log` VALUES (108502466194182144, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1729907403, NULL, 1729907403, 1729907403, NULL);
INSERT INTO `ma_system_login_log` VALUES (108519264071323648, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1729911408, NULL, 1729911408, 1729911408, NULL);
INSERT INTO `ma_system_login_log` VALUES (108602423114862592, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1729931235, NULL, 1729931235, 1729931235, NULL);
INSERT INTO `ma_system_login_log` VALUES (108627142602002432, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1729937128, NULL, 1729937128, 1729937128, NULL);
INSERT INTO `ma_system_login_log` VALUES (108637895753076736, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1729939692, NULL, 1729939692, 1729939692, NULL);
INSERT INTO `ma_system_login_log` VALUES (108857578196439040, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1729992069, NULL, 1729992069, 1729992069, NULL);
INSERT INTO `ma_system_login_log` VALUES (109036854040465408, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730034811, NULL, 1730034811, 1730034811, NULL);
INSERT INTO `ma_system_login_log` VALUES (109218313221050368, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730078074, NULL, 1730078075, 1730078075, NULL);
INSERT INTO `ma_system_login_log` VALUES (109242456691838976, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730083831, NULL, 1730083831, 1730083831, NULL);
INSERT INTO `ma_system_login_log` VALUES (109320275757240320, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730102384, NULL, 1730102384, 1730102384, NULL);
INSERT INTO `ma_system_login_log` VALUES (109361855205609472, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730112298, NULL, 1730112298, 1730112298, NULL);
INSERT INTO `ma_system_login_log` VALUES (109367223386247168, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730113578, NULL, 1730113578, 1730113578, NULL);
INSERT INTO `ma_system_login_log` VALUES (109389610844557312, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730118915, NULL, 1730118915, 1730118915, NULL);
INSERT INTO `ma_system_login_log` VALUES (109394145059147776, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730119996, NULL, 1730119996, 1730119996, NULL);
INSERT INTO `ma_system_login_log` VALUES (109394156484431872, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730119999, NULL, 1730119999, 1730119999, NULL);
INSERT INTO `ma_system_login_log` VALUES (109394171491651584, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120002, NULL, 1730120002, 1730120002, NULL);
INSERT INTO `ma_system_login_log` VALUES (109394694517166080, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120127, NULL, 1730120127, 1730120127, NULL);
INSERT INTO `ma_system_login_log` VALUES (109395234277953536, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120256, NULL, 1730120256, 1730120256, NULL);
INSERT INTO `ma_system_login_log` VALUES (109395474125033472, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120313, NULL, 1730120313, 1730120313, NULL);
INSERT INTO `ma_system_login_log` VALUES (109395549316321280, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120331, NULL, 1730120331, 1730120331, NULL);
INSERT INTO `ma_system_login_log` VALUES (109395981975556096, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120434, NULL, 1730120434, 1730120434, NULL);
INSERT INTO `ma_system_login_log` VALUES (109396034224001024, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120447, NULL, 1730120447, 1730120447, NULL);
INSERT INTO `ma_system_login_log` VALUES (109396215531180032, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120490, NULL, 1730120490, 1730120490, NULL);
INSERT INTO `ma_system_login_log` VALUES (109396579613544448, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120577, NULL, 1730120577, 1730120577, NULL);
INSERT INTO `ma_system_login_log` VALUES (109396647791955968, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120593, NULL, 1730120593, 1730120593, NULL);
INSERT INTO `ma_system_login_log` VALUES (109396866088701952, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120645, NULL, 1730120645, 1730120645, NULL);
INSERT INTO `ma_system_login_log` VALUES (109397037031755776, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730120686, NULL, 1730120686, 1730120686, NULL);
INSERT INTO `ma_system_login_log` VALUES (109398389413777408, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730121008, NULL, 1730121008, 1730121008, NULL);
INSERT INTO `ma_system_login_log` VALUES (109399122943021056, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730121183, NULL, 1730121183, 1730121183, NULL);
INSERT INTO `ma_system_login_log` VALUES (109399181558419456, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730121197, NULL, 1730121197, 1730121197, NULL);
INSERT INTO `ma_system_login_log` VALUES (109414185674346496, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730124774, NULL, 1730124774, 1730124774, NULL);
INSERT INTO `ma_system_login_log` VALUES (109414245569007616, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730124788, NULL, 1730124788, 1730124788, NULL);
INSERT INTO `ma_system_login_log` VALUES (109414306948452352, 'admin', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730124803, NULL, 1730124803, 1730124803, NULL);
INSERT INTO `ma_system_login_log` VALUES (109414372660613120, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730124819, NULL, 1730124819, 1730124819, NULL);
INSERT INTO `ma_system_login_log` VALUES (109414605683560448, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730124874, NULL, 1730124874, 1730124874, NULL);
INSERT INTO `ma_system_login_log` VALUES (109414744506634240, 'test', '127.0.0.1', '未知', 'Windows', 'Chrome', 1, '登录成功', 1730124907, NULL, 1730124907, 1730124907, NULL);
INSERT INTO `ma_system_login_log` VALUES (109753993772797952, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730205791, NULL, 1730205791, 1730205791, NULL);
INSERT INTO `ma_system_login_log` VALUES (109955541123600384, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730253843, NULL, 1730253843, 1730253843, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962239611637760, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255440, NULL, 1730255440, 1730255440, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962478049431552, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255497, NULL, 1730255497, 1730255497, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962657896992768, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255540, NULL, 1730255540, 1730255540, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962691849883648, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255548, NULL, 1730255548, 1730255548, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962749563506688, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255562, NULL, 1730255562, 1730255562, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962782069362688, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255570, NULL, 1730255570, 1730255570, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962788876718080, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255571, NULL, 1730255571, 1730255571, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962793712750592, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255573, NULL, 1730255573, 1730255573, NULL);
INSERT INTO `ma_system_login_log` VALUES (109962872368533504, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255591, NULL, 1730255591, 1730255591, NULL);
INSERT INTO `ma_system_login_log` VALUES (109963266834436096, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255685, NULL, 1730255685, 1730255685, NULL);
INSERT INTO `ma_system_login_log` VALUES (109963290775523328, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730255691, NULL, 1730255691, 1730255691, NULL);
INSERT INTO `ma_system_login_log` VALUES (109965779696488448, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730256284, NULL, 1730256284, 1730256284, NULL);
INSERT INTO `ma_system_login_log` VALUES (109967702403190784, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730256743, NULL, 1730256743, 1730256743, NULL);
INSERT INTO `ma_system_login_log` VALUES (109967706941427712, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730256744, NULL, 1730256744, 1730256744, NULL);
INSERT INTO `ma_system_login_log` VALUES (109967951716814848, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730256802, NULL, 1730256802, 1730256802, NULL);
INSERT INTO `ma_system_login_log` VALUES (109975389291548672, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730258576, NULL, 1730258576, 1730258576, NULL);
INSERT INTO `ma_system_login_log` VALUES (110050371312947200, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730276453, NULL, 1730276453, 1730276453, NULL);
INSERT INTO `ma_system_login_log` VALUES (110050415478968320, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730276463, NULL, 1730276463, 1730276463, NULL);
INSERT INTO `ma_system_login_log` VALUES (110051430395678720, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730276705, NULL, 1730276705, 1730276705, NULL);
INSERT INTO `ma_system_login_log` VALUES (110052181813628928, 'admin', '127.0.0.1', '未知', 'Other', 'Other', 1, '登录成功', 1730276884, NULL, 1730276884, 1730276884, NULL);

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_menu
-- ----------------------------
INSERT INTO `ma_system_menu` VALUES (109399693666160640, 1704792844212551689, 'admin', '详情', 'system:user:detail', NULL, 3, 40, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121319, NULL, 1730121355, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109399811517714432, 1704792844212551689, 'admin', '编辑', 'system:user:update', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121347, NULL, 1730121415, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109400032410734592, 1704792844212551689, 'admin', '重置密码', 'system:user:reset_password', NULL, 3, 50, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121400, NULL, 1730121400, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109400289219579904, 1704792844212551689, 'admin', '添加', 'system:user:save', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121461, NULL, 1730121461, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109400534368260096, 1704792844212551689, 'admin', '删除', 'system:user:remove', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121519, NULL, 1730121519, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109400678908170240, 1704792844212551689, 'admin', '授权角色', 'system:user:grant_role', NULL, 3, 60, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121554, NULL, 1730121554, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109407841718243328, 1704792844212551689, 'admin', '冻结用户', 'system:user:locked', NULL, 3, 70, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123262, NULL, 1730123314, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109408005879107584, 1704792844212551689, 'admin', '取消冻结', 'system:user:un_locked', NULL, 3, 80, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123301, NULL, 1730123301, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109410079526227968, 1704792844212551686, 'admin', '添加', 'system:menu:save', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123795, NULL, 1730123795, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109410203694403584, 1704792844212551686, 'admin', '删除', 'system:menu:remove', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123825, NULL, 1730123825, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109410337828245504, 1704792844212551686, 'admin', '编辑', 'system:menu:update', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123857, NULL, 1730123857, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109410533710630912, 1704792844212551686, 'admin', '详情', 'system:menu:detail', NULL, 3, 40, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123903, NULL, 1730123903, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109410879493246976, 1704792844212551687, 'admin', '添加', 'system:dept:save', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123986, NULL, 1730123986, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109410959407321088, 1704792844212551687, 'admin', '删除', 'system:dept:remove', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124005, NULL, 1730124005, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109411049895235584, 1704792844212551687, 'admin', '编辑', 'system:dept:update', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124027, NULL, 1730124027, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109411169378373632, 1704792844212551687, 'admin', '详情', 'system:dept:detail', NULL, 3, 40, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124055, NULL, 1730124055, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109411405593186304, 1704792844212551688, 'admin', '添加', 'system:post:save', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124111, NULL, 1730124111, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109411475520622592, 1704792844212551688, 'admin', '删除', 'system:post:remove', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124128, NULL, 1730124128, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109411563806527488, 1704792844212551688, 'admin', '编辑', 'system:post:update', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124149, NULL, 1730124149, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109411800763731968, 1704792844212551688, 'admin', '详情', 'system:post:detail', NULL, 3, 40, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124206, NULL, 1730124221, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109412207485390848, 1704792844212551690, 'admin', '添加', 'system:role:save', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124303, NULL, 1730124303, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109412280751493120, 1704792844212551690, 'admin', '删除', 'system:role:remove', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124320, NULL, 1730124320, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109412429192105984, 1704792844212551690, 'admin', '编辑', 'system:role:update', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124355, NULL, 1730124355, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109412494933626880, 1704792844212551690, 'admin', '详情', 'system:role:detail', NULL, 3, 40, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124371, NULL, 1730124371, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109413386634268672, 1704792844212551690, 'admin', '授权', 'system:rbac:save_role_menu', NULL, 3, 50, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124584, NULL, 1730124584, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109413560358146048, 1704792844212551690, 'admin', '用户', 'system:rbac:user_list_by_role_id', NULL, 3, 60, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124625, NULL, 1730124625, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109414087879954432, 1704792844212551690, 'admin', '移除用户', 'system:rbac:remove_user_role', NULL, 3, 70, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124751, NULL, 1730124751, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109415471077527552, 1704792844212551691, 'admin', '新增', 'system:dict:save', NULL, 3, 10, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125081, NULL, 1730125081, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109415539092361216, 1704792844212551691, 'admin', '删除', 'system:dict:remove', NULL, 3, 20, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125097, NULL, 1730125097, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109415607354658816, 1704792844212551691, 'admin', '编辑', 'system:dict:update', NULL, 3, 30, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125113, NULL, 1730125113, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109415680117444608, 1704792844212551691, 'admin', '详情', 'system:dict:detail', NULL, 3, 40, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125130, NULL, 1730125130, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109415924959940608, 1704792844212551691, 'admin', '字典项列表', 'system:dict-item:list', NULL, 3, 50, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125189, NULL, 1730125428, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109416658409492480, 1704792844212551691, 'admin', '字典项添加', 'system:dict_item:save', NULL, 3, 60, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125364, NULL, 1730125364, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109416733357510656, 1704792844212551691, 'admin', '字典项删除', 'system:dict_item:remove', NULL, 3, 70, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125382, NULL, 1730125382, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (109416801456230400, 1704792844212551691, 'admin', '字典项编辑', 'system:dict_item:update', NULL, 3, 80, NULL, NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125398, NULL, 1730125398, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551681, 0, 'admin', '首页', 'Dashboard', NULL, 1, -1, '/', 'BasicLayout', '/analytics', 'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722348622, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551682, 1704792844212551681, 'admin', '分析页', 'Analytics', NULL, 1, -1, '/analytics', '/dashboard/analytics/index', NULL, 'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 1, NULL, 1722348622, 1567738052492341249, 1722348622, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551683, 1704792844212551681, 'admin', '工作台', 'Workspace', NULL, 1, -1, '/workspace', '/dashboard/workspace/index', NULL, 'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722348622, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551684, 0, 'admin', '系统设置', 'system', NULL, 1, 1000, '/system', 'BasicLayout', '', 'ant-design:setting-outlined', 1, 0, NULL, 1, 0, 1, 1, 0, NULL, 1722348622, 1567738052492341249, 1729910254, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551686, 1704792844212551684, 'admin', '菜单管理', 'system:menu', NULL, 2, 1000, '/system/menu', '/system/menu/index', NULL, 'ant-design:menu-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722653097, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551687, 1704792844212551684, 'admin', '部门管理', 'system:dept', NULL, 2, 1000, '/system/dept', '/system/dept/index', NULL, 'ant-design:facebook-filled', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1730123948, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551688, 1704792844212551684, 'admin', '职位管理', 'system:post', NULL, 2, 1000, '/system/post', '/system/post/index', NULL, 'ant-design:database-filled', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1723265516, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551689, 1704792844212551684, 'admin', '用户管理', 'system:user', NULL, 2, 1000, '/system/user', '/system/user/index', NULL, 'ant-design:user-add-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722650392, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551690, 1704792844212551684, 'admin', '角色管理', 'system:role', NULL, 2, 1000, '/system/role', '/system/role/index', NULL, 'ant-design:team-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1730124265, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu` VALUES (1704792844212551691, 1704792844212551684, 'admin', '数据字典', 'system:dict', NULL, 2, 1000, '/system/dict', '/system/dict/index', NULL, 'ant-design:profile-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723993192, NULL, 1724934955, NULL, NULL, 'GET');

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
  `create_time` int(10) NULL DEFAULT NULL COMMENT '操作时间',
  `user_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作账号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统操作日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_operate_log
-- ----------------------------
INSERT INTO `ma_system_operate_log` VALUES (109791746430472192, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730205791,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-29 20:43:11\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730214792, NULL);
INSERT INTO `ma_system_operate_log` VALUES (109791759067910144, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730205791,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-29 20:43:11\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730214795, NULL);
INSERT INTO `ma_system_operate_log` VALUES (109791998344564736, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730205791,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-29 20:43:11\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730214852, NULL);
INSERT INTO `ma_system_operate_log` VALUES (109793638170628096, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730205791,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-29 20:43:11\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730215243, '');
INSERT INTO `ma_system_operate_log` VALUES (109793656415850496, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730205791,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-29 20:43:11\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730215247, '');
INSERT INTO `ma_system_operate_log` VALUES (109793665714622464, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730205791,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-29 20:43:11\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730215249, '');
INSERT INTO `ma_system_operate_log` VALUES (109793670789730304, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730205791,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-29 20:43:11\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730215250, '');
INSERT INTO `ma_system_operate_log` VALUES (110051785330266112, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730276705,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-30 16:25:05\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730276790, '');
INSERT INTO `ma_system_operate_log` VALUES (110052159550263296, '用户管理', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/user', 'app\\admin\\controller\\system\\SystemUserController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":1,\"user_name\":\"admin\",\"real_name\":\"Mesh Admin\",\"is_super\":1,\"mobile_phone\":\"13888888888\",\"email\":\"admin@admin.com\",\"avatar\":\"\",\"signed\":\"Today is very good\\uff01\",\"dashboard\":\"statistics\",\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730276705,\"backend_setting\":{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"},\"created_by\":1,\"updated_by\":1,\"create_time\":null,\"update_time\":\"2024-10-30 16:25:05\",\"delete_time\":null,\"sex\":2,\"remark\":null,\"birthday\":\"2024-08-15 23:52:01\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":1,\"role_id\":\"108354281047986176\"}}]},{\"id\":\"73421010136862720\",\"user_name\":\"test\",\"real_name\":\"\\u6d4b\\u8bd5\\u7528\\u62371\",\"is_super\":2,\"mobile_phone\":\"18888888888\",\"email\":\"405784684@qq.com\",\"avatar\":null,\"signed\":null,\"dashboard\":null,\"dept_id\":4,\"enabled\":1,\"login_ip\":\"127.0.0.1\",\"login_time\":1730124907,\"backend_setting\":null,\"created_by\":null,\"updated_by\":\"73421010136862720\",\"create_time\":\"2024-07-21 14:28:52\",\"update_time\":\"2024-10-28 22:15:07\",\"delete_time\":null,\"sex\":1,\"remark\":null,\"birthday\":\"2024-08-11\",\"tel\":null,\"is_locked\":0,\"depts\":{\"id\":4,\"pid\":2,\"level\":\"0,1,2\",\"code\":\"00003\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":null,\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-07-22 17:25:26\",\"update_time\":\"2024-08-10 00:13:12\",\"remark\":null},\"posts\":[],\"roles\":[{\"id\":\"108354281047986176\",\"pid\":0,\"name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"code\":\"superAdmin\",\"is_super_admin\":1,\"role_type\":1,\"data_scope\":1,\"enabled\":1,\"sort\":0,\"remark\":\"12\",\"created_by\":1,\"updated_by\":1,\"create_time\":\"2024-10-26 00:01:13\",\"update_time\":\"2024-10-28 14:44:31\",\"pivot\":{\"user_id\":\"73421010136862720\",\"role_id\":\"108354281047986176\"}}]}],\"total\":11}}', 1730276879, '');
INSERT INTO `ma_system_operate_log` VALUES (110058294403534848, '未知', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/logs/login', 'app\\admin\\controller\\system\\SystemLoginLogController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":\"107526264562257920\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674659,\"remark\":null,\"create_time\":\"2024-10-23 17:10:59\",\"update_time\":\"2024-10-23 17:10:59\"},{\"id\":\"107526302969499648\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674668,\"remark\":null,\"create_time\":\"2024-10-23 17:11:08\",\"update_time\":\"2024-10-23 17:11:08\"},{\"id\":\"107526588165394432\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674736,\"remark\":null,\"create_time\":\"2024-10-23 17:12:16\",\"update_time\":\"2024-10-23 17:12:16\"},{\"id\":\"107526843699171328\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674797,\"remark\":null,\"create_time\":\"2024-10-23 17:13:17\",\"update_time\":\"2024-10-23 17:13:17\"},{\"id\":\"107527009500008448\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674836,\"remark\":null,\"create_time\":\"2024-10-23 17:13:56\",\"update_time\":\"2024-10-23 17:13:56\"},{\"id\":\"107527043712946176\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674844,\"remark\":null,\"create_time\":\"2024-10-23 17:14:04\",\"update_time\":\"2024-10-23 17:14:04\"},{\"id\":\"107527119814397952\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674863,\"remark\":null,\"create_time\":\"2024-10-23 17:14:23\",\"update_time\":\"2024-10-23 17:14:23\"},{\"id\":\"107527497444364288\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674953,\"remark\":null,\"create_time\":\"2024-10-23 17:15:53\",\"update_time\":\"2024-10-23 17:15:53\"},{\"id\":\"107527684472573952\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674997,\"remark\":null,\"create_time\":\"2024-10-23 17:16:37\",\"update_time\":\"2024-10-23 17:16:37\"},{\"id\":\"107527713597820928\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":0,\"message\":\"\\u8d26\\u53f7\\u6216\\u5bc6\\u7801\\u9519\\u8bef\\uff0c\\u8bf7\\u91cd\\u65b0\\u8f93\\u5165!\",\"login_time\":1729675004,\"remark\":null,\"create_time\":\"2024-10-23 17:16:44\",\"update_time\":\"2024-10-23 17:16:44\"}],\"total\":70}}', 1730278342, '');
INSERT INTO `ma_system_operate_log` VALUES (110058859942514688, '未知', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/logs/login/1', 'app\\admin\\controller\\system\\SystemLoginLogController', 'destroy', 'DELETE', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":[]}', 1730278477, '');
INSERT INTO `ma_system_operate_log` VALUES (110059113026818048, '未知', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/logs/op', 'app\\admin\\controller\\system\\SystemLoginLogController', 'index', 'GET', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":{\"items\":[{\"id\":\"107526264562257920\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674659,\"remark\":null,\"create_time\":\"2024-10-23 17:10:59\",\"update_time\":\"2024-10-23 17:10:59\"},{\"id\":\"107526302969499648\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674668,\"remark\":null,\"create_time\":\"2024-10-23 17:11:08\",\"update_time\":\"2024-10-23 17:11:08\"},{\"id\":\"107526588165394432\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674736,\"remark\":null,\"create_time\":\"2024-10-23 17:12:16\",\"update_time\":\"2024-10-23 17:12:16\"},{\"id\":\"107526843699171328\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674797,\"remark\":null,\"create_time\":\"2024-10-23 17:13:17\",\"update_time\":\"2024-10-23 17:13:17\"},{\"id\":\"107527009500008448\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674836,\"remark\":null,\"create_time\":\"2024-10-23 17:13:56\",\"update_time\":\"2024-10-23 17:13:56\"},{\"id\":\"107527043712946176\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674844,\"remark\":null,\"create_time\":\"2024-10-23 17:14:04\",\"update_time\":\"2024-10-23 17:14:04\"},{\"id\":\"107527119814397952\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674863,\"remark\":null,\"create_time\":\"2024-10-23 17:14:23\",\"update_time\":\"2024-10-23 17:14:23\"},{\"id\":\"107527497444364288\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674953,\"remark\":null,\"create_time\":\"2024-10-23 17:15:53\",\"update_time\":\"2024-10-23 17:15:53\"},{\"id\":\"107527684472573952\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":1,\"message\":\"\\u767b\\u5f55\\u6210\\u529f\",\"login_time\":1729674997,\"remark\":null,\"create_time\":\"2024-10-23 17:16:37\",\"update_time\":\"2024-10-23 17:16:37\"},{\"id\":\"107527713597820928\",\"user_name\":\"admin\",\"ip\":\"127.0.0.1\",\"ip_location\":\"\\u672a\\u77e5\",\"os\":\"Other\",\"browser\":\"Other\",\"enabled\":0,\"message\":\"\\u8d26\\u53f7\\u6216\\u5bc6\\u7801\\u9519\\u8bef\\uff0c\\u8bf7\\u91cd\\u65b0\\u8f93\\u5165!\",\"login_time\":1729675004,\"remark\":null,\"create_time\":\"2024-10-23 17:16:44\",\"update_time\":\"2024-10-23 17:16:44\"}],\"total\":70}}', 1730278537, '');
INSERT INTO `ma_system_operate_log` VALUES (110059449334501376, '未知', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/logs/op/1', 'app\\admin\\controller\\system\\SystemLoginLogController', 'destroy', 'DELETE', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":[]}', 1730278617, '');
INSERT INTO `ma_system_operate_log` VALUES (110059804071956480, '未知', 'admin', '127.0.0.1', '未知', 'Other', 'Other', '/system/logs/login/1', 'app\\admin\\controller\\system\\SystemLoginLogController', 'destroy', 'DELETE', '[]', '{\"code\":0,\"msg\":\"ok\",\"data\":[]}', 1730278702, '');

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
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '岗位信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_post
-- ----------------------------
INSERT INTO `ma_system_post` VALUES (108515861576421376, '0001', '总经理', 0, 1, 1, 1, 1729910597, 1729910597, NULL, NULL);
INSERT INTO `ma_system_post` VALUES (108515985312583680, '0002', '业务员', 0, 1, 1, 1, 1729910627, 1729910627, NULL, NULL);
INSERT INTO `ma_system_post` VALUES (108516334249316352, '0003', '采购员', 0, 1, 1, 1, 1729910710, 1729910710, NULL, NULL);

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
  `role_type` tinyint(6) NULL DEFAULT NULL COMMENT '角色类型',
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
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_role
-- ----------------------------
INSERT INTO `ma_system_role` VALUES (108354281047986176, 0, '超级管理员', 'superAdmin', 1, 1, 1, 1, 0, '12', 1, 1, 1729872073, 1730097871, NULL);
INSERT INTO `ma_system_role` VALUES (108506227927027712, 0, '普通管理员', 'ss', 0, 1, 1, 1, 0, NULL, 1, 1, 1729908300, 1730037190, NULL);

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
  `menu_id` bigint(20) UNSIGNED NOT NULL COMMENT '菜单主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与菜单关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_role_menu
-- ----------------------------
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551681);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551682);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551683);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551684);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551686);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844212551690);
INSERT INTO `ma_system_role_menu` VALUES (5, 1704792844212551691);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551681);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551682);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551684);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551686);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551687);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551688);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551689);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551690);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551691);
INSERT INTO `ma_system_role_menu` VALUES (108354281047986176, 1704792844212551683);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551690);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551691);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551687);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551688);
INSERT INTO `ma_system_role_menu` VALUES (108506227927027712, 1704792844212551689);

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
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_user
-- ----------------------------
INSERT INTO `ma_system_user` VALUES (1, 'admin', 'Mesh Admin', '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 1, '13888888888', 'admin@admin.com', '', 'Today is very good！', 'statistics', 4, 1, '127.0.0.1', 1730276884, '{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"}', 1, 1, NULL, 1730276884, NULL, 2, NULL, '2024-08-15 23:52:01', NULL, 0);
INSERT INTO `ma_system_user` VALUES (73421010136862720, 'test', '测试用户1', '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 4, 1, '127.0.0.1', 1730124907, NULL, NULL, 73421010136862720, 1721543332, 1730124907, NULL, 1, NULL, '2024-08-11', NULL, 0);
INSERT INTO `ma_system_user` VALUES (73421384377831424, '12对对对4', '测试用户2', '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, '18888888888', NULL, NULL, NULL, NULL, 4, 0, NULL, NULL, NULL, NULL, 1, 1721543422, 1730110040, NULL, 1, NULL, '2024-08-11', NULL, 0);
INSERT INTO `ma_system_user` VALUES (73421690444582912, '12对对对45', '测试用户3', '$2y$10$6JMairFZ.P.lD1RhTIEHYOxwZqUWMKW1dDlfMA1NauQZQcUBOo/uu', 2, '18888888888', NULL, NULL, NULL, NULL, 5, 0, NULL, NULL, NULL, NULL, 1, 1721543495, 1730119816, NULL, 1, NULL, '2024-08-11', NULL, 0);
INSERT INTO `ma_system_user` VALUES (73421839434649600, '12对对对45f', '测试用户4', '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, '18888888888', NULL, NULL, NULL, NULL, 6, 0, NULL, NULL, NULL, NULL, 1, 1721543530, 1730096125, NULL, 1, NULL, '2024-08-11', NULL, 0);
INSERT INTO `ma_system_user` VALUES (73422563472183296, '1288', '测试用户5', '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, '18888888888', NULL, NULL, NULL, NULL, 7, 0, NULL, NULL, NULL, NULL, 1, 1721543703, 1730096131, NULL, 1, NULL, '2024-08-11', NULL, 0);
INSERT INTO `ma_system_user` VALUES (81893691261849600, 'dfdvdf', '测试用户6', '$2y$10$gyCkjXCBZai.wRbBaQ0puOoPkNkoAqMmJeztOCahAWS0h.7pdPsaq', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 3, 0, NULL, NULL, NULL, NULL, NULL, 1723563377, 1723563377, NULL, 1, NULL, NULL, '0592-8830352', 0);
INSERT INTO `ma_system_user` VALUES (81896469954695168, 'dfdvdf5', '测试用户7', '', 2, '18888888888', '405784684@qq.com', NULL, NULL, NULL, 2, 0, NULL, NULL, NULL, NULL, 1, 1723564040, 1730097723, NULL, 2, '123122s', NULL, '0592-8830352', 0);
INSERT INTO `ma_system_user` VALUES (108620053292912640, 'admin1', '测试用户9', '$2y$10$MQCWNNObA4REPeWhYm1OX.z8G4h4FiYXZ0.W.6b8Qk0MXvFIy2hK.', 2, '18888888888', '1111', NULL, NULL, NULL, 2, 0, NULL, NULL, NULL, 1, 1, 1729935438, 1730042777, NULL, 1, NULL, NULL, NULL, 0);
INSERT INTO `ma_system_user` VALUES (109227769325555712, '12', '4545', '$2y$10$fl4JtOVhtw7/qLi.Vs2S5OPnRmHSbWfF.u0mFB8nKm2rjlXAxierq', 2, '18973598388', NULL, NULL, NULL, NULL, 2, 0, NULL, NULL, NULL, 1, 1, 1730080329, 1730095645, NULL, 1, NULL, NULL, '0735-8830323', 0);
INSERT INTO `ma_system_user` VALUES (109292941754896384, '1212', '45454', '$2y$10$Yw78SY0U5VqLmElg3fOjJ.TdO1gEu38pQmH3hPMSA/N7kN0VFjSui', 2, '13555555555', '86', NULL, NULL, NULL, 2, 0, NULL, NULL, NULL, 1, 1, 1730095867, 1730096004, NULL, 1, '888', NULL, '0735-8830323', 0);

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
INSERT INTO `ma_system_user_post` VALUES (81896469954695168, 108515861576421376);
INSERT INTO `ma_system_user_post` VALUES (108629512094355456, 108515985312583680);
INSERT INTO `ma_system_user_post` VALUES (108667463734005760, 108515861576421376);

-- ----------------------------
-- Table structure for ma_system_user_role
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user_role`;
CREATE TABLE `ma_system_user_role`  (
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
  `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与角色关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ma_system_user_role
-- ----------------------------
INSERT INTO `ma_system_user_role` VALUES (109292941754896384, 108506227927027712);
INSERT INTO `ma_system_user_role` VALUES (1, 108354281047986176);
INSERT INTO `ma_system_user_role` VALUES (73421010136862720, 108354281047986176);

SET FOREIGN_KEY_CHECKS = 1;
