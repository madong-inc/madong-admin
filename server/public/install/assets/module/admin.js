/** EasyWeb site v2.0.0 date:2019-10-01 License By http://easyweb.vip */

layui.define(['layer', 'element'], function (exports) {
    var $ = layui.jquery;
    var layer = layui.layer;

    var admin = {
        popupRight: function (param) {
            if (param.title == undefined) {
                param.title = false;
                param.closeBtn = false;
            }
            if (param.fixed == undefined) {
                param.fixed = true;
            }
            param.anim = -1;
            param.offset = 'r';
            param.shadeClose = true;
            param.area || (param.area = '336px');
            param.skin || (param.skin = 'layui-anim layui-anim-rl layui-layer-adminRight');
            param.move = false;
            return admin.open(param);
        },
        /* 封装layer.open */
        open: function (param) {
            if (!param.area) {
                param.area = (param.type == 2) ? ['360px', '300px'] : '360px';
            }
            if (!param.skin) {
                param.skin = 'layui-layer-admin';
            }
            if (param.fixed == undefined) {
                param.fixed = false;
            }
            param.resize = param.resize != undefined ? param.resize : false;
            param.shade = param.shade != undefined ? param.shade : .1;
            var eCallBack = param.end;
            param.end = function () {
                layer.closeAll('tips');
                eCallBack && eCallBack();
            };
            if (param.url) {
                (param.type == undefined) && (param.type = 1);
                var sCallBack = param.success;
                param.success = function (layero, index) {
                    admin.showLoading(layero, 2);
                    $(layero).children('.layui-layer-content').load(param.url, function () {
                        sCallBack ? sCallBack(layero, index) : '';
                        admin.removeLoading(layero, false);
                    });
                };
            }
            var layIndex = layer.open(param);
            (param.data) && (admin.layerData['d' + layIndex] = param.data);
            return layIndex;
        },
        /* 弹窗数据 */
        layerData: {},
        /* 获取弹窗数据 */
        getLayerData: function (index, key) {
            if (index == undefined) {
                index = parent.layer.getFrameIndex(window.name);
                return parent.layui.admin.getLayerData(index, key);
            } else if (index.toString().indexOf('#') == 0) {
                index = $(index).parents('.layui-layer').attr('id').substring(11);
            }
            var layerData = admin.layerData['d' + index];
            if (key) {
                return layerData ? layerData[key] : layerData;
            }
            return layerData;
        },
        /* 放入弹窗数据 */
        putLayerData: function (key, value, index) {
            if (index == undefined) {
                index = parent.layer.getFrameIndex(window.name);
                return parent.layui.admin.putLayerData(key, value, index);
            } else if (index.toString().indexOf('#') == 0) {
                index = $(index).parents('.layui-layer').attr('id').substring(11);
            }
            var layerData = admin.getLayerData(index);
            layerData || (layerData = {});
            layerData[key] = value;
        },
        /* 显示加载动画 */
        showLoading: function (elem, type, opacity) {
            var size;
            if (elem != undefined && (typeof elem != 'string') && !(elem instanceof $)) {
                type = elem.type;
                opacity = elem.opacity;
                size = elem.size;
                elem = elem.elem;
            }
            (!elem) && (elem = 'body');
            (type == undefined) && (type = 1);
            (size == undefined) && (size = 'sm');
            size = ' ' + size;
            var loader = [
                '<div class="ball-loader' + size + '"><span></span><span></span><span></span><span></span></div>'
            ];
            $(elem).addClass('page-no-scroll');  // 禁用滚动条
            var $loading = $(elem).children('.page-loading');
            if ($loading.length <= 0) {
                $(elem).append('<div class="page-loading">' + loader[type - 1] + '</div>');
                $loading = $(elem).children('.page-loading');
            }
            opacity && $loading.css('background-color', 'rgba(255,255,255,' + opacity + ')');
            $loading.show();
        },
        /* 移除加载动画 */
        removeLoading: function (elem, fade, del) {
            if (!elem) {
                elem = 'body';
            }
            if (fade == undefined) {
                fade = true;
            }
            var $loading = $(elem).children('.page-loading');
            if (del) {
                $loading.remove();
            } else {
                fade ? $loading.fadeOut() : $loading.hide();
            }
            $(elem).removeClass('page-no-scroll');
        },
        /* 缓存临时数据 */
        putTempData: function (key, value) {
            var tableName = admin.tableName + '_tempData';
            if (value != undefined && value != null) {
                layui.sessionData(tableName, {key: key, value: value});
            } else {
                layui.sessionData(tableName, {key: key, remove: true});
            }
        },
        /* 获取缓存临时数据 */
        getTempData: function (key) {
            var tableName = admin.tableName + '_tempData';
            var tempData = layui.sessionData(tableName);
            if (tempData) {
                return tempData[key];
            } else {
                return false;
            }
        },
        /* 刷新当前页面 */
        refresh: function () {
            location.reload();
        },
        /* 关闭当前iframe层弹窗 */
        closeThisDialog: function () {
            parent.layer.close(parent.layer.getFrameIndex(window.name));
        },
        /* 关闭elem所在的页面层弹窗 */
        closeDialog: function (elem) {
            var id = $(elem).parents('.layui-layer').attr('id').substring(11);
            layer.close(id);
        },
        /* 让当前的ifram弹层自适应高度 */
        iframeAuto: function () {
            parent.layer.iframeAuto(parent.layer.getFrameIndex(window.name));
        },
        /* 获取浏览器高度 */
        getPageHeight: function () {
            return document.documentElement.clientHeight || document.body.clientHeight;
        },
        /* 获取浏览器宽度 */
        getPageWidth: function () {
            return document.documentElement.clientWidth || document.body.clientWidth;
        },
        /* 重置表格尺寸 */
        resizeTable: function (time) {
            setTimeout(function () {
                $('.layui-table-view').each(function () {
                    var tbId = $(this).attr("lay-id");
                    layui.table && layui.table.resize(tbId);
                });
            }, time == undefined ? 0 : time);
        }
    };

    /** admin提供的事件 */
    admin.events = {
        /* 刷新主体部分 */
        refresh: function () {
            admin.refresh();
        },
        /* 后退 */
        back: function () {
            history.back();
        },
        /* 打开修改密码弹窗 */
        psw: function () {
            var url = $(this).data('url');
            admin.open({
                id: 'pswForm',
                type: 2,
                title: '修改密码',
                area: ['360px', '287px'],
                content: url ? url : 'page/tpl/tpl-password.html'
            });
        },
        /* 退出登录 */
        logout: function () {
            var url = $(this).data('url');
            layer.confirm('确定要退出登录吗？', {
                title: '温馨提示',
                skin: 'layui-layer-admin',
                shade: .1
            }, function () {
                location.replace(url ? url : '/');
            });
        },
        /* 打开弹窗 */
        open: function () {
            var option = $(this).data();
            admin.open(admin.parseLayerOption(admin.util.deepClone(option)));
        },
        /* 打开右侧弹窗 */
        popupRight: function () {
            var option = $(this).data();
            admin.popupRight(admin.parseLayerOption(admin.util.deepClone(option)));
        },
        /* 关闭当前iframe层弹窗 */
        closeDialog: function () {
            admin.closeThisDialog();
        },
        /* 关闭当前页面层弹窗 */
        closePageDialog: function () {
            admin.closeDialog(this);
        }
    };

    /** 工具类 */
    admin.util = {
        /* 深度克隆对象 */
        deepClone: function (obj) {
            var result;
            var oClass = admin.util.isClass(obj);
            if (oClass === "Object") {
                result = {};
            } else if (oClass === "Array") {
                result = [];
            } else {
                return obj;
            }
            for (var key in obj) {
                var copy = obj[key];
                if (admin.util.isClass(copy) == "Object") {
                    result[key] = arguments.callee(copy); // 递归调用
                } else if (admin.util.isClass(copy) == "Array") {
                    result[key] = arguments.callee(copy);
                } else {
                    result[key] = obj[key];
                }
            }
            return result;
        },
        /* 获取变量类型 */
        isClass: function (o) {
            if (o === null)
                return "Null";
            if (o === undefined)
                return "Undefined";
            return Object.prototype.toString.call(o).slice(8, -1);
        }
    };

    /** 所有ew-event */
    $(document).on('click', '*[ew-event]', function () {
        var event = $(this).attr('ew-event');
        var te = admin.events[event];
        te && te.call(this, $(this));
    });

    /** 所有lay-tips处理 */
    $(document).on('mouseenter', '*[lay-tips]', function () {
        var tipText = $(this).attr('lay-tips');
        var dt = $(this).attr('lay-direction');
        var bgColor = $(this).attr('lay-bg');
        var offset = $(this).attr('lay-offset');
        layer.tips(tipText, this, {
            tips: [dt || 1, bgColor || '#303133'], time: -1, success: function (layero, index) {
                if (offset) {
                    offset = offset.split(',');
                    var top = offset[0], left = offset.length > 1 ? offset[1] : undefined;
                    top && ($(layero).css('margin-top', top));
                    left && ($(layero).css('margin-left', left));
                }
            }
        });
    }).on('mouseleave', '*[lay-tips]', function () {
        layer.closeAll('tips');
    });

    exports('admin', admin);
});
