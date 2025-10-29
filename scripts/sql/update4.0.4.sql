START TRANSACTION;

ALTER TABLE `ma_sys_message` ADD COLUMN `related_type` VARCHAR(100) NULL DEFAULT NULL COMMENT '业务类型';
ALTER TABLE `ma_sys_message` ADD COLUMN `jump_params` json NULL DEFAULT NULL COMMENT '业务跳转参数';


COMMIT;


