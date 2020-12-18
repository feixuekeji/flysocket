DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11)  NOT NULL DEFAULT '0' COMMENT '角色id',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码',
  `gender` tinyint(3) NOT NULL DEFAULT '0' COMMENT '性别1男2女0未知',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表';


DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `list_order` bigint(20) NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '名称',
  `action` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '1',
  `create_time` int(10) DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单表';

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '角色称呼',
  `create_time` int(11) DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色表';

DROP TABLE IF EXISTS `role_power`;
CREATE TABLE `role_power` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `role_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `menu_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '菜单ID',
  `list` tinyint(1) NOT NULL DEFAULT '0' COMMENT '查看',
  `add` tinyint(1) NOT NULL DEFAULT '0' COMMENT '新增',
  `edit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '编辑',
  `delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `print` tinyint(1) NOT NULL DEFAULT '0' COMMENT '打印',
  `detail` tinyint(1) NOT NULL DEFAULT '0' COMMENT '详情',
  `custom_one` tinyint(1) NOT NULL DEFAULT '0' COMMENT '自定义1',
  `custom_two` tinyint(1) NOT NULL DEFAULT '0' COMMENT '自定义2',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色-权限关联表';


