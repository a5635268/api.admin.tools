define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'content/teacher/index',
                    add_url: 'content/teacher/add',
                    edit_url: 'content/teacher/edit',
                    del_url: 'content/teacher/del',
                    multi_url: 'content/teacher/multi',
                    table: 'teacher',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'teacher_id',
                sortName: 'teacher_id',
                pageSize: 20,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'teacher_id', title: __('Teacher_id'),operate: false},
                        {field: 'head_img', title: __('Head_img'), events: Controller.api.events.image, formatter: Table.api.formatter.image,operate: false},
                        {field: 'name', title: __('Name')},
                        {field: 'organization.name', title: __('所属机构'), formatter:Table.api.formatter.search},
                        {field: 'school_age', title: __('School_age'),operate: false},
                        {field: 'status', title: __('Status'), searchList: {"1": __('Yes'), "0": __('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
            Controller.api.org();
           
        },
        edit: function () {
            Controller.api.bindevent();
            Controller.api.org();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
                Controller.api.org();
            },
            events: {
                image : {
                    //格式为：方法名+空格+DOM元素
                    'click .img-sm': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var img = '<img src="'+ value +'">';
                        Layer.open({
                            type: 1,//Page层类型
                            title: '图片查看',
                            shade: 0.6,//遮罩透明度
                            maxmin: true,
                            //area: '500px',
                            anim: 3,//0-6的动画形式，-1不开启
                            content: img
                        });
                        return false;
                    }
                }
            },
            org: function () {
                $('#c-org_id').change(function () {
                    var org_id = $('#c-org_id').val();
                    var obj = $("#c-course_ids_text").data("selectPageObject").option.params['custom[org_id]']=org_id;
                    return true;
                });
            }
        }
    };
    return Controller;
});
