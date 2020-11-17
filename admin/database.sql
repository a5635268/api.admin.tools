/*
 Navicat Premium Data Transfer

 Source Server         : 157开发机
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : localhost:3306
 Source Schema         : dms

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : 65001

 Date: 17/11/2020 16:12:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tb_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `tb_auth_rule`;
CREATE TABLE `tb_auth_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为菜单',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT 0 COMMENT '权重',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE,
  INDEX `weigh`(`weigh`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 172 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '节点表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tb_auth_rule
-- ----------------------------
INSERT INTO `tb_auth_rule` VALUES (85, 'file', 0, 'addon', '插件管理', 'fa fa-circle-o', '', '可在线安装、卸载、禁用、启用插件，同时支持添加本地插件。FastAdmin已上线插件商店 ，你可以发布你的免费或付费插件：<a href=\"https://www.fastadmin.net/store.html\" target=\"_blank\">https://www.fastadmin.net/store.html</a>', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (86, 'file', 85, 'addon/index', '查看', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (87, 'file', 85, 'addon/config', '配置', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (88, 'file', 85, 'addon/install', '安装', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (89, 'file', 85, 'addon/uninstall', '卸载', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (90, 'file', 85, 'addon/state', '禁用启用', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (91, 'file', 85, 'addon/local', '本地上传', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (92, 'file', 85, 'addon/upgrade', '更新插件', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (93, 'file', 85, 'addon/downloaded', '已装插件', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (94, 'file', 85, 'addon/add', '添加', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (95, 'file', 85, 'addon/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (96, 'file', 85, 'addon/del', '删除', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (97, 'file', 85, 'addon/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (98, 'file', 0, 'auth', '权限管理', 'fa fa-list', '', '', 1, 1560158558, 1577517130, 10, 'normal');
INSERT INTO `tb_auth_rule` VALUES (99, 'file', 98, 'auth/admin', '管理员管理', 'fa fa-users', '', '一个管理员可以有多个角色组,左侧的菜单根据管理员所拥有的权限进行生成', 1, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (100, 'file', 99, 'auth/admin/index', '查看', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (101, 'file', 99, 'auth/admin/add', '添加', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (102, 'file', 99, 'auth/admin/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (103, 'file', 99, 'auth/admin/del', '删除', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (104, 'file', 98, 'auth/adminlog', '管理员日志', 'fa fa-users', '', '管理员可以查看自己所拥有的权限的管理员日志', 1, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (105, 'file', 104, 'auth/adminlog/index', '查看', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (106, 'file', 104, 'auth/adminlog/detail', '详情', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (107, 'file', 104, 'auth/adminlog/del', '删除', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (108, 'file', 104, 'auth/adminlog/selectpage', 'Selectpage', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (109, 'file', 98, 'auth/group', '角色组', 'fa fa-group', '', '角色组可以有多个,角色有上下级层级关系,如果子角色有角色组和管理员的权限则可以派生属于自己组别下级的角色组或管理员', 1, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (110, 'file', 109, 'auth/group/index', '查看', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (111, 'file', 109, 'auth/group/add', '添加', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (112, 'file', 109, 'auth/group/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (113, 'file', 109, 'auth/group/del', '删除', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (114, 'file', 98, 'auth/rule', '规则管理', 'fa fa-list', '', '规则通常对应一个控制器的方法,同时左侧的菜单栏数据也从规则中体现,通常建议通过控制台进行生成规则节点', 1, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (115, 'file', 114, 'auth/rule/index', '查看', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (116, 'file', 114, 'auth/rule/add', '添加', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (117, 'file', 114, 'auth/rule/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (118, 'file', 114, 'auth/rule/del', '删除', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (119, 'file', 114, 'auth/rule/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (120, 'file', 0, 'general', '常规管理', 'fa fa-list', '', '', 1, 1560158558, 1567997572, 9, 'normal');
INSERT INTO `tb_auth_rule` VALUES (121, 'file', 120, 'general/attachment', '附件管理', 'fa fa-circle-o', '', '主要用于管理上传到又拍云的数据或上传至本服务的上传数据', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (122, 'file', 121, 'general/attachment/index', '查看', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (123, 'file', 121, 'general/attachment/select', '选择附件', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (124, 'file', 121, 'general/attachment/add', '添加', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (125, 'file', 121, 'general/attachment/del', '删除附件', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (126, 'file', 121, 'general/attachment/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (127, 'file', 121, 'general/attachment/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (128, 'file', 120, 'general/config', '系统配置', 'fa fa-cogs', '', '可以在此增改系统的变量和分组,也可以自定义分组和变量,如果需要删除请从数据库中删除', 1, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (129, 'file', 128, 'general/config/index', '查看', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (130, 'file', 128, 'general/config/add', '添加', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (131, 'file', 128, 'general/config/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (132, 'file', 128, 'general/config/del', 'Del', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (133, 'file', 128, 'general/config/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (134, 'file', 120, 'general/profile', '个人配置', 'fa fa-user', '', '', 1, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (135, 'file', 134, 'general/profile/index', '查看', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (136, 'file', 134, 'general/profile/update', '更新个人信息', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (137, 'file', 134, 'general/profile/add', '添加', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (138, 'file', 134, 'general/profile/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (139, 'file', 134, 'general/profile/del', '删除', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');
INSERT INTO `tb_auth_rule` VALUES (140, 'file', 134, 'general/profile/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1560158558, 1560158558, 0, 'normal');

-- ----------------------------
-- Table structure for tb_config
-- ----------------------------
DROP TABLE IF EXISTS `tb_config`;
CREATE TABLE `tb_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '变量值',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '变量字典数据',
  `rule` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '扩展属性',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tb_config
-- ----------------------------
INSERT INTO `tb_config` VALUES (1, 'name', 'basic', 'Site name', '请填写站点名称', 'string', '资管系统', '', 'required', '');
INSERT INTO `tb_config` VALUES (2, 'beian', 'basic', 'Beian', '粤ICP备15054802号-4', 'string', '', '', '', '');
INSERT INTO `tb_config` VALUES (3, 'cdnurl', 'basic', 'Cdn url', '如果静态资源使用第三方云储存请配置该值', 'string', '', '', '', '');
INSERT INTO `tb_config` VALUES (4, 'version', 'basic', 'Version', '如果静态资源有变动请重新配置该值', 'string', '1.0.1', '', 'required', '');
INSERT INTO `tb_config` VALUES (5, 'timezone', 'basic', 'Timezone', '', 'string', 'Asia/Shanghai', '', 'required', '');
INSERT INTO `tb_config` VALUES (6, 'forbiddenip', 'basic', 'Forbidden ip', '一行一条记录', 'text', '', '', '', '');
INSERT INTO `tb_config` VALUES (7, 'languages', 'basic', 'Languages', '', 'array', '{\"backend\":\"zh-cn\"}', '', 'required', '');
INSERT INTO `tb_config` VALUES (8, 'fixedpage', 'basic', 'Fixed page', '请尽量输入左侧菜单栏存在的链接', 'string', 'dashboard', '', 'required', '');
INSERT INTO `tb_config` VALUES (9, 'categorytype', 'dictionary', 'Category type', '', 'array', '{\"default\":\"Default\",\"page\":\"Page\",\"article\":\"Article\",\"test\":\"Test\"}', '', '', '');
INSERT INTO `tb_config` VALUES (10, 'configgroup', 'dictionary', 'Config group', '', 'array', '{\"basic\":\"Basic\",\"email\":\"Email\",\"dictionary\":\"Dictionary\",\"user\":\"User\",\"example\":\"Example\"}', '', '', '');
INSERT INTO `tb_config` VALUES (11, 'mail_type', 'email', 'Mail type', '选择邮件发送方式', 'select', '1', '[\"Please select\",\"SMTP\",\"Mail\"]', '', '');
INSERT INTO `tb_config` VALUES (12, 'mail_smtp_host', 'email', 'Mail smtp host', '错误的配置发送邮件会导致服务器超时', 'string', 'smtp.qq.com', '', '', '');
INSERT INTO `tb_config` VALUES (13, 'mail_smtp_port', 'email', 'Mail smtp port', '(不加密默认25,SSL默认465,TLS默认587)', 'string', '465', '', '', '');
INSERT INTO `tb_config` VALUES (14, 'mail_smtp_user', 'email', 'Mail smtp user', '（填写完整用户名）', 'string', '10000', '', '', '');
INSERT INTO `tb_config` VALUES (15, 'mail_smtp_pass', 'email', 'Mail smtp password', '（填写您的密码）', 'string', 'password', '', '', '');
INSERT INTO `tb_config` VALUES (16, 'mail_verify_type', 'email', 'Mail vertify type', '（SMTP验证方式[推荐SSL]）', 'select', '2', '[\"None\",\"TLS\",\"SSL\"]', '', '');
INSERT INTO `tb_config` VALUES (17, 'mail_from', 'email', 'Mail from', '', 'string', '10000@qq.com', '', '', '');

-- ----------------------------
-- Table structure for tb_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `tb_auth_group_access`;
CREATE TABLE `tb_auth_group_access`  (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '会员ID',
  `group_id` int(10) UNSIGNED NOT NULL COMMENT '级别ID',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限分组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tb_auth_group_access
-- ----------------------------
INSERT INTO `tb_auth_group_access` VALUES (1, 1, 0, 0);

-- ----------------------------
-- Table structure for tb_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `tb_auth_group`;
CREATE TABLE `tb_auth_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父组别',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规则ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '分组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tb_auth_group
-- ----------------------------
INSERT INTO `tb_auth_group` VALUES (1, 0, 'Admin group', '*', 1490883540, 149088354, 'normal');
INSERT INTO `tb_auth_group` VALUES (2, 1, 'Second group', '13,14,16,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,40,41,42,43,44,45,46,47,48,49,50,55,56,57,58,59,60,61,62,63,64,65,1,9,10,11,7,6,8,2,4,5', 1490883540, 1505465692, 'normal');
INSERT INTO `tb_auth_group` VALUES (3, 2, 'Third group', '1,4,9,10,11,13,14,15,16,17,40,41,42,43,44,45,46,47,48,49,50,55,56,57,58,59,60,61,62,63,64,65,5', 1490883540, 1502205322, 'normal');
INSERT INTO `tb_auth_group` VALUES (4, 1, 'Second group 2', '1,4,13,14,15,16,17,55,56,57,58,59,60,61,62,63,64,65', 1490883540, 1502205350, 'normal');
INSERT INTO `tb_auth_group` VALUES (5, 2, 'Third group 2', '1,2,6,7,8,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34', 1490883540, 1502205344, 'normal');

-- ----------------------------
-- Table structure for tb_attachment
-- ----------------------------
DROP TABLE IF EXISTS `tb_attachment`;
CREATE TABLE `tb_attachment`  (
  `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员ID',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '图片帧数',
  `filesize` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小',
  `mimetype` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建日期',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `uploadtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传时间',
  `storage` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '附件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tb_admin
-- ----------------------------
DROP TABLE IF EXISTS `tb_admin`;
CREATE TABLE `tb_admin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败次数',
  `logintime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录时间',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `token` varchar(59) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tb_admin
-- ----------------------------
INSERT INTO `tb_admin` VALUES (1, 'admin', 'Admin', 'a08f36c6f5598b806b6f5d9e5c4bd902', '8be410', 'http://jsu173.oss-cn-shanghai.aliyuncs.com/upload/5538a316013d8fff2e744832c7c88b96.jpg', 'xiaogang.zhou@qq.com', 0, 1583905760, 1492186163, 1583905760, 'd18bf13f-3870-4be2-b5bc-04b4d496e5bf', 'normal');

-- ----------------------------
-- Table structure for tb_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `tb_admin_log`;
CREATE TABLE `tb_admin_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员日志表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
