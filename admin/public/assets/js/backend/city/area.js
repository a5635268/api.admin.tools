define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'city/area/index',
                    add_url: 'city/area/add',
                    edit_url: 'city/area/edit',
                    del_url: 'city/area/del',
                    multi_url: 'city/area/multi',
                    table: 'city_area',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'city_id',
                sortName: 'city_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'city_id', title: __('City_id')},
                        {field: 'city_code', title: __('City_code')},
                        {field: 'city_first_py', title: __('City_first_py')},
                        {field: 'city_name', title: __('City_name')},
                        {field: 'city_pinyin', title: __('City_pinyin')},
                        {field: 'city_level', title: __('City_level')},
                        {field: 'province', title: __('Province')},
                        {field: 'province_pinyin', title: __('Province_pinyin')},
                        {field: 'nation', title: __('Nation')},
                        {field: 'parent_city_code', title: __('Parent_city_code')},
                        {field: 'longitude', title: __('Longitude')},
                        {field: 'latitude', title: __('Latitude')},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'create_user_code', title: __('Create_user_code')},
                        {field: 'is_hot', title: __('Is_hot')},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'update_user_code', title: __('Update_user_code')},
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