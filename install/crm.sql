CREATE TABLE `channel` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '渠道名，与dict type = 6一至',
  `en_name` varchar(20) NOT NULL DEFAULT '',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '1-实时推 2-定时拉',
  `config` text COMMENT '渠道配置',
  `token` text,
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间',
  `remark` varchar(512) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `cost` float NOT NULL DEFAULT '0' COMMENT '渠道成本'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道';

CREATE TABLE `customer` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile_jiami` varchar(128) NOT NULL DEFAULT '' COMMENT '加密手机号',
  `city` int(11) NOT NULL DEFAULT '0' COMMENT '城市',
  `star` int(11) NOT NULL DEFAULT '0' COMMENT '星级',
  `source` varchar(11) DEFAULT '0' COMMENT '渠道来源',
  `user_from` int(11) NOT NULL DEFAULT '1' COMMENT '客户来源',
  `age` int(11) NOT NULL DEFAULT '0' COMMENT '年龄',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '申请金额',
  `sex` int(11) NOT NULL DEFAULT '0' COMMENT '客户性别',
  `live_province` int(11) NOT NULL DEFAULT '0' COMMENT '居住-省',
  `live_city` int(11) NOT NULL DEFAULT '0' COMMENT '居住-市',
  `live_county` int(11) NOT NULL DEFAULT '0' COMMENT '居住-县',
  `live_address` varchar(200) NOT NULL DEFAULT '' COMMENT '居住-地址',
  `household_province` int(11) NOT NULL DEFAULT '0' COMMENT '户籍-省',
  `household_city` int(11) NOT NULL DEFAULT '0' COMMENT '户籍-市',
  `household_county` int(11) NOT NULL DEFAULT '0' COMMENT '户籍-县',
  `household_address` varchar(200) NOT NULL DEFAULT '' COMMENT '户籍-地址',
  `marry` int(11) NOT NULL DEFAULT '0' COMMENT '婚姻状况',
  `work` int(11) NOT NULL DEFAULT '0' COMMENT '职业',
  `company` varchar(200) NOT NULL DEFAULT '' COMMENT '单位',
  `income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '年收入',
  `house` int(11) NOT NULL DEFAULT '0' COMMENT '房产信息',
  `car` int(11) NOT NULL DEFAULT '0' COMMENT '车辆信息',
  `policy` int(11) NOT NULL DEFAULT '0' COMMENT '保单信息',
  `funds` int(11) NOT NULL DEFAULT '0' COMMENT '公积金',
  `insurance` int(11) NOT NULL DEFAULT '0' COMMENT '社保信息',
  `wage` int(11) NOT NULL DEFAULT '0' COMMENT '打卡工资',
  `credit` int(11) NOT NULL DEFAULT '0' COMMENT '信用信息',
  `qualification` varchar(2000) NOT NULL DEFAULT '' COMMENT '资质描述',
  `remark` varchar(2000) NOT NULL DEFAULT '' COMMENT '备注信息',
  `follow_time` int(11) NOT NULL DEFAULT '0' COMMENT '跟进时间',
  `follow_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '跟进人ID',
  `follow_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `assign_time` int(11) NOT NULL DEFAULT '0' COMMENT '分配时间',
  `household_area` int(11) NOT NULL DEFAULT '0' COMMENT '地区',
  `live_area` int(11) NOT NULL DEFAULT '0' COMMENT '地区',
  `apply_time` int(11) NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `add_user_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  `important` int(11) NOT NULL DEFAULT '0' COMMENT '重要客户 0-否 1-是',
  `lock` int(11) NOT NULL DEFAULT '0' COMMENT '锁定',
  `channel_id` varchar(100) NOT NULL DEFAULT '' COMMENT '渠道用户id',
  `first_follow_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '第一个跟进的人',
  `mobile_md5` varchar(100) NOT NULL DEFAULT '',
  `introid` int(11) NOT NULL DEFAULT '0',
  `cost` float NOT NULL DEFAULT '0',
  `family` int(11) NOT NULL DEFAULT '0',
  `credit_detail` int(11) NOT NULL DEFAULT '0',
  `anum` int(11) NOT NULL DEFAULT '0',
  `giveup_time` int(11) NOT NULL DEFAULT '0',
  `popup` int(11) NOT NULL DEFAULT '0',
  `car_info` varchar(64) NOT NULL DEFAULT '',
  `house_type` varchar(100) DEFAULT '' COMMENT '房屋类型',
  `house_status` varchar(100) NOT NULL DEFAULT '' COMMENT '房屋状态 ',
  `company_type` varchar(100) NOT NULL DEFAULT '' COMMENT '单位性质',
  `deposit_amount` varchar(100) NOT NULL DEFAULT '' COMMENT '合计公积金月缴额（单位和个人）',
  `cont_last_times` varchar(100) NOT NULL DEFAULT '' COMMENT '连续缴存月数',
  `is_follow` int(11) NOT NULL DEFAULT '0',
  `mobile` varchar(120) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户信息';

CREATE TABLE `customer_backs` (
  `id` int(11) UNSIGNED NOT NULL,
  `custom_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户id',
  `apply_date` int(11) NOT NULL DEFAULT '0' COMMENT '申请时间',
  `apply_amount` int(11) NOT NULL DEFAULT '0' COMMENT '申请金额',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '放款时间',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '放款金额',
  `fee` float NOT NULL DEFAULT '0' COMMENT '手续费收入',
  `follow_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '跟进人id',
  `oper_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人id',
  `real_amount` float NOT NULL DEFAULT '0' COMMENT '收入',
  `remark` varchar(1024) NOT NULL DEFAULT '' COMMENT '备注',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间',
  `status` int(11) NOT NULL DEFAULT '1',
  `cost` float NOT NULL DEFAULT '0' COMMENT '成本',
  `hetong` varchar(100) NOT NULL DEFAULT '' COMMENT '合同编号\r\n',
  `quanzheng` int(11) NOT NULL DEFAULT '0' COMMENT '权证',
  `product_id` varchar(100) NOT NULL DEFAULT '' COMMENT '产品id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跟进日志';


CREATE TABLE `customer_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户id',
  `type` int(11) NOT NULL DEFAULT '0',
  `before` varchar(256) NOT NULL DEFAULT '' COMMENT '旧值',
  `after` varchar(256) NOT NULL DEFAULT '' COMMENT '新值',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人id',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1-有效 0-无效',
  `remark` varchar(2000) NOT NULL DEFAULT '0' COMMENT '备注',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跟进日志';


CREATE TABLE `customer_remark_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户id',
  `remark` varchar(2048) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人id',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1-有效 0-无效',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='备注日志';


CREATE TABLE `customer_rule_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名',
  `config` varchar(2000) NOT NULL DEFAULT '' COMMENT '配置',
  `status` int(11) NOT NULL DEFAULT '1',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间',
  `is_del` int(11) NOT NULL DEFAULT '0',
  `context` text,
  `myorder` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配置';


INSERT INTO `customer_rule_config` (`id`, `name`, `config`, `status`, `create_time`, `update_time`, `is_del`, `context`, `myorder`) VALUES
(1, '新数据分配规则', '[]', 1, '2022-07-30 13:35:46', '2023-04-03 22:33:41', 0, '', 1),
(2, '客户自动流入公共池规则', '{\"hour\":\"0.5\"}', 0, '2022-07-30 13:35:46', '2022-12-08 09:49:47', 0, '', 2),
(3, '客户自动流入公共池规则', '{\"values\":[1,3,4,6,5,2,7,8,9,12],\"type\":\"1\",\"day\":\"3\"}', 1, '2022-07-30 13:35:46', '2023-01-02 16:51:55', 0, '', 3),
(4, '客户自动流入公共池规则', '{\"values\":[],\"type\":\"1\",\"day\":\"\"}', 0, '2022-07-30 13:35:46', '2022-11-02 22:12:14', 0, '', 4),
(5, '客户自动流入公共池规则', '{\"values\":[],\"type\":\"2\",\"day\":\"\"}', 0, '2022-07-30 13:35:46', '2022-11-02 22:12:14', 0, '', 5),
(6, '客户自动流入公共池规则', '{\"values\":[],\"type\":\"2\",\"day\":\"\"}', 0, '2022-07-30 13:35:46', '2022-11-02 22:12:14', 0, '', 6),
(7, '客户自动流入公共池规则', '{\"values\":[],\"type\":\"2\",\"day\":\"\"}', 0, '2022-07-30 13:35:46', '2022-11-02 22:12:14', 0, '', 7),
(8, '公共池客户领取规则', '{\"type\":2,\"day\":\"7\"}', 0, '2022-07-30 13:35:46', '2022-11-02 22:12:14', 0, '', 8),
(9, '公共池客户领取规则', '{\"type\":1}', 0, '2022-07-30 13:35:46', '2022-11-02 22:12:14', 0, '', 9),
(10, '公海数据分配规则', '{\"day\":\"\"}', 0, '2022-07-30 13:35:46', '2022-11-02 22:12:14', 0, '', 10),
(11, '客户锁定规则', '{\"day\":\"15\",\"num\":\"1\"}', 1, '2022-07-30 13:35:46', '2022-12-08 10:32:04', 0, '', 11),
(12, '客户数量规则', '{\"num\":\"300\"}', 0, '2022-07-30 13:35:46', '2022-12-08 09:49:47', 0, '', 12),
(13, '客户自动流入公共池规则', '{\"hour\":\"3\"}', 0, '2022-07-30 13:35:46', '2022-11-02 22:42:17', 0, '', 2.1),
(14, '客户数量规则', '{\"num\":\"300\"}', 0, '2022-07-30 13:35:46', '2022-12-08 09:49:47', 0, '', 12),
(15, '客户数量规则', '{\"num\":\"122\"}', 0, '2022-07-30 13:35:46', '2023-04-16 17:30:48', 0, '', 12);

CREATE TABLE `dict` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '类型',
  `tid` int(11) NOT NULL DEFAULT '0' COMMENT 'id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名字',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间',
  `status` int(11) NOT NULL DEFAULT '1',
  `groups` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据字典';

INSERT INTO `dict` (`id`, `type`, `tid`, `name`, `create_time`, `update_time`, `status`, `groups`) VALUES
(1, 1, 1, '北京', '2022-06-19 14:37:58', '2022-07-09 08:33:54', 1, 1),
(2, 1, 2, '上海', '2022-06-19 14:37:58', '2022-07-09 08:33:56', 1, 1),
(3, 2, 1, '职员', '2022-06-19 14:39:10', '2022-07-09 08:33:58', 1, 1),
(4, 2, 2, '企业主', '2022-06-19 14:39:10', '2022-07-09 08:34:00', 1, 1),
(5, 3, 1, '未受理', '2022-06-19 14:37:58', '2022-10-29 15:05:05', 1, 1),
(6, 4, 5, '5星', '2022-07-09 08:49:10', '2022-07-09 08:50:21', 1, 1),
(7, 4, 4, '4星', '2022-07-09 08:49:10', '2022-07-09 08:49:10', 1, 1),
(11, 4, 3, '3星', '2022-07-09 08:49:59', '2022-07-09 08:49:59', 1, 1),
(12, 4, 2, '2星', '2022-07-09 08:49:59', '2022-07-09 08:49:59', 1, 1),
(13, 4, 1, '1星', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(14, 5, 1, '新数据', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(16, 5, 2, '再分配', '2022-07-09 08:50:13', '2022-10-23 10:04:13', 1, 1),
(17, 5, 3, '公共池分配', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(18, 5, 4, '自己录入', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(19, 6, 1, '12', '2022-07-09 08:50:13', '2023-04-24 16:47:58', 1, 1),
(21, 6, 2, '抖音', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(22, 7, 1, '有房', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(28, 7, 2, '有车', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(29, 7, 3, '有保单', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(30, 7, 4, '有社保', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(31, 7, 5, '有公积金', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(32, 7, 6, '无逾期', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(33, 8, 1, '客户回访', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(37, 8, 2, '客户邀约', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(38, 8, 3, '客户上门', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(39, 8, 4, '提交资料', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(40, 3, 2, '待跟进', '2022-06-19 14:37:58', '2022-10-29 15:05:11', 1, 1),
(41, 1, 3, '深圳', '2022-06-19 14:37:58', '2022-07-09 08:33:56', 1, 1),
(42, 1, 4, '重庆', '2022-06-19 14:37:58', '2022-07-09 08:33:56', 1, 1),
(43, 1, 5, '天津', '2022-06-19 14:37:58', '2022-07-09 08:33:56', 1, 1),
(44, 1, 6, '海口', '2022-06-19 14:37:58', '2022-07-09 08:33:56', 1, 1),
(45, 6, 3, '微博', '2022-07-09 08:50:13', '2022-07-09 08:50:24', 1, 1),
(47, 3, 3, '电话未接通', '2022-06-19 14:37:58', '2022-10-29 15:05:16', 1, 1),
(48, 3, 4, '意向客户', '2022-06-19 14:37:58', '2022-10-29 15:05:21', 1, 1),
(49, 3, 5, '资质不符', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),
(51, 5, 5, '公共池认领', '2022-07-09 08:50:13', '2022-10-23 10:04:13', 1, 1),
(57, 5, 6, '转介绍', '2022-07-09 08:50:13', '2022-10-23 10:04:13', 1, 1),
(58, 3, 6, '邀约中', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),
(59, 3, 7, '已约见', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),
(60, 3, 8, '待签约', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),
(61, 3, 9, '已签约', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),
(62, 3, 10, '进件审批', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),
(63, 3, 11, '审批通过', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),
(64, 3, 12, '审批否决', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),
(65, 3, 13, '成功放款', '2022-06-19 14:37:58', '2022-10-29 15:05:27', 1, 1),

-- --------------------------------------------------------

--
-- 表的结构 `notice`
--

CREATE TABLE `notice` (
  `id` int(11) UNSIGNED NOT NULL,
  `custom_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户id',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '类型',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
  `follow_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '跟进人id',
  `remark` varchar(2000) NOT NULL DEFAULT '' COMMENT '备注',
  `is_read` int(11) DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跟进日志';

-- --------------------------------------------------------

--
-- 表的结构 `product`
--

CREATE TABLE `product` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '名称',
  `bank` varchar(200) NOT NULL DEFAULT '' COMMENT '机构',
  `amount` float NOT NULL DEFAULT '0' COMMENT '额度',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0-离线 1-在线',
  `remark` varchar(20000) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '创建人',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间',
  `amount2` float NOT NULL DEFAULT '0',
  `amount1` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品';

-- --------------------------------------------------------

--
-- 表的结构 `system_field`
--

CREATE TABLE `system_field` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '分组',
  `name_cn` varchar(50) NOT NULL DEFAULT '' COMMENT '中文名',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统字段';

--
-- 转存表中的数据 `system_field`
--

INSERT INTO `system_field` (`id`, `name`, `type`, `name_cn`, `create_time`, `update_time`) VALUES
(3, 'name', 1, '姓名', '2022-06-19 14:42:25', '2022-06-19 14:42:25'),
(4, 'mobile', 1, '手机号', '2022-06-19 14:42:25', '2022-06-19 14:42:25'),
(11, 'city', 1, '城市', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(12, 'star', 2, '星级', '2022-07-09 17:39:48', '2022-07-16 16:53:32'),
(13, 'source', 3, '渠道来源', '2022-07-09 17:39:48', '2022-07-16 16:53:35'),
(14, 'user_from', 3, '客户来源', '2022-07-09 17:39:48', '2022-07-16 16:53:37'),
(15, 'age', 1, '年龄', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(16, 'amount', 1, '申请金额', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(17, 'sex', 1, '客户性别', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(26, 'marry', 1, '婚姻状况', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(27, 'work', 1, '职业', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(28, 'company', 1, '单位', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(29, 'income', 1, '年收入', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(30, 'house', 2, '房产信息', '2022-07-09 17:39:48', '2022-07-16 16:54:20'),
(31, 'car', 2, '车辆信息', '2022-07-09 17:39:48', '2022-07-16 16:54:23'),
(32, 'policy', 2, '保单信息', '2022-07-09 17:39:48', '2022-07-16 16:54:25'),
(33, 'funds', 2, '公积金', '2022-07-09 17:39:48', '2022-07-16 16:54:29'),
(34, 'insurance', 1, '社保信息', '2022-07-09 17:39:48', '2022-07-09 17:39:48'),
(35, 'wage', 2, '打卡工资', '2022-07-09 17:39:48', '2022-07-16 16:54:50'),
(36, 'credit', 2, '信用信息', '2022-07-09 17:39:48', '2022-07-16 16:54:47'),
(39, 'follow_time', 3, '跟进时间', '2022-07-09 17:39:48', '2022-07-16 16:54:40'),
(40, 'follow_user_id', 3, '跟进人ID', '2022-07-09 17:39:48', '2022-07-16 16:54:54'),
(41, 'follow_status', 3, '状态', '2022-07-09 17:39:48', '2022-07-16 16:54:57'),
(42, 'assign_time', 3, '分配时间', '2022-07-09 17:39:48', '2022-07-16 16:54:59'),
(45, 'apply_time', 3, '申请时间', '2022-07-09 17:39:48', '2022-07-16 16:55:07');

-- --------------------------------------------------------

--
-- 表的结构 `system_log`
--

CREATE TABLE `system_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '类型',
  `obj_id` int(11) NOT NULL DEFAULT '0',
  `before` varchar(1024) NOT NULL DEFAULT '',
  `after` varchar(1024) NOT NULL DEFAULT '',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '备注',
  `user_id` int(11) NOT NULL DEFAULT '0',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统用户';

-- --------------------------------------------------------

--
-- 表的结构 `system_right`
--

CREATE TABLE `system_right` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `name_cn` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(50) NOT NULL DEFAULT '' COMMENT '后端url',
  `router` varchar(50) NOT NULL DEFAULT '' COMMENT '前段router',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '类型 1-菜单 2-功能',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级id',
  `orders` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(11) NOT NULL DEFAULT '1',
  `hide_in_menu` int(11) NOT NULL DEFAULT '0' COMMENT '是否隐藏\r\n',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统权限';

--
-- 转存表中的数据 `system_right`
--

INSERT INTO `system_right` (`id`, `name`, `name_cn`, `url`, `router`, `icon`, `type`, `parent_id`, `orders`, `status`, `hide_in_menu`, `create_time`, `update_time`) VALUES
(1, 'menu.system', '系统管理', 'system', 'User', 'icon-command', 1, 0, 0, 1, 0, '2022-06-19 14:44:19', '2022-09-11 23:16:02'),
(2, 'menu.system.user', '用户管理', 'system/user/list', 'User', '', 1, 1, 3, 1, 0, '2022-06-19 14:45:15', '2022-07-06 11:37:25'),
(3, 'menu.system.role', '角色管理', 'system/role/list', 'Role', '', 1, 1, 2, 1, 0, '2022-06-19 14:45:46', '2022-07-06 11:37:28'),
(4, 'menu.system.team', '团队管理', 'system/team/list', 'Team', '', 1, 1, 1, 1, 0, '2022-06-19 14:49:09', '2022-07-06 21:51:37'),
(5, 'menu.dashboard', '首页', 'dashboard', 'Workplace', 'icon-home\r\n', 1, 0, 11, 1, 0, '2022-06-25 09:13:09', '2022-10-29 15:43:48'),
(6, 'menu.customer', '客户管理', 'customer', 'CustomerList', 'icon-user-group', 1, 0, 9, 1, 0, '2022-06-25 09:13:09', '2022-09-11 23:15:11'),
(7, 'menu.customer.importcustomer', '重要客户', 'dashboard', 'CustomerImportList', '', 1, 6, 8, 1, 0, '2022-06-25 09:13:34', '2022-07-15 21:46:30'),
(8, 'menu.customer.innercustomer', '内部流转客户', 'customer', 'CustomerInnerList', '', 1, 6, 7, 1, 0, '2022-06-25 09:13:34', '2022-07-15 21:46:35'),
(9, 'menu.customer.newcustomer', '新客户', 'dashboard', 'CustomerNewList', '', 1, 6, 6, 1, 0, '2022-06-25 09:14:00', '2022-07-15 21:46:39'),
(10, 'menu.operate', '运营管理', 'customer', 'Follow', 'icon-computer', 1, 0, 4, 1, 0, '2022-06-25 09:14:00', '2022-09-11 23:15:47'),
(12, 'menu.customer.genjin', '全部客户', 'custom/list', 'CustomerList', '', 1, 6, 9, 1, 0, '2022-06-25 09:15:30', '2022-07-09 21:18:45'),
(20, 'menu.operate.follow', '客户流转管理', 'operate/assign/config', 'Follow', '', 1, 10, 9, 1, 0, '2022-06-25 09:18:25', '2022-07-30 13:55:04'),
(21, 'menu.operate.assign', '客户分配管理', 'system/user/list', 'Assign', '', 1, 10, 8, 1, 0, '2022-06-25 09:18:25', '2022-07-19 12:47:43'),
(23, 'menu.system.user.edit', '编辑', 'system/user/edit', 'Useredit', '', 1, 3, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:40:28'),
(24, 'menu.system.user.preview', '查看', 'system/user/info', 'Userpreview', '', 1, 2, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:40:22'),
(25, 'menu.system.user.lock', '冻结', 'system/user/lock', 'Userlock', '', 1, 2, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:40:17'),
(26, 'menu.system.user.role', '权限设置', 'system/user/role', 'Userrole', '', 1, 2, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 13:39:43'),
(27, 'menu.system.role.edit', '编辑', 'system/role/edit', 'Roleedit', '', 1, 3, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:40:31'),
(28, 'menu.system.role.lock', '冻结', 'system/role/lock', 'Rolelock', '', 1, 3, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:40:33'),
(29, 'menu.system.role.previe', '查看', 'system/role/info', 'Rolepreview', '', 1, 3, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:40:35'),
(30, 'menu.system.team.edit', '编辑', 'system/team/edit', 'Teamedit', '', 1, 4, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:41:17'),
(31, 'menu.system.team.perview', '查看', 'system/team/info', 'Teampreview', '', 1, 4, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:41:20'),
(32, 'menu.customer.genjin.customedit', '录入客户', 'custom/edit', 'CustomerEdit', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(33, 'menu.customer.genjin.custompreivew', '查看客户', 'custom/info', 'CustomerPreview', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(34, 'menu.customer.genjin.customassign', '分配客户', 'custom/assign', 'CustomerAssign', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(35, 'menu.customer.genjin.customassignlist', '分配客户历史', 'custom/assignlist', 'CustomerAssignList', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(36, 'menu.customer.genjin.customfollowlist', '跟进客户历史', 'custom/followlist', 'CustomerFollowList', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(37, 'menu.customer.genjin.customstarlist', '星级变更历史', 'custom/starlist', 'CustomerStarList', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(38, 'menu.customer.genjin.customlahei', '拉黑客户', 'custom/lahei', 'CustomerLahei', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(39, 'menu.customer.genjin.custombatchupload', '批量上传客户', 'custom/upload', 'CustomerBatchUploadNewPool', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:16'),
(40, 'menu.newpool', '新数据公共池', 'custom/newpool', 'CustomerNewpool', 'icon-user', 1, 0, 8, 1, 0, '2022-06-19 14:44:19', '2022-07-15 22:11:23'),
(41, 'menu.pool', '公共池客户', 'custom/pool', 'CustomerPool', 'icon-public', 1, 0, 7, 1, 0, '2022-06-19 14:44:19', '2022-07-15 22:11:53'),
(42, 'menu.custom.unvalid', '无效客户', 'custom/unvalid', 'CustomerUnvalid', 'icon-bug', 1, 0, 6, 1, 0, '2022-06-19 14:44:19', '2022-07-15 22:12:13'),
(43, 'menu.system.user.resetpwd', '重置密码', 'system/user/resetpwd', 'UserResetpwd', '', 1, 3, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:40:28'),
(44, 'menu.customer.genjin.customedit', '跟进客户', 'custom/edit', 'CustomerFollow', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(45, 'menu.customer.genjin.customget', '认领客户', 'custom/get', 'CustomerGetNewPool', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:23'),
(46, 'menu.operate.assignperview', '查看', 'operate/assign/info', 'Assignpreview', '', 1, 21, 8, 1, 1, '2022-06-25 09:18:25', '2022-07-19 12:49:59'),
(47, 'menu.operate.assignedit', '编辑数据权限', 'operate/assign/edit', 'Assignedit', '', 1, 21, 8, 1, 1, '2022-06-25 09:18:25', '2022-07-19 12:50:01'),
(48, 'menu.operate.assignlog', '查看日志', 'operate/assign/log', 'Assignlog', '', 1, 21, 8, 1, 1, '2022-06-25 09:18:25', '2022-07-19 12:50:02'),
(49, 'menu.customer.genjin.customerimportant', '标记重要', 'custom/important', 'CustomerImportant', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-19 19:44:37'),
(50, 'menu.customer.genjin.customerlock', '锁定客户\r\n', 'custom/lock', 'CustomerLock', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-19 19:44:37'),
(51, 'menu.customer.genjin.customergiveup', '移入公海', 'custom/giveup', 'CustomerGiveup', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-19 19:44:37'),
(52, 'menu.customer.genjin.customeraddnotice', '添加待办', 'custom/addnotices', 'CustomerAddNotice', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-21 21:32:51'),
(53, 'menu.customer.genjin.customeraddback', '添加回款', 'custom/submitback', 'CustomerAddBack', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-21 22:28:33'),
(54, 'menu.customer.genjin.custombacklist', '回款历史', 'custom/backlist', 'CustomerBackList', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(55, 'menu.system.team.del', '删除', 'system/team/del', 'Teamdel', '', 1, 4, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:41:17'),
(57, 'menu.operate.follow.editrule', '编辑规则', 'operate/assign/editrule', 'Follow', '', 1, 20, 9, 1, 1, '2022-06-25 09:18:25', '2022-07-30 14:39:18'),
(58, 'menu.operate.follow.setstatus', '修改规则状态', 'operate/assign/setstatus', 'Follow', '', 1, 20, 9, 1, 1, '2022-06-25 09:18:25', '2022-07-30 14:39:20'),
(59, 'menu.customer.genjin.customerbatchgiveup', '批量移入公海', 'custom/batchgiveup', 'BatchCustomerGiveup', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-19 19:44:37'),
(60, 'menu.customer.genjin.customerbatchget', '批量认领', 'custom/batchget', 'BatchCustomerGetNewPool', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:25'),
(62, 'menu.system.role.previe', '业绩排行-姓名', 'Nothing', 'YejiName', '', 1, 5, 2, 1, 1, '2022-06-19 14:45:15', '2022-09-17 14:38:48'),
(63, 'menu.system.role.previe', '业绩排行-团队', 'Nothing', 'YejiTeam', '', 1, 5, 3, 1, 1, '2022-06-19 14:45:15', '2022-09-17 14:38:49'),
(64, 'menu.system.role.previe', '业绩排行-业绩金额', 'Nothing', 'YejiAmount', '', 1, 5, 4, 1, 1, '2022-06-19 14:45:15', '2022-09-17 14:38:51'),
(65, 'menu.system.role.previe', '业绩排行-实际创收', 'Nothing', 'YejiRealAmount', '', 1, 5, 5, 1, 1, '2022-06-19 14:45:15', '2022-09-17 14:38:52'),
(66, 'menu.system.role.previe', '首页内容', 'Nothing', 'Nothing', '', 1, 5, 1, 1, 1, '2022-06-19 14:45:15', '2022-09-17 14:38:46'),
(67, 'menu.customer.genjin.customedit', '编辑客户', 'custom/edit', 'CustomerEditNewPool', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:27'),
(68, 'menu.customer.genjin.custompreivew', '查看客户', 'custom/info', 'CustomerPreviewNewPool', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:29'),
(69, 'menu.customer.genjin.customassign', '分配客户', 'custom/assign', 'CustomerAssignNewPool', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:31'),
(70, 'menu.customer.genjin.custombatchupload', '批量上传客户', 'custom/upload', 'CustomerBatchUploadPool', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:16'),
(71, 'menu.customer.genjin.customget', '认领客户', 'custom/get', 'CustomerGetPool', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:23'),
(72, 'menu.customer.genjin.customerbatchget', '批量认领', 'custom/batchget', 'BatchCustomerGetPool', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:25'),
(73, 'menu.customer.genjin.customedit', '编辑客户', 'custom/edit', 'CustomerEditPool', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:27'),
(74, 'menu.customer.genjin.custompreivew', '查看客户', 'custom/info', 'CustomerPreviewPool', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:29'),
(75, 'menu.customer.genjin.customassign', '分配客户', 'custom/assign', 'CustomerAssignPool', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:31'),
(76, 'menu.customer.genjin.customassign', '客户列表', 'Nothing', 'Nothing', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:31'),
(77, 'menu.customer.genjin.customassign', '客户列表', 'Nothing', 'Nothing', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:31'),
(81, 'menu.customer.genjin.export', '导出客户', 'custom/export', 'CustomerExport', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(82, 'menu.customer.genjin.export', '导出客户', 'custom/export', 'CustomerNewpoolExport', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(83, 'menu.customer.genjin.export', '导出客户', 'custom/export', 'CustomerPoolExport', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(84, 'menu.customer.genjin.export', '导出客户', 'custom/export', 'CustomerUnvalidExport', '', 1, 42, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(85, 'menu.customer.genjin.export', '导出客户', 'custom/export', 'CustomerImportExport', '', 1, 7, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(86, 'menu.customer.genjin.export', '导出客户', 'custom/export', 'CustomerInnerExport', '', 1, 8, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(87, 'menu.customer.genjin.export', '导出客户', 'custom/export', 'CustomerNewExport', '', 1, 9, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(88, 'menu.customer.genjin.customassign', '查询', 'Nothing', 'Nothing', '', 2, 7, 1, 1, 1, '2022-06-25 09:15:30', '2022-09-20 22:09:56'),
(90, 'menu.customer.genjin.customassign', '查询', 'Nothing', 'Nothing', '', 2, 8, 1, 1, 1, '2022-06-25 09:15:30', '2022-09-20 22:09:56'),
(91, 'menu.customer.genjin.customassign', '查询', 'Nothing', 'Nothing', '', 2, 9, 1, 1, 1, '2022-06-25 09:15:30', '2022-10-15 16:08:05'),
(92, 'menu.customer.genjin.customassign', '查询', 'Nothing', 'Nothing', '', 2, 42, 1, 1, 1, '2022-06-25 09:15:30', '2022-10-15 17:58:15'),
(93, 'menu.system.user.delete', '删除', 'system/user/delete', 'Userdelete', '', 1, 2, 0, 1, 1, '2022-06-19 14:45:15', '2022-10-15 16:51:45'),
(94, 'menu.customer.genjin.customassign', '分配客户', 'custom/assign', 'CustomerAssignUnvalid', '', 1, 42, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:31'),
(95, 'menu.system.user.delete', '删除', 'system/role/delete', 'Roledelete', '', 1, 3, 0, 1, 1, '2022-06-19 14:45:15', '2022-10-15 16:51:45'),
(96, 'menu.system.setting', '系统配置', 'system/setting', 'Setting', '', 1, 1, 0, 1, 0, '2022-06-19 14:45:46', '2022-10-27 22:28:00'),
(97, 'menu.customer.genjin.customerintro', '转介绍', 'custom/intro', 'CustomerIntro', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-19 19:44:37'),
(108, 'menu.system.product', '产品管理', 'system/product/list', 'Product', 'icon-apps', 1, 0, 1, 1, 0, '2022-06-19 14:49:09', '2022-11-01 23:14:20'),
(109, 'menu.system.product.edit', '编辑', 'system/product/edit', 'ProductEdit', '', 1, 108, 0, 1, 1, '2022-06-19 14:45:15', '2022-11-01 22:26:20'),
(110, 'menu.system.product.perview', '查看', 'system/product/info', 'ProductView', '', 1, 108, 0, 1, 1, '2022-06-19 14:45:15', '2022-11-01 22:26:23'),
(111, 'menu.customer.genjin.customdianping', '主管点评', 'custom/dianping', 'CustomerDianping', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-11-03 21:57:07'),
(112, 'menu.customer.genjin.customeditmobile', '修改手机号', 'custom/editmobile', 'CustomerEditMobile', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-11-03 21:57:07'),
(114, 'menu.system.user.onoffline', '上下线', 'system/user/onoffline', 'OnoffLine', '', 1, 2, 0, 1, 1, '2022-06-19 14:45:15', '2022-07-06 23:40:17'),
(118, 'menu.customer.pool.add', '录入客户', 'custom/edit', 'CustomerPoolAdd', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(119, 'menu.customer.pool.add', '录入客户', 'custom/edit', 'CustomerNewpoolAdd', '', 1, 40, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(120, 'menu.customer.customerimportassign', '分配', 'custom/assign', 'CustomerImportAssign', '', 1, 7, 9, 1, 1, '2022-06-25 09:15:30', '2023-04-13 22:45:33'),
(121, 'menu.customer.customerimportassign', '分配', 'custom/assign', 'CustomerInnerAssign', '', 1, 8, 9, 1, 1, '2022-06-25 09:15:30', '2023-04-13 22:45:36'),
(123, 'menu.customer.genjin.customcleanassign', '清理并分配客户', 'custom/cleanassign', 'CustomerCleanAssign', '', 1, 12, 9, 1, 1, '2022-06-25 09:15:30', '2022-07-09 10:40:29'),
(124, 'menu.customer.genjin.customcleanassign', '清理并分配客户', 'custom/cleanassign', 'CustomerCleanAssignPool', '', 1, 41, 9, 1, 1, '2022-06-25 09:15:30', '2022-09-17 16:40:31');

-- --------------------------------------------------------

--
-- 表的结构 `system_role`
--

CREATE TABLE `system_role` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `views` int(11) NOT NULL DEFAULT '1' COMMENT '可见范围 1-所有 2-上下级',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1- 有效 0-无效',
  `fields` text COMMENT '可见字段\r\n可见字段',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '更新时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间',
  `is_delete` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色';

--
-- 转存表中的数据 `system_role`
--

INSERT INTO `system_role` (`id`, `name`, `views`, `status`, `fields`, `create_time`, `update_time`, `is_delete`) VALUES
(1, '系统管理员', 1, 1, 'name,mobile,city,age,amount,sex,marry,work,company,income,insurance,star,house,car,policy,funds,wage,credit,source,follow_time,follow_status,assign_time,apply_time,user_from,follow_user_id', '2022-06-19 14:03:45', '2023-05-06 10:01:50', 0);

-- --------------------------------------------------------

--
-- 表的结构 `system_role_field`
--

CREATE TABLE `system_role_field` (
  `id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `field_id` int(11) NOT NULL DEFAULT '0' COMMENT '字段id',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统角色字段';

-- --------------------------------------------------------

--
-- 表的结构 `system_role_right`
--

CREATE TABLE `system_role_right` (
  `id` int(11) UNSIGNED NOT NULL,
  `right_id` int(11) NOT NULL DEFAULT '0' COMMENT '权限id',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统用户';

--
-- 转存表中的数据 `system_role_right`
--

INSERT INTO `system_role_right` (`id`, `right_id`, `role_id`, `create_time`, `update_time`) VALUES
(74, 1, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(75, 2, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(76, 3, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(80, 7, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(81, 8, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(82, 9, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(83, 10, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(84, 11, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(86, 13, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(87, 14, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(88, 15, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(89, 16, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(90, 17, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(91, 18, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(92, 19, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(93, 20, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(94, 21, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(96, 23, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(97, 24, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(98, 25, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(99, 26, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(100, 27, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(101, 28, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(102, 29, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(103, 30, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(104, 31, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(177, 6, 1, '2022-07-11 07:46:39', '2022-07-11 07:46:39'),
(178, 40, 1, '2022-07-15 21:50:50', '2022-07-15 21:50:50'),
(186, 41, 1, '2022-07-15 21:52:53', '2022-07-15 21:52:53'),
(188, 22, 1, '2022-07-15 22:13:27', '2022-07-15 22:13:27'),
(189, 4, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(198, 43, 1, '2022-07-17 16:31:19', '2022-07-17 16:31:19'),
(201, 46, 1, '2022-07-19 12:48:49', '2022-07-19 12:48:49'),
(202, 47, 1, '2022-07-19 12:48:49', '2022-07-19 12:48:49'),
(203, 48, 1, '2022-07-19 12:48:49', '2022-07-19 12:48:49'),
(210, 55, 1, '2022-07-24 11:44:06', '2022-07-24 11:44:06'),
(211, 56, 1, '2022-07-24 17:40:14', '2022-07-24 17:40:14'),
(275, 57, 1, '2022-07-30 14:38:42', '2022-07-30 14:38:42'),
(276, 58, 1, '2022-07-30 14:38:42', '2022-07-30 14:38:42'),
(281, 61, 1, '2022-09-11 23:57:36', '2022-09-11 23:57:36'),
(288, 66, 1, '2022-09-17 14:38:20', '2022-09-17 14:38:20'),
(289, 5, 1, '2022-09-17 14:38:20', '2022-09-17 14:38:20'),
(290, 65, 1, '2022-09-17 14:47:34', '2022-09-17 14:47:34'),
(291, 64, 1, '2022-09-17 14:47:34', '2022-09-17 14:47:34'),
(292, 63, 1, '2022-09-17 14:47:34', '2022-09-17 14:47:34'),
(293, 62, 1, '2022-09-17 14:47:34', '2022-09-17 14:47:34'),
(303, 76, 1, '2022-09-17 16:58:07', '2022-09-17 16:58:07'),
(304, 77, 1, '2022-09-17 16:58:07', '2022-09-17 16:58:07'),
(305, 69, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(306, 68, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(307, 67, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(308, 39, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(309, 45, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(310, 60, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(311, 70, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(312, 71, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(313, 72, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(314, 73, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(315, 74, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(316, 75, 1, '2022-09-17 17:02:22', '2022-09-17 17:02:22'),
(318, 79, 1, '2022-09-20 22:10:47', '2022-09-20 22:10:47'),
(319, 78, 1, '2022-09-20 22:24:29', '2022-09-20 22:24:29'),
(320, 80, 1, '2022-09-26 22:59:05', '2022-09-26 22:59:05'),
(322, 88, 1, '2022-10-15 16:08:25', '2022-10-15 16:08:25'),
(323, 90, 1, '2022-10-15 16:08:25', '2022-10-15 16:08:25'),
(324, 91, 1, '2022-10-15 16:08:25', '2022-10-15 16:08:25'),
(325, 85, 1, '2022-10-15 16:09:42', '2022-10-15 16:09:42'),
(326, 86, 1, '2022-10-15 16:12:11', '2022-10-15 16:12:11'),
(327, 87, 1, '2022-10-15 16:12:23', '2022-10-15 16:12:23'),
(328, 82, 1, '2022-10-15 16:12:48', '2022-10-15 16:12:48'),
(329, 83, 1, '2022-10-15 16:13:37', '2022-10-15 16:13:37'),
(330, 42, 1, '2022-10-15 16:14:49', '2022-10-15 16:14:49'),
(331, 84, 1, '2022-10-15 16:14:49', '2022-10-15 16:14:49'),
(333, 93, 1, '2022-10-15 16:52:24', '2022-10-15 16:52:24'),
(334, 94, 1, '2022-10-23 09:53:56', '2022-10-23 09:53:56'),
(335, 92, 1, '2022-10-23 09:53:56', '2022-10-23 09:53:56'),
(336, 95, 1, '2022-10-23 10:17:52', '2022-10-23 10:17:52'),
(337, 96, 1, '2022-10-27 22:21:58', '2022-10-27 22:21:58'),
(339, 98, 1, '2022-10-29 10:43:38', '2022-10-29 10:43:38'),
(340, 99, 1, '2022-10-29 10:43:38', '2022-10-29 10:43:38'),
(341, 100, 1, '2022-10-29 13:22:56', '2022-10-29 13:22:56'),
(342, 101, 1, '2022-10-29 13:22:56', '2022-10-29 13:22:56'),
(347, 102, 1, '2022-10-31 21:19:51', '2022-10-31 21:19:51'),
(350, 107, 1, '2022-10-31 21:19:51', '2022-10-31 21:19:51'),
(351, 103, 1, '2022-10-31 21:19:51', '2022-10-31 21:19:51'),
(352, 104, 1, '2022-10-31 21:19:51', '2022-10-31 21:19:51'),
(353, 108, 1, '2022-11-01 22:26:33', '2022-11-01 22:26:33'),
(354, 109, 1, '2022-11-01 22:26:33', '2022-11-01 22:26:33'),
(355, 110, 1, '2022-11-01 22:26:33', '2022-11-01 22:26:33'),
(518, 117, 1, '2022-07-10 01:34:54', '2022-07-10 01:34:54'),
(519, 119, 1, '2023-04-02 00:23:41', '2023-04-02 00:23:41'),
(520, 118, 1, '2023-04-02 00:23:41', '2023-04-02 00:23:41'),
(521, 120, 1, '2023-04-13 22:44:39', '2023-04-13 22:44:39'),
(522, 121, 1, '2023-04-13 22:44:39', '2023-04-13 22:44:39'),
(553, 12, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(554, 32, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(555, 33, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(556, 34, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(557, 35, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(558, 36, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(559, 37, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(560, 38, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(561, 44, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(562, 49, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(563, 50, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(564, 51, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(565, 52, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(566, 53, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(567, 54, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(568, 59, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(569, 81, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(570, 97, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(571, 111, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(572, 112, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(573, 115, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(574, 116, 1, '2023-04-25 16:27:10', '2023-04-25 16:27:10'),
(575, 106, 1, '2023-04-25 16:28:43', '2023-04-25 16:28:43'),
(576, 105, 1, '2023-04-25 16:28:43', '2023-04-25 16:28:43'),
(577, 122, 1, '2023-04-26 10:14:03', '2023-04-26 10:14:03'),
(578, 123, 1, '2023-05-03 20:22:46', '2023-05-03 20:22:46'),
(579, 124, 1, '2023-05-03 20:22:46', '2023-05-03 20:22:46'),
(580, 114, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `system_setting`
--

CREATE TABLE `system_setting` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(200) NOT NULL DEFAULT '' COMMENT 'ip',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间',
  `role_id` varchar(100) NOT NULL DEFAULT '',
  `etime` varchar(100) NOT NULL DEFAULT '',
  `stime` varchar(100) NOT NULL DEFAULT '',
  `clean` int(11) DEFAULT '0' COMMENT '清理任务'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

--
-- 转存表中的数据 `system_setting`
--

INSERT INTO `system_setting` (`id`, `ip`, `create_time`, `update_time`, `role_id`, `etime`, `stime`, `clean`) VALUES
(1, '', '2022-10-27 22:19:09', '2023-04-26 10:15:00', '1', '22:50:04', '09:00:00', 1);

-- --------------------------------------------------------

--
-- 表的结构 `system_team`
--

CREATE TABLE `system_team` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `manager_id` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员id',
  `status` int(11) NOT NULL DEFAULT '1',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统团队';

-- --------------------------------------------------------

--
-- 表的结构 `system_user`
--

CREATE TABLE `system_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(50) NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(50) NOT NULL DEFAULT '' COMMENT '密码',
  `team_id` int(11) NOT NULL DEFAULT '0' COMMENT '团队id',
  `password_salt` varchar(50) NOT NULL DEFAULT '' COMMENT '密码盐',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态：0-无效 1-有效',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级',
  `assign_rights` varchar(256) NOT NULL DEFAULT '' COMMENT '分配\r\n权限配置',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间',
  `is_del` int(11) NOT NULL DEFAULT '0',
  `online` int(11) NOT NULL DEFAULT '1',
  `token_time` int(11) NOT NULL DEFAULT '0',
  `token` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统用户';

--
-- 转存表中的数据 `system_user`
--

INSERT INTO `system_user` (`id`, `name`, `mobile`, `password`, `team_id`, `password_salt`, `status`, `parent_id`, `assign_rights`, `create_time`, `update_time`, `is_del`, `online`, `token_time`, `token`) VALUES
(1, '黄晓明', '18211000000', 'd83a3aaa1c9ee44bd622f40dff1bec3a', 1, '960479', 1, 13, '{\"public\":\"60\",\"inner\":\"33\"}', '2022-06-19 14:55:40', '2023-03-21 21:53:57', 0, 1, 1679406837, 'b1cfdf2a4ff27e63f7f605cb67e0e59a');

-- --------------------------------------------------------

--
-- 表的结构 `system_user_role`
--

CREATE TABLE `system_user_role` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
   `create_time` datetime DEFAULT CURRENT_TIMESTAMP  COMMENT '创建时间',
   `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统用户';

--
-- 转存表中的数据 `system_user_role`
--

INSERT INTO `system_user_role` (`id`, `user_id`, `role_id`, `create_time`, `update_time`) VALUES
(1, 1, 1, '2022-11-09 15:47:06', '2022-11-09 15:49:53');

ALTER TABLE `channel`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `idx_mm` (`mobile_md5`),
  ADD KEY `mobile_jiami` (`mobile_jiami`),
  ADD KEY `follow_user_id` (`follow_user_id`,`user_from`,`is_follow`,`assign_time`) USING BTREE,
  ADD KEY `follow_user_id_2` (`follow_user_id`,`follow_time`,`is_follow`,`assign_time`),
  ADD KEY `follow_user_id_3` (`follow_user_id`,`apply_time`,`is_follow`,`assign_time`),
  ADD KEY `follow_user_id_4` (`follow_user_id`,`assign_time`,`follow_time`,`follow_status`);


ALTER TABLE `customer_backs`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `customer_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `create_time` (`customer_id`,`create_time`) USING BTREE,
  ADD KEY `idx_ac` (`after`(255),`create_time`,`type`,`customer_id`) USING BTREE,
  ADD KEY `user_id` (`user_id`,`type`,`id`),
  ADD KEY `type` (`type`,`after`(255),`create_time`);


ALTER TABLE `customer_remark_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);


ALTER TABLE `customer_rule_config`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `dict`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tid` (`type`,`tid`);


ALTER TABLE `notice`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `system_field`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);


ALTER TABLE `system_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_right`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_role`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_role_field`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `system_role_right`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_setting`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `system_team`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mobile` (`mobile`) USING BTREE;

ALTER TABLE `system_user_role`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `channel`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `customer`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `customer_backs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `customer_remark_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_rule_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;


ALTER TABLE `dict`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;


ALTER TABLE `notice`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `product`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `system_field`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;


ALTER TABLE `system_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `system_right`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;


ALTER TABLE `system_role`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `system_role_field`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `system_role_right`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=581;


ALTER TABLE `system_team`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `system_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `system_user_role`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

