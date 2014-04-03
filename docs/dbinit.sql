DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL auto_increment,
  `role_code` varchar(32) NOT NULL default '' COMMENT '角色编码',
  `resource_code` int(11) NOT NULL default '0' COMMENT '资源ID',
  `enabled` enum('N','Y') NOT NULL default 'N' COMMENT '是否有效，N表示无权限，y为有权限',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限';
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `Id` int(11) NOT NULL auto_increment,
  `title` varchar(80) collate utf8_unicode_ci default NULL COMMENT '标题',
  `content` varchar(900) collate utf8_unicode_ci NOT NULL default '此处填写消息' COMMENT '内容',
  `sendtime` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '发送日期',
  `sendtolp` enum('Y','N') collate utf8_unicode_ci default 'N' COMMENT 'Y值时发送对象是法人',
  `sendtomember` enum('Y','N') collate utf8_unicode_ci default 'N' COMMENT 'Y值时发送对象是人才',
  `sendtoadmin` enum('Y','N') collate utf8_unicode_ci default 'N' COMMENT 'Y值时发送给管理员',
  `sended` enum('Y','N') collate utf8_unicode_ci NOT NULL default 'Y' COMMENT '是否草稿,N为未发送,是草稿',
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `mail_template`;
CREATE TABLE `mail_template` (
  `mt_id` int(10) NOT NULL auto_increment COMMENT 'id',
  `mt_name` varchar(50) NOT NULL default '' COMMENT '邮件名称',
  `mt_title` varchar(200) NOT NULL COMMENT '邮件标题',
  `mt_content` text NOT NULL COMMENT '邮件内容',
  `last_modify` datetime NOT NULL COMMENT '最后修改时间',
  PRIMARY KEY  (`mt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='邮件模板配置表';
DROP TABLE IF EXISTS `active_etc`;
CREATE TABLE `active_etc` (
  `type` varchar(32) NOT NULL default '' COMMENT '类型',
  `code` varchar(32) NOT NULL default '' COMMENT '名称',
  `value_prefix` varchar(16) NOT NULL default '' COMMENT '值前缀',
  `value` int(11) NOT NULL default '0' COMMENT '值',
  PRIMARY KEY  (`type`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `etc`;
CREATE TABLE `etc` (
  `type` varchar(32) NOT NULL default '' COMMENT '类型',
  `code` varchar(64) NOT NULL default '' COMMENT '编码',
  `name` varchar(32) NOT NULL default '' COMMENT '名称',
  `idx` int(11) NOT NULL default '0' COMMENT '排序',
  PRIMARY KEY  (`type`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `level` tinyint(4) default NULL COMMENT '1: INFO, 2: WARNING, 3: ERROR',
  `msg` varchar(1024) default NULL COMMENT 'log内容',
  `module` varchar(64) default NULL COMMENT '日志发生的模块',
  `class` varchar(64) default NULL COMMENT '日志发生的类',
  `func` varchar(64) default NULL COMMENT '日志发生的方法',
  `create_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8 COMMENT='logs, 记录Web运行日志';
DROP TABLE IF EXISTS `usr`;
CREATE TABLE `usr` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '' COMMENT '用户代号',
  `role_code` enum('','ADMIN','ASSIT','EDITOR','LP','EMP','MEMBER','GUEST') NOT NULL default '' COMMENT '角色编码，一个人只可以属于一个角色',
  `email` varchar(32) NOT NULL default '' COMMENT '邮箱',
  `email_consignee` varchar(32) NOT NULL default '' COMMENT '收件人邮箱',
  `passwd` char(32) NOT NULL default '' COMMENT '密码,md5()加密',
  `nickname` varchar(32) NOT NULL default '' COMMENT '用户昵称',
  `lp_address` varchar(128) character set utf8 collate utf8_unicode_ci NOT NULL COMMENT '法人公司地址',
  `lp_linkman` varchar(64) character set utf8 collate utf8_unicode_ci NOT NULL COMMENT '联系人',
  `website` varchar(200) character set utf8 collate utf8_unicode_ci NOT NULL COMMENT '网站',
  `fullname_p` varchar(64) NOT NULL default '' COMMENT '用户全名拼音',
  `fullname` varchar(64) NOT NULL default '' COMMENT '用户全名',
  `tel` varchar(32) NOT NULL default '' COMMENT '联系电话',
  `birthday` date NOT NULL default '1970-01-02' COMMENT '出生日期',
  `sex` enum('','F','M') NOT NULL default '' COMMENT '性别',
  `country` varchar(32) NOT NULL default '' COMMENT '现住所：国家',
  `province` varchar(32) NOT NULL default '' COMMENT '现住所：省',
  `is_receive_news` enum('N','Y') NOT NULL default 'N' COMMENT '是否接收电子邮件新闻',
  `referee` varchar(32) NOT NULL default '' COMMENT '推荐人',
  `status` varchar(32) NOT NULL default '' COMMENT '用户当前状态，记录用户当前的工作状态',
  `balance` int(11) NOT NULL default '0' COMMENT '用户剩余点数',
  `create_usr` varchar(32) NOT NULL default '' COMMENT '创建人',
  `create_time` timestamp NOT NULL default '1970-01-02 00:00:00' COMMENT '创建时间',
  `update_usr` varchar(32) NOT NULL default '' COMMENT '修改人',
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '修改时间',
  `last_login_time` timestamp NOT NULL default '1970-01-02 00:00:00' COMMENT '上次登陆时间',
  `enabled` enum('','N','Y') NOT NULL default '' COMMENT '用户帐号可用状态，N：不可用，Y：可用，''：申请中',
  `employcount` int(11) NOT NULL COMMENT '员工数',
  `highlightdate` varchar(50) NOT NULL COMMENT '高亮日期',
  `applydate` date NOT NULL COMMENT '应聘查看日期',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `usr_email_ui` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
--法人可开通服务定义
DROP TABLE IF EXISTS `lp_service`;
CREATE TABLE `lp_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL COMMENT '服务名',
  `code` varchar(32) NOT NULL DEFAULT '' COMMENT '服务编码',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '法人可开通服务定义';
--法人开通服务信息
DROP TABLE IF EXISTS `lp_service_setting`;
CREATE TABLE `lp_service_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lp_code` varchar(32) NOT NULL COMMENT '法人编码',
  `service_code` varchar(32) NOT NULL DEFAULT '' COMMENT '服务编码',
  `effective` enum('N','Y') NOT NULL DEFAULT 'N' COMMENT '是否有效',
  `start_date` date DEFAULT NULL COMMENT '服务开始时间',
  `end_date` date DEFAULT NULL COMMENT '服务结束时间',
  `is_current` enum('N','Y') NOT NULL DEFAULT 'Y' COMMENT '是否当前使用的服务',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '法人开通服务信息';
--入金纪录表
DROP TABLE IF EXISTS `purchase_record`;
CREATE TABLE `purchase_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_code` varchar(32) NOT NULL DEFAULT '' COMMENT '服务编码',
  `money` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT '入金金额',
  `create_date` timestamp NULL DEFAULT NULL COMMENT '入金日期',
  `akb_record_id` int(10) NOT NULL COMMENT 'AKB记录ID',
  `amount` int(10) NOT NULL COMMENT '购买AKB数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '入金纪录表';
--系统金币使用纪录
DROP TABLE IF EXISTS `akb_record`;
CREATE TABLE `akb_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lp_code` varchar(32) NOT NULL COMMENT '法人编码',
  `inout_flag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0进，1出',
  `amount` int(10) NOT NULL DEFAULT '0' COMMENT '使用AKB数量',
  `case_akb_record_id` int(10) NOT NULL DEFAULT '0' COMMENT '案件竞价消耗AKB纪录ID',
  `usr_akb_record_id` int(10) NOT NULL DEFAULT '0' COMMENT '人才竞价消耗AKB纪录ID',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '理由',
  `modify_date` timestamp NULL DEFAULT NULL COMMENT '入出点日期',
  `remain` int(10) NOT NULL DEFAULT '0' COMMENT '剩余AKB数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '系统金币使用纪录';
--案件竞价消耗金币纪录
DROP TABLE IF EXISTS `case_akb_record`;
CREATE TABLE `case_akb_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lp_code` varchar(32) NOT NULL COMMENT '法人编码',
  `case_code` varchar(32) NOT NULL COMMENT '案件编码',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '竞价点数',
  `start_date` date DEFAULT NULL COMMENT '竞价开始日期',
  `end_date` date DEFAULT NULL COMMENT '竞价结束日期',
  `usr` varchar(32) DEFAULT NULL COMMENT '竞价操作者',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '案件竞价消耗金币纪录';
--人才竞价消耗金币纪录
DROP TABLE IF EXISTS `usr_akb_record`;
CREATE TABLE `usr_akb_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lp_code` varchar(32) NOT NULL COMMENT '法人编码',
  `usr_code` varchar(32) NOT NULL COMMENT '人才编码',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '竞价点数',
  `start_date` date DEFAULT NULL COMMENT '竞价开始日期',
  `end_date` date DEFAULT NULL COMMENT '竞价结束日期',
  `usr` varchar(32) DEFAULT NULL COMMENT '竞价操作者',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '人才竞价消耗金币纪录';
--案件
DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '案件名',
  `code` varchar(32) NOT NULL DEFAULT '' COMMENT '案件编号',
  `working_place_detail` varchar(256) NOT NULL DEFAULT '' COMMENT '工作地点详细说明',
  `working_place` varchar(32) NOT NULL DEFAULT '' COMMENT '工作地点 参考etc',
  `careers` varchar(256) NOT NULL DEFAULT '' COMMENT '职种 参考etc 有多个值以逗号分隔',
  `industries` varchar(256) NOT NULL DEFAULT '' COMMENT '业务领域 参考etc 有多个值以逗号分隔',
  `languages` varchar(256) NOT NULL DEFAULT '' COMMENT '经验语言 参考etc 有多个值以逗号分隔',
  `jpl` varchar(32) NOT NULL DEFAULT '' COMMENT '日语水平要求 参考etc',
  `case_range_start` varchar(32) NOT NULL DEFAULT '' COMMENT '担当范围开始 参考etc',
  `case_range_end` varchar(32) NOT NULL DEFAULT '' COMMENT '担当范围结束 参考etc',
  `business_req` varchar(256) NOT NULL DEFAULT '' COMMENT '业务要求',
  `technical_req` varchar(256) NOT NULL DEFAULT '' COMMENT '技术要求',
  `start_date` date DEFAULT NULL COMMENT '案件开始日期',
  `end_date` date DEFAULT NULL COMMENT '案件结束日期',
  `end_range` varchar(32) NOT NULL DEFAULT '' COMMENT '案件期间 参考etc',
  `delay` enum('','N','Y') NOT NULL DEFAULT '' COMMENT '可能延期',
  `unit_price` varchar(32) NOT NULL DEFAULT '' COMMENT '单价 参考etc',
  `unit_price_low` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT '最低单价',
  `unit_price_high` decimal(18,2) NOT NULL DEFAULT '0.00' COMMENT '最高单价',
  `unit_price_low_view` varchar(32) NOT NULL DEFAULT '' COMMENT '显示最低单价',
  `unit_price_high_view` varchar(32) NOT NULL DEFAULT '' COMMENT '显示最高单价',
  `overtime_pay` enum('','N','Y') NOT NULL DEFAULT '' COMMENT '是否有加班费',
  `overtime_pay_detail` varchar(256) NOT NULL DEFAULT '' COMMENT '加班费详细说明',
  `interviewer` int(11) NOT NULL DEFAULT '0' COMMENT '面试次数',
  `age_req` enum('','N','Y') NOT NULL DEFAULT '' COMMENT '是否有年龄要求',
  `age_req_begin` varchar(32) NOT NULL DEFAULT '' COMMENT '详细的年龄要求 A~B格式',
  `age_req_end` varchar(32) NOT NULL DEFAULT '' COMMENT '详细的年龄要求 A~B格式',
  `country_req` varchar(32) NOT NULL DEFAULT '' COMMENT '国籍要求 参考etc',
  `timeliness` date DEFAULT NULL COMMENT '时效性',
  `visibility` varchar(32) NOT NULL DEFAULT '' COMMENT '案件可见度 参考etc',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '备注',
  `type` enum('U','R','C','D') NOT NULL DEFAULT 'U' COMMENT 'U:草稿, R:已发布, C:已关闭, D:已删除',
  `lp_code` varchar(32) NOT NULL COMMENT '发布案件的法人编码',
  `delete_date` timestamp NULL DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `close_date` timestamp NULL DEFAULT NULL,
  `release_date` timestamp NULL DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT '' COMMENT '案件当前状态',
  `viewers` int(11) NOT NULL DEFAULT '0' COMMENT '浏览人数',
  `akb` int(11) NOT NULL DEFAULT '0' COMMENT '案件消耗AKB',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT '案件';
--案件关注
DROP TABLE IF EXISTS `case_attention`;
CREATE TABLE `case_attention` (
  `case_code` varchar(32) NOT NULL DEFAULT '' COMMENT '案件编号',
  `usr_code` varchar(32) NOT NULL DEFAULT '' COMMENT '用户编号/法人编号',
  `create_date` timestamp NULL DEFAULT NULL COMMENT '关注操作时间',
  PRIMARY KEY (`case_code`, `usr_code`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '案件关注';
--面试调整
CREATE TABLE `interview` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caseapply_id` int(11) NOT NULL DEFAULT '0' COMMENT '应聘表ID',
  `expect_interview_date1` date DEFAULT NULL COMMENT '期望面试时间1',
  `expect_interview_date2` date DEFAULT NULL COMMENT '期望面试时间2',
  `expect_interview_date3` date DEFAULT NULL COMMENT '期望面试时间3',
  `real_interview_date` date DEFAULT NULL COMMENT '真正面试时间，调整后的面试时间',
  `interview_linkman` varchar(128) NOT NULL DEFAULT '' COMMENT '联系人',
  `interview_telephone` varchar(32) NOT NULL DEFAULT '' COMMENT '联系电话',
  `interview_place` varchar(256) NOT NULL DEFAULT '' COMMENT '面试地点',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='面试调整';
--面试结果
CREATE TABLE `interview_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caseapply_id` int(11) NOT NULL DEFAULT '0' COMMENT '应聘表ID',
  `result` enum('','OK','NG') NOT NULL DEFAULT '' COMMENT '面试结果',
  `reason` varchar(32) NOT NULL DEFAULT '' COMMENT 'NG原因',
  `admission_place` varchar(256) DEFAULT '' COMMENT '入场地点',
  `admission_linkman` varchar(128) DEFAULT '' COMMENT '入场联系人',
  `admission_telephone` varchar(32) DEFAULT '' COMMENT '入场联系电话',
  `admission_date` date DEFAULT NULL COMMENT '入场时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='面试结果';
--员工应聘
CREATE TABLE `case_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` int(10) NOT NULL DEFAULT '0' COMMENT '案件ID',
  `usr_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID/法人ID',
  `resume_id` int(10) NOT NULL DEFAULT '0' COMMENT '简历ID',
  `emp_id` int(10) NOT NULL DEFAULT '0' COMMENT '员工ID 法人的员工应聘',
  `status` varchar(32) NOT NULL DEFAULT '' COMMENT '应聘状态',
  `is_detail` enum('','Y','N') NOT NULL DEFAULT 'N' COMMENT '是否应聘明细',
  `apply_time` date DEFAULT NULL COMMENT '应聘时间',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '备注',
  `reason` varchar(16) NOT NULL DEFAULT '' COMMENT '原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
--员工应聘纪录
CREATE TABLE `case_apply_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `caseapply_id` int(10) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT '' COMMENT '应聘状态',
  `is_current` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '是否当前状态',
  `updated_time` date DEFAULT NULL COMMENT '状态更新时间',
  `updated_usr` int(10) NOT NULL DEFAULT '0' COMMENT '状态更新用户',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '状态更新备注',
  `reason` varchar(16) NOT NULL DEFAULT '' COMMENT '原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
