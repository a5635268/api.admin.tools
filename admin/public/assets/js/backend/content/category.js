define(['jquery', 'bootstrap', 'backend', 'table', 'form','editable'], function ($, undefined, Backend, Table, Form, undefined) {

  var Controller = {
    index: function () {
      // 初始化表格参数配置
      Table.api.init({
        extend: {
          index_url: 'content/category/index',
          add_url: 'content/category/add',
          edit_url: 'content/category/edit',
          del_url: '',
          dragsort_url: '',
          table: 'category',
        }
      });


      var table = $("#table");
      var tableOptions = {
        url: $.fn.bootstrapTable.defaults.extend.index_url,
        escape: false,
        pk: 'category_id',
      //  sortName: 'weigh',
        pagination: false,
        commonSearch: false,
        columns: [
          [
            {field: 'id', title: __('Id')},
            {field: 'name', title: __('Name'), align: 'left'},
            {field: 'py_name', title: __('Py_name')},
            {field: 'icon', title: __('Icon'), operate: false, formatter: Table.api.formatter.image},
            {
                field: 'label',
                title: __('标签'),
                operate: false,
                editable: {
                    type: "checklist",
                    separator:",",
                    emptytext: "普通",
                    source: [
                        { value: '热门', text: '热门' },
                        { value: '首页', text: '首页' }
                    ],
                }
            },
            {field: 'weigh', title: __('Weigh')},
            {field: 'status', title: __('Status'), operate: false, searchList: {"1": __('显示'), "0": __('隐藏')}, formatter: Table.api.formatter.toggle},
            {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
          ]
        ]
      };
      // 初始化表格
      table.bootstrapTable(tableOptions);

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
