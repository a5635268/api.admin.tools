define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'content/course/index',
                    add_url: 'content/course/add',
                    edit_url: 'content/course/edit',
                    del_url: 'content/course/del',
                    multi_url: 'content/course/multi',
                    dragsort_url: '',
                    table: 'course',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'course_id',
                sortName: 'weigh',
                searchFormVisible: true,
                pageSize: 25,
                queryParams: function (params) {
                    //这里可以追加搜索条件
                    var filter = JSON.parse(params.filter);
                    var op = JSON.parse(params.op);
                    //这里可以动态赋值，比如从URL中获取admin_id的值，filter.admin_id=Fast.api.query('admin_id');
                    var org_id = Fast.api.query('org_id');
                    console.log('params',params);
                    if(org_id){
                        filter.org_id = Fast.api.query('org_id');
                        op.org_id = "=";
                    }
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'course_id', title: __('Course_id'),operate: false},
                        {field: 'name', title: __('Name')},
                        {field: 'image', title: __('Image'), events: Controller.api.events.image, formatter: Table.api.formatter.image,operate: false},
                        {field: 'organization.name', title: __('机构名称')},
                        {field: 'bak_url',title: __('参考'),formatter: Controller.api.formatter.bak_url, operate: false},
                        {field: 'weigh', title: __('Weigh'),operate: false},
                        {field: 'status', title: __('Status'),searchList: {"1": __('Yes'), "0": __('No')}, formatter: Table.api.formatter.toggle,operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                bak_url: function (value, row, index) {
                    var html = '<a href="'+ value +'" target="_blank"><span class="label label-info">链接</span></a>'
                    return html;
                }
            },
            events: {
                image : {
                    //格式为：方法名+空格+DOM元素
                    'click .img-sm': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var img = '<img src="' + value + '">';
                        layer.open({
                            type: 1,//Page层类型
                            closeBtn: 0,
                            title: false,
                            area: ['auto'],
                            shadeClose: true,
                            shade: 0.6,//遮罩透明度
                            skin: 'layui-layer-nobg',
                            anim: 1,//0-6的动画形式，-1不开启
                            content: img
                        });
                        return false;
                    }
                }
            },
    
        }
    };
    return Controller;
});
