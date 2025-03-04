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

 Date: 24/11/2024 16:12:58
*/

SET NAMES utf8mb4;
SET
FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ma_cache
-- ----------------------------
DROP TABLE IF EXISTS `ma_cache`;
CREATE TABLE `ma_cache`
(
    `key`         varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
    `result`      text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
    `expire_time` int(11) NULL DEFAULT 0,
    `create_time` int(11) NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_cache
-- ----------------------------

-- ----------------------------
-- Table structure for ma_system_config
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_config`;
CREATE TABLE `ma_system_config`
(
    `id`          bigint(20) NOT NULL COMMENT '配置ID',
    `group_code`  varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分组编码',
    `code`        varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '唯一编码',
    `name`        varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置名称',
    `content`     longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置内容',
    `is_sys`      tinyint(1) NULL DEFAULT 0 COMMENT '是否系统',
    `enabled`     tinyint(1) NULL DEFAULT 1 COMMENT '是否启用',
    `create_time` bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
    `create_user` bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
    `update_time` bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
    `update_user` bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
    `delete_time` timestamp NULL DEFAULT NULL COMMENT '是否删除',
    `remark`      longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX         `idx_config_code`(`code`) USING BTREE,
    INDEX         `idx_config_group_code`(`group_code`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_config
-- ----------------------------
INSERT INTO `ma_system_config`
VALUES (234808972845260800, 'local', 'root', '', 'public', 0, 1, 1732029810, NULL, 1732029878, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234808972937535488, 'local', 'dirname', '', 'upload', 0, 1, 1732029810, NULL, 1732029878, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234808972962701312, 'local', 'domain', '', 'http://127.0.0.1:8899/', 0, 1, 1732029810, NULL, 1732029878, NULL,
        NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234812723148627968, 'oss', 'accessKeyId', '', '1', 0, 1, 1732030257, NULL, 1732030257, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234812723182182400, 'oss', 'accessKeySecret', '', '2', 0, 1, 1732030257, NULL, 1732030257, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234812723207348224, 'oss', 'bucket', '', '3', 0, 1, 1732030257, NULL, 1732030257, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234812723224125440, 'oss', 'dirname', '', '4', 0, 1, 1732030257, NULL, 1732030257, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234812723249291264, 'oss', 'domain', '', '5', 0, 1, 1732030257, NULL, 1732030257, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234812723274457088, 'oss', 'endpoint', '', '6', 0, 1, 1732030257, NULL, 1732030257, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234812723308011520, 'oss', 'remark', '', '7', 0, 1, 1732030257, NULL, 1732030257, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813088564781056, 'cos', 'secretId', '', '11', 0, 1, 1732030301, NULL, 1732030301, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813088589946880, 'cos', 'secretKey', '', '22', 0, 1, 1732030301, NULL, 1732030301, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813088606724096, 'cos', 'bucket', '', '33', 0, 1, 1732030301, NULL, 1732030301, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813088682221568, 'cos', 'dirname', '', '44', 0, 1, 1732030301, NULL, 1732030301, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813088707387392, 'cos', 'domain', '', '55', 0, 1, 1732030301, NULL, 1732030301, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813088724164608, 'cos', 'region', '', '66', 0, 1, 1732030301, NULL, 1732030301, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813088732553216, 'cos', 'remark', '', '77', 0, 1, 1732030301, NULL, 1732030301, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813242898391040, 'qiniu', 'accessKey', '', '99', 0, 1, 1732030319, NULL, 1732030319, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813242915168256, 'qiniu', 'secretKey', '', '88', 0, 1, 1732030319, NULL, 1732030319, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813242940334080, 'qiniu', 'bucket', '', '7', 0, 1, 1732030319, NULL, 1732030319, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813242965499904, 'qiniu', 'dirname', '', '78', 0, 1, 1732030319, NULL, 1732030319, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813242990665728, 'qiniu', 'domain', '', '8', 0, 1, 1732030319, NULL, 1732030319, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813243007442944, 'qiniu', 'region', '', '', 0, 1, 1732030319, NULL, 1732030319, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813243024220160, 'qiniu', 'remark', '', '897', 0, 1, 1732030319, NULL, 1732030319, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346136989696, 's3', 'key', '', '12', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346153766912, 's3', 'secret', '', '12', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346170544128, 's3', 'bucket', '', '12', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346178932736, 's3', 'dirname', '', '12', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346195709952, 's3', 'domain', '', '12', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346220875776, 's3', 'endpoint', '', '12', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346246041600, 's3', 'region', '', '12', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346262818816, 's3', 'acl', '', '6', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234813346287984640, 's3', 'remark', '', '4', 0, 1, 1732030332, NULL, 1732030332, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234824011379646464, 'email_setting', 'SMTPSecure', '', 'ssl', 0, 1, 1732031603, NULL, 1732429949, NULL, NULL,
        NULL);
INSERT INTO `ma_system_config`
VALUES (234824011396423680, 'email_setting', 'Host', '', 'smtp.qq.com', 0, 1, 1732031603, NULL, 1732429949, NULL, NULL,
        NULL);
INSERT INTO `ma_system_config`
VALUES (234824011429978112, 'email_setting', 'Port', '', '465', 0, 1, 1732031603, NULL, 1732429949, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234824011455143936, 'email_setting', 'Username', '', 'kzhzjdyw888@qq.com', 0, 1, 1732031603, NULL, 1732429949,
        NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234824011480309760, 'email_setting', 'Password', '', '', 0, 1, 1732031603, NULL, 1732429949, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234824011513864192, 'email_setting', 'From', '', '', 0, 1, 1732031603, NULL, 1732429949, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (234824011539030016, 'email_setting', 'FromName', '', 'kzhzjdyw888@qq.com', 0, 1, 1732031603, NULL, 1732429949,
        NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238040600610611200, 'basic_upload_setting', 'mode', '上传模式', 'local', 0, 1, 1732415050, NULL, 1732429734,
        NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238040600669331456, 'basic_upload_setting', 'single_limit', '上传大小', '1024', 0, 1, 1732415050, NULL,
        1732429734, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238040600694497280, 'basic_upload_setting', 'total_limit', '文件限制', '1024', 0, 1, 1732415050, NULL,
        1732429734, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238040600711274496, 'basic_upload_setting', 'nums', '数量限制', '10', 0, 1, 1732415050, NULL, 1732429734, NULL,
        NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238040600728051712, 'basic_upload_setting', 'exclude', '不允许文件类型', 'php,ext,exe', 0, 1, 1732415050, NULL,
        1732429734, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553500991488, 'site_setting', 'site_open', '站点开启', '1', 0, 1, 1732429827, NULL, 1732430264, NULL,
        NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553517768704, 'site_setting', 'site_url', '网站地址', '127.0.0.1:8899', 0, 1, 1732429827, NULL,
        1732430264, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553534545920, 'site_setting', 'site_name', '站点名称', 'MaDong Admin', 0, 1, 1732429827, NULL, 1732430264,
        NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553551323136, 'site_setting', 'site_logo', '站点Logo', 'https://madong.tech/assets/images/logo.svg', 0, 1,
        1732429827, NULL, 1732430264, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553568100352, 'site_setting', 'site_network_security', '网备案号', '2024042441号-2', 0, 1, 1732429827,
        NULL, 1732430264, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553584877568, 'site_setting', 'site_description', '网站描述', '快速开发框架', 0, 1, 1732429827, NULL,
        1732430264, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553610043392, 'site_setting', 'site_record_no', '网站ICP', '2024042442', 0, 1, 1732429827, NULL,
        1732430264, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553618432000, 'site_setting', 'site_icp_url', 'ICP URL', 'https://beian.miit.gov.cn/', 0, 1, 1732429827,
        NULL, 1732430264, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238164553635209216, 'site_setting', 'site_network_security_url', '网安备案链接', '', 0, 1, 1732429827, NULL,
        1732430264, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238165979958616064, 'sms_setting', 'enable', '是否开启', '1', 0, 1, 1732429997, NULL, 1732430097, NULL, NULL,
        NULL);
INSERT INTO `ma_system_config`
VALUES (238165979975393280, 'sms_setting', 'access_key_id', 'access_key_id', '234813346262818816', 0, 1, 1732429997,
        NULL, 1732430097, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238165979992170496, 'sms_setting', 'access_key_secret', 'access_key_secret', '238164553517768704', 0, 1,
        1732429997, NULL, 1732430097, NULL, NULL, NULL);
INSERT INTO `ma_system_config`
VALUES (238165980201885696, 'sms_setting', 'sign_name', 'sign_name', '【码动开源】，你的验证码是{code}，有效期5分钟。', 0, 1,
        1732429997, NULL, 1732430097, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for ma_system_crontab
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_crontab`;
CREATE TABLE `ma_system_crontab`
(
    `id`                bigint(20) UNSIGNED NOT NULL,
    `biz_id`            varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务id',
    `title`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务标题',
    `type`              tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务类型1 url,2 eval,3 shell',
    `task_cycle`        tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务周期',
    `cycle_rule`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务周期规则',
    `rule`              longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '任务表达式',
    `target`            longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '调用任务字符串',
    `running_times`     int(11) NOT NULL DEFAULT 0 COMMENT '已运行次数',
    `last_running_time` int(11) NOT NULL DEFAULT 0 COMMENT '上次运行时间',
    `enabled`           tinyint(4) NOT NULL DEFAULT 0 COMMENT '任务状态状态0禁用,1启用',
    `create_time`       int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
    `delete_time`       int(11) NOT NULL DEFAULT 0 COMMENT '软删除时间',
    `singleton`         tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否单次执行0是,1不是',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX               `title`(`title`) USING BTREE,
    INDEX               `status`(`enabled`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时器任务表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_crontab
-- ----------------------------
INSERT INTO `ma_system_crontab`
VALUES (2, NULL, '执行php方法', 2, 5,
        '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":null,\"second\":\"5\"}', '*/5 * * * * *',
        'return 123;', 1625, 1731116644, 0, 1713752627, 0, 1);
INSERT INTO `ma_system_crontab`
VALUES (3, NULL, '调用php类静态方法', 2, 5,
        '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":null,\"second\":\"10\"}', '*/10 * * * * *',
        'return 888;', 1277, 1731136461, 0, 1713752627, 0, 1);
INSERT INTO `ma_system_crontab`
VALUES (8, NULL, '调用远程链接', 1, 4,
        '{\"month\":null,\"week\":null,\"day\":null,\"hour\":null,\"minute\":\"10\",\"second\":null}', '*/10 * * * *',
        'http://www.baidu.com', 25, 1731136446, 0, 1713749636, 0, 1);

-- ----------------------------
-- Table structure for ma_system_crontab_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_crontab_log`;
CREATE TABLE `ma_system_crontab_log`
(
    `id`           bigint(20) UNSIGNED NOT NULL,
    `crontab_id`   bigint(20) UNSIGNED NOT NULL COMMENT '任务id',
    `target`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务调用目标字符串',
    `log`          longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '任务执行日志',
    `return_code`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '执行返回状态,1成功,0失败',
    `running_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '执行所用时间',
    `create_time`  int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX          `create_time`(`create_time`) USING BTREE,
    INDEX          `crontab_id`(`crontab_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时器任务执行日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_crontab_log
-- ----------------------------
INSERT INTO `ma_system_crontab_log`
VALUES (111149897096499200, 2, 'return 123;', '123', 0, '0.017857', 1730538601);
INSERT INTO `ma_system_crontab_log`
VALUES (111149925072506880, 2, 'return 123;', '123', 0, '0.017802', 1730538607);
INSERT INTO `ma_system_crontab_log`
VALUES (111190211987378176, 2, 'return 123;', '123', 0, '0.061462', 1730548212);
INSERT INTO `ma_system_crontab_log`
VALUES (112338000423292928, 2, 'return 123;', '123', 0, '0.025085', 1730821866);
INSERT INTO `ma_system_crontab_log`
VALUES (112338112256020480, 8, 'http://www.baidu.com', 'Class \"GuzzleHttp\\Client\" not found', 1, '0.009472',
        1730821893);
INSERT INTO `ma_system_crontab_log`
VALUES (112338136092250112, 2, 'return 123;', '123', 0, '0.009036', 1730821899);
INSERT INTO `ma_system_crontab_log`
VALUES (112338347338371072, 8, 'http://www.baidu.com', 'Class \"GuzzleHttp\\Client\" not found', 1, '0.009893',
        1730821949);
INSERT INTO `ma_system_crontab_log`
VALUES (112338366669918208, 2, 'return 123;', '123', 0, '0.014634', 1730821954);
INSERT INTO `ma_system_crontab_log`
VALUES (112338748666155008, 2, 'return 123;', '123', 0, '0.010351', 1730822045);
INSERT INTO `ma_system_crontab_log`
VALUES (112338926714359808, 8, 'http://www.baidu.com', 'Class \"GuzzleHttp\\Client\" not found', 1, '0.011564',
        1730822087);
INSERT INTO `ma_system_crontab_log`
VALUES (112572360514736128, 2, 'return 123;', '123', 0, '0.009097', 1730877742);
INSERT INTO `ma_system_crontab_log`
VALUES (112573966157221888, 8, 'http://www.baidu.com', 'Class \"GuzzleHttp\\Client\" not found', 1, '0.010504',
        1730878125);
INSERT INTO `ma_system_crontab_log`
VALUES (112832958330703872, 8, 'http://www.baidu.com', 'Class \"GuzzleHttp\\Client\" not found', 1, '0.029228',
        1730939874);
INSERT INTO `ma_system_crontab_log`
VALUES (113559363318321152, 8, 'http://www.baidu.com', 'Class \"GuzzleHttp\\Client\" not found', 1, '0.018335',
        1731113062);
INSERT INTO `ma_system_crontab_log`
VALUES (113574386786766848, 2, 'return 123;', '123', 0, '0.034394', 1731116644);
INSERT INTO `ma_system_crontab_log`
VALUES (113629900933566464, 8, 'http://www.baidu.com', 'Class \"GuzzleHttp\\Client\" not found', 1, '0.022526',
        1731129880);
INSERT INTO `ma_system_crontab_log`
VALUES (113657444739190784, 8, 'http://www.baidu.com', 'Class \"GuzzleHttp\\Client\" not found', 1, '0.015836',
        1731136446);
INSERT INTO `ma_system_crontab_log`
VALUES (113657507351760896, 3, 'return 888;', '888', 0, '0.040985', 1731136461);

-- ----------------------------
-- Table structure for ma_system_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dept`;
CREATE TABLE `ma_system_dept`
(
    `id`             bigint(20) UNSIGNED NOT NULL COMMENT '主键',
    `pid`            bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '父ID',
    `level`          varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '组级集合',
    `code`           varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '部门唯一编码',
    `name`           varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '部门名称',
    `main_leader_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '负责人',
    `phone`          varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '联系电话',
    `enabled`        smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
    `sort`           smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
    `created_by`     bigint(20) NULL DEFAULT NULL COMMENT '创建者',
    `updated_by`     bigint(20) NULL DEFAULT NULL COMMENT '更新者',
    `create_time`    int(11) NULL DEFAULT NULL COMMENT '创建时间',
    `update_time`    int(11) NULL DEFAULT NULL COMMENT '修改时间',
    `delete_time`    int(11) NULL DEFAULT NULL COMMENT '删除时间',
    `remark`         longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX            `parent_id`(`pid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_dept
-- ----------------------------
INSERT INTO `ma_system_dept`
VALUES (2, 0, '0,1', '00002', '上海分公司', '109227769325555712', NULL, 1, 1, 1, 1, 1721640326, 1730080847, NULL, NULL);
INSERT INTO `ma_system_dept`
VALUES (3, 1, '0,1', '00001', '厦门总公司', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723247885, NULL, NULL);
INSERT INTO `ma_system_dept`
VALUES (4, 2, '0,1,2', '00003', '市场部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219992, NULL, NULL);
INSERT INTO `ma_system_dept`
VALUES (5, 2, '0,1,2', '00004', '财务部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219992, NULL, NULL);
INSERT INTO `ma_system_dept`
VALUES (6, 3, '0,1,3', '00005', '研发部门', NULL, NULL, 1, 1, 1, 1, 1721640326, 1723219253, NULL, NULL);

-- ----------------------------
-- Table structure for ma_system_dept_leader
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dept_leader`;
CREATE TABLE `ma_system_dept_leader`
(
    `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '部门主键',
    `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '部门领导关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_dept_leader
-- ----------------------------
INSERT INTO `ma_system_dept_leader`
VALUES (7, 73421010136862720);
INSERT INTO `ma_system_dept_leader`
VALUES (2, 73421384377831424);
INSERT INTO `ma_system_dept_leader`
VALUES (2, 73421690444582912);

-- ----------------------------
-- Table structure for ma_system_dict
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dict`;
CREATE TABLE `ma_system_dict`
(
    `id`          bigint(20) UNSIGNED NOT NULL COMMENT '主键',
    `group_code`  varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典类型',
    `name`        varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典名称',
    `code`        varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标示',
    `sort`        bigint(20) NULL DEFAULT NULL COMMENT '排序',
    `data_type`   smallint(6) NULL DEFAULT 1 COMMENT '数据类型',
    `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '描述',
    `enabled`     smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
    `created_by`  int(11) NULL DEFAULT NULL COMMENT '创建者',
    `updated_by`  int(11) NULL DEFAULT NULL COMMENT '更新者',
    `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
    `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典类型表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_dict
-- ----------------------------
INSERT INTO `ma_system_dict`
VALUES (108318632626491392, 'default', '字典类型', 'sys_dict_data_type', NULL, 2, NULL, 1, 1, 1, 1729863574, 1729863574,
        NULL);
INSERT INTO `ma_system_dict`
VALUES (108318797395529728, 'default', '是否', 'yes_no', NULL, 2, NULL, 1, 1, 1, 1729863613, 1729863613, NULL);
INSERT INTO `ma_system_dict`
VALUES (108327199312056320, 'default', '性别', 'sex', NULL, 2, NULL, 1, 1, 1, 1729865616, 1729865616, NULL);
INSERT INTO `ma_system_dict`
VALUES (108327562568142848, 'default', '菜单类型', 'sys_menu_type', NULL, 2, NULL, 1, 1, 1, 1729865703, 1729865703,
        NULL);
INSERT INTO `ma_system_dict`
VALUES (108328403966496768, 'default', '菜单打开类型', 'sys_menu_open_type', NULL, 2, NULL, 1, 1, 1, 1729865904,
        1729865904, NULL);
INSERT INTO `ma_system_dict`
VALUES (108329631148544000, 'default', '是否超级管理员', 'sys_user_admin_type', NULL, 2, NULL, 1, 1, 1, 1729866196,
        1729866196, NULL);
INSERT INTO `ma_system_dict`
VALUES (108339891250794496, 'default', '所属分组', 'sys_dict_group_code', NULL, 1, NULL, 1, 1, 1, 1729868642,
        1729868642, NULL);
INSERT INTO `ma_system_dict`
VALUES (108352455238094848, 'default', '角色类型', 'sys_role_role_type', NULL, 2, NULL, 1, 1, 1, 1729871638, 1729871638,
        NULL);
INSERT INTO `ma_system_dict`
VALUES (108523964137082880, 'default', '请求类型', 'request_mode', NULL, 1, NULL, 1, 1, 1, 1729912529, 1729912529,
        NULL);
INSERT INTO `ma_system_dict`
VALUES (112148157206499328, 'default', '定时任务模式', 'monitor_crontab_mode', NULL, 2, NULL, 1, 1, 1, 1730776604,
        1730787558, NULL);
INSERT INTO `ma_system_dict`
VALUES (112193372432764928, 'default', '定时任务类型', 'monitor_crontab_type', NULL, 2, NULL, 1, 1, 1, 1730787384,
        1730787508, NULL);
INSERT INTO `ma_system_dict`
VALUES (112197326231179264, 'default', '定时任务执行周期', 'monitor_crontab_cycle', NULL, 2, NULL, 1, 1, 1, 1730788327,
        1730788327, NULL);
INSERT INTO `ma_system_dict`
VALUES (112667401899872256, 'default', 'CPU监控', 'monitor_server_cpu', NULL, 1, NULL, 1, 1, 1, 1730900401, 1730900401,
        NULL);
INSERT INTO `ma_system_dict`
VALUES (112699203632893952, 'default', '内存监控', 'monitor_server_memory', NULL, 1, NULL, 1, 1, 1, 1730907983,
        1730907983, NULL);
INSERT INTO `ma_system_dict`
VALUES (113231428107505664, 'default', '配置分组', 'sys_group', NULL, 1, NULL, 1, 1, 1, 1731034876, 1731034898, NULL);

-- ----------------------------
-- Table structure for ma_system_dict_item
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_dict_item`;
CREATE TABLE `ma_system_dict_item`
(
    `id`          bigint(20) UNSIGNED NOT NULL COMMENT '主键',
    `dict_id`     bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '字典类型ID',
    `label`       varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标签',
    `value`       varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典值',
    `code`        varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字典标示',
    `sort`        smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
    `enabled`     smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
    `created_by`  int(11) NULL DEFAULT NULL COMMENT '创建者',
    `updated_by`  int(11) NULL DEFAULT NULL COMMENT '更新者',
    `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
    `remark`      longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
    `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX         `dict_id`(`dict_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '字典数据表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_dict_item
-- ----------------------------
INSERT INTO `ma_system_dict_item`
VALUES (108322740880150528, 108339891250794496, '默认分组', 'default', 'sys_dict_group_code', 1, 1, 1, 1, 1729864553,
        1729864553, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108323904434606080, 108339891250794496, '其他', 'other', 'sys_dict_group_code', 2, 1, 1, 1, 1729864831,
        1729864831, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108325217297895424, 108318797395529728, '是', '1', 'yes_no', 0, 1, 1, 1, 1729865144, 1729865144, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108325255969378304, 108318797395529728, '否', '0', 'yes_no', 0, 1, 1, 1, 1729865153, 1729865153, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108326613028048896, 108318632626491392, '字符串', '1', 'sys_dict_data_type', 1, 1, 1, 1, 1729865477, 1729865477,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108326693793566720, 108318632626491392, '整型', '2', 'sys_dict_data_type', 0, 1, 1, 1, 1729865496, 1729865496,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108327272594935808, 108327199312056320, '男', '1', 'sex', 0, 1, 1, 1, 1729865634, 1729865634, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108327327527735296, 108327199312056320, '女', '2', 'sex', 0, 1, 1, 1, 1729865647, 1729865647, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108327812343140352, 108327562568142848, '目录', '1', 'sys_menu_type', 0, 1, 1, 1, 1729865763, 1729865763, NULL,
        NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108327862620262400, 108327562568142848, '菜单', '2', 'sys_menu_type', 0, 1, 1, 1, 1729865775, 1729865775, NULL,
        NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108327897684643840, 108327562568142848, '按钮', '3', 'sys_menu_type', 0, 1, 1, 1, 1729865783, 1729865783, NULL,
        NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108327956966936576, 108327562568142848, '接口', '4', 'sys_menu_type', 0, 1, 1, 1, 1729865797, 1729865797, NULL,
        NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108329132017979392, 108328403966496768, '无', '0', 'sys_menu_open_type', 1, 1, 1, 1, 1729866077, 1729866126,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108329204327780352, 108328403966496768, '组件', '1', 'sys_menu_open_type', 2, 1, 1, 1, 1729866094, 1729866134,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108329252830711808, 108328403966496768, '内链', '2', 'sys_menu_open_type', 3, 1, 1, 1, 1729866106, 1729866146,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108329303904751616, 108328403966496768, '外链', '3', 'sys_menu_open_type', 4, 1, 1, 1, 1729866118, 1729866152,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108329785582817280, 108329631148544000, '超级管理员', '1', 'sys_user_admin_type', 1, 1, 1, 1, 1729866233,
        1729866233, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108329829593649152, 108329631148544000, '普通管理员', '2', 'sys_user_admin_type', 2, 1, 1, 1, 1729866244,
        1729866244, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108353068097212416, 108352455238094848, '普通角色', '1', 'sys_role_role_type', 1, 1, 1, 1, 1729871784,
        1729871784, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108353350323539968, 108352455238094848, '数据角色', '2', 'sys_role_role_type', 2, 1, 1, 1, 1729871851,
        1729871851, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108524077190352896, 108523964137082880, 'GET', 'GET', 'request_mode', 1, 1, 1, 1, 1729912556, 1729912556, NULL,
        NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108524119187918848, 108523964137082880, 'POST', 'POST', 'request_mode', 2, 1, 1, 1, 1729912566, 1729912566,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108524162666074112, 108523964137082880, 'PUT', 'PUT', 'request_mode', 3, 1, 1, 1, 1729912576, 1729912576, NULL,
        NULL);
INSERT INTO `ma_system_dict_item`
VALUES (108524230089510912, 108523964137082880, 'DELETE', 'DELETE', 'request_mode', 4, 1, 1, 1, 1729912592, 1729912592,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112149913353195520, 112148157206499328, '单次', '0', 'monitor_crontab_mode', 1, 1, 1, 1, 1730777022, 1730787566,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112150063618330624, 112148157206499328, '循环', '1', 'monitor_crontab_mode', 2, 1, 1, 1, 1730777058, 1730787569,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112193510920294400, 112193372432764928, 'url', '1', 'monitor_crontab_type', 1, 1, 1, 1, 1730787417, 1730787515,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112193612384702464, 112193372432764928, 'eval', '2', 'monitor_crontab_type', 2, 1, 1, 1, 1730787441, 1730787518,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112193667938258944, 112193372432764928, 'shell', '3', 'monitor_crontab_type', 3, 1, 1, 1, 1730787454,
        1730787522, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112197759649583104, 112197326231179264, '每天', '1', 'monitor_crontab_cycle', 1, 1, 1, 1, 1730788430,
        1730788430, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112197829258252288, 112197326231179264, '每小时', '2', 'monitor_crontab_cycle', 2, 1, 1, 1, 1730788447,
        1730788447, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112197879539568640, 112197326231179264, 'N小时', '3', 'monitor_crontab_cycle', 3, 1, 1, 1, 1730788459,
        1730788459, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112197957499097088, 112197326231179264, 'N分钟', '4', 'monitor_crontab_cycle', 4, 1, 1, 1, 1730788477,
        1730788477, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112198023311921152, 112197326231179264, 'N秒', '5', 'monitor_crontab_cycle', 5, 1, 1, 1, 1730788493, 1730788493,
        NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112198098184441856, 112197326231179264, '每星期', '6', 'monitor_crontab_cycle', 6, 1, 1, 1, 1730788511,
        1730788511, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112198148448980992, 112197326231179264, '每月', '7', 'monitor_crontab_cycle', 7, 1, 1, 1, 1730788523,
        1730788546, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112198205793505280, 112197326231179264, '每年', '8', 'monitor_crontab_cycle', 8, 1, 1, 1, 1730788536,
        1730788536, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112668208758132736, 112667401899872256, '型号', 'cpu_name', 'monitor_server_cpu', 1, 1, 1, 1, 1730900594,
        1730900594, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112668898339459072, 112667401899872256, '物理核心数', 'physical_cores', 'monitor_server_cpu', 2, 1, 1, 1,
        1730900758, 1730900758, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112668978320642048, 112667401899872256, '逻辑核心数', 'logical_cores', 'monitor_server_cpu', 3, 1, 1, 1,
        1730900777, 1730900777, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112669590324121600, 112667401899872256, '缓存大小 (MB)', 'cache_size_mb', 'monitor_server_cpu', 4, 1, 1, 1,
        1730900923, 1730900923, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112669695777312768, 112667401899872256, 'CPU 使用百分比', 'cpu_usage_percentage', 'monitor_server_cpu', 5, 1, 1,
        1, 1730900948, 1730900948, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112669783031418880, 112667401899872256, '空闲 CPU 百分比', 'free_cpu_percentage', 'monitor_server_cpu', 6, 1, 1,
        1, 1730900969, 1730900969, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112699384545808384, 112699203632893952, '总内存', 'total_memory', 'monitor_server_memory', 1, 1, 1, 1,
        1730908027, 1730908027, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112699464619266048, 112699203632893952, '可用内存', 'available_memory', 'monitor_server_memory', 2, 1, 1, 1,
        1730908046, 1730908046, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112699568285683712, 112699203632893952, '已用内存', 'used_memory', 'monitor_server_memory', 3, 1, 1, 1,
        1730908070, 1730908070, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112699637563002880, 112699203632893952, 'PHP内存使用', 'php_memory_usage', 'monitor_server_memory', 4, 1, 1, 1,
        1730908087, 1730908087, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (112699721931427840, 112699203632893952, '内存使用率', 'memory_usage_rate', 'monitor_server_memory', 0, 1, 1, 1,
        1730908107, 1730908107, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (113231766831108096, 113231428107505664, '系统配置', 'system_config', 'sys_group', 1, 1, 1, 1, 1731034956,
        1731034956, NULL, NULL);
INSERT INTO `ma_system_dict_item`
VALUES (113232347830292480, 113231428107505664, '上传配置', 'system_storage', 'sys_group', 2, 1, 1, 1, 1731035095,
        1731035095, NULL, NULL);

-- ----------------------------
-- Table structure for ma_system_login_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_login_log`;
CREATE TABLE `ma_system_login_log`
(
    `id`           bigint(20) UNSIGNED NOT NULL COMMENT '主键',
    `user_name`    varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户名',
    `ip`           varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '登录IP地址',
    `ip_location`  varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'IP所属地',
    `os`           varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '操作系统',
    `browser`      varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '浏览器',
    `status`       smallint(6) NULL DEFAULT 1 COMMENT '登录状态 (1成功 2失败)',
    `message`      varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '提示消息',
    `login_time`   int(11) NULL DEFAULT NULL COMMENT '登录时间',
    `key`          longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'key',
    `create_time`  int(11) NULL DEFAULT NULL COMMENT '创建时间',
    `expires_time` int(11) NULL DEFAULT NULL COMMENT '过期时间',
    `update_time`  int(11) NULL DEFAULT NULL COMMENT '更新时间',
    `delete_time`  datetime NULL DEFAULT NULL COMMENT '删除时间',
    `remark`       varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX          `username`(`user_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '登录日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_login_log
-- ----------------------------

-- ----------------------------
-- Table structure for ma_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_menu`;
CREATE TABLE `ma_system_menu`
(
    `id`          bigint(20) NOT NULL COMMENT '菜单ID',
    `pid`         bigint(20) NOT NULL DEFAULT 0 COMMENT '父ID',
    `app`         varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用编码',
    `title`       varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单名称',
    `code`        varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '唯一编码',
    `level`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '父ID集合',
    `type`        int(11) NULL DEFAULT NULL COMMENT '菜单类型1=>目录  2>菜单 3=>按钮 4=>接口',
    `sort`        bigint(20) NULL DEFAULT 999 COMMENT '排序',
    `path`        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '路由地址',
    `component`   varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组件地址',
    `redirect`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '重定向',
    `icon`        varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单图标',
    `is_show`     tinyint(1) NULL DEFAULT 1 COMMENT '是否显示 0=>否   1=>是',
    `is_link`     tinyint(1) NULL DEFAULT 0 COMMENT '是否外链 0=>否   1=>是',
    `link_url`    longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '外部链接地址',
    `enabled`     tinyint(1) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
    `open_type`   int(11) NULL DEFAULT 0 COMMENT '是否外链 1=>是    0=>否',
    `is_cache`    tinyint(1) NULL DEFAULT 0 COMMENT '是否缓存 1=>是    0=>否',
    `is_sync`     tinyint(1) NULL DEFAULT 1 COMMENT '是否同步',
    `is_affix`    tinyint(1) NULL DEFAULT 0 COMMENT '是否固定tags无法关闭',
    `variable`    varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '额外参数JSON',
    `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
    `create_by`   bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
    `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
    `update_by`   bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
    `delete_time` datetime NULL DEFAULT NULL COMMENT '是否删除',
    `methods`     varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'get' COMMENT '请求方法',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX         `idx_sys_menu_code`(`code`) USING BTREE,
    INDEX         `idx_sys_menu_app_code`(`app`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_menu
-- ----------------------------
INSERT INTO `ma_system_menu`
VALUES (109399693666160640, 1704792844212551689, 'admin', '详情', 'system:user:detail', NULL, 3, 40, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121319, NULL, 1730121355, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109399811517714432, 1704792844212551689, 'admin', '编辑', 'system:user:update', NULL, 3, 30, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121347, NULL, 1730121415, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109400032410734592, 1704792844212551689, 'admin', '重置密码', 'system:user:reset_password', NULL, 3, 50, NULL,
        NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121400, NULL, 1730121400, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109400289219579904, 1704792844212551689, 'admin', '添加', 'system:user:save', NULL, 3, 10, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121461, NULL, 1730121461, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109400534368260096, 1704792844212551689, 'admin', '删除', 'system:user:remove', NULL, 3, 20, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121519, NULL, 1730121519, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109400678908170240, 1704792844212551689, 'admin', '授权角色', 'system:user:grant_role', NULL, 3, 60, NULL, NULL,
        NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730121554, NULL, 1730121554, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109407841718243328, 1704792844212551689, 'admin', '冻结用户', 'system:user:locked', NULL, 3, 70, NULL, NULL,
        NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123262, NULL, 1730123314, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109408005879107584, 1704792844212551689, 'admin', '取消冻结', 'system:user:un_locked', NULL, 3, 80, NULL, NULL,
        NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123301, NULL, 1730123301, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109410079526227968, 1704792844212551686, 'admin', '添加', 'system:menu:save', NULL, 3, 10, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123795, NULL, 1730123795, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109410203694403584, 1704792844212551686, 'admin', '删除', 'system:menu:remove', NULL, 3, 20, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123825, NULL, 1730123825, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109410337828245504, 1704792844212551686, 'admin', '编辑', 'system:menu:update', NULL, 3, 30, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123857, NULL, 1730123857, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109410533710630912, 1704792844212551686, 'admin', '详情', 'system:menu:detail', NULL, 3, 40, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123903, NULL, 1730123903, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109410879493246976, 1704792844212551687, 'admin', '添加', 'system:dept:save', NULL, 3, 10, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730123986, NULL, 1730123986, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109410959407321088, 1704792844212551687, 'admin', '删除', 'system:dept:remove', NULL, 3, 20, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124005, NULL, 1730124005, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109411049895235584, 1704792844212551687, 'admin', '编辑', 'system:dept:update', NULL, 3, 30, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124027, NULL, 1730124027, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109411169378373632, 1704792844212551687, 'admin', '详情', 'system:dept:detail', NULL, 3, 40, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124055, NULL, 1730124055, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109411405593186304, 1704792844212551688, 'admin', '添加', 'system:post:save', NULL, 3, 10, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124111, NULL, 1730124111, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109411475520622592, 1704792844212551688, 'admin', '删除', 'system:post:remove', NULL, 3, 20, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124128, NULL, 1730124128, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109411563806527488, 1704792844212551688, 'admin', '编辑', 'system:post:update', NULL, 3, 30, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124149, NULL, 1730124149, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109411800763731968, 1704792844212551688, 'admin', '详情', 'system:post:detail', NULL, 3, 40, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124206, NULL, 1730124221, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109412207485390848, 1704792844212551690, 'admin', '添加', 'system:role:save', NULL, 3, 10, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124303, NULL, 1730124303, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109412280751493120, 1704792844212551690, 'admin', '删除', 'system:role:remove', NULL, 3, 20, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124320, NULL, 1730124320, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109412429192105984, 1704792844212551690, 'admin', '编辑', 'system:role:update', NULL, 3, 30, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124355, NULL, 1730124355, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109412494933626880, 1704792844212551690, 'admin', '详情', 'system:role:detail', NULL, 3, 40, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124371, NULL, 1730124371, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109413386634268672, 1704792844212551690, 'admin', '授权', 'system:rbac:save_role_menu', NULL, 3, 50, NULL, NULL,
        NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124584, NULL, 1730124584, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109413560358146048, 1704792844212551690, 'admin', '用户', 'system:rbac:user_list_by_role_id', NULL, 3, 60, NULL,
        NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124625, NULL, 1730124625, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109414087879954432, 1704792844212551690, 'admin', '移除用户', 'system:rbac:remove_user_role', NULL, 3, 70, NULL,
        NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730124751, NULL, 1730124751, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109415471077527552, 1704792844212551691, 'admin', '新增', 'system:dict:save', NULL, 3, 10, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125081, NULL, 1730125081, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109415539092361216, 1704792844212551691, 'admin', '删除', 'system:dict:remove', NULL, 3, 20, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125097, NULL, 1730125097, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109415607354658816, 1704792844212551691, 'admin', '编辑', 'system:dict:update', NULL, 3, 30, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125113, NULL, 1730125113, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109415680117444608, 1704792844212551691, 'admin', '详情', 'system:dict:detail', NULL, 3, 40, NULL, NULL, NULL,
        NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125130, NULL, 1730125130, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109415924959940608, 1704792844212551691, 'admin', '字典项列表', 'system:dict-item:list', NULL, 3, 50, NULL,
        NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125189, NULL, 1730125428, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109416658409492480, 1704792844212551691, 'admin', '字典项添加', 'system:dict_item:save', NULL, 3, 60, NULL,
        NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125364, NULL, 1730125364, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109416733357510656, 1704792844212551691, 'admin', '字典项删除', 'system:dict_item:remove', NULL, 3, 70, NULL,
        NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125382, NULL, 1730125382, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (109416801456230400, 1704792844212551691, 'admin', '字典项编辑', 'system:dict_item:update', NULL, 3, 80, NULL,
        NULL, NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1730125398, NULL, 1730125398, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (111819772848640000, 0, 'admin', '系统监控', 'monitors', NULL, 1, 999, '/monitors', 'BasicLayout', NULL,
        'ant-design:video-camera-filled', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1730698311, NULL, 1730698373, NULL, NULL,
        'GET');
INSERT INTO `ma_system_menu`
VALUES (111822475028992000, 111819772848640000, 'admin', 'Redis监控', 'monitors:redis', NULL, 2, 999, '/monitors/redis',
        '/monitor/redis/index', NULL, 'ant-design:trademark-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1730698955,
        NULL, 1730705914, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (111822901178667008, 111819772848640000, 'admin', '性能监控', 'monitors:monitor:server', NULL, 2, 999,
        '/monitors/server', '/monitor/server/index', NULL, 'ant-design:line-chart-outlined', 1, 0, NULL, 1, 0, 0, 1, 0,
        NULL, 1730699057, NULL, 1730699352, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (111826765697126400, 111819772848640000, 'admin', '登录日志', 'system:logs:login', NULL, 2, 999,
        '/monitor/logs/login', '/system/logs/login/index', NULL, 'ant-design:credit-card-filled', 1, 0, NULL, 1, 0, 0,
        1, 0, NULL, 1730699978, NULL, 1730707305, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (111827149941510144, 111819772848640000, 'admin', '操作日志', 'monitor:logs:operate', NULL, 2, 999,
        '/monitor/logs/operate', '/system/logs/operate/index', NULL, 'ant-design:schedule-filled', 1, 0, NULL, 1, 0, 0,
        1, 0, NULL, 1730700070, NULL, 1730707283, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (111960469564166144, 111819772848640000, NULL, '定时任务', 'monitor:crontab', NULL, 2, 999, '/monitor/crontab',
        '/monitor/crontab/index', NULL, 'ant-design:dashboard-filled', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1730731856,
        NULL, 1730774898, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (112877567396155392, 1704792844212551684, NULL, '附件管理', 'system:files', NULL, 2, 999, '/system/files',
        '/system/files/index', NULL, 'ant-design:folder-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1730950509, NULL,
        1730963813, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (114491196964999168, 1704792844212551684, NULL, '系统参数', 'system:config', NULL, 2, 999999, '/systen/config',
        '/system/config/index', NULL, 'ant-design:tool-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1731335228, NULL,
        1731833648, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (238042663444815872, 1704792844212551684, NULL, '数据回收', 'system:recycle_bin', NULL, 2, 999999999,
        '/system/recycle-bin', '/system/recycle-bin/index', NULL, 'ant-design:rest-filled', 1, 0, NULL, 1, 0, 0, 1, 0,
        NULL, 1732415296, NULL, 1732415931, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (238044307846864896, 238042663444815872, NULL, '恢复', 'system:recycle_bin:recover', NULL, 3, 999, NULL, NULL,
        NULL, NULL, 1, 0, NULL, 1, 1, 0, 1, 0, NULL, 1732415492, NULL, 1732415670, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (238044685007069184, 238042663444815872, NULL, '删除', 'system:recycle_bin:remove', NULL, 3, 999, NULL, NULL,
        NULL, NULL, 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1732415537, NULL, 1732415588, NULL, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551681, 0, 'admin', '首页', 'Dashboard', NULL, 1, -1, '/', 'BasicLayout', '/analytics',
        'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622, 1567738052492341249, 1722348622,
        1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551682, 1704792844212551681, 'admin', '分析页', 'Analytics', NULL, 1, -1, '/analytics',
        '/dashboard/analytics/index', NULL, 'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 1, NULL, 1722348622,
        1567738052492341249, 1722348622, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551683, 1704792844212551681, 'admin', '工作台', 'Workspace', NULL, 1, -1, '/workspace',
        '/dashboard/workspace/index', NULL, 'ant-design:home-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622,
        1567738052492341249, 1722348622, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551684, 0, 'admin', '系统设置', 'system', NULL, 1, 1000, '/system', 'BasicLayout', '',
        'ant-design:setting-outlined', 1, 0, NULL, 1, 0, 1, 1, 0, NULL, 1722348622, 1567738052492341249, 1729910254,
        1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551686, 1704792844212551684, 'admin', '菜单管理', 'system:menu', NULL, 2, 1000, '/system/menu',
        '/system/menu/index', NULL, 'ant-design:menu-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622,
        1567738052492341249, 1722653097, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551687, 1704792844212551684, 'admin', '部门管理', 'system:dept', NULL, 2, 1000, '/system/dept',
        '/system/dept/index', NULL, 'ant-design:facebook-filled', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622,
        1567738052492341249, 1730123948, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551688, 1704792844212551684, 'admin', '职位管理', 'system:post', NULL, 2, 1000, '/system/post',
        '/system/post/index', NULL, 'ant-design:database-filled', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622,
        1567738052492341249, 1723265516, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551689, 1704792844212551684, 'admin', '用户管理', 'system:user', NULL, 2, 1000, '/system/user',
        '/system/user/index', NULL, 'ant-design:user-add-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622,
        1567738052492341249, 1722650392, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551690, 1704792844212551684, 'admin', '角色管理', 'system:role', NULL, 2, 1000, '/system/role',
        '/system/role/index', NULL, 'ant-design:team-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1722348622,
        1567738052492341249, 1730124265, 1567738052492341249, NULL, 'GET');
INSERT INTO `ma_system_menu`
VALUES (1704792844212551691, 1704792844212551684, 'admin', '数据字典', 'system:dict', NULL, 2, 1000, '/system/dict',
        '/system/dict/index', NULL, 'ant-design:profile-outlined', 1, 0, NULL, 1, 0, 0, 1, 0, NULL, 1723993192, NULL,
        1724934955, NULL, NULL, 'GET');

-- ----------------------------
-- Table structure for ma_system_operate_log
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_operate_log`;
CREATE TABLE `ma_system_operate_log`
(
    `id`          bigint(20) NOT NULL COMMENT '主键',
    `name`        varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '内容',
    `app`         varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用名称',
    `ip`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求ip',
    `ip_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求ip归属地',
    `browser`     varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '浏览器',
    `os`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作系统',
    `url`         varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求地址',
    `class_name`  varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类名称',
    `action`      varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '方法名称',
    `method`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求方式（GET POST PUT DELETE)',
    `param`       longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求参数',
    `result`      longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '返回结果',
    `create_time` int(11) NULL DEFAULT NULL COMMENT '操作时间',
    `user_name`   varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作账号',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统操作日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_operate_log
-- ----------------------------

-- ----------------------------
-- Table structure for ma_system_post
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_post`;
CREATE TABLE `ma_system_post`
(
    `id`          bigint(20) UNSIGNED NOT NULL COMMENT '主键',
    `code`        varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '岗位代码',
    `name`        varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL COMMENT '岗位名称',
    `sort`        smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
    `enabled`     smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
    `created_by`  bigint(20) NULL DEFAULT NULL COMMENT '创建者',
    `updated_by`  bigint(20) NULL DEFAULT NULL COMMENT '更新者',
    `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
    `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
    `remark`      varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '岗位信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_post
-- ----------------------------
INSERT INTO `ma_system_post`
VALUES (108515985312583680, '0002', '业务员', 0, 1, 1, 1, 1729910627, 1729910627, NULL, NULL);
INSERT INTO `ma_system_post`
VALUES (108516334249316352, '0003', '采购员', 0, 1, 1, 1, 1729910710, 1729910710, NULL, NULL);

-- ----------------------------
-- Table structure for ma_system_recycle_bin
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_recycle_bin`;
CREATE TABLE `ma_system_recycle_bin`
(
    `id`           bigint(20) UNSIGNED NOT NULL COMMENT 'ID',
    `data`         longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '回收的数据',
    `table_name`   varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '数据表',
    `table_prefix` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '表前缀',
    `enabled`      tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已还原',
    `ip`           varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '操作者IP',
    `operate_id`   bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作管理员',
    `create_time`  bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `update_time`  bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '数据回收记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_recycle_bin
-- ----------------------------
INSERT INTO `ma_system_recycle_bin`
VALUES (238117280163307520,
        '{\"id\":238115444610048000,\"code\":\"qqe\",\"name\":\"qweq\",\"sort\":11,\"enabled\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":1732423972,\"update_time\":1732423972,\"delete_time\":null,\"remark\":\"1\"}',
        'system_post', 'ma_', 0, '127.0.0.1', 1, 1732424191);
INSERT INTO `ma_system_recycle_bin`
VALUES (238117416654348288,
        '{\"id\":7,\"pid\":3,\"level\":\"0,1,3\",\"code\":\"00006\",\"name\":\"\\u5e02\\u573a\\u90e8\\u95e8\",\"main_leader_id\":\"81896469954695168\",\"phone\":null,\"enabled\":1,\"sort\":1,\"created_by\":1,\"updated_by\":1,\"create_time\":1721640326,\"update_time\":1730035124,\"delete_time\":null,\"remark\":null,\"leader_id_list\":[]}',
        'system_dept', 'ma_', 0, '127.0.0.1', 1, 1732424208);

-- ----------------------------
-- Table structure for ma_system_role
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_role`;
CREATE TABLE `ma_system_role`
(
    `id`             bigint(20) UNSIGNED NOT NULL COMMENT '主键',
    `pid`            bigint(20) NULL DEFAULT 0 COMMENT '父级id',
    `name`           varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '角色名称',
    `code`           varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '角色代码',
    `is_super_admin` tinyint(1) NULL DEFAULT 0 COMMENT '是否超级管理员 1=是   0=否',
    `role_type`      tinyint(4) NULL DEFAULT NULL COMMENT '角色类型',
    `data_scope`     smallint(6) NULL DEFAULT 1 COMMENT '数据范围(1:全部数据权限 2:自定义数据权限 3:本部门数据权限 4:本部门及以下数据权限 5:本人数据权限)',
    `enabled`        smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
    `sort`           smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
    `remark`         varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
    `created_by`     bigint(20) NULL DEFAULT NULL COMMENT '创建者',
    `updated_by`     bigint(20) NULL DEFAULT NULL COMMENT '更新者',
    `create_time`    int(11) NULL DEFAULT NULL COMMENT '创建时间',
    `update_time`    int(11) NULL DEFAULT NULL COMMENT '修改时间',
    `delete_time`    int(11) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_role
-- ----------------------------
INSERT INTO `ma_system_role`
VALUES (108354281047986176, 0, '超级管理员', 'superAdmin', 1, 1, 1, 1, 0, '12', 1, 1, 1729872073, 1730097871, NULL);
INSERT INTO `ma_system_role`
VALUES (108506227927027712, 0, '普通管理员', 'ss', 0, 1, 1, 1, 0, NULL, 1, 1, 1729908300, 1730037190, NULL);
INSERT INTO `ma_system_role`
VALUES (232632128901488640, 0, '测试1号', 'test', 0, 1, 1, 1, 0, NULL, 1, 1, 1731770310, 1731770310, NULL);
INSERT INTO `ma_system_role`
VALUES (232632220630917120, 0, '测试2', 'tests', 0, 1, 1, 1, 0, NULL, 1, 1, 1731770321, 1731770321, NULL);
INSERT INTO `ma_system_role`
VALUES (232632352457891840, 0, '测试3号', 'tesss', 0, 1, 1, 1, 0, NULL, 1, 1, 1731770337, 1731770337, NULL);

-- ----------------------------
-- Table structure for ma_system_role_dept
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_role_dept`;
CREATE TABLE `ma_system_role_dept`
(
    `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
    `dept_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
    PRIMARY KEY (`role_id`, `dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与部门关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_role_dept
-- ----------------------------
INSERT INTO `ma_system_role_dept`
VALUES (2, 2);
INSERT INTO `ma_system_role_dept`
VALUES (2, 4);
INSERT INTO `ma_system_role_dept`
VALUES (2, 5);

-- ----------------------------
-- Table structure for ma_system_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_role_menu`;
CREATE TABLE `ma_system_role_menu`
(
    `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键',
    `menu_id` bigint(20) UNSIGNED NOT NULL COMMENT '菜单主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色与菜单关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_role_menu
-- ----------------------------
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551681);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551682);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551683);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551684);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551686);
INSERT INTO `ma_system_role_menu`
VALUES (5, 1704792844212551690);
INSERT INTO `ma_system_role_menu`
VALUES (5, 1704792844212551691);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551681);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551682);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551684);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551686);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551687);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551688);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551689);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551690);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551691);
INSERT INTO `ma_system_role_menu`
VALUES (108354281047986176, 1704792844212551683);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551690);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551691);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551687);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551688);
INSERT INTO `ma_system_role_menu`
VALUES (108506227927027712, 1704792844212551689);

-- ----------------------------
-- Table structure for ma_system_upload
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_upload`;
CREATE TABLE `ma_system_upload`
(
    `id`                bigint(20) NOT NULL COMMENT '文件信息ID',
    `url`               longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文件访问地址',
    `size`              bigint(20) NULL DEFAULT NULL COMMENT '文件大小，单位字节',
    `size_info`         varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件大小，有单位',
    `hash`              varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件hash',
    `filename`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件名称',
    `original_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '原始文件名',
    `base_path`         varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '基础存储路径',
    `path`              varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '存储路径',
    `ext`               varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件扩展名',
    `content_type`      varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'MIME类型',
    `platform`          varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '存储平台',
    `th_url`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图访问路径',
    `th_filename`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图大小，单位字节',
    `th_size`           bigint(20) NULL DEFAULT NULL COMMENT '缩略图大小，单位字节',
    `th_size_info`      varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图大小，有单位',
    `th_content_type`   varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图MIME类型',
    `object_id`         varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件所属对象id',
    `object_type`       varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件所属对象类型，例如用户头像，评价图片',
    `attr`              text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '附加属性',
    `create_time`       bigint(20) NULL DEFAULT NULL COMMENT '创建时间',
    `created_by`        bigint(20) NULL DEFAULT NULL COMMENT '创建用户',
    `update_time`       bigint(20) NULL DEFAULT NULL COMMENT '更新时间',
    `updated_by`        bigint(20) NULL DEFAULT NULL COMMENT '更新用户',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_upload
-- ----------------------------
INSERT INTO `ma_system_upload`
VALUES (1, '127.0.0.1:8787', 111, '1212', '1212', '20220222161910_3802.jpg', '3802', 'public',
        'public/upload/20220222161910_3802.jpg', 'png', 'image', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1729935438, 1, 1729935438, 1);
INSERT INTO `ma_system_upload`
VALUES (3, '127.0.0.1:8787', 111, '1212', '1212', '20220322174230_6546.jpg', '6546', 'public',
        'public/upload/20220322174230_6546.jpg', 'png', 'image', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1729935438, 1, 1729935438, 1);
INSERT INTO `ma_system_upload`
VALUES (4, '127.0.0.1:8787', 111, '1212', '1212', '20220322174259_4085.jpg', '4085', 'public',
        'public/upload/20220322174259_4085.jpg', 'png', 'image', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1729935438, 1, 1729935438, 1);
INSERT INTO `ma_system_upload`
VALUES (232552154622337024, 'http://127.0.0.1:8899/upload/2c3402afdd52639f8a7fff6ef397e6b9.png', 514, '514 B',
        '2c3402afdd52639f8a7fff6ef397e6b9', 'D:/MyMotion/MaDong/public/upload/2c3402afdd52639f8a7fff6ef397e6b9.png',
        'favicon copy.png', 'D:/MyMotion/MaDong/public/upload/2c3402afdd52639f8a7fff6ef397e6b', NULL, 'png',
        'image/png', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1731760777, 1, 1731760777, 1);
INSERT INTO `ma_system_upload`
VALUES (232572539929632768, 'http://127.0.0.1:8899/upload/6fb703e7699c1112d4eb729dc9aeb3d5.jpg', 58313, '56.95 KB',
        '6fb703e7699c1112d4eb729dc9aeb3d5', 'D:/MyMotion/MaDong/public/upload/6fb703e7699c1112d4eb729dc9aeb3d5.jpg',
        'b1103ccb470d7dc1a6c525d907e640a.jpg', 'D:/MyMotion/MaDong/public/upload/6fb703e7699c1112d4eb729dc9aeb3d', NULL,
        'jpg', 'image/jpeg', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1731763207, 1, 1731763207, 1);
INSERT INTO `ma_system_upload`
VALUES (232574886810492928, 'http://127.0.0.1:8899/upload/a837f17870bdf2cc22f41fdf307d54ec.jpg', 89872, '87.77 KB',
        'a837f17870bdf2cc22f41fdf307d54ec', 'D:/MyMotion/MaDong/public/upload/a837f17870bdf2cc22f41fdf307d54ec.jpg',
        '1730560210332.jpg', 'D:/MyMotion/MaDong/public/upload/a837f17870bdf2cc22f41fdf307d54e', NULL, 'jpg',
        'image/jpeg', 'local', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1731763487, 1, 1731763487, 1);

-- ----------------------------
-- Table structure for ma_system_user
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user`;
CREATE TABLE `ma_system_user`
(
    `id`              bigint(20) UNSIGNED NOT NULL COMMENT '用户ID,主键',
    `user_name`       varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL COMMENT '账号',
    `real_name`       varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户',
    `nick_name`       varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '昵称',
    `password`        varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
    `is_super`        tinyint(4) NULL DEFAULT 2 COMMENT '用户类型:(1系统用户 2普通用户)',
    `mobile_phone`    varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '手机',
    `email`           varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户邮箱',
    `avatar`          varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户头像',
    `signed`          varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '个人签名',
    `dashboard`       varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '后台首页类型',
    `dept_id`         bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '部门ID',
    `enabled`         smallint(6) NULL DEFAULT 1 COMMENT '状态 (1正常 0停用)',
    `login_ip`        varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '最后登陆IP',
    `login_time`      int(11) NULL DEFAULT NULL COMMENT '最后登陆时间',
    `backend_setting` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '后台设置数据',
    `created_by`      bigint(20) NULL DEFAULT NULL COMMENT '创建者',
    `updated_by`      bigint(20) NULL DEFAULT NULL COMMENT '更新者',
    `create_time`     int(11) NULL DEFAULT NULL COMMENT '创建时间',
    `update_time`     int(11) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    `delete_time`     int(11) NULL DEFAULT NULL COMMENT '删除时间',
    `sex`             tinyint(1) NULL DEFAULT 0 COMMENT '0=未知  1=男 2=女',
    `remark`          longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '备注',
    `birthday`        varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '生日',
    `tel`             varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '座机',
    `is_locked`       smallint(6) NULL DEFAULT 0 COMMENT '是否锁定（1是 0否）',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `username`(`user_name`) USING BTREE,
    INDEX             `dept_id`(`dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_user
-- ----------------------------
INSERT INTO `ma_system_user`
VALUES (1, 'admin', '超级管理员', '', '$2y$10$X1CoPxnqOZPIuyOk/RSXoOIVxflBZVUyqF/8fYOKzvn2hk0VGU52C', 1, '18888888888',
        'admin@admin.com', '', 'Today is very good！', 'statistics', 4, 1, '127.0.0.1', 1732432145,
        '{\"mode\":\"light\",\"tag\":true,\"menuCollapse\":false,\"menuWidth\":230,\"layout\":\"classic\",\"skin\":\"mine\",\"i18n\":true,\"language\":\"zh_CN\",\"animation\":\"ma-slide-down\",\"color\":\"#165DFF\"}',
        1, 1, NULL, 1732432145, NULL, 1, NULL, '2024-08-15 23:52:01', NULL, 0);
INSERT INTO `ma_system_user`
VALUES (73421384377831424, '12对对对4', '测试用户2', NULL,
        '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, NULL, NULL, NULL, NULL, NULL, 4, 1, NULL,
        NULL, NULL, NULL, 1, 1721543422, 1730110040, NULL, 1, NULL, '2024-08-11', NULL, 0);
INSERT INTO `ma_system_user`
VALUES (73421690444582912, '12对对对45', '测试用户3', NULL,
        '$2y$10$6JMairFZ.P.lD1RhTIEHYOxwZqUWMKW1dDlfMA1NauQZQcUBOo/uu', 2, NULL, NULL, NULL, NULL, NULL, 5, 1, NULL,
        NULL, NULL, NULL, 1, 1721543495, 1730119816, NULL, 1, NULL, '2024-08-11', NULL, 0);
INSERT INTO `ma_system_user`
VALUES (73421839434649600, '12对对对45f', '测试用户4', NULL,
        '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2, NULL, NULL, NULL, NULL, NULL, 6, 1, NULL,
        NULL, NULL, NULL, 1, 1721543530, 1730096125, NULL, 1, NULL, '2024-08-11', NULL, 0);
INSERT INTO `ma_system_user`
VALUES (73422563472183296, '1288', '测试用户5', NULL, '$2y$10$Q70WC9RBqMSS72DmppsbIuQtyAydXSmeD.Ae6W8YhmE/w15uLLpiy', 2,
        NULL, NULL, NULL, NULL, NULL, 7, 1, NULL, NULL, NULL, NULL, 1, 1721543703, 1732423903, 1732423903, 1, NULL,
        '2024-08-11', NULL, 0);

-- ----------------------------
-- Table structure for ma_system_user_post
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user_post`;
CREATE TABLE `ma_system_user_post`
(
    `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
    `post_id` bigint(20) UNSIGNED NOT NULL COMMENT '岗位主键',
    PRIMARY KEY (`user_id`, `post_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与岗位关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_user_post
-- ----------------------------
INSERT INTO `ma_system_user_post`
VALUES (81896469954695168, 108515861576421376);
INSERT INTO `ma_system_user_post`
VALUES (108629512094355456, 108515985312583680);
INSERT INTO `ma_system_user_post`
VALUES (108667463734005760, 108515861576421376);

-- ----------------------------
-- Table structure for ma_system_user_role
-- ----------------------------
DROP TABLE IF EXISTS `ma_system_user_role`;
CREATE TABLE `ma_system_user_role`
(
    `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户主键',
    `role_id` bigint(20) UNSIGNED NOT NULL COMMENT '角色主键'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户与角色关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ma_system_user_role
-- ----------------------------
INSERT INTO `ma_system_user_role`
VALUES (109292941754896384, 108506227927027712);
INSERT INTO `ma_system_user_role`
VALUES (73421010136862720, 108354281047986176);
INSERT INTO `ma_system_user_role`
VALUES (73421010136862720, 108506227927027712);
INSERT INTO `ma_system_user_role`
VALUES (1, 108354281047986176);
INSERT INTO `ma_system_user_role`
VALUES (1, 108506227927027712);
INSERT INTO `ma_system_user_role`
VALUES (1, 232632128901488640);
INSERT INTO `ma_system_user_role`
VALUES (1, 232632220630917120);
INSERT INTO `ma_system_user_role`
VALUES (1, 232632352457891840);

SET
FOREIGN_KEY_CHECKS = 1;
