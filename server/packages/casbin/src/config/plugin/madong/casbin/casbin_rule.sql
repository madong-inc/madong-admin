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

 Date: 07/07/2025 09:08:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ma_sys_casbin_rule
-- ----------------------------

DROP TABLE IF EXISTS `ma_sys_casbin_rule`;
CREATE TABLE `ma_sys_casbin_rule` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '雪花ID',
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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = 'Casbin策略规则表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
