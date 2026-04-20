<?php

/**
 * 创建系统基础表
 * 表:
 * - sys_admin (用户信息表)
 * - sys_admin_casbin (用户与策略关联表)
 * - sys_admin_dept (用户与部门关联表)
 * - sys_admin_post (用户与岗位关联表)
 * - sys_admin_role (用户与角色关联表)
 * - sys_casbin_rule (Casbin策略规则表)
 * - sys_config (配置表)
 * - sys_crontab (定时任务表)
 * - sys_crontab_log (定时任务日志表)
 * - sys_dept (部门信息表)
 * - sys_dept_leader (部门领导关联表)
 * - sys_dict (字典类型表)
 * - sys_dict_item (字典数据表)
 * - sys_login_log (登录日志表)
 * - sys_menu (菜单表)
 * - sys_message (系统消息表)
 * - sys_notice (通知公告表)
 * - sys_operate_log (系统操作日志表)
 * - sys_post (岗位信息表)
 * - sys_rate_limiter (限流规则表)
 * - sys_rate_restrictions (限制访问名单表)
 * - sys_recycle_bin (数据回收记录表)
 * - sys_review (审核记录表)
 * - sys_role (角色信息表)
 * - sys_role_casbin (角色与策略关联表)
 * - sys_role_dept (角色与部门关联表)
 * - sys_role_menu (角色与菜单关联表)
 * - sys_role_scope_dept (角色与部门关联表)
 * - sys_route (路由规则表)
 * - sys_route_cate (路由规则分组表)
 * - sys_upload (文件信息表)
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return new class {

    public function up(Builder $schema): void
    {
        // 用户信息表
        if (!$schema->hasTable('sys_admin')) {
            $schema->create('sys_admin', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('用户ID');
                $table->string('user_name', 20)->unique()->comment('账号');
                $table->string('real_name', 30)->nullable()->comment('用户');
                $table->string('nick_name', 100)->nullable()->comment('昵称');
                $table->string('password', 100)->comment('密码');
                $table->tinyInteger('is_super')->default(0)->comment('用户类型:1系统用户 0普通用户');
                $table->string('mobile_phone', 11)->nullable()->comment('手机');
                $table->string('email', 50)->nullable()->comment('用户邮箱');
                $table->string('avatar', 255)->nullable()->comment('用户头像');
                $table->string('signed', 255)->nullable()->comment('个人签名');
                $table->string('dashboard', 100)->nullable()->comment('后台首页类型');
                $table->bigInteger('dept_id')->nullable()->comment('部门ID');
                $table->smallInteger('enabled')->default(1)->comment('状态: 1正常 0停用');
                $table->string('login_ip', 45)->nullable()->comment('最后登陆IP');
                $table->integer('login_time')->nullable()->comment('最后登陆时间');
                $table->text('backend_setting')->nullable()->comment('后台设置数据');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->unsignedInteger('deleted_at')->nullable()->comment('删除时间');
                $table->tinyInteger('sex')->default(0)->comment('0=未知 1=男 2=女');
                $table->longText('remark')->nullable()->comment('备注');
                $table->string('birthday', 50)->nullable()->comment('生日');
                $table->string('tel', 255)->nullable()->comment('座机');
                $table->smallInteger('is_locked')->default(0)->comment('是否锁定: 1是 0否');
            });
        }

        // 用户与部门职位关联表
        if (!$schema->hasTable('sys_admin_main')) {
            $schema->create('sys_admin_main', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->bigInteger('admin_id')->comment('用户ID(外键)');
                $table->bigInteger('main_dept_id')->nullable()->comment('主部门ID');
                $table->bigInteger('main_post_id')->nullable()->comment('主职位ID');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->index('admin_id', 'idx_admin_id');
                $table->index('main_dept_id', 'idx_main_dept_id');
                $table->index('main_post_id', 'idx_main_post_id');
            });
        }

        // 用户与部门关联表
        if (!$schema->hasTable('sys_admin_dept')) {
            $schema->create('sys_admin_dept', function (Blueprint $table) {
                $table->bigInteger('admin_id')->comment('用户主键');
                $table->bigInteger('dept_id')->comment('角色主键');
                $table->primary(['admin_id', 'dept_id']);
            });
        }

        // 用户与岗位关联表
        if (!$schema->hasTable('sys_admin_post')) {
            $schema->create('sys_admin_post', function (Blueprint $table) {
                $table->bigInteger('admin_id')->comment('管理员主键');
                $table->bigInteger('post_id')->comment('岗位主键');
                $table->primary(['admin_id', 'post_id']);
            });
        }

        // 用户与角色关联表
        if (!$schema->hasTable('sys_admin_role')) {
            $schema->create('sys_admin_role', function (Blueprint $table) {
                $table->bigInteger('admin_id')->comment('管理员主键');
                $table->bigInteger('role_id')->comment('角色主键');
                $table->primary(['admin_id', 'role_id']);
            });
        }

        // 配置表
        if (!$schema->hasTable('sys_config')) {
            $schema->create('sys_config', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('配置ID');
                $table->string('group_code', 64)->nullable()->comment('分组编码');
                $table->string('code', 64)->comment('唯一编码');
                $table->string('name', 64)->comment('配置名称');
                $table->longText('content')->nullable()->comment('配置内容');
                $table->tinyInteger('is_sys')->default(0)->comment('是否系统');
                $table->tinyInteger('enabled')->default(1)->comment('是否启用');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('created_by')->nullable()->comment('创建用户');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
                $table->bigInteger('updated_by')->nullable()->comment('更新用户');
                $table->bigInteger('deleted_at')->nullable()->comment('是否删除');
                $table->longText('remark')->nullable()->comment('备注');
                $table->index('code');
                $table->index('group_code');
            });
        }

        // 定时任务表
        if (!$schema->hasTable('sys_crontab')) {
            $schema->create('sys_crontab', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->string('biz_id', 36)->nullable()->comment('业务id');
                $table->string('title', 255)->comment('任务标题');
                $table->tinyInteger('type')->default(1)->comment('任务类型: 1 url, 2 eval, 3 shell');
                $table->tinyInteger('task_cycle')->default(1)->comment('任务周期');
                $table->json('cycle_rule')->nullable()->comment('任务周期规则');
                $table->longText('rule')->nullable()->comment('任务表达式');
                $table->longText('target')->nullable()->comment('调用任务字符串');
                $table->integer('running_times')->default(0)->comment('已运行次数');
                $table->integer('last_running_time')->default(0)->comment('上次运行时间');
                $table->tinyInteger('first_started')->default(0)->comment('是否已首次启动: 0未启动, 1已启动');
                $table->tinyInteger('enabled')->default(0)->comment('任务状态: 0禁用, 1启用');
                $table->integer('created_at')->default(0)->comment('创建时间');
                $table->bigInteger('created_by')->nullable()->comment('创建人');
                $table->bigInteger('deleted_at')->default(0)->comment('软删除时间');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
                $table->bigInteger('updated_by')->nullable()->comment('更新人');
                $table->tinyInteger('singleton')->default(1)->comment('是否循环执行');
                $table->longText('remark')->nullable()->comment('备注');
                $table->index('title');
                $table->index('enabled');
                $table->index('first_started');
            });
        }

        // 定时任务日志表
        if (!$schema->hasTable('sys_crontab_log')) {
            $schema->create('sys_crontab_log', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->bigInteger('crontab_id')->comment('任务id');
                $table->string('target', 255)->nullable()->comment('任务调用目标字符串');
                $table->longText('log')->nullable()->comment('任务执行日志');
                $table->tinyInteger('return_code')->default(1)->comment('执行返回状态: 1成功, 0失败');
                $table->string('running_time', 10)->comment('执行所用时间');
                $table->bigInteger('created_at')->default(0)->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
                $table->index('created_at');
                $table->index('crontab_id');
            });
        }

        // 部门信息表
        if (!$schema->hasTable('sys_dept')) {
            $schema->create('sys_dept', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->bigInteger('pid')->nullable()->comment('父ID');
                $table->string('level', 500)->nullable()->comment('组级集合');
                $table->string('code', 50)->nullable()->comment('部门唯一编码');
                $table->string('name', 30)->nullable()->comment('部门名称');
                $table->string('main_leader_id', 20)->nullable()->comment('负责人');
                $table->string('phone', 11)->nullable()->comment('联系电话');
                $table->smallInteger('enabled')->default(1)->comment('状态: 1正常 0停用');
                $table->smallInteger('sort')->default(0)->comment('排序');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
                $table->longText('remark')->nullable()->comment('备注');
                $table->index('pid');
            });
        }

        // 部门领导关联表
        if (!$schema->hasTable('sys_dept_leader')) {
            $schema->create('sys_dept_leader', function (Blueprint $table) {
                $table->bigInteger('dept_id')->comment('部门主键');
                $table->bigInteger('admin_id')->comment('管理员主键');
                $table->primary(['dept_id', 'admin_id']);
            });
        }

        // 字典类型表
        if (!$schema->hasTable('sys_dict')) {
            $schema->create('sys_dict', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->string('group_code', 50)->nullable()->comment('字典类型');
                $table->string('name', 50)->nullable()->comment('字典名称');
                $table->string('code', 100)->nullable()->comment('字典标示');
                $table->bigInteger('sort')->default(0)->comment('排序');
                $table->smallInteger('data_type')->default(1)->comment('数据类型');
                $table->longText('description')->nullable()->comment('描述');
                $table->smallInteger('enabled')->default(1)->comment('状态: 1正常 0停用');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
            });
        }

        // 字典数据表
        if (!$schema->hasTable('sys_dict_item')) {
            $schema->create('sys_dict_item', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->bigInteger('dict_id')->nullable()->comment('字典类型ID');
                $table->string('label', 50)->nullable()->comment('字典标签');
                $table->string('value', 100)->nullable()->comment('字典值');
                $table->string('code', 100)->nullable()->comment('字典标示');
                $table->string('color', 50)->nullable()->comment('tag颜色');
                $table->string('other_class', 50)->nullable();
                $table->smallInteger('sort')->default(0)->comment('排序');
                $table->smallInteger('enabled')->default(1)->comment('状态: 1正常 0停用');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
                $table->longText('remark')->nullable()->comment('备注');
                $table->index('dict_id');
            });
        }

        // 登录日志表
        if (!$schema->hasTable('sys_login_log')) {
            $schema->create('sys_login_log', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->bigInteger('user_id')->nullable()->comment('关联Admin ID');
                $table->string('ip', 45)->nullable()->comment('登录IP地址');
                $table->string('ip_location', 255)->nullable()->comment('IP所属地');
                $table->string('os', 50)->nullable()->comment('操作系统');
                $table->string('browser', 50)->nullable()->comment('浏览器');
                $table->smallInteger('status')->default(1)->comment('登录状态: 1成功 2失败');
                $table->longText('message')->nullable()->comment('提示消息');
                $table->bigInteger('login_time')->nullable()->comment('登录时间');
                $table->longText('key')->nullable()->comment('key');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('expires_at')->nullable()->comment('过期时间');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
                $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
                $table->string('remark', 255)->nullable()->comment('备注');
                $table->index('user_id');
            });
        }

        // 菜单表
        if (!$schema->hasTable('sys_menu')) {
            $schema->create('sys_menu', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('菜单ID');
                $table->bigInteger('pid')->default(0)->comment('父ID');
                $table->string('app', 32)->default('admin')->comment('应用编码');
                $table->string('source', 50)->default('system')->comment('菜单来源');
                $table->string('title', 64)->comment('菜单名称');
                $table->string('code', 64)->nullable()->comment('唯一编码');
                $table->string('level', 255)->nullable()->comment('父ID集合');
                $table->integer('type')->nullable()->comment('菜单类型: 1目录 2菜单 3按钮 4接口 5内链 6外链');
                $table->bigInteger('sort')->default(999)->comment('排序');
                $table->string('path', 100)->nullable()->comment('路由地址');
                $table->string('component', 100)->nullable()->comment('组件地址');
                $table->string('redirect', 255)->nullable()->comment('重定向');
                $table->string('icon', 64)->nullable()->comment('菜单图标');
                $table->tinyInteger('is_show')->default(1)->comment('是否显示: 0否 1是');
                $table->tinyInteger('is_link')->default(0)->comment('是否外链: 0否 1是');
                $table->longText('link_url')->nullable()->comment('外部链接地址');
                $table->tinyInteger('enabled')->default(1)->comment('状态: 1正常 0停用');
                $table->integer('open_type')->default(0)->comment('是否外链: 1是 0否');
                $table->tinyInteger('is_cache')->default(0)->comment('是否缓存: 1是 0否');
                $table->tinyInteger('is_sync')->default(1)->comment('是否同步');
                $table->tinyInteger('is_affix')->default(0)->comment('是否固定tags无法关闭');
                $table->tinyInteger('is_global')->default(0)->comment('是否全局公共菜单');
                $table->string('variable', 500)->nullable()->comment('额外参数JSON');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('created_by')->nullable()->comment('创建用户');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
                $table->bigInteger('updated_by')->nullable()->comment('更新用户');
                $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
                $table->string('methods', 10)->default('get')->comment('请求方法');
                $table->tinyInteger('is_frame')->nullable()->comment('是否外链');
                $table->index('code');
                $table->index('app');
            });
        }

        // 系统消息表
        if (!$schema->hasTable('sys_message')) {
            $schema->create('sys_message', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->string('type', 50)->nullable()->comment('消息类型');
                $table->string('title', 255)->default('')->comment('消息标题');
                $table->longText('content')->nullable()->comment('消息内容');
                $table->bigInteger('sender_id')->default(0)->comment('发送者ID');
                $table->bigInteger('receiver_id')->comment('接收者ID');
                $table->string('message_uuid', 50)->nullable()->comment('消息唯一编码');
                $table->enum('status', ['unread', 'read'])->default('unread')->comment('消息状态');
                $table->tinyInteger('priority')->default(3)->comment('优先级: 1紧急 2急迫 3普通');
                $table->string('channel', 50)->default('message')->comment('发送渠道');
                $table->string('related_id', 100)->default('')->comment('关联业务ID');
                $table->string('related_type', 100)->default('')->comment('关联业务类型');
                $table->json('jump_params')->nullable()->comment('调整参数');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('expired_at')->nullable()->comment('过期时间');
                $table->bigInteger('read_at')->nullable()->comment('已读时间');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
                $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
            });
        }

        // 通知公告表
        if (!$schema->hasTable('sys_notice')) {
            $schema->create('sys_notice', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('公告ID');
                $table->enum('type', ['announcement', 'notice'])->default('announcement')->comment('公告类型');
                $table->string('title', 50)->comment('公告标题');
                $table->longText('content')->nullable()->comment('公告内容');
                $table->integer('sort')->default(10)->comment('排序');
                $table->tinyInteger('enabled')->default(0)->comment('公告状态: 0正常 1关闭');
                $table->string('uuid', 50)->nullable()->comment('uuid');
                $table->bigInteger('created_dept')->nullable()->comment('创建部门');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
                $table->string('remark', 255)->nullable()->comment('备注');
            });
        }

        // 系统操作日志表
        if (!$schema->hasTable('sys_operate_log')) {
            $schema->create('sys_operate_log', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->string('name', 50)->nullable()->comment('内容');
                $table->string('app', 50)->nullable()->comment('应用名称');
                $table->string('ip', 255)->nullable()->comment('请求ip');
                $table->string('ip_location', 255)->nullable()->comment('请求ip归属地');
                $table->string('browser', 255)->nullable()->comment('浏览器');
                $table->string('os', 255)->nullable()->comment('操作系统');
                $table->string('url', 500)->nullable()->comment('请求地址');
                $table->string('class_name', 500)->nullable()->comment('类名称');
                $table->string('action', 500)->nullable()->comment('方法名称');
                $table->string('method', 255)->nullable()->comment('请求方式');
                $table->longText('param')->nullable()->comment('请求参数');
                $table->longText('result')->nullable()->comment('返回结果');
                $table->bigInteger('created_at')->nullable()->comment('操作时间');
                $table->bigInteger('updated_at')->nullable();
                $table->string('user_name', 50)->nullable()->comment('操作账号');
            });
        }

        // 岗位信息表
        if (!$schema->hasTable('sys_post')) {
            $schema->create('sys_post', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->bigInteger('dept_id')->nullable()->comment('部门id');
                $table->string('code', 100)->comment('岗位代码');
                $table->string('name', 50)->comment('岗位名称');
                $table->smallInteger('sort')->default(0)->comment('排序');
                $table->smallInteger('enabled')->default(1)->comment('状态: 1正常 0停用');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
                $table->string('remark', 255)->nullable()->comment('备注');
            });
        }

        // 限流规则表
        if (!$schema->hasTable('sys_rate_limiter')) {
            $schema->create('sys_rate_limiter', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->string('name', 64)->comment('规则名称');
                $table->tinyInteger('enabled')->default(1)->comment('状态');
                $table->integer('priority')->default(100)->comment('优先级');
                $table->enum('match_type', ['exact', 'wildcard', 'regex'])->default('exact')->comment('匹配类型');
                $table->string('ip', 50)->nullable()->comment('ip地址');
                $table->string('methods', 100)->default('GET')->comment('请求方法');
                $table->string('path', 255)->default('/')->comment('请求路径');
                $table->string('limit_type', 50)->default('ip')->comment('限制类型');
                $table->integer('limit_value')->default(0)->comment('限制值');
                $table->integer('period')->default(60)->comment('统计周期(秒)');
                $table->integer('ttl')->nullable()->comment('缓存时间(秒)');
                $table->longText('message')->nullable()->comment('提示信息');
                $table->bigInteger('created_by')->nullable()->comment('创建人');
                $table->bigInteger('updated_by')->nullable()->comment('修改人');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->index(['enabled', 'priority']);
                $table->index(['methods', 'path']);
            });
        }

        // 限制访问名单表
        if (!$schema->hasTable('sys_rate_restrictions')) {
            $schema->create('sys_rate_restrictions', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->string('ip', 50)->nullable()->comment('IP地址');
                $table->string('name', 64)->comment('名称');
                $table->tinyInteger('enabled')->default(1)->comment('规则状态: 0禁用 1启用');
                $table->integer('priority')->default(100)->comment('规则优先级');
                $table->string('methods', 100)->default('GET')->comment('请求方法');
                $table->string('path', 255)->default('/')->comment('路径');
                $table->string('message', 255)->nullable()->comment('提示信息');
                $table->bigInteger('start_time')->nullable()->comment('开始时间');
                $table->bigInteger('end_time')->nullable()->comment('结束时间');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->bigInteger('created_by')->nullable()->comment('创建人');
                $table->bigInteger('updated_by')->nullable()->comment('修改人');
                $table->longText('remark')->nullable()->comment('备注');
                $table->index(['enabled', 'priority']);
                $table->index(['ip', 'methods', 'path']);
            });
        }

        // 数据回收记录表
        if (!$schema->hasTable('sys_recycle_bin')) {
            $schema->create('sys_recycle_bin', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('ID');
                $table->bigInteger('original_id')->nullable()->comment('原始数据ID');
                $table->json('data')->nullable()->comment('回收的数据');
                $table->string('table_name', 100)->default('')->comment('数据表');
                $table->string('table_prefix', 50)->nullable()->comment('表前缀');
                $table->tinyInteger('enabled')->default(0)->comment('是否已还原');
                $table->string('ip', 50)->default('')->comment('操作者IP');
                $table->bigInteger('operate_by')->default(0)->comment('操作管理员');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
            });
        }

        // 审核记录表
        if (!$schema->hasTable('sys_review')) {
            $schema->create('sys_review', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('雪花ID主键');
                $table->string('reviewable_type', 255)->comment('关联模型类型');
                $table->bigInteger('reviewable_id')->comment('关联模型ID');
                $table->tinyInteger('status')->default(0)->comment('审核状态: 0待审/1通过/2拒绝');
                $table->longText('reason')->nullable()->comment('审核原因/备注');
                $table->bigInteger('reviewer_id')->nullable()->comment('审核人ID');
                $table->integer('reviewed_at')->nullable()->comment('审核时间戳');
                $table->string('flow_instance_id', 100)->nullable()->comment('审批流实例ID');
                $table->json('extra_data')->nullable()->comment('扩展数据');
                $table->bigInteger('created_by')->comment('创建人ID');
                $table->bigInteger('updated_by')->nullable()->comment('更新人ID');
                $table->integer('created_at')->comment('创建时间戳');
                $table->integer('updated_at')->comment('更新时间戳');
                $table->integer('deleted_at')->nullable()->comment('软删除时间戳');
                $table->index(['reviewable_type', 'reviewable_id']);
                $table->index('status');
                $table->index('reviewer_id');
                $table->index('flow_instance_id');
                $table->index('created_at');
            });
        }

        // 角色信息表
        if (!$schema->hasTable('sys_role')) {
            $schema->create('sys_role', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('主键');
                $table->bigInteger('pid')->default(0)->comment('父级id');
                $table->string('name', 30)->nullable()->comment('角色名称');
                $table->string('code', 100)->nullable()->comment('角色代码');
                $table->tinyInteger('is_super_admin')->default(0)->comment('是否超级管理员: 1是 0否');
                $table->tinyInteger('role_type')->nullable()->comment('角色类型');
                $table->smallInteger('data_scope')->default(1)->comment('数据范围');
                $table->smallInteger('enabled')->default(1)->comment('状态: 1正常 0停用');
                $table->smallInteger('sort')->default(0)->comment('排序');
                $table->string('remark', 255)->nullable()->comment('备注');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('修改时间');
                $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
            });
        }

        // 角色与部门关联表
        if (!$schema->hasTable('sys_role_dept')) {
            $schema->create('sys_role_dept', function (Blueprint $table) {
                $table->bigInteger('role_id')->comment('用户主键');
                $table->bigInteger('dept_id')->comment('角色主键');
                $table->primary(['role_id', 'dept_id']);
            });
        }

        // 角色与菜单关联表
        if (!$schema->hasTable('sys_role_menu')) {
            $schema->create('sys_role_menu', function (Blueprint $table) {
                $table->bigInteger('role_id')->comment('角色主键');
                $table->bigInteger('menu_id')->comment('菜单主键');
                $table->primary(['role_id', 'menu_id']);
            });
        }

        // 角色与部门关联表
        if (!$schema->hasTable('sys_role_scope_dept')) {
            $schema->create('sys_role_scope_dept', function (Blueprint $table) {
                $table->bigInteger('role_id')->comment('用户主键');
                $table->bigInteger('dept_id')->comment('角色主键');
                $table->primary(['role_id', 'dept_id']);
            });
        }

        // 文件信息表
        if (!$schema->hasTable('sys_upload')) {
            $schema->create('sys_upload', function (Blueprint $table) {
                $table->bigInteger('id')->primary()->comment('文件信息ID');
                $table->longText('url')->comment('文件访问地址');
                $table->bigInteger('size')->nullable()->comment('文件大小');
                $table->string('size_info', 64)->nullable()->comment('文件大小有单位');
                $table->string('hash', 64)->nullable()->comment('文件hash');
                $table->string('filename', 255)->nullable()->comment('文件名称');
                $table->string('original_filename', 255)->nullable()->comment('原始文件名');
                $table->longText('base_path')->nullable()->comment('基础存储路径');
                $table->longText('path')->nullable()->comment('存储路径');
                $table->string('ext', 32)->nullable()->comment('文件扩展名');
                $table->string('content_type', 100)->nullable()->comment('MIME类型');
                $table->string('platform', 32)->nullable()->comment('存储平台');
                $table->string('th_url', 255)->nullable()->comment('缩略图访问路径');
                $table->string('th_filename', 255)->nullable()->comment('缩略图文件名');
                $table->bigInteger('th_size')->nullable()->comment('缩略图大小');
                $table->string('th_size_info', 64)->nullable()->comment('缩略图大小有单位');
                $table->string('th_content_type', 32)->nullable()->comment('缩略图MIME类型');
                $table->string('object_id', 32)->nullable()->comment('文件所属对象id');
                $table->string('object_type', 32)->nullable()->comment('文件所属对象类型');
                $table->text('attr')->nullable()->comment('附加属性');
                $table->bigInteger('created_at')->nullable()->comment('创建时间');
                $table->bigInteger('updated_at')->nullable()->comment('更新时间');
                $table->bigInteger('created_by')->nullable()->comment('创建用户');
                $table->bigInteger('updated_by')->nullable()->comment('更新用户');
            });
        }

        echo "Created sys tables.\n";
    }

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('sys_upload');
        $schema->dropIfExists('sys_role_scope_dept');
        $schema->dropIfExists('sys_role_menu');
        $schema->dropIfExists('sys_role_dept');
        $schema->dropIfExists('sys_role');
        $schema->dropIfExists('sys_review');
        $schema->dropIfExists('sys_recycle_bin');
        $schema->dropIfExists('sys_rate_restrictions');
        $schema->dropIfExists('sys_rate_limiter');
        $schema->dropIfExists('sys_post');
        $schema->dropIfExists('sys_operate_log');
        $schema->dropIfExists('sys_notice');
        $schema->dropIfExists('sys_message');
        $schema->dropIfExists('sys_menu');
        $schema->dropIfExists('sys_login_log');
        $schema->dropIfExists('sys_dict_item');
        $schema->dropIfExists('sys_dict');
        $schema->dropIfExists('sys_dept_leader');
        $schema->dropIfExists('sys_dept');
        $schema->dropIfExists('sys_crontab_log');
        $schema->dropIfExists('sys_crontab');
        $schema->dropIfExists('sys_config');
        $schema->dropIfExists('sys_admin_role');
        $schema->dropIfExists('sys_admin_post');
        $schema->dropIfExists('sys_admin_dept');
        $schema->dropIfExists('sys_admin_main');
        $schema->dropIfExists('sys_admin');
    }
};
