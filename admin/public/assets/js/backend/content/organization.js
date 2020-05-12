define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'content/organization/index',
                    add_url: 'content/organization/add',
                    edit_url: 'content/organization/edit',
                    del_url: 'content/organization/del',
                    multi_url: 'content/organization/multi',
                    dragsort_url: '',
                    table: 'organization',
                }
            });

            var table = $("#table");

          //在普通搜索渲染后
          table.on('post-common-search.bs.table', function (event, table) {
              $('#category_data').parents('div.form-group').remove();
          });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'org_id',
                sortName: 'weigh',
                pageSize: 25,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'org_id', title: __('Org_id'), operate: false},
                        {field: 'city_name', title: __('城市'), operate: false},
                        {field: 'name', title: __('Name'), operate: false},
                        {field: 'py_name',title: __('关键词'), operate: false},
                        {
                            field: 'bak_url',
                            title: __('参考'),
                            formatter: Controller.api.formatter.bak_url,
                            operate: false
                        },
                        {
                            field: 'category_data',
                            title: __('所属分类'),
                            formatter: Controller.api.formatter.category
                        },
                        {field: 'logo', title: __('Logo'), events: Controller.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'cover', title: __('Cover'), events: Controller.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'status', title: __('Status'),searchList: {"1": __('显示'), "0": __('隐藏')}, formatter: Table.api.formatter.toggle, operate: false},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {
                            field: 'buttons',
                            width: "120px",
                            title: __('关联'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'addtabs',
                                    text: __('课程'),
                                    title: __('课程'),
                                    classname: 'btn btn-xs btn-info btn-addtabs',
                                    icon: 'fa fa-list',
                                    url: 'content/course/index?org_id={org_id}'
                                }
                            ],
                            formatter: Table.api.formatter.buttons,
                            operate: false
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
            Controller.api.category();
        },
        edit: function () {
            Controller.api.bindevent();
            Controller.api.category();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
              category : function (value, row, index) {
                  var that = this;
                  var colorArr = ['success'];
                  var html = [];
                  var arr = value;
                  $.each(arr, function (i, item) {
                    var color = colorArr[Math.floor((Math.random()*colorArr.length))];;
                    var display = item.name;
                    var item = '<a href="javascript:;" class="searchit" data-toggle="tooltip" title="' + __('Click to search %s', display) + '" data-field="category_data" data-value="' + item.category_id + '"><span class="label label-' + color + '">' + display + '</span></a>';
                    item += (html.length+1) % 3 === 0 ? '<br />' : '';
                    html.push(item);
                  });
                  return html.join(' ');
              },
                bak_url: function (value, row, index) {
                    var html = '<a href="'+ value +'" target="_blank"><span class="label label-info">参考链接</span></a>'
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
            category: function () {
                $('.category_id_2').change(function () {
                    var categoryId = $(this).find('option:selected').val();
                    var url = 'content/category/selectpage?category_id=' + categoryId;
                    var inputText = $(this).next().find('li.input_box').find('input');
                    inputText.attr('name', '');
                    inputText.data("selectPageObject").option.data = url;
                    return true;
                })
                // 添加
                $('.form-inline .btn-dragsort').bind('click',function () {
                    $(this).parents('.form-inline').next().removeClass('hide');
                })
                // 删除
                $('.form-inline .btn-danger').bind('click',function () {
                    var thisDiv = $(this).parents('.form-inline');
                    thisDiv.addClass('hide');
                    thisDiv.find('.category_id_2').val('');
                    thisDiv.find('.category_id_2').next().find('ul>li.selected_tag').remove();
                    thisDiv.find('.category_id_2').next().find('input.sp_hidden').val('');
                })
            }
        }
    };
    return Controller;
});
