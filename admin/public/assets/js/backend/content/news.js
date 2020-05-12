define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'content/news/index',
                    add_url: 'content/news/add',
                    edit_url: 'content/news/edit',
                    del_url: 'content/news/del',
                    multi_url: 'content/news/multi',
                    table: 'news',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'news_id',
                sortName: 'news_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'news_id', title: __('News_id')},
                        {field: 'title', title: __('Title')},
                        {field: 'image', title: __('Image'), formatter: Table.api.formatter.image},
                        {field: 'tags', title: __('Tags')},
                        {field: 'listorder', title: __('Listorder')},
                        {field: 'status', title: __('Status'), searchList: {"1": __('显示'), "0": __('隐藏')}, formatter: Table.api.formatter.toggle},
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
            }
        }
    };
    return Controller;
});