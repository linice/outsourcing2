insert active_etc(type, code, value_prefix, value) values ('CODE', 'CS_CODE', 'Cs', '0');
insert active_etc(type, code, value_prefix, value) values ('CODE', 'USR_CODE', 'Usr', '0');
insert active_etc(type, code, value_prefix, value) values ('CODE', 'LP_CODE', 'Lp', '0');
insert active_etc(type, code, value_prefix, value) values ('CODE', 'EDT_CODE', 'Edt', '0');
insert active_etc(type, code, value_prefix, value) values ('CODE', 'MGR_CODE', 'Mgr', '0');
insert active_etc(type, code, value_prefix, value) values ('CODE', 'RESUME_CODE', 'Rsm', '0');

--日语水平
INSERT INTO etc VALUES ('JPL', 'JPL_NATIVE_LANG', '母语相当', 1);
INSERT INTO etc VALUES ('JPL', 'JPL_BASIC_COMM', '基本交流可', 2);
INSERT INTO etc VALUES ('JPL', 'JPL_SIMPLE_COMM', '简单交流可', 3);
INSERT INTO etc VALUES ('JPL', 'JPL_SIMPLE_KNOW', '认识', 4);
INSERT INTO etc VALUES ('JPL', 'JPL_NONE', '无要求', 5);
INSERT INTO etc VALUES ('JPL', 'JPL_LEVEL1', '1級以上', 6);
INSERT INTO etc VALUES ('JPL', 'JPL_LEVEL2', '2級以上', 7);
INSERT INTO etc VALUES ('JPL', 'JPL_LEVEL3', '3級以上', 8);

--担当范围
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_PRELIMINARY_DESIGN', '概要设计', 1);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_BASIC_DESIGN', '基本设计', 2);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_FUNCTIONAL_DESIGN', '机能设计', 3);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_DETAILED_DESIGN', '详细设计', 4);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_PROGRAMMING', '编程', 5);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_UT', '单体测试', 6);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_CT', '结合测试', 7);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_SUMM', '综合测试', 8);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_MAINTAIN', '维护', 9);
INSERT INTO etc VALUES ('CASE_RANGE', 'RANGE_USE', '运用', 10);

--结束时间
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_1', '1个月', 1);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_2', '2个月', 2);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_3', '3个月', 3);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_4', '4个月', 4);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_5', '5个月', 5);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_6', '6个月', 6);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_7', '7个月', 7);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_8', '8个月', 8);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_9', '9个月', 9);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_10', '10个月', 10);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_MONTH_11', '11个月', 11);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_YEAR_1', '1年', 12);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_YEAR_2', '2年', 13);
INSERT INTO etc VALUES ('END_RANGE', 'END_RANGE_YEAR_MORE', '长期', 14);

--单价
INSERT INTO etc VALUES ('UNIT_PRICE', 'UNIT_PRICE_EXP', '经验决定', 1);
INSERT INTO etc VALUES ('UNIT_PRICE', 'UNIT_PRICE_MIN', '最低', 2);
INSERT INTO etc VALUES ('UNIT_PRICE', 'UNIT_PRICE_MAX', '最高', 3);
INSERT INTO etc VALUES ('UNIT_PRICE', 'UNIT_PRICE_RANGE', '范围', 4);

--年龄要求
INSERT INTO etc VALUES ('AGE_REQ', 'AGE_REQ_NONE', '无', 1);
INSERT INTO etc VALUES ('AGE_REQ', 'AGE_REQ_MIN', '最小', 2);
INSERT INTO etc VALUES ('AGE_REQ', 'AGE_REQ_MAX', '最大', 3);
INSERT INTO etc VALUES ('AGE_REQ', 'AGE_REQ_RANGE', '范围', 4);

--加班费
INSERT INTO etc VALUES ('OVERTIME_PAY', 'OVERTIME_PAY_Y', '有', 1);
INSERT INTO etc VALUES ('OVERTIME_PAY', 'OVERTIME_PAY_N', '无', 2);
INSERT INTO etc VALUES ('OVERTIME_PAY', 'OVERTIME_PAY_C', '确认中', 3);

--国籍要求
INSERT INTO etc VALUES ('COUNTRY_REQ', 'COUNTRY_REQ_NONE', '无', 1);
INSERT INTO etc VALUES ('COUNTRY_REQ', 'COUNTRY_REQ_CHINA', '中国', 2);
INSERT INTO etc VALUES ('COUNTRY_REQ', 'COUNTRY_REQ_JP', '日本', 3);
INSERT INTO etc VALUES ('COUNTRY_REQ', 'COUNTRY_REQ_KOREA', '韩国', 4);
INSERT INTO etc VALUES ('COUNTRY_REQ', 'COUNTRY_REQ_OTHER', '其它', 5);

--延期可能
INSERT INTO etc VALUES ('CASE_DELAY', 'CASE_DELAY_Y', '有', 1);
INSERT INTO etc VALUES ('CASE_DELAY', 'CASE_DELAY_N', '无', 2);
INSERT INTO etc VALUES ('CASE_DELAY', 'CASE_DELAY_C', '不确定', 3);

--面试次数
INSERT INTO etc VALUES ('INTERVIEWER_NUM', 'INTERVIEWER_1', '1次', 1);
INSERT INTO etc VALUES ('INTERVIEWER_NUM', 'INTERVIEWER_2', '2次', 2);
INSERT INTO etc VALUES ('INTERVIEWER_NUM', 'INTERVIEWER_3', '3次以上', 3);

--期望工作地点
insert into etc (type, name, value, idx)values ('WORKING_PLACE_JP', 'WORKING_PLACE_BHD', '北海道', 1);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_JP', 'WORKING_PLACE_DB', '东北', 2);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_JP', 'WORKING_PLACE_GD', '关东', 3);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_JP', 'WORKING_PLACE_DH', '东海', 4);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_JP', 'WORKING_PLACE_BXY', '北信越', 5);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_JP', 'WORKING_PLACE_GX', '关西', 6);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_JP', 'WORKING_PLACE_SG', '中国.四国', 7);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_JP', 'WORKING_PLACE_JZCS', '九州.冲绳', 8);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_OTHER', 'WORKING_PLACE_CHINA', '中国', 1);
insert into etc (type, name, value, idx)values ('WORKING_PLACE_OTHER', 'WORKING_PLACE_OTHER', '其它', 2);

insert into etc (type, name, value, idx)values ('AKB', 'LPREG', 30, 1);
insert into etc (type, name, value, idx)values ('UNIT_PRICE_VIEW_SETTING', 'HIGH', 6, 1);
insert into etc (type, name, value, idx)values ('UNIT_PRICE_VIEW_SETTING', 'LOW', 6, 2);
