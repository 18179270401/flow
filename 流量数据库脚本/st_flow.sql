/*
Navicat MySQL Data Transfer

Source Server         : st_flow_out
Source Server Version : 50629
Source Host           : stflowout01.mysql.rds.aliyuncs.com:3306
Source Database       : st_flow

Target Server Type    : MYSQL
Target Server Version : 50629
File Encoding         : 65001

Date: 2016-12-13 09:25:58
*/

SET FOREIGN_KEY_CHECKS=0;

set global event_scheduler = on;

-- ----------------------------
-- Table structure for t_flow_account_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_account_record`;
CREATE TABLE `t_flow_account_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `operater_price` decimal(11,3) DEFAULT NULL,
  `operater_before_balance` decimal(11,3) DEFAULT NULL,
  `operater_after_balance` decimal(11,3) DEFAULT NULL,
  `operate_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '操作类型（1：购买流量，2：充值，3：提现，4：划拨，5：返还，6：分红、7退款、8：测试款、9：账户冻结、10：账户解冻）',
  `balance_type` tinyint(1) NOT NULL COMMENT '收支类型（1：收入、2：支出）',
  `record_date` datetime NOT NULL COMMENT '记录时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `user_id` bigint(18) DEFAULT NULL,
  `operation_date` datetime DEFAULT NULL,
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2：企业)',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `obj_user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2：企业)',
  `obj_proxy_id` bigint(18) DEFAULT NULL,
  `obj_enterprise_id` bigint(18) DEFAULT NULL,
  `order_id` bigint(18) NOT NULL COMMENT '订单ID号（分红时记录）',
  `device_name` varchar(100) DEFAULT NULL COMMENT '操作主机名称',
  PRIMARY KEY (`record_id`),
  KEY `ix_t_flow_account_record_operate_type` (`operate_type`),
  KEY `ix_t_flow_account_record_proxy_operation_date` (`proxy_id`,`operation_date`),
  KEY `ix_t_flow_account_record_enterprise_id_operation_date` (`enterprise_id`,`operation_date`)
) ENGINE=InnoDB AUTO_INCREMENT=48582275 DEFAULT CHARSET=utf8 COMMENT='账户流水表';

-- ----------------------------
-- Table structure for t_flow_account_record_update
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_account_record_update`;
CREATE TABLE `t_flow_account_record_update` (
  `record_id` bigint(18) NOT NULL DEFAULT '0' COMMENT '设置ID',
  `operater_price` decimal(11,3) DEFAULT NULL,
  `operater_before_balance` decimal(11,3) DEFAULT NULL,
  `operater_after_balance` decimal(11,3) DEFAULT NULL,
  `operate_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '操作类型（1：购买流量，2：充值，3：提现，4：划拨，5：返还，6：分红、7退款、8：测试款、9：账户冻结、10：账户解冻）',
  `balance_type` tinyint(1) NOT NULL COMMENT '收支类型（1：收入、2：支出）',
  `record_date` datetime NOT NULL COMMENT '记录时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `user_id` bigint(18) DEFAULT NULL,
  `operation_date` datetime DEFAULT NULL,
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2：企业)',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `obj_user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2：企业)',
  `obj_proxy_id` bigint(18) DEFAULT NULL,
  `obj_enterprise_id` bigint(18) DEFAULT NULL,
  `order_id` bigint(18) NOT NULL COMMENT '订单ID号（分红时记录）',
  `device_name` varchar(100) DEFAULT NULL COMMENT '操作主机名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_address_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_address_user`;
CREATE TABLE `t_flow_address_user` (
  `address_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '地址ID',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `contact_name` varchar(100) DEFAULT NULL COMMENT '联系人',
  `contact_tel` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `contact_province_id` bigint(18) DEFAULT NULL COMMENT '省份',
  `contact_city_id` bigint(18) DEFAULT NULL COMMENT '城市',
  `contact_address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COMMENT='用户邮寄地址信息表';

-- ----------------------------
-- Table structure for t_flow_available_menu
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_available_menu`;
CREATE TABLE `t_flow_available_menu` (
  `available_menu_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2企业）',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `menu_id` bigint(18) DEFAULT NULL,
  `menu_name` varchar(100) DEFAULT NULL,
  `menu_url` varchar(100) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`available_menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6182 DEFAULT CHARSET=utf8 COMMENT='企业可用菜单';

-- ----------------------------
-- Table structure for t_flow_channel
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel`;
CREATE TABLE `t_flow_channel` (
  `channel_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `channel_name` varchar(30) NOT NULL COMMENT '通道名称',
  `channel_code` varchar(20) NOT NULL COMMENT '通道编号',
  `channel_file_name` varchar(30) DEFAULT NULL COMMENT '通道文件名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '通道状态（0：禁用、1：正常）',
  `discount_mobile` decimal(4,3) NOT NULL DEFAULT '1.000' COMMENT '移动折扣',
  `discount_unicom` decimal(4,3) NOT NULL DEFAULT '1.000' COMMENT '联通折扣',
  `discount_telecom` decimal(4,3) NOT NULL DEFAULT '1.000' COMMENT '电信折扣',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改人时间',
  `province_id` bigint(18) DEFAULT '1' COMMENT '省ID',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `type` tinyint(1) DEFAULT '0' COMMENT '流量类型',
  `is_filter` tinyint(1) DEFAULT '0' COMMENT '是否过虑通道（0：否、1：是）',
  `account_id` bigint(18) DEFAULT NULL COMMENT '账户ID（所属通道账户）',
  `platform_id` int(11) DEFAULT '1' COMMENT '平台ID  1:php 2:java',
  `attribute_id` tinyint(1) NOT NULL DEFAULT '1' COMMENT '通道属性（1.普通通道，2流量包通道）',
  `is_cache` tinyint(1) DEFAULT '1' COMMENT '是否缓存（1.正常，0.缓存）',
  `fail_threshold` decimal(5,4) DEFAULT NULL COMMENT '失败率通知阈值',
  `block_card_num` int(11) DEFAULT NULL COMMENT '卡单数目阈值',
  `is_message` tinyint(1) DEFAULT '0' COMMENT '发短信状态（0.禁用，1.启用）',
  PRIMARY KEY (`channel_id`,`attribute_id`),
  KEY `ix_t_flow_channel_channel_code` (`channel_code`),
  KEY `ix_t_flow_channel_channel_name` (`channel_name`)
) ENGINE=InnoDB AUTO_INCREMENT=663 DEFAULT CHARSET=utf8 COMMENT='通道信息表';

-- ----------------------------
-- Table structure for t_flow_channel_account
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_account`;
CREATE TABLE `t_flow_channel_account` (
  `account_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '通道账号ID号',
  `account_name` varchar(50) DEFAULT NULL COMMENT '账户名称',
  `total_money` decimal(11,3) DEFAULT NULL,
  `surplus_money` decimal(11,3) DEFAULT NULL,
  `times` int(5) DEFAULT NULL COMMENT '加款次数',
  `last_recharge_money` decimal(11,3) DEFAULT NULL,
  `last_recharge_date` datetime DEFAULT NULL COMMENT '最后充值时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `quota_remind` decimal(11,3) DEFAULT NULL,
  `caution_money` decimal(11,3) DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8 COMMENT='通道账户信息表';

-- ----------------------------
-- Table structure for t_flow_channel_account_his
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_account_his`;
CREATE TABLE `t_flow_channel_account_his` (
  `id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `account_id` bigint(18) NOT NULL COMMENT '通道账号ID号',
  `account_name` varchar(50) DEFAULT NULL COMMENT '账户名称',
  `total_money` decimal(11,3) DEFAULT NULL,
  `surplus_money` decimal(11,3) DEFAULT NULL,
  `times` int(5) DEFAULT NULL COMMENT '加款次数',
  `last_recharge_money` decimal(11,3) DEFAULT NULL,
  `last_recharge_date` datetime DEFAULT NULL COMMENT '最后充值时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `quota_remind` decimal(11,3) DEFAULT NULL,
  `record_day` bigint(50) DEFAULT NULL COMMENT '记录日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13400 DEFAULT CHARSET=utf8 COMMENT='通道账户信息日记录表';

-- ----------------------------
-- Table structure for t_flow_channel_account_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_account_record`;
CREATE TABLE `t_flow_channel_account_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '账户流水ID',
  `account_id` bigint(18) DEFAULT NULL COMMENT '通道账户ID',
  `operater_price` decimal(11,3) DEFAULT NULL,
  `operater_before_balance` decimal(11,3) DEFAULT NULL,
  `operater_after_balance` decimal(11,3) DEFAULT NULL,
  `payment_method` tinyint(1) NOT NULL DEFAULT '1' COMMENT '付款方式（1为汇款，2微信支付，3为支付宝支付）',
  `transaction_number` varchar(100) DEFAULT NULL COMMENT '交易号',
  `payment_account` varchar(20) DEFAULT NULL,
  `payment_bank` varchar(50) DEFAULT NULL COMMENT '付款银行',
  `payment_date` varchar(50) DEFAULT NULL COMMENT '加款时间',
  `record_date` datetime DEFAULT NULL COMMENT '记录时间',
  `user_id` bigint(18) DEFAULT NULL COMMENT '操作员ID',
  `device_name` varchar(100) DEFAULT NULL COMMENT '操作主机名',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1820 DEFAULT CHARSET=utf8 COMMENT='通道加款流水';

-- ----------------------------
-- Table structure for t_flow_channel_discount
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_discount`;
CREATE TABLE `t_flow_channel_discount` (
  `discount_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(18) DEFAULT NULL COMMENT '通道ID',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '运营商ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省ID',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `discount_number` decimal(5,3) DEFAULT NULL COMMENT '折扣数',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `rebate_discoun` decimal(5,3) DEFAULT NULL COMMENT '返利折扣',
  PRIMARY KEY (`discount_id`)
) ENGINE=InnoDB AUTO_INCREMENT=457 DEFAULT CHARSET=utf8 COMMENT='通道折扣信息表';

-- ----------------------------
-- Table structure for t_flow_channel_discount_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_discount_record`;
CREATE TABLE `t_flow_channel_discount_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '记录ID号',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '通道ID',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '所属运营商',
  `province_id` bigint(18) DEFAULT NULL COMMENT '所属地区',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `discount_type` tinyint(1) DEFAULT NULL COMMENT '折扣类型(1:普通折扣、2：返利折扣)',
  `discount_before` decimal(5,3) DEFAULT NULL COMMENT '操作前折扣数',
  `discount_after` decimal(5,3) DEFAULT NULL COMMENT '操作后折扣数',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=777 DEFAULT CHARSET=utf8 COMMENT='通道折扣变动记录表';

-- ----------------------------
-- Table structure for t_flow_channel_market_quotes
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_market_quotes`;
CREATE TABLE `t_flow_channel_market_quotes` (
  `quotes_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '行情报价ID',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '通道ID',
  `company_name` varchar(255) DEFAULT NULL COMMENT '公司名称',
  `discount_mobile` decimal(4,3) DEFAULT NULL,
  `discount_unicom` decimal(4,3) DEFAULT NULL,
  `discount_telecom` decimal(4,3) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`quotes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='通道行情报价';

-- ----------------------------
-- Table structure for t_flow_channel_msg
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_msg`;
CREATE TABLE `t_flow_channel_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) NOT NULL COMMENT '通道ID',
  `msg` varchar(300) NOT NULL COMMENT '短信内容',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态:0失效；1正常',
  `create_id` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `modify_id` int(11) DEFAULT NULL COMMENT '最后修改人',
  `modify_time` datetime DEFAULT NULL COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='通道短信信息表';

-- ----------------------------
-- Table structure for t_flow_channel_product
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_product`;
CREATE TABLE `t_flow_channel_product` (
  `product_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `product_name` varchar(50) DEFAULT NULL COMMENT '名称',
  `channel_id` bigint(18) NOT NULL COMMENT '所属通道',
  `operator_id` tinyint(1) NOT NULL COMMENT '所属运营商',
  `province_id` tinyint(2) NOT NULL COMMENT '所属地区',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `price` decimal(11,3) DEFAULT NULL,
  `discount` decimal(11,3) DEFAULT NULL,
  `number` varchar(255) NOT NULL COMMENT '编号',
  `product_type` tinyint(1) DEFAULT NULL COMMENT '产品类型（0：全国流量、1：省流量）',
  `size` int(10) DEFAULT NULL COMMENT '包含流量大小（单位为M，1G=1024M）',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态（0：禁用、1：启用、2：删除）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改人时间',
  PRIMARY KEY (`product_id`),
  KEY `FK_Reference_15` (`channel_id`),
  KEY `ix_t_flow_channel_product_channel_id` (`channel_id`),
  KEY `ix_t_flow_channel_product_create_date` (`create_date`),
  KEY `ix_t_flow_channel_product_operator_id` (`operator_id`),
  KEY `ix_t_flow_channel_product_product_id` (`product_id`),
  KEY `ix_t_flow_channel_product_status` (`status`),
  CONSTRAINT `FK_Reference_15` FOREIGN KEY (`channel_id`) REFERENCES `t_flow_channel` (`channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4247 DEFAULT CHARSET=utf8 COMMENT='通道产品表';

-- ----------------------------
-- Table structure for t_flow_channel_province
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_province`;
CREATE TABLE `t_flow_channel_province` (
  `channel_province_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '通道账号ID号',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '通道ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `province_money` decimal(11,3) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '通道状态（0：禁用、1：正常）',
  `type` tinyint(1) DEFAULT '1' COMMENT '类型（1：省全国流量、2：省省内流量）',
  `quota_remind` decimal(11,3) DEFAULT NULL,
  PRIMARY KEY (`channel_province_id`),
  KEY `ix_channel_province_province_id_channel_id` (`channel_id`,`province_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='通道省份信息表';

-- ----------------------------
-- Table structure for t_flow_channel_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_channel_user`;
CREATE TABLE `t_flow_channel_user` (
  `channel_user_id` int(6) NOT NULL AUTO_INCREMENT COMMENT '用户独立通道ID',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '通道ID',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`channel_user_id`),
  KEY `ix_t_flow_channel_user_create_date` (`create_date`),
  KEY `ix_t_flow_channel_user_enterprise_id` (`enterprise_id`),
  KEY `ix_t_flow_channel_user_proxy_id` (`proxy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61927 DEFAULT CHARSET=utf8 COMMENT='用户设置独立通道表';

-- ----------------------------
-- Table structure for t_flow_company_blacklist
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_company_blacklist`;
CREATE TABLE `t_flow_company_blacklist` (
  `company_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) NOT NULL,
  `tel` varchar(20) DEFAULT NULL COMMENT '公司电话',
  `contact_name` varchar(50) DEFAULT NULL COMMENT '联系人',
  `contact_tel` varchar(20) DEFAULT NULL COMMENT '联系人电话',
  `email` varchar(30) DEFAULT NULL COMMENT '邮箱',
  `province` varchar(100) DEFAULT NULL COMMENT '省份',
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `remark` varchar(500) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='公司黑名单';

-- ----------------------------
-- Table structure for t_flow_discount
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_discount`;
CREATE TABLE `t_flow_discount` (
  `discount_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `operator_id` tinyint(1) NOT NULL COMMENT '所属运营商',
  `province_id` tinyint(2) NOT NULL COMMENT '所属地区',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `discount_number` decimal(5,3) NOT NULL COMMENT '折扣数',
  `create_proxy_id` bigint(18) DEFAULT NULL COMMENT '创建代理商ID',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`discount_id`),
  KEY `ix_t_flow_discount_create_date` (`create_date`),
  KEY `ix_t_flow_discount_enterprise_id` (`enterprise_id`),
  KEY `ix_t_flow_discount_operator_id` (`operator_id`),
  KEY `ix_t_flow_discount_proxy_id` (`proxy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21380 DEFAULT CHARSET=utf8 COMMENT='代理商或企业折扣表';

-- ----------------------------
-- Table structure for t_flow_discount_product
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_discount_product`;
CREATE TABLE `t_flow_discount_product` (
  `discount_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `operator_id` tinyint(1) NOT NULL COMMENT '所属运营商',
  `province_id` tinyint(2) NOT NULL COMMENT '所属地区',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `discount_number` decimal(5,3) NOT NULL COMMENT '折扣数',
  `create_proxy_id` bigint(18) DEFAULT NULL COMMENT '创建代理商ID',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `size` int(10) DEFAULT NULL COMMENT '流量大小（单位为M，1G=1024M）',
  PRIMARY KEY (`discount_id`),
  KEY `ix_t_flow_discount_create_date` (`create_date`),
  KEY `ix_t_flow_discount_enterprise_id` (`enterprise_id`),
  KEY `ix_t_flow_discount_operator_id` (`operator_id`),
  KEY `ix_t_flow_discount_proxy_id` (`proxy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2335 DEFAULT CHARSET=utf8 COMMENT='代理商或企业折扣表';

-- ----------------------------
-- Table structure for t_flow_discount_product_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_discount_product_record`;
CREATE TABLE `t_flow_discount_product_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '记录ID号',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `top_proxy_id` bigint(18) DEFAULT NULL COMMENT '上级代理商',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '所属运营商',
  `province_id` bigint(18) DEFAULT NULL COMMENT '所属地区',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市',
  `discount_before` decimal(5,3) DEFAULT NULL COMMENT '操作前折扣数',
  `discount_after` decimal(5,3) DEFAULT NULL COMMENT '操作后折扣数',
  `create_proxy_id` bigint(18) DEFAULT NULL COMMENT '创建代理商ID',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `size` int(10) DEFAULT NULL COMMENT '流量大小（单位为M，1G=1024M）',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2791 DEFAULT CHARSET=utf8 COMMENT='用户折扣变动记录';

-- ----------------------------
-- Table structure for t_flow_discount_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_discount_record`;
CREATE TABLE `t_flow_discount_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '记录ID号',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `top_proxy_id` bigint(18) DEFAULT NULL COMMENT '上级代理商',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '所属运营商',
  `province_id` bigint(18) DEFAULT NULL COMMENT '所属地区',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市',
  `discount_before` decimal(5,3) DEFAULT NULL COMMENT '操作前折扣数',
  `discount_after` decimal(5,3) DEFAULT NULL COMMENT '操作后折扣数',
  `create_proxy_id` bigint(18) DEFAULT NULL COMMENT '创建代理商ID',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12591 DEFAULT CHARSET=utf8 COMMENT='用户折扣变动记录';

-- ----------------------------
-- Table structure for t_flow_domain
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_domain`;
CREATE TABLE `t_flow_domain` (
  `domain_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2企业）',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `domain_name` varchar(100) DEFAULT NULL COMMENT '公司域名',
  `record_number` varchar(100) DEFAULT NULL COMMENT '备案号',
  `web_name` varchar(100) DEFAULT NULL COMMENT '首页头部名称',
  `logo_img` varchar(100) DEFAULT NULL COMMENT '网站Logo',
  `web_end` varchar(150) DEFAULT NULL COMMENT '网站版权',
  `ico_img` varchar(150) DEFAULT NULL COMMENT 'icon图标',
  `back_img` varchar(100) DEFAULT NULL COMMENT '背景图',
  `remark` varchar(255) DEFAULT NULL,
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（0：等待审核、1初审通过、2初审驳回、3复审通过、4复审驳回）',
  `last_approve_date` datetime DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`domain_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='公司域名信息表';

-- ----------------------------
-- Table structure for t_flow_domain_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_domain_process`;
CREATE TABLE `t_flow_domain_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `domain_id` bigint(18) NOT NULL,
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（1：审核通过、2：审核失败）',
  `approve_user_id` bigint(18) DEFAULT NULL,
  `approve_date` datetime DEFAULT NULL,
  `approve_remark` varchar(255) DEFAULT NULL,
  `approve_stage` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='域名审核记录表';

-- ----------------------------
-- Table structure for t_flow_enterprise
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise`;
CREATE TABLE `t_flow_enterprise` (
  `enterprise_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '企业ID',
  `enterprise_code` int(6) NOT NULL COMMENT '企业编号（生成规则：100001开始递增）',
  `enterprise_name` varchar(100) NOT NULL COMMENT '名称',
  `tel` varchar(20) NOT NULL COMMENT '公司电话',
  `contact_name` varchar(50) NOT NULL COMMENT '联系人',
  `contact_tel` varchar(20) DEFAULT NULL COMMENT '联系人电话',
  `email` varchar(30) DEFAULT NULL COMMENT '邮箱',
  `top_proxy_id` bigint(18) NOT NULL COMMENT '所属上级代理商',
  `operator` varchar(5) NOT NULL COMMENT '支持运营商',
  `province` varchar(100) DEFAULT NULL COMMENT '所在省份',
  `city` varchar(100) DEFAULT NULL COMMENT '所在城市',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `sale_id` bigint(18) DEFAULT NULL COMMENT '客户经理',
  `icense_img` varchar(255) DEFAULT NULL,
  `icense_img_num` varchar(30) DEFAULT NULL COMMENT '营业执照编号',
  `identity_img` varchar(255) DEFAULT NULL COMMENT '身份证图片附件',
  `identity_img_num` varchar(30) DEFAULT NULL COMMENT '身份证编号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 (0:已禁用，1：正常，2：删除)',
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态（0：待审核、1：审核通过、2：审核驳回）',
  `approve_user_id` bigint(18) DEFAULT NULL,
  `approve_date` datetime DEFAULT NULL,
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `enterprise_type` tinyint(1) DEFAULT NULL COMMENT '企业类型（1.为正常企业，2为活动企业）',
  `refund_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可以退款的状态：1，可以退款；0，禁止退款',
  `message_status` tinyint(1) DEFAULT '0' COMMENT '发短信状态（0.禁用，1启用)',
  PRIMARY KEY (`enterprise_id`),
  KEY `ix_t_flow_enterprise_create_date` (`create_date`),
  KEY `ix_t_flow_enterprise_enterprise_code` (`enterprise_code`),
  KEY `ix_t_flow_enterprise_sale_id` (`sale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=797 DEFAULT CHARSET=utf8 COMMENT='企业信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_account
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_account`;
CREATE TABLE `t_flow_enterprise_account` (
  `account_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '账号ID号',
  `enterprise_id` bigint(18) NOT NULL COMMENT '企业ID',
  `account_balance` decimal(11,3) DEFAULT NULL,
  `freeze_money` decimal(11,3) DEFAULT NULL,
  `credit_money` decimal(11,3) DEFAULT NULL,
  `credit_freeze_money` decimal(11,3) DEFAULT NULL,
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人ID',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  `cache_credit` decimal(11,3) DEFAULT NULL,
  `new_quota_remind` decimal(11,3) DEFAULT '10000.000',
  `channel_cache_credit` decimal(11,3) DEFAULT NULL COMMENT '通道缓存额度',
  PRIMARY KEY (`account_id`),
  KEY `ix_enterprise_account_enterprise_id` (`enterprise_id`)
) ENGINE=InnoDB AUTO_INCREMENT=786 DEFAULT CHARSET=utf8 COMMENT='企业账户表';

-- ----------------------------
-- Table structure for t_flow_enterprise_account_his
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_account_his`;
CREATE TABLE `t_flow_enterprise_account_his` (
  `id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `account_id` bigint(18) NOT NULL COMMENT '账号ID号',
  `enterprise_id` bigint(18) NOT NULL COMMENT '企业ID',
  `account_balance` decimal(11,3) DEFAULT NULL,
  `freeze_money` decimal(11,3) DEFAULT NULL,
  `credit_money` decimal(11,3) DEFAULT NULL,
  `credit_freeze_money` decimal(11,3) DEFAULT NULL,
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人ID',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  `cache_credit` decimal(11,3) DEFAULT NULL,
  `new_quota_remind` decimal(11,3) DEFAULT NULL,
  `channel_cache_credit` decimal(11,3) DEFAULT NULL COMMENT '通道缓存额度',
  `record_day` bigint(50) DEFAULT NULL COMMENT '记录日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43990 DEFAULT CHARSET=utf8 COMMENT='企业账户表日记录表';

-- ----------------------------
-- Table structure for t_flow_enterprise_contract
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_contract`;
CREATE TABLE `t_flow_enterprise_contract` (
  `contract_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '合同ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `contract_code` varchar(100) DEFAULT NULL COMMENT '合同编号',
  `contract_money` decimal(11,3) DEFAULT NULL,
  `effect_date` datetime DEFAULT NULL COMMENT '生效时间',
  `expire_date` datetime DEFAULT NULL COMMENT '到期时间',
  `describe` text COMMENT '条款说明',
  `enclosure` varchar(500) DEFAULT NULL COMMENT '附件',
  `remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态\n            1草稿\n            2等待审核\n            3初审通过\n            4初审驳回\n            5复审通过\n            6复审驳回',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `discount` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`contract_id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8 COMMENT='企业合同信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_contract_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_contract_process`;
CREATE TABLE `t_flow_enterprise_contract_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `contract_id` bigint(18) DEFAULT NULL COMMENT '合同ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\n            1审核通过\n            2审核驳回\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审）',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8 COMMENT='企业合同审核过程记录表';

-- ----------------------------
-- Table structure for t_flow_enterprise_frozen_account
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_frozen_account`;
CREATE TABLE `t_flow_enterprise_frozen_account` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '申请ID',
  `enterprise_id` bigint(18) NOT NULL COMMENT '企业ID',
  `account_id` bigint(18) NOT NULL COMMENT '账户ID',
  `apply_money` decimal(11,3) DEFAULT NULL,
  `apply_date` datetime DEFAULT NULL COMMENT '申请时间',
  `opr_type` tinyint(1) DEFAULT NULL COMMENT '操作类型(1:冻结、2:解冻)',
  `frozen_date` datetime DEFAULT NULL COMMENT '冻结时间',
  `thaw_date` datetime DEFAULT NULL COMMENT '解冻时间',
  `frozen_remark` varchar(255) DEFAULT NULL COMMENT '冻结说明',
  `thaw_remark` varchar(255) DEFAULT NULL COMMENT '解冻说明',
  `frozen_approve_status` tinyint(1) DEFAULT NULL COMMENT '冻结审核状态（1：等待、2：冻结初审成功、3：冻结初审驳回、4：冻结复审成功、5：冻结复审驳回）',
  `frozen_last_approve_date` datetime DEFAULT NULL COMMENT '冻结最后审核时间',
  `thaw_approve_status` tinyint(1) DEFAULT NULL COMMENT '解冻审核状态（1：等待、2：解冻初审成功、3：解冻初审驳回、4：解冻复审成功、5：解冻复审驳回）',
  `thaw_last_approve_date` datetime DEFAULT NULL COMMENT '解冻最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `thaw_create_user_id` bigint(18) DEFAULT NULL COMMENT '解冻创建人',
  `thaw_create_date` datetime DEFAULT NULL COMMENT '解冻创建时间',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COMMENT='企业冻结账户申请信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_frozen_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_frozen_process`;
CREATE TABLE `t_flow_enterprise_frozen_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `apply_id` bigint(18) NOT NULL COMMENT '申请记录ID',
  `process_type` tinyint(1) DEFAULT NULL COMMENT '审核类型(1:冻结、2:解冻)',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\n            1审核通过\n            2审核驳回\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审、3：打款）',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8 COMMENT='企业冻结账户申请审核记录';

-- ----------------------------
-- Table structure for t_flow_enterprise_loan
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_loan`;
CREATE TABLE `t_flow_enterprise_loan` (
  `loan_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '借款ID',
  `loan_code` varchar(100) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `loan_money` decimal(11,3) DEFAULT NULL,
  `loan_date` datetime DEFAULT NULL,
  `repayment_money` decimal(11,3) DEFAULT NULL,
  `last_repayment_date` datetime DEFAULT NULL,
  `repayment_number` int(2) DEFAULT NULL,
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态\n            1草稿\n            2等待审核\n            3初审通过\n            4初审驳回\n            5复审通过\n            6复审驳回',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `is_pay_off` tinyint(1) DEFAULT '0' COMMENT '是否还清（0：未还清，1：已还清）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '说明',
  `repayment_date` datetime DEFAULT NULL COMMENT '预计还未时间',
  PRIMARY KEY (`loan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1464 DEFAULT CHARSET=utf8 COMMENT='企业借款信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_loan_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_loan_process`;
CREATE TABLE `t_flow_enterprise_loan_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `loan_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\n            1审核通过\n            2审核驳回\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审、3：打款）',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2875 DEFAULT CHARSET=utf8 COMMENT='企业借款审核记录';

-- ----------------------------
-- Table structure for t_flow_enterprise_recharge_apply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_recharge_apply`;
CREATE TABLE `t_flow_enterprise_recharge_apply` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '代理商ID号',
  `apply_code` varchar(50) NOT NULL COMMENT '编号（生成规则：代理商编号+时间戳+5位随机数）',
  `source` tinyint(1) DEFAULT '1' COMMENT '来源（1为汇款，2微信支付，3为支付宝支付）',
  `apply_money` decimal(11,3) DEFAULT NULL,
  `transaction_number` varchar(100) DEFAULT NULL COMMENT '交易号',
  `credential_one` varchar(255) DEFAULT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `credential_two` varchar(255) DEFAULT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `credential_three` varchar(255) DEFAULT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `transaction_date` varchar(50) DEFAULT NULL COMMENT '付款日期',
  `remark` varchar(255) DEFAULT NULL COMMENT '说明内容',
  `top_proxy_id` bigint(18) DEFAULT NULL COMMENT '上级代理商',
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态\r\n            1草稿\r\n            2等待审核\r\n            3初审通过\r\n            4初审驳回\r\n            5复审通过\r\n            6复审驳回',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `apply_type` tinyint(1) DEFAULT '1' COMMENT '申请类型（1：正常、2测试款）',
  PRIMARY KEY (`apply_id`),
  KEY `FK_Reference_21` (`enterprise_id`),
  KEY `ix_t_flow_enterprise_recharge_apply_create_date` (`create_date`),
  KEY `ix_t_flow_enterprise_recharge_apply_enterprise_id` (`enterprise_id`),
  CONSTRAINT `FK_Reference_21` FOREIGN KEY (`enterprise_id`) REFERENCES `t_flow_enterprise` (`enterprise_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6890 DEFAULT CHARSET=utf8 COMMENT='企业充值申请信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_recharge_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_recharge_process`;
CREATE TABLE `t_flow_enterprise_recharge_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `apply_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\r\n            1审核通过\r\n            2审核驳回\r\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审）',
  PRIMARY KEY (`process_id`),
  KEY `FK_Reference_17` (`apply_id`),
  KEY `ix_t_flow_enterprise_recharge_process_apply_id` (`apply_id`),
  KEY `ix_t_flow_enterprise_recharge_process_approve_date` (`approve_date`)
) ENGINE=InnoDB AUTO_INCREMENT=14298 DEFAULT CHARSET=utf8 COMMENT='企业充值审核过程记录表（记录每次审核的人员信息）';

-- ----------------------------
-- Table structure for t_flow_enterprise_repaymen
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_repaymen`;
CREATE TABLE `t_flow_enterprise_repaymen` (
  `repaymen_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '还款ID',
  `loan_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `source` tinyint(1) DEFAULT '1' COMMENT '来源（1为汇款，2微信支付，3为支付宝支付）',
  `transaction_number` varchar(100) DEFAULT NULL COMMENT '交易号',
  `credential_one` varchar(255) DEFAULT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `repayment_money` decimal(11,3) DEFAULT NULL,
  `repayment_date` datetime DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '说明内容',
  `approve_user_id` bigint(18) DEFAULT NULL,
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态\n            1草稿\n            2等待审核\n            3初审通过\n            4初审驳回\n            5复审通过\n            6复审驳回',
  `approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`repaymen_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1461 DEFAULT CHARSET=utf8 COMMENT='企业还款记录表';

-- ----------------------------
-- Table structure for t_flow_enterprise_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_set`;
CREATE TABLE `t_flow_enterprise_set` (
  `set_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `enterprise_id` bigint(18) NOT NULL COMMENT '企业ID',
  `bank_account` varchar(50) DEFAULT NULL COMMENT '开户行',
  `account_opening` varchar(50) DEFAULT NULL COMMENT '开户省市',
  `card_number` varchar(20) DEFAULT NULL COMMENT '银行卡号',
  `beneficiary_name` varchar(20) DEFAULT NULL COMMENT '收款人姓名',
  `mobile` varchar(20) DEFAULT NULL COMMENT '联系电话',
  PRIMARY KEY (`set_id`),
  KEY `ix_t_flow_enterprise_set_enterprise_id` (`enterprise_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='企业设置表';

-- ----------------------------
-- Table structure for t_flow_enterprise_ticket_apply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_ticket_apply`;
CREATE TABLE `t_flow_enterprise_ticket_apply` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '申请ID',
  `apply_code` varchar(50) DEFAULT NULL COMMENT '申请编号',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `company_name` varchar(150) DEFAULT NULL COMMENT '开票公司名称',
  `ticket_type` tinyint(1) DEFAULT NULL COMMENT '票据类型（1：增值税普通发票、2：增值税专用发票）',
  `ticket_content` tinyint(1) DEFAULT NULL COMMENT '开票内容（1：电信增值业务）',
  `ticket_code` varchar(100) DEFAULT NULL COMMENT '发票编号',
  `apply_ticket_money` decimal(16,3) DEFAULT NULL,
  `actual_ticket_money` decimal(16,3) DEFAULT NULL,
  `enclosure` varchar(100) DEFAULT NULL COMMENT '附件',
  `remark` varchar(255) DEFAULT NULL COMMENT '注备',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '发票状态（1：草稿、2：等待审核、3：初审通过、4：初审驳回、5：复审通过、6：复审驳回、7：已开票、8：开票驳回）',
  `contact_name` varchar(100) DEFAULT NULL COMMENT '联系人',
  `contact_tel` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `contact_province_id` bigint(18) DEFAULT NULL COMMENT '省份',
  `contact_city_id` bigint(18) DEFAULT NULL COMMENT '城市',
  `contact_address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `apply_user_id` bigint(18) DEFAULT NULL COMMENT '申请人ID',
  `apply_date` datetime DEFAULT NULL COMMENT '申请时间',
  `open_ticket_date` datetime DEFAULT NULL COMMENT '开票时间',
  `open_ticket_uaer_id` bigint(18) DEFAULT NULL COMMENT '开票人ID',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8 COMMENT='企业开票信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_ticket_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_ticket_process`;
CREATE TABLE `t_flow_enterprise_ticket_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `apply_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT NULL COMMENT '审核阶段',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=367 DEFAULT CHARSET=utf8 COMMENT='企业开票审核信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_transfer_apply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_transfer_apply`;
CREATE TABLE `t_flow_enterprise_transfer_apply` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '申请单ID',
  `apply_code` varchar(50) DEFAULT NULL COMMENT '申请编号',
  `apply_money` decimal(11,3) DEFAULT NULL,
  `pay_enterprise_id` bigint(18) DEFAULT NULL COMMENT '支出企业ID',
  `receive_enterprise_id` bigint(18) DEFAULT NULL COMMENT '接收企业ID',
  `apply_user_id` bigint(18) DEFAULT NULL COMMENT '申请人',
  `apply_date` datetime DEFAULT NULL COMMENT '申请时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（1草稿、2等待审核、3初审通过、4初审驳回、5复审通过、6复审驳回）',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=612 DEFAULT CHARSET=utf8 COMMENT='企业资金划拨信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_transfer_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_transfer_process`;
CREATE TABLE `t_flow_enterprise_transfer_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `apply_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT NULL COMMENT '审核阶段',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1215 DEFAULT CHARSET=utf8 COMMENT='企业资金划拨审核信息表';

-- ----------------------------
-- Table structure for t_flow_enterprise_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_user`;
CREATE TABLE `t_flow_enterprise_user` (
  `user_id` bigint(18) NOT NULL COMMENT '用户ID',
  `enterprise_id` bigint(18) NOT NULL COMMENT '企业ID',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  KEY `ix_t_flow_enterprise_user_enterprise_id` (`enterprise_id`),
  KEY `ix_t_flow_enterprise_user_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='企业权限表';

-- ----------------------------
-- Table structure for t_flow_enterprise_withdraw_apply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_withdraw_apply`;
CREATE TABLE `t_flow_enterprise_withdraw_apply` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '代理商ID号',
  `apply_code` varchar(50) NOT NULL COMMENT '编号（生成规则：代理商编号+时间戳+5位随机数）',
  `apply_money` decimal(11,3) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL COMMENT '开户行',
  `account_opening` varchar(50) DEFAULT NULL COMMENT '开户省市',
  `card_number` varchar(20) DEFAULT NULL COMMENT '银行卡号',
  `beneficiary_name` varchar(20) DEFAULT NULL COMMENT '收款人姓名',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `transaction_date` datetime DEFAULT NULL COMMENT '交易时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '说明内容',
  `top_proxy_id` bigint(18) DEFAULT NULL COMMENT '上级代理商',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态 1草稿 2等待审核 3初审通过 4初审驳回 5复审通过 6复审驳回 7打款成功 8打款驳回',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `transaction_number` varchar(100) DEFAULT NULL COMMENT '交易号',
  `payment_account` varchar(20) DEFAULT NULL,
  `payment_money` decimal(11,3) DEFAULT NULL,
  `payment_bank` varchar(50) DEFAULT NULL COMMENT '打款银行',
  `payment_date` datetime DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `is_play_money` tinyint(1) DEFAULT '0' COMMENT '是否打款（0.未打款，1.已打款）',
  PRIMARY KEY (`apply_id`),
  KEY `FK_Reference_22` (`enterprise_id`),
  KEY `ix_t_flow_enterprise_withdraw_apply_create_date` (`create_date`),
  KEY `ix_t_flow_enterprise_withdraw_apply_enterprise_id` (`enterprise_id`),
  KEY `ix_t_flow_enterprise_withdraw_apply_top_proxy_id` (`top_proxy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='企业提现申请表';

-- ----------------------------
-- Table structure for t_flow_enterprise_withdraw_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_enterprise_withdraw_process`;
CREATE TABLE `t_flow_enterprise_withdraw_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `apply_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\r\n            1审核通过\r\n            2审核驳回\r\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审、3：打款）',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  PRIMARY KEY (`process_id`),
  KEY `FK_Reference_18` (`apply_id`),
  KEY `ix_t_flow_enterprise_withdraw_process_approve_date` (`approve_date`),
  KEY `ix_t_flow_enterprise_withdraw_process_process_id` (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COMMENT='企业提现审核过程记录表';

-- ----------------------------
-- Table structure for t_flow_exchange_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_exchange_record`;
CREATE TABLE `t_flow_exchange_record` (
  `exchange_score_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '积分兑换id',
  `order_code` varchar(100) DEFAULT NULL COMMENT '订单编号',
  `product_id` bigint(18) DEFAULT NULL,
  `operator_id` tinyint(1) DEFAULT NULL COMMENT '三大运营商（1.移动，2.联通，3.电信）',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `exchage_time` datetime DEFAULT NULL COMMENT '兑换时间',
  `order_date` datetime DEFAULT NULL COMMENT '下单时间',
  `complete_time` datetime DEFAULT NULL,
  `exchange_status` tinyint(1) DEFAULT NULL COMMENT '兑换状态 (1.未兑换，2，已兑换)',
  `refund_status` tinyint(1) DEFAULT NULL COMMENT '退积分状态（1.未退积分，2已退积分）',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2企业）',
  `exchange_score` int(10) DEFAULT NULL COMMENT '兑换积分',
  `wx_user_id` bigint(18) DEFAULT NULL COMMENT '微信用户id',
  PRIMARY KEY (`exchange_score_id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_flowcode
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_flowcode`;
CREATE TABLE `t_flow_flowcode` (
  `flowcode_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '流量码id',
  `flowcode_code` varchar(20) DEFAULT NULL COMMENT '流量码号',
  `product_name` varchar(5) DEFAULT NULL COMMENT '产品名称',
  `size` varchar(5) DEFAULT NULL COMMENT '流量包大小',
  `type` tinyint(1) DEFAULT NULL COMMENT '流量码属性(1.全国,2广东省)',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态：（1.未激活2.已激活3.已使用4.已作废）',
  `end_time` datetime DEFAULT NULL COMMENT '截止时间',
  `order_code` varchar(50) DEFAULT NULL COMMENT '下单编号',
  `order_status` tinyint(2) DEFAULT NULL COMMENT '充值状态',
  `phone` varchar(18) DEFAULT NULL COMMENT '手机号',
  `operator_id` tinyint(1) DEFAULT NULL COMMENT '运营商（1.移动，2.联通，3电信）',
  `order_time` datetime DEFAULT NULL COMMENT '兑换时间',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `wx_name` varchar(40) DEFAULT NULL,
  `wx_photo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`flowcode_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_flowcode_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_flowcode_set`;
CREATE TABLE `t_flow_flowcode_set` (
  `set_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '活动id',
  `background_img` varchar(200) DEFAULT NULL COMMENT '背景图片',
  `logo_img` varchar(200) DEFAULT NULL COMMENT 'logo',
  `activity_rule` text COMMENT '活动规则',
  `url` varchar(200) DEFAULT NULL COMMENT '活动地址',
  `share_title` varchar(20) DEFAULT NULL COMMENT '分享主题',
  `share_content` varchar(100) DEFAULT NULL COMMENT '分享内容',
  `share_img` varchar(200) DEFAULT NULL,
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '需改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_gateway_channel
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_gateway_channel`;
CREATE TABLE `t_flow_gateway_channel` (
  `gateway_channel_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '网关上游通道id',
  `server_id` bigint(18) NOT NULL COMMENT '网关服务器ID',
  `channel_id` bigint(18) NOT NULL COMMENT '通道ID',
  `account` varchar(100) NOT NULL COMMENT '账号',
  `password` varchar(100) NOT NULL COMMENT '密码/ApiKey',
  `submit_url` varchar(200) NOT NULL COMMENT '提交地址',
  `submit_size` int(11) NOT NULL DEFAULT '1' COMMENT '每次提交数量',
  `submit_time` int(11) NOT NULL DEFAULT '1' COMMENT '多少秒后提交',
  `submit_freq` int(11) NOT NULL DEFAULT '20' COMMENT '每秒提交数量',
  `report_url` varchar(200) DEFAULT NULL COMMENT '状态报告查询地址',
  `report_size` int(11) NOT NULL DEFAULT '1' COMMENT '每次提交数量',
  `report_time` int(11) NOT NULL DEFAULT '1' COMMENT '多少秒后查询',
  `report_freq` int(11) NOT NULL DEFAULT '20' COMMENT '每秒提交数量',
  `report_type` int(11) NOT NULL DEFAULT '2' COMMENT '网关报告类型:0-没有,1-推送,2-查询',
  `balance_url` varchar(200) DEFAULT NULL COMMENT '余额查询地址',
  `protocol` varchar(50) NOT NULL,
  `ext_parameter` varchar(500) DEFAULT NULL,
  `range` int(11) NOT NULL DEFAULT '0' COMMENT '0 全国 1 省 3 城市',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 无效 1 有效',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`server_id`,`channel_id`,`gateway_channel_id`),
  UNIQUE KEY `channel_id` (`channel_id`),
  KEY `gateway_channel_id` (`gateway_channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_gateway_protocol
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_gateway_protocol`;
CREATE TABLE `t_flow_gateway_protocol` (
  `protocol_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '协议ID',
  `protocol` varchar(50) NOT NULL COMMENT '协议名',
  `channel` varchar(200) DEFAULT NULL COMMENT '通道类名',
  `submit` varchar(200) CHARACTER SET utf8mb4 NOT NULL COMMENT '网关订单提交操作类名',
  `reportReceiver` varchar(200) DEFAULT NULL COMMENT '网关报告解析类名',
  `reportQuery` varchar(200) DEFAULT NULL COMMENT '网关报告查询类名',
  `balance` varchar(200) DEFAULT NULL COMMENT '余额查询类名',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 无效 1 有效',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`protocol_id`),
  UNIQUE KEY `protocol` (`protocol`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_gateway_run
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_gateway_run`;
CREATE TABLE `t_flow_gateway_run` (
  `run_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '服务器id',
  `server_id` bigint(18) NOT NULL COMMENT '网关服务器ID',
  `server_name` varchar(50) NOT NULL COMMENT '服务器名称',
  `server_ip` varchar(50) DEFAULT NULL COMMENT '服务器IP',
  `server_remark` varchar(100) DEFAULT NULL COMMENT ' 备注',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 未启动 1 已启动',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`run_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_mobile_blacklist
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_mobile_blacklist`;
CREATE TABLE `t_flow_mobile_blacklist` (
  `mobile_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) NOT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`mobile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2816 DEFAULT CHARSET=utf8 COMMENT='号码黑名单';

-- ----------------------------
-- Table structure for t_flow_monitor_channel_stat
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_monitor_channel_stat`;
CREATE TABLE `t_flow_monitor_channel_stat` (
  `stat_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(20) NOT NULL DEFAULT '0',
  `channel_code` varchar(500) DEFAULT NULL,
  `channel_name` varchar(500) DEFAULT NULL,
  `stat_year` int(11) DEFAULT NULL,
  `stat_month` int(11) DEFAULT NULL,
  `stat_day` int(11) DEFAULT NULL,
  `stat_time` bigint(20) NOT NULL DEFAULT '0',
  `total_count` bigint(20) DEFAULT NULL,
  `success_count` bigint(20) DEFAULT NULL,
  `success_amount` decimal(12,3) DEFAULT NULL,
  `faile_count` bigint(20) DEFAULT NULL,
  `faile_amount` decimal(12,3) DEFAULT NULL,
  `rand_id` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '分散插入ID',
  PRIMARY KEY (`channel_id`,`stat_time`,`rand_id`),
  KEY `stat_id` (`stat_id`),
  KEY `ix_monitor_channel_stat_channel_id_stat_day` (`channel_id`,`stat_day`),
  KEY `ix_monitor_channel_stat_stat_time_total_count` (`stat_time`,`total_count`)
) ENGINE=InnoDB AUTO_INCREMENT=4840246 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_monitor_province_stat
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_monitor_province_stat`;
CREATE TABLE `t_flow_monitor_province_stat` (
  `stat_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `province_id` bigint(20) NOT NULL DEFAULT '0',
  `province_name` varchar(500) DEFAULT NULL,
  `stat_year` int(11) DEFAULT NULL,
  `stat_month` int(11) DEFAULT NULL,
  `stat_day` int(11) DEFAULT NULL,
  `stat_time` bigint(20) NOT NULL DEFAULT '0',
  `total_count` bigint(20) DEFAULT NULL,
  `success_count` bigint(20) DEFAULT NULL,
  `success_amount` decimal(12,3) DEFAULT NULL,
  `faile_count` bigint(20) DEFAULT NULL,
  `faile_amount` decimal(12,3) DEFAULT NULL,
  `rand_id` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '分散插入ID',
  PRIMARY KEY (`province_id`,`stat_time`,`rand_id`),
  KEY `stat_id` (`stat_id`),
  KEY `ix_monitor_province_stat_province_id_stat_day` (`province_id`,`stat_day`)
) ENGINE=InnoDB AUTO_INCREMENT=371208 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_operator_contact
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_operator_contact`;
CREATE TABLE `t_flow_operator_contact` (
  `contact_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '联系人ID',
  `operator_info_id` bigint(18) DEFAULT NULL COMMENT '资源方信息ID',
  `contact_name` varchar(50) DEFAULT NULL COMMENT '联系人姓名',
  `tel` varchar(13) DEFAULT NULL COMMENT '联系电话',
  `job` varchar(50) DEFAULT NULL COMMENT '职位',
  `qq` bigint(12) DEFAULT NULL COMMENT 'QQ号',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `remark` text COMMENT '备注',
  `status` tinyint(1) DEFAULT NULL COMMENT '启用禁用状态，0：已禁用、1：正常、2：已删除',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='资源方联系人信息表';

-- ----------------------------
-- Table structure for t_flow_operator_info
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_operator_info`;
CREATE TABLE `t_flow_operator_info` (
  `operator_info_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '运营商信息ID',
  `operator_name` varchar(50) DEFAULT NULL COMMENT '资源方名称',
  `connect_type` tinyint(1) DEFAULT '0' COMMENT '连接类型（0：第三方公司、1：直连运营商）',
  `resources_type` varchar(50) DEFAULT NULL COMMENT '资源类型（1：省内号码省内流量、2：省内号码全国流量、3：全国号码全国流量）',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '运营商ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省ID',
  `city_id` bigint(18) DEFAULT NULL COMMENT '市ID',
  `discount` decimal(9,3) DEFAULT NULL,
  `quota` decimal(11,3) DEFAULT NULL,
  `negotiate_status` tinyint(1) DEFAULT NULL COMMENT '洽谈状态（1：等待洽谈、2：洽谈中、3：走合同中、4：合作中、5：复通中、6：停止合作）',
  `channel_status` tinyint(1) DEFAULT NULL COMMENT '通道状态（1：等待对接、2：测试中、3：已开通、4：已关闭）',
  `record_user_id` bigint(18) DEFAULT NULL COMMENT '跟进人ID',
  `start_date` datetime DEFAULT NULL COMMENT '起始时间',
  `fall_company` tinyint(2) DEFAULT NULL COMMENT '落地公司（1：江西尚通、2：广东尚通、3：广东尚云、4：深圳诚汇赢、5：深圳赢通、6：深圳真辉映、7：北京达通、8：君诚科技）',
  `is_limit` tinyint(1) DEFAULT NULL COMMENT '是否限价：1是；0否',
  `is_rebate` tinyint(1) DEFAULT NULL COMMENT '是否后返利：1是、0否',
  `connect_date` datetime DEFAULT NULL COMMENT '预计接通时间',
  `remark` text COMMENT '备注',
  `status` tinyint(1) DEFAULT NULL COMMENT '启用禁用状态，0：已禁用、1：正常、2：已删除',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`operator_info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COMMENT='资源方信息表(记录市场经理跟进运营情况）';

-- ----------------------------
-- Table structure for t_flow_operator_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_operator_record`;
CREATE TABLE `t_flow_operator_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '跟进记录ID',
  `operator_info_id` bigint(18) DEFAULT NULL COMMENT '资源方信息ID',
  `contact_id` bigint(18) DEFAULT NULL COMMENT '拜访人ID',
  `visit_type` tinyint(1) DEFAULT '1' COMMENT '拜访方式（1:电话、2:面谈、3:邮件、4:其他）',
  `record_date` datetime DEFAULT NULL COMMENT '拜访时间',
  `content` text COMMENT '拜访内容',
  `negotiate_status` tinyint(1) DEFAULT NULL COMMENT '洽谈状态（1：等待洽谈、2：洽谈中、3：走合同中、4：合作中、5：复通中、6：停止合作）',
  `channel_status` tinyint(1) DEFAULT NULL COMMENT '通道状态（1：等待对接、2：测试中、3：已开通、4：已关闭）',
  `record_user_id` bigint(18) DEFAULT NULL COMMENT '跟进人ID',
  `follow_content` varchar(255) DEFAULT NULL COMMENT '跟进内容',
  `remark` text COMMENT '备注',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='资源方跟进记录表';

-- ----------------------------
-- Table structure for t_flow_operator_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_operator_user`;
CREATE TABLE `t_flow_operator_user` (
  `operator_role_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '资源方权限id号',
  `user_id` bigint(18) DEFAULT NULL COMMENT '角户id号',
  `operator_info_id` bigint(18) DEFAULT NULL COMMENT '权限状态（0：为所有权限、id：指定权限）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`operator_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8 COMMENT='资源方权限信息表';

-- ----------------------------
-- Table structure for t_flow_opphase
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_opphase`;
CREATE TABLE `t_flow_opphase` (
  `id` int(11) NOT NULL,
  `phone_section` varchar(15) DEFAULT NULL COMMENT '号段',
  `operaid` varchar(5) DEFAULT NULL COMMENT '区ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运营商解析表';

-- ----------------------------
-- Table structure for t_flow_order
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order`;
CREATE TABLE `t_flow_order` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市id',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_complete_time` (`complete_time`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_enterprise_id_orderno_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id_orderno_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_fow_order_refund_id` (`refund_id`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`proxy_id`,`enterprise_id`),
  KEY `ix_t_flow_order_channel_order_code` (`channel_order_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Table structure for t_flow_order_201604
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_201604`;
CREATE TABLE `t_flow_order_201604` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `top_rebate_discount` decimal(5,3) DEFAULT '0.000' COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT '0.000' COMMENT '备通道返利折口',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `last_update_time` datetime DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_enterprise_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Table structure for t_flow_order_201605
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_201605`;
CREATE TABLE `t_flow_order_201605` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `top_rebate_discount` decimal(5,3) DEFAULT '0.000' COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT '0.000' COMMENT '备通道返利折口',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `last_update_time` datetime DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_enterprise_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`order_status`,`price`,`discount_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Table structure for t_flow_order_201606
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_201606`;
CREATE TABLE `t_flow_order_201606` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市id',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_enterprise_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_order_201607
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_201607`;
CREATE TABLE `t_flow_order_201607` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市id',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_enterprise_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_order_201608
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_201608`;
CREATE TABLE `t_flow_order_201608` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市id',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_enterprise_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_order_201609
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_201609`;
CREATE TABLE `t_flow_order_201609` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市id',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_enterprise_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_order_201610
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_201610`;
CREATE TABLE `t_flow_order_201610` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市id',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_enterprise_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_order_cache
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_cache`;
CREATE TABLE `t_flow_order_cache` (
  `order_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '备通道ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `refund_id` bigint(18) DEFAULT '0' COMMENT '退款ID号',
  `top_rebate_discount` decimal(5,3) DEFAULT '0.000' COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT '0.000' COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `url` varchar(100) DEFAULT NULL,
  `city_id` bigint(18) DEFAULT '0' COMMENT '手机号市ID',
  `range` int(11) NOT NULL DEFAULT '0' COMMENT '0 全国 1 省 3 城市',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=796025340943191982 DEFAULT CHARSET=utf8 COMMENT='订单预处理表';

-- ----------------------------
-- Table structure for t_flow_order_callback
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_callback`;
CREATE TABLE `t_flow_order_callback` (
  `callback_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `order_id` bigint(18) DEFAULT NULL COMMENT '所属订单ID',
  `url` varchar(255) DEFAULT NULL COMMENT '回调地址',
  `content` varchar(255) DEFAULT NULL COMMENT '发送信息',
  `rece_content` text COMMENT '执行信息',
  `times` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '状态（0：等待，1：成功，2：失败）',
  `end_date` datetime DEFAULT NULL COMMENT '完成时间',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '备通道ID',
  `orderno_id` varchar(50) DEFAULT NULL,
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `final_channel_id` bigint(18) DEFAULT NULL COMMENT '最终通道ID',
  PRIMARY KEY (`callback_id`),
  KEY `ix_t_flow_order_callback_end_date` (`end_date`),
  KEY `ix_t_flow_order_callback_order_id` (`order_id`),
  KEY `ix_t_flow_order_callback_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=45332654 DEFAULT CHARSET=utf8 COMMENT='订单回调表(平台给下级用户的回调)';

-- ----------------------------
-- Table structure for t_flow_order_callback_201608
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_callback_201608`;
CREATE TABLE `t_flow_order_callback_201608` (
  `callback_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_id` bigint(18) DEFAULT NULL COMMENT '所属订单ID',
  `url` varchar(100) DEFAULT NULL COMMENT '回调地址',
  `content` varchar(255) DEFAULT NULL COMMENT '发送信息',
  `rece_content` text COMMENT '执行信息',
  `times` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '状态（0：等待，1：成功，2：失败）',
  `end_date` datetime DEFAULT NULL COMMENT '完成时间',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '备通道ID',
  `orderno_id` varchar(50) DEFAULT NULL,
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  KEY `fk_t_flow_order_callback_his_order_id` (`order_id`),
  KEY `ix_t_flow_order_callback_order_id` (`order_id`),
  KEY `ix_t_flow_order_callback_status` (`status`),
  KEY `ix_t_flow_order_callback_end_date` (`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单回调历史表(平台给下级用户的回调)';

-- ----------------------------
-- Table structure for t_flow_order_callback_his
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_callback_his`;
CREATE TABLE `t_flow_order_callback_his` (
  `callback_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_id` bigint(18) DEFAULT NULL COMMENT '所属订单ID',
  `url` varchar(255) DEFAULT NULL COMMENT '回调地址',
  `content` varchar(255) DEFAULT NULL COMMENT '发送信息',
  `rece_content` text COMMENT '执行信息',
  `times` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '状态（0：等待，1：成功，2：失败）',
  `end_date` datetime DEFAULT NULL COMMENT '完成时间',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '备通道ID',
  `orderno_id` varchar(50) DEFAULT NULL,
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `final_channel_id` bigint(18) DEFAULT NULL COMMENT '最终通道ID',
  KEY `fk_t_flow_order_callback_his_order_id` (`order_id`),
  KEY `ix_t_flow_order_callback_order_id` (`order_id`),
  KEY `ix_t_flow_order_callback_status` (`status`),
  KEY `ix_t_flow_order_callback_end_date` (`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单回调历史表(平台给下级用户的回调)';

-- ----------------------------
-- Table structure for t_flow_order_channel_cache
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_channel_cache`;
CREATE TABLE `t_flow_order_channel_cache` (
  `order_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '备通道ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `refund_id` bigint(18) DEFAULT '0' COMMENT '退款ID号',
  `top_rebate_discount` decimal(5,3) DEFAULT '0.000' COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT '0.000' COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `url` varchar(100) DEFAULT NULL,
  `city_id` bigint(18) DEFAULT '0' COMMENT '手机号市ID',
  `range` int(11) NOT NULL DEFAULT '0' COMMENT '0 全国 1 省 3 城市',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=804138871484543608 DEFAULT CHARSET=utf8 COMMENT='通道缓存表';

-- ----------------------------
-- Table structure for t_flow_order_get_log
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_get_log`;
CREATE TABLE `t_flow_order_get_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(4000) DEFAULT NULL,
  `dt` timestamp(6) NULL DEFAULT NULL,
  `channel_id` bigint(20) DEFAULT NULL,
  `platform_id` bigint(20) DEFAULT NULL,
  `end_dt` timestamp(6) NULL DEFAULT NULL,
  `get_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_t_flow_order_get_log_dt` (`dt`)
) ENGINE=InnoDB AUTO_INCREMENT=31621688 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_order_handle_fail
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_handle_fail`;
CREATE TABLE `t_flow_order_handle_fail` (
  `fail_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '失败记录ID',
  `order_id` bigint(18) DEFAULT NULL COMMENT '订单ID',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `account_id` bigint(18) DEFAULT NULL COMMENT '账号ID号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `is_success` tinyint(1) DEFAULT NULL COMMENT '订单是否成功（0：未成功、1：已成功）',
  `handle_status` tinyint(1) DEFAULT '0' COMMENT '处理状态（0：未处理、1已处理）',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `end_date` datetime DEFAULT NULL COMMENT '完成时间',
  PRIMARY KEY (`fail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=825 DEFAULT CHARSET=utf8 COMMENT='订单处理失败信息表';

-- ----------------------------
-- Table structure for t_flow_order_pre
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_pre`;
CREATE TABLE `t_flow_order_pre` (
  `order_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` decimal(5,3) DEFAULT NULL,
  `one_proxy_discount` decimal(5,3) DEFAULT NULL,
  `profit` decimal(11,3) DEFAULT NULL,
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `back_top_discount` decimal(5,3) DEFAULT NULL,
  `top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `city_id` bigint(18) DEFAULT '0' COMMENT '手机号市ID',
  PRIMARY KEY (`order_id`),
  KEY `ix_order_pre_order_status_channel_id_last_update_time` (`order_status`,`is_using`,`channel_id`,`last_update_time`),
  KEY `ix_order_pre_order_status_back_channel_id_last_update_time` (`order_status`,`is_using`,`back_channel_id`,`last_update_time`),
  KEY `ix_order_pre_channel_order_code` (`channel_order_code`),
  KEY `ix_order_pre_order_code` (`order_code`),
  KEY `ix_order_pre_orderno_id` (`orderno_id`)
) ENGINE=InnoDB AUTO_INCREMENT=808483260820868648 DEFAULT CHARSET=utf8 COMMENT='订单预处理表';

-- ----------------------------
-- Table structure for t_flow_order_refund
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_refund`;
CREATE TABLE `t_flow_order_refund` (
  `refund_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '退款',
  `refund_code` varchar(50) NOT NULL COMMENT '退款编号',
  `refund_type` tinyint(1) DEFAULT '1' COMMENT '退款类型（1：余额退款、2：微信退款、3：支付宝退款）',
  `status` tinyint(1) DEFAULT NULL COMMENT '退款状态（0：等待审核、1：初审通过、2：初审驳回、3：复审通过、4：复审驳回）',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `credential_one` varchar(255) NOT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `credential_two` varchar(255) NOT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `credential_three` varchar(255) NOT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `order_id` bigint(20) DEFAULT NULL COMMENT '订单ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+时间戳+4位随机）\r\n            ',
  `channel_order_code` varchar(50) DEFAULT NULL COMMENT '通道订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `operator_id` bigint(18) NOT NULL COMMENT '运营商ID',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `order_status` tinyint(1) NOT NULL COMMENT '订单类型\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `refund_cause` tinyint(1) DEFAULT '3' COMMENT '退款原因（1.流量未到账，2.订单超时，3.其他）',
  PRIMARY KEY (`refund_id`),
  KEY `ix_t_flow_order_refund_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_refund_channel_product_id` (`channel_product_id`),
  KEY `ix_t_flow_order_refund_create_date` (`create_date`),
  KEY `ix_t_flow_order_refund_enterprise_id` (`enterprise_id`),
  KEY `ix_t_flow_order_refund_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_refund_order_status` (`order_status`),
  KEY `ix_t_flow_order_refund_proxy_id` (`proxy_id`),
  KEY `ix_t_flow_order_refund_refund_code` (`refund_code`),
  KEY `ix_t_flow_order_refund_refund_type` (`refund_type`),
  KEY `ix_t_flow_order_refund_status` (`status`),
  KEY `ix_t_flow_order_refund_order_code` (`order_code`),
  KEY `ix_t_flow_order_refund_channel_order_code` (`channel_order_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2375 DEFAULT CHARSET=utf8 COMMENT='订单退款表';

-- ----------------------------
-- Table structure for t_flow_order_refund_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_refund_process`;
CREATE TABLE `t_flow_order_refund_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `refund_id` bigint(18) DEFAULT NULL COMMENT '退款记录ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\r\n            1审核通过\r\n            2审核驳回\r\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审）',
  PRIMARY KEY (`process_id`),
  KEY `FK_Reference_24` (`refund_id`),
  CONSTRAINT `FK_Reference_24` FOREIGN KEY (`refund_id`) REFERENCES `t_flow_order_refund` (`refund_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4671 DEFAULT CHARSET=utf8 COMMENT='订单退款流程表';

-- ----------------------------
-- Table structure for t_flow_order_sql_result_stat
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_sql_result_stat`;
CREATE TABLE `t_flow_order_sql_result_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sql_hash_value` bigint(20) NOT NULL,
  `sql_text` varchar(4000) DEFAULT NULL,
  `sql_fulltext` text,
  `result_stat_type` int(11) NOT NULL,
  `result_stat_date` bigint(20) NOT NULL,
  `sql_result_stat` text,
  `user_id` bigint(18) NOT NULL COMMENT '用户ID',
  `user_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户类型(1：尚通、2：代理商、3企业)',
  `cache_time` datetime NOT NULL COMMENT '缓存时间',
  PRIMARY KEY (`sql_hash_value`,`result_stat_type`,`result_stat_date`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=155645 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_order_tran_log
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_tran_log`;
CREATE TABLE `t_flow_order_tran_log` (
  `log_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `order_id` bigint(18) DEFAULT NULL COMMENT '订单ID',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `order_status` int(11) DEFAULT NULL COMMENT '订单状态',
  `tran_type` tinyint(1) DEFAULT NULL COMMENT '处理类型  订单失败（10 回滚，11 主通失败 12 备用通道失败），订单成功（20 回滚，21 主通成功，22 备用通道成功）',
  `handle_status` tinyint(1) DEFAULT '0' COMMENT '处理状态（0：未处理、1已处理）',
  `create_date` timestamp(6) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(6) COMMENT '创建时间',
  `end_date` timestamp(6) NULL DEFAULT NULL COMMENT '完成时间',
  PRIMARY KEY (`log_id`),
  KEY `ix_t_flow_order_tran_log_order_id` (`order_id`),
  KEY `ix_t_flow_order_tran_log_create_date` (`create_date`)
) ENGINE=InnoDB AUTO_INCREMENT=47191022 DEFAULT CHARSET=utf8 COMMENT='订单处理失败信息表';


-- ----------------------------
-- Table structure for `t_flow_user_set`
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_user_set`;
CREATE TABLE `t_flow_user_set` (
  `account_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '账户ID',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `wx_appid` varchar(100) DEFAULT NULL COMMENT '公众号',
  `wx_appsecret` varchar(100) DEFAULT NULL COMMENT 'APPSECRET',
  `wx_mchid` varchar(100) DEFAULT NULL COMMENT '商户号',
  `wx_key` varchar(50) DEFAULT NULL COMMENT '商户号密钥',
  `wx_pem_file_one` varchar(100) DEFAULT NULL COMMENT 'pem文件1',
  `wx_pem_file_two` varchar(100) DEFAULT NULL COMMENT 'pem文件2',
  `alipay_partner` varchar(100) DEFAULT NULL COMMENT '支付宝账号',
  `alipay_key` varchar(100) DEFAULT NULL COMMENT '支付宝密钥',
  `alipay_pem_file` varchar(100) DEFAULT NULL COMMENT '支付宝pem文件',
  `alipay_pem_file_two` varchar(100) DEFAULT NULL,
  `app_appid` varchar(100) DEFAULT NULL,
  `app_appsecret` varchar(100) DEFAULT NULL,
  `app_mchid` varchar(100) DEFAULT NULL,
  `app_key` varchar(50) DEFAULT NULL,
  `paykey` varchar(50) DEFAULT NULL COMMENT '密钥key 用于支付宝退款',
  `app_pem_file_one` varchar(100) DEFAULT NULL,
  `app_pem_file_two` varchar(100) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `payment_type` tinyint(1) DEFAULT '2' COMMENT '收款方式（1.运营方收款，2企业收款，3代理商收款）',
  `third_app_key` varchar(32) DEFAULT NULL,
  `third_app_code` varchar(32) DEFAULT NULL,
  `wx_name` varchar(100) DEFAULT NULL COMMENT '微信号',
  `wx_type` tinyint(1) DEFAULT '1' COMMENT '公众号类型：1.服务号 2.订阅号',
  `explanation` text COMMENT '充值说明',
  `consumer_phone` varchar(15) DEFAULT NULL COMMENT '客服电话',
  `pc_alipay_account` varchar(100) DEFAULT NULL COMMENT '网页支付宝账号',
  `pc_alipay_partner` text COMMENT '网页支付宝商户号',
  `pc_alipay_key` text COMMENT '网页支付宝密钥',
  `pc_explanation` text COMMENT '网页充值说明',
  `pc_notice` text COMMENT '网页公告',
  `pub_notice` text COMMENT '公共公告',
  `template_type` tinyint(1) DEFAULT NULL COMMENT '模板ID',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=483 DEFAULT CHARSET=utf8 COMMENT='用户设置信息表';


-- ----------------------------
-- Table structure for `t_flow_user_sceneset`
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_user_sceneset`;
CREATE TABLE `t_flow_user_sceneset` (
  `user_sceneid` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `user_set_id` bigint(18) DEFAULT NULL COMMENT '用户账户设置ID',
  `user_province_type` tinyint(1) DEFAULT NULL COMMENT '从折扣类型',
  `user_headpics` text COMMENT '图片轮播路径(json格式)',
  `follow_type` tinyint(1) DEFAULT NULL COMMENT '是否需要关注',
  PRIMARY KEY (`user_sceneid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='微信账号设置';


-- ----------------------------
-- Table structure for t_flow_order_update
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_update`;
CREATE TABLE `t_flow_order_update` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `order_code` varchar(50) NOT NULL COMMENT '订单号（生成规则：电话号码+13位时间戳+6位随机）',
  `channel_order_code` varchar(50) DEFAULT '' COMMENT '上游通道返回的订单号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理商名称',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `enterprise_name` varchar(100) DEFAULT NULL COMMENT '企业名称',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '主通道产品ID',
  `channel_product_id` bigint(18) NOT NULL COMMENT '主通道产品ID',
  `back_channel_product_id` bigint(18) NOT NULL COMMENT '备用通道产品ID',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省份ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `pay_type` tinyint(1) DEFAULT '1' COMMENT '支付类型（1：账户余额、2：微信、3：支付宝）',
  `is_payment` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否支付（0：否、1：是）',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `order_effect_date` datetime NOT NULL COMMENT '生效时间',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `success_channel_product_id` bigint(18) DEFAULT NULL COMMENT '最终成功通道产品ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型 1下游接口 2平台 3网站 4移动端',
  `back_content` text COMMENT '订单信息（上游返回的信息和时间）',
  `back_fail_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '状态反馈',
  `complete_time` datetime(6) DEFAULT NULL COMMENT '完成时间',
  `profit_case` text NOT NULL COMMENT 'json，记录所有该充值订单产生的各级返利数据 格式为json数组 array(array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣''),array(''代理商/企业类型'',''ID号'',''返利金额;单位:元'',''自身折扣'',''下级折扣'')...)',
  `is_profit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红 0否 1是 2不需要分红',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '订单处理状态（0：未处理、1：正在处理、2：处理完成）',
  `top_discount` float DEFAULT '1' COMMENT '上游折扣',
  `one_proxy_discount` float DEFAULT '1' COMMENT '一级代理折扣',
  `profit` float DEFAULT '0' COMMENT '平台利润',
  `one_proxy_id` bigint(18) DEFAULT NULL COMMENT '一级代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '一级代理商名称',
  `orderno_id` varchar(50) DEFAULT NULL,
  `refund_id` bigint(20) DEFAULT '0' COMMENT '退款ID号',
  `back_top_discount` float DEFAULT '1',
  `top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '主通道返利折口',
  `back_top_rebate_discount` decimal(5,3) DEFAULT NULL COMMENT '备通道返利折口',
  `last_update_time` datetime(6) DEFAULT NULL COMMENT '最后更新时间',
  `channel_code` varchar(50) DEFAULT NULL COMMENT '主通道代码',
  `back_channel_code` varchar(50) DEFAULT NULL COMMENT '备通道代码',
  `product_province_id` bigint(20) DEFAULT NULL COMMENT '产品省份iD',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `province_name` varchar(100) DEFAULT NULL COMMENT '手机号省份名',
  `city_name` varchar(100) DEFAULT NULL COMMENT '手机号市名',
  `city_id` bigint(18) DEFAULT '0' COMMENT '市id',
  PRIMARY KEY (`order_id`),
  KEY `ix_t_flow_order_back_channel_id` (`back_channel_id`),
  KEY `ix_t_flow_order_channel_id` (`channel_id`),
  KEY `ix_t_flow_order_order_code` (`order_code`),
  KEY `ix_t_flow_order_complete_time` (`complete_time`),
  KEY `ix_t_flow_order_operator_id` (`operator_id`),
  KEY `ix_t_flow_order_mobile` (`mobile`),
  KEY `ix_t_flow_order_order_status_order_date` (`order_date`,`order_status`,`price`,`discount_price`),
  KEY `ix_t_flow_order_enterprise_id_orderno_id` (`enterprise_id`,`orderno_id`),
  KEY `ix_t_flow_order_proxy_id_orderno_id` (`proxy_id`,`orderno_id`),
  KEY `ix_t_fow_order_refund_id` (`refund_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Table structure for t_flow_order_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_order_user`;
CREATE TABLE `t_flow_order_user` (
  `user_id` bigint(20) NOT NULL COMMENT '企业ID代理ID',
  `user_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `complete_time` bigint(50) NOT NULL COMMENT '下单日期',
  `order_num` bigint(20) NOT NULL COMMENT '下单数量',
  PRIMARY KEY (`user_id`,`user_type`,`complete_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户日提交订单统计表';

-- ----------------------------
-- Table structure for t_flow_pay_order
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_pay_order`;
CREATE TABLE `t_flow_pay_order` (
  `pay_order_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '支付订单ID号',
  `pay_order_code` varchar(100) NOT NULL DEFAULT '' COMMENT '预支付订单编号',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `order_code` varchar(50) DEFAULT '' COMMENT '订单编号(order表)',
  `product_id` bigint(18) DEFAULT NULL COMMENT '流量包ID(通道产品表)',
  `number` varchar(100) DEFAULT NULL COMMENT '交易号',
  `pay_type` tinyint(1) DEFAULT NULL COMMENT '支付类型(1：支付宝、2：微信)',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `price` decimal(6,3) DEFAULT NULL,
  `discount_price` decimal(6,3) DEFAULT NULL,
  `pay_status` tinyint(1) DEFAULT '1' COMMENT '支付状态（1：未支付、2：已支付）',
  `order_date` datetime DEFAULT NULL COMMENT '下单时间',
  `pay_date` datetime DEFAULT NULL COMMENT '支付时间',
  `other_user_id` varchar(100) DEFAULT NULL COMMENT 'EOC用户ID',
  `remark` varchar(100) DEFAULT NULL COMMENT '记录购买失败信息',
  `refund_status` tinyint(1) DEFAULT NULL COMMENT '退款状态：1表示未退款，2表示已退款',
  `payment_type` tinyint(1) DEFAULT NULL COMMENT '收款方式（1.运营方收款，2企业收款，3代理商收款）',
  `batch_no` varchar(50) DEFAULT NULL COMMENT '退款订单号',
  `deduct_price` decimal(6,3) DEFAULT NULL,
  `recharge_sources` varchar(50) DEFAULT NULL COMMENT '数据来源',
  `we_openid` varchar(50) DEFAULT NULL COMMENT '微信用户编号',
  PRIMARY KEY (`pay_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3564 DEFAULT CHARSET=utf8 COMMENT='支付订单信息表';

-- ----------------------------
-- Table structure for t_flow_pay_sources_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_pay_sources_record`;
CREATE TABLE `t_flow_pay_sources_record` (
  `sources_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '来源ID号',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `sources_name` varchar(50) DEFAULT NULL COMMENT '来源名称',
  `sources_url` varchar(100) DEFAULT NULL COMMENT '充值链接',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`sources_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='流量充值来源记录';

-- ----------------------------
-- Table structure for t_flow_person_discount
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_person_discount`;
CREATE TABLE `t_flow_person_discount` (
  `person_discount_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `discount_type` tinyint(1) DEFAULT NULL COMMENT '折扣类型（1.微信端折扣，2.sdk端折扣）',
  `mobile_discount` decimal(7,3) DEFAULT NULL,
  `unicom_discount` decimal(7,3) DEFAULT NULL,
  `telecom_discount` decimal(7,3) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `province_id` tinyint(2) DEFAULT NULL COMMENT '省份id',
  `operator_id` tinyint(1) DEFAULT NULL COMMENT '运营商id',
  `charge_discount` decimal(5,3) DEFAULT NULL COMMENT '用户折扣',
  PRIMARY KEY (`person_discount_id`)
) ENGINE=InnoDB AUTO_INCREMENT=320 DEFAULT CHARSET=utf8 COMMENT='个人用户折扣表';

-- ----------------------------
-- Table structure for t_flow_product
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_product`;
CREATE TABLE `t_flow_product` (
  `product_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `product_name` varchar(5) NOT NULL COMMENT '产品名称',
  `product_code` varchar(10) DEFAULT NULL COMMENT '产品编号',
  `operator_id` tinyint(1) NOT NULL COMMENT '所属运营商',
  `province_id` tinyint(2) NOT NULL COMMENT '所属地区',
  `city_id` bigint(18) DEFAULT NULL COMMENT '市ID',
  `base_price` decimal(11,3) DEFAULT NULL,
  `discount` decimal(5,3) DEFAULT '1.000' COMMENT '折扣（游客使用）',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道名',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '备用通道名',
  `status` tinyint(1) DEFAULT '1' COMMENT '产品状态（0：已禁用，1：正常）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改人时间',
  `size` int(10) DEFAULT NULL COMMENT '流量大小（单位为M，1G=1024M）',
  PRIMARY KEY (`product_id`),
  KEY `ix_t_flow_product_create_date` (`create_date`),
  KEY `ix_t_flow_product_operator_id` (`operator_id`),
  KEY `ix_t_flow_product_product_code` (`product_code`),
  KEY `ix_t_flow_product_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COMMENT='流量产品表';

-- ----------------------------
-- Table structure for t_flow_proxy
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy`;
CREATE TABLE `t_flow_proxy` (
  `proxy_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '代理商ID号',
  `proxy_code` int(5) NOT NULL COMMENT '代理商编号（生成规则：20001开始递增）',
  `proxy_name` varchar(50) NOT NULL COMMENT '代理商名称',
  `tel` varchar(20) NOT NULL COMMENT '公司电话',
  `contact_name` varchar(50) NOT NULL COMMENT '联系人',
  `contact_tel` varchar(20) DEFAULT NULL COMMENT '联系人电话',
  `email` varchar(30) DEFAULT NULL COMMENT '邮箱',
  `top_proxy_id` bigint(18) NOT NULL COMMENT '所属上级代理商',
  `operator` varchar(5) NOT NULL COMMENT '支持运营商\r\n            1,2,3\r\n            ',
  `province` varchar(100) DEFAULT NULL COMMENT '所在省份',
  `city` varchar(100) DEFAULT NULL COMMENT '所在城市',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `sale_id` bigint(18) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '0：已禁用、1：正常、2：删除',
  `icense_img` varchar(255) DEFAULT NULL COMMENT '营业执照图片',
  `icense_img_num` varchar(30) DEFAULT NULL COMMENT '营业执照号码',
  `identity_img` varchar(255) DEFAULT NULL COMMENT '法人身份证图片',
  `identity_img_num` varchar(30) DEFAULT NULL COMMENT '法人身份证号码',
  `proxy_level` tinyint(4) NOT NULL COMMENT '1：一级代理商\r\n            2：二级代理商\r\n            3：三级代理商',
  `proxy_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '代理商类型（0：普通代理商、1：自营代理商）',
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态（0：待审核、1：审核通过、2：审核驳回）',
  `approve_user_id` bigint(18) DEFAULT NULL,
  `approve_date` datetime DEFAULT NULL,
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `refund_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可以退款的状态：1，可以退款；0，禁止退款',
  `message_status` tinyint(1) DEFAULT '0' COMMENT '发短信状态（1.启用，禁用）',
  PRIMARY KEY (`proxy_id`),
  KEY `ix_t_flow_proxy_create_date` (`create_date`),
  KEY `ix_t_flow_proxy_proxy_code` (`proxy_code`),
  KEY `ix_t_flow_proxy_proxy_name` (`proxy_name`),
  KEY `ix_t_flow_proxy_top_proxy_id` (`top_proxy_id`),
  KEY `ix_t_flow_proxy_sale_id` (`sale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8 COMMENT='代理商信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_account
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_account`;
CREATE TABLE `t_flow_proxy_account` (
  `account_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '账号ID号',
  `proxy_id` bigint(18) NOT NULL COMMENT '代理商ID',
  `account_balance` decimal(11,3) DEFAULT NULL,
  `freeze_money` decimal(11,3) DEFAULT NULL,
  `credit_money` decimal(11,3) DEFAULT NULL,
  `credit_freeze_money` decimal(11,3) DEFAULT NULL,
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `cache_credit` decimal(11,3) DEFAULT NULL,
  `new_quota_remind` decimal(11,3) DEFAULT '10000.000',
  `channel_cache_credit` decimal(11,3) DEFAULT NULL COMMENT '通道缓存额度',
  PRIMARY KEY (`account_id`),
  KEY `ix_t_flow_proxy_account_create_date` (`create_date`),
  KEY `ix_t_flow_proxy_account_proxy_id` (`proxy_id`),
  CONSTRAINT `FK_Reference_11` FOREIGN KEY (`proxy_id`) REFERENCES `t_flow_proxy` (`proxy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8 COMMENT='代理商账户表';

-- ----------------------------
-- Table structure for t_flow_proxy_account_his
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_account_his`;
CREATE TABLE `t_flow_proxy_account_his` (
  `id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `account_id` bigint(18) NOT NULL COMMENT '账号ID号',
  `proxy_id` bigint(18) NOT NULL COMMENT '代理商ID',
  `account_balance` decimal(11,3) DEFAULT NULL,
  `freeze_money` decimal(11,3) DEFAULT NULL,
  `credit_money` decimal(11,3) DEFAULT NULL,
  `credit_freeze_money` decimal(11,3) DEFAULT NULL,
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `cache_credit` decimal(11,3) DEFAULT NULL,
  `new_quota_remind` decimal(11,3) DEFAULT NULL,
  `channel_cache_credit` decimal(11,3) DEFAULT NULL COMMENT '通道缓存额度',
  `record_day` bigint(50) DEFAULT NULL COMMENT '记录日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5462 DEFAULT CHARSET=utf8 COMMENT='代理商账户表日记录表';

-- ----------------------------
-- Table structure for t_flow_proxy_contract
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_contract`;
CREATE TABLE `t_flow_proxy_contract` (
  `contract_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '合同ID',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `contract_code` varchar(100) DEFAULT NULL COMMENT '合同编号',
  `contract_money` decimal(11,3) DEFAULT NULL,
  `effect_date` datetime DEFAULT NULL COMMENT '生效时间',
  `expire_date` datetime DEFAULT NULL COMMENT '到期时间',
  `describe` text COMMENT '条款说明',
  `enclosure` varchar(500) DEFAULT NULL COMMENT '附件',
  `remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态\n            1草稿\n            2等待审核\n            3初审通过\n            4初审驳回\n            5复审通过\n            6复审驳回',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `discount` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`contract_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='代理商合同信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_contract_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_contract_process`;
CREATE TABLE `t_flow_proxy_contract_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `contract_id` bigint(18) DEFAULT NULL COMMENT '合同ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\n            1审核通过\n            2审核驳回\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审）',
  PRIMARY KEY (`process_id`),
  KEY `FK_Reference_26` (`contract_id`),
  CONSTRAINT `FK_Reference_26` FOREIGN KEY (`contract_id`) REFERENCES `t_flow_proxy_contract` (`contract_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='代理商合同审核过程记录表';

-- ----------------------------
-- Table structure for t_flow_proxy_frozen_account
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_frozen_account`;
CREATE TABLE `t_flow_proxy_frozen_account` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '申请ID',
  `proxy_id` bigint(18) NOT NULL COMMENT '代理商ID',
  `account_id` bigint(18) NOT NULL COMMENT '账户ID',
  `apply_money` decimal(11,3) DEFAULT NULL,
  `apply_date` datetime DEFAULT NULL COMMENT '申请时间',
  `opr_type` tinyint(1) DEFAULT NULL COMMENT '操作类型(1:冻结、2:解冻)',
  `frozen_date` datetime DEFAULT NULL COMMENT '冻结时间',
  `thaw_date` datetime DEFAULT NULL COMMENT '解冻时间',
  `frozen_remark` varchar(255) DEFAULT NULL COMMENT '冻结说明',
  `thaw_remark` varchar(255) DEFAULT NULL COMMENT '解冻说明',
  `frozen_approve_status` tinyint(1) DEFAULT NULL COMMENT '冻结审核状态（1：等待、2：冻结初审成功、3：冻结初审驳回、4：冻结复审成功、5：冻结复审驳回）',
  `frozen_last_approve_date` datetime DEFAULT NULL COMMENT '冻结最后审核时间',
  `thaw_approve_status` tinyint(1) DEFAULT NULL COMMENT '解冻审核状态（1：等待、2：解冻初审成功、3：解冻初审驳回、4：解冻复审成功、5：解冻复审驳回）',
  `thaw_last_approve_date` datetime DEFAULT NULL COMMENT '解冻最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `thaw_create_user_id` bigint(18) DEFAULT NULL COMMENT '解冻创建人ID',
  `thaw_create_date` datetime DEFAULT NULL COMMENT '解冻创建时间',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='代理商冻结账户申请信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_frozen_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_frozen_process`;
CREATE TABLE `t_flow_proxy_frozen_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `apply_id` bigint(18) NOT NULL COMMENT '申请记录ID',
  `process_type` tinyint(1) DEFAULT NULL COMMENT '审核类型(1:冻结、2:解冻)',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\n            1审核通过\n            2审核驳回\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审、3：打款）',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='代理商冻结账户申请审核记录';

-- ----------------------------
-- Table structure for t_flow_proxy_loan
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_loan`;
CREATE TABLE `t_flow_proxy_loan` (
  `loan_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '借款ID',
  `loan_code` varchar(100) DEFAULT NULL COMMENT '借款编号',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `loan_money` decimal(11,3) DEFAULT NULL,
  `loan_date` datetime DEFAULT NULL COMMENT '借款时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '说明内容',
  `repayment_date` datetime DEFAULT NULL COMMENT '预计还款时间',
  `repayment_money` decimal(11,3) DEFAULT NULL,
  `last_repayment_date` datetime DEFAULT NULL COMMENT '最后还款时间',
  `repayment_number` int(2) DEFAULT NULL COMMENT '还款次数',
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态\n            1草稿\n            2等待审核\n            3初审通过\n            4初审驳回\n            5复审通过\n            6复审驳回',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `is_pay_off` tinyint(1) DEFAULT '0' COMMENT '是否还清（0：未还清，1：已还清）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`loan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='代理商借款信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_loan_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_loan_process`;
CREATE TABLE `t_flow_proxy_loan_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `loan_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\n            1审核通过\n            2审核驳回\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审、3：打款）',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='代理商借款审核记录';

-- ----------------------------
-- Table structure for t_flow_proxy_recharge_apply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_recharge_apply`;
CREATE TABLE `t_flow_proxy_recharge_apply` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID号',
  `apply_code` varchar(50) NOT NULL COMMENT '编号（生成规则：代理商编号+时间戳+5位随机数）',
  `source` tinyint(1) DEFAULT '1' COMMENT '来源（1为汇款，2微信支付，3为支付宝支付）',
  `apply_money` decimal(11,3) DEFAULT NULL,
  `transaction_number` varchar(100) DEFAULT NULL COMMENT '交易号',
  `credential_one` varchar(255) DEFAULT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `credential_two` varchar(255) DEFAULT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `credential_three` varchar(255) DEFAULT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `transaction_date` varchar(50) DEFAULT NULL COMMENT '付款日期',
  `remark` varchar(255) DEFAULT NULL COMMENT '说明内容',
  `top_proxy_id` bigint(18) DEFAULT NULL COMMENT '上级代理商',
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态\r\n            1草稿\r\n            2等待审核\r\n            3初审通过\r\n            4初审驳回\r\n            5复审通过\r\n            6复审驳回',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `apply_type` tinyint(1) DEFAULT '1' COMMENT '申请类型（1：正常、2测试款）',
  PRIMARY KEY (`apply_id`),
  KEY `FK_Reference_13` (`proxy_id`),
  KEY `ix_t_flow_proxy_recharge_apply_apply_code` (`apply_code`),
  KEY `ix_t_flow_proxy_recharge_apply_approve_status` (`approve_status`),
  KEY `ix_t_flow_proxy_recharge_apply_create_date` (`create_date`),
  KEY `ix_t_flow_proxy_recharge_apply_proxy_id` (`proxy_id`),
  KEY `ix_t_flow_proxy_recharge_apply_top_proxy_id` (`top_proxy_id`),
  CONSTRAINT `FK_Reference_13` FOREIGN KEY (`proxy_id`) REFERENCES `t_flow_proxy` (`proxy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=268 DEFAULT CHARSET=utf8 COMMENT='代理商充值申请信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_recharge_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_recharge_process`;
CREATE TABLE `t_flow_proxy_recharge_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `apply_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\r\n            1审核通过\r\n            2审核驳回\r\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审）',
  PRIMARY KEY (`process_id`),
  KEY `ix_t_flow_proxy_recharge_process_apply_id` (`apply_id`),
  KEY `ix_t_flow_proxy_recharge_process_approve_date` (`approve_date`)
) ENGINE=InnoDB AUTO_INCREMENT=478 DEFAULT CHARSET=utf8 COMMENT='充值审核过程记录表（记录每次审核的人员信息）';

-- ----------------------------
-- Table structure for t_flow_proxy_repaymen
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_repaymen`;
CREATE TABLE `t_flow_proxy_repaymen` (
  `repaymen_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '还款ID',
  `loan_id` bigint(18) DEFAULT NULL COMMENT '借款ID',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `source` tinyint(1) DEFAULT '1' COMMENT '来源（1为汇款，2微信支付，3为支付宝支付）',
  `transaction_number` varchar(100) DEFAULT NULL COMMENT '交易号',
  `credential_one` varchar(255) DEFAULT NULL COMMENT '凭据(微信订单号、支付宝订单号、银行票据图片)',
  `repayment_money` decimal(11,3) DEFAULT NULL,
  `repayment_date` datetime DEFAULT NULL COMMENT '还款时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '说明内容',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人',
  `approve_status` tinyint(1) DEFAULT '0' COMMENT '审核状态\n            1草稿\n            2等待审核\n            3收款通过\n            4收款驳回',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`repaymen_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='代理商还款记录表';

-- ----------------------------
-- Table structure for t_flow_proxy_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_set`;
CREATE TABLE `t_flow_proxy_set` (
  `set_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `proxy_id` bigint(18) NOT NULL COMMENT '代理商ID',
  `bank_account` varchar(50) DEFAULT NULL COMMENT '开户行',
  `account_opening` varchar(50) DEFAULT NULL COMMENT '开户省市',
  `card_number` varchar(20) DEFAULT NULL COMMENT '银行卡号',
  `beneficiary_name` varchar(20) DEFAULT NULL COMMENT '收款人姓名',
  `mobile` varchar(20) DEFAULT NULL COMMENT '联系电话',
  PRIMARY KEY (`set_id`),
  KEY `ix_t_flow_proxy_set_proxy_id` (`proxy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='代理商设置表';

-- ----------------------------
-- Table structure for t_flow_proxy_ticket_apply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_ticket_apply`;
CREATE TABLE `t_flow_proxy_ticket_apply` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '申请ID',
  `apply_code` varchar(50) DEFAULT NULL COMMENT '申请编号',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `company_name` varchar(150) DEFAULT NULL COMMENT '开票公司名称',
  `ticket_type` tinyint(1) DEFAULT NULL COMMENT '票据类型（1：增值税普通发票、2：增值税专用发票）',
  `ticket_content` tinyint(1) DEFAULT NULL COMMENT '开票内容（1：电信增值业务）',
  `ticket_code` varchar(100) DEFAULT NULL COMMENT '发票编号',
  `apply_ticket_money` decimal(16,3) DEFAULT NULL,
  `actual_ticket_money` decimal(16,3) DEFAULT NULL,
  `enclosure` varchar(100) DEFAULT NULL COMMENT '附件',
  `remark` varchar(255) DEFAULT NULL COMMENT '注备',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（1：草稿、2：等待审核、3：初审通过、4：初审驳回、5：复审通过、6：复审驳回、7：已开票、8：开票驳回）',
  `contact_name` varchar(50) DEFAULT NULL COMMENT '联系人',
  `contact_tel` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `contact_province_id` bigint(18) DEFAULT NULL COMMENT '省份',
  `contact_city_id` bigint(18) DEFAULT NULL COMMENT '城市',
  `contact_address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `apply_user_id` bigint(18) DEFAULT NULL COMMENT '申请人ID',
  `apply_date` datetime DEFAULT NULL COMMENT '申请时间',
  `open_ticket_date` datetime DEFAULT NULL COMMENT '开票时间',
  `open_ticket_uaer_id` bigint(18) DEFAULT NULL COMMENT '开票人ID',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='代理商开票信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_ticket_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_ticket_process`;
CREATE TABLE `t_flow_proxy_ticket_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `apply_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT NULL COMMENT '审核阶段',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='代理商开票审核信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_transfer_apply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_transfer_apply`;
CREATE TABLE `t_flow_proxy_transfer_apply` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '申请单ID',
  `apply_code` varchar(50) DEFAULT NULL COMMENT '申请编号',
  `apply_money` decimal(11,3) DEFAULT NULL,
  `pay_proxy_id` bigint(18) DEFAULT NULL COMMENT '支出代理商ID',
  `receive_proxy_id` bigint(18) DEFAULT NULL COMMENT '接收人理商ID',
  `apply_date` datetime DEFAULT NULL COMMENT '申请时间',
  `apply_user_id` bigint(18) DEFAULT NULL COMMENT '申请人',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（1草稿、2等待审核、3初审通过、4初审驳回、5复审通过、6复审驳回）',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='代理商资金划拨信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_transfer_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_transfer_process`;
CREATE TABLE `t_flow_proxy_transfer_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `apply_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT NULL COMMENT '审核阶段',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='代理商资金划拨审核信息表';

-- ----------------------------
-- Table structure for t_flow_proxy_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_user`;
CREATE TABLE `t_flow_proxy_user` (
  `user_id` bigint(18) NOT NULL COMMENT '用户ID',
  `proxy_id` bigint(18) NOT NULL COMMENT '代理商ID',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  KEY `ix_t_flow_proxy_user_proxy_id` (`proxy_id`),
  KEY `ix_t_flow_proxy_user_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代理商权限表';

-- ----------------------------
-- Table structure for t_flow_proxy_withdraw_apply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_withdraw_apply`;
CREATE TABLE `t_flow_proxy_withdraw_apply` (
  `apply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID号',
  `apply_code` varchar(50) NOT NULL COMMENT '编号（生成规则：代理商编号+时间戳+5位随机数）',
  `apply_money` decimal(11,3) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL COMMENT '开户行',
  `account_opening` varchar(50) DEFAULT NULL COMMENT '开户省市',
  `card_number` varchar(50) DEFAULT NULL COMMENT '银行卡号',
  `beneficiary_name` varchar(20) DEFAULT NULL COMMENT '收款人姓名',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `transaction_date` datetime DEFAULT NULL COMMENT '交易时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '说明内容',
  `top_proxy_id` bigint(18) DEFAULT NULL COMMENT '上级代理商',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态 1草稿 2等待审核 3初审通过 4初审驳回 5复审通过 6复审驳回 7打款成功 8打款驳回',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `transaction_number` varchar(100) DEFAULT NULL COMMENT '交易号',
  `payment_account` varchar(20) DEFAULT NULL,
  `payment_money` decimal(11,3) DEFAULT NULL,
  `payment_bank` varchar(50) DEFAULT NULL COMMENT '打款银行',
  `payment_date` datetime DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `is_play_money` tinyint(1) DEFAULT '0' COMMENT '是否已打款',
  PRIMARY KEY (`apply_id`),
  KEY `FK_Reference_19` (`proxy_id`),
  KEY `ix_t_flow_proxy_withdraw_apply_apply_code` (`apply_code`),
  KEY `ix_t_flow_proxy_withdraw_apply_approve_status` (`approve_status`),
  KEY `ix_t_flow_proxy_withdraw_apply_create_date` (`create_date`),
  KEY `ix_t_flow_proxy_withdraw_apply_proxy_id` (`proxy_id`),
  KEY `ix_t_flow_proxy_withdraw_apply_top_proxy_id` (`top_proxy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COMMENT='代理商提现申请表';

-- ----------------------------
-- Table structure for t_flow_proxy_withdraw_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_proxy_withdraw_process`;
CREATE TABLE `t_flow_proxy_withdraw_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `apply_id` bigint(18) DEFAULT NULL COMMENT '申请记录ID',
  `approve_status` tinyint(1) DEFAULT '1' COMMENT '审核状态\r\n            1审核通过\r\n            2审核驳回\r\n            ',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审、3：打款）',
  PRIMARY KEY (`process_id`),
  UNIQUE KEY `process_id` (`process_id`),
  KEY `FK_Reference_18` (`apply_id`),
  KEY `ix_t_flow_proxy_withdraw_process_` (`apply_id`),
  KEY `ix_t_flow_proxy_withdraw_process_approve_date` (`approve_date`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8 COMMENT='提现审核过程记录表';

-- ----------------------------
-- Table structure for t_flow_red_order
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_red_order`;
CREATE TABLE `t_flow_red_order` (
  `red_order_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '红包订单ID',
  `red_order_code` varchar(50) DEFAULT NULL COMMENT '订单编号',
  `number` varchar(50) DEFAULT NULL COMMENT '交易号',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `wx_openid` varchar(50) DEFAULT NULL COMMENT '购买者微信openid',
  `packages` varchar(100) DEFAULT NULL COMMENT '流量包',
  `out_packages` varchar(100) DEFAULT NULL COMMENT '已领流量包',
  `share_link` varchar(250) DEFAULT NULL COMMENT '分享链接',
  `pay_price` decimal(6,3) DEFAULT NULL,
  `discount_price` decimal(6,3) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `other_userid` varchar(100) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `pay_status` tinyint(1) DEFAULT NULL COMMENT '支付状态（1：未支付、2：已支付）',
  `order_date` datetime DEFAULT NULL COMMENT '下单时间',
  `pay_date` datetime DEFAULT NULL COMMENT '支付时间',
  `discount` varchar(50) DEFAULT NULL COMMENT '下单时折扣  格式：移动折扣，联通折扣，电信折扣',
  `payment_type` tinyint(1) DEFAULT NULL COMMENT '收款方式（1.运营方收款，2企业收款，3代理商收款）',
  `pay_type` tinyint(1) DEFAULT NULL COMMENT '支付类型（1.支付宝，2.微信端，3.微信app）',
  `batch_no` varchar(50) DEFAULT NULL COMMENT '退款订单号',
  PRIMARY KEY (`red_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=782 DEFAULT CHARSET=utf8 COMMENT='流量红包订单记录表';

-- ----------------------------
-- Table structure for t_flow_red_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_red_record`;
CREATE TABLE `t_flow_red_record` (
  `red_record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '领取记录ID',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `red_order_id` bigint(18) DEFAULT NULL COMMENT '红包订单ID',
  `order_id` varchar(50) DEFAULT NULL COMMENT '订单ID',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `product_id` bigint(18) DEFAULT NULL COMMENT '流量包ID',
  `product_name` varchar(50) DEFAULT NULL COMMENT '流量包名称',
  `wx_openid` varchar(50) DEFAULT NULL COMMENT '微信openid',
  `wx_photo` varchar(100) DEFAULT NULL COMMENT '微信用户头像',
  `wx_name` varchar(50) DEFAULT NULL COMMENT '微信昵称',
  `receive_date` datetime DEFAULT NULL COMMENT '领取时间',
  `refund_status` tinyint(1) DEFAULT NULL COMMENT '退款状态：1.未退款，2.已退款',
  PRIMARY KEY (`red_record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8 COMMENT='流量红包领取记录表';

-- ----------------------------
-- Table structure for t_flow_rpt_channel
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_rpt_channel`;
CREATE TABLE `t_flow_rpt_channel` (
  `rpt_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(18) DEFAULT NULL,
  `rpt_date` date DEFAULT NULL,
  `expense_sum` decimal(16,3) DEFAULT NULL,
  `cost_sum` decimal(16,3) DEFAULT NULL,
  `profit_sum` decimal(16,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `rebate_sum` decimal(16,3) DEFAULT NULL,
  PRIMARY KEY (`rpt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=252486 DEFAULT CHARSET=utf8 COMMENT='通道收入统计表';

-- ----------------------------
-- Table structure for t_flow_rpt_channel_20161122
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_rpt_channel_20161122`;
CREATE TABLE `t_flow_rpt_channel_20161122` (
  `rpt_id` bigint(18) NOT NULL DEFAULT '0',
  `channel_id` bigint(18) DEFAULT NULL,
  `rpt_date` date DEFAULT NULL,
  `expense_sum` decimal(16,3) DEFAULT NULL,
  `cost_sum` decimal(16,3) DEFAULT NULL,
  `profit_sum` decimal(16,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `rebate_sum` decimal(16,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_rpt_direct_enterprise
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_rpt_direct_enterprise`;
CREATE TABLE `t_flow_rpt_direct_enterprise` (
  `rpt_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `proxy_id` bigint(18) DEFAULT NULL,
  `rpt_date` date DEFAULT NULL,
  `expense_sum` decimal(16,3) DEFAULT NULL,
  `cost_sum` decimal(16,3) DEFAULT NULL,
  `profit_sum` decimal(16,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `rebate_sum` decimal(16,3) DEFAULT NULL,
  KEY `rpt_id` (`rpt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=361985 DEFAULT CHARSET=utf8 COMMENT='直销企业收入统计表';

-- ----------------------------
-- Table structure for t_flow_rpt_direct_enterprise_20161122
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_rpt_direct_enterprise_20161122`;
CREATE TABLE `t_flow_rpt_direct_enterprise_20161122` (
  `rpt_id` bigint(18) NOT NULL DEFAULT '0',
  `enterprise_id` bigint(18) DEFAULT NULL,
  `proxy_id` bigint(18) DEFAULT NULL,
  `rpt_date` date DEFAULT NULL,
  `expense_sum` decimal(16,3) DEFAULT NULL,
  `cost_sum` decimal(16,3) DEFAULT NULL,
  `profit_sum` decimal(16,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `rebate_sum` decimal(16,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_rpt_proxy
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_rpt_proxy`;
CREATE TABLE `t_flow_rpt_proxy` (
  `rpt_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `proxy_id` bigint(18) DEFAULT NULL,
  `rpt_date` date DEFAULT NULL,
  `expense_sum` decimal(16,3) DEFAULT NULL,
  `cost_sum` decimal(16,3) DEFAULT NULL,
  `profit_sum` decimal(16,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `rebate_sum` decimal(16,3) DEFAULT NULL,
  PRIMARY KEY (`rpt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19315 DEFAULT CHARSET=utf8 COMMENT='代理商收入统计表';

-- ----------------------------
-- Table structure for t_flow_scene_activity
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_scene_activity`;
CREATE TABLE `t_flow_scene_activity` (
  `activity_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '活动ID',
  `activity_name` varchar(100) DEFAULT NULL COMMENT '活动名称',
  `activity_file_name` varchar(100) DEFAULT NULL COMMENT '活动文件名',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `activity_rule` text COMMENT '活动规则',
  `status` tinyint(4) DEFAULT NULL COMMENT '启用禁用状态，0：已禁用、1：正常、2：已删除',
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='活动信息表';

-- ----------------------------
-- Table structure for t_flow_scene_buy_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_scene_buy_record`;
CREATE TABLE `t_flow_scene_buy_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `product_name` varchar(50) DEFAULT NULL COMMENT '流量包名称',
  `product_id` bigint(18) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `activity_id` bigint(18) DEFAULT NULL COMMENT '参与活动ID',
  `order_id` bigint(18) DEFAULT NULL COMMENT '订单ID',
  `order_price` decimal(16,3) DEFAULT NULL,
  `order_status` int(11) DEFAULT NULL COMMENT '0-购买失败\n            1-购买成功',
  `remark` varchar(255) DEFAULT NULL,
  `wx_open_id` varchar(50) DEFAULT NULL COMMENT '微信openid',
  `wx_photo_url` varchar(200) DEFAULT NULL COMMENT '微信用户头像',
  `wx_name` varchar(50) DEFAULT NULL COMMENT '微信昵称',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `order_time` datetime DEFAULT NULL COMMENT '流量包名称',
  `complete_time` datetime DEFAULT NULL COMMENT '领取时间',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动购买记录';

-- ----------------------------
-- Table structure for t_flow_scene_configuration
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_scene_configuration`;
CREATE TABLE `t_flow_scene_configuration` (
  `configuration_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(18) DEFAULT NULL,
  `probability` int(11) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `user_activity_id` bigint(18) DEFAULT NULL,
  `user_type` int(11) DEFAULT NULL COMMENT '1代理商，2企业',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`configuration_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4246 DEFAULT CHARSET=utf8 COMMENT='活动场景产品配置表';

-- ----------------------------
-- Table structure for t_flow_scene_info
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_scene_info`;
CREATE TABLE `t_flow_scene_info` (
  `info_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '信息ID',
  `user_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) NOT NULL DEFAULT '0' COMMENT '代理商ID',
  `enterprise_id` bigint(18) NOT NULL DEFAULT '0' COMMENT '企业ID',
  `propagandat_img` varchar(100) DEFAULT NULL COMMENT '宣传图',
  `logo_img` varchar(100) DEFAULT NULL COMMENT 'logo图片',
  `background_img` varchar(100) DEFAULT NULL COMMENT '背景图',
  `redpack_address` varchar(255) NOT NULL DEFAULT '' COMMENT '流量红包地址',
  `recharge_address` varchar(255) NOT NULL DEFAULT '' COMMENT '流量充值地址',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `share_title` varchar(100) DEFAULT NULL COMMENT '分享主题',
  `share_content` varchar(255) DEFAULT NULL COMMENT '分享内容',
  `share_img` varchar(100) DEFAULT NULL COMMENT '分享图',
  `share_url` varchar(255) DEFAULT NULL COMMENT '分享链接',
  `active_appid` varchar(100) DEFAULT NULL,
  `active_appsecret` varchar(100) DEFAULT NULL,
  `active_wx_name` varchar(100) DEFAULT NULL COMMENT '领取活动公众微信号',
  `active_wx_type` tinyint(1) DEFAULT '1' COMMENT '公众号类型：1服务号，2订阅号',
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8 COMMENT='场景基本信息表';

-- ----------------------------
-- Table structure for t_flow_scene_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_scene_record`;
CREATE TABLE `t_flow_scene_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `order_id` varchar(50) DEFAULT NULL COMMENT '订单ID',
  `user_activity_id` bigint(18) DEFAULT NULL COMMENT '参与活动ID',
  `openid` varchar(50) DEFAULT NULL COMMENT '微信openid',
  `wx_photo` varchar(150) DEFAULT NULL COMMENT '微信用户头像',
  `wx_name` varchar(200) DEFAULT NULL COMMENT '微信昵称',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `product_name` varchar(50) DEFAULT NULL COMMENT '流量包名称',
  `receive_date` varchar(100) DEFAULT NULL COMMENT '领取时间',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40693 DEFAULT CHARSET=utf8 COMMENT='活动领取记录';

-- ----------------------------
-- Table structure for t_flow_scene_user_activity
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_scene_user_activity`;
CREATE TABLE `t_flow_scene_user_activity` (
  `user_activity_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT 'ID号',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `activity_id` bigint(18) DEFAULT NULL COMMENT '活动ID',
  `activity_address` varchar(255) DEFAULT NULL COMMENT '活动地址',
  `activity_rule` text COMMENT '活动规则',
  `start_date` datetime DEFAULT NULL COMMENT '开始时间',
  `end_date` datetime DEFAULT NULL COMMENT '结束时间',
  `frequency` tinyint(1) DEFAULT NULL COMMENT '参与频率（1：天、2：周、3：月、4：整个活动）',
  `number` int(2) DEFAULT NULL COMMENT '参与次数',
  `activity_status` tinyint(1) DEFAULT NULL COMMENT '活动状态（1：启用、2：禁用）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `point` varchar(100) DEFAULT NULL COMMENT '位置坐标（格式：x,y）',
  `accuracy` int(10) DEFAULT NULL COMMENT '范围（千米）',
  `lbs_status` tinyint(1) DEFAULT NULL COMMENT 'lbs状态（1.开启，2关闭）',
  `propagandat_img` varchar(100) DEFAULT NULL COMMENT '宣传图',
  `logo_img` varchar(100) DEFAULT NULL COMMENT 'logo图',
  `background_img` varchar(100) DEFAULT NULL COMMENT '背景图',
  `share_title` varchar(100) DEFAULT NULL,
  `share_content` varchar(255) DEFAULT NULL COMMENT '分享内容',
  `share_img` varchar(100) DEFAULT NULL COMMENT '分享图片',
  `share_url` varchar(255) DEFAULT NULL COMMENT '分享链接',
  `key_word` varchar(100) DEFAULT NULL COMMENT '关键字搜索',
  `activity_money` decimal(11,3) DEFAULT NULL,
  `used_money` decimal(11,3) DEFAULT NULL,
  `user_activity_type` tinyint(1) DEFAULT NULL COMMENT '活动类型（1、领取活动，流量券活动）',
  `ticket_effective_duration` varchar(10) DEFAULT NULL COMMENT '流量券时长',
  `user_activity_guide_link` varchar(255) DEFAULT NULL COMMENT '引导链接（备注：流量券兑换引导作用）',
  `user_activity_name` varchar(100) DEFAULT NULL COMMENT '活动名称',
  PRIMARY KEY (`user_activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8 COMMENT='用户活动关系表';

-- ----------------------------
-- Table structure for t_flow_score_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_score_record`;
CREATE TABLE `t_flow_score_record` (
  `record_score_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '积分记录id',
  `wx_user_id` bigint(18) DEFAULT NULL COMMENT '微信用户id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `score_modify_time` datetime DEFAULT NULL COMMENT '积分记录时间',
  `score_change` int(10) DEFAULT NULL COMMENT '积分数',
  `score_remark` varchar(200) DEFAULT NULL COMMENT '备注',
  `scoreFromType` tinyint(1) DEFAULT NULL COMMENT '积分来源类型',
  PRIMARY KEY (`record_score_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1481 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_sence_counts
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sence_counts`;
CREATE TABLE `t_flow_sence_counts` (
  `count_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL COMMENT '类型（1.进入链接，2.输入号码，3.选择包型，4.点击支付，5,完成支付）',
  `phone` varchar(11) DEFAULT NULL COMMENT '手机号码',
  `product_name` varchar(5) DEFAULT NULL,
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `enterprise_id` bigint(18) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`count_id`)
) ENGINE=InnoDB AUTO_INCREMENT=955 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_sms
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sms`;
CREATE TABLE `t_flow_sms` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL COMMENT '单价',
  `product_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '产品名称',
  `status` tinyint(1) NOT NULL COMMENT '发送状态：0 待发送 1正在发送 2 发送成功 3 发送失败',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '获取次数',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `complete_time` datetime DEFAULT NULL COMMENT '完成时间',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '备通道ID',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `content` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '内容',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信记录表';

-- ----------------------------
-- Table structure for t_flow_sms_pre
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sms_pre`;
CREATE TABLE `t_flow_sms_pre` (
  `order_id` bigint(18) NOT NULL COMMENT '设置ID',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '手机号',
  `price` decimal(11,3) DEFAULT NULL COMMENT '单价',
  `product_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '产品名称',
  `status` tinyint(1) NOT NULL COMMENT '发送状态：0 待发送 1正在发送 2 发送成功 3 发送失败',
  `is_using` tinyint(1) DEFAULT '0' COMMENT '获取次数',
  `order_date` datetime(6) NOT NULL COMMENT '下单时间',
  `complete_time` datetime DEFAULT NULL COMMENT '完成时间',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2企业)',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '主通道ID',
  `back_channel_id` bigint(18) DEFAULT NULL COMMENT '备通道ID',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态\r\n            0：等待提交\r\n            1：提交成功 \r\n            2：充值成功 \r\n            3：充值/提交 失败，再次等待备用通道提交 \r\n            4：备用通道提交成功\r\n            5：备用通道充值成功  \r\n            6：备用通道 充值/提交 失败',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `content` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '内容',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信预处理表';

-- ----------------------------
-- Table structure for t_flow_sms_send
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sms_send`;
CREATE TABLE `t_flow_sms_send` (
  `send_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `sys_type` tinyint(1) NOT NULL COMMENT '系统类型（1尚通，2代理商，3企业）',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `msg_type` int(10) NOT NULL COMMENT '短信类型（1通道额度提醒，2通道余额提醒，3用户短信提醒）',
  `timing` tinyint(1) DEFAULT '0' COMMENT '0即时发送，1定时发送',
  `order_time` datetime DEFAULT NULL,
  `msg_content` varchar(400) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `send_time` datetime DEFAULT NULL,
  `send_state` int(10) NOT NULL COMMENT '0待发送 1已发送未返回 2已返回 3发送成功 4发送失败 5取消发送',
  `return_time` datetime DEFAULT NULL COMMENT '返回时间',
  `return_type` int(10) DEFAULT NULL COMMENT '返回类型',
  `send_times` int(10) DEFAULT '0' COMMENT '发送次数',
  `fail_times` int(10) DEFAULT '0' COMMENT '失败次数',
  `delete_status` tinyint(1) DEFAULT NULL COMMENT '删除状态（0正常，1删除）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  PRIMARY KEY (`send_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30702 DEFAULT CHARSET=utf8 COMMENT='系统短信发送记录表';

-- ----------------------------
-- Table structure for t_flow_stat_channel
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_stat_channel`;
CREATE TABLE `t_flow_stat_channel` (
  `stat_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '统计ID',
  `stat_type` int(2) DEFAULT '0' COMMENT '统计类型：0:null, 1 :order',
  `stat_status` bigint(18) DEFAULT '0' COMMENT '状态',
  `operator_id` bigint(18) DEFAULT '0' COMMENT '运营商ID',
  `channel_id` bigint(18) DEFAULT '0' COMMENT '通道ID',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '通道CODE',
  `channel_name` varchar(50) DEFAULT NULL COMMENT '通道名称',
  `province_id` bigint(18) DEFAULT '0' COMMENT '省份ID',
  `province_name` varchar(100) DEFAULT NULL COMMENT '省份',
  `stat_year` int(50) DEFAULT NULL COMMENT '年：2015',
  `stat_month` int(50) DEFAULT NULL COMMENT '年月：201507',
  `stat_day` bigint(50) DEFAULT NULL COMMENT '年月日：20150720',
  `stat_size` bigint(18) DEFAULT NULL COMMENT '流量包',
  `stat_price` decimal(11,3) DEFAULT NULL,
  `stat_count` bigint(20) DEFAULT NULL COMMENT '记录数',
  `stat_refund_price` decimal(11,3) DEFAULT NULL,
  `profit_price` decimal(11,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=188746 DEFAULT CHARSET=utf8 COMMENT='通道统计表';

-- ----------------------------
-- Table structure for t_flow_stat_channel_20161122
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_stat_channel_20161122`;
CREATE TABLE `t_flow_stat_channel_20161122` (
  `stat_id` bigint(18) NOT NULL DEFAULT '0' COMMENT '统计ID',
  `stat_type` int(2) DEFAULT '0' COMMENT '统计类型：0:null, 1 :order',
  `stat_status` bigint(18) DEFAULT '0' COMMENT '状态',
  `operator_id` bigint(18) DEFAULT '0' COMMENT '运营商ID',
  `channel_id` bigint(18) DEFAULT '0' COMMENT '通道ID',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '通道CODE',
  `channel_name` varchar(50) DEFAULT NULL COMMENT '通道名称',
  `province_id` bigint(18) DEFAULT '0' COMMENT '省份ID',
  `province_name` varchar(100) DEFAULT NULL COMMENT '省份',
  `stat_year` int(50) DEFAULT NULL COMMENT '年：2015',
  `stat_month` int(50) DEFAULT NULL COMMENT '年月：201507',
  `stat_day` bigint(50) DEFAULT NULL COMMENT '年月日：20150720',
  `stat_size` bigint(18) DEFAULT NULL COMMENT '流量包',
  `stat_price` decimal(11,3) DEFAULT NULL,
  `stat_count` bigint(20) DEFAULT NULL COMMENT '记录数',
  `stat_refund_price` decimal(11,3) DEFAULT NULL,
  `profit_price` decimal(11,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_stat_enterprise
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_stat_enterprise`;
CREATE TABLE `t_flow_stat_enterprise` (
  `stat_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '统计ID',
  `stat_type` int(2) DEFAULT '0' COMMENT '统计类型：0:null, 1 :order',
  `stat_status` bigint(18) DEFAULT '0' COMMENT '状态',
  `operator_id` bigint(18) DEFAULT '0' COMMENT '运营商ID',
  `enterprise_id` bigint(18) DEFAULT '0' COMMENT '企业ID',
  `enterprise_code` int(11) DEFAULT NULL COMMENT '企业CODE',
  `enterprise_name` varchar(50) DEFAULT NULL COMMENT '企业名称',
  `province_id` bigint(18) DEFAULT '0' COMMENT '省份ID',
  `province_name` varchar(100) DEFAULT NULL COMMENT '省份',
  `stat_year` int(50) DEFAULT NULL COMMENT '年：2015',
  `stat_month` int(50) DEFAULT NULL COMMENT '年月：201507',
  `stat_day` bigint(50) DEFAULT NULL COMMENT '年月日：20150720',
  `stat_size` bigint(18) DEFAULT NULL COMMENT '流量包',
  `stat_price` decimal(11,3) DEFAULT NULL,
  `stat_count` bigint(20) DEFAULT NULL COMMENT '记录数',
  `stat_refund_price` decimal(11,3) DEFAULT NULL,
  `channel_id` bigint(18) DEFAULT '0' COMMENT '通道ID',
  `channel_name` varchar(30) DEFAULT NULL COMMENT '通道名称',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '通道编号',
  `sale_discount` decimal(4,3) DEFAULT '1.000' COMMENT '销售折扣',
  `top_discount` decimal(4,3) DEFAULT '1.000' COMMENT '成本折扣',
  `discount_price` decimal(11,3) DEFAULT NULL,
  `top_price` decimal(11,3) DEFAULT NULL,
  `profit_price` decimal(11,3) DEFAULT NULL,
  `rebate_price` decimal(11,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`stat_id`),
  KEY `ix_t_flow_stat_enterprise_stat_type_stat_day` (`stat_type`,`stat_day`)
) ENGINE=InnoDB AUTO_INCREMENT=1817121 DEFAULT CHARSET=utf8 COMMENT='代理首页统计表';

-- ----------------------------
-- Table structure for t_flow_stat_enterprise_20161122
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_stat_enterprise_20161122`;
CREATE TABLE `t_flow_stat_enterprise_20161122` (
  `stat_id` bigint(18) NOT NULL DEFAULT '0' COMMENT '统计ID',
  `stat_type` int(2) DEFAULT '0' COMMENT '统计类型：0:null, 1 :order',
  `stat_status` bigint(18) DEFAULT '0' COMMENT '状态',
  `operator_id` bigint(18) DEFAULT '0' COMMENT '运营商ID',
  `enterprise_id` bigint(18) DEFAULT '0' COMMENT '企业ID',
  `enterprise_code` int(11) DEFAULT NULL COMMENT '企业CODE',
  `enterprise_name` varchar(50) DEFAULT NULL COMMENT '企业名称',
  `province_id` bigint(18) DEFAULT '0' COMMENT '省份ID',
  `province_name` varchar(100) DEFAULT NULL COMMENT '省份',
  `stat_year` int(50) DEFAULT NULL COMMENT '年：2015',
  `stat_month` int(50) DEFAULT NULL COMMENT '年月：201507',
  `stat_day` bigint(50) DEFAULT NULL COMMENT '年月日：20150720',
  `stat_size` bigint(18) DEFAULT NULL COMMENT '流量包',
  `stat_price` decimal(11,3) DEFAULT NULL,
  `stat_count` bigint(20) DEFAULT NULL COMMENT '记录数',
  `stat_refund_price` decimal(11,3) DEFAULT NULL,
  `channel_id` bigint(18) DEFAULT '0' COMMENT '通道ID',
  `channel_name` varchar(30) DEFAULT NULL COMMENT '通道名称',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '通道编号',
  `sale_discount` decimal(4,3) DEFAULT '1.000' COMMENT '销售折扣',
  `top_discount` decimal(4,3) DEFAULT '1.000' COMMENT '成本折扣',
  `discount_price` decimal(11,3) DEFAULT NULL,
  `top_price` decimal(11,3) DEFAULT NULL,
  `profit_price` decimal(11,3) DEFAULT NULL,
  `rebate_price` decimal(11,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_stat_product
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_stat_product`;
CREATE TABLE `t_flow_stat_product` (
  `stat_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '统计ID',
  `stat_type` int(2) DEFAULT '0' COMMENT '统计类型：0:null, 1 :proxy 2:enterprise',
  `stat_status` bigint(18) DEFAULT '0' COMMENT '状态',
  `product_id` bigint(18) DEFAULT NULL COMMENT '产品规格ID',
  `channel_id` bigint(18) DEFAULT '0' COMMENT '通道ID',
  `channel_name` varchar(30) DEFAULT NULL COMMENT '通道名称',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '通道编号',
  `user_id` bigint(18) DEFAULT '0' COMMENT '用户ID',
  `user_name` varchar(50) DEFAULT NULL COMMENT '用户名称',
  `one_proxy_id` bigint(18) DEFAULT '0' COMMENT '代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '代理名称',
  `stat_year` int(50) DEFAULT NULL COMMENT '年：2015',
  `stat_month` int(50) DEFAULT NULL COMMENT '年月：201507',
  `stat_day` bigint(50) DEFAULT NULL COMMENT '年月日：20150720',
  `stat_price` decimal(11,3) DEFAULT NULL,
  `stat_count` bigint(20) DEFAULT NULL COMMENT '记录数',
  `sale_discount` decimal(4,3) DEFAULT '1.000' COMMENT '销售折扣',
  `sale_price`  decimal(11,3) DEFAULT NULL COMMENT '一级代理商金额',
  `top_discount` decimal(4,3) DEFAULT '1.000' COMMENT '成本折扣',
  `discount_price` decimal(11,3) DEFAULT NULL,
  `operator_id` bigint(18) DEFAULT '0' COMMENT '运营商ID',
  `top_price` decimal(11,3) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `rebate_price` decimal(11,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1166077 DEFAULT CHARSET=utf8 COMMENT='产品统计表';

-- ----------------------------
-- Table structure for t_flow_stat_product_20161122
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_stat_product_20161122`;
CREATE TABLE `t_flow_stat_product_20161122` (
  `stat_id` bigint(18) NOT NULL DEFAULT '0' COMMENT '统计ID',
  `stat_type` int(2) DEFAULT '0' COMMENT '统计类型：0:null, 1 :proxy 2:enterprise',
  `stat_status` bigint(18) DEFAULT '0' COMMENT '状态',
  `product_id` bigint(18) DEFAULT NULL COMMENT '产品规格ID',
  `channel_id` bigint(18) DEFAULT '0' COMMENT '通道ID',
  `channel_name` varchar(30) DEFAULT NULL COMMENT '通道名称',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '通道编号',
  `user_id` bigint(18) DEFAULT '0' COMMENT '用户ID',
  `user_name` varchar(50) DEFAULT NULL COMMENT '用户名称',
  `one_proxy_id` bigint(18) DEFAULT '0' COMMENT '代理ID',
  `one_proxy_name` varchar(50) DEFAULT NULL COMMENT '代理名称',
  `stat_year` int(50) DEFAULT NULL COMMENT '年：2015',
  `stat_month` int(50) DEFAULT NULL COMMENT '年月：201507',
  `stat_day` bigint(50) DEFAULT NULL COMMENT '年月日：20150720',
  `stat_price` decimal(11,3) DEFAULT NULL,
  `stat_count` bigint(20) DEFAULT NULL COMMENT '记录数',
  `sale_discount` decimal(4,3) DEFAULT '1.000' COMMENT '销售折扣',
  `top_discount` decimal(4,3) DEFAULT '1.000' COMMENT '成本折扣',
  `discount_price` decimal(11,3) DEFAULT NULL,
  `operator_id` bigint(18) DEFAULT '0' COMMENT '运营商ID',
  `top_price` decimal(11,3) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `rebate_price` decimal(11,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_stat_proxy
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_stat_proxy`;
CREATE TABLE `t_flow_stat_proxy` (
  `stat_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '统计ID',
  `stat_type` int(2) DEFAULT '0' COMMENT '统计类型：0:null, 1 :order',
  `stat_status` bigint(18) DEFAULT '0' COMMENT '状态',
  `operator_id` bigint(18) DEFAULT '0' COMMENT '运营商ID',
  `proxy_id` bigint(18) DEFAULT '0' COMMENT '代理ID',
  `proxy_code` int(11) DEFAULT NULL COMMENT '代理CODE',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理名称',
  `province_id` bigint(18) DEFAULT '0' COMMENT '省份ID',
  `province_name` varchar(100) DEFAULT NULL COMMENT '省份',
  `stat_year` int(50) DEFAULT NULL COMMENT '年：2015',
  `stat_month` int(50) DEFAULT NULL COMMENT '年月：201507',
  `stat_day` bigint(50) DEFAULT NULL COMMENT '年月日：20150720',
  `stat_size` bigint(18) DEFAULT NULL COMMENT '流量包',
  `stat_price` decimal(11,3) DEFAULT NULL,
  `stat_count` bigint(20) DEFAULT NULL COMMENT '记录数',
  `stat_refund_price` decimal(11,3) DEFAULT NULL,
  `channel_id` bigint(18) DEFAULT '0' COMMENT '通道ID',
  `channel_name` varchar(30) DEFAULT NULL COMMENT '通道名称',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '通道编号',
  `sale_discount` decimal(4,3) DEFAULT '1.000' COMMENT '销售折扣',
  `top_discount` decimal(4,3) DEFAULT '1.000' COMMENT '成本折扣',
  `discount_price` decimal(11,3) DEFAULT NULL,
  `profit_price` decimal(11,3) DEFAULT NULL,
  `top_price` decimal(11,3) DEFAULT NULL,
  `rebate_price` decimal(11,3) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`stat_id`),
  KEY `ix_t_flow_stat_proxy_stat_type_stat_day` (`stat_type`,`stat_day`)
) ENGINE=InnoDB AUTO_INCREMENT=297724 DEFAULT CHARSET=utf8 COMMENT='代理首页统计表';

-- ----------------------------
-- Table structure for t_flow_stat_proxy_20161122
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_stat_proxy_20161122`;
CREATE TABLE `t_flow_stat_proxy_20161122` (
  `stat_id` bigint(18) NOT NULL DEFAULT '0' COMMENT '统计ID',
  `stat_type` int(2) DEFAULT '0' COMMENT '统计类型：0:null, 1 :order',
  `stat_status` bigint(18) DEFAULT '0' COMMENT '状态',
  `operator_id` bigint(18) DEFAULT '0' COMMENT '运营商ID',
  `proxy_id` bigint(18) DEFAULT '0' COMMENT '代理ID',
  `proxy_code` int(11) DEFAULT NULL COMMENT '代理CODE',
  `proxy_name` varchar(50) DEFAULT NULL COMMENT '代理名称',
  `province_id` bigint(18) DEFAULT '0' COMMENT '省份ID',
  `province_name` varchar(100) DEFAULT NULL COMMENT '省份',
  `stat_year` int(50) DEFAULT NULL COMMENT '年：2015',
  `stat_month` int(50) DEFAULT NULL COMMENT '年月：201507',
  `stat_day` bigint(50) DEFAULT NULL COMMENT '年月日：20150720',
  `stat_size` bigint(18) DEFAULT NULL COMMENT '流量包',
  `stat_price` decimal(11,3) DEFAULT NULL,
  `stat_count` bigint(20) DEFAULT NULL COMMENT '记录数',
  `stat_refund_price` decimal(11,3) DEFAULT NULL,
  `channel_id` bigint(18) DEFAULT '0' COMMENT '通道ID',
  `channel_name` varchar(30) DEFAULT NULL COMMENT '通道名称',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '通道编号',
  `sale_discount` decimal(4,3) DEFAULT '1.000' COMMENT '销售折扣',
  `top_discount` decimal(4,3) DEFAULT '1.000' COMMENT '成本折扣',
  `discount_price` decimal(11,3) DEFAULT NULL,
  `profit_price` decimal(11,3) DEFAULT NULL,
  `top_price` decimal(11,3) DEFAULT NULL,
  `rebate_price` decimal(11,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_sys_api
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_api`;
CREATE TABLE `t_flow_sys_api` (
  `api_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '接口ID',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型(1：代理商、2：企业)',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `api_account` varchar(20) NOT NULL COMMENT '账号名称',
  `api_key` varchar(32) NOT NULL COMMENT 'Key值',
  `api_callback_address` varchar(250) DEFAULT NULL COMMENT '回调地址',
  `api_callback_ip` varchar(255) DEFAULT NULL COMMENT 'IP鉴权地址',
  `is_activity` tinyint(1) DEFAULT '0' COMMENT '是否活动用户',
  PRIMARY KEY (`api_id`),
  UNIQUE KEY `uni_api_account` (`api_account`)
) ENGINE=InnoDB AUTO_INCREMENT=922 DEFAULT CHARSET=utf8 COMMENT='接口管理表';

-- ----------------------------
-- Table structure for t_flow_sys_city
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_city`;
CREATE TABLE `t_flow_sys_city` (
  `city_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '市ID号',
  `city_name` varchar(50) DEFAULT NULL COMMENT '市名称',
  `province_id` bigint(18) DEFAULT NULL COMMENT '省ID号',
  `province_name` varchar(50) DEFAULT NULL COMMENT '省名称',
  `area_code` tinyint(6) DEFAULT NULL COMMENT '市编号',
  `area_id` varchar(10) DEFAULT NULL COMMENT '市代码',
  PRIMARY KEY (`city_id`),
  KEY `FK_Reference_23` (`province_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=347 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='市字典表';

-- ----------------------------
-- Table structure for t_flow_sys_depart
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_depart`;
CREATE TABLE `t_flow_sys_depart` (
  `depart_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `depart_name` varchar(100) NOT NULL COMMENT '部门名称',
  `top_depart_id` bigint(18) NOT NULL COMMENT '上级部门ID',
  `user_id` bigint(18) NOT NULL COMMENT '所属用户ID',
  `status` tinyint(1) DEFAULT '1' COMMENT '功能状态(0：禁用，1：正常,2：删除)',
  `order_num` int(4) DEFAULT NULL COMMENT '排序号',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人ID',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  `depart_manager` bigint(18) DEFAULT NULL COMMENT '部门经理',
  PRIMARY KEY (`depart_id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='部门信息表';

-- ----------------------------
-- Table structure for t_flow_sys_event_log
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_event_log`;
CREATE TABLE `t_flow_sys_event_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `log_type` int(11) DEFAULT NULL COMMENT '日志类型（1-报表，2-业务交易）',
  `event_title` varchar(2000) DEFAULT NULL COMMENT '任务事件标题',
  `log_desc` varchar(200) DEFAULT NULL COMMENT '任务事件描述',
  `log_status` int(11) DEFAULT NULL COMMENT '日志状态（0-失败，1-成功）',
  `start_time` datetime(6) DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime(6) DEFAULT NULL COMMENT '结束时间',
  KEY `log_id` (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2480 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_sys_function
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_function`;
CREATE TABLE `t_flow_sys_function` (
  `function_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '功能ID',
  `function_name` varchar(50) NOT NULL COMMENT '功能名称',
  `menu_id` bigint(18) DEFAULT NULL,
  `action_url` varchar(200) DEFAULT NULL COMMENT '方法URL',
  `icon_path` varchar(200) DEFAULT NULL COMMENT '图标',
  `order_num` int(6) DEFAULT '1' COMMENT '排序号',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(0：已禁用、1：已启用、2：已删除)',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`function_id`),
  KEY `FK_Reference_9` (`menu_id`),
  CONSTRAINT `FK_Reference_9` FOREIGN KEY (`menu_id`) REFERENCES `t_flow_sys_menu` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=853 DEFAULT CHARSET=utf8 COMMENT='系统功能表';

-- ----------------------------
-- Table structure for t_flow_sys_id
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_id`;
CREATE TABLE `t_flow_sys_id` (
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_sys_ip_bw_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_ip_bw_set`;
CREATE TABLE `t_flow_sys_ip_bw_set` (
  `set_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT 'ip黑白名单ID',
  `set_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '设置类型（1：黑名单、2：白名单）',
  `ip_addr` varchar(11) NOT NULL COMMENT 'IP地址',
  `set_scope` tinyint(1) NOT NULL COMMENT '-1表示针对所有代理商用户\n            -2表示针对尚通和所有代理商用户\n            ',
  `valid_flag` tinyint(1) NOT NULL DEFAULT '1' COMMENT '有效标识(0：无效、1：有效)',
  `user_id` bigint(18) DEFAULT NULL COMMENT '为空则表示是针对所有用户的设置',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人ID',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`set_id`),
  KEY `ix_t_flow_sys_ip_bw_set_ip_addr` (`ip_addr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='IP黑白名单设置表';

-- ----------------------------
-- Table structure for t_flow_sys_log
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_log`;
CREATE TABLE `t_flow_sys_log` (
  `log_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `log_type` varchar(100) NOT NULL COMMENT '日志类型（新增、修改、删除）',
  `method_url` varchar(255) DEFAULT NULL COMMENT '方法URL',
  `ip_addr` varchar(20) DEFAULT NULL COMMENT 'IP地址',
  `note` text COMMENT '操作概述',
  `create_user_id` bigint(18) NOT NULL COMMENT '操作用户ID',
  `create_date` datetime NOT NULL COMMENT '操作时间',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1运营端、2代理商端、3企业端）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  PRIMARY KEY (`log_id`),
  KEY `ix_t_flow_sys_log_create_date` (`create_date`),
  KEY `ix_t_flow_sys_log_log_type` (`log_type`)
) ENGINE=InnoDB AUTO_INCREMENT=128231 DEFAULT CHARSET=utf8 COMMENT='操作日志表';

-- ----------------------------
-- Table structure for t_flow_sys_login_log
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_login_log`;
CREATE TABLE `t_flow_sys_login_log` (
  `log_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `ip_addr` varchar(20) NOT NULL COMMENT 'IP地址',
  `login_type` tinyint(1) NOT NULL COMMENT '1：表示从WEB登陆页登陆，其它的后面再加',
  `login_user_id` bigint(18) NOT NULL COMMENT '登陆用户ID',
  `login_user_name` varchar(50) NOT NULL COMMENT '登陆用户名',
  `login_date` datetime NOT NULL COMMENT '登录时间',
  `login_name_full` varchar(50) DEFAULT NULL COMMENT '登录全称',
  PRIMARY KEY (`log_id`),
  KEY `ix_t_flow_sys_login_log_login_user_id_type_date` (`login_user_id`,`login_type`,`login_date`)
) ENGINE=InnoDB AUTO_INCREMENT=74628 DEFAULT CHARSET=utf8 COMMENT='登录日志表';

-- ----------------------------
-- Table structure for t_flow_sys_menu
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_menu`;
CREATE TABLE `t_flow_sys_menu` (
  `menu_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `menu_name` varchar(50) NOT NULL COMMENT '菜单名称',
  `sys_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '菜单所属(1：尚通，2：代理商，3：企业)',
  `top_menu_id` bigint(18) DEFAULT NULL COMMENT '上级菜单',
  `menu_type` tinyint(1) DEFAULT '1' COMMENT '菜单类型(1：分级菜单，2：功能菜单)',
  `page_url` varchar(200) DEFAULT NULL COMMENT '页面URL',
  `icon_path` varchar(200) DEFAULT NULL COMMENT '图标',
  `order_num` int(4) DEFAULT '1' COMMENT '排序号',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态（0：已禁用，1：正常，2：已删除）',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `group_name` varchar(255) DEFAULT NULL COMMENT '分组名称',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人ID',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=238 DEFAULT CHARSET=utf8 COMMENT='系统菜单表';

-- ----------------------------
-- Table structure for t_flow_sys_mobile_dict
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_mobile_dict`;
CREATE TABLE `t_flow_sys_mobile_dict` (
  `mobile_dict_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '手机信息日志ID',
  `mobile` varchar(50) NOT NULL COMMENT '手机号码',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商ID',
  `operator_name` varchar(100) NOT NULL COMMENT '所属运营商名称',
  `province_id` bigint(18) NOT NULL COMMENT '所属省份ID',
  `city_id` bigint(18) DEFAULT '0' COMMENT '城市ID',
  `province_name` varchar(100) NOT NULL COMMENT '所属省份名称',
  `area_code` varchar(50) NOT NULL COMMENT '地区编号',
  `city_name` varchar(100) NOT NULL COMMENT '城市名称',
  `card` varchar(100) DEFAULT NULL COMMENT '卡',
  `postcode` int(7) DEFAULT NULL COMMENT '邮政编码',
  PRIMARY KEY (`mobile_dict_id`),
  UNIQUE KEY `ix_t_flow_sys_mobile_dict_mobile` (`mobile`),
  KEY `ix_t_flow_sys_mobile_dict_province_id` (`province_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9278174 DEFAULT CHARSET=utf8 COMMENT='手机字典信息表（记录手机所属运营商和省市信息）';

-- ----------------------------
-- Table structure for t_flow_sys_mobile_dict_20160929
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_mobile_dict_20160929`;
CREATE TABLE `t_flow_sys_mobile_dict_20160929` (
  `mobile_dict_id` bigint(18) NOT NULL DEFAULT '0' COMMENT '手机信息日志ID',
  `mobile` varchar(50) NOT NULL COMMENT '手机号码',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商ID',
  `operator_name` varchar(100) NOT NULL COMMENT '所属运营商名称',
  `province_id` bigint(18) NOT NULL COMMENT '所属省份ID',
  `city_id` bigint(18) DEFAULT '0' COMMENT '城市ID',
  `province_name` varchar(100) NOT NULL COMMENT '所属省份名称',
  `area_code` varchar(50) NOT NULL COMMENT '地区编号',
  `city_name` varchar(100) NOT NULL COMMENT '城市名称',
  `card` varchar(100) DEFAULT NULL COMMENT '卡',
  `postcode` int(7) DEFAULT NULL COMMENT '邮政编码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_sys_mobile_dict_bak
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_mobile_dict_bak`;
CREATE TABLE `t_flow_sys_mobile_dict_bak` (
  `mobile_dict_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '手机信息日志ID',
  `mobile` varchar(50) NOT NULL COMMENT '手机号码',
  `operator_id` bigint(18) NOT NULL COMMENT '所属运营商ID',
  `operator_name` varchar(100) NOT NULL COMMENT '所属运营商名称',
  `province_id` bigint(18) NOT NULL COMMENT '所属省份ID',
  `city_id` bigint(18) DEFAULT '0' COMMENT '城市ID',
  `province_name` varchar(100) NOT NULL COMMENT '所属省份名称',
  `area_code` varchar(50) NOT NULL COMMENT '地区编号',
  `city_name` varchar(100) NOT NULL COMMENT '城市名称',
  `card` varchar(100) DEFAULT NULL COMMENT '卡',
  `postcode` int(7) DEFAULT NULL COMMENT '邮政编码',
  PRIMARY KEY (`mobile_dict_id`),
  UNIQUE KEY `ix_t_flow_sys_mobile_dict_mobile` (`mobile`),
  KEY `ix_t_flow_sys_mobile_dict_province_id` (`province_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9467140 DEFAULT CHARSET=utf8 COMMENT='手机字典信息表（记录手机所属运营商和省市信息）';

-- ----------------------------
-- Table structure for t_flow_sys_notice
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_notice`;
CREATE TABLE `t_flow_sys_notice` (
  `notice_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '公告ID',
  `notice_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '公告类型：1紧急公告 2普通公告',
  `notice_title` varchar(100) NOT NULL COMMENT '标题',
  `notice_content` text NOT NULL COMMENT '内容',
  `scope` varchar(5) NOT NULL COMMENT '接收人类型（1：尚通，2：代理商，3：企业用户）多个：1,2',
  `valid_date_begin` datetime DEFAULT NULL COMMENT '有效期开始',
  `valid_date_end` datetime DEFAULT NULL COMMENT '有效期截止',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：无效，1：有效，2：已删除）',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`notice_id`),
  KEY `ix_t_flow_sys_notice_create_date` (`create_date`),
  KEY `ix_t_flow_sys_notice_scope` (`scope`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='系统公告表';

-- ----------------------------
-- Table structure for t_flow_sys_notice_read
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_notice_read`;
CREATE TABLE `t_flow_sys_notice_read` (
  `read_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '阅读ID',
  `user_id` bigint(18) NOT NULL COMMENT '用户ID',
  `notice_id` bigint(18) NOT NULL COMMENT '公告ID',
  `read_time` datetime NOT NULL COMMENT '阅读时间',
  PRIMARY KEY (`read_id`),
  KEY `FK_Reference_33` (`notice_id`),
  KEY `ix_t_flow_sys_notice_read_notice_id` (`notice_id`),
  KEY `ix_t_flow_sys_notice_read_read_time` (`read_time`),
  KEY `ix_t_flow_sys_notice_read_user_id` (`user_id`),
  CONSTRAINT `FK_Reference_33` FOREIGN KEY (`notice_id`) REFERENCES `t_flow_sys_notice` (`notice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='用户阅读公告的记录，已阅读过的公告就不会出现在系统首页提醒中';

-- ----------------------------
-- Table structure for t_flow_sys_numcollate
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_numcollate`;
CREATE TABLE `t_flow_sys_numcollate` (
  `phone_section` varchar(15) NOT NULL COMMENT '号段',
  `area_id` varchar(5) DEFAULT NULL COMMENT '区ID',
  PRIMARY KEY (`phone_section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='号段表';

-- ----------------------------
-- Table structure for t_flow_sys_numcollate_new
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_numcollate_new`;
CREATE TABLE `t_flow_sys_numcollate_new` (
  `phone_section` varchar(15) NOT NULL COMMENT '号段',
  `area_id` varchar(5) DEFAULT NULL COMMENT '区ID',
  PRIMARY KEY (`phone_section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='号段表';

-- ----------------------------
-- Table structure for t_flow_sys_operator
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_operator`;
CREATE TABLE `t_flow_sys_operator` (
  `operator_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '运营商ID',
  `operator_name` varchar(30) NOT NULL COMMENT '运营商名称',
  `remark` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`operator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='运营商字典表\n\n1：中国移动\n2：中国联通\n3：中国电信';

-- ----------------------------
-- Table structure for t_flow_sys_province
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_province`;
CREATE TABLE `t_flow_sys_province` (
  `province_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '省ID',
  `province_name` varchar(50) NOT NULL COMMENT '省名称',
  `province_abbr` varchar(20) DEFAULT NULL COMMENT '省拼音',
  `order_num` int(2) DEFAULT NULL COMMENT '顺序号',
  PRIMARY KEY (`province_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='省表（全国，以及各个省数据）';

-- ----------------------------
-- Table structure for t_flow_sys_remind_content
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_remind_content`;
CREATE TABLE `t_flow_sys_remind_content` (
  `content_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `remind_type_id` bigint(18) NOT NULL,
  `remind_content` varchar(500) NOT NULL,
  `create_user_id` bigint(18) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`content_id`),
  KEY `FK_Reference_29` (`remind_type_id`),
  KEY `ix_t_flow_sys_remind_content_create_date` (`create_date`),
  KEY `ix_t_flow_sys_remind_content_remind_type_id` (`remind_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8824 DEFAULT CHARSET=utf8 COMMENT='事务提醒的内容';

-- ----------------------------
-- Table structure for t_flow_sys_remind_receive_readed
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_remind_receive_readed`;
CREATE TABLE `t_flow_sys_remind_receive_readed` (
  `receive_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(18) NOT NULL,
  `content_id` bigint(18) NOT NULL,
  `receive_time` datetime NOT NULL,
  `create_user_id` bigint(18) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`receive_id`),
  KEY `FK_Reference_31` (`content_id`),
  KEY `ix_t_flow_sys_remind_receive_readed_create_date` (`create_date`),
  KEY `ix_t_flow_sys_remind_receive_readed_receive_time` (`receive_time`),
  KEY `ix_t_flow_sys_remind_receive_readed_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1641 DEFAULT CHARSET=utf8 COMMENT='已读的事务提醒';

-- ----------------------------
-- Table structure for t_flow_sys_remind_receive_unread
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_remind_receive_unread`;
CREATE TABLE `t_flow_sys_remind_receive_unread` (
  `receive_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(18) NOT NULL,
  `content_id` bigint(18) NOT NULL,
  `create_user_id` bigint(18) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`receive_id`),
  KEY `FK_Reference_30` (`content_id`),
  KEY `ix_t_flow_sys_remind_receive_unread_create_date` (`create_date`),
  KEY `ix_t_flow_sys_remind_receive_unreaduser_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9147 DEFAULT CHARSET=utf8 COMMENT='未读的事务提醒';

-- ----------------------------
-- Table structure for t_flow_sys_remind_type
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_remind_type`;
CREATE TABLE `t_flow_sys_remind_type` (
  `remind_type_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '人工设置，不从序列产生',
  `remind_type_name` varchar(30) NOT NULL,
  `menu_id` bigint(10) DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '0-禁用\n            1-启用\n            2-删除',
  `receive_scope` smallint(6) NOT NULL COMMENT '0-全部\n            1-尚通\n            2-代理商\n            3-企业',
  `create_user_id` bigint(18) NOT NULL,
  `create_date` datetime NOT NULL,
  `modify_user_id` bigint(18) NOT NULL,
  `modify_date` datetime NOT NULL,
  PRIMARY KEY (`remind_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='系统发送给用户的事务提醒';

-- ----------------------------
-- Table structure for t_flow_sys_remind_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_remind_user`;
CREATE TABLE `t_flow_sys_remind_user` (
  `remind_type_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(18) NOT NULL,
  `create_user_id` bigint(18) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`remind_type_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='设置提醒类型对应的尚通用户';

-- ----------------------------
-- Table structure for t_flow_sys_role
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_role`;
CREATE TABLE `t_flow_sys_role` (
  `role_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(100) NOT NULL COMMENT '角色名称',
  `user_id` bigint(18) NOT NULL COMMENT '用户ID',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否删除（0：已禁用、1：正常、2：已删除）',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人ID',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  `depart_id` bigint(18) DEFAULT NULL COMMENT '部门ID',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8 COMMENT='角色信息表，用于角色权限分配';

-- ----------------------------
-- Table structure for t_flow_sys_role_function
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_role_function`;
CREATE TABLE `t_flow_sys_role_function` (
  `role_id` bigint(18) NOT NULL COMMENT '角色ID',
  `function_id` bigint(18) NOT NULL COMMENT '功能ID',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`role_id`,`function_id`),
  KEY `FK_Reference_6` (`function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色所绑定的功能权限';

-- ----------------------------
-- Table structure for t_flow_sys_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_set`;
CREATE TABLE `t_flow_sys_set` (
  `set_id` tinyint(1) NOT NULL AUTO_INCREMENT COMMENT '系统参数ID',
  `proxy_quota_remind` decimal(8,3) DEFAULT NULL,
  `enterprise_quota_remind` decimal(8,3) DEFAULT NULL,
  `channel_shut_down_remind` varchar(200) DEFAULT NULL COMMENT '通道关停提醒人电话（多个用逗号隔开）',
  `return_date` tinyint(1) DEFAULT NULL COMMENT '返现天数',
  `channel_quota_remind` varchar(255) DEFAULT NULL COMMENT '通道额度、余额不足提醒人',
  `channel_quota_remark` varchar(255) DEFAULT NULL COMMENT '通道额度、余额不足提醒人姓名备注',
  `early_warning_people` varchar(255) DEFAULT NULL COMMENT '预警提醒人',
  `early_warning_people_remark` varchar(255) DEFAULT NULL COMMENT '预警提醒人备注',
  `card_warning_people` varchar(255) DEFAULT NULL COMMENT '卡单提醒人',
  PRIMARY KEY (`set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='系统参数表';

-- ----------------------------
-- Table structure for t_flow_sys_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_user`;
CREATE TABLE `t_flow_sys_user` (
  `user_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL COMMENT '姓名',
  `login_name` varchar(50) NOT NULL COMMENT '登陆名',
  `login_name_full` varchar(50) NOT NULL COMMENT '完整登陆名',
  `login_pass` varchar(50) NOT NULL COMMENT '密码',
  `user_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户类型(1：尚通、2：代理商、3企业)',
  `is_manager` smallint(6) NOT NULL DEFAULT '0' COMMENT '0否，1是',
  `is_all_enterprise` tinyint(1) DEFAULT '0' COMMENT '是否有查看所有企业的权限',
  `is_all_proxy` tinyint(1) DEFAULT '0' COMMENT '是否有查看所有代理商的权限',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `depart_id` bigint(18) DEFAULT NULL COMMENT '部门ID',
  `mobile` varchar(15) DEFAULT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT '1' COMMENT '性别：（1：男，2：女）',
  `email` varchar(100) DEFAULT NULL,
  `posts` varchar(255) DEFAULT NULL COMMENT '职务',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：已禁用，1：正常，2：已删除）',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人ID',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  `monitor_login_type` tinyint(1) DEFAULT '0' COMMENT '监控登录状态:0，下线，1上线',
  `registration_id` varchar(50) DEFAULT NULL COMMENT '手机设备的 RegistrationID',
  PRIMARY KEY (`user_id`),
  KEY `FK_Reference_1` (`depart_id`),
  KEY `ix_t_flow_sys_user_login_name` (`login_name`),
  KEY `ix_t_flow_sys_user_login_pass` (`login_pass`),
  KEY `ix_t_flow_sys_user_enterprise_id_user_type` (`enterprise_id`,`user_type`),
  KEY `ix_t_flow_sys_user_proxy_id_user_type` (`proxy_id`,`user_type`),
  KEY `ix_t_flow_sys_user_login_name_full` (`login_name_full`)
) ENGINE=InnoDB AUTO_INCREMENT=1299 DEFAULT CHARSET=utf8 COMMENT='系统用户表，包含尚通、代理商和企业三大角色用户';

-- ----------------------------
-- Table structure for t_flow_sys_user_channel
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_user_channel`;
CREATE TABLE `t_flow_sys_user_channel` (
  `user_channel_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '用户通道权限ID',
  `user_id` bigint(18) DEFAULT NULL COMMENT '用户ID',
  `channel_id` bigint(18) DEFAULT NULL COMMENT '通道ID',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`user_channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_sys_user_order_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_user_order_set`;
CREATE TABLE `t_flow_sys_user_order_set` (
  `user_order_set_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '订单数据设置ID',
  `set_orle_id` varchar(255) DEFAULT NULL COMMENT '角色ID（多个用,号分开）',
  `set_user_id` varchar(255) DEFAULT NULL COMMENT '用户ID（多个用,号分开）',
  PRIMARY KEY (`user_order_set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_sys_user_role
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_user_role`;
CREATE TABLE `t_flow_sys_user_role` (
  `user_id` bigint(18) NOT NULL COMMENT '用户ID',
  `role_id` bigint(18) NOT NULL COMMENT '角色ID',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  KEY `FK_Reference_3` (`user_id`),
  KEY `FK_Reference_4` (`role_id`),
  KEY `ix_t_flow_sys_user_role_role_id` (`role_id`),
  KEY `ix_t_flow_sys_user_role_user_id` (`user_id`),
  CONSTRAINT `FK_Reference_3` FOREIGN KEY (`user_id`) REFERENCES `t_flow_sys_user` (`user_id`),
  CONSTRAINT `FK_Reference_4` FOREIGN KEY (`role_id`) REFERENCES `t_flow_sys_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色表';

-- ----------------------------
-- Table structure for t_flow_sys_user_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_user_set`;
CREATE TABLE `t_flow_sys_user_set` (
  `user_set_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型(1-代理商，2-企业）',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `logo_img` varchar(150) DEFAULT NULL,
  `web_name` varchar(150) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `is_sub_use` tinyint(1) DEFAULT '1' COMMENT '下级是否可见：1，可用；0，不可用',
  PRIMARY KEY (`user_set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='用户系统设置信息表';

-- ----------------------------
-- Table structure for t_flow_ticket_exchange
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_ticket_exchange`;
CREATE TABLE `t_flow_ticket_exchange` (
  `redeem_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '兑换Id',
  `redeem_code` varchar(50) DEFAULT NULL COMMENT '兑换码',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '三大运营商',
  `order_id` varchar(50) DEFAULT NULL COMMENT '订单id',
  `product_id` bigint(18) DEFAULT NULL COMMENT '流量包Id',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `exchange_time` datetime DEFAULT NULL COMMENT '兑换时间',
  `order_date` datetime DEFAULT NULL COMMENT '下单时间',
  `complete_time` datetime DEFAULT NULL COMMENT '下单完成时间',
  `order_status` tinyint(1) DEFAULT NULL COMMENT '充值状态',
  `user_activity_id` int(10) DEFAULT NULL COMMENT '流量券活动Id',
  `platform_openid` varchar(50) DEFAULT NULL COMMENT '平台用户id',
  `wx_photo` varchar(150) DEFAULT NULL COMMENT '微信头像',
  `wx_name` varchar(150) DEFAULT NULL COMMENT '微信昵称',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商,2企业）',
  PRIMARY KEY (`redeem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_ticket_history
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_ticket_history`;
CREATE TABLE `t_flow_ticket_history` (
  `flowticket_history_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '领取历史Id',
  `redeem_code` varchar(50) DEFAULT NULL COMMENT '兑换码',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '三大运营商',
  `product_id` bigint(18) DEFAULT NULL COMMENT '流量包Id',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `receive_time` datetime DEFAULT NULL COMMENT '领取时间',
  `effective_duration` varchar(0) DEFAULT NULL COMMENT '流量券有效时长',
  `flowticket_status` tinyint(1) DEFAULT NULL COMMENT '流量券状态（已兑换、已过期、已失效）',
  `user_activity_id` bigint(18) DEFAULT NULL COMMENT '流量券活动Id',
  PRIMARY KEY (`flowticket_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_ticket_receive
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_ticket_receive`;
CREATE TABLE `t_flow_ticket_receive` (
  `flowticket_receive_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '领取Id',
  `redeem_code` varchar(50) DEFAULT NULL COMMENT '兑换码',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '三大运营商',
  `product_id` bigint(18) DEFAULT NULL COMMENT '流量包Id',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `receive_time` datetime DEFAULT NULL COMMENT '领取时间',
  `flowticket_status` tinyint(1) DEFAULT NULL COMMENT '流量券状态（已兑换、已过期、已失效）',
  `effective_duration` varchar(50) DEFAULT NULL COMMENT '流量券有效时长',
  `user_activity_id` bigint(18) DEFAULT NULL COMMENT '流量券活动Id',
  PRIMARY KEY (`flowticket_receive_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_top_contract
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_top_contract`;
CREATE TABLE `t_flow_top_contract` (
  `top_contract_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '上游合同ID',
  `contract_code` varchar(100) DEFAULT NULL COMMENT '合同编号',
  `top_name` varchar(100) DEFAULT NULL COMMENT '上游名称',
  `contract_money` decimal(11,3) DEFAULT NULL,
  `effect_date` datetime DEFAULT NULL COMMENT '生效时间',
  `expire_date` datetime DEFAULT NULL COMMENT '到期时间',
  `contract_desc` text COMMENT '条款说明',
  `enclosure` varchar(500) DEFAULT NULL COMMENT '附件',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（1：草稿、2：等待审核、3：初审通过、4：初审驳回、5：复审通过、6：复审驳回）',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `connect_type` tinyint(1) DEFAULT NULL COMMENT '连接类型：1,直连运营商；2，第三方公司',
  `discount` varchar(255) DEFAULT NULL,
  `quota` decimal(11,3) DEFAULT NULL,
  `is_enclosure` tinyint(1) DEFAULT NULL COMMENT '是否上传附件，1：是，0：否',
  `is_channel` tinyint(1) DEFAULT NULL COMMENT '是否设置通道账户，1：是，0：否',
  `is_discount` tinyint(1) DEFAULT NULL COMMENT '是否设置通道折扣，1：是，0：否',
  `sign_company` tinyint(1) DEFAULT NULL COMMENT '签约公司',
  PRIMARY KEY (`top_contract_id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8 COMMENT='上游合同信息表';

-- ----------------------------
-- Table structure for t_flow_top_contract_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_top_contract_process`;
CREATE TABLE `t_flow_top_contract_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `top_contract_id` bigint(18) NOT NULL COMMENT '上游合同ID',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（1：审核通过、2：审核驳回）',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审、3：打款）',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8 COMMENT='上游合同审核记录表';

-- ----------------------------
-- Table structure for t_flow_top_ticke
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_top_ticke`;
CREATE TABLE `t_flow_top_ticke` (
  `ticke_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '开票ID号',
  `ticke_code` varchar(50) DEFAULT NULL COMMENT '发票申请单号（自动生成字母+年月日+4位排序号）',
  `top_name` varchar(150) DEFAULT NULL COMMENT '上游名称',
  `taxpayer_name` varchar(150) DEFAULT NULL COMMENT '纳税人名称',
  `taxpayer_number` varchar(150) DEFAULT NULL COMMENT '纳税人识别号',
  `ticket_money` decimal(16,3) DEFAULT NULL,
  `cumulative` decimal(16,3) DEFAULT NULL,
  `actual_ticket_money` decimal(16,3) DEFAULT NULL,
  `property` tinyint(1) DEFAULT NULL COMMENT '上游属性：1直连；2第三方',
  `is_record` tinyint(1) DEFAULT NULL COMMENT '是否录入开票信息：1是；0否',
  `charge_name` varchar(150) DEFAULT NULL COMMENT '负责人',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`ticke_id`)
) ENGINE=InnoDB AUTO_INCREMENT=289 DEFAULT CHARSET=utf8 COMMENT='上游开票信息表';

-- ----------------------------
-- Table structure for t_flow_top_ticke_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_top_ticke_record`;
CREATE TABLE `t_flow_top_ticke_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `ticke_id` bigint(18) DEFAULT NULL COMMENT '开票ID号',
  `ticket_money` decimal(11,3) DEFAULT NULL,
  `operater_before_money` decimal(11,3) DEFAULT NULL,
  `operater_after_money` decimal(11,3) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=390 DEFAULT CHARSET=utf8 COMMENT='上游开票记录表';

-- ----------------------------
-- Table structure for t_flow_tran_sequence
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_tran_sequence`;
CREATE TABLE `t_flow_tran_sequence` (
  `id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `tran_type` int(11) DEFAULT NULL COMMENT '交易类型',
  `current_seq` bigint(18) DEFAULT NULL COMMENT '当前序列值',
  `next_seq` bigint(18) DEFAULT '1' COMMENT '下一序列值',
  `get_count` int(11) DEFAULT NULL COMMENT '取值数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='事务提交序列表';

-- ----------------------------
-- Table structure for t_flow_user_contact
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_user_contact`;
CREATE TABLE `t_flow_user_contact` (
  `id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '联系人ID',
  `company_id` bigint(18) DEFAULT NULL COMMENT '代理商、企业的ID',
  `company_type` tinyint(1) DEFAULT NULL COMMENT '1、代理商、2、企业',
  `contact_name` varchar(50) DEFAULT NULL COMMENT '联系人姓名',
  `tel` varchar(13) DEFAULT NULL COMMENT '联系电话',
  `job` varchar(50) DEFAULT NULL COMMENT '职位',
  `qq` bigint(12) DEFAULT NULL COMMENT 'QQ号',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `remark` text COMMENT '备注',
  `status` tinyint(1) DEFAULT NULL COMMENT '启用禁用状态，0：已禁用、1：正常、2：已删除',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='企业、代理商联系人表';

-- ----------------------------
-- Table structure for t_flow_user_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_user_set`;
CREATE TABLE `t_flow_user_set` (
  `account_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '账户ID',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `wx_appid` varchar(100) DEFAULT NULL COMMENT '公众号',
  `wx_appsecret` varchar(100) DEFAULT NULL COMMENT 'APPSECRET',
  `wx_mchid` varchar(100) DEFAULT NULL COMMENT '商户号',
  `wx_key` varchar(50) DEFAULT NULL COMMENT '商户号密钥',
  `wx_pem_file_one` varchar(100) DEFAULT NULL COMMENT 'pem文件1',
  `wx_pem_file_two` varchar(100) DEFAULT NULL COMMENT 'pem文件2',
  `alipay_partner` varchar(100) DEFAULT NULL COMMENT '支付宝账号',
  `alipay_key` varchar(100) DEFAULT NULL COMMENT '支付宝密钥',
  `alipay_pem_file` varchar(100) DEFAULT NULL COMMENT '支付宝pem文件',
  `alipay_pem_file_two` varchar(100) DEFAULT NULL,
  `app_appid` varchar(100) DEFAULT NULL,
  `app_appsecret` varchar(100) DEFAULT NULL,
  `app_mchid` varchar(100) DEFAULT NULL,
  `app_key` varchar(50) DEFAULT NULL,
  `paykey` varchar(50) DEFAULT NULL COMMENT '密钥key 用于支付宝退款',
  `app_pem_file_one` varchar(100) DEFAULT NULL,
  `app_pem_file_two` varchar(100) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `payment_type` tinyint(1) DEFAULT '2' COMMENT '收款方式（1.运营方收款，2企业收款，3代理商收款）',
  `third_app_key` varchar(32) DEFAULT NULL,
  `third_app_code` varchar(32) DEFAULT NULL,
  `wx_name` varchar(100) DEFAULT NULL COMMENT '微信号',
  `wx_type` tinyint(1) DEFAULT '1' COMMENT '公众号类型：1.服务号 2.订阅号',
  `explanation` text COMMENT '充值说明',
  `consumer_phone` varchar(15) DEFAULT NULL COMMENT '客服电话',
  `pc_alipay_account` varchar(100) DEFAULT NULL COMMENT '网页支付宝账号',
  `pc_alipay_partner` varchar(100) DEFAULT NULL COMMENT '网页支付宝商户号',
  `pc_alipay_key` varchar(100) DEFAULT NULL COMMENT '网页支付宝密钥',
  `pc_explanation` text COMMENT '网页充值说明',
  `pc_notice` text COMMENT '网页公告',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=380 DEFAULT CHARSET=utf8 COMMENT='用户设置信息表';

-- ----------------------------
-- Table structure for t_flow_wxs_attention
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_attention`;
CREATE TABLE `t_flow_wxs_attention` (
  `attention_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '状态表id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型(1.代理商，2企业)',
  `attention_new` bigint(18) DEFAULT NULL COMMENT '新增关注人数',
  `attention_cancel` bigint(18) DEFAULT NULL COMMENT '取消关注人数',
  `attention_grow` bigint(18) DEFAULT NULL COMMENT '净增关注人数',
  `attention_total` bigint(18) DEFAULT NULL COMMENT '累计关注人数',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`attention_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_wxs_auth
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_auth`;
CREATE TABLE `t_flow_wxs_auth` (
  `auth_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '授权id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商,2企业）',
  `auth_status` tinyint(1) DEFAULT NULL COMMENT '是否绑定（1.已绑,2未绑）',
  `auth_nickname` varchar(50) DEFAULT NULL COMMENT '授权方昵称',
  `auth_headimg` varchar(255) DEFAULT NULL COMMENT '授权方头像',
  `auth_service_type` tinyint(1) DEFAULT NULL COMMENT '授权公众号类型（1.服务号，2.订阅号）',
  `auth_wxname` varchar(50) DEFAULT NULL COMMENT '授权方微信号',
  `auth_businesspay` tinyint(1) DEFAULT NULL COMMENT '是否开通微信支付功能(1.开通，2，未开通)',
  `auth_appid` varchar(50) DEFAULT NULL COMMENT '授权方appid',
  `auth_code` varchar(75) DEFAULT NULL COMMENT '授权码',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_wxs_reply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_reply`;
CREATE TABLE `t_flow_wxs_reply` (
  `reply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `reply_type` tinyint(1) DEFAULT NULL COMMENT '回复类型(1文字，2图文。3多图文。4活动))',
  `reply_keyword` varchar(50) DEFAULT NULL COMMENT '回复所需关键字',
  `reply_keywordid` bigint(18) DEFAULT NULL COMMENT '回复反馈id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `reply_concern` tinyint(1) DEFAULT NULL COMMENT '关注回复（1.是，2.否）',
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_wxs_replyactivity
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_replyactivity`;
CREATE TABLE `t_flow_wxs_replyactivity` (
  `replyactivity_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `user_activity_id` bigint(18) DEFAULT NULL COMMENT '回复活动id',
  `replyactivity_title` varchar(100) DEFAULT NULL COMMENT '回复标题',
  `replyactivity_img` varchar(200) DEFAULT NULL COMMENT '回复图片',
  `replyactivity_description` text COMMENT '回复详细内容',
  `replyactivity_contact` text COMMENT '回复文字',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`replyactivity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_wxs_replyimg
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_replyimg`;
CREATE TABLE `t_flow_wxs_replyimg` (
  `replyimg_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `replyimg_title` varchar(100) DEFAULT NULL COMMENT '回复标题',
  `replyimg_img` varchar(100) DEFAULT NULL COMMENT '回复图片',
  `replyimg_url` varchar(100) DEFAULT NULL COMMENT '回复链接',
  `replyimg_description` text COMMENT '回复详细内容',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`replyimg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_wxs_replymoreimg
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_replymoreimg`;
CREATE TABLE `t_flow_wxs_replymoreimg` (
  `replymoreimg_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `replymoreimg_title1` varchar(100) DEFAULT NULL COMMENT '回复标题1',
  `replymoreimg_img1` varchar(100) DEFAULT NULL COMMENT '回复图片1',
  `replymoreimg_url1` varchar(100) DEFAULT NULL COMMENT '回复路径1',
  `replymoreimg_title2` varchar(100) DEFAULT NULL COMMENT '回复标题2',
  `replymoreimg_img2` varchar(100) DEFAULT NULL COMMENT '回复图片2',
  `replymoreimg_url2` varchar(100) DEFAULT NULL COMMENT '回复路径2',
  `replymoreimg_title3` varchar(100) DEFAULT NULL COMMENT '回复标题3',
  `replymoreimg_img3` varchar(100) DEFAULT NULL COMMENT '回复图片3',
  `replymoreimg_url3` varchar(100) DEFAULT NULL COMMENT '回复路径3',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `replymoreimg_material` varchar(50) DEFAULT NULL COMMENT '多图片素材集',
  PRIMARY KEY (`replymoreimg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_wxs_replytext
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_replytext`;
CREATE TABLE `t_flow_wxs_replytext` (
  `replytext_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `replytext_contact` text COMMENT '回复文字',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`replytext_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_wx_enterprise
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wx_enterprise`;
CREATE TABLE `t_flow_wx_enterprise` (
  `flowscore_basic_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '企业积分基础信息id',
  `flowscore_exchange_rate` int(10) DEFAULT NULL COMMENT '积分兑换比率',
  `daily_score` int(10) DEFAULT NULL COMMENT ' 没日签到积分',
  `flowscore_basic_logo` varchar(100) DEFAULT NULL COMMENT '每日签到logo图路径',
  `flowscore_basic_background` varchar(100) DEFAULT NULL COMMENT '每日签到背景图路径',
  `flowscore_basic_photo` varchar(100) DEFAULT NULL COMMENT '每日签到宣传图路径',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '需改时间',
  `start_time` varchar(20) DEFAULT NULL COMMENT '开始时间',
  `end_time` varchar(20) DEFAULT NULL COMMENT '结束时间',
  `start_date` datetime DEFAULT NULL COMMENT '活动开始时间',
  `end_date` datetime DEFAULT NULL COMMENT '活动结束时间',
  PRIMARY KEY (`flowscore_basic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_flow_wx_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wx_user`;
CREATE TABLE `t_flow_wx_user` (
  `wx_user_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '用户微信id',
  `wx_openid` varchar(50) DEFAULT NULL COMMENT '微信用户openid',
  `wx_photo_url` varchar(200) DEFAULT NULL COMMENT '微信用户头像',
  `wx_name` varchar(50) DEFAULT NULL COMMENT '微信用户昵称',
  `user_flow_score` bigint(18) DEFAULT NULL COMMENT '用户积分',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业类型',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `last_flow_date` datetime DEFAULT NULL COMMENT '最后签到时间',
  `mobile` varchar(11) DEFAULT '-1' COMMENT '手机号',
  PRIMARY KEY (`wx_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=716 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_order_amount
-- ----------------------------
DROP TABLE IF EXISTS `t_order_amount`;
CREATE TABLE `t_order_amount` (
  `order_id` bigint(20) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `price` decimal(11,3) DEFAULT NULL,
  `dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Procedure structure for p_auto_init_monitor_channel_stat
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_init_monitor_channel_stat`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_init_monitor_channel_stat`(
)
BEGIN 
	/*
	author		cxw
	date		2016/07/25
	desc		通道订单监控初始化统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	DECLARE v_stat_date DATE;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	SET v_stat_date = DATE_ADD(CURDATE(),INTERVAL 1 DAY);
	#SET v_stat_date = CURDATE();
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (3,'p_auto_init_monitor_channel_stat','通道订单监控初始化统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_monitor_channel_stat WHERE stat_day = DATE_FORMAT(v_stat_date,'%Y%m%d');
		#新增初始数据
		INSERT INTO t_flow_monitor_channel_stat(channel_id,channel_code,channel_name
		,stat_year,stat_month,stat_day,stat_time
		,total_count,success_count,success_amount,faile_count,faile_amount,rand_id)
		SELECT c.channel_id,c.channel_code,c.channel_name
		,YEAR(v_stat_date) stat_year,DATE_FORMAT(v_stat_date,'%Y%m') stat_month,DATE_FORMAT(v_stat_date,'%Y%m%d') stat_day
		,CONCAT(DATE_FORMAT(v_stat_date,'%Y%m%d'),sid.id) stat_time
		,0 total_count,0 success_count,0 success_amount,0 faile_count,0 faile_amount
		,rid.id
		FROM `t_flow_channel` c,(SELECT RIGHT(100+id - 1,2) id FROM t_flow_sys_id WHERE id <= 24) sid
		,(SELECT id FROM t_flow_sys_id WHERE id <= 5) rid
		ORDER BY c.channel_id,stat_time;
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_init_monitor_province_stat
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_init_monitor_province_stat`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_init_monitor_province_stat`(
)
BEGIN 
	/*
	author		cxw
	date		2016/07/25
	desc		省份订单监控初始化统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	DECLARE v_stat_date DATE;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	SET v_stat_date = DATE_ADD(CURDATE(),INTERVAL 1 DAY);
	#SET v_stat_date = CURDATE();
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (3,'p_auto_init_monitor_province_stat','省份订单监控初始化统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_monitor_province_stat WHERE stat_day = DATE_FORMAT(v_stat_date,'%Y%m%d');
		#新增初始数据
		INSERT INTO t_flow_monitor_province_stat(province_id,province_name
		,stat_year,stat_month,stat_day,stat_time
		,total_count,success_count,success_amount,faile_count,faile_amount,rand_id)
		SELECT c.province_id,c.province_name
		,YEAR(v_stat_date) stat_year,DATE_FORMAT(v_stat_date,'%Y%m') stat_month,DATE_FORMAT(v_stat_date,'%Y%m%d') stat_day
		,CONCAT(DATE_FORMAT(v_stat_date,'%Y%m%d'),sid.id) stat_time
		,0 total_count,0 success_count,0 success_amount,0 faile_count,0 faile_amount
		,rid.id
		FROM `t_flow_sys_province` c,(SELECT RIGHT(100+id - 1,2) id FROM t_flow_sys_id WHERE id <= 24) sid
		,(SELECT id FROM t_flow_sys_id WHERE id <= 5) rid
		ORDER BY c.province_id,stat_time;
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_channel
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_channel`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_channel`(
  IN p_start_time DATETIME
  ,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		cxw
	date		2016/05/25
	desc		通道收入日统计
	收入金额	expense_sum	收入金额=单价*一级代理折扣
	成本金额	cost_sum	成本金额=单价*通道折扣
	利润金额	profit_sum	利润金额=收入-成本
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_channel','通道收入日统计',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#通道日统计
		#先清空
		DELETE FROM t_flow_rpt_channel WHERE rpt_date BETWEEN p_start_time AND p_end_time;
		#主通道
		INSERT INTO t_flow_rpt_channel(channel_id,rpt_date,expense_sum,cost_sum,profit_sum,create_date,rebate_sum)
		SELECT cdt.channel_id,cdt.rpt_date
		,IFNULL(cd.expense_sum,0) expense_sum
		,IFNULL(cd.cost_sum,0) cost_sum
		,IFNULL(cd.expense_sum,0) - IFNULL(cd.cost_sum,0) + IFNULL(cd.rebate_sum,0)  profit_sum
		,NOW() create_date
		,IFNULL(cd.rebate_sum,0) rebate_sum
		FROM 
		(
			SELECT c.channel_id,dt.rpt_date
			FROM t_flow_channel c,(
				SELECT DATE_ADD(p_start_time, INTERVAL + id - 1 DAY) rpt_date
				FROM t_flow_sys_id 
				WHERE id <= DATEDIFF(p_end_time,p_start_time)
			) dt
		) cdt LEFT JOIN
		(
			SELECT oc.channel_id
			,DATE_FORMAT(oc.order_date,'%Y-%m-%d') rpt_date
			,SUM(CASE WHEN oc.stat_type = 1 THEN  oc.discount_price ELSE oc.price*oc.one_proxy_discount END) expense_sum
			,SUM(ROUND(oc.price*oc.top_discount,3)) cost_sum
			,SUM(ROUND(oc.price*oc.top_rebate_discount,3)) rebate_sum
			#,SUM(CASE oc.top_discount WHEN 1 THEN 0 ELSE ROUND((CASE WHEN oc.stat_type = 1 THEN ROUND(oc.discount_price/oc.price,3) ELSE oc.one_proxy_discount END - oc.top_discount)*oc.`price`,3) END) profit_sum	
			FROM 
			(
				SELECT o.channel_id,o.order_date,o.discount_price,o.one_proxy_discount,o.top_discount,o.price,1 stat_type,o.top_rebate_discount
				FROM t_flow_order o
				WHERE o.order_status = 2
				AND o.order_date BETWEEN p_start_time AND p_end_time
				AND o.user_type = 2
				AND EXISTS(SELECT * FROM t_flow_enterprise e,t_flow_proxy p WHERE e.`top_proxy_id` = p.`proxy_id` AND p.`proxy_type` = 1 AND o.enterprise_id = e.enterprise_id)
				UNION ALL
				SELECT o.back_channel_id channel_id,o.order_date, o.discount_price,o.one_proxy_discount,o.back_top_discount top_discount,o.price,1 stat_type,o.back_top_rebate_discount top_rebate_discount
				FROM t_flow_order o
				WHERE o.order_status = 5
				AND o.user_type = 2
				AND EXISTS(SELECT * FROM t_flow_enterprise e,t_flow_proxy p WHERE e.`top_proxy_id` = p.`proxy_id` AND p.`proxy_type` = 1 AND o.enterprise_id = e.enterprise_id)
				AND o.order_date BETWEEN p_start_time AND p_end_time
				UNION ALL
				SELECT o.channel_id,o.order_date,o.discount_price,o.one_proxy_discount,o.top_discount,o.price,2 stat_type,o.top_rebate_discount
				FROM t_flow_order o
				WHERE o.order_status = 2
				AND o.order_date BETWEEN p_start_time AND p_end_time
				#AND o.user_type = 1
				AND EXISTS(SELECT * FROM t_flow_proxy p WHERE p.proxy_level  = 1 AND p.proxy_type <> 1 AND o.one_proxy_id = p.proxy_id)
				UNION ALL
				SELECT o.back_channel_id channel_id,o.order_date,o.discount_price,o.one_proxy_discount,o.back_top_discount top_discount,o.price,2 stat_type,o.back_top_rebate_discount top_rebate_discount
				FROM t_flow_order o
				WHERE o.order_status = 5
				#AND o.user_type = 1
				AND EXISTS(SELECT * FROM t_flow_proxy p WHERE p.proxy_level  = 1 AND p.proxy_type <> 1 AND o.one_proxy_id = p.proxy_id)
				AND o.order_date BETWEEN p_start_time AND p_end_time
			) oc
			GROUP BY oc.channel_id,DATE_FORMAT(oc.order_date,'%Y-%m-%d')
		) cd
		ON cdt.channel_id = cd.channel_id AND cdt.rpt_date = cd.rpt_date
		ORDER BY cdt.channel_id,cdt.rpt_date;
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK;  
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_channel2
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_channel2`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_channel2`(
  in p_channel_id bigint
  ,IN p_start_time DATETIME
  ,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		cxw
	date		2016/05/25
	desc		单个通道收入日统计
	收入金额	expense_sum	收入金额=单价*一级代理折扣
	成本金额	cost_sum	成本金额=单价*通道折扣
	利润金额	profit_sum	利润金额=收入-成本
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_channel','通道收入日统计',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#通道日统计
		#先清空
		DELETE FROM t_flow_rpt_channel WHERE channel_id = p_channel_id AND rpt_date BETWEEN p_start_time AND p_end_time;
		#主通道
		INSERT INTO t_flow_rpt_channel(channel_id,rpt_date,expense_sum,cost_sum,profit_sum,create_date,rebate_sum)
		SELECT cdt.channel_id,cdt.rpt_date
		,IFNULL(cd.expense_sum,0) expense_sum
		,IFNULL(cd.cost_sum,0) cost_sum
		,IFNULL(cd.expense_sum,0) - IFNULL(cd.cost_sum,0) + IFNULL(cd.rebate_sum,0)  profit_sum
		,NOW() create_date
		,IFNULL(cd.rebate_sum,0) rebate_sum
		FROM 
		(
			SELECT c.channel_id,dt.rpt_date
			FROM (SELECT * FROM t_flow_channel WHERE channel_id = p_channel_id) c,(
				SELECT DATE_ADD(p_start_time, INTERVAL + id - 1 DAY) rpt_date
				FROM t_flow_sys_id 
				WHERE id <= DATEDIFF(p_end_time,p_start_time)
			) dt
		) cdt LEFT JOIN
		(
			SELECT oc.channel_id
			,DATE_FORMAT(oc.order_date,'%Y-%m-%d') rpt_date
			,SUM(CASE WHEN oc.stat_type = 1 THEN  oc.discount_price ELSE oc.price*oc.one_proxy_discount END) expense_sum
			,SUM(ROUND(oc.price*oc.top_discount,3)) cost_sum
			,SUM(ROUND(oc.price*oc.top_rebate_discount,3)) rebate_sum
			#,SUM(CASE oc.top_discount WHEN 1 THEN 0 ELSE ROUND((CASE WHEN oc.stat_type = 1 THEN ROUND(oc.discount_price/oc.price,3) ELSE oc.one_proxy_discount END - oc.top_discount)*oc.`price`,3) END) profit_sum	
			FROM 
			(
				SELECT o.channel_id,o.order_date,o.discount_price,o.one_proxy_discount,o.top_discount,o.price,1 stat_type,o.top_rebate_discount
				FROM t_flow_order o
				WHERE o.order_status = 2
				AND o.order_date BETWEEN p_start_time AND p_end_time
				AND o.user_type = 2
				AND EXISTS(SELECT * FROM t_flow_enterprise e,t_flow_proxy p WHERE e.`top_proxy_id` = p.`proxy_id` AND p.`proxy_type` = 1 AND o.enterprise_id = e.enterprise_id)
				and o.channel_id = p_channel_id
				UNION ALL
				SELECT o.back_channel_id channel_id,o.order_date, o.discount_price,o.one_proxy_discount,o.back_top_discount top_discount,o.price,1 stat_type,o.back_top_rebate_discount top_rebate_discount
				FROM t_flow_order o
				WHERE o.order_status = 5
				AND o.user_type = 2
				AND EXISTS(SELECT * FROM t_flow_enterprise e,t_flow_proxy p WHERE e.`top_proxy_id` = p.`proxy_id` AND p.`proxy_type` = 1 AND o.enterprise_id = e.enterprise_id)
				AND o.order_date BETWEEN p_start_time AND p_end_time
				AND o.back_channel_id = p_channel_id
				UNION ALL
				SELECT o.channel_id,o.order_date,o.discount_price,o.one_proxy_discount,o.top_discount,o.price,2 stat_type,o.top_rebate_discount
				FROM t_flow_order o
				WHERE o.order_status = 2
				AND o.order_date BETWEEN p_start_time AND p_end_time
				#AND o.user_type = 1
				AND EXISTS(SELECT * FROM t_flow_proxy p WHERE p.proxy_level  = 1 AND p.proxy_type <> 1 AND o.one_proxy_id = p.proxy_id)
				AND o.channel_id = p_channel_id
				UNION ALL
				SELECT o.back_channel_id channel_id,o.order_date,o.discount_price,o.one_proxy_discount,o.back_top_discount top_discount,o.price,2 stat_type,o.back_top_rebate_discount top_rebate_discount
				FROM t_flow_order o
				WHERE o.order_status = 5
				#AND o.user_type = 1
				AND EXISTS(SELECT * FROM t_flow_proxy p WHERE p.proxy_level  = 1 AND p.proxy_type <> 1 AND o.one_proxy_id = p.proxy_id)
				AND o.back_channel_id = p_channel_id
				AND o.order_date BETWEEN p_start_time AND p_end_time
			) oc
			GROUP BY oc.channel_id,DATE_FORMAT(oc.order_date,'%Y-%m-%d')
		) cd
		ON cdt.channel_id = cd.channel_id AND cdt.rpt_date = cd.rpt_date
		ORDER BY cdt.channel_id,cdt.rpt_date;
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK;  
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_channel_account
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_channel_account`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_channel_account`(
	IN p_start_time DATETIME
)
BEGIN 
	/*
	author		lk
	date		2016/08/18
	desc		通道账户信息日记录(快照)
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_channel_account','通道账户信息日记录',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		#DELETE FROM t_flow_channel_account_his WHERE  record_day = DATE_FORMAT(p_start_time,'%Y%m%d') ;
	
		#新增初始数据
		INSERT INTO t_flow_channel_account_his(account_id,account_name,total_money,surplus_money,times,
 		last_recharge_money,last_recharge_date,
 		create_user_id,create_date,quota_remind,record_day)
 		SELECT 
		a.account_id account_id,
		a.account_name account_name,
 		a.total_money total_money,
 		a.surplus_money surplus_money,
 		a.times times,a.last_recharge_money last_recharge_money,
 		a.last_recharge_date last_recharge_date,
 		a.create_user_id create_user_id,
 		a.create_date create_date,
 		a.quota_remind quota_remind,
 		DATE_FORMAT(p_start_time,'%Y%m%d') record_day
		FROM t_flow_channel_account AS a ;
	
	IF t_error = 1 THEN 
		
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_enterprise
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_enterprise`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_enterprise`(
  IN p_start_time DATETIME
  ,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		cxw
	date		2016/05/25
	desc		处理成功订单
	收入金额	expense_sum	收入金额=单价*一级代理折扣
	成本金额	cost_sum	成本金额=单价*通道折扣
	利润金额	profit_sum	利润金额=收入-成本
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_enterprise','企业收入日统计',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#企业日统计
		#先清空
		DELETE FROM t_flow_rpt_direct_enterprise WHERE rpt_date BETWEEN p_start_time AND p_end_time;
		#新增最新数据
		INSERT INTO t_flow_rpt_direct_enterprise(enterprise_id,proxy_id,rpt_date,expense_sum,cost_sum,profit_sum,create_date,rebate_sum)
		SELECT cdt.enterprise_id,cdt.proxy_id,cdt.rpt_date
		,IFNULL(cd.expense_sum,0) expense_sum
		,IFNULL(cd.cost_sum,0) cost_sum
		,IFNULL(cd.expense_sum,0) - IFNULL(cd.cost_sum,0) + IFNULL(cd.rebate_sum,0)  profit_sum
		,NOW() create_date
		,IFNULL(cd.rebate_sum,0) rebate_sum
		FROM 
		(
			SELECT c.enterprise_id,c.top_proxy_id proxy_id,dt.rpt_date
			FROM ( SELECT * FROM t_flow_enterprise e  
				WHERE EXISTS(SELECT * FROM t_flow_proxy p WHERE proxy_type = 1 AND e.`top_proxy_id` = p.`proxy_id`)) c,(
				SELECT DATE_ADD(p_start_time, INTERVAL + id - 1 DAY) rpt_date
				FROM t_flow_sys_id 
				WHERE id <= DATEDIFF(p_end_time,p_start_time)
			) dt
		) cdt LEFT JOIN
		(
			SELECT o.enterprise_id
			,MAX(o.proxy_id) proxy_id
			,DATE_FORMAT(o.order_date,'%Y-%m-%d') rpt_date
			,SUM(o.discount_price) expense_sum
			,SUM(ROUND(o.price*o.top_discount,3)) cost_sum	
			,NOW() create_date
			,SUM(ROUND(o.price*o.top_rebate_discount,3)) rebate_sum
			FROM (
				SELECT enterprise_id,proxy_id,order_date,discount_price,one_proxy_discount
				,CASE WHEN order_status = 2 THEN top_discount ELSE back_top_discount END top_discount
				,price
				,CASE order_status WHEN 2 THEN top_rebate_discount WHEN 5 THEN back_top_rebate_discount END top_rebate_discount 
				FROM t_flow_order
				WHERE order_status IN (2,5)
				AND user_type = 2
				AND order_date BETWEEN p_start_time AND p_end_time
			) o
			GROUP BY o.enterprise_id,DATE_FORMAT(o.order_date,'%Y-%m-%d')
		) cd
		ON cdt.enterprise_id = cd.enterprise_id AND cdt.rpt_date = cd.rpt_date
		ORDER BY cdt.enterprise_id,cdt.rpt_date;	
		
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK;  
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT;  
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_enterprise2
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_enterprise2`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_enterprise2`(
  in p_enterprise_id bigint
  ,IN p_start_time DATETIME
  ,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		cxw
	date		2016/05/25
	desc		处理成功订单
	收入金额	expense_sum	收入金额=单价*一级代理折扣
	成本金额	cost_sum	成本金额=单价*通道折扣
	利润金额	profit_sum	利润金额=收入-成本
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_enterprise','企业收入日统计',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#企业日统计
		#先清空
		DELETE FROM t_flow_rpt_direct_enterprise WHERE enterprise_id = p_enterprise_id and rpt_date BETWEEN p_start_time AND p_end_time;
		#新增最新数据
		INSERT INTO t_flow_rpt_direct_enterprise(enterprise_id,proxy_id,rpt_date,expense_sum,cost_sum,profit_sum,create_date,rebate_sum)
		SELECT cdt.enterprise_id,cdt.proxy_id,cdt.rpt_date
		,IFNULL(cd.expense_sum,0) expense_sum
		,IFNULL(cd.cost_sum,0) cost_sum
		,IFNULL(cd.expense_sum,0) - IFNULL(cd.cost_sum,0) + IFNULL(cd.rebate_sum,0)  profit_sum
		,NOW() create_date
		,IFNULL(cd.rebate_sum,0) rebate_sum
		FROM 
		(
			SELECT c.enterprise_id,c.top_proxy_id proxy_id,dt.rpt_date
			FROM ( SELECT * FROM t_flow_enterprise e  
				WHERE enterprise_id = p_enterprise_id 
				and EXISTS(SELECT * FROM t_flow_proxy p WHERE proxy_type = 1 AND e.`top_proxy_id` = p.`proxy_id`)) c,(
				SELECT DATE_ADD(p_start_time, INTERVAL + id - 1 DAY) rpt_date
				FROM t_flow_sys_id 
				WHERE id <= DATEDIFF(p_end_time,p_start_time)
			) dt
		) cdt LEFT JOIN
		(
			SELECT o.enterprise_id
			,MAX(o.proxy_id) proxy_id
			,DATE_FORMAT(o.order_date,'%Y-%m-%d') rpt_date
			,SUM(o.discount_price) expense_sum
			,SUM(ROUND(o.price*o.top_discount,3)) cost_sum	
			,NOW() create_date
			,SUM(ROUND(o.price*o.top_rebate_discount,3)) rebate_sum
			FROM (
				SELECT enterprise_id,proxy_id,order_date,discount_price,one_proxy_discount
				,CASE WHEN order_status = 2 THEN top_discount ELSE back_top_discount END top_discount
				,price
				,CASE order_status WHEN 2 THEN top_rebate_discount WHEN 5 THEN back_top_rebate_discount END top_rebate_discount 
				FROM t_flow_order
				WHERE order_status IN (2,5)
				AND user_type = 2
				and enterprise_id = p_enterprise_id
				AND order_date BETWEEN p_start_time AND p_end_time
			) o
			GROUP BY o.enterprise_id,DATE_FORMAT(o.order_date,'%Y-%m-%d')
		) cd
		ON cdt.enterprise_id = cd.enterprise_id AND cdt.rpt_date = cd.rpt_date
		ORDER BY cdt.enterprise_id,cdt.rpt_date;	
		
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK;  
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT;  
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_enterprise_account
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_enterprise_account`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_enterprise_account`(
	IN p_start_time DATETIME
)
BEGIN 
	/*
	author		lk
	date		2016/10/20
	desc		企业账户信息日记录(快照)
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_enterprise_account','企业账户信息日记录',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_enterprise_account_his WHERE  record_day = DATE_FORMAT(p_start_time,'%Y%m%d') ;
	
		#新增初始数据
		INSERT INTO t_flow_enterprise_account_his(account_id,enterprise_id,account_balance,freeze_money,credit_money
		,credit_freeze_money,create_user_id,create_date,modify_user_id,modify_date,cache_credit
		,new_quota_remind,channel_cache_credit,record_day )
 		SELECT 
		a.account_id account_id,
		a.enterprise_id enterprise_id,
		a.account_balance account_balance,
		a.freeze_money freeze_money,
		a.credit_money credit_money,
		a.credit_freeze_money credit_freeze_money,
		a.create_user_id create_user_id,
		a.create_date create_date,
		a.modify_user_id modify_user_id,
		a.modify_date modify_date,
		a.cache_credit cache_credit,
		a.new_quota_remind new_quota_remind,
		a.channel_cache_credit channel_cache_credit,
 		DATE_FORMAT(p_start_time,'%Y%m%d') record_day
		FROM t_flow_enterprise_account AS a ;
	
	IF t_error = 1 THEN 
		
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_enterprise
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_enterprise`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_enterprise`(
	IN p_start_time DATETIME
	,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		lk
	date		2016/08/04
	desc		企业首页统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	DECLARE v_begin_date DATETIME;
	DECLARE v_end_date DATETIME;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#SET v_curdate = DATE_ADD( CURDATE(), INTERVAL -1 DAY) ;
	#SET v_begin_date = DATE_FORMAT(v_curdate,'%Y-%m-%d 00:00:00');
	#SET v_end_date = DATE_FORMAT(v_curdate,'%Y-%m-%d 23:59:59.999999');
	SET v_begin_date = p_start_time;
	SET v_end_date = p_end_time;
	#SELECT v_begin_date,v_end_date,p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_enterprise','企业首页统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_enterprise WHERE stat_type=1 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		INSERT INTO t_flow_stat_enterprise(stat_type,stat_status,operator_id,enterprise_id,enterprise_code,enterprise_name,province_id,province_name,
		stat_year,stat_month,stat_day,stat_size,stat_price,create_date,stat_count)
		SELECT 
		1 stat_type,
		205 stat_status,
		o.operator_id operator_id,
		o.enterprise_id enterprise_id,
		en.enterprise_code enterprise_code,
		en.enterprise_name enterprise_name,
		o.province_id province_id,
		MAX(o.province_name) province_name,
		MAX(DATE_FORMAT(o.complete_time,'%Y')) stat_year,
		MAX(DATE_FORMAT(o.complete_time,'%Y%m')) stat_month,
		DATE_FORMAT(o.complete_time,'%Y%m%d') stat_day,
		SUM(cp.size) stat_size,
		SUM(o.discount_price),
		NOW() create_date,
		count(*) stat_count
		FROM t_flow_order AS o 
		INNER JOIN t_flow_channel_product AS cp ON cp.product_id=o.success_channel_product_id  
		LEFT JOIN t_flow_enterprise AS en ON en.enterprise_id= o.enterprise_id
		WHERE o.complete_time BETWEEN v_begin_date AND v_end_date  
		AND o.user_type=2 AND o.order_status IN (2,5)
		GROUP BY o.operator_id ,o.enterprise_id,en.enterprise_code,en.enterprise_name,o.province_id,DATE_FORMAT(o.complete_time,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_channel
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_channel`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_channel`(IN p_start_time DATETIME
	,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/19
	desc		通道利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_channel','通道利润汇总统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_channel WHERE stat_type=3 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_channel ( stat_type,stat_status,operator_id,channel_id,channel_code,channel_name,province_id
		,province_name,stat_year,stat_month,stat_day,stat_count,profit_price,create_date)
		SELECT
		3 stat_type,
		205 stat_status,
		a.operator_id operator_id,
		a.channel_id  channel_id,
		MAX(a.channel_code)  channel_code,
		MAX(a.channel_name)  channel_name,
		a.province_id province_id,
		MAX(a.province_name) province_name,
		MAX(a.stat_year) stat_year,
		MAX(a.stat_month) stat_month,
		a.stat_day stat_day,
		SUM(a.stat_count) stat_count,
		SUM(a.profit_price),
		NOW() create_date
		FROM (
			SELECT channel_id,channel_code,channel_name,operator_id,province_id,province_name,stat_count,stat_year,stat_month,stat_day,profit_price 
			FROM t_flow_stat_enterprise 
			WHERE stat_type=3 AND stat_status =205 AND  stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d')
			UNION ALL
			SELECT channel_id,channel_code,channel_name,operator_id,province_id,province_name,stat_count,stat_year,stat_month,stat_day ,profit_price
			FROM t_flow_stat_proxy 
			WHERE stat_type=3 AND stat_status =205 AND  stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d')
		)a
		GROUP BY a.channel_id,a.province_id,a.operator_id,a.stat_day;
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_channel2
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_channel2`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_channel2`(IN p_channel_id BIGINT,IN p_start_time DATETIME
	,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/19
	desc		通道利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_channel2','通道利润汇总统计数据(手动刷)',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_channel WHERE channel_id = p_channel_id AND stat_type=3 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_channel ( stat_type,stat_status,operator_id,channel_id,channel_code,channel_name,province_id
		,province_name,stat_year,stat_month,stat_day,stat_count,profit_price,create_date)
		SELECT
		3 stat_type,
		205 stat_status,
		a.operator_id operator_id,
		a.channel_id  channel_id,
		MAX(a.channel_code)  channel_code,
		MAX(a.channel_name)  channel_name,
		a.province_id province_id,
		MAX(a.province_name) province_name,
		MAX(a.stat_year) stat_year,
		MAX(a.stat_month) stat_month,
		a.stat_day stat_day,
		SUM(a.stat_count) stat_count,
		SUM(a.profit_price),
		NOW() create_date
		
		FROM (
			SELECT channel_id,channel_code,channel_name,operator_id,province_id,province_name,stat_count,stat_year,stat_month,stat_day,profit_price 
			FROM t_flow_stat_enterprise 
			WHERE  channel_id = p_channel_id and stat_type=3 AND stat_status =205 AND  stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d')
			UNION ALL
			SELECT channel_id,channel_code,channel_name,operator_id,province_id,province_name,stat_count,stat_year,stat_month,stat_day ,profit_price
			FROM t_flow_stat_proxy 
			WHERE  channel_id = p_channel_id and stat_type=3 AND stat_status =205 AND  stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d')
		)a
		GROUP BY a.channel_id,a.province_id,a.operator_id,a.stat_day;
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_enterprise
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_enterprise`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_enterprise`(IN p_start_time DATETIME
	,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/19
	desc		企业利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#SELECT p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_enterprise','企业利润汇总统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_enterprise WHERE stat_type=3 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_enterprise ( stat_type,stat_status,operator_id,enterprise_id,enterprise_code,enterprise_name,province_id,
		province_name,stat_year,stat_month,stat_day,stat_price,stat_count,channel_id,channel_name,channel_code,
		top_discount,discount_price,top_price,profit_price,rebate_price,create_date)
		SELECT
		3 stat_type,
		205 stat_status,
		b.operator_id operator_id,
		b.enterprise_id enterprise_id,
		MAX(b.enterprise_code) enterprise_code,
		MAX(b.enterprise_name) enterprise_name,
		b.province_id province_id,
		MAX(b.province_name) province_name,
		MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
		MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
		DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
		SUM(b.price) stat_price,
		COUNT(*) stat_count,
		a.channel_id channel_id,
		MAX(a.channel_name) channel_name,
		MAX(a.channel_code) channel_code,
		MAX(b.top_discount) top_discount,#成本折扣=系统通道成本折扣数-返利折扣数
		SUM(b.discount_price) discount_price,
		SUM(ROUND((b.top_discount)*b.price,3)) top_price,#成本总额=原价金额*(系统通道成本折扣数-返利折扣数)
		SUM(b.discount_price)-SUM(ROUND(b.top_discount*b.price,3)) profit_price,
		SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price,
		NOW() create_date
		FROM t_flow_channel a
		INNER JOIN (
		SELECT o.enterprise_id,en.enterprise_code,o.enterprise_name,o.channel_id,o.channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.one_proxy_name,o.top_discount,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_enterprise en ON o.enterprise_id = en.enterprise_id
		WHERE o.order_status = 2
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 2
		UNION ALL
		SELECT o.enterprise_id,en.enterprise_code,o.enterprise_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.one_proxy_name,o.back_top_discount top_discount,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_enterprise en ON o.enterprise_id = en.enterprise_id
		WHERE o.order_status = 5
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 2
		) b ON a.channel_id = b.channel_id
		GROUP BY b.enterprise_id,a.channel_id,b.province_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_enterprise2
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_enterprise2`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_enterprise2`(IN p_channel_id BIGINT
	,IN p_start_time DATETIME
	,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/19
	desc		企业利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#SELECT p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_enterprise2','企业利润汇总统计数据(手动刷)',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_enterprise WHERE channel_id = p_channel_id and stat_type=3 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_enterprise ( stat_type,stat_status,operator_id,enterprise_id,enterprise_code,enterprise_name,province_id,
		province_name,stat_year,stat_month,stat_day,stat_price,stat_count,channel_id,channel_name,channel_code,
		top_discount,discount_price,top_price,profit_price,rebate_price,create_date)
		SELECT
		3 stat_type,
		205 stat_status,
		b.operator_id operator_id,
		b.enterprise_id enterprise_id,
		MAX(b.enterprise_code) enterprise_code,
		MAX(b.enterprise_name) enterprise_name,
		b.province_id province_id,
		MAX(b.province_name) province_name,
		MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
		MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
		DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
		SUM(b.price) stat_price,
		COUNT(*) stat_count,
		a.channel_id channel_id,
		MAX(a.channel_name) channel_name,
		MAX(a.channel_code) channel_code,
		MAX(b.top_discount) top_discount,#成本折扣=系统通道成本折扣数-返利折扣数
		SUM(b.discount_price) discount_price,
		SUM(ROUND((b.top_discount)*b.price,3)) top_price,#成本总额=原价金额*(系统通道成本折扣数-返利折扣数)
		SUM(b.discount_price)-SUM(ROUND(b.top_discount*b.price,3)) profit_price,
		SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price,
		NOW() create_date
		FROM t_flow_channel a
		INNER JOIN (
		SELECT o.enterprise_id,en.enterprise_code,o.enterprise_name,o.channel_id,o.channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.one_proxy_name,o.top_discount,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_enterprise en ON o.enterprise_id = en.enterprise_id
		WHERE o.order_status = 2
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 2
		and o.channel_id = p_channel_id
		UNION ALL
		SELECT o.enterprise_id,en.enterprise_code,o.enterprise_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.one_proxy_name,o.back_top_discount top_discount,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_enterprise en ON o.enterprise_id = en.enterprise_id
		WHERE o.order_status = 5
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 2
		and o.back_channel_id = p_channel_id
		) b ON a.channel_id = b.channel_id
		GROUP BY b.enterprise_id,a.channel_id,b.province_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_one_proxy
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_one_proxy`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_one_proxy`(IN p_start_time DATETIME
	,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/29
	desc		一级代理利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#SELECT p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_one_proxy','一级代理利润汇总统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_proxy WHERE stat_type=4 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_proxy ( stat_type,stat_status,operator_id,proxy_id,proxy_code,proxy_name,province_id,
		province_name,stat_year,stat_month,stat_day,stat_price,stat_count,channel_id,channel_name,channel_code,
		sale_discount,top_discount,discount_price,profit_price,top_price,rebate_price,create_date)
		SELECT
		4 stat_type,
		205 stat_status,
		b.operator_id operator_id,
		b.one_proxy_id proxy_id,
		MAX(b.proxy_code) proxy_code,
		MAX(b.one_proxy_name) proxy_name,
		b.province_id province_id,
		MAX(b.province_name) province_name,
		MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
		MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
		DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
		SUM(b.price) stat_price,
		COUNT(*) stat_count,
		a.channel_id channel_id,
		MAX(a.channel_name) channel_name,
		MAX(a.channel_code) channel_code,
		MAX(b.one_proxy_discount)  sale_discount,
		MAX(b.top_discount) top_discount,
		SUM(ROUND(b.one_proxy_discount*b.price,3)) discount_price,
		SUM(ROUND(b.one_proxy_discount*b.price,3)-ROUND(b.top_discount*b.price,3)) profit_price,
		SUM(ROUND(b.top_discount*b.price,3)) top_price,
		SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price,
		NOW() create_date
		FROM t_flow_channel a
		INNER JOIN (
		SELECT o.one_proxy_id,en.proxy_code,o.one_proxy_name,o.channel_id,o.channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.top_discount,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.one_proxy_id = en.proxy_id
		WHERE o.order_status = 2
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 2
		#AND EXISTS(SELECT * FROM t_flow_enterprise e,t_flow_proxy p WHERE e.`top_proxy_id` = p.`proxy_id` AND p.`proxy_type` = 1 AND o.enterprise_id = e.enterprise_id)
		UNION ALL
		SELECT o.one_proxy_id,en.proxy_code,o.one_proxy_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.back_top_discount top_discount,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.one_proxy_id = en.proxy_id
		WHERE o.order_status = 5
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 2
		#AND EXISTS(SELECT * FROM t_flow_enterprise e,t_flow_proxy p WHERE e.`top_proxy_id` = p.`proxy_id` AND p.`proxy_type` = 1 AND o.enterprise_id = e.enterprise_id)
		UNION ALL 
		SELECT o.one_proxy_id,en.proxy_code,o.one_proxy_name,o.channel_id,o.channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.top_discount,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.one_proxy_id = en.proxy_id
		WHERE o.order_status = 2
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 1
		AND EXISTS(SELECT * FROM t_flow_proxy p WHERE p.proxy_level  = 1 AND p.proxy_type <> 1 AND p.approve_status = 1 AND p.status = 1 AND o.one_proxy_id = p.proxy_id)
		UNION ALL
		SELECT o.one_proxy_id,en.proxy_code,o.one_proxy_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.back_top_discount top_discount,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.one_proxy_id = en.proxy_id
		WHERE o.order_status = 5
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 1
		AND EXISTS(SELECT * FROM t_flow_proxy p WHERE p.proxy_level  = 1 AND p.proxy_type <> 1 AND p.approve_status = 1 AND p.status = 1 AND o.one_proxy_id = p.proxy_id)
		) b ON a.channel_id = b.channel_id
		#left join t_flow_channel_discount c on b.channel_id = c.channel_id   and b.operator_id = c.operator_id	
		WHERE a.status = 1
		GROUP BY b.one_proxy_id,a.channel_id,b.province_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_one_proxy2
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_one_proxy2`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_one_proxy2`(
	IN p_channel_id BIGINT
	,IN p_start_time DATETIME
	,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		lk
	date		2016/08/29
	desc		一级代理利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#SELECT p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_one_proxy2','一级代理利润汇总统计数据（手动刷）',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_proxy WHERE channel_id = p_channel_id AND stat_type=4 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_proxy ( stat_type,stat_status,operator_id,proxy_id,proxy_code,proxy_name,province_id,
		province_name,stat_year,stat_month,stat_day,stat_price,stat_count,channel_id,channel_name,channel_code,
		sale_discount,top_discount,discount_price,profit_price,top_price,rebate_price,create_date)
		SELECT
		4 stat_type,
		205 stat_status,
		b.operator_id operator_id,
		b.one_proxy_id proxy_id,
		MAX(b.proxy_code) proxy_code,
		MAX(b.one_proxy_name) proxy_name,
		b.province_id province_id,
		MAX(b.province_name) province_name,
		MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
		MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
		DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
		SUM(b.price) stat_price,
		COUNT(*) stat_count,
		a.channel_id channel_id,
		MAX(a.channel_name) channel_name,
		MAX(a.channel_code) channel_code,
		MAX(b.one_proxy_discount)  sale_discount,
		MAX(b.top_discount) top_discount,
		SUM(ROUND(b.one_proxy_discount*b.price,3)) discount_price,
		SUM(ROUND(b.one_proxy_discount*b.price,3)-ROUND(b.top_discount*b.price,3)) profit_price,
		SUM(ROUND(b.top_discount*b.price,3)) top_price,
		SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price,
		NOW() create_date
		FROM t_flow_channel a
		INNER JOIN (
		SELECT o.one_proxy_id,en.proxy_code,o.one_proxy_name,o.channel_id,o.channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.top_discount,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.one_proxy_id = en.proxy_id
		WHERE o.order_status = 2
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 2
		and o.channel_id = p_channel_id
		#AND EXISTS(SELECT * FROM t_flow_enterprise e,t_flow_proxy p WHERE e.`top_proxy_id` = p.`proxy_id` AND p.`proxy_type` = 1 AND o.enterprise_id = e.enterprise_id)
		UNION ALL
		SELECT o.one_proxy_id,en.proxy_code,o.one_proxy_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.back_top_discount top_discount,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.one_proxy_id = en.proxy_id
		WHERE o.order_status = 5
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 2
		and o.back_channel_id = p_channel_id
		#AND EXISTS(SELECT * FROM t_flow_enterprise e,t_flow_proxy p WHERE e.`top_proxy_id` = p.`proxy_id` AND p.`proxy_type` = 1 AND o.enterprise_id = e.enterprise_id)
		UNION ALL 
		SELECT o.one_proxy_id,en.proxy_code,o.one_proxy_name,o.channel_id,o.channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.top_discount,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.one_proxy_id = en.proxy_id
		WHERE o.order_status = 2
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 1
		and o.channel_id = p_channel_id
		AND EXISTS(SELECT * FROM t_flow_proxy p WHERE p.proxy_level  = 1 AND p.proxy_type <> 1 AND p.approve_status = 1 AND p.status = 1 AND o.one_proxy_id = p.proxy_id)
		UNION ALL
		SELECT o.one_proxy_id,en.proxy_code,o.one_proxy_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.back_top_discount top_discount,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.one_proxy_id = en.proxy_id
		WHERE o.order_status = 5
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 1
		and o.back_channel_id = p_channel_id
		AND EXISTS(SELECT * FROM t_flow_proxy p WHERE p.proxy_level  = 1 AND p.proxy_type <> 1 AND p.approve_status = 1 AND p.status = 1 AND o.one_proxy_id = p.proxy_id)
		) b ON a.channel_id = b.channel_id
		#left join t_flow_channel_discount c on b.channel_id = c.channel_id   and b.operator_id = c.operator_id	
		WHERE a.status = 1
		GROUP BY b.one_proxy_id,a.channel_id,b.province_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_product
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_product`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_product`(IN p_start_time DATETIME
  ,IN p_end_time DATETIME)
BEGIN 
  /*
  author    lk
  date    2016/08/22
  desc    产品利润汇总统计数据
  */
  #事务处理标志
  DECLARE t_error INTEGER DEFAULT 0; 
  DECLARE v_log_id BIGINT;
  DECLARE v_begin_date DATETIME;
  DECLARE v_end_date DATETIME;
  #申明事务处理错误标志
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
  
  SET v_begin_date = p_start_time;
  SET v_end_date = p_end_time;
  #SELECT v_begin_date,v_end_date,p_start_time,p_end_time;
  #开始时间
  INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
  VALUES (1,'p_auto_stat_order_profit_product','产品利润汇总统计数据',1,NOW(6),NULL);
  SET v_log_id = @@IDENTITY;
  
  
  #开始事务
  START TRANSACTION;
    #删除旧有初始数据
    DELETE FROM t_flow_stat_product WHERE stat_type IN(1,2) AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
    #新增初始数据
    
    INSERT INTO t_flow_stat_product (
    stat_type,stat_status,product_id,product_name,channel_id,channel_code,channel_name,user_id,
    user_name,stat_year,stat_month,stat_day,stat_price,stat_count,top_discount,discount_price,
    operator_id,top_price,rebate_price,one_proxy_id,one_proxy_name,sale_discount,sale_price,create_date
    )
    SELECT  
    MAX(b.stat_type) stat_type,
    205 stat_status,
    b.product_id product_id,
    b.product_name product_name,
    a.channel_id channel_id,
    MAX(a.channel_code) channel_code,
    MAX(a.channel_name) channel_name,
    b.user_id user_id,
    MAX(b.user_name) user_name,
    MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
    MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
    DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
    SUM(b.price) stat_price,
    COUNT(*) stat_count,
    MAX(b.top_discount) top_discount,
    SUM(b.discount_price) discount_price,
    b.operator_id operator_id,
    SUM(ROUND(b.top_discount*b.price,3)) top_price,
    SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price,
    MAX(b.one_proxy_id) one_proxy_id,
    MAX(b.one_proxy_name) one_proxy_name,
    MAX(b.one_proxy_discount) sale_discount,
    SUM(ROUND(b.one_proxy_discount*b.price,3)) sale_price,
    NOW() create_date
    FROM t_flow_channel a
    INNER JOIN (
    SELECT 2 stat_type ,o.enterprise_id user_id,o.enterprise_name user_name,o.channel_id,o.channel_code,o.price,o.discount_price,
           o.operator_id,o.top_discount,o.channel_product_id product_id,o.product_name,o.top_rebate_discount,
           o.order_date,o.one_proxy_id,o.one_proxy_name,o.one_proxy_discount
    FROM t_flow_order o
    WHERE o.order_status = 2
    AND o.order_date BETWEEN v_begin_date AND v_end_date
    AND o.user_type = 2
    UNION ALL
    SELECT 2 stat_type,o.enterprise_id user_id,o.enterprise_name user_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,
           o.operator_id,o.back_top_discount top_discount,o.back_channel_product_id product_id,o.product_name,o.back_top_rebate_discount top_rebate_discount,
           o.order_date,o.one_proxy_id,o.one_proxy_name,o.one_proxy_discount
    FROM t_flow_order o
    WHERE o.order_status = 5
    AND o.order_date BETWEEN v_begin_date AND v_end_date
    AND o.user_type = 2
    UNION ALL
    SELECT 1 stat_type,o.proxy_id user_id,o.proxy_name user_name,o.channel_id,o.channel_code,o.price,o.discount_price,
           o.operator_id,o.top_discount,o.channel_product_id product_id,o.product_name,o.top_rebate_discount,
           o.order_date,o.one_proxy_id,o.one_proxy_name,o.one_proxy_discount
    FROM t_flow_order o
    WHERE o.order_status = 2
    AND o.order_date BETWEEN v_begin_date AND v_end_date
    AND o.user_type = 1
    UNION ALL
    SELECT 1 stat_type, o.proxy_id user_id,o.proxy_name user_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,
           o.operator_id,o.back_top_discount top_discount,o.back_channel_product_id product_id,o.product_name,o.back_top_rebate_discount top_rebate_discount,
           o.order_date,o.one_proxy_id,o.one_proxy_name,o.one_proxy_discount
    FROM t_flow_order o
    WHERE o.order_status = 5
    AND o.order_date BETWEEN v_begin_date AND v_end_date
    AND o.user_type = 1
    ) b ON a.channel_id = b.channel_id
    GROUP BY b.product_id,b.product_name,a.channel_id,b.user_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
  IF t_error = 1 THEN 
    #数据回滚     
    ROLLBACK; 
    #失败时间和状态
    UPDATE t_flow_sys_event_log
    SET log_status = 0
    ,end_time = NOW(6)
    WHERE log_id = v_log_id;
  ELSE    
    #提交
    COMMIT; 
    #更新结束时间
    UPDATE t_flow_sys_event_log
    SET end_time = NOW(6)
    WHERE log_id = v_log_id;
  END IF;
  
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_product2
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_product2`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_product2`(IN p_channel_id BIGINT
	,IN p_start_time DATETIME
	,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/22
	desc		产品利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	DECLARE v_begin_date DATETIME;
	DECLARE v_end_date DATETIME;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_begin_date = p_start_time;
	SET v_end_date = p_end_time;
	#SELECT v_begin_date,v_end_date,p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_product2','产品利润汇总统计数据(手动刷)',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_product WHERE channel_id = p_channel_id AND stat_type IN(1,2) AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_product (
		stat_type,stat_status,product_id,product_name,channel_id,channel_code,channel_name,user_id,
		user_name,stat_year,stat_month,stat_day,stat_price,stat_count,top_discount,discount_price,
		operator_id,top_price,rebate_price,create_date
		)
		SELECT  
		MAX(b.stat_type) stat_type,
		205 stat_status,
		b.product_id product_id,
		b.product_name product_name,
		a.channel_id channel_id,
		MAX(a.channel_code) channel_code,
		MAX(a.channel_name) channel_name,
		b.user_id user_id,
		MAX(b.user_name) user_name,
		MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
		MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
		DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
		SUM(b.price) stat_price,
		COUNT(*) stat_count,
		MAX(b.top_discount) top_discount,
		SUM(b.discount_price) discount_price,
		b.operator_id operator_id,
		SUM(ROUND(b.top_discount*b.price,3)) top_price,
		SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price,
		NOW() create_date
		FROM t_flow_channel a
		INNER JOIN (
		SELECT 2 stat_type ,o.enterprise_id user_id,o.enterprise_name user_name,o.channel_id,o.channel_code,o.price,o.discount_price,
					 o.operator_id,o.top_discount,o.channel_product_id product_id,o.product_name,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		WHERE o.order_status = 2
		AND o.order_date BETWEEN v_begin_date AND v_end_date
		AND o.user_type = 2
		and o.channel_id = p_channel_id
		UNION ALL
		SELECT 2 stat_type,o.enterprise_id user_id,o.enterprise_name user_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,
					 o.operator_id,o.back_top_discount top_discount,o.back_channel_product_id product_id,o.product_name,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		WHERE o.order_status = 5
		AND o.order_date BETWEEN v_begin_date AND v_end_date
		AND o.user_type = 2
		and o.back_channel_id = p_channel_id
		UNION ALL
		SELECT 1 stat_type,o.proxy_id user_id,o.proxy_name user_name,o.channel_id,o.channel_code,o.price,o.discount_price,
					 o.operator_id,o.top_discount,o.channel_product_id product_id,o.product_name,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		WHERE o.order_status = 2
		AND o.order_date BETWEEN v_begin_date AND v_end_date
		AND o.user_type = 1
		and o.channel_id = p_channel_id
		UNION ALL
		SELECT 1 stat_type, o.proxy_id user_id,o.proxy_name user_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,
					 o.operator_id,o.back_top_discount top_discount,o.back_channel_product_id product_id,o.product_name,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		WHERE o.order_status = 5
		AND o.order_date BETWEEN v_begin_date AND v_end_date
		AND o.user_type = 1
		and o.back_channel_id = p_channel_id
		) b ON a.channel_id = b.channel_id
		GROUP BY b.product_id,b.product_name,a.channel_id,b.user_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_product_20161108
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_product_20161108`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_product_20161108`(
	IN p_start_time DATETIME
	,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		lk
	date		2016/08/22
	desc		产品利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	DECLARE v_begin_date DATETIME;
	DECLARE v_end_date DATETIME;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_begin_date = p_start_time;
	SET v_end_date = p_end_time;
	#SELECT v_begin_date,v_end_date,p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_product','产品利润汇总统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_product WHERE stat_type IN(1,2) AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_product (
		stat_type,stat_status,product_id,product_name,channel_id,channel_code,channel_name,user_id,
		user_name,stat_year,stat_month,stat_day,stat_price,stat_count,top_discount,discount_price,
		operator_id,top_price,rebate_price
		)
		SELECT  
		MAX(b.stat_type) stat_type,
		205 stat_status,
		b.product_id product_id,
		b.product_name product_name,
		a.channel_id channel_id,
		MAX(a.channel_code) channel_code,
		MAX(a.channel_name) channel_name,
		b.user_id user_id,
		MAX(b.user_name) user_name,
		MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
		MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
		DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
		SUM(b.price) stat_price,
		COUNT(*) stat_count,
		MAX(b.top_discount) top_discount,
		SUM(b.discount_price) discount_price,
		b.operator_id operator_id,
		SUM(ROUND(b.top_discount*b.price,3)) top_price,
		SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price
		FROM t_flow_channel a
		INNER JOIN (
		SELECT 2 stat_type ,o.enterprise_id user_id,o.enterprise_name user_name,o.channel_id,o.channel_code,o.price,o.discount_price,
					 o.operator_id,o.top_discount,o.channel_product_id product_id,o.product_name,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		WHERE o.order_status = 2
		AND o.order_date BETWEEN v_begin_date AND v_end_date
		AND o.user_type = 2
		UNION ALL
		SELECT 2 stat_type,o.enterprise_id user_id,o.enterprise_name user_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,
					 o.operator_id,o.back_top_discount top_discount,o.back_channel_product_id product_id,o.product_name,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		WHERE o.order_status = 5
		AND o.order_date BETWEEN v_begin_date AND v_end_date
		AND o.user_type = 2
		UNION ALL
		SELECT 1 stat_type,o.proxy_id user_id,o.proxy_name user_name,o.channel_id,o.channel_code,o.price,o.discount_price,
					 o.operator_id,o.top_discount,o.channel_product_id product_id,o.product_name,o.top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		WHERE o.order_status = 2
		AND o.order_date BETWEEN v_begin_date AND v_end_date
		AND o.user_type = 1
		UNION ALL
		SELECT 1 stat_type, o.proxy_id user_id,o.proxy_name user_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,
					 o.operator_id,o.back_top_discount top_discount,o.back_channel_product_id product_id,o.product_name,o.back_top_rebate_discount top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		WHERE o.order_status = 5
		AND o.order_date BETWEEN v_begin_date AND v_end_date
		AND o.user_type = 1
		) b ON a.channel_id = b.channel_id
		GROUP BY b.product_id,b.product_name,a.channel_id,b.user_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_proxy
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_proxy`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_proxy`(IN p_start_time DATETIME
	,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/19
	desc		代理利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	#SELECT p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_proxy','代理利润汇总统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_proxy WHERE stat_type=3 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_proxy ( stat_type,stat_status,operator_id,proxy_id,proxy_code,proxy_name,province_id,
		province_name,stat_year,stat_month,stat_day,stat_price,stat_count,channel_id,channel_name,channel_code,
		top_discount,discount_price,top_price,profit_price,rebate_price,create_date)
		SELECT
		3 stat_type,
		205 stat_status,
		b.operator_id operator_id,
		b.proxy_id proxy_id,
		MAX(b.proxy_code) proxy_code,
		MAX(b.proxy_name) proxy_name,
		b.province_id province_id,
		MAX(b.province_name) province_name,
		MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
		MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
		DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
		SUM(b.price) stat_price,
		COUNT(*) stat_count,
		a.channel_id channel_id,
		MAX(a.channel_name) channel_name,
		MAX(a.channel_code) channel_code,
		MAX(b.top_discount) top_discount,
		SUM(b.discount_price) discount_price,
		SUM(ROUND(b.top_discount*b.price,3)) top_price,
		SUM(ROUND(b.one_proxy_discount*b.price,3))-SUM(ROUND(b.top_discount*b.price,3)) profit_price,
		SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price,
		NOW() create_date
		FROM t_flow_channel a
		INNER JOIN (
		SELECT o.proxy_id,en.proxy_code,o.proxy_name,o.channel_id,o.channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.one_proxy_name,o.top_discount,top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.proxy_id = en.proxy_id
		WHERE o.order_status = 2
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 1
		UNION ALL
		SELECT o.proxy_id,en.proxy_code,o.proxy_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.one_proxy_name,o.back_top_discount top_discount,top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.proxy_id = en.proxy_id
		WHERE o.order_status = 5
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 1
		) b ON a.channel_id = b.channel_id
		GROUP BY b.proxy_id,a.channel_id,b.province_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_profit_proxy2
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_profit_proxy2`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_profit_proxy2`(
	IN p_channel_id BIGINT
	,IN p_start_time DATETIME
	,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/19
	desc		代理利润汇总统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	#SELECT p_start_time,p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_profit_proxy2','代理利润汇总统计数据（手动刷）',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_proxy WHERE  channel_id = p_channel_id AND stat_type=3 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		
		INSERT INTO t_flow_stat_proxy ( stat_type,stat_status,operator_id,proxy_id,proxy_code,proxy_name,province_id,
		province_name,stat_year,stat_month,stat_day,stat_price,stat_count,channel_id,channel_name,channel_code,
		top_discount,discount_price,top_price,profit_price,rebate_price,create_date)
		SELECT
		3 stat_type,
		205 stat_status,
		b.operator_id operator_id,
		b.proxy_id proxy_id,
		MAX(b.proxy_code) proxy_code,
		MAX(b.proxy_name) proxy_name,
		b.province_id province_id,
		MAX(b.province_name) province_name,
		MAX(DATE_FORMAT(b.order_date,'%Y')) stat_year,
		MAX(DATE_FORMAT(b.order_date,'%Y%m')) stat_month,
		DATE_FORMAT(b.order_date,'%Y%m%d') stat_day,
		SUM(b.price) stat_price,
		COUNT(*) stat_count,
		a.channel_id channel_id,
		MAX(a.channel_name) channel_name,
		MAX(a.channel_code) channel_code,
		MAX(b.top_discount) top_discount,
		SUM(b.discount_price) discount_price,
		SUM(ROUND(b.top_discount*b.price,3)) top_price,
		SUM(ROUND(b.one_proxy_discount*b.price,3))-SUM(ROUND(b.top_discount*b.price,3)) profit_price,
		SUM(ROUND(IFNULL(b.top_rebate_discount,0)*b.price,3)) rebate_price,
		NOW() create_date
		FROM t_flow_channel a
		INNER JOIN (
		SELECT o.proxy_id,en.proxy_code,o.proxy_name,o.channel_id,o.channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.one_proxy_name,o.top_discount,top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.proxy_id = en.proxy_id
		WHERE o.order_status = 2
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 1
		and o.channel_id = p_channel_id
		UNION ALL
		SELECT o.proxy_id,en.proxy_code,o.proxy_name,o.back_channel_id channel_id,o.back_channel_code channel_code,o.price,o.discount_price,one_proxy_discount,
					 o.operator_id,o.province_id,o.province_name,o.one_proxy_name,o.back_top_discount top_discount,top_rebate_discount,
					 o.order_date
		FROM t_flow_order o
		LEFT JOIN t_flow_proxy en ON o.proxy_id = en.proxy_id
		WHERE o.order_status = 5
		AND o.order_date BETWEEN p_start_time AND p_end_time
		AND o.user_type = 1
		and o.back_channel_id = p_channel_id
		) b ON a.channel_id = b.channel_id
		GROUP BY b.proxy_id,a.channel_id,b.province_id,b.operator_id,DATE_FORMAT(b.order_date,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_order_proxy
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_order_proxy`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_order_proxy`(
	IN p_start_time DATETIME
	,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		lk
	date		2016/08/04
	desc		代理首页统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	DECLARE v_begin_date DATETIME;
	DECLARE v_end_date DATETIME;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#SET v_curdate = DATE_ADD( CURDATE(), INTERVAL -1 DAY) ;
	#SET v_begin_date = DATE_FORMAT(v_curdate,'%Y-%m-%d 00:00:00');
	#SET v_end_date = DATE_FORMAT(v_curdate,'%Y-%m-%d 23:59:59.999999');
	SET v_begin_date = p_start_time;
	SET v_end_date = p_end_time;
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_order_proxy','代理首页统计数据',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_stat_proxy WHERE stat_type=1 AND stat_status =205 AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d') ;
		#新增初始数据
		INSERT INTO t_flow_stat_proxy(stat_type,stat_status,operator_id,proxy_id,proxy_code,proxy_name,province_id,province_name,
		stat_year,stat_month,stat_day,stat_size,stat_price,create_date,stat_count)	
		SELECT 
		1 stat_type,
		205 stat_status,
		o.operator_id operator_id,
		o.proxy_id proxy_id,
		pr.proxy_code proxy_code,
		pr.proxy_name proxy_name,
		o.province_id province_id,
		MAX(o.province_name) province_name,
		MAX(DATE_FORMAT(o.complete_time,'%Y')) stat_year,
		MAX(DATE_FORMAT(o.complete_time,'%Y%m')) stat_month,
		DATE_FORMAT(o.complete_time,'%Y%m%d') stat_day,
		SUM(cp.size) stat_size,
		SUM(o.discount_price),
		NOW() create_date,
		count(*) stat_count
		FROM t_flow_order AS o 
		INNER JOIN t_flow_channel_product AS cp ON cp.product_id=o.success_channel_product_id 
		LEFT JOIN t_flow_proxy AS pr ON pr.proxy_id = o.proxy_id 
		WHERE o.complete_time BETWEEN v_begin_date AND v_end_date  
		AND o.user_type=1 AND o.order_status IN (2,5)
		GROUP BY o.operator_id ,o.proxy_id,pr.proxy_code,pr.proxy_name,o.province_id,DATE_FORMAT(o.complete_time,'%Y%m%d');
	IF t_error = 1 THEN 
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_profit
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_profit`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_profit`(IN p_start_time DATETIME
  ,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/22
	desc		自动统计数据(05:00)
	*/
	CALL `p_auto_stat_order_profit_enterprise`(p_start_time,p_end_time);
	CALL `p_auto_stat_order_profit_proxy`(p_start_time,p_end_time);
	CALL `p_auto_stat_order_profit_product`(p_start_time,p_end_time);
	#备注：p_auto_stat_order_profit_channel必须在p_auto_stat_order_profit_enterprise和p_auto_stat_order_profit_proxy之后执行
	CALL `p_auto_stat_order_profit_channel`(p_start_time,p_end_time);
	CALL `p_auto_stat_order_profit_one_proxy`(p_start_time,p_end_time);
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_proxy
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_proxy`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_proxy`(
  IN p_start_time DATETIME
  ,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		cxw
	date		2016/05/25
	desc		处理成功订单
	收入金额	expense_sum	收入金额=单价*一级代理折扣
	成本金额	cost_sum	成本金额=单价*通道折扣
	利润金额	profit_sum	利润金额=收入-成本
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_proxy','代理商收入日统计',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#代理商日统计
		#先清空
		DELETE FROM t_flow_rpt_proxy WHERE rpt_date BETWEEN p_start_time AND p_end_time;
		#新增最新数据
		INSERT INTO t_flow_rpt_proxy(proxy_id,rpt_date,expense_sum,cost_sum,profit_sum,create_date,rebate_sum)
		SELECT cdt.proxy_id,cdt.rpt_date
		,IFNULL(cd.expense_sum,0) expense_sum
		,IFNULL(cd.cost_sum,0) cost_sum
		,IFNULL(cd.expense_sum,0) - IFNULL(cd.cost_sum,0)  + IFNULL(cd.rebate_sum,0)  profit_sum
		,NOW() create_date
		,IFNULL(cd.rebate_sum,0) rebate_sum
		FROM 
		(
			SELECT c.proxy_id,dt.rpt_date
			FROM (SELECT * FROM t_flow_proxy WHERE proxy_level  = 1 AND proxy_type <> 1) c,(
				SELECT DATE_ADD(p_start_time, INTERVAL + id - 1 DAY) rpt_date
				FROM t_flow_sys_id 
				WHERE id <= DATEDIFF(p_end_time,p_start_time)
			) dt
		) cdt LEFT JOIN
		(
			SELECT o.one_proxy_id proxy_id
			,DATE_FORMAT(o.order_date,'%Y-%m-%d') rpt_date
			,SUM(ROUND(o.price*o.one_proxy_discount,3)) expense_sum
			,SUM(ROUND(o.price*o.top_discount,3)) cost_sum	
			,NOW() create_date
			,SUM(ROUND(o.price*o.top_rebate_discount,3)) rebate_sum
			FROM (
				SELECT one_proxy_id, order_date,one_proxy_discount
				,CASE WHEN order_status = 2 THEN top_discount ELSE back_top_discount END top_discount
				,price
				,CASE order_status WHEN 2 THEN top_rebate_discount WHEN 5 THEN back_top_rebate_discount END top_rebate_discount 
				FROM t_flow_order 
				WHERE order_status IN (2,5)
				#AND o.user_type = 1
				AND order_date BETWEEN p_start_time AND p_end_time
			) o
			GROUP BY o.one_proxy_id,DATE_FORMAT(o.order_date,'%Y-%m-%d')
		) cd
		ON cdt.proxy_id = cd.proxy_id AND cdt.rpt_date = cd.rpt_date
		ORDER BY cdt.proxy_id,cdt.rpt_date;
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_proxy_account
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_proxy_account`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_proxy_account`(
	IN p_start_time DATETIME
)
BEGIN 
	/*
	author		lk
	date		2016/10/20
	desc		代理账户信息日记录(快照)
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_proxy_account','代理账户信息日记录',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	
	
	#开始事务
	START TRANSACTION;
		#删除旧有初始数据
		DELETE FROM t_flow_proxy_account_his WHERE  record_day = DATE_FORMAT(p_start_time,'%Y%m%d') ;
	
		#新增初始数据
		INSERT INTO t_flow_proxy_account_his(account_id,proxy_id,account_balance,freeze_money,credit_money
		,credit_freeze_money,create_user_id,create_date,modify_user_id,modify_date
		,cache_credit,new_quota_remind,channel_cache_credit,record_day )
 		SELECT 
		a.account_id account_id,
		a.proxy_id proxy_id,
		a.account_balance account_balance,
		a.freeze_money freeze_money,
		a.credit_money credit_money,
		a.credit_freeze_money credit_freeze_money,
		a.create_user_id create_user_id,
		a.create_date create_date,
		a.modify_user_id modify_user_id,
		a.modify_date modify_date,
		a.cache_credit cache_credit,
		a.new_quota_remind new_quota_remind,
		a.channel_cache_credit channel_cache_credit,
 		DATE_FORMAT(p_start_time,'%Y%m%d') record_day
		FROM t_flow_proxy_account AS a ;
	
	IF t_error = 1 THEN 
		
		#数据回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_proxy_consume
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_proxy_consume`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_proxy_consume`(
  IN p_start_time DATETIME
  ,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		cxw
	date		2016/08/08
	desc		代理流量消费统计
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title, log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_proxy_consume','代理流量消费统计',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#代理商日统计
		#先清空
		DELETE FROM t_flow_stat_proxy 
		WHERE stat_type = 2 
		AND stat_status = 205
		AND stat_day BETWEEN DATE_FORMAT(p_start_time,'%Y%m%d') AND DATE_FORMAT(p_end_time,'%Y%m%d');
		#新增最新数据
		INSERT INTO t_flow_stat_proxy(stat_type ,stat_status 
		,proxy_id,proxy_code,proxy_name
		,stat_year,stat_month,stat_day
		,stat_price,stat_count,stat_refund_price,create_date)
		SELECT 2 stat_type
		,205 stat_status
		,o.one_proxy_id proxy_id
		,MAX(p.proxy_code) proxy_code
		,MAX(p.proxy_name) proxy_name
		,MAX(DATE_FORMAT(o.complete_time,'%Y')) stat_year
		,MAX(DATE_FORMAT(o.complete_time,'%Y%m')) stat_month
		,DATE_FORMAT(o.complete_time,'%Y%m%d') stat_day
		,ROUND(SUM(o.price * o.one_proxy_discount),3) stat_price
		,COUNT(*) stat_count 
		,0 stat_refund_price,
		NOW() create_date
		FROM t_flow_order o 
		INNER JOIN t_flow_proxy p ON o.`one_proxy_id` = p.`proxy_id` 
		WHERE o.order_status IN (2,5) 
		AND o.complete_time BETWEEN p_start_time AND p_end_time
		GROUP BY o.`one_proxy_id`,DATE_FORMAT(o.complete_time,'%Y%m%d');
		
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_proxy_refund
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_proxy_refund`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_proxy_refund`(
  IN p_start_time DATETIME
  ,IN p_end_time DATETIME
)
BEGIN 
	/*
	author		cxw
	date		2016/08/08
	desc		代理充值退款统计
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	DECLARE v_done INT DEFAULT 0; 
	DECLARE v_stat_type INT;
	DECLARE v_stat_status INT;
	DECLARE v_proxy_id BIGINT;
	DECLARE v_proxy_code INT;
	DECLARE v_proxy_name VARCHAR(200);
	DECLARE v_stat_year INT;
	DECLARE v_stat_month INT;
	DECLARE v_stat_day INT;
	DECLARE v_stat_refund_price DECIMAL(11,3);
	DECLARE v_count INT DEFAULT 0;	
	DECLARE v_cur CURSOR FOR 
	SELECT 2 stat_type
	,205 stat_status
	,o.one_proxy_id proxy_id
	,MAX(p.proxy_code) proxy_code
	,MAX(p.proxy_name) proxy_name
	,MAX(DATE_FORMAT(o.complete_time,'%Y')) stat_year
	,MAX(DATE_FORMAT(o.complete_time,'%Y%m')) stat_month
	,DATE_FORMAT(o.complete_time,'%Y%m%d') stat_day
	,SUM(r.price * o.one_proxy_discount) stat_refund_price
	FROM t_flow_order o 
	INNER JOIN t_flow_proxy p ON o.`one_proxy_id` = p.`proxy_id` 
	INNER JOIN (SELECT * FROM t_flow_order_refund re WHERE re.`status` = 4) r ON r.`order_id` = o.`order_id`
	WHERE o.order_status IN (2,5) 
	GROUP BY o.`one_proxy_id`,DATE_FORMAT(o.complete_time,'%Y%m%d');
	#申明事务处理错误标志	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1; 
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始时间
	INSERT INTO t_flow_sys_event_log(log_type ,event_title, log_desc ,log_status ,start_time,end_time )
	VALUES (1,'p_auto_stat_proxy_refund','代理充值退款统计',1,NOW(6),NULL);
	SET v_log_id = @@IDENTITY;
	#开始事务
	START TRANSACTION;
		#代理充值退款统计
		OPEN v_cur;   
		FETCH v_cur INTO v_stat_type,v_stat_status,v_proxy_id,v_proxy_code,v_proxy_name,v_stat_year,v_stat_month,v_stat_day,v_stat_refund_price;   
		WHILE v_done = 0 DO  
			#SELECT v_stat_type,v_stat_status,v_proxy_id,v_proxy_code,v_proxy_name,v_stat_year,v_stat_month,v_stat_day,v_stat_refund_price;
			SELECT COUNT(*) 
			INTO  v_count
			FROM t_flow_stat_proxy sp
			WHERE sp.stat_type = v_stat_type
			AND sp.stat_status = v_stat_status
			AND sp.proxy_id = v_proxy_id
			AND sp.stat_day = v_stat_day;
			#select v_count;
			IF v_count > 0 THEN
				UPDATE t_flow_stat_proxy sp
				SET stat_refund_price = v_stat_refund_price
				WHERE sp.stat_type = v_stat_type
				AND sp.stat_status = v_stat_status
				AND sp.proxy_id = v_proxy_id
				AND sp.stat_day = v_stat_day
				AND sp.stat_refund_price <> v_stat_refund_price;
			ELSE
				INSERT INTO t_flow_stat_proxy(stat_type ,stat_status 
				,proxy_id,proxy_code,proxy_name
				,stat_year,stat_month,stat_day
				,stat_price,stat_count,stat_refund_price,create_date)
				VALUES(v_stat_type ,v_stat_status 
				,v_proxy_id,v_proxy_code,v_proxy_name
				,v_stat_year,v_stat_month,v_stat_day
				,0,0,v_stat_refund_price,NOW());
			END IF;
			
			FETCH v_cur INTO v_stat_type,v_stat_status,v_proxy_id,v_proxy_code,v_proxy_name,v_stat_year,v_stat_month,v_stat_day,v_stat_refund_price;
		END WHILE;   
		CLOSE v_cur; 
		
		
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE  	
		#提交
		COMMIT; 
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_record
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_record`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_record`(IN p_start_time DATETIME
  ,IN p_end_time DATETIME)
BEGIN 
	/*
	author		lk
	date		2016/08/18
	desc		自动统计数据(00:00)
	*/
	CALL `p_auto_stat_channel_account`(p_start_time);
	CALL `p_auto_stat_proxy_account`(p_start_time);
	CALL `p_auto_stat_enterprise_account`(p_start_time);
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_auto_stat_rpt
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_auto_stat_rpt`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_auto_stat_rpt`(IN p_start_time DATETIME
  ,IN p_end_time DATETIME)
BEGIN 
	/*
	author		cxw
	date		2016/05/25
	desc		自动统计报表数据
	*/
	CALL `p_auto_stat_proxy`(p_start_time,p_end_time);
	CALL `p_auto_stat_enterprise`(p_start_time,p_end_time);
	CALL `p_auto_stat_channel`(p_start_time,p_end_time);	
	CALL `p_auto_init_monitor_channel_stat`();
	CALL `p_auto_init_monitor_province_stat`();
	CALL `p_auto_stat_order_enterprise`(p_start_time,p_end_time);
	CALL `p_auto_stat_order_proxy`(p_start_time,p_end_time);
	CALL p_auto_stat_proxy_consume(p_start_time,p_end_time);
	CALL p_auto_stat_proxy_refund(NULL,NULL);
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_edit_column_lk
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_edit_column_lk`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_edit_column_lk`()
BEGIN
	DECLARE v_done INT DEFAULT 0; 
	DECLARE v_table_name VARCHAR(64) DEFAULT ''; 
	DECLARE temp_table_name VARCHAR(64) DEFAULT '';
	DECLARE v_column_name VARCHAR(64) DEFAULT ''; 
	DECLARE v_data_type VARCHAR(64) DEFAULT ''; 
	DECLARE v_numeric_precision BIGINT(21) DEFAULT NULL; 
	DECLARE v_numeric_scale BIGINT(21) DEFAULT NULL; 
	DECLARE v_modify_str VARCHAR(2000) DEFAULT ''; 
	DECLARE v_strsql_str VARCHAR(2000) DEFAULT ''; 
	DECLARE temp_strsql_str VARCHAR(10000) DEFAULT ''; 
	#事务处理标志
	#DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_num INTEGER DEFAULT 0; 
	
	
	DECLARE v_cur CURSOR FOR 
	SELECT table_name,column_name,data_type,numeric_precision,numeric_scale FROM information_schema.`COLUMNS`  
	WHERE TABLE_SCHEMA='st_flow' AND data_type = 'float';
		
	#事务处理标志符
	#DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1; 
	
	#开始事务
	#START TRANSACTION;
	OPEN v_cur;
	FETCH v_cur INTO v_table_name,v_column_name,v_data_type,v_numeric_precision,v_numeric_scale;
	WHILE v_done = 0 DO   
	      SET v_data_type = 'DECIMAL';
	      IF  v_table_name <> temp_table_name THEN
		     IF temp_table_name <>'' THEN
			SET temp_strsql_str = CONCAT(temp_strsql_str,'\r',v_strsql_str,SUBSTR(v_modify_str,1,LENGTH(v_modify_str)-1),';');
			
			/*
			PREPARE strsql FROM @strsql; 
		        EXECUTE strsql; 
		        DEALLOCATE PREPARE strsql;
		        */
		     END IF;
		        
			SET temp_table_name = v_table_name;
			SET v_strsql_str = CONCAT('ALTER TABLE ',temp_table_name);
			SET v_modify_str = '';
			SET v_modify_str = CONCAT(v_modify_str,
			      '\r MODIFY COLUMN '
			      ,v_column_name,' ');
			IF v_numeric_scale IS NULL AND v_column_name = 'profit' THEN 
				SET v_modify_str = CONCAT(v_modify_str,
				v_data_type,'(',11,',',3,'),');
			ELSEIF  v_numeric_scale IS NULL THEN
				SET v_modify_str = CONCAT(v_modify_str,
				v_data_type,'(',5,',',3,'),');
			ELSE
				SET v_modify_str = CONCAT(v_modify_str,
				v_data_type,'(',v_numeric_precision,',',v_numeric_scale,'),');
			END IF;
			      
	      ELSE
			SET v_modify_str = CONCAT(v_modify_str,
			      '\r MODIFY COLUMN '
			      ,v_column_name,' ');
			IF v_numeric_scale IS NULL AND v_column_name = 'profit' THEN 
				SET v_modify_str = CONCAT(v_modify_str,
				v_data_type,'(',11,',',3,'),');
			ELSEIF  v_numeric_scale IS NULL THEN
				SET v_modify_str = CONCAT(v_modify_str,
				v_data_type,'(',5,',',3,'),');
			ELSE
				SET v_modify_str = CONCAT(v_modify_str,
				v_data_type,'(',v_numeric_precision,',',v_numeric_scale,'),');
			END IF;
	      END IF;
	      
	     
	      SET v_num = v_num+1;
	      FETCH v_cur INTO v_table_name,v_column_name,v_data_type,v_numeric_precision,v_numeric_scale;   
	END WHILE;
	CLOSE v_cur; 
	SELECT temp_strsql_str;
	/*
	IF t_error = 1 THEN 
		SET p_out_flag = 0;
		ROLLBACK;  
	ELSE  
		SET p_out_flag = 1;
		COMMIT;  
	END IF;
	*/
	
 END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_callback_order
-- ----------------------------

DROP PROCEDURE IF EXISTS `p_get_callback_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_callback_order`(p_channel_id BIGINT
,p_limit INT
,OUT p_out_flag INT)
BEGIN 
  /*
  author    cxw
  date    2016/07/01
  desc    给下游代理商或企业主动推送回调
  */
  #取得订单信息，每个订单ID按,拼接
  #DECLARE v_callback_ids VARCHAR(2000) DEFAULT '';
  #输出标志，0代表执行失败，1代表执行成功
  DECLARE v_flag INT DEFAULT 0;
  #事务处理标志
  DECLARE t_error INTEGER DEFAULT 0; 
  #订单ID
  DECLARE v_callback_id BIGINT;   
  DECLARE v_start_time TIMESTAMP(6);
  DECLARE v_end_time TIMESTAMP(6);
  #获取所需通道ID
  DECLARE v_channel_id_str TEXT DEFAULT '';
  #事务处理标志符
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
  
  SET v_start_time = CURRENT_TIMESTAMP(6);
  
  IF p_channel_id = 0 THEN
    SELECT GROUP_CONCAT(channel_id)
    INTO v_channel_id_str
    FROM `t_flow_channel` ;
    
  ELSE 
    SET v_channel_id_str = p_channel_id;
  END IF;
  
  #开始事务
  START TRANSACTION;
    SET @strsqlLimit=CONCAT(' SELECT GROUP_CONCAT(a.callback_id) '
    ,'\r INTO @v_callback_ids '
    ,'\r FROM '
    ,'\r ('
    ,'\r  SELECT oc.callback_id '
    ,'\r  FROM t_flow_order_callback oc '
    ,'\r  WHERE oc.`status` = 0 '
    ,'\r    AND oc.times < 3 '
    ,'\r  AND oc.order_status >0 '
    ,'\r  AND oc.final_channel_id in (',v_channel_id_str,') '
    ,'\r  ORDER BY oc.callback_id ASC LIMIT ',p_limit,' FOR UPDATE'
    ,'\r ) a');   
    #select @strsqlLimit;
    PREPARE stmtsqlLimit FROM @strsqlLimit; 
    EXECUTE stmtsqlLimit; 
    DEALLOCATE PREPARE stmtsqlLimit;  
    SET @v_callback_ids = IFNULL(@v_callback_ids,'0');
    
    IF @v_callback_ids > '0' THEN
      #更新订单is_using = 0 标志代表已经取过
      SET @strsqlUpdate = CONCAT('UPDATE t_flow_order_callback SET status = -1 WHERE callback_id IN (',@v_callback_ids,')'); 
      
      #SELECT @strsqlUpdate; 
      PREPARE stmtsqlUpdate FROM @strsqlUpdate; 
      EXECUTE stmtsqlUpdate; 
      DEALLOCATE PREPARE stmtsqlUpdate;
      
      #获取订单相关信息
      SET @strsqlSelect = CONCAT('SELECT `callback_id`,`order_id`,`url`,`content`,`rece_content`,`times`,`status`,`end_date`,orderno_id'
      ,'\r FROM t_flow_order_callback'
      ,'\r WHERE callback_id IN (',@v_callback_ids,')'); 
      
      #SELECT  @strsqlSelect;
      PREPARE stmtsqlSelect FROM @strsqlSelect; 
      EXECUTE stmtsqlSelect; 
      DEALLOCATE PREPARE stmtsqlSelect;
      
      #记录取订单日志信息    
      SET v_end_time = CURRENT_TIMESTAMP(6);
    
      INSERT INTO t_flow_order_get_log(order_id,dt,channel_id,end_dt,get_type) 
      VALUES(@v_callback_ids,v_start_time,p_channel_id,v_end_time,3);
    END IF;
          
  IF t_error = 1 THEN 
    SET p_out_flag = 0;
    ROLLBACK;  
  ELSE  
    SET p_out_flag = 1;
    COMMIT;  
  END IF;   
  
END
;;
DELIMITER ;



-- ----------------------------
-- Procedure structure for p_get_callback_order_20161130temp
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_callback_order_20161130temp`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_callback_order_20161130temp`(p_channel_id BIGINT 
,p_platform_id INT 
,p_limit INT
,OUT p_out_flag INT)
BEGIN 
	/*
	author		cxw
	date		2016/07/01
	desc		给下游代理商或企业主动推送回调
	*/
	#取得订单信息，每个订单ID按,拼接
	#DECLARE v_callback_ids VARCHAR(2000) DEFAULT '';
	#输出标志，0代表执行失败，1代表执行成功
	DECLARE v_flag INT DEFAULT 0;
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#订单ID
	DECLARE v_callback_id BIGINT;  	
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	#获取所需通道ID
	DECLARE v_channel_id_str TEXT DEFAULT '';
	#事务处理标志符
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	IF p_channel_id = 0 THEN
		SELECT GROUP_CONCAT(channel_id)
		INTO v_channel_id_str
		FROM `t_flow_channel` 
		WHERE platform_id = p_platform_id;
	ELSE 
		SET v_channel_id_str = p_channel_id;
	END IF;
	
	#开始事务
	START TRANSACTION;
		
		SET @strsqlLimit=CONCAT(' SELECT GROUP_CONCAT(a.callback_id) '
		,'\r INTO @v_callback_ids '
		,'\r FROM '
		,'\r ('
		,'\r 	SELECT oc.callback_id '
		,'\r 	FROM t_flow_order_callback oc '
		,'\r 	WHERE oc.`status` = 0 '
		,'\r 	AND oc.url <> ''''  '
		,'\r 	AND oc.content <> '''' '
		,'\r  AND oc.times < 3 '
		,'\r 	AND '
		,'\r 	( '
		,'\r 		(oc.order_status IN (1,2,3) '
		,'\r 			AND oc.channel_id IN (',v_channel_id_str,') '
		,'\r 		) '
		,'\r 		OR '
		,'\r 		(oc.order_status IN (4,5,6) '
		,'\r 			AND oc.back_channel_id IN (',v_channel_id_str,') '
		,'\r 		)'
		,'\r 	)'
		,'\r 	ORDER BY oc.callback_id ASC LIMIT ',p_limit,' FOR UPDATE'
		,'\r ) a');		
		
		#select @strsqlLimit;
		PREPARE stmtsqlLimit FROM @strsqlLimit; 
		EXECUTE stmtsqlLimit; 
		DEALLOCATE PREPARE stmtsqlLimit;	
		SET @v_callback_ids = IFNULL(@v_callback_ids,'0');
		
		IF @v_callback_ids > '0' THEN
			#更新订单is_using = 0 标志代表已经取过
			SET @strsqlUpdate = CONCAT('UPDATE t_flow_order_callback SET status = -1 WHERE callback_id IN (',@v_callback_ids,')'); 
			
			#SELECT @strsqlUpdate; 
			PREPARE stmtsqlUpdate FROM @strsqlUpdate; 
			EXECUTE stmtsqlUpdate; 
			DEALLOCATE PREPARE stmtsqlUpdate;
			
			#获取订单相关信息
			SET @strsqlSelect = CONCAT('SELECT `callback_id`,`order_id`,`url`,`content`,`rece_content`,`times`,`status`,`end_date`,orderno_id'
			,'\r FROM t_flow_order_callback'
			,'\r WHERE callback_id IN (',@v_callback_ids,')'); 
			
			#SELECT  @strsqlSelect;
			PREPARE stmtsqlSelect FROM @strsqlSelect; 
			EXECUTE stmtsqlSelect; 
			DEALLOCATE PREPARE stmtsqlSelect;
			
			#记录取订单日志信息		
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_get_log(order_id,dt,channel_id,platform_id,end_dt,get_type) 
			VALUES(@v_callback_ids,v_start_time,p_channel_id,p_platform_id,v_end_time,3);
	END IF;
					
	IF t_error = 1 THEN 
		SET p_out_flag = 0;
		ROLLBACK;  
	ELSE  
		SET p_out_flag = 1;
		COMMIT;  
	END IF;		
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_commit_order
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_commit_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_commit_order`(p_limit INT
,OUT p_out_flag INT)
BEGIN 
  /*
  author    cxw
  date    2016/05/16
  parameter
      p_limit #获取记录数条数
      
  desc    获取预处理订单信息，分次取5个订单
      2016/07/11 增加通道ID，处理各自的版本通道订单 by cxw 
  */
  #取得订单信息，每个订单ID按,拼接
  #DECLARE v_order_ids VARCHAR(2000) DEFAULT '';
  #输出标志，0代表执行失败，1代表执行成功
  DECLARE v_flag INT DEFAULT 0;
  #事务处理标志
  DECLARE t_error INTEGER DEFAULT 0; 
  #订单ID
  DECLARE v_order_id BIGINT; 

  #事务处理标志符
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 

  #开始事务

  START TRANSACTION;
    #获得需要处理订单信息并加锁
    #主通道
    SET @strsqlLimit=CONCAT(' SELECT GROUP_CONCAT(a.order_id) '
    ,'\r INTO @v_order_ids '
    ,'\r FROM '
    ,'\r ('
    ,'\r  SELECT op.order_id '
    ,'\r  FROM t_flow_order_pre op '
    ,'\r  WHERE op.order_status = 0 '
    ,'\r  AND op.is_using = 0 '
    ,'\r  ORDER BY op.`last_update_time` ASC LIMIT ',p_limit,' FOR UPDATE'
    ,'\r ) a');   
    
    #select @strsqlLimit;
    PREPARE stmtsqlLimit FROM @strsqlLimit; 
    #select @strsqlLimit;
    EXECUTE stmtsqlLimit; 
    DEALLOCATE PREPARE stmtsqlLimit;
    
    #备通道
    SET @strsqlLimitBack=CONCAT(' SELECT GROUP_CONCAT(a.order_id) '
    ,'\r INTO @v_back_order_ids '
    ,'\r FROM '
    ,'\r ('
    ,'\r  SELECT op.order_id '
    ,'\r  FROM t_flow_order_pre op '
    ,'\r  WHERE op.order_status = 3 '
    ,'\r  AND op.is_using = 0 '
    ,'\r  ORDER BY op.`last_update_time` ASC LIMIT ',p_limit,' FOR UPDATE'
    ,'\r ) a');   
    
    #select @strsqlLimitBack;
    PREPARE stmtsqlLimitBack FROM @strsqlLimitBack; 
    #select @strsqlLimit;
    EXECUTE stmtsqlLimitBack; 
    DEALLOCATE PREPARE stmtsqlLimitBack;
    
    SET @v_order_ids = CONCAT(IFNULL(@v_order_ids,'0'),',',IFNULL(@v_back_order_ids,'0'));
    #记录取订单日志信息
    #select @v_order_ids ;
    
    IF @v_order_ids > '0,0' THEN
      INSERT INTO t_flow_order_get_log(order_id,dt) 
      VALUES(@v_order_ids,CURRENT_TIMESTAMP(6));
      #更新订单is_using = 0 标志代表已经取过
      SET @strsqlUpdate = CONCAT('UPDATE t_flow_order_pre '
      ,'\r SET is_using = 1'
      #,'\r,last_update_time = NOW(6) '
      ,'\r WHERE order_id IN (',@v_order_ids,')'); 
      
      #SELECT @strsqlUpdate; 
      PREPARE stmtsqlUpdate FROM @strsqlUpdate; 
      EXECUTE stmtsqlUpdate; 
      DEALLOCATE PREPARE stmtsqlUpdate;
    
      #获取订单相关信息
      SET @strsqlSelect = CONCAT('SELECT op.order_id,op.order_code,op.mobile,op.back_content,op.order_status'
      ,'\r ,op.channel_id,op.back_channel_id,op.channel_order_code,op.`order_date` '
      ,'\r ,op.`back_fail_desc`,op.`complete_time`,cp.`size`'
      ,'\r ,CASE op.order_status WHEN 0 THEN cp.`number` ELSE bcp.number END number' 
      ,'\r ,CASE op.order_status WHEN 0 THEN c.`channel_file_name` ELSE bc.`channel_file_name` END channel_file_name'
      ,'\r ,op.orderno_id'
      ,'\r FROM t_flow_order_pre op, t_flow_channel_product cp,t_flow_channel c,t_flow_channel bc,t_flow_channel_product bcp'
      ,'\r WHERE op.`channel_product_id` = cp.`product_id` '
      ,'\r AND op.`back_channel_product_id` = bcp.`product_id` '
      ,'\r AND op.channel_id = c.channel_id'
      ,'\r AND op.back_channel_id = bc.channel_id'
      ,'\r AND op.order_id IN (',@v_order_ids,')'); 
      
      #SELECT  @strsqlSelect;
      PREPARE stmtsqlSelect FROM @strsqlSelect; 
      EXECUTE stmtsqlSelect; 
      DEALLOCATE PREPARE stmtsqlSelect;
    END IF;
    
  IF t_error = 1 THEN 
    SET p_out_flag = 0;
    ROLLBACK;  
  ELSE  
    SET p_out_flag = 1;
    COMMIT;  
  END IF;   
  
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_commit_order_back_0924
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_commit_order_back_0924`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_commit_order_back_0924`(p_channel_id BIGINT 
,p_platform_id INT 
,p_limit INT
,OUT p_out_flag INT)
BEGIN 
	/*
	author		cxw
	date		2016/05/16
	parameter
			p_channel_id #0代表所有，>0代表具体通道ID
			p_platform_id #1:php 2:java
			p_limit #获取记录数条数
			
	desc		获取预处理订单信息，分次取5个订单
			2016/07/11 增加通道ID，处理各自的版本通道订单 by cxw 
	*/
	#取得订单信息，每个订单ID按,拼接
	#DECLARE v_order_ids VARCHAR(2000) DEFAULT '';
	#输出标志，0代表执行失败，1代表执行成功
	DECLARE v_flag INT DEFAULT 0;
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#订单ID
	DECLARE v_order_id BIGINT; 
	#获取所需通道ID
	DECLARE v_channel_id_str TEXT DEFAULT '';
	
	#事务处理标志符
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	IF p_channel_id = 0 THEN
		SELECT GROUP_CONCAT(channel_id)
		INTO v_channel_id_str
		FROM `t_flow_channel` 
		WHERE platform_id = p_platform_id;
	ELSE 
		SET v_channel_id_str = p_channel_id;
	END IF;
	#select v_channel_id_str;					
	#开始事务
	#select v_channel_id_str;
	START TRANSACTION;
		#获得需要处理订单信息并加锁
		SET @strsqlLimit=CONCAT(' SELECT GROUP_CONCAT(a.order_id) '
		,'\r INTO @v_order_ids '
		,'\r FROM '
		,'\r ('
		,'\r 	SELECT op.order_id '
		,'\r 	FROM t_flow_order_pre op '
		,'\r 	WHERE op.order_status IN(0,3) '
		,'\r 	AND op.is_using = 0 '
		,'\r 	AND op.last_update_time < DATE_ADD(NOW(6), INTERVAL -30 SECOND) '
		,'\r 	AND '
		,'\r 	( '
		,'\r 		(op.order_status = 0 '
		,'\r 			AND op.channel_id IN (',v_channel_id_str,') '
		,'\r 		) '
		,'\r 		OR '
		,'\r 		(op.order_status = 3 '
		,'\r 			AND op.back_channel_id IN (',v_channel_id_str,') '
		,'\r 		)'
		,'\r 	)'
		,'\r 	ORDER BY op.`last_update_time` ASC LIMIT ',p_limit,' FOR UPDATE'
		,'\r ) a');		
		
		#select @strsqlLimit;
		PREPARE stmtsqlLimit FROM @strsqlLimit; 
		#select @strsqlLimit;
		EXECUTE stmtsqlLimit; 
		DEALLOCATE PREPARE stmtsqlLimit;	
		
		SET @v_order_ids = IFNULL(@v_order_ids,'0');
		#记录取订单日志信息
		IF @v_order_ids > '0' THEN
			INSERT INTO t_flow_order_get_log(order_id,dt,channel_id,platform_id) 
			VALUES(@v_order_ids,CURRENT_TIMESTAMP(6),p_channel_id,p_platform_id);
			#更新订单is_using = 0 标志代表已经取过
			SET @strsqlUpdate = CONCAT('UPDATE t_flow_order_pre '
			,'\r SET is_using = 1'
			#,'\r,last_update_time = NOW(6) '
			,'\r WHERE order_id IN (',@v_order_ids,')'); 
			
			#SELECT @strsqlUpdate; 
			PREPARE stmtsqlUpdate FROM @strsqlUpdate; 
			EXECUTE stmtsqlUpdate; 
			DEALLOCATE PREPARE stmtsqlUpdate;
		
			#获取订单相关信息
			SET @strsqlSelect = CONCAT('SELECT op.order_id,op.order_code,op.mobile,op.back_content,op.order_status'
			,'\r ,op.channel_id,op.back_channel_id,op.channel_order_code,op.`order_date` '
			,'\r ,op.`back_fail_desc`,op.`complete_time`,cp.`size`'
			,'\r ,CASE op.order_status WHEN 0 THEN cp.`number` ELSE bcp.number END number' 
			,'\r ,CASE op.order_status WHEN 0 THEN c.`channel_file_name` ELSE bc.`channel_file_name` END channel_file_name'
			,'\r ,op.orderno_id'
			,'\r FROM t_flow_order_pre op, t_flow_channel_product cp,t_flow_channel c,t_flow_channel bc,t_flow_channel_product bcp'
			,'\r WHERE op.`channel_product_id` = cp.`product_id` '
			,'\r AND op.`back_channel_product_id` = bcp.`product_id` '
			,'\r AND op.channel_id = c.channel_id'
			,'\r AND op.back_channel_id = bc.channel_id'
			,'\r AND op.order_id IN (',@v_order_ids,')'); 
			
			#SELECT  @strsqlSelect;
			PREPARE stmtsqlSelect FROM @strsqlSelect; 
			EXECUTE stmtsqlSelect; 
			DEALLOCATE PREPARE stmtsqlSelect;
		END IF;
			
	IF t_error = 1 THEN 
		SET p_out_flag = 0;
		ROLLBACK;  
	ELSE  
		SET p_out_flag = 1;
		COMMIT;  
	END IF;		
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_commit_sms
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_commit_sms`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_commit_sms`(
p_limit INT
,OUT p_out_flag INT)
BEGIN 
	/*
	author		lk
	date		2016/11/01
	parameter
			p_limit #获取记录数条数a
			
	desc		获取短信预处理信息
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	
	#事务处理标志符
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
						
	#开始事务
	START TRANSACTION;
		#获得需要处理订单信息并加锁
		#主通道
		SET @strsqlLimit=CONCAT(' SELECT GROUP_CONCAT(a.order_id) '
		,'\r INTO @v_order_ids '
		,'\r FROM '
		,'\r ('
		,'\r 	SELECT sp.order_id '
		,'\r 	FROM t_flow_sms_pre sp '
		,'\r 	WHERE sp.status = 0 '
		,'\r 	AND sp.create_date < DATE_ADD(NOW(6), INTERVAL -30 SECOND) '
		,'\r 	ORDER BY sp.create_date ASC LIMIT ',p_limit,' FOR UPDATE'
		,'\r ) a');		
		
		#select @strsqlLimit;
		PREPARE stmtsqlLimit FROM @strsqlLimit; 
		#select @strsqlLimit;
		EXECUTE stmtsqlLimit; 
		DEALLOCATE PREPARE stmtsqlLimit;
		
		SET @v_order_ids = IFNULL(@v_order_ids,'0');
		IF @v_order_ids > '0' THEN
			#更新记录status = 1 标志代表已经取过
			SET @strsqlUpdate = CONCAT('UPDATE t_flow_sms_pre '
			,'\r SET status = 1,is_using = 1'
			,'\r WHERE order_id IN (',@v_order_ids,')'); 
			
			#SELECT @strsqlUpdate; 
			PREPARE stmtsqlUpdate FROM @strsqlUpdate; 
			EXECUTE stmtsqlUpdate; 
			DEALLOCATE PREPARE stmtsqlUpdate;
		
			#获取相关信息
			SET @strsqlSelect = CONCAT('SELECT sp.order_id,sp.mobile,sp.price,sp.product_name'
			,'\r ,sp.status,sp.order_date,sp.complete_time,sp.create_date '
			,'\r FROM t_flow_sms_pre sp'
			,'\r WHERE sp.order_id IN (',@v_order_ids,')'); 
			
			#SELECT  @strsqlSelect;
			PREPARE stmtsqlSelect FROM @strsqlSelect; 
			EXECUTE stmtsqlSelect; 
			DEALLOCATE PREPARE stmtsqlSelect;
		ELSE
			#获取相关信息
			SET @strsqlSelect = CONCAT('SELECT sp.order_id,sp.mobile,sp.price,sp.product_name'
			,'\r ,sp.status,sp.order_date,sp.complete_time,sp.create_date '
			,'\r FROM t_flow_sms_pre sp'
			,'\r WHERE sp.order_id =0'); 
			PREPARE stmtsqlSelect FROM @strsqlSelect; 
			EXECUTE stmtsqlSelect; 
			DEALLOCATE PREPARE stmtsqlSelect;
		END IF;
		
	IF t_error = 1 THEN 
		SET p_out_flag = 0;
		ROLLBACK;  
	ELSE  
		SET p_out_flag = 1;
		COMMIT;  
	END IF;		
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_make_query_order
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_make_query_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_make_query_order`(
p_channel_id BIGINT 
,p_platform_id INT 
,p_limit INT
,OUT p_out_flag INT
)
BEGIN 
	/*
	author		cxw
	date		2016/07/01
	parameter
			p_channel_id #0代表所有，>0代表具体通道ID
			p_platform_id #1:php 2:java
			p_limit #获取记录数条数
			
	desc		主动去上游通道查询订单状态
			2016/07/11 增加通道ID，处理各自的版本通道订单 by cxw 
			2016/07/26 修改卡单频繁取数据时间 by cxw
	*/
	#取得订单信息，每个订单ID按,拼接
	#DECLARE v_order_ids VARCHAR(2000) DEFAULT '';
	#输出标志，0代表执行失败，1代表执行成功
	DECLARE v_flag INT DEFAULT 0;
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#订单ID
	DECLARE v_order_id BIGINT;
	#获取所需通道ID
	DECLARE v_channel_id_str TEXT DEFAULT '';
	#事务处理标志符
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	IF p_channel_id = 0 THEN
		SELECT GROUP_CONCAT(channel_id)
		INTO v_channel_id_str
		FROM `t_flow_channel` 
		WHERE platform_id = p_platform_id;
	ELSE 
		SET v_channel_id_str = p_channel_id;
	END IF;
	
	#开始事务
	START TRANSACTION;
		#获得需要处理订单信息并加锁
		#主通道
		SET @stmtsqlLimit = CONCAT(' SELECT GROUP_CONCAT(a.order_id)'
		,'\r INTO @v_order_ids'
		,'\r FROM'
		,'\r ('
		,'\r 	SELECT order_id'
		,'\r 	FROM t_flow_order_pre o '
		,'\r 	WHERE o.`last_update_time` < DATE_ADD(NOW(6), INTERVAL -5 MINUTE)'
		,'\r 	AND  o.order_status = 1 '
		,'\r 	AND o.is_using = 0 '
		,'\r 	AND o.channel_id IN (',v_channel_id_str,')'
		,'\r 	ORDER BY o.`last_update_time` ASC LIMIT ',p_limit,' FOR UPDATE'
		,'\r ) a');
		
		#select @stmtsqlLimit;
		PREPARE stmtsqlLimit FROM @stmtsqlLimit; 
		EXECUTE stmtsqlLimit; 
		DEALLOCATE PREPARE stmtsqlLimit;	
		
		#备通道
		SET @stmtsqlLimitBack = CONCAT(' SELECT GROUP_CONCAT(a.order_id)'
		,'\r INTO @v_back_order_ids'
		,'\r FROM'
		,'\r ('
		,'\r 	SELECT order_id'
		,'\r 	FROM t_flow_order_pre o '
		,'\r 	WHERE o.`last_update_time` < DATE_ADD(NOW(6), INTERVAL -5 MINUTE)'
		,'\r 	AND  o.order_status = 4 '
		,'\r 	AND o.is_using = 0 '
		,'\r 	AND o.back_channel_id IN (',v_channel_id_str,')'
		,'\r 	ORDER BY o.`last_update_time` ASC LIMIT ',p_limit,' FOR UPDATE'
		,'\r ) a');
		
		#select @stmtsqlLimitBack;
		PREPARE stmtsqlLimitBack FROM @stmtsqlLimitBack; 
		EXECUTE stmtsqlLimitBack; 
		DEALLOCATE PREPARE stmtsqlLimitBack;
		
		SET @v_order_ids = CONCAT(IFNULL(@v_order_ids,'0'),',',IFNULL(@v_back_order_ids,'0'));
		
		IF @v_order_ids > '0,0' THEN			
			#记录取订单日志信息		
			INSERT INTO t_flow_order_get_log(order_id,dt,channel_id,platform_id) 
			VALUES(@v_order_ids,CURRENT_TIMESTAMP(6),p_channel_id,p_platform_id);
			#更新订单is_using = 0 标志代表已经取过
			
			SET @strsqlUpdate = CONCAT('UPDATE t_flow_order_pre '
			,'\r SET is_using = 1'
			,'\r ,last_update_time = DATE_ADD(NOW(6), INTERVAL -4 MINUTE) '
			,'\r WHERE order_id IN (',@v_order_ids,')'); 
			
			#SELECT @strsqlUpdate; 
			PREPARE stmtsqlUpdate FROM @strsqlUpdate; 
			EXECUTE stmtsqlUpdate; 
			DEALLOCATE PREPARE stmtsqlUpdate;
			
			#获取订单相关信息
			SET @strsqlSelect = CONCAT('SELECT op.order_id,op.order_code,op.mobile,op.back_content,op.order_status'
			,'\r ,op.channel_id,op.back_channel_id,op.channel_order_code,op.`order_date` '
			,'\r ,op.`back_fail_desc`,op.`complete_time`,cp.`size`'
			,'\r ,CASE op.order_status WHEN 1 THEN cp.`number` ELSE bcp.number END number' 
			,'\r ,CASE op.order_status WHEN 1 THEN c.`channel_file_name` ELSE bc.`channel_file_name` END channel_file_name'
			,'\r ,op.orderno_id'
			,'\r FROM t_flow_order_pre op, t_flow_channel_product cp,t_flow_channel c,t_flow_channel bc,t_flow_channel_product bcp'
			,'\r WHERE op.`channel_product_id` = cp.`product_id` '
			,'\r AND op.`back_channel_product_id` = bcp.`product_id` '
			,'\r AND op.channel_id = c.channel_id'
			,'\r AND op.back_channel_id = bc.channel_id'
			,'\r AND op.order_id IN (',@v_order_ids,')'); 
			
			#SELECT  @strsqlSelect;
			PREPARE stmtsqlSelect FROM @strsqlSelect; 
			EXECUTE stmtsqlSelect; 
			DEALLOCATE PREPARE stmtsqlSelect;
		END IF;	
		
	IF t_error = 1 THEN 
		SET p_out_flag = 0;
		ROLLBACK;  
	ELSE  
		SET p_out_flag = 1;
		COMMIT;  
	END IF;		
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_make_query_order_back_0924
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_make_query_order_back_0924`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_make_query_order_back_0924`(
p_channel_id BIGINT 
,p_platform_id INT 
,p_limit INT
,OUT p_out_flag INT
)
BEGIN 
	/*
	author		cxw
	date		2016/07/01
	parameter
			p_channel_id #0代表所有，>0代表具体通道ID
			p_platform_id #1:php 2:java
			p_limit #获取记录数条数
			
	desc		主动去上游通道查询订单状态
			2016/07/11 增加通道ID，处理各自的版本通道订单 by cxw 
			2016/07/26 修改卡单频繁取数据时间 by cxw
	*/
	#取得订单信息，每个订单ID按,拼接
	#DECLARE v_order_ids VARCHAR(2000) DEFAULT '';
	#输出标志，0代表执行失败，1代表执行成功
	DECLARE v_flag INT DEFAULT 0;
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#订单ID
	DECLARE v_order_id BIGINT;
	#获取所需通道ID
	DECLARE v_channel_id_str TEXT DEFAULT '';
	#事务处理标志符
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	IF p_channel_id = 0 THEN
		SELECT GROUP_CONCAT(channel_id)
		INTO v_channel_id_str
		FROM `t_flow_channel` 
		WHERE platform_id = p_platform_id;
	ELSE 
		SET v_channel_id_str = p_channel_id;
	END IF;
	
	#开始事务
	START TRANSACTION;
		#获得需要处理订单信息并加锁
		SET @stmtsqlLimit = CONCAT(' SELECT GROUP_CONCAT(a.order_id)'
		,'\r INTO @v_order_ids'
		,'\r FROM'
		,'\r ('
		,'\r 	SELECT order_id'
		,'\r 	FROM t_flow_order_pre o '
		,'\r 	WHERE o.`last_update_time` < DATE_ADD(NOW(6), INTERVAL -5 MINUTE)'
		,'\r 	AND  o.order_status IN(1,4) '
		,'\r 	AND o.is_using = 0 '
		,'\r 	AND'
		,'\r 	('
		,'\r 		(o.order_status = 1 '
		,'\r 			AND o.channel_id IN (',v_channel_id_str,')'
		,'\r 		)'
		,'\r 		OR '
		,'\r 		(o.order_status = 4 '
		,'\r 			AND o.back_channel_id IN (',v_channel_id_str,')'
		,'\r 		)'
		,'\r 	)'
		,'\r 	ORDER BY o.`order_id` ASC LIMIT ',p_limit,' FOR UPDATE'
		,'\r ) a');
		
		PREPARE stmtsqlLimit FROM @stmtsqlLimit; 
		EXECUTE stmtsqlLimit; 
		DEALLOCATE PREPARE stmtsqlLimit;		
		
		SET @v_order_ids = IFNULL(@v_order_ids,'0');
		
		IF @v_order_ids > '0' THEN			
			#记录取订单日志信息		
			INSERT INTO t_flow_order_get_log(order_id,dt,channel_id,platform_id) 
			VALUES(@v_order_ids,CURRENT_TIMESTAMP(6),p_channel_id,p_platform_id);
			#更新订单is_using = 0 标志代表已经取过
			
			SET @strsqlUpdate = CONCAT('UPDATE t_flow_order_pre '
			,'\r SET is_using = 1'
			,'\r ,last_update_time = DATE_ADD(NOW(6), INTERVAL -4 MINUTE) '
			,'\r WHERE order_id IN (',@v_order_ids,')'); 
			
			#SELECT @strsqlUpdate; 
			PREPARE stmtsqlUpdate FROM @strsqlUpdate; 
			EXECUTE stmtsqlUpdate; 
			DEALLOCATE PREPARE stmtsqlUpdate;
			
			#获取订单相关信息
			SET @strsqlSelect = CONCAT('SELECT op.order_id,op.order_code,op.mobile,op.back_content,op.order_status'
			,'\r ,op.channel_id,op.back_channel_id,op.channel_order_code,op.`order_date` '
			,'\r ,op.`back_fail_desc`,op.`complete_time`,cp.`size`'
			,'\r ,CASE op.order_status WHEN 0 THEN cp.`number` ELSE bcp.number END number' 
			,'\r ,CASE op.order_status WHEN 0 THEN c.`channel_file_name` ELSE bc.`channel_file_name` END channel_file_name'
			,'\r ,op.orderno_id'
			,'\r FROM t_flow_order_pre op, t_flow_channel_product cp,t_flow_channel c,t_flow_channel bc,t_flow_channel_product bcp'
			,'\r WHERE op.`channel_product_id` = cp.`product_id` '
			,'\r AND op.`back_channel_product_id` = bcp.`product_id` '
			,'\r AND op.channel_id = c.channel_id'
			,'\r AND op.back_channel_id = bc.channel_id'
			,'\r AND op.order_id IN (',@v_order_ids,')'); 
			
			#SELECT  @strsqlSelect;
			PREPARE stmtsqlSelect FROM @strsqlSelect; 
			EXECUTE stmtsqlSelect; 
			DEALLOCATE PREPARE stmtsqlSelect;
		END IF;	
	IF t_error = 1 THEN 
		SET p_out_flag = 0;
		ROLLBACK;  
	ELSE  
		SET p_out_flag = 1;
		COMMIT;  
	END IF;		
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_mobile_info
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_mobile_info`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_mobile_info`(
IN p_mobile VARCHAR(50)
)
BEGIN   
	SELECT ohh.operator_id,ohh.operator_name,ncac.province_id,ncac.province_name,ncac.area_code,ncac.city_name,ncac.city_id,NULL card,NULL postcode 
	FROM 
	(
		SELECT c.province_id,c.province_name,c.area_id area_code,c.city_id,c.city_name,NULL card,NULL postcode 
		FROM 
		(
			SELECT nc.area_id
			FROM t_flow_sys_numcollate nc 
			WHERE nc.phone_section = SUBSTR(p_mobile,1,7)
		) nca,t_flow_sys_city c 
		WHERE nca.area_id = c.area_id
	) ncac,(
		SELECT oh.operaid operator_id
		,CASE oh.operaid WHEN 1 THEN '中国移动' 
		WHEN 2 THEN '中国联通' 
		WHEN 3 THEN '中国电信' END operator_name
		FROM t_flow_opphase oh 
		WHERE oh.phone_section = IF(p_mobile LIKE '170%',SUBSTR(p_mobile,1,4),SUBSTR(p_mobile,1,3)) #SUBSTR(p_mobile,1,3)
	) ohh LIMIT 1;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_order_product
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_order_product`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_order_product`(IN p_size INT
,IN p_operator_id INT
,IN p_province_id INT 
,IN p_city_id INT
,IN p_user_type INT
,IN p_proxy_id BIGINT
,IN p_enterprise_id BIGINT
,IN p_range INT #0代表全国 1省内
,IN p_is_filter INT

,IN p_limit_count INT)
BEGIN 
  /*
  author    cxw
  date    2016/05/23
  desc    获取订单产品信息
      2016/06/27 by cxw 增加订单缓存需求，当帐户余额不足将订单缓存 
      2016/07/12 by cxw 增加通道省网折扣控制，如果通道省网设置折扣，智能分流不选择全国通道
      2016/09/09 用户产品折扣判断 by lk
  */
  #取得订单信息，每个订单ID按,拼接
  DECLARE v_orderby_str VARCHAR(1000) DEFAULT '';
  DECLARE v_orderby_name VARCHAR(1000) DEFAULT '';
  DECLARE v_where_str VARCHAR(3000) DEFAULT '';
  DECLARE v_channel_id_str VARCHAR(4000) DEFAULT '';
  DECLARE v_channel_condition VARCHAR(4000) DEFAULT '';
  DECLARE v_province_id_condition  VARCHAR(2000) DEFAULT '';
  #DECLARE v_channel_product_province_id INT;
  DECLARE v_channel_product_pc_condition  VARCHAR(2000) DEFAULT '';
  DECLARE v_is_discount INT DEFAULT 0;
  DECLARE v_object_id BIGINT;
     
  
  IF p_user_type = 1 THEN #代理商
    SET v_object_id = p_proxy_id; 
  ELSEIF p_user_type = 2 THEN #企业
    SET v_object_id = p_enterprise_id;      
  END IF; 
  SET v_is_discount = f_get_is_discount(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id,p_size);
  IF p_operator_id = 1 THEN
    SET v_orderby_name = ',c.discount_mobile ASC';
  ELSEIF p_operator_id = 2 THEN
    SET v_orderby_name = ',c.discount_unicom ASC';
  ELSEIF p_operator_id = 3 THEN
    SET v_orderby_name = ',c.discount_telecom ASC';
  END IF;
  
  SET v_orderby_str = CONCAT('ORDER BY IF(cp.`city_id` > 0,cp.`city_id`,0) DESC, IF(c.`city_id` > 0,c.`city_id`,0) DESC,c.`province_id` DESC'
  ,'\r ,f_get_channel_discount(c.channel_id,',p_operator_id,',c.province_id,c.city_id,',v_object_id,',',p_user_type,',cp.size) ASC',v_orderby_name);
  
  SET v_orderby_str = CONCAT('ORDER BY IF(cp.`city_id` > 0,cp.`city_id`,0) DESC, IF(c.`city_id` > 0,c.`city_id`,0) DESC,c.`province_id` DESC'
    ,'\r,f_get_channel_discount(c.channel_id,',p_operator_id,',c.province_id,c.city_id,',v_object_id,',',p_user_type,',cp.size) ASC'
    ,v_orderby_name);

  IF p_is_filter = 1 THEN
    SET v_channel_condition = '\r AND c.is_filter = 1';
  ELSE
    IF p_user_type = 1 THEN #代理商
      SELECT GROUP_CONCAT(cu.`channel_id`) channel_id_str 
      INTO v_channel_id_str
      FROM `t_flow_channel_user` cu 
      WHERE cu.`user_type` = p_user_type
      AND cu.`proxy_id` = p_proxy_id; 
    ELSEIF p_user_type = 2 THEN #企业
      SELECT GROUP_CONCAT(cu.`channel_id`) channel_id_str 
      INTO v_channel_id_str
      FROM `t_flow_channel_user` cu 
      WHERE cu.`user_type` = p_user_type 
      AND cu.`enterprise_id` = p_enterprise_id;     
    END IF; 
    
    IF v_channel_id_str IS NULL OR v_channel_id_str = '' THEN
      SET v_channel_condition = CONCAT('\r AND 1<>1');
    ELSE
      SET v_channel_condition = CONCAT('\r AND c.is_filter = 0 AND c.channel_id IN (',v_channel_id_str,')');
    END IF;
  END IF;

  #通道产品省份ID
  #SET v_is_discount = 2;
  IF p_range = 0 THEN #代表全国
    IF v_is_discount IN(1,3) THEN #市
      SET v_province_id_condition = CONCAT('\r AND (c.province_id = 1 AND c.city_id = ',p_city_id,')');
      SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = 1 AND cp.city_id = ',p_city_id,')');  
    ELSEIF v_is_discount IN(2,4) THEN #省
      
      SET v_province_id_condition = CONCAT('\r AND (c.province_id = ',p_province_id,' OR c.city_id = ',p_city_id,')');
      SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = 1 AND cp.city_id IN (0,',p_city_id,'))');
      
    ELSE
      SET v_province_id_condition = CONCAT('\r AND (c.province_id IN (1,',p_province_id,') OR c.city_id = ',p_city_id,')');
      SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = 1 AND cp.city_id IN(0,',p_city_id,'))');  
    END IF;
    
  ELSEIF p_range = 1 THEN #代表省内
    IF v_is_discount IN(1,3) THEN #市
      SET v_province_id_condition = CONCAT('\r AND (c.province_id = ',p_province_id,' AND c.city_id = ',p_city_id,')');
      SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = ',p_province_id,' AND cp.city_id = ',p_city_id,')');
    ELSEIF v_is_discount IN(2,4) THEN #省
      SET v_province_id_condition = CONCAT('\r AND (c.province_id IN (0, ',p_province_id,') AND c.city_id IN (0,',p_city_id,'))');
      SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = ',p_province_id,'  AND cp.city_id = 0)');
    ELSE
      SET v_province_id_condition = CONCAT('\r AND (c.province_id IN (1,',p_province_id,') OR c.city_id = ',p_city_id,')');
      SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = ',p_province_id,' AND cp.city_id IN(0,',p_city_id,'))');  
    END IF;
    
  ELSEIF p_range = 2 THEN #代表市内
    SET v_channel_product_pc_condition = CONCAT('\r AND (cp.city_id = ',p_city_id,' AND cp.province_id = 0)');
    SET v_province_id_condition = CONCAT('\r AND (c.city_id = ',p_city_id,' AND c.province_id = 0)');
  END IF; 
  #SELECT f_get_channel_discount(p_channel_id,p_operator_id,p_province_id,p_city_id,p_user_id,p_user_type,p_size);
  SET @strsql=CONCAT('INSERT INTO temp_order_product(product_id ,product_name ,channel_id ,operator_id ,province_id,city_id '
  ,'\r ,price ,discount ,number,size,channel_name,channel_province_id'
  ,'\r ,channel_code,channel_city_id)'
  ,'\r SELECT cp.`product_id`,cp.`product_name`,cp.`channel_id`,cp.`operator_id`,cp.`province_id`,cp.city_id'
  ,'\r ,cp.`price`,cp.`discount`,cp.`number`,cp.`size`, c.channel_name,c.province_id channel_province_id'
  ,'\r ,c.channel_code,c.city_id'
  ,'\r FROM t_flow_channel_product cp'
  ,'\r INNER JOIN t_flow_channel c'
  ,'\r ON cp.channel_id = c.channel_id'
  ,'\r WHERE c.status = 1'
  ,'\r AND cp.status = 1'
  ,'\r AND cp.size = ',p_size
  ,'\r AND cp.operator_id = ',p_operator_id
  ,v_province_id_condition
  ,v_channel_product_pc_condition
  ,v_channel_condition
  ,'\r ',v_orderby_str,' LIMIT ',p_limit_count);
  SELECT @strsql;
  PREPARE stmtsql FROM @strsql; 
  EXECUTE stmtsql; 
  DEALLOCATE PREPARE stmtsql;
  
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_order_product_lk
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_order_product_lk`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_order_product_lk`(IN p_size INT
,IN p_operator_id INT
,IN p_province_id INT 
,IN p_city_id INT
,IN p_user_type INT
,IN p_proxy_id BIGINT
,IN p_enterprise_id BIGINT
,IN p_range INT #0代表全国 1省内
,IN p_is_filter INT
,IN p_is_cache INT
,IN p_is_channel_cache INT#通道缓存标识
,IN p_limit_count INT)
BEGIN 
	/*
	author		cxw
	date		2016/05/23
	desc		获取订单产品信息
			2016/06/27 by cxw 增加订单缓存需求，当帐户余额不足将订单缓存 
			2016/07/12 by cxw 增加通道省网折扣控制，如果通道省网设置折扣，智能分流不选择全国通道
			2016/09/09 用户产品折扣判断 by lk
			2016/09/26 去除上游通道余额不足判断 by lk
	*/
	#取得订单信息，每个订单ID按,拼接
	DECLARE v_orderby_str VARCHAR(1000) DEFAULT '';
	DECLARE v_orderby_name VARCHAR(1000) DEFAULT '';
	DECLARE v_where_str VARCHAR(3000) DEFAULT '';
	DECLARE v_channel_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_channel_condition VARCHAR(4000) DEFAULT '';
	DECLARE v_province_id_condition  VARCHAR(2000) DEFAULT '';
	#DECLARE v_channel_product_province_id INT;
	DECLARE v_surplus_province_money_condition VARCHAR(3000) DEFAULT '';
	DECLARE v_channel_product_pc_condition  VARCHAR(2000) DEFAULT '';
	DECLARE v_is_discount INT DEFAULT 0;
	DECLARE v_object_id BIGINT;
		 
	
	IF p_user_type = 1 THEN	#代理商
		SET v_object_id = p_proxy_id;	
	ELSEIF p_user_type = 2 THEN #企业
		SET v_object_id = p_enterprise_id;			
	END IF;	
	SET v_is_discount = f_get_is_discount(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id,p_size);
	IF p_operator_id = 1 THEN
		SET v_orderby_name = ',c.discount_mobile ASC';
	ELSEIF p_operator_id = 2 THEN
		SET v_orderby_name = ',c.discount_unicom ASC';
	ELSEIF p_operator_id = 3 THEN
		SET v_orderby_name = ',c.discount_telecom ASC';
	END IF;
	
	SET v_orderby_str = CONCAT('ORDER BY IF(cp.`city_id` > 0,cp.`city_id`,0) DESC, IF(c.`city_id` > 0,c.`city_id`,0) DESC,c.`province_id` DESC'
	,'\r ,f_get_channel_discount(c.channel_id,',p_operator_id,',c.province_id,c.city_id,',v_object_id,',',p_user_type,',cp.size) ASC',v_orderby_name);
	
	#判断企业是否进行订单缓存
	IF p_is_cache = 0 OR p_is_channel_cache = 0 THEN
		SET v_orderby_str = CONCAT('ORDER BY IF(cp.`city_id` > 0,cp.`city_id`,0) DESC, IF(c.`city_id` > 0,c.`city_id`,0) DESC,c.`province_id` DESC'
			,'\r ,f_get_channel_discount(c.channel_id,',p_operator_id,',c.province_id,c.city_id,',v_object_id,',',p_user_type,',cp.size) ASC'
			,v_orderby_name);
	
		#SET v_surplus_province_money_condition = CONCAT('\r AND (ca.surplus_money IS NULL OR ca.surplus_money >= cp.price * 2)','\r AND (cpc.province_money IS NULL OR cpc.province_money >= cp.price * 2)');
		#去除通道余额限制
		SET v_surplus_province_money_condition = CONCAT('\r AND (cpc.province_money IS NULL OR cpc.province_money >= cp.price * 2)');
	ELSE 
		SET v_orderby_str = CONCAT('ORDER BY IF(cp.`city_id` > 0,cp.`city_id`,0) DESC, IF(c.`city_id` > 0,c.`city_id`,0) DESC,c.`province_id` DESC'
			,'\r,IF(cpc.province_money < 10,0,1) + IF(ca.surplus_money < 10,0,1) DESC '
			#,'\r,IF(cpc.province_money < 10,0,1)  DESC '
			,'\r,f_get_channel_discount(c.channel_id,',p_operator_id,',c.province_id,c.city_id,',v_object_id,',',p_user_type,',cp.size) ASC'
			,v_orderby_name);
	
		SET v_orderby_str = CONCAT(v_orderby_str,'\r,ca.surplus_money DESC,cpc.province_money DESC');
		SET v_surplus_province_money_condition = '';
	END IF;
	
	IF p_is_filter = 1 THEN
		SET v_channel_condition = '\r AND c.is_filter = 1';
	ELSE
		IF p_user_type = 1 THEN	#代理商
			SELECT GROUP_CONCAT(cu.`channel_id`) channel_id_str 
			INTO v_channel_id_str
			FROM `t_flow_channel_user` cu 
			WHERE cu.`user_type` = p_user_type
			AND cu.`proxy_id` = p_proxy_id;	
		ELSEIF p_user_type = 2 THEN #企业
			SELECT GROUP_CONCAT(cu.`channel_id`) channel_id_str 
			INTO v_channel_id_str
			FROM `t_flow_channel_user` cu 
			WHERE cu.`user_type` = p_user_type 
			AND cu.`enterprise_id` = p_enterprise_id;			
		END IF;	
		
		IF v_channel_id_str IS NULL OR v_channel_id_str = '' THEN
			SET v_channel_condition = CONCAT('\r AND c.is_filter = 0 AND 1<>1');
		ELSE
			SET v_channel_condition = CONCAT('\r AND c.is_filter = 0 AND c.channel_id IN (',v_channel_id_str,')');
		END IF;
	END IF;
	#通道产品省份ID
	#SET v_is_discount = 2;
	IF p_range = 0 THEN #代表全国		
		IF v_is_discount IN(1,3) THEN #市
			SET v_province_id_condition = CONCAT('\r AND (c.province_id = 1 AND c.city_id = ',p_city_id,')');
			SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = 1 AND cp.city_id = ',p_city_id,')');	
		ELSEIF v_is_discount IN(2,4) THEN #省
			
			SET v_province_id_condition = CONCAT('\r AND (c.province_id = ',p_province_id,' OR c.city_id = ',p_city_id,')');
			SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = 1 AND cp.city_id IN (0,',p_city_id,'))');
			
		ELSE
			SET v_province_id_condition = CONCAT('\r AND (c.province_id IN (1,',p_province_id,') OR c.city_id = ',p_city_id,')');
			SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = 1 AND cp.city_id IN(0,',p_city_id,'))');	
		END IF;
		
	ELSEIF p_range = 1 THEN #代表省内
		IF v_is_discount IN(1,3) THEN #市
			SET v_province_id_condition = CONCAT('\r AND (c.province_id = ',p_province_id,' AND c.city_id = ',p_city_id,')');
			SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = ',p_province_id,' AND cp.city_id = ',p_city_id,')');
		ELSEIF v_is_discount IN(2,4) THEN #省
			SET v_province_id_condition = CONCAT('\r AND (c.province_id IN (0, ',p_province_id,') AND c.city_id IN (0,',p_city_id,'))');
			SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = ',p_province_id,'  AND cp.city_id = 0)');
		ELSE
			SET v_province_id_condition = CONCAT('\r AND (c.province_id IN (1,',p_province_id,') OR c.city_id = ',p_city_id,')');
			SET v_channel_product_pc_condition = CONCAT('\r AND (cp.province_id = ',p_province_id,' AND cp.city_id IN(0,',p_city_id,'))');	
		END IF;
		
	ELSEIF p_range = 2 THEN #代表市内
		SET v_channel_product_pc_condition = CONCAT('\r AND (cp.city_id = ',p_city_id,' AND cp.province_id = 0)');
		SET v_province_id_condition = CONCAT('\r AND (c.city_id = ',p_city_id,' AND c.province_id = 0)');
	END IF;	
	#SELECT f_get_channel_discount(p_channel_id,p_operator_id,p_province_id,p_city_id,p_user_id,p_user_type,p_size);
	SET @strsql=CONCAT('INSERT INTO temp_order_product(product_id ,product_name ,channel_id ,operator_id ,province_id,city_id '
	,'\r ,price ,discount ,number,size,channel_name,channel_province_id,surplus_money,province_money'
	,'\r ,channel_code,channel_city_id)'
	,'\r SELECT cp.`product_id`,cp.`product_name`,cp.`channel_id`,cp.`operator_id`,cp.`province_id`,cp.city_id'
	,'\r ,cp.`price`,cp.`discount`,cp.`number`,cp.`size`, c.channel_name,c.province_id channel_province_id,ca.surplus_money,cpc.province_money'
	,'\r ,c.channel_code,c.city_id'
	,'\r FROM t_flow_channel_product cp'
	,'\r INNER JOIN t_flow_channel c'
	,'\r ON cp.channel_id = c.channel_id'
	,'\r LEFT JOIN t_flow_channel_account ca'
	,'\r ON ca.account_id = c.account_id'
	,'\r LEFT JOIN t_flow_channel_province cpc'
	,'\r ON cpc.channel_id = c.channel_id'
	,'\r AND cpc.province_id = cp.province_id'
	,'\r WHERE c.status = 1'
	,'\r AND cp.status = 1'
	,'\r AND cp.size = ',p_size
	,'\r AND cp.operator_id = ',p_operator_id
	,v_province_id_condition
	,v_surplus_province_money_condition
	,v_channel_product_pc_condition
	,v_channel_condition
	,'\r ',v_orderby_str,' LIMIT ',p_limit_count);
	SELECT @strsql;
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_order_status
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_order_status`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_order_status`(
IN p_order_code VARCHAR(200)
,IN p_user_type INT
,IN p_user_id BIGINT
)
BEGIN 
	/*
	author		cxw
	date		2016/08/16
	desc		获取订单状态信息
	*/
	IF p_user_type = 1 THEN		
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order_cache` 
		WHERE `order_code` = p_order_code  AND `proxy_id` = p_user_id
		UNION ALL
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order_cache` 
		WHERE  `orderno_id` = p_order_code AND `proxy_id` = p_user_id
		UNION ALL
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order_pre` 
		WHERE `order_code` = p_order_code  AND `proxy_id` = p_user_id
		UNION ALL
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order_pre` 
		WHERE  `orderno_id` = p_order_code AND `proxy_id` = p_user_id
		UNION ALL		
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order` 
		WHERE `orderno_id` = p_order_code AND `proxy_id` = p_user_id
		UNION ALL
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order` 
		WHERE `order_code` = p_order_code  AND `proxy_id` = p_user_id;
	ELSEIF p_user_type = 2 THEN		
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order_cache` 
		WHERE `order_code` = p_order_code  AND `enterprise_id` = p_user_id
		UNION ALL
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order_cache` 
		WHERE  `orderno_id` = p_order_code AND `enterprise_id` = p_user_id
		UNION ALL
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order_pre` 
		WHERE `order_code` = p_order_code  AND `enterprise_id` = p_user_id
		UNION ALL
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order_pre` 
		WHERE  `orderno_id` = p_order_code AND `enterprise_id` = p_user_id
		UNION ALL		
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order` 
		WHERE `orderno_id` = p_order_code AND `enterprise_id` = p_user_id
		UNION ALL
		SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc,orderno_id 
		FROM `t_flow_order` 
		WHERE `order_code` = p_order_code  AND `enterprise_id` = p_user_id;
	END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_proxy_child
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_proxy_child`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_proxy_child`(IN p_proxy_id BIGINT,IN p_nDepth INT)
BEGIN
      DECLARE v_done INT DEFAULT 0;   
      DECLARE v_proxy_id BIGINT;
      
      DECLARE v_cur CURSOR FOR 
      SELECT px.proxy_id
      FROM t_flow_proxy px      
      WHERE px.status IN (0,1) 
      AND px.top_proxy_id = p_proxy_id;
      
      DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;   
      SET max_sp_recursion_depth=12;   
       
      INSERT INTO temp_proxy_all_list(proxy_id ,depth) VALUES (p_proxy_id,p_nDepth);   
       
      OPEN v_cur;   
       
      FETCH v_cur INTO v_proxy_id;   
      WHILE v_done = 0 DO   
              CALL p_get_proxy_child(v_proxy_id,p_nDepth+1);
              FETCH v_cur INTO v_proxy_id;   
      END WHILE;   
       
      CLOSE v_cur;  
    END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_stat_common
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_stat_common`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_stat_common`(	
	IN p_stat_type INT # 统计类型
	,IN p_stat_status INT #统计状态
	,IN p_start_time BIGINT #开始时间 
	,IN p_end_time BIGINT #结束时间 
	,IN p_table VARCHAR(100) #统计表名
	,IN p_stat_column VARCHAR(4000) #统计列名
	,IN p_other_column VARCHAR(4000) #其它显示列名
	,IN p_stat_group VARCHAR(4000) #统计分组列
	,IN p_condition VARCHAR(4000) #条件
	,IN p_ispage INT #是否分页查询
	,IN p_pageIndex INT
	,IN p_pageSize INT
	,OUT p_total_count INT
)
BEGIN
	/*
	author		cxw
	date		2016/08/08
	desc		统计公用查询对外接口(分页查询)			
	*/
	DECLARE v_stat_column_str VARCHAR(4000) DEFAULT '';
	DECLARE v_other_column_str VARCHAR(4000) DEFAULT '';	
	DECLARE v_group_column_str VARCHAR(4000) DEFAULT '';	
	DECLARE v_condition_str VARCHAR(4000) DEFAULT '';
	DECLARE v_page_str VARCHAR(300) DEFAULT '';	
	#合并显示统计列
	SET v_stat_column_str = f_get_column(p_stat_column,',','SUM');
	#合并其它列
	SET v_other_column_str = f_get_column(p_other_column,',','MAX');
	#合并分组列
	SET v_group_column_str = f_get_column(p_stat_group,',',NULL);
	#select v_other_column_str;
	IF p_condition IS NOT NULL THEN
		SET v_condition_str = CONCAT('\r',p_condition);
	END IF;	
	IF p_ispage = 1 THEN
		#统计个数
		SET @strsqlStat = CONCAT('SELECT SUM(sp.stat_count) total_count'
		,'\r','INTO  @total_count'
		,'\r','FROM ',p_table,' sp '
		,'\r','WHERE sp.`stat_type` = ',p_stat_type	
		,'\r','AND sp.stat_status = ',p_stat_status
		,v_condition_str
		,'\r','AND sp.`stat_day` BETWEEN ''',p_start_time,''' AND ''',p_end_time,'''');
		
		#SELECT @strsqlStat;	
		PREPARE stmtsqlStat FROM @strsqlStat; 
		EXECUTE stmtsqlStat; 
		DEALLOCATE PREPARE stmtsqlStat;	
		SET p_total_count =  @total_count;
		SET v_page_str = CONCAT('LIMIT ',(p_pageIndex-1)*p_pageSize,',',p_pageSize);
	END IF;
	SET p_total_count = IFNULL(p_total_count,0);
	#分布查询
	SET @strsql = CONCAT('SELECT ',v_group_column_str
	,',',v_other_column_str
	,',',v_stat_column_str
	,'FROM ',p_table,' sp '
	,'\r','WHERE sp.`stat_type` = ',p_stat_type	
	,'\r','AND sp.stat_status = ',p_stat_status
	,v_condition_str
	,'\r','AND sp.`stat_day` BETWEEN ',p_start_time,' AND ',p_end_time
	,'\r','GROUP BY ',v_group_column_str
	,v_page_str);
	
	#select @strsql;
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql;
	
    END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_stat_order_home
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_stat_order_home`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_stat_order_home`(IN p_user_type INT # -1：管理员端 1：代理商端 2：企业端
	,IN p_pic_id INT # 0：饼图 1：曲线 2：柱状
	,IN p_begin_date DATE #查询开始时间
	,IN p_end_date DATE #查询结束时间
	,IN p_operator_id INT # 0:全部 1：移动 2：联通 3：电信
	,IN p_user_id BIGINT)
BEGIN
	/*
	author		lk
	date		2016/08/05
	desc		获取首页曲线数据
			
	*/
	DECLARE v_table_name VARCHAR(500) DEFAULT ''; #表名1
	DECLARE v_table_name_again VARCHAR(500) DEFAULT '';#表名1
	DECLARE v_field VARCHAR(500) DEFAULT ''; #字段1
	DECLARE v_field_again VARCHAR(500) DEFAULT '';#字段2
	DECLARE v_group_str VARCHAR(500) DEFAULT '';#group by 
	DECLARE v_operator_id VARCHAR(500) DEFAULT '';#运营商
	DECLARE v_user_id VARCHAR(500) DEFAULT ''; #用户ID2  0：管理员端 >0 代理ID/企业ID
	DECLARE v_user_id_again VARCHAR(500) DEFAULT '';#用户ID2 0：管理员端 >0 代理ID/企业ID
	DECLARE v_sql_str VARCHAR(4000) DEFAULT '';
	DECLARE v_begin_date BIGINT;
	DECLARE v_end_date BIGINT;
	
	SET v_begin_date =DATE_FORMAT(p_begin_date,'%Y%m%d');
	SET v_end_date = DATE_FORMAT(p_end_date,'%Y%m%d');
	
	#判断平台，设置表名和用户ID
	IF p_user_type =1 THEN 
		SET v_table_name ='t_flow_stat_proxy';
		SET v_user_id = CONCAT('\r AND proxy_id = ',p_user_id);
	ELSEIF p_user_type = 2 THEN
		SET v_table_name ='t_flow_stat_enterprise';
		SET v_user_id = CONCAT('\r AND enterprise_id = ',p_user_id);
	ELSE
		SET v_table_name ='t_flow_stat_proxy';
		SET v_table_name_again='t_flow_stat_enterprise';
		SET v_user_id = CONCAT('\r AND proxy_id >= ',p_user_id);
		SET v_user_id_again = CONCAT('\r AND enterprise_id >= ',p_user_id);
	END IF;
	
	#判断图型，设置获取字段、GROUP BY 、运营商ID
	IF p_pic_id =0 THEN
		SET v_field = CONCAT('CASE operator_id WHEN 1 THEN ','''中国移动''',' WHEN 2  THEN ','''中国联通''',' WHEN 3 THEN  ','''中国电信''',' ELSE ','''其他''',' END AS operator_id,SUM(stat_count) stat_count');
		SET v_field_again = CONCAT('operator_id,SUM(stat_count) stat_count');
		SET v_group_str ='\r GROUP BY operator_id ';
		SET v_operator_id ='\r AND operator_id IN(1,2,3)';
	ELSEIF p_pic_id=1 THEN
		SET v_field=CONCAT('CASE operator_id WHEN 1 THEN ','''中国移动''',' WHEN 2  THEN ','''中国联通''',' WHEN 3 THEN  ','''中国电信''',' ELSE ','''其他''',' END AS operator_id,SUM(stat_size) stat_size');
		SET v_field_again = CONCAT('operator_id,SUM(stat_size) stat_size');
		SET v_group_str='\r GROUP BY operator_id ';
		SET v_operator_id ='\r AND operator_id IN(1,2,3)';
	ELSEIF p_pic_id=2 THEN
		SET v_field='province_name,province_id,SUM(stat_count) stat_count';
		SET v_field_again = 'province_name,province_id,SUM(stat_count) stat_count';
		SET v_group_str='\r GROUP BY province_name,province_id ';
		
		SET v_operator_id = CONCAT('\r AND operator_id =',p_operator_id,'');
	END IF;
	
	IF p_user_type = -1 THEN
		SET v_sql_str =CONCAT(' SELECT  ',v_field_again
		,' \r FROM ( SELECT  ',v_field,' FROM  ', v_table_name 
		,' \r WHERE stat_type=1 AND stat_status=205 AND stat_day BETWEEN  ',v_begin_date,' AND ',v_end_date,'' ,v_operator_id,v_user_id,v_group_str
		,' \r UNION ALL SELECT  ',v_field,' FROM ', v_table_name_again 
		,' \r WHERE stat_type=1 AND stat_status=205 AND stat_day BETWEEN  ',v_begin_date,' AND ',v_end_date,'' ,v_operator_id,v_user_id_again,v_group_str,') a ',v_group_str);
       # SELECT v_sql_str;	
	ELSE 
		SET v_sql_str = CONCAT('SELECT ',v_field,' FROM ', v_table_name 
		,' \r WHERE stat_type=1 AND stat_status=205 AND stat_day BETWEEN  ',v_begin_date,' AND ',v_end_date,'' ,v_operator_id,v_user_id,v_group_str);
	END IF;
	#SELECT v_sql_str;
	SET @strsql=v_sql_str;
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql;
	
    END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_stat_order_home_old
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_stat_order_home_old`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_stat_order_home_old`(IN p_user_type INT # -1：管理员端 1：代理商端 2：企业端
	,IN p_pic_id INT # 0：饼图 1：曲线 2：柱状
	,IN p_begin_date DATE #查询开始时间
	,IN p_end_date DATE #查询结束时间
	,IN p_operator_id INT # 0:全部 1：移动 2：联通 3：电信
	,IN p_user_id BIGINT)
BEGIN
	/*
	author		lk
	date		2016/08/05
	desc		获取首页曲线数据
			
	*/
	DECLARE v_table_name VARCHAR(500) DEFAULT ''; #表名1
	DECLARE v_table_name_again VARCHAR(500) DEFAULT '';#表名1
	DECLARE v_field VARCHAR(500) DEFAULT ''; #字段1
	DECLARE v_field_again VARCHAR(500) DEFAULT '';#字段2
	DECLARE v_group_str VARCHAR(500) DEFAULT '';#group by 
	DECLARE v_operator_id VARCHAR(500) DEFAULT '';#运营商
	DECLARE v_user_id VARCHAR(500) DEFAULT ''; #用户ID2  0：管理员端 >0 代理ID/企业ID
	DECLARE v_user_id_again VARCHAR(500) DEFAULT '';#用户ID2 0：管理员端 >0 代理ID/企业ID
	DECLARE v_sql_str VARCHAR(4000) DEFAULT '';
	DECLARE v_begin_date BIGINT;
	DECLARE v_end_date BIGINT;
	
	SET v_begin_date =DATE_FORMAT(p_begin_date,'%Y%m%d');
	SET v_end_date = DATE_FORMAT(p_end_date,'%Y%m%d');
	
	#判断平台，设置表名和用户ID
	IF p_user_type =1 THEN 
		SET v_table_name ='t_flow_stat_proxy';
		SET v_user_id = CONCAT('\r AND proxy_id = ',p_user_id);
	ELSEIF p_user_type = 2 THEN
		SET v_table_name ='t_flow_stat_enterprise';
		SET v_user_id = CONCAT('\r AND enterprise_id = ',p_user_id);
	ELSE
		SET v_table_name ='t_flow_stat_proxy';
		SET v_table_name_again='t_flow_stat_enterprise';
		SET v_user_id = CONCAT('\r AND proxy_id >= ',p_user_id);
		SET v_user_id_again = CONCAT('\r AND enterprise_id >= ',p_user_id);
	END IF;
	
	#判断图型，设置获取字段、GROUP BY 、运营商ID
	IF p_pic_id =0 THEN
		SET v_field = 'operator_id,SUM(stat_size) size';
		SET v_field_again = ' operator_id,SUM(size) size';
		SET v_group_str ='\r GROUP BY operator_id ';
		SET v_operator_id ='\r AND operator_id IN(1,2,3)';
	ELSEIF p_pic_id=1 THEN
		SET v_field='stat_day,SUM(stat_size) size';
		SET v_field_again = 'stat_day,SUM(size) size';
		SET v_group_str='\r GROUP BY stat_day ';
		SET v_operator_id = CONCAT('\r AND operator_id IN(',p_operator_id,')');
	ELSEIF p_pic_id=2 THEN
		SET v_field='province_name,province_id,SUM(stat_size) size';
		SET v_field_again = 'province_name,province_id,SUM(size) size';
		SET v_group_str='\r GROUP BY province_name,province_id ';
		SET v_operator_id ='\r AND operator_id IN(1,2,3)';
	END IF;
	
	IF p_user_type = -1 THEN
		SET v_sql_str =CONCAT(' SELECT  ',v_field_again
		,' \r FROM ( SELECT  ',v_field,' FROM  ', v_table_name 
		,' \r WHERE stat_type=1 AND stat_status=205 AND stat_day BETWEEN  ',v_begin_date,' AND ',v_end_date,'' ,v_operator_id,v_user_id,v_group_str
		,' \r UNION ALL SELECT  ',v_field,' FROM ', v_table_name_again 
		,' \r WHERE stat_type=1 AND stat_status=205 AND stat_day BETWEEN  ',v_begin_date,' AND ',v_end_date,'' ,v_operator_id,v_user_id_again,v_group_str,') a ',v_group_str);
       # SELECT v_sql_str;	
	ELSE 
		SET v_sql_str = CONCAT('SELECT ',v_field,' FROM ', v_table_name 
		,' \r WHERE stat_type=1 AND stat_status=205 AND stat_day BETWEEN  ',v_begin_date,' AND ',v_end_date,'' ,v_operator_id,v_user_id,v_group_str);
	END IF;
	#select v_sql_str;
	SET @strsql=v_sql_str;
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql;
	
    END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_get_user_proxy
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_get_user_proxy`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_get_user_proxy`(IN p_user_id BIGINT)
BEGIN
	DECLARE v_done INT DEFAULT 0; 
	DECLARE v_proxy_id INT;	
	
	DECLARE v_proxy_cur CURSOR FOR 
	SELECT pu.proxy_id
	FROM t_flow_proxy_user pu
	WHERE pu.user_id =  p_user_id;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1; 
	
	OPEN v_proxy_cur;
	FETCH v_proxy_cur INTO v_proxy_id;
	WHILE v_done = 0 DO   
	      CALL p_get_proxy_child(v_proxy_id,0);
	      FETCH v_proxy_cur INTO v_proxy_id;   
	END WHILE;
	CLOSE v_proxy_cur; 
 END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_monitor_get_channel_exception
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_monitor_get_channel_exception`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_monitor_get_channel_exception`(
  IN p_start_time INT
  ,IN p_end_time INT
)
BEGIN 
	/*
	author		cxw
	date		2016/07/26
	desc		获取通道订单监控数据
	*/
	SELECT mcs.channel_id
	,mcs.stat_time
	,mcs.channel_code
	,mcs.channel_name
	,mcs.stat_time
	,IFNULL(SUM(mcs.success_count),0) success_count
	,IFNULL(SUM(mcs.success_amount),0) success_amount
	,CONCAT(ROUND((SUM(mcs.success_count) / SUM(total_count))*100,3),'%') success_rate
	,IFNULL(SUM(mcs.faile_count),0) faile_count
	,IFNULL(SUM(mcs.faile_amount),0) faile_amount
	,CONCAT(ROUND((SUM(mcs.faile_count) / SUM(total_count))*100,3),'%') faile_rate
	FROM `t_flow_monitor_channel_stat` mcs
	WHERE mcs.stat_time BETWEEN  p_start_time AND p_end_time
	AND total_count > 0
	GROUP BY mcs.channel_id,mcs.stat_time,mcs.channel_code,mcs.channel_name
	ORDER BY mcs.channel_id,mcs.stat_time;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_monitor_get_order_channel
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_monitor_get_order_channel`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_monitor_get_order_channel`(IN p_start_day INT
  ,IN p_end_day INT
  ,IN p_channel_id BIGINT
  ,IN p_operator_id INT
  ,IN p_province_id INT)
BEGIN 
	/*
	author		cxw
	date		2016/07/26
	desc		获取通道订单监控数据
		channel_code : A 中国移动  B 中国联通  C 中国电信
	*/
	
	SELECT c.`channel_id`
	,c.`channel_code`
	,c.`channel_name`
	,IFNULL(oc.wait_count,0) wait_count
	,IFNULL(oc.wait_amount,0) wait_amount
	,IFNULL(oc.submit_success_count,0) submit_success_count
	,IFNULL(oc.submit_success_amount,0) submit_success_amount
	,IFNULL(cs.success_count,0) success_count
	,IFNULL(cs.success_amount,0) success_amount
	,IFNULL(cs.faile_count,0) faile_count
	,IFNULL(cs.faile_amount,0) faile_amount
	FROM (
		SELECT * FROM `t_flow_channel` cc 
		WHERE (cc.`channel_id` = p_channel_id OR p_channel_id = -1)
		AND (cc.`province_id` = p_province_id OR p_province_id = -1)
		AND (SUBSTR(cc.`channel_code`,1,1) = CASE p_operator_id WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' END OR p_operator_id = -1)
	) c LEFT JOIN
	(
		SELECT mcs.`channel_id`
		,SUM(mcs.`success_count`) success_count
		,SUM(mcs.success_amount) success_amount
		,SUM(mcs.`faile_count`) faile_count
		,SUM(mcs.`faile_amount`) faile_amount
		FROM t_flow_monitor_channel_stat mcs ,t_flow_channel c
		WHERE mcs.channel_id = c.channel_id
		AND mcs.`stat_day` BETWEEN  p_start_day AND  p_end_day
		AND mcs.`total_count` > 0
		AND (c.`channel_id` = p_channel_id OR p_channel_id = -1)
		AND (c.`province_id` = p_province_id OR p_province_id = -1)
		AND (SUBSTR(c.`channel_code`,1,1) = CASE p_operator_id WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' END OR p_operator_id = -1)
		GROUP BY mcs.`channel_id`
	) cs ON c.`channel_id` = cs.channel_id
	LEFT JOIN
	(
		SELECT CASE od.order_status 
			WHEN 0 THEN od.`channel_id` 
			WHEN 3 THEN od.back_channel_id
			WHEN 1 THEN od.`channel_id` 
			WHEN 4 THEN od.back_channel_id END channel_id
		,SUM(CASE WHEN od.order_status IN (0,3) THEN 1 ELSE 0 END) wait_count
		,SUM(CASE WHEN od.order_status IN (0,3) THEN od.`discount_price` ELSE 0 END) wait_amount
		,SUM(CASE WHEN od.order_status IN (1,4) THEN 1 ELSE 0 END) submit_success_count
		,SUM(CASE WHEN od.order_status IN (1,4) THEN od.`discount_price` ELSE 0 END) submit_success_amount
		FROM `t_flow_order_pre` od
		WHERE  od.order_status IN (0,3,1,4)
		AND od.order_date BETWEEN DATE_FORMAT(p_start_day,'%Y-%m-%d') AND DATE_ADD(DATE_FORMAT(p_end_day,'%Y-%m-%d'), INTERVAL 1 DAY)
		GROUP BY CASE od.order_status 
			WHEN 0 THEN od.`channel_id` 
			WHEN 3 THEN od.back_channel_id
			WHEN 1 THEN od.`channel_id` 
			WHEN 4 THEN od.back_channel_id END 
	) oc
	ON c.`channel_id` = oc.channel_id
	WHERE IFNULL(oc.wait_count,0) + IFNULL(oc.submit_success_count,0) + IFNULL(cs.success_count,0) + IFNULL(cs.faile_count,0) > 0
	ORDER BY 9 DESC;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_monitor_get_order_province
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_monitor_get_order_province`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_monitor_get_order_province`(
  IN p_start_day INT
  ,IN p_end_day INT
)
BEGIN 
	/*
	author		cxw
	date		2016/07/26
	desc		获取通道订单监控数据
	*/
	SET @rank=0;  
	SET @last_success_count=-1;  
	
	SELECT @rank := @rank + IF(@last_success_count = cc.success_count,0,1) AS rank	
	,cc.province_id
	,cc.province_name
	,IFNULL(cc.wait_count,0) wait_count
	,IFNULL(cc.wait_amount,0) wait_amount
	,IFNULL(cc.submit_success_count,0) submit_success_count
	,IFNULL(cc.submit_success_amount,0) submit_success_amount
	,IFNULL(cc.success_count,0) success_count
	,IFNULL(cc.success_amount,0) success_amount
	,IFNULL(cc.faile_count,0) faile_count
	,IFNULL(cc.faile_amount,0) faile_amount
	,@last_success_count := success_count AS last_success_count
	FROM 
	(
		SELECT c.`province_id`
		,c.`province_name`
		,oc.wait_count
		,oc.wait_amount
		,oc.submit_success_count
		,oc.submit_success_amount
		,cs.success_count
		,cs.success_amount
		,cs.faile_count
		,cs.faile_amount
		FROM `t_flow_sys_province` c LEFT JOIN
		(
			SELECT mcs.`province_id`
			,SUM(mcs.`success_count`) success_count
			,SUM(mcs.success_amount) success_amount
			,SUM(mcs.`faile_count`) faile_count
			,SUM(mcs.`faile_amount`) faile_amount
			FROM t_flow_monitor_province_stat mcs 
			WHERE mcs.`stat_day` BETWEEN  p_start_day AND  p_end_day
			AND mcs.`total_count` > 0
			GROUP BY mcs.`province_id`
		) cs ON c.`province_id` = cs.province_id
		LEFT JOIN
		(
			SELECT od.`province_id` 
			,SUM(CASE WHEN od.order_status IN (0,3) THEN 1 ELSE 0 END) wait_count
			,SUM(CASE WHEN od.order_status IN (0,3) THEN od.`discount_price` ELSE 0 END) wait_amount
			,SUM(CASE WHEN od.order_status IN (1,4) THEN 1 ELSE 0 END) submit_success_count
			,SUM(CASE WHEN od.order_status IN (1,4) THEN od.`discount_price` ELSE 0 END) submit_success_amount
			FROM `t_flow_order_pre` od
			WHERE od.order_status IN (0,3,1,4)
			AND od.order_date BETWEEN DATE_FORMAT(p_start_day,'%Y-%m-%d') AND DATE_ADD(DATE_FORMAT(p_end_day,'%Y-%m-%d'), INTERVAL 1 DAY)
			GROUP BY od.`province_id`  
		) oc
		ON c.`province_id` = oc.province_id
		WHERE IFNULL(oc.wait_count,0) + IFNULL(oc.submit_success_count,0) + IFNULL(cs.success_count,0) + IFNULL(cs.faile_count,0) > 0
		ORDER BY cs.success_count DESC
	) cc;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_monitor_init_channel_stat
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_monitor_init_channel_stat`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_monitor_init_channel_stat`(
p_channel_id BIGINT
,OUT p_out_flag INT
)
BEGIN 
	/*
	author		cxw
	date		2016/07/25
	desc		通道订单监控初始化统计数据
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_log_id BIGINT;
	DECLARE v_stat_date DATE;
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	SET v_stat_date = DATE_ADD(CURDATE(),INTERVAL 1 DAY);
	#开始事务
	START TRANSACTION;
		#删除指定通道当天初始统计数据
		DELETE FROM t_flow_monitor_channel_stat 
		WHERE channel_id = p_channel_id 
		AND stat_day = DATE_FORMAT(CURDATE(),'%Y%m%d');
		#删除指定通道明天初始统计数据
		DELETE FROM t_flow_monitor_channel_stat 
		WHERE channel_id = p_channel_id 
		AND stat_day = DATE_FORMAT(v_stat_date,'%Y%m%d');
		#新增指定通道当天初始统计数据
		INSERT INTO t_flow_monitor_channel_stat(channel_id,channel_code,channel_name
		,stat_year,stat_month,stat_day,stat_time
		,total_count,success_count,success_amount,faile_count,faile_amount,rand_id)
		SELECT c.channel_id,c.channel_code,c.channel_name
		,YEAR(NOW()) stat_year,DATE_FORMAT(NOW(),'%Y%m') stat_month,DATE_FORMAT(NOW(),'%Y%m%d') stat_day
		,CONCAT(DATE_FORMAT(NOW(),'%Y%m%d'),sid.id) stat_time
		,0 total_count,0 success_count,0 success_amount,0 faile_count,0 faile_amount
		,rid.id
		FROM `t_flow_channel` c,(SELECT RIGHT(100+id - 1,2) id FROM t_flow_sys_id WHERE id <= 24) sid
		,(SELECT id FROM t_flow_sys_id WHERE id <= 5) rid
		WHERE c.channel_id = p_channel_id
		ORDER BY c.channel_id,stat_time;
		#新增指定通道明天初始统计数据
		INSERT INTO t_flow_monitor_channel_stat(channel_id,channel_code,channel_name
		,stat_year,stat_month,stat_day,stat_time
		,total_count,success_count,success_amount,faile_count,faile_amount,rand_id)
		SELECT c.channel_id,c.channel_code,c.channel_name
		,YEAR(v_stat_date) stat_year,DATE_FORMAT(v_stat_date,'%Y%m') stat_month,DATE_FORMAT(v_stat_date,'%Y%m%d') stat_day
		,CONCAT(DATE_FORMAT(v_stat_date,'%Y%m%d'),sid.id) stat_time
		,0 total_count,0 success_count,0 success_amount,0 faile_count,0 faile_amount
		,rid.id
		FROM `t_flow_channel` c,(SELECT RIGHT(100+id - 1,2) id FROM t_flow_sys_id WHERE id <= 24) sid
		,(SELECT id FROM t_flow_sys_id WHERE id <= 5) rid
		WHERE c.channel_id = p_channel_id
		ORDER BY c.channel_id,stat_time;
	IF t_error = 1 THEN 
		#回滚输出标志
		SET p_out_flag = 0;
		#订单回滚			
		ROLLBACK; 
	ELSE  
		#回滚输出标志
		SET p_out_flag = 1;
		#提交
		COMMIT; 
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_monitor_order_channel
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_monitor_order_channel`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_monitor_order_channel`(
IN p_channel_id BIGINT
,IN p_monitor_type INT
,IN p_rand_id INT
,IN p_order_date DATETIME
,IN p_price DECIMAL(11,3))
BEGIN 
	/*
	author		cxw
	date		2016/07/25 
	desc		订单监控
	parameter:
			p_monitor_type: 1--下单成功，2--下单失败
	*/
	DECLARE v_stat_time BIGINT;
	SET v_stat_time = DATE_FORMAT(p_order_date,'%Y%m%d%H');
	#select v_stat_time;
	IF p_monitor_type = 1 THEN #下单成功
		UPDATE LOW_PRIORITY t_flow_monitor_channel_stat
		SET total_count = total_count + 1
		,success_count = success_count + 1
		,success_amount = success_amount + p_price
		WHERE channel_id = p_channel_id
		AND stat_time = v_stat_time
		AND rand_id = p_rand_id;
	ELSEIF p_monitor_type = 2 THEN #下单失败
		UPDATE LOW_PRIORITY t_flow_monitor_channel_stat
		SET total_count = total_count + 1
		,faile_count = faile_count + 1
		,faile_amount = faile_amount + p_price
		WHERE channel_id = p_channel_id
		AND stat_time = v_stat_time
		AND rand_id = p_rand_id;
	END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_monitor_order_province
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_monitor_order_province`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_monitor_order_province`(
IN p_province_id BIGINT
,IN p_monitor_type INT
,IN p_rand_id INT
,IN p_order_date DATETIME
,IN p_price DECIMAL(11,3))
BEGIN 
	/*
	author		cxw
	date		2016/07/25 
	desc		订单监控
	parameter:
			p_monitor_type: 1--下单成功，2--下单失败
	*/
	DECLARE v_stat_time BIGINT;
	SET v_stat_time = DATE_FORMAT(p_order_date,'%Y%m%d%H');
	
	IF p_monitor_type = 1 THEN #下单成功
		UPDATE LOW_PRIORITY t_flow_monitor_province_stat
		SET total_count = total_count + 1
		,success_count = success_count + 1
		,success_amount = success_amount + p_price
		WHERE province_id = p_province_id
		AND stat_time = v_stat_time
		AND rand_id = p_rand_id;
	ELSEIF p_monitor_type = 2 THEN #下单失败
		UPDATE LOW_PRIORITY t_flow_monitor_province_stat
		SET total_count = total_count + 1
		,faile_count = faile_count + 1
		,faile_amount = faile_amount + p_price
		WHERE province_id = p_province_id
		AND stat_time = v_stat_time
		AND rand_id = p_rand_id;
	END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_query_order
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_query_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_query_order`(IN p_order_type INT ,
		IN p_user_id INT ,
		IN p_user_name VARCHAR(50),
		IN p_user_code VARCHAR(50),
		IN p_order_code VARCHAR(50),
		IN p_mobile VARCHAR(100),
		IN p_channel_id VARCHAR(4000),
		IN p_back_channel_id VARCHAR(4000),
		IN p_operator_id INT ,
		IN p_province_id INT ,
		IN p_order_status VARCHAR(50),
		IN p_start_order_date VARCHAR(50),
		IN p_end_order_date VARCHAR(50),
		IN p_product_name VARCHAR(50),
		IN p_sale_id BIGINT ,
		IN p_pageIndex INT ,
		IN p_pageSize INT ,
		IN p_is_count INT ,
		OUT p_total_count INT ,
		OUT p_success_count INT ,
		OUT p_success_amount DECIMAL(19 ,3),
		OUT p_faile_count INT ,
		OUT p_faile_amount DECIMAL(19 ,3),
		OUT p_wait_count INT ,
		OUT p_wait_amount DECIMAL(19 ,3),
		OUT p_submit_success_count INT ,
		OUT p_submit_success_amount DECIMAL(19 ,3),
		OUT p_success_price DECIMAL(19 ,3),
		OUT p_faile_price DECIMAL(19 ,3),
		OUT p_wait_price DECIMAL(19 ,3),
		OUT p_submit_success_price DECIMAL(19 ,3))
BEGIN  	
	DECLARE v_order_table VARCHAR(100);
	DECLARE v_proxy_enterprise_str VARCHAR(1000);
	#DECLARE v_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_id_title VARCHAR(1000);
	DECLARE v_channel_str VARCHAR(500) DEFAULT '';
	DECLARE v_back_channel_str VARCHAR(500) DEFAULT '';	
	#DECLARE v_inner_table VARCHAR(500) DEFAULT '';
	#DECLARE v_inner_title VARCHAR(4000) DEFAULT '';
	DECLARE v_dbowner VARCHAR(50) DEFAULT '';
	DECLARE v_proxy_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_query_days INT DEFAULT 1;
	DECLARE v_index_days INT DEFAULT 0;
	DECLARE v_start_day VARCHAR(100);
	DECLARE v_end_day VARCHAR(100);
	DECLARE v_query_null_count INT DEFAULT 0;#是否使用索引标识
	DECLARE v_end_order_date VARCHAR(100);#结束时间结尾加.999999
	DECLARE v_result_days INTEGER DEFAULT -1;#默认配置查询天数
	DECLARE v_exists_result INTEGER DEFAULT 0;
	DECLARE v_sql_hash_value BIGINT;
	#小时差
	DECLARE v_query_times INT DEFAULT 0;	
	
	SET @is_manager = 0;
	SET @user_type = 0;
	SET @proxy_id = 0;
	SET @is_all_proxy = 0;
	SET @is_all_enterprise = 0;
	SET @enterprise_id = 0;
	
	SET @totalStat = 0;
	SET @conditionStr = '';
	SET @conditionStatStr = '';
	SET @conditionSqlFullTextStr = '';
	SET @indexStr = '';
	#结束时间结尾加.999999
	SET v_end_order_date = CONCAT(p_end_order_date,'.999999');
	
	/*2
	SET v_inner_title = CONCAT('\r ,(SELECT dt.province_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) province_name'
				,'\r ,(SELECT dt.city_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) city_name');
	*/
	IF (p_order_type = 1) THEN 
		SET v_order_table = 't_flow_order_pre';
		SET v_channel_str = ' AND od.order_status IN (0,1)';
		SET v_back_channel_str = ' AND od.order_status IN (3,4)';
		set v_result_days = -999;
	ELSEIF (p_order_type = 2) THEN 
		SET v_order_table = 't_flow_order';
		SET v_channel_str = ' AND od.order_status IN (2,3)';
		SET v_back_channel_str = ' AND od.order_status IN (5,6)';
	ELSEIF (p_order_type = 3) THEN 
		SET v_order_table = 't_flow_order_cache';
		set v_result_days = -999;
	ELSEIF (p_order_type = 4) THEN 
		SET v_order_table = 't_flow_order_channel_cache';
		SET v_result_days = -999;
	ELSEIF (LENGTH(p_order_type) = 6) THEN 
		SET v_order_table = CONCAT('t_flow_order_',p_order_type);
		SET v_channel_str = ' AND od.order_status IN (2,3)';
		SET v_back_channel_str = ' AND od.order_status IN (5,6)';
	END IF; 
	#获取用户相关数据
	SELECT u.is_manager,u.user_type,u.proxy_id,u.is_all_proxy,u.is_all_enterprise,u.enterprise_id
	INTO @is_manager,@user_type,@proxy_id,@is_all_proxy,@is_all_enterprise,@enterprise_id
	FROM t_flow_sys_user u
	WHERE u.user_id = p_user_id;
	
	#创建代理商临时表
	DROP TEMPORARY TABLE IF EXISTS temp_proxy_all_list;	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_proxy_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,proxy_id BIGINT,depth INT);  
	#创建企业临时表
	DROP TEMPORARY TABLE IF EXISTS temp_enterprise_all_list;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_enterprise_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,enterprise_id BIGINT);  
	#创建统计临时表
	DROP TEMPORARY TABLE IF EXISTS temp_order_status_stat;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_status_stat    
	(sno INT PRIMARY KEY AUTO_INCREMENT
	,order_status BIGINT
	,order_days BIGINT
	,total_count BIGINT
	,discount_price_sum DECIMAL(19,3)
	,price_sum DECIMAL(19,3));  
	
	#是管理员
	IF @user_type IN (1,2) THEN
		IF @is_manager = 1 THEN 
			IF @user_type = 1 THEN #运营端
				#生成顶级代理相关代理数据
				CALL p_get_proxy_child(@proxy_id,0);
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		ELSE
			IF @user_type = 1 THEN #运营端
				#通过数据权限获取代理商数据
				IF @is_all_proxy = 1 THEN				
					CALL p_get_proxy_child(@proxy_id,0);
				ELSE
					CALL p_get_user_proxy(p_user_id);
				END IF;	
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		#默认可以查询直属数据权限
		INSERT INTO temp_proxy_all_list(proxy_id) VALUES(@proxy_id);
		IF @user_type = 1 THEN #运营端
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id;
			ELSE
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id AND pl.proxy_id <> @proxy_id;
			END IF;
		ELSEIF @user_type = 2 THEN #代理商
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM t_flow_enterprise e
				WHERE e.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		
		#用户所分配的企业权限
		INSERT INTO temp_enterprise_all_list(enterprise_id)
		SELECT pe.enterprise_id
		FROM t_flow_enterprise_user pe
		WHERE pe.user_id = p_user_id;
	END IF;
	#select @is_manager,@user_type;
	#仅四个用户能查询该企业判断 2016-11-21加
	IF p_user_id NOT IN (168,173,183,383) THEN
		DELETE FROM temp_enterprise_all_list WHERE enterprise_id IN(717,718);
	END IF; 
	#获取代理商ID
	 SELECT IFNULL(GROUP_CONCAT(tp.proxy_id),'-1')
	 INTO v_proxy_id_str
	 FROM 
	 (
		SELECT DISTINCT proxy_id FROM temp_proxy_all_list
	 ) tp;
	 #获取企业ID
	 SELECT IFNULL(GROUP_CONCAT(tp.enterprise_id),'-1')
	 INTO v_enterprise_id_str
	 FROM 
	 (
		SELECT DISTINCT enterprise_id FROM temp_enterprise_all_list
	 ) tp;
	
	#数据权限
	#用户类型(1：尚通、2：代理商、3企业)
	IF @user_type IN (1,2) && @is_manager = 1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
	ELSE
		IF @user_type = 1 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		ELSEIF @user_type = 2 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.enterprise_id = ',@enterprise_id);
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id = ',@enterprise_id);
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id = ',@enterprise_id);
		END IF;
	END IF;
	
	#SELECT * FROM temp_proxy_all_list; 	
	#用户名称,顶级代理,手机号,运营商,归属地,流量包名称,折后价格,通道编码,提交时间,完成时间,订单状态		
	#条件过滤
	IF p_user_name IS NOT NULL && p_user_name <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_name_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_name,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_name_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_name,'%');
	
		IF @user_type IN(1,2) THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,') '
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.proxy_id IN (',v_proxy_id_name_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_name_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_name_str,')');
		END IF;
	END IF;	
	
	IF p_user_code IS NOT NULL && p_user_code <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_code_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_code,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_code_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_code,'%');
		
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
		END IF;
	END IF;	
	
	IF p_order_code IS NOT NULL && p_order_code <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');		
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.order_code LIKE ''''',p_order_code,'%''''');
	END IF;
	
	IF p_mobile IS NOT NULL && p_mobile <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.mobile = ''''',p_mobile,'''''');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_channel_id IS NOT NULL && p_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_sale_id IS NOT NULL && p_sale_id <> -1 THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_sales_str
		FROM t_flow_proxy p 
		WHERE p.`sale_id` = p_sale_id;
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_sales_str
		FROM t_flow_enterprise e
		WHERE e.`sale_id` = p_sale_id;
		
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
		END IF;
	END IF;
	
	IF p_back_channel_id IS NOT NULL && p_back_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_operator_id IS NOT NULL && p_operator_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.operator_id = ',p_operator_id);
	END IF;
	IF p_province_id IS NOT NULL && p_province_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.province_id = ',p_province_id);
	END IF;
	
	IF p_order_status IS NOT NULL && p_order_status <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.order_status IN (',p_order_status,')');
	END IF;
	
	IF p_product_name IS NOT NULL && p_product_name <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.product_name = ''''',p_product_name,'''''');
	END IF;
	
	#select @conditionStr;
	#动态SQL语句
	#set @user_type = 3;
	
	SET @strsql = CONCAT('SELECT od.order_id'
	,'\r ,CASE WHEN od.proxy_id <=0 THEN od.enterprise_name ELSE od.proxy_name END proxy_name'
	,'\r ,od.enterprise_id'
	,'\r ,od.proxy_id'
	,'\r ,od.one_proxy_name top_proxy_name'
	,'\r ,od.mobile'
	,'\r ,CASE od.operator_id WHEN 1 THEN ''中国移动'' WHEN 2 THEN ''中国联通'' WHEN 3 THEN ''中国电信'' END  operator_name'
	,'\r ,od.product_province_id province_id'
	,'\r ,od.province_name'
	,'\r ,od.city_name'
	,'\r ,od.product_name'
	,'\r ,od.discount_price'
	,'\r ,od.price'
	,'\r ,od.back_channel_code bc_channel_code'
	,'\r ,od.channel_code'
	,'\r ,od.order_date'
	,'\r ,od.complete_time'
	,'\r ,od.order_status'
	,'\r ,od.refund_id'
	,'\r ,od.proxy_id'
	,'\r ,od.enterprise_id'	
	,'\r ,od.order_code' 
	,'\r ,CASE od.pay_type WHEN 1 THEN ''账户余额'' WHEN 2 THEN ''微信'' WHEN 3 THEN ''支付宝'' END pay_type' #支付类型（1：账户余额、2：微信、3：支付宝）
	,'\r ,CASE od.source_type WHEN 1 THEN ''下游接口'' WHEN 2 THEN ''平台'' WHEN 3 THEN ''网站'' WHEN 4 THEN ''移动端'' END source_type' #来源类型 1下游接口 2平台 3网站 4移动端
	,'\r ,od.orderno_id'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_discount END top_discount'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_rebate_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_rebate_discount END top_rebate_discount'
	,'\r ,od.back_content'
	,'\r ,od.channel_order_code'
	,'\r FROM ',v_order_table,' AS od '
	#,v_inner_table
	,'\r WHERE od.order_date BETWEEN ''',p_start_order_date,''' AND ''',v_end_order_date,''''
	,@conditionStr
	,'\r ORDER BY od.order_date DESC '
	,'\r LIMIT ',(p_pageIndex-1)*p_pageSize,',',p_pageSize);
	
	/*
	订单类型
	0：等待提交
	1：提交成功 
	2：充值成功 
	3：充值/提交 失败，再次等待备用通道提交 
	4：备用通道提交成功
	5：备用通道充值成功  
	6：备用通道 充值/提交 失败	
	*/
	# 充值成功 '2','5'
	# 充值/提交 失败 '6','3'
	# 等待提交 '0','1','4'
	#统计数SQL;
	
	IF p_is_count = 1 THEN 	
		#判断使用索引，当仅有时间查询条件时，使用 【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		#p_user_name、p_mobile、p_sale_id、p_product_name、p_channel_id、p_back_channel_id、p_operator_id、p_province_id、p_order_status
		#为空或为-1时用【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		IF p_order_type=2 && v_query_null_count = 3 && @user_type IN (1,2) THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		IF LENGTH(p_order_type) = 6 && v_query_null_count = 3 && @user_type IN (1,2) THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		SET v_query_days = DATEDIFF(v_end_order_date,p_start_order_date);
		SET v_query_times = HOUR(TIMEDIFF(v_end_order_date,p_start_order_date));#计算小时差
		SET v_index_days = 0;
		WHILE v_index_days <= v_query_days DO 
			
			IF v_query_days = 0 THEN
				SET v_start_day = p_start_order_date;
				SET v_end_day = v_end_order_date;
			ELSE
				IF v_index_days = 0 THEN
					SET v_start_day = p_start_order_date;
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);
				ELSEIF v_index_days = v_query_days THEN
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = v_end_order_date;
				ELSE
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);				
				END IF;
			END IF;
			#默认查询多少天不进行缓存，时时查询			
			#IF DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S') BETWEEN CONCAT(DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y%m%d'),'000000') AND CONCAT(DATE_FORMAT(NOW(),'%Y%m%d'),'000000')
			IF v_query_times <24 OR DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S') BETWEEN CONCAT(DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y%m%d'),'000000') AND DATE_FORMAT(NOW(),'%Y%m%d%H%i%S')			
			THEN
				#select v_start_day,v_end_day,DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d');
				SET @strsqlStatDays = CONCAT('INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)'
				 ,'\r SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionStatStr
				 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
				 ,'\r GROUP BY od.order_status');	
				 
				#select 1;
				#SELECT @strsqlStatDays;
				
				PREPARE stmtsqlStatDays FROM @strsqlStatDays; 
				EXECUTE stmtsqlStatDays; 
				DEALLOCATE PREPARE stmtsqlStatDays;
			ELSE 
				SET @sql_fulltext = CONCAT('SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''''%Y%m%d'''') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionSqlFullTextStr
				 ,'\r AND od.order_date BETWEEN ''''',v_start_day,''''' AND ''''',v_end_day,''''''
				 ,'\r GROUP BY od.order_status');
				 
				 #select @sql_fulltext;
				 
				SET @sql_fulltext_exec = CONCAT('SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionStatStr
				 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
				 ,'\r GROUP BY od.order_status');
					 
				SET v_sql_hash_value = `f_get_hashValue`(@sql_fulltext);
				
				SELECT COUNT(*) 
				INTO v_exists_result
				FROM t_flow_order_sql_result_stat osrs 
				WHERE osrs.`sql_hash_value` = v_sql_hash_value 
				AND osrs.`result_stat_date` = DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S');
				
				#select v_exists_result;
				IF v_exists_result <= 0 THEN
					#select 20;
					 
					SET @strsqlStatDaysResult = CONCAT('INSERT INTO t_flow_order_sql_result_stat(sql_hash_value,sql_text ,sql_fulltext,result_stat_type ,result_stat_date ,sql_result_stat,user_id,user_type,cache_time )'
					 ,'\r SELECT ',v_sql_hash_value,' sql_hash_value'
					 ,'\r ,''',SUBSTR(@sql_fulltext,1,2000),''' sql_text'
					 ,'\r ,''',@sql_fulltext,''' sql_fulltext'
					 ,'\r ,os.order_status result_stat_type'
					 ,'\r ,DATE_FORMAT(os.order_days,''%Y%m%d%H%i%S'') result_stat_date'
					 ,'\r ,CONCAT(''total_count='',os.total_count,'',discount_price_sum='',os.discount_price_sum,'',price_sum='',os.price_sum) sql_result_stat'
					 ,'\r ,',p_user_id,'  user_id'
					 ,'\r ,',@user_type,'  user_type'
					 ,'\r ,''',CURRENT_TIMESTAMP(),'''  cache_time'
					 ,'\r FROM'
					 ,'\r ('
					 ,'\r ',@sql_fulltext_exec
					 ,'\r ) os'
					 ,'\r ON DUPLICATE KEY UPDATE sql_result_stat=CONCAT(''total_count='',os.total_count,'',discount_price_sum='',os.discount_price_sum,'',price_sum='',os.price_sum)');
					 
					#SELECT @strsqlStatDaysResult;
					
					PREPARE stmtsqlStatDaysResult FROM @strsqlStatDaysResult; 
					EXECUTE stmtsqlStatDaysResult; 
					DEALLOCATE PREPARE stmtsqlStatDaysResult;
				END IF;
				
				INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)
				SELECT osrs.result_stat_type order_status
				,DATE_FORMAT(v_start_day,'%Y%m%d') order_days
				,f_str_split(`f_str_split`(sql_result_stat,',',1),'=',2) total_count
				,f_str_split(`f_str_split`(sql_result_stat,',',2),'=',2) discount_price_sum
				,f_str_split(`f_str_split`(sql_result_stat,',',3),'=',2) price_sum
				FROM t_flow_order_sql_result_stat osrs 
				WHERE osrs.`sql_hash_value` = v_sql_hash_value 
				AND osrs.`result_stat_date` = DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S');
				
			END IF;			
			
			SET v_index_days = v_index_days +1; 
		END WHILE; 
		
		SET @strsqlStat = CONCAT('SELECT SUM(al.total_count) total_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.total_count ELSE 0 END ) success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.discount_price_sum ELSE 0 END ) success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.total_count ELSE 0 END ) faile_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.discount_price_sum ELSE 0 END ) faile_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.total_count ELSE 0 END ) wait_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.discount_price_sum ELSE 0 END ) wait_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.total_count ELSE 0 END ) submit_success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.discount_price_sum ELSE 0 END ) submit_success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.price_sum ELSE 0 END ) success_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.price_sum ELSE 0 END ) faile_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.price_sum ELSE 0 END ) wait_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.price_sum ELSE 0 END ) submit_success_price '
		 ,'\r INTO  @total_count,@success_count,@success_amount,@faile_count,@faile_amount,@wait_count,@wait_amount,@submit_success_count,@submit_success_amount ,@success_price,@faile_price,@wait_price,@submit_success_price'
		 ,'\r FROM temp_order_status_stat al');
		#SELECT @strsqlStat;
		
		#select * from temp_order_status_stat;
		
		PREPARE stmtsqlStat FROM @strsqlStat; 
		EXECUTE stmtsqlStat; 
		DEALLOCATE PREPARE stmtsqlStat;
		
		SET p_total_count =  @total_count;
		SET p_success_count = @success_count;
		SET p_success_amount =  @success_amount;
		SET p_faile_count = @faile_count;
		SET p_faile_amount = @faile_amount;
		SET p_wait_count = @wait_count;
		SET p_wait_amount = @wait_amount;
		SET p_submit_success_count = @submit_success_count;
		SET p_submit_success_amount = @submit_success_amount;
		SET p_success_price =  @success_price;
		SET p_faile_price = @faile_price;
		SET p_wait_price = @wait_price;
		SET p_submit_success_price = @submit_success_price;
	ELSE		
		SET p_total_count =  0;
		SET p_success_count = 0;
		SET p_success_amount =  0;
		SET p_faile_count = 0;
		SET p_faile_amount = 0;
		SET p_wait_count = 0;
		SET p_wait_amount = 0;
		SET p_submit_success_count = 0;
		SET p_submit_success_amount = 0;
		SET p_success_price =  0;
		SET p_faile_price = 0;
		SET p_wait_price = 0;
		SET p_submit_success_price = 0;
	END IF;
		
	#SELECT @strsql;
	
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql; 
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_query_order_20160914
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_query_order_20160914`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_query_order_20160914`(
IN p_order_type INT
,IN p_user_id INT
,IN p_user_name VARCHAR(50)
,IN p_user_code VARCHAR(50)
,IN p_order_code VARCHAR(50)
,IN p_mobile VARCHAR(100)
,IN p_channel_id VARCHAR(4000)
,IN p_back_channel_id VARCHAR(4000)
,IN p_operator_id INT
,IN p_province_id INT
,IN p_order_status VARCHAR(50)
,IN p_start_order_date VARCHAR(50)
,IN p_end_order_date VARCHAR(50)
,IN p_product_name VARCHAR(50)
,IN p_sale_id BIGINT
,IN p_pageIndex INT
,IN p_pageSize INT
,IN p_is_count INT
,OUT p_total_count INT
,OUT p_success_count INT
,OUT p_success_amount DECIMAL(18,2)
,OUT p_faile_count INT
,OUT p_faile_amount DECIMAL(18,2)
,OUT p_wait_count INT
,OUT p_wait_amount DECIMAL(18,2)
,OUT p_submit_success_count INT
,OUT p_submit_success_amount DECIMAL(18,2)
,OUT p_success_price DECIMAL(18,2)
,OUT p_faile_price DECIMAL(18,2)
,OUT p_wait_price DECIMAL(18,2)
,OUT p_submit_success_price DECIMAL(18,2)
)
BEGIN  	
	DECLARE v_order_table VARCHAR(100);
	DECLARE v_proxy_enterprise_str VARCHAR(1000);
	#DECLARE v_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_id_title VARCHAR(1000);
	DECLARE v_channel_str VARCHAR(500) DEFAULT '';
	DECLARE v_back_channel_str VARCHAR(500) DEFAULT '';	
	#DECLARE v_inner_table VARCHAR(500) DEFAULT '';
	#DECLARE v_inner_title VARCHAR(4000) DEFAULT '';
	DECLARE v_dbowner VARCHAR(50) DEFAULT '';
	#declare v_proxy_id_str VARCHAR(4000) DEFAULT '';
	#declare v_enterprise_id_str VARCHAR(4000) DEFAULT '';
	
	SET @is_manager = 0;
	SET @user_type = 0;
	SET @proxy_id = 0;
	SET @is_all_proxy = 0;
	SET @is_all_enterprise = 0;
	SET @enterprise_id = 0;
	
	SET @totalStat = 0;
	SET @conditionStr = '';
	SET @conditionStatStr = '';
	
	/*2
	SET v_inner_title = CONCAT('\r ,(SELECT dt.province_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) province_name'
				,'\r ,(SELECT dt.city_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) city_name');
	*/
	IF (p_order_type = 1) THEN 
		SET v_order_table = 't_flow_order_pre';
		SET v_channel_str = ' AND od.order_status IN (0,1)';
		SET v_back_channel_str = ' AND od.order_status IN (3,4)';
	ELSEIF (p_order_type = 2) THEN 
		SET v_order_table = 't_flow_order';
		SET v_channel_str = ' AND od.order_status IN (2,3)';
		SET v_back_channel_str = ' AND od.order_status IN (5,6)';
	ELSEIF (p_order_type = 3) THEN 
		SET v_order_table = 't_flow_order_cache';
	ELSEIF (LENGTH(p_order_type) = 6) THEN 
		SET v_order_table = CONCAT('t_flow_order_',p_order_type);
	END IF; 
	#获取用户相关数据
	SELECT u.is_manager,u.user_type,u.proxy_id,u.is_all_proxy,u.is_all_enterprise,u.enterprise_id
	INTO @is_manager,@user_type,@proxy_id,@is_all_proxy,@is_all_enterprise,@enterprise_id
	FROM t_flow_sys_user u
	WHERE u.user_id = p_user_id;
	
	#创建代理商临时表
	DROP TEMPORARY TABLE IF EXISTS temp_proxy_all_list;	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_proxy_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,proxy_id BIGINT,depth INT);  
	#创建企业临时表
	DROP TEMPORARY TABLE IF EXISTS temp_enterprise_all_list;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_enterprise_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,enterprise_id BIGINT);  
	
	#是管理员
	IF @user_type IN (1,2) THEN
		IF @is_manager = 1 THEN 
			IF @user_type = 1 THEN #运营端
				#生成顶级代理相关代理数据
				CALL p_get_proxy_child(@proxy_id,0);
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		ELSE
			IF @user_type = 1 THEN #运营端
				#通过数据权限获取代理商数据
				IF @is_all_proxy = 1 THEN				
					CALL p_get_proxy_child(@proxy_id,0);
				ELSE
					CALL p_get_user_proxy(p_user_id);
				END IF;	
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		#默认可以查询直属数据权限
		INSERT INTO temp_proxy_all_list(proxy_id) VALUES(@proxy_id);
		IF @user_type = 1 THEN #运营端
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id;
			ELSE
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id AND pl.proxy_id <> @proxy_id;
			END IF;
		ELSEIF @user_type = 2 THEN #代理商
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM t_flow_enterprise e
				WHERE e.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		
		#用户所分配的企业权限
		INSERT INTO temp_enterprise_all_list(enterprise_id)
		SELECT pe.enterprise_id
		FROM t_flow_enterprise_user pe
		WHERE pe.user_id = p_user_id;
	END IF;
	#select @is_manager,@user_type;
	
	#数据权限
	#用户类型(1：尚通、2：代理商、3企业)
	IF @user_type IN (1,2) && @is_manager = 1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (select proxy_id from temp_proxy_all_list ) '
		,'\r OR od.enterprise_id in (select enterprise_id from temp_enterprise_all_list))');
		
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (select proxy_id from temp_proxy_all_list ) '
		,'\r OR od.enterprise_id in (select enterprise_id from temp_enterprise_all_list))');
	ELSE
		IF @user_type = 1 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (select proxy_id from temp_proxy_all_list )'
			,'\r OR od.enterprise_id in (select enterprise_id from temp_enterprise_all_list))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (select proxy_id from temp_proxy_all_list )'
			,'\r OR od.enterprise_id in (select enterprise_id from temp_enterprise_all_list))');
		ELSEIF @user_type = 2 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (select proxy_id from temp_proxy_all_list )'
			,'\r OR od.enterprise_id in (select enterprise_id from temp_enterprise_all_list))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (select proxy_id from temp_proxy_all_list )'
			,'\r OR od.enterprise_id in (select enterprise_id from temp_enterprise_all_list))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.enterprise_id = ',@enterprise_id);
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id = ',@enterprise_id);
		END IF;
	END IF;
	
	#SELECT * FROM temp_proxy_all_list; 	
	#用户名称,顶级代理,手机号,运营商,归属地,流量包名称,折后价格,通道编码,提交时间,完成时间,订单状态		
	#条件过滤
	IF p_user_name IS NOT NULL && p_user_name <> '' THEN
		IF @user_type IN(1,2) THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_name LIKE ''%',p_user_name,'%'' '
			,'\r OR od.enterprise_name LIKE ''%',p_user_name,'%'' )');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (SELECT r.proxy_id FROM t_flow_proxy r WHERE r.proxy_name LIKE ''%',p_user_name,'%'')'
			,'\r OR od.enterprise_id IN (SELECT e.enterprise_id FROM t_flow_enterprise e WHERE e.enterprise_name LIKE ''%',p_user_name,'%'' ))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.enterprise_name LIKE ''%',p_user_name,'%'' '
			,'\r OR od.enterprise_name LIKE ''%',p_user_name,'%'' )');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (SELECT e.enterprise_id FROM t_flow_enterprise e WHERE e.enterprise_name LIKE ''%',p_user_name,'%'' )');
		END IF;
	END IF;	
	
	IF p_user_code IS NOT NULL && p_user_code <> '' THEN
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (SELECT r.proxy_id FROM t_flow_proxy r WHERE r.proxy_code LIKE ''',p_user_code,'%'')'
			,'\r OR od.enterprise_id IN (SELECT e.enterprise_id FROM t_flow_enterprise e WHERE e.enterprise_code LIKE ''',p_user_code,'%'' ))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (SELECT r.proxy_id FROM t_flow_proxy r WHERE r.proxy_code LIKE ''',p_user_code,'%'')'
			,'\r OR od.enterprise_id IN (SELECT e.enterprise_id FROM t_flow_enterprise e WHERE e.enterprise_code LIKE ''',p_user_code,'%'' ))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (SELECT e.enterprise_id FROM t_flow_enterprise e WHERE e.enterprise_code LIKE ''',p_user_code,'%'' )');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (SELECT e.enterprise_id FROM t_flow_enterprise e WHERE e.enterprise_code LIKE ''',p_user_code,'%'' )');
		END IF;
	END IF;	
	
	IF p_order_code IS NOT NULL && p_order_code <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');
	END IF;
	
	IF p_mobile IS NOT NULL && p_mobile <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.mobile = ''',p_mobile,'''');
	END IF;
	
	IF p_channel_id IS NOT NULL && p_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
	END IF;
	
	IF p_sale_id IS NOT NULL && p_sale_id <> -1 THEN
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (SELECT r.proxy_id FROM t_flow_proxy r WHERE r.`sale_id` = ',p_sale_id,')'
			,'\r OR od.enterprise_id IN (SELECT e.enterprise_id FROM t_flow_enterprise e WHERE e.`sale_id` = ',p_sale_id,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (SELECT r.proxy_id FROM t_flow_proxy r WHERE r.`sale_id` = ',p_sale_id,')'
			,'\r OR od.enterprise_id IN (SELECT e.enterprise_id FROM t_flow_enterprise e WHERE e.`sale_id` = ',p_sale_id,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (SELECT enterprise_id FROM t_flow_enterprise e WHERE e.`sale_id` = ',p_sale_id,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (SELECT enterprise_id FROM t_flow_enterprise e WHERE e.`sale_id` = ',p_sale_id,')');
		END IF;
		
	END IF;
	
	IF p_back_channel_id IS NOT NULL && p_back_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
	END IF;
	
	IF p_operator_id IS NOT NULL && p_operator_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.operator_id = ',p_operator_id);
	END IF;
	IF p_province_id IS NOT NULL && p_province_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.province_id = ',p_province_id);
	END IF;
	
	IF p_order_status IS NOT NULL && p_order_status <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_status IN (',p_order_status,')');
	END IF;
	
	IF p_product_name IS NOT NULL && p_product_name <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.product_name = ''',p_product_name,'''');
	END IF;
	
	#select @conditionStr;
	#动态SQL语句
	#set @user_type = 3;
	
	SET @strsql = CONCAT('SELECT od.order_id'
	,'\r ,CASE WHEN od.proxy_id <=0 THEN od.enterprise_name ELSE od.proxy_name END proxy_name'
	,'\r ,od.enterprise_id'
	,'\r ,od.proxy_id'
	,'\r ,od.one_proxy_name top_proxy_name'
	,'\r ,od.mobile'
	,'\r ,CASE od.operator_id WHEN 1 THEN ''中国移动'' WHEN 2 THEN ''中国联通'' WHEN 3 THEN ''中国电信'' END  operator_name'
	,'\r ,od.product_province_id province_id'
	,'\r ,od.province_name'
	,'\r ,od.city_name'
	,'\r ,od.product_name'
	,'\r ,od.discount_price'
	,'\r ,od.price'
	,'\r ,od.back_channel_code bc_channel_code'
	,'\r ,od.channel_code'
	,'\r ,od.order_date'
	,'\r ,od.complete_time'
	,'\r ,od.order_status'
	,'\r ,od.refund_id'
	,'\r ,od.proxy_id'
	,'\r ,od.enterprise_id'	
	,'\r ,od.order_code' 
	,'\r ,CASE od.pay_type WHEN 1 THEN ''账户余额'' WHEN 2 THEN ''微信'' WHEN 3 THEN ''支付宝'' END pay_type' #支付类型（1：账户余额、2：微信、3：支付宝）
	,'\r ,CASE od.source_type WHEN 1 THEN ''下游接口'' WHEN 2 THEN ''平台'' WHEN 3 THEN ''网站'' WHEN 4 THEN ''移动端'' END source_type' #来源类型 1下游接口 2平台 3网站 4移动端
	,'\r ,od.orderno_id'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_discount END top_discount'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_rebate_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_rebate_discount END top_rebate_discount'
	,'\r ,od.back_content'
	,'\r FROM ',v_order_table,' AS od '
	#,v_inner_table
	,'\r WHERE od.order_date BETWEEN ''',p_start_order_date,''' AND ''',p_end_order_date,''''
	,@conditionStr
	,'\r ORDER BY od.order_date DESC '
	,'\r LIMIT ',(p_pageIndex-1)*p_pageSize,',',p_pageSize);
	/*
	订单类型
	0：等待提交
	1：提交成功 
	2：充值成功 
	3：充值/提交 失败，再次等待备用通道提交 
	4：备用通道提交成功
	5：备用通道充值成功  
	6：备用通道 充值/提交 失败	
	*/
	# 充值成功 '2','5'
	# 充值/提交 失败 '6','3'
	# 等待提交 '0','1','4'
	#统计数SQL;
	
	IF p_is_count = 1 THEN 
		SET @strsqlStat = CONCAT('SELECT COUNT(*) total_count'
		,'\r ,SUM(CASE WHEN order_status IN (2,5) THEN 1 ELSE 0 END ) success_count'
		,'\r ,SUM(CASE WHEN order_status IN (2,5) THEN od.discount_price ELSE 0 END ) success_amount'
		,'\r ,SUM(CASE WHEN order_status IN (6,3) THEN 1 ELSE 0 END ) faile_count'
		,'\r ,SUM(CASE WHEN order_status IN (6,3) THEN od.discount_price ELSE 0 END ) faile_amount'
		,'\r ,SUM(CASE WHEN order_status IN (0) THEN 1 ELSE 0 END ) wait_count'
		,'\r ,SUM(CASE WHEN order_status IN (0) THEN od.discount_price ELSE 0 END ) wait_amount'
		,'\r ,SUM(CASE WHEN order_status IN (1,4) THEN 1 ELSE 0 END ) submit_success_count'
		,'\r ,SUM(CASE WHEN order_status IN (1,4) THEN od.discount_price ELSE 0 END ) submit_success_amount'
		,'\r ,SUM(CASE WHEN order_status IN (2,5) THEN od.price ELSE 0 END ) success_price'
		,'\r ,SUM(CASE WHEN order_status IN (6,3) THEN od.price ELSE 0 END ) faile_price'
		,'\r ,SUM(CASE WHEN order_status IN (0) THEN od.price ELSE 0 END ) wait_price'
		,'\r ,SUM(CASE WHEN order_status IN (1,4) THEN od.price ELSE 0 END ) submit_success_price'
		,'\r INTO  @total_count,@success_count,@success_amount,@faile_count,@faile_amount,@wait_count'
		',@wait_amount,@submit_success_count,@submit_success_amount '
		,',@success_price,@faile_price,@wait_price,@submit_success_price'
		,'\r FROM ',v_order_table,' od '
		#,v_inner_table
		,'\r WHERE  od.user_type IN (1,2,3)'
		,@conditionStatStr
		,'\r AND od.order_date BETWEEN ''',p_start_order_date,''' AND ''',p_end_order_date,''''); 
		
		SELECT @strsqlStat;
		
		PREPARE stmtsqlStat FROM @strsqlStat; 
		EXECUTE stmtsqlStat; 
		DEALLOCATE PREPARE stmtsqlStat;
		
		SET p_total_count =  @total_count;
		SET p_success_count = @success_count;
		SET p_success_amount =  @success_amount;
		SET p_faile_count = @faile_count;
		SET p_faile_amount = @faile_amount;
		SET p_wait_count = @wait_count;
		SET p_wait_amount = @wait_amount;
		SET p_submit_success_count = @submit_success_count;
		SET p_submit_success_amount = @submit_success_amount;
		SET p_success_price =  @success_price;
		SET p_faile_price = @faile_price;
		SET p_wait_price = @wait_price;
		SET p_submit_success_price = @submit_success_price;
	ELSE		
		SET p_total_count =  0;
		SET p_success_count = 0;
		SET p_success_amount =  0;
		SET p_faile_count = 0;
		SET p_faile_amount = 0;
		SET p_wait_count = 0;
		SET p_wait_amount = 0;
		SET p_submit_success_count = 0;
		SET p_submit_success_amount = 0;
		SET p_success_price =  0;
		SET p_faile_price = 0;
		SET p_wait_price = 0;
		SET p_submit_success_price = 0;
	END IF;
		
	#SELECT @strsql;
	
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql; 
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_query_order_20161020
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_query_order_20161020`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_query_order_20161020`(IN p_order_type INT ,
		IN p_user_id INT ,
		IN p_user_name VARCHAR(50),
		IN p_user_code VARCHAR(50),
		IN p_order_code VARCHAR(50),
		IN p_mobile VARCHAR(100),
		IN p_channel_id VARCHAR(4000),
		IN p_back_channel_id VARCHAR(4000),
		IN p_operator_id INT ,
		IN p_province_id INT ,
		IN p_order_status VARCHAR(50),
		IN p_start_order_date VARCHAR(50),
		IN p_end_order_date VARCHAR(50),
		IN p_product_name VARCHAR(50),
		IN p_sale_id BIGINT ,
		IN p_pageIndex INT ,
		IN p_pageSize INT ,
		IN p_is_count INT ,
		OUT p_total_count INT ,
		OUT p_success_count INT ,
		OUT p_success_amount DECIMAL(18 ,2),
		OUT p_faile_count INT ,
		OUT p_faile_amount DECIMAL(18 ,2),
		OUT p_wait_count INT ,
		OUT p_wait_amount DECIMAL(18 ,2),
		OUT p_submit_success_count INT ,
		OUT p_submit_success_amount DECIMAL(18 ,2),
		OUT p_success_price DECIMAL(18 ,2),
		OUT p_faile_price DECIMAL(18 ,2),
		OUT p_wait_price DECIMAL(18 ,2),
		OUT p_submit_success_price DECIMAL(18 ,2))
BEGIN  	
	DECLARE v_order_table VARCHAR(100);
	DECLARE v_proxy_enterprise_str VARCHAR(1000);
	#DECLARE v_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_id_title VARCHAR(1000);
	DECLARE v_channel_str VARCHAR(500) DEFAULT '';
	DECLARE v_back_channel_str VARCHAR(500) DEFAULT '';	
	#DECLARE v_inner_table VARCHAR(500) DEFAULT '';
	#DECLARE v_inner_title VARCHAR(4000) DEFAULT '';
	DECLARE v_dbowner VARCHAR(50) DEFAULT '';
	DECLARE v_proxy_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_query_days INT DEFAULT 1;
	DECLARE v_index_days INT DEFAULT 0;
	DECLARE v_start_day VARCHAR(100);
	DECLARE v_end_day VARCHAR(100);
	DECLARE v_query_null_count INT DEFAULT 0;#是否使用索引标识
	DECLARE v_end_order_date VARCHAR(100);#结束时间结尾加.999999
	
	SET @is_manager = 0;
	SET @user_type = 0;
	SET @proxy_id = 0;
	SET @is_all_proxy = 0;
	SET @is_all_enterprise = 0;
	SET @enterprise_id = 0;
	
	SET @totalStat = 0;
	SET @conditionStr = '';
	SET @conditionStatStr = '';
	SET @indexStr = '';
	#结束时间结尾加.999999
	SET v_end_order_date = CONCAT(p_end_order_date,'.999999');
	
	/*2
	SET v_inner_title = CONCAT('\r ,(SELECT dt.province_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) province_name'
				,'\r ,(SELECT dt.city_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) city_name');
	*/
	IF (p_order_type = 1) THEN 
		SET v_order_table = 't_flow_order_pre';
		SET v_channel_str = ' AND od.order_status IN (0,1)';
		SET v_back_channel_str = ' AND od.order_status IN (3,4)';
	ELSEIF (p_order_type = 2) THEN 
		SET v_order_table = 't_flow_order';
		SET v_channel_str = ' AND od.order_status IN (2,3)';
		SET v_back_channel_str = ' AND od.order_status IN (5,6)';
	ELSEIF (p_order_type = 3) THEN 
		SET v_order_table = 't_flow_order_cache';
	ELSEIF (LENGTH(p_order_type) = 6) THEN 
		SET v_order_table = CONCAT('t_flow_order_',p_order_type);
	END IF; 
	#获取用户相关数据
	SELECT u.is_manager,u.user_type,u.proxy_id,u.is_all_proxy,u.is_all_enterprise,u.enterprise_id
	INTO @is_manager,@user_type,@proxy_id,@is_all_proxy,@is_all_enterprise,@enterprise_id
	FROM t_flow_sys_user u
	WHERE u.user_id = p_user_id;
	
	#创建代理商临时表
	DROP TEMPORARY TABLE IF EXISTS temp_proxy_all_list;	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_proxy_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,proxy_id BIGINT,depth INT);  
	#创建企业临时表
	DROP TEMPORARY TABLE IF EXISTS temp_enterprise_all_list;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_enterprise_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,enterprise_id BIGINT);  
	#创建统计临时表
	DROP TEMPORARY TABLE IF EXISTS temp_order_status_stat;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_status_stat    
	(sno INT PRIMARY KEY AUTO_INCREMENT
	,order_status BIGINT
	,order_days BIGINT
	,total_count BIGINT
	,discount_price_sum DECIMAL(18,2)
	,price_sum DECIMAL(18,2));  
	
	#是管理员
	IF @user_type IN (1,2) THEN
		IF @is_manager = 1 THEN 
			IF @user_type = 1 THEN #运营端
				#生成顶级代理相关代理数据
				CALL p_get_proxy_child(@proxy_id,0);
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		ELSE
			IF @user_type = 1 THEN #运营端
				#通过数据权限获取代理商数据
				IF @is_all_proxy = 1 THEN				
					CALL p_get_proxy_child(@proxy_id,0);
				ELSE
					CALL p_get_user_proxy(p_user_id);
				END IF;	
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		#默认可以查询直属数据权限
		INSERT INTO temp_proxy_all_list(proxy_id) VALUES(@proxy_id);
		IF @user_type = 1 THEN #运营端
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id;
			ELSE
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id AND pl.proxy_id <> @proxy_id;
			END IF;
		ELSEIF @user_type = 2 THEN #代理商
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM t_flow_enterprise e
				WHERE e.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		
		#用户所分配的企业权限
		INSERT INTO temp_enterprise_all_list(enterprise_id)
		SELECT pe.enterprise_id
		FROM t_flow_enterprise_user pe
		WHERE pe.user_id = p_user_id;
	END IF;
	#select @is_manager,@user_type;
	
	#获取代理商ID
	 SELECT IFNULL(GROUP_CONCAT(tp.proxy_id),'-1')
	 INTO v_proxy_id_str
	 FROM 
	 (
		SELECT DISTINCT proxy_id FROM temp_proxy_all_list
	 ) tp;
	 #获取企业ID
	 SELECT IFNULL(GROUP_CONCAT(tp.enterprise_id),'-1')
	 INTO v_enterprise_id_str
	 FROM 
	 (
		SELECT DISTINCT enterprise_id FROM temp_enterprise_all_list
	 ) tp;
	#数据权限
	#用户类型(1：尚通、2：代理商、3企业)
	IF @user_type IN (1,2) && @is_manager = 1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
	ELSE
		IF @user_type = 1 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		ELSEIF @user_type = 2 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.enterprise_id = ',@enterprise_id);
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id = ',@enterprise_id);
		END IF;
	END IF;
	
	#SELECT * FROM temp_proxy_all_list; 	
	#用户名称,顶级代理,手机号,运营商,归属地,流量包名称,折后价格,通道编码,提交时间,完成时间,订单状态		
	#条件过滤
	IF p_user_name IS NOT NULL && p_user_name <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_name_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_name,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_name_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_name,'%');
	
		IF @user_type IN(1,2) THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,') '
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.proxy_id IN (',v_proxy_id_name_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_name_str,')');
		END IF;
	END IF;	
	
	IF p_user_code IS NOT NULL && p_user_code <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_code_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_code,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_code_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_code,'%');
		
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
		END IF;
	END IF;	
	
	IF p_order_code IS NOT NULL && p_order_code <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');
	END IF;
	
	IF p_mobile IS NOT NULL && p_mobile <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.mobile = ''',p_mobile,'''');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_channel_id IS NOT NULL && p_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_sale_id IS NOT NULL && p_sale_id <> -1 THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_sales_str
		FROM t_flow_proxy p 
		WHERE p.`sale_id` = p_sale_id;
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_sales_str
		FROM t_flow_enterprise e
		WHERE e.`sale_id` = p_sale_id;
		
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
		END IF;
	END IF;
	
	IF p_back_channel_id IS NOT NULL && p_back_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_operator_id IS NOT NULL && p_operator_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.operator_id = ',p_operator_id);
	END IF;
	IF p_province_id IS NOT NULL && p_province_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.province_id = ',p_province_id);
	END IF;
	
	IF p_order_status IS NOT NULL && p_order_status <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_status IN (',p_order_status,')');
	END IF;
	
	IF p_product_name IS NOT NULL && p_product_name <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.product_name = ''',p_product_name,'''');
	END IF;
	
	#select @conditionStr;
	#动态SQL语句
	#set @user_type = 3;
	
	SET @strsql = CONCAT('SELECT od.order_id'
	,'\r ,CASE WHEN od.proxy_id <=0 THEN od.enterprise_name ELSE od.proxy_name END proxy_name'
	,'\r ,od.enterprise_id'
	,'\r ,od.proxy_id'
	,'\r ,od.one_proxy_name top_proxy_name'
	,'\r ,od.mobile'
	,'\r ,CASE od.operator_id WHEN 1 THEN ''中国移动'' WHEN 2 THEN ''中国联通'' WHEN 3 THEN ''中国电信'' END  operator_name'
	,'\r ,od.product_province_id province_id'
	,'\r ,od.province_name'
	,'\r ,od.city_name'
	,'\r ,od.product_name'
	,'\r ,od.discount_price'
	,'\r ,od.price'
	,'\r ,od.back_channel_code bc_channel_code'
	,'\r ,od.channel_code'
	,'\r ,od.order_date'
	,'\r ,od.complete_time'
	,'\r ,od.order_status'
	,'\r ,od.refund_id'
	,'\r ,od.proxy_id'
	,'\r ,od.enterprise_id'	
	,'\r ,od.order_code' 
	,'\r ,CASE od.pay_type WHEN 1 THEN ''账户余额'' WHEN 2 THEN ''微信'' WHEN 3 THEN ''支付宝'' END pay_type' #支付类型（1：账户余额、2：微信、3：支付宝）
	,'\r ,CASE od.source_type WHEN 1 THEN ''下游接口'' WHEN 2 THEN ''平台'' WHEN 3 THEN ''网站'' WHEN 4 THEN ''移动端'' END source_type' #来源类型 1下游接口 2平台 3网站 4移动端
	,'\r ,od.orderno_id'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_discount END top_discount'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_rebate_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_rebate_discount END top_rebate_discount'
	,'\r ,od.back_content'
	,'\r FROM ',v_order_table,' AS od '
	#,v_inner_table
	,'\r WHERE od.order_date BETWEEN ''',p_start_order_date,''' AND ''',v_end_order_date,''''
	,@conditionStr
	,'\r ORDER BY od.order_date DESC '
	,'\r LIMIT ',(p_pageIndex-1)*p_pageSize,',',p_pageSize);
	/*
	订单类型
	0：等待提交
	1：提交成功 
	2：充值成功 
	3：充值/提交 失败，再次等待备用通道提交 
	4：备用通道提交成功
	5：备用通道充值成功  
	6：备用通道 充值/提交 失败	
	*/
	# 充值成功 '2','5'
	# 充值/提交 失败 '6','3'
	# 等待提交 '0','1','4'
	#统计数SQL;
	
	IF p_is_count = 1 THEN 	
		#判断使用索引，当仅有时间查询条件时，使用 【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		#p_user_name、p_mobile、p_sale_id、p_product_name、p_channel_id、p_back_channel_id、p_operator_id、p_province_id、p_order_status
		#为空或为-1时用【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		IF p_order_type=2 && v_query_null_count = 3 THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		IF LENGTH(p_order_type) = 6 && v_query_null_count = 3 THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		SET v_query_days = DATEDIFF(v_end_order_date,p_start_order_date);
		SET v_index_days = 0;
		WHILE v_index_days <= v_query_days DO 
			
			if v_query_days = 0 then
				set v_start_day = p_start_order_date;
				set v_end_day = v_end_order_date;
			else
				IF v_index_days = 0 THEN
					SET v_start_day = p_start_order_date;
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);
				ELSEIF v_index_days = v_query_days THEN
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = v_end_order_date;
				ELSE
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);				
				END IF;
			end if;
			
			SET @strsqlStatDays = CONCAT('INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)'
			 ,'\r SELECT od.order_status'
			 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
			 ,'\r ,COUNT(*) total_count'
			 ,'\r ,SUM(od.discount_price) discount_price_sum'
			 ,'\r ,SUM(od.price) price_sum'
			 ,'\r FROM ',v_order_table,' od '
			 ,@indexStr
			 ,'\r WHERE  od.user_type IN (1,2,3)'
			 ,@conditionStatStr
			 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
			 ,'\r GROUP BY od.order_status');			 
			 
			#select @strsqlStatDays;
			
			PREPARE stmtsqlStatDays FROM @strsqlStatDays; 
			EXECUTE stmtsqlStatDays; 
			DEALLOCATE PREPARE stmtsqlStatDays;
			
			SET v_index_days = v_index_days +1; 
		END WHILE; 
		
		SET @strsqlStat = CONCAT('SELECT SUM(al.total_count) total_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.total_count ELSE 0 END ) success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.discount_price_sum ELSE 0 END ) success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.total_count ELSE 0 END ) faile_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.discount_price_sum ELSE 0 END ) faile_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.total_count ELSE 0 END ) wait_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.discount_price_sum ELSE 0 END ) wait_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.total_count ELSE 0 END ) submit_success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.discount_price_sum ELSE 0 END ) submit_success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.price_sum ELSE 0 END ) success_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.price_sum ELSE 0 END ) faile_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.price_sum ELSE 0 END ) wait_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.price_sum ELSE 0 END ) submit_success_price '
		 ,'\r INTO  @total_count,@success_count,@success_amount,@faile_count,@faile_amount,@wait_count,@wait_amount,@submit_success_count,@submit_success_amount ,@success_price,@faile_price,@wait_price,@submit_success_price'
		 ,'\r FROM temp_order_status_stat al');
		#SELECT @strsqlStat;
		
		PREPARE stmtsqlStat FROM @strsqlStat; 
		EXECUTE stmtsqlStat; 
		DEALLOCATE PREPARE stmtsqlStat;
		
		SET p_total_count =  @total_count;
		SET p_success_count = @success_count;
		SET p_success_amount =  @success_amount;
		SET p_faile_count = @faile_count;
		SET p_faile_amount = @faile_amount;
		SET p_wait_count = @wait_count;
		SET p_wait_amount = @wait_amount;
		SET p_submit_success_count = @submit_success_count;
		SET p_submit_success_amount = @submit_success_amount;
		SET p_success_price =  @success_price;
		SET p_faile_price = @faile_price;
		SET p_wait_price = @wait_price;
		SET p_submit_success_price = @submit_success_price;
	ELSE		
		SET p_total_count =  0;
		SET p_success_count = 0;
		SET p_success_amount =  0;
		SET p_faile_count = 0;
		SET p_faile_amount = 0;
		SET p_wait_count = 0;
		SET p_wait_amount = 0;
		SET p_submit_success_count = 0;
		SET p_submit_success_amount = 0;
		SET p_success_price =  0;
		SET p_faile_price = 0;
		SET p_wait_price = 0;
		SET p_submit_success_price = 0;
	END IF;
		
	#SELECT @strsql;
	
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql; 
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_query_order_20161021
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_query_order_20161021`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_query_order_20161021`(IN p_order_type INT ,
		IN p_user_id INT ,
		IN p_user_name VARCHAR(50),
		IN p_user_code VARCHAR(50),
		IN p_order_code VARCHAR(50),
		IN p_mobile VARCHAR(100),
		IN p_channel_id VARCHAR(4000),
		IN p_back_channel_id VARCHAR(4000),
		IN p_operator_id INT ,
		IN p_province_id INT ,
		IN p_order_status VARCHAR(50),
		IN p_start_order_date VARCHAR(50),
		IN p_end_order_date VARCHAR(50),
		IN p_product_name VARCHAR(50),
		IN p_sale_id BIGINT ,
		IN p_pageIndex INT ,
		IN p_pageSize INT ,
		IN p_is_count INT ,
		OUT p_total_count INT ,
		OUT p_success_count INT ,
		OUT p_success_amount DECIMAL(19 ,3),
		OUT p_faile_count INT ,
		OUT p_faile_amount DECIMAL(19 ,3),
		OUT p_wait_count INT ,
		OUT p_wait_amount DECIMAL(19 ,3),
		OUT p_submit_success_count INT ,
		OUT p_submit_success_amount DECIMAL(19 ,3),
		OUT p_success_price DECIMAL(19 ,3),
		OUT p_faile_price DECIMAL(19 ,3),
		OUT p_wait_price DECIMAL(19 ,3),
		OUT p_submit_success_price DECIMAL(19 ,3))
BEGIN  	
	DECLARE v_order_table VARCHAR(100);
	DECLARE v_proxy_enterprise_str VARCHAR(1000);
	#DECLARE v_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_id_title VARCHAR(1000);
	DECLARE v_channel_str VARCHAR(500) DEFAULT '';
	DECLARE v_back_channel_str VARCHAR(500) DEFAULT '';	
	#DECLARE v_inner_table VARCHAR(500) DEFAULT '';
	#DECLARE v_inner_title VARCHAR(4000) DEFAULT '';
	DECLARE v_dbowner VARCHAR(50) DEFAULT '';
	DECLARE v_proxy_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_query_days INT DEFAULT 1;
	DECLARE v_index_days INT DEFAULT 0;
	DECLARE v_start_day VARCHAR(100);
	DECLARE v_end_day VARCHAR(100);
	DECLARE v_query_null_count INT DEFAULT 0;#是否使用索引标识
	DECLARE v_end_order_date VARCHAR(100);#结束时间结尾加.999999
	DECLARE v_result_days INTEGER DEFAULT -1;#默认配置查询天数
	DECLARE v_exists_result INTEGER DEFAULT 0;
	DECLARE v_sql_hash_value BIGINT;	
	
	SET @is_manager = 0;
	SET @user_type = 0;
	SET @proxy_id = 0;
	SET @is_all_proxy = 0;
	SET @is_all_enterprise = 0;
	SET @enterprise_id = 0;
	
	SET @totalStat = 0;
	SET @conditionStr = '';
	SET @conditionStatStr = '';
	SET @conditionSqlFullTextStr = '';
	SET @indexStr = '';
	#结束时间结尾加.999999
	SET v_end_order_date = CONCAT(p_end_order_date,'.999999');
	
	/*2
	SET v_inner_title = CONCAT('\r ,(SELECT dt.province_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) province_name'
				,'\r ,(SELECT dt.city_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) city_name');
	*/
	IF (p_order_type = 1) THEN 
		SET v_order_table = 't_flow_order_pre';
		SET v_channel_str = ' AND od.order_status IN (0,1)';
		SET v_back_channel_str = ' AND od.order_status IN (3,4)';
		SET v_result_days = -999;
	ELSEIF (p_order_type = 2) THEN 
		SET v_order_table = 't_flow_order';
		SET v_channel_str = ' AND od.order_status IN (2,3)';
		SET v_back_channel_str = ' AND od.order_status IN (5,6)';
	ELSEIF (p_order_type = 3) THEN 
		SET v_order_table = 't_flow_order_cache';
		SET v_result_days = -999;
	ELSEIF (p_order_type = 4) THEN 
		SET v_order_table = 't_flow_order_channel_cache';
		SET v_result_days = -999;
	ELSEIF (LENGTH(p_order_type) = 6) THEN 
		SET v_order_table = CONCAT('t_flow_order_',p_order_type);
	END IF; 
	#获取用户相关数据
	SELECT u.is_manager,u.user_type,u.proxy_id,u.is_all_proxy,u.is_all_enterprise,u.enterprise_id
	INTO @is_manager,@user_type,@proxy_id,@is_all_proxy,@is_all_enterprise,@enterprise_id
	FROM t_flow_sys_user u
	WHERE u.user_id = p_user_id;
	
	#创建代理商临时表
	DROP TEMPORARY TABLE IF EXISTS temp_proxy_all_list;	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_proxy_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,proxy_id BIGINT,depth INT);  
	#创建企业临时表
	DROP TEMPORARY TABLE IF EXISTS temp_enterprise_all_list;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_enterprise_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,enterprise_id BIGINT);  
	#创建统计临时表
	DROP TEMPORARY TABLE IF EXISTS temp_order_status_stat;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_status_stat    
	(sno INT PRIMARY KEY AUTO_INCREMENT
	,order_status BIGINT
	,order_days BIGINT
	,total_count BIGINT
	,discount_price_sum DECIMAL(19,3)
	,price_sum DECIMAL(19,3));  
	
	#是管理员
	IF @user_type IN (1,2) THEN
		IF @is_manager = 1 THEN 
			IF @user_type = 1 THEN #运营端
				#生成顶级代理相关代理数据
				CALL p_get_proxy_child(@proxy_id,0);
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		ELSE
			IF @user_type = 1 THEN #运营端
				#通过数据权限获取代理商数据
				IF @is_all_proxy = 1 THEN				
					CALL p_get_proxy_child(@proxy_id,0);
				ELSE
					CALL p_get_user_proxy(p_user_id);
				END IF;	
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		#默认可以查询直属数据权限
		INSERT INTO temp_proxy_all_list(proxy_id) VALUES(@proxy_id);
		IF @user_type = 1 THEN #运营端
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id;
			ELSE
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id AND pl.proxy_id <> @proxy_id;
			END IF;
		ELSEIF @user_type = 2 THEN #代理商
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM t_flow_enterprise e
				WHERE e.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		
		#用户所分配的企业权限
		INSERT INTO temp_enterprise_all_list(enterprise_id)
		SELECT pe.enterprise_id
		FROM t_flow_enterprise_user pe
		WHERE pe.user_id = p_user_id;
	END IF;
	#select @is_manager,@user_type;
	#仅四个用户能查询该企业判断 2016-11-21加
	IF p_user_id NOT IN (168,173,183,383) THEN
		DELETE FROM temp_enterprise_all_list WHERE enterprise_id IN(717,718);
	END IF; 
	#获取代理商ID
	 SELECT IFNULL(GROUP_CONCAT(tp.proxy_id),'-1')
	 INTO v_proxy_id_str
	 FROM 
	 (
		SELECT DISTINCT proxy_id FROM temp_proxy_all_list
	 ) tp;
	 #获取企业ID
	 SELECT IFNULL(GROUP_CONCAT(tp.enterprise_id),'-1')
	 INTO v_enterprise_id_str
	 FROM 
	 (
		SELECT DISTINCT enterprise_id FROM temp_enterprise_all_list
	 ) tp;
	
	#数据权限
	#用户类型(1：尚通、2：代理商、3企业)
	IF @user_type IN (1,2) && @is_manager = 1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
	ELSE
		IF @user_type = 1 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		ELSEIF @user_type = 2 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.enterprise_id = ',@enterprise_id);
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id = ',@enterprise_id);
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id = ',@enterprise_id);
		END IF;
	END IF;
	
	#SELECT * FROM temp_proxy_all_list; 	
	#用户名称,顶级代理,手机号,运营商,归属地,流量包名称,折后价格,通道编码,提交时间,完成时间,订单状态		
	#条件过滤
	IF p_user_name IS NOT NULL && p_user_name <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_name_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_name,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_name_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_name,'%');
	
		IF @user_type IN(1,2) THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,') '
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.proxy_id IN (',v_proxy_id_name_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_name_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_name_str,')');
		END IF;
	END IF;	
	
	IF p_user_code IS NOT NULL && p_user_code <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_code_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_code,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_code_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_code,'%');
		
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
		END IF;
	END IF;	
	
	IF p_order_code IS NOT NULL && p_order_code <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');		
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.order_code LIKE ''''',p_order_code,'%''''');
	END IF;
	
	IF p_mobile IS NOT NULL && p_mobile <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.mobile = ''''',p_mobile,'''''');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_channel_id IS NOT NULL && p_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_sale_id IS NOT NULL && p_sale_id <> -1 THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_sales_str
		FROM t_flow_proxy p 
		WHERE p.`sale_id` = p_sale_id;
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_sales_str
		FROM t_flow_enterprise e
		WHERE e.`sale_id` = p_sale_id;
		
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
		END IF;
	END IF;
	
	IF p_back_channel_id IS NOT NULL && p_back_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_operator_id IS NOT NULL && p_operator_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.operator_id = ',p_operator_id);
	END IF;
	IF p_province_id IS NOT NULL && p_province_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.province_id = ',p_province_id);
	END IF;
	
	IF p_order_status IS NOT NULL && p_order_status <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.order_status IN (',p_order_status,')');
	END IF;
	
	IF p_product_name IS NOT NULL && p_product_name <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.product_name = ''''',p_product_name,'''''');
	END IF;
	
	#select @conditionStr;
	#动态SQL语句
	#set @user_type = 3;
	
	SET @strsql = CONCAT('SELECT od.order_id'
	,'\r ,CASE WHEN od.proxy_id <=0 THEN od.enterprise_name ELSE od.proxy_name END proxy_name'
	,'\r ,od.enterprise_id'
	,'\r ,od.proxy_id'
	,'\r ,od.one_proxy_name top_proxy_name'
	,'\r ,od.mobile'
	,'\r ,CASE od.operator_id WHEN 1 THEN ''中国移动'' WHEN 2 THEN ''中国联通'' WHEN 3 THEN ''中国电信'' END  operator_name'
	,'\r ,od.product_province_id province_id'
	,'\r ,od.province_name'
	,'\r ,od.city_name'
	,'\r ,od.product_name'
	,'\r ,od.discount_price'
	,'\r ,od.price'
	,'\r ,od.back_channel_code bc_channel_code'
	,'\r ,od.channel_code'
	,'\r ,od.order_date'
	,'\r ,od.complete_time'
	,'\r ,od.order_status'
	,'\r ,od.refund_id'
	,'\r ,od.proxy_id'
	,'\r ,od.enterprise_id'	
	,'\r ,od.order_code' 
	,'\r ,CASE od.pay_type WHEN 1 THEN ''账户余额'' WHEN 2 THEN ''微信'' WHEN 3 THEN ''支付宝'' END pay_type' #支付类型（1：账户余额、2：微信、3：支付宝）
	,'\r ,CASE od.source_type WHEN 1 THEN ''下游接口'' WHEN 2 THEN ''平台'' WHEN 3 THEN ''网站'' WHEN 4 THEN ''移动端'' END source_type' #来源类型 1下游接口 2平台 3网站 4移动端
	,'\r ,od.orderno_id'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_discount END top_discount'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_rebate_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_rebate_discount END top_rebate_discount'
	,'\r ,od.back_content'
	,'\r ,od.channel_order_code'
	,'\r FROM ',v_order_table,' AS od '
	#,v_inner_table
	,'\r WHERE od.order_date BETWEEN ''',p_start_order_date,''' AND ''',v_end_order_date,''''
	,@conditionStr
	,'\r ORDER BY od.order_date DESC '
	,'\r LIMIT ',(p_pageIndex-1)*p_pageSize,',',p_pageSize);
	
	/*
	订单类型
	0：等待提交
	1：提交成功 
	2：充值成功 
	3：充值/提交 失败，再次等待备用通道提交 
	4：备用通道提交成功
	5：备用通道充值成功  
	6：备用通道 充值/提交 失败	
	*/
	# 充值成功 '2','5'
	# 充值/提交 失败 '6','3'
	# 等待提交 '0','1','4'
	#统计数SQL;
	
	IF p_is_count = 1 THEN 	
		#判断使用索引，当仅有时间查询条件时，使用 【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		#p_user_name、p_mobile、p_sale_id、p_product_name、p_channel_id、p_back_channel_id、p_operator_id、p_province_id、p_order_status
		#为空或为-1时用【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		IF p_order_type=2 && v_query_null_count = 3 && @user_type IN (1,2) THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		IF LENGTH(p_order_type) = 6 && v_query_null_count = 3 && @user_type IN (1,2) THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		SET v_query_days = DATEDIFF(v_end_order_date,p_start_order_date);
		SET v_index_days = 0;
		WHILE v_index_days <= v_query_days DO 
			
			IF v_query_days = 0 THEN
				SET v_start_day = p_start_order_date;
				SET v_end_day = v_end_order_date;
			ELSE
				IF v_index_days = 0 THEN
					SET v_start_day = p_start_order_date;
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);
				ELSEIF v_index_days = v_query_days THEN
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = v_end_order_date;
				ELSE
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);				
				END IF;
			END IF;
			
			#select v_result_days;
			
			#默认查询多少天不进行缓存，时时查询			
			#IF DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S') BETWEEN CONCAT(DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y%m%d'),'000000') AND CONCAT(DATE_FORMAT(NOW(),'%Y%m%d'),'000000')
			IF DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S') BETWEEN CONCAT(DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y%m%d'),'000000') AND DATE_FORMAT(NOW(),'%Y%m%d%H%i%S')			
			THEN
				#select v_start_day,v_end_day,DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d');
				SET @strsqlStatDays = CONCAT('INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)'
				 ,'\r SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionStatStr
				 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
				 ,'\r GROUP BY od.order_status');	
				 
				#select 1;
				#SELECT @strsqlStatDays;
				
				PREPARE stmtsqlStatDays FROM @strsqlStatDays; 
				EXECUTE stmtsqlStatDays; 
				DEALLOCATE PREPARE stmtsqlStatDays;
			ELSE 
				SET @sql_fulltext = CONCAT('SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''''%Y%m%d'''') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionSqlFullTextStr
				 ,'\r AND od.order_date BETWEEN ''''',v_start_day,''''' AND ''''',v_end_day,''''''
				 ,'\r GROUP BY od.order_status');
				 
				 #select @sql_fulltext;
				 
				SET @sql_fulltext_exec = CONCAT('SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionStatStr
				 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
				 ,'\r GROUP BY od.order_status');
					 
				SET v_sql_hash_value = `f_get_hashValue`(@sql_fulltext);
				
				SELECT COUNT(*) 
				INTO v_exists_result
				FROM t_flow_order_sql_result_stat osrs 
				WHERE osrs.`sql_hash_value` = v_sql_hash_value 
				AND osrs.`result_stat_date` = DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S');
				
				#select v_exists_result;
				IF v_exists_result <= 0 THEN
					#select 20;
					 
					SET @strsqlStatDaysResult = CONCAT('INSERT INTO t_flow_order_sql_result_stat(sql_hash_value,sql_text ,sql_fulltext,result_stat_type ,result_stat_date ,sql_result_stat,user_id,user_type,cache_time )'
					 ,'\r SELECT ',v_sql_hash_value,' sql_hash_value'
					 ,'\r ,''',SUBSTR(@sql_fulltext,1,2000),''' sql_text'
					 ,'\r ,''',@sql_fulltext,''' sql_fulltext'
					 ,'\r ,os.order_status result_stat_type'
					 ,'\r ,DATE_FORMAT(os.order_days,''%Y%m%d%H%i%S'') result_stat_date'
					 ,'\r ,CONCAT(''total_count='',os.total_count,'',discount_price_sum='',os.discount_price_sum,'',price_sum='',os.price_sum) sql_result_stat'
					 ,'\r ,',p_user_id,'  user_id'
					 ,'\r ,',@user_type,'  user_type'
					 ,'\r ,''',CURRENT_TIMESTAMP(),'''  cache_time'
					 ,'\r FROM'
					 ,'\r ('
					 ,'\r ',@sql_fulltext_exec
					 ,'\r ) os'
					 ,'\r ON DUPLICATE KEY UPDATE sql_result_stat=CONCAT(''total_count='',os.total_count,'',discount_price_sum='',os.discount_price_sum,'',price_sum='',os.price_sum)');
					 
					#SELECT @strsqlStatDaysResult;
					
					PREPARE stmtsqlStatDaysResult FROM @strsqlStatDaysResult; 
					EXECUTE stmtsqlStatDaysResult; 
					DEALLOCATE PREPARE stmtsqlStatDaysResult;
				END IF;
				
				INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)
				SELECT osrs.result_stat_type order_status
				,DATE_FORMAT(v_start_day,'%Y%m%d') order_days
				,f_str_split(`f_str_split`(sql_result_stat,',',1),'=',2) total_count
				,f_str_split(`f_str_split`(sql_result_stat,',',2),'=',2) discount_price_sum
				,f_str_split(`f_str_split`(sql_result_stat,',',3),'=',2) price_sum
				FROM t_flow_order_sql_result_stat osrs 
				WHERE osrs.`sql_hash_value` = v_sql_hash_value 
				AND osrs.`result_stat_date` = DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S');
				
			END IF;			
			
			SET v_index_days = v_index_days +1; 
		END WHILE; 
		
		SET @strsqlStat = CONCAT('SELECT SUM(al.total_count) total_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.total_count ELSE 0 END ) success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.discount_price_sum ELSE 0 END ) success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.total_count ELSE 0 END ) faile_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.discount_price_sum ELSE 0 END ) faile_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.total_count ELSE 0 END ) wait_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.discount_price_sum ELSE 0 END ) wait_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.total_count ELSE 0 END ) submit_success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.discount_price_sum ELSE 0 END ) submit_success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.price_sum ELSE 0 END ) success_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.price_sum ELSE 0 END ) faile_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.price_sum ELSE 0 END ) wait_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.price_sum ELSE 0 END ) submit_success_price '
		 ,'\r INTO  @total_count,@success_count,@success_amount,@faile_count,@faile_amount,@wait_count,@wait_amount,@submit_success_count,@submit_success_amount ,@success_price,@faile_price,@wait_price,@submit_success_price'
		 ,'\r FROM temp_order_status_stat al');
		#SELECT @strsqlStat;
		
		#select * from temp_order_status_stat;
		
		PREPARE stmtsqlStat FROM @strsqlStat; 
		EXECUTE stmtsqlStat; 
		DEALLOCATE PREPARE stmtsqlStat;
		
		SET p_total_count =  @total_count;
		SET p_success_count = @success_count;
		SET p_success_amount =  @success_amount;
		SET p_faile_count = @faile_count;
		SET p_faile_amount = @faile_amount;
		SET p_wait_count = @wait_count;
		SET p_wait_amount = @wait_amount;
		SET p_submit_success_count = @submit_success_count;
		SET p_submit_success_amount = @submit_success_amount;
		SET p_success_price =  @success_price;
		SET p_faile_price = @faile_price;
		SET p_wait_price = @wait_price;
		SET p_submit_success_price = @submit_success_price;
	ELSE		
		SET p_total_count =  0;
		SET p_success_count = 0;
		SET p_success_amount =  0;
		SET p_faile_count = 0;
		SET p_faile_amount = 0;
		SET p_wait_count = 0;
		SET p_wait_amount = 0;
		SET p_submit_success_count = 0;
		SET p_submit_success_amount = 0;
		SET p_success_price =  0;
		SET p_faile_price = 0;
		SET p_wait_price = 0;
		SET p_submit_success_price = 0;
	END IF;
		
	#SELECT @strsql;
	
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql; 
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_query_order_lk
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_query_order_lk`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_query_order_lk`(IN p_order_type INT ,
		IN p_user_id INT ,
		IN p_user_name VARCHAR(50),
		IN p_user_code VARCHAR(50),
		IN p_order_code VARCHAR(50),
		IN p_mobile VARCHAR(100),
		IN p_channel_id VARCHAR(4000),
		IN p_back_channel_id VARCHAR(4000),
		IN p_operator_id INT ,
		IN p_province_id INT ,
		IN p_order_status VARCHAR(50),
		IN p_start_order_date VARCHAR(50),
		IN p_end_order_date VARCHAR(50),
		IN p_product_name VARCHAR(50),
		IN p_sale_id BIGINT ,
		IN p_pageIndex INT ,
		IN p_pageSize INT ,
		IN p_is_count INT ,
		OUT p_total_count INT ,
		OUT p_success_count INT ,
		OUT p_success_amount DECIMAL(19 ,3),
		OUT p_faile_count INT ,
		OUT p_faile_amount DECIMAL(19 ,3),
		OUT p_wait_count INT ,
		OUT p_wait_amount DECIMAL(19 ,3),
		OUT p_submit_success_count INT ,
		OUT p_submit_success_amount DECIMAL(19 ,3),
		OUT p_success_price DECIMAL(19 ,3),
		OUT p_faile_price DECIMAL(19 ,3),
		OUT p_wait_price DECIMAL(19 ,3),
		OUT p_submit_success_price DECIMAL(19 ,3))
BEGIN  	
	DECLARE v_order_table VARCHAR(100);
	DECLARE v_proxy_enterprise_str VARCHAR(1000);
	#DECLARE v_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_id_title VARCHAR(1000);
	DECLARE v_channel_str VARCHAR(500) DEFAULT '';
	DECLARE v_back_channel_str VARCHAR(500) DEFAULT '';	
	#DECLARE v_inner_table VARCHAR(500) DEFAULT '';
	#DECLARE v_inner_title VARCHAR(4000) DEFAULT '';
	DECLARE v_dbowner VARCHAR(50) DEFAULT '';
	DECLARE v_proxy_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_query_days INT DEFAULT 1;
	DECLARE v_index_days INT DEFAULT 0;
	DECLARE v_start_day VARCHAR(100);
	DECLARE v_end_day VARCHAR(100);
	DECLARE v_query_null_count INT DEFAULT 0;#是否使用索引标识
	DECLARE v_end_order_date VARCHAR(100);#结束时间结尾加.999999
	DECLARE v_result_days INTEGER DEFAULT -1;#默认配置查询天数
	DECLARE v_exists_result INTEGER DEFAULT 0;
	DECLARE v_sql_hash_value BIGINT;	
	
	SET @is_manager = 0;
	SET @user_type = 0;
	SET @proxy_id = 0;
	SET @is_all_proxy = 0;
	SET @is_all_enterprise = 0;
	SET @enterprise_id = 0;
	
	SET @totalStat = 0;
	SET @conditionStr = '';
	SET @conditionStatStr = '';
	SET @conditionSqlFullTextStr = '';
	SET @indexStr = '';
	#结束时间结尾加.999999
	SET v_end_order_date = CONCAT(p_end_order_date,'.999999');
	
	/*2
	SET v_inner_title = CONCAT('\r ,(SELECT dt.province_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) province_name'
				,'\r ,(SELECT dt.city_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) city_name');
	*/
	IF (p_order_type = 1) THEN 
		SET v_order_table = 't_flow_order_pre';
		SET v_channel_str = ' AND od.order_status IN (0,1)';
		SET v_back_channel_str = ' AND od.order_status IN (3,4)';
		set v_result_days = -999;
	ELSEIF (p_order_type = 2) THEN 
		SET v_order_table = 't_flow_order';
		SET v_channel_str = ' AND od.order_status IN (2,3)';
		SET v_back_channel_str = ' AND od.order_status IN (5,6)';
	ELSEIF (p_order_type = 3) THEN 
		SET v_order_table = 't_flow_order_cache';
		set v_result_days = -999;
	ELSEIF (p_order_type = 4) THEN 
		SET v_order_table = 't_flow_order_channel_cache';
		SET v_result_days = -999;
	ELSEIF (LENGTH(p_order_type) = 6) THEN 
		SET v_order_table = CONCAT('t_flow_order_',p_order_type);
	END IF; 
	#获取用户相关数据
	SELECT u.is_manager,u.user_type,u.proxy_id,u.is_all_proxy,u.is_all_enterprise,u.enterprise_id
	INTO @is_manager,@user_type,@proxy_id,@is_all_proxy,@is_all_enterprise,@enterprise_id
	FROM t_flow_sys_user u
	WHERE u.user_id = p_user_id;
	
	#创建代理商临时表
	DROP TEMPORARY TABLE IF EXISTS temp_proxy_all_list;	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_proxy_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,proxy_id BIGINT,depth INT);  
	#创建企业临时表
	DROP TEMPORARY TABLE IF EXISTS temp_enterprise_all_list;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_enterprise_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,enterprise_id BIGINT);  
	#创建统计临时表
	DROP TEMPORARY TABLE IF EXISTS temp_order_status_stat;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_status_stat    
	(sno INT PRIMARY KEY AUTO_INCREMENT
	,order_status BIGINT
	,order_days BIGINT
	,total_count BIGINT
	,discount_price_sum DECIMAL(19,3)
	,price_sum DECIMAL(19,3));  
	
	#是管理员
	IF @user_type IN (1,2) THEN
		IF @is_manager = 1 THEN 
			IF @user_type = 1 THEN #运营端
				#生成顶级代理相关代理数据
				CALL p_get_proxy_child(@proxy_id,0);
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		ELSE
			IF @user_type = 1 THEN #运营端
				#通过数据权限获取代理商数据
				IF @is_all_proxy = 1 THEN				
					CALL p_get_proxy_child(@proxy_id,0);
				ELSE
					CALL p_get_user_proxy(p_user_id);
				END IF;	
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		#默认可以查询直属数据权限
		INSERT INTO temp_proxy_all_list(proxy_id) VALUES(@proxy_id);
		IF @user_type = 1 THEN #运营端
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id;
			ELSE
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id AND pl.proxy_id <> @proxy_id;
			END IF;
		ELSEIF @user_type = 2 THEN #代理商
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM t_flow_enterprise e
				WHERE e.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		
		#用户所分配的企业权限
		INSERT INTO temp_enterprise_all_list(enterprise_id)
		SELECT pe.enterprise_id
		FROM t_flow_enterprise_user pe
		WHERE pe.user_id = p_user_id;
	END IF;
	#select @is_manager,@user_type;
	#仅四个用户能查询该企业判断 2016-11-21加
	IF p_user_id NOT IN (168,173,183,383) THEN
		DELETE FROM temp_enterprise_all_list WHERE enterprise_id IN(717,718);
	END IF; 
	#获取代理商ID
	 SELECT IFNULL(GROUP_CONCAT(tp.proxy_id),'-1')
	 INTO v_proxy_id_str
	 FROM 
	 (
		SELECT DISTINCT proxy_id FROM temp_proxy_all_list
	 ) tp;
	 #获取企业ID
	 SELECT IFNULL(GROUP_CONCAT(tp.enterprise_id),'-1')
	 INTO v_enterprise_id_str
	 FROM 
	 (
		SELECT DISTINCT enterprise_id FROM temp_enterprise_all_list
	 ) tp;
	
	#数据权限
	#用户类型(1：尚通、2：代理商、3企业)
	IF @user_type IN (1,2) && @is_manager = 1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
		,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
	ELSE
		IF @user_type = 1 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		ELSEIF @user_type = 2 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')'
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.enterprise_id = ',@enterprise_id);
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id = ',@enterprise_id);
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id = ',@enterprise_id);
		END IF;
	END IF;
	
	#SELECT * FROM temp_proxy_all_list; 	
	#用户名称,顶级代理,手机号,运营商,归属地,流量包名称,折后价格,通道编码,提交时间,完成时间,订单状态		
	#条件过滤
	IF p_user_name IS NOT NULL && p_user_name <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_name_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_name,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_name_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_name,'%');
	
		IF @user_type IN(1,2) THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,') '
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_name_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_name_str,'))');
		ELSEIF @user_type = 3 THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.proxy_id IN (',v_proxy_id_name_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_name_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_name_str,')');
		END IF;
	END IF;	
	
	IF p_user_code IS NOT NULL && p_user_code <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_code_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_code,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_code_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_code,'%');
		
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_code_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_code_str,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_code_str,')');
		END IF;
	END IF;	
	
	IF p_order_code IS NOT NULL && p_order_code <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');		
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.order_code LIKE ''''',p_order_code,'%''''');
	END IF;
	
	IF p_mobile IS NOT NULL && p_mobile <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.mobile = ''''',p_mobile,'''''');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_channel_id IS NOT NULL && p_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_sale_id IS NOT NULL && p_sale_id <> -1 THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_sales_str
		FROM t_flow_proxy p 
		WHERE p.`sale_id` = p_sale_id;
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_sales_str
		FROM t_flow_enterprise e
		WHERE e.`sale_id` = p_sale_id;
		
		IF @user_type IN(1,2) THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id IN (',v_proxy_id_sales_str,')'
			,'\r OR od.enterprise_id IN (',v_enterprise_id_sales_str,'))');
		ELSEIF @user_type = 3 THEN			
			SET @conditionStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id IN (',v_enterprise_id_sales_str,')');
		END IF;
	END IF;
	
	IF p_back_channel_id IS NOT NULL && p_back_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_operator_id IS NOT NULL && p_operator_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.operator_id = ',p_operator_id);
	END IF;
	IF p_province_id IS NOT NULL && p_province_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.province_id = ',p_province_id);
	END IF;
	
	IF p_order_status IS NOT NULL && p_order_status <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.order_status IN (',p_order_status,')');
	END IF;
	
	IF p_product_name IS NOT NULL && p_product_name <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.product_name = ''''',p_product_name,'''''');
	END IF;
	
	#select @conditionStr;
	#动态SQL语句
	#set @user_type = 3;
	
	SET @strsql = CONCAT('SELECT od.order_id'
	,'\r ,CASE WHEN od.proxy_id <=0 THEN od.enterprise_name ELSE od.proxy_name END proxy_name'
	,'\r ,od.enterprise_id'
	,'\r ,od.proxy_id'
	,'\r ,od.one_proxy_name top_proxy_name'
	,'\r ,od.mobile'
	,'\r ,CASE od.operator_id WHEN 1 THEN ''中国移动'' WHEN 2 THEN ''中国联通'' WHEN 3 THEN ''中国电信'' END  operator_name'
	,'\r ,od.product_province_id province_id'
	,'\r ,od.province_name'
	,'\r ,od.city_name'
	,'\r ,od.product_name'
	,'\r ,od.discount_price'
	,'\r ,od.price'
	,'\r ,od.back_channel_code bc_channel_code'
	,'\r ,od.channel_code'
	,'\r ,od.order_date'
	,'\r ,od.complete_time'
	,'\r ,od.order_status'
	,'\r ,od.refund_id'
	,'\r ,od.proxy_id'
	,'\r ,od.enterprise_id'	
	,'\r ,od.order_code' 
	,'\r ,CASE od.pay_type WHEN 1 THEN ''账户余额'' WHEN 2 THEN ''微信'' WHEN 3 THEN ''支付宝'' END pay_type' #支付类型（1：账户余额、2：微信、3：支付宝）
	,'\r ,CASE od.source_type WHEN 1 THEN ''下游接口'' WHEN 2 THEN ''平台'' WHEN 3 THEN ''网站'' WHEN 4 THEN ''移动端'' END source_type' #来源类型 1下游接口 2平台 3网站 4移动端
	,'\r ,od.orderno_id'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_discount END top_discount'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_rebate_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_rebate_discount END top_rebate_discount'
	,'\r ,od.back_content'
	,'\r ,od.channel_order_code'
	,'\r FROM ',v_order_table,' AS od '
	#,v_inner_table
	,'\r WHERE od.order_date BETWEEN ''',p_start_order_date,''' AND ''',v_end_order_date,''''
	,@conditionStr
	,'\r ORDER BY od.order_date DESC '
	,'\r LIMIT ',(p_pageIndex-1)*p_pageSize,',',p_pageSize);
	
	/*
	订单类型
	0：等待提交
	1：提交成功 
	2：充值成功 
	3：充值/提交 失败，再次等待备用通道提交 
	4：备用通道提交成功
	5：备用通道充值成功  
	6：备用通道 充值/提交 失败	
	*/
	# 充值成功 '2','5'
	# 充值/提交 失败 '6','3'
	# 等待提交 '0','1','4'
	#统计数SQL;
	
	IF p_is_count = 1 THEN 	
		#判断使用索引，当仅有时间查询条件时，使用 【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		#p_user_name、p_mobile、p_sale_id、p_product_name、p_channel_id、p_back_channel_id、p_operator_id、p_province_id、p_order_status
		#为空或为-1时用【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		IF p_order_type=2 && v_query_null_count = 3 && @user_type IN (1,2) THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		IF LENGTH(p_order_type) = 6 && v_query_null_count = 3 && @user_type IN (1,2) THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		SET v_query_days = DATEDIFF(v_end_order_date,p_start_order_date);
		SET v_index_days = 0;
		WHILE v_index_days <= v_query_days DO 
			
			IF v_query_days = 0 THEN
				SET v_start_day = p_start_order_date;
				SET v_end_day = v_end_order_date;
			ELSE
				IF v_index_days = 0 THEN
					SET v_start_day = p_start_order_date;
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);
				ELSEIF v_index_days = v_query_days THEN
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = v_end_order_date;
				ELSE
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);				
				END IF;
			END IF;
			#默认查询多少天不进行缓存，时时查询			
			#IF DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S') BETWEEN CONCAT(DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y%m%d'),'000000') AND CONCAT(DATE_FORMAT(NOW(),'%Y%m%d'),'000000')
			IF DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S') BETWEEN CONCAT(DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y%m%d'),'000000') AND DATE_FORMAT(NOW(),'%Y%m%d%H%i%S')			
			THEN
				#select v_start_day,v_end_day,DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d');
				SET @strsqlStatDays = CONCAT('INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)'
				 ,'\r SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionStatStr
				 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
				 ,'\r GROUP BY od.order_status');	
				 
				#select 1;
				#SELECT @strsqlStatDays;
				
				PREPARE stmtsqlStatDays FROM @strsqlStatDays; 
				EXECUTE stmtsqlStatDays; 
				DEALLOCATE PREPARE stmtsqlStatDays;
			ELSE 
				SET @sql_fulltext = CONCAT('SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''''%Y%m%d'''') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionSqlFullTextStr
				 ,'\r AND od.order_date BETWEEN ''''',v_start_day,''''' AND ''''',v_end_day,''''''
				 ,'\r GROUP BY od.order_status');
				 
				 #select @sql_fulltext;
				 
				SET @sql_fulltext_exec = CONCAT('SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionStatStr
				 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
				 ,'\r GROUP BY od.order_status');
					 
				SET v_sql_hash_value = `f_get_hashValue`(@sql_fulltext);
				
				SELECT COUNT(*) 
				INTO v_exists_result
				FROM t_flow_order_sql_result_stat osrs 
				WHERE osrs.`sql_hash_value` = v_sql_hash_value 
				AND osrs.`result_stat_date` = DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S');
				
				#select v_exists_result;
				IF v_exists_result <= 0 THEN
					#select 20;
					 
					SET @strsqlStatDaysResult = CONCAT('INSERT INTO t_flow_order_sql_result_stat(sql_hash_value,sql_text ,sql_fulltext,result_stat_type ,result_stat_date ,sql_result_stat,user_id,user_type,cache_time )'
					 ,'\r SELECT ',v_sql_hash_value,' sql_hash_value'
					 ,'\r ,''',SUBSTR(@sql_fulltext,1,2000),''' sql_text'
					 ,'\r ,''',@sql_fulltext,''' sql_fulltext'
					 ,'\r ,os.order_status result_stat_type'
					 ,'\r ,DATE_FORMAT(os.order_days,''%Y%m%d%H%i%S'') result_stat_date'
					 ,'\r ,CONCAT(''total_count='',os.total_count,'',discount_price_sum='',os.discount_price_sum,'',price_sum='',os.price_sum) sql_result_stat'
					 ,'\r ,',p_user_id,'  user_id'
					 ,'\r ,',@user_type,'  user_type'
					 ,'\r ,''',CURRENT_TIMESTAMP(),'''  cache_time'
					 ,'\r FROM'
					 ,'\r ('
					 ,'\r ',@sql_fulltext_exec
					 ,'\r ) os'
					 ,'\r ON DUPLICATE KEY UPDATE sql_result_stat=CONCAT(''total_count='',os.total_count,'',discount_price_sum='',os.discount_price_sum,'',price_sum='',os.price_sum)');
					 
					#SELECT @strsqlStatDaysResult;
					
					PREPARE stmtsqlStatDaysResult FROM @strsqlStatDaysResult; 
					EXECUTE stmtsqlStatDaysResult; 
					DEALLOCATE PREPARE stmtsqlStatDaysResult;
				END IF;
				
				INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)
				SELECT osrs.result_stat_type order_status
				,DATE_FORMAT(v_start_day,'%Y%m%d') order_days
				,f_str_split(`f_str_split`(sql_result_stat,',',1),'=',2) total_count
				,f_str_split(`f_str_split`(sql_result_stat,',',2),'=',2) discount_price_sum
				,f_str_split(`f_str_split`(sql_result_stat,',',3),'=',2) price_sum
				FROM t_flow_order_sql_result_stat osrs 
				WHERE osrs.`sql_hash_value` = v_sql_hash_value 
				AND osrs.`result_stat_date` = DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S');
				
			END IF;			
			
			SET v_index_days = v_index_days +1; 
		END WHILE; 
		
		SET @strsqlStat = CONCAT('SELECT SUM(al.total_count) total_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.total_count ELSE 0 END ) success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.discount_price_sum ELSE 0 END ) success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.total_count ELSE 0 END ) faile_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.discount_price_sum ELSE 0 END ) faile_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.total_count ELSE 0 END ) wait_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.discount_price_sum ELSE 0 END ) wait_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.total_count ELSE 0 END ) submit_success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.discount_price_sum ELSE 0 END ) submit_success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.price_sum ELSE 0 END ) success_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.price_sum ELSE 0 END ) faile_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.price_sum ELSE 0 END ) wait_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.price_sum ELSE 0 END ) submit_success_price '
		 ,'\r INTO  @total_count,@success_count,@success_amount,@faile_count,@faile_amount,@wait_count,@wait_amount,@submit_success_count,@submit_success_amount ,@success_price,@faile_price,@wait_price,@submit_success_price'
		 ,'\r FROM temp_order_status_stat al');
		#SELECT @strsqlStat;
		
		#select * from temp_order_status_stat;
		
		PREPARE stmtsqlStat FROM @strsqlStat; 
		EXECUTE stmtsqlStat; 
		DEALLOCATE PREPARE stmtsqlStat;
		
		SET p_total_count =  @total_count;
		SET p_success_count = @success_count;
		SET p_success_amount =  @success_amount;
		SET p_faile_count = @faile_count;
		SET p_faile_amount = @faile_amount;
		SET p_wait_count = @wait_count;
		SET p_wait_amount = @wait_amount;
		SET p_submit_success_count = @submit_success_count;
		SET p_submit_success_amount = @submit_success_amount;
		SET p_success_price =  @success_price;
		SET p_faile_price = @faile_price;
		SET p_wait_price = @wait_price;
		SET p_submit_success_price = @submit_success_price;
	ELSE		
		SET p_total_count =  0;
		SET p_success_count = 0;
		SET p_success_amount =  0;
		SET p_faile_count = 0;
		SET p_faile_amount = 0;
		SET p_wait_count = 0;
		SET p_wait_amount = 0;
		SET p_submit_success_count = 0;
		SET p_submit_success_amount = 0;
		SET p_success_price =  0;
		SET p_faile_price = 0;
		SET p_wait_price = 0;
		SET p_submit_success_price = 0;
	END IF;
		
	SELECT @strsql;
	
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql; 
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_query_order_test
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_query_order_test`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_query_order_test`(IN p_order_type INT ,
		IN p_user_id INT ,
		IN p_user_name VARCHAR(50),
		IN p_user_code VARCHAR(50),
		IN p_order_code VARCHAR(50),
		IN p_mobile VARCHAR(100),
		IN p_channel_id VARCHAR(4000),
		IN p_back_channel_id VARCHAR(4000),
		IN p_operator_id INT ,
		IN p_province_id INT ,
		IN p_order_status VARCHAR(50),
		IN p_start_order_date VARCHAR(50),
		IN p_end_order_date VARCHAR(50),
		IN p_product_name VARCHAR(50),
		IN p_sale_id BIGINT ,
		IN p_pageIndex INT ,
		IN p_pageSize INT ,
		IN p_is_count INT ,
		OUT p_total_count INT ,
		OUT p_success_count INT ,
		OUT p_success_amount DECIMAL(19 ,3),
		OUT p_faile_count INT ,
		OUT p_faile_amount DECIMAL(19 ,3),
		OUT p_wait_count INT ,
		OUT p_wait_amount DECIMAL(19 ,3),
		OUT p_submit_success_count INT ,
		OUT p_submit_success_amount DECIMAL(19 ,3),
		OUT p_success_price DECIMAL(19 ,3),
		OUT p_faile_price DECIMAL(19 ,3),
		OUT p_wait_price DECIMAL(19 ,3),
		OUT p_submit_success_price DECIMAL(19 ,3))
BEGIN  	
	DECLARE v_order_table VARCHAR(100);
	DECLARE v_proxy_enterprise_str VARCHAR(1000);
	#DECLARE v_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_name_title VARCHAR(1000);
	#DECLARE v_top_proxy_id_title VARCHAR(1000);
	DECLARE v_channel_str VARCHAR(500) DEFAULT '';
	DECLARE v_back_channel_str VARCHAR(500) DEFAULT '';	
	#DECLARE v_inner_table VARCHAR(500) DEFAULT '';
	#DECLARE v_inner_title VARCHAR(4000) DEFAULT '';
	DECLARE v_dbowner VARCHAR(50) DEFAULT '';
	DECLARE v_proxy_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_name_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_code_str VARCHAR(4000) DEFAULT '';
	DECLARE v_proxy_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_enterprise_id_sales_str VARCHAR(4000) DEFAULT '';
	DECLARE v_query_days INT DEFAULT 1;
	DECLARE v_index_days INT DEFAULT 0;
	DECLARE v_start_day VARCHAR(100);
	DECLARE v_end_day VARCHAR(100);
	DECLARE v_query_null_count INT DEFAULT 0;#是否使用索引标识
	DECLARE v_end_order_date VARCHAR(100);#结束时间结尾加.999999
	DECLARE v_result_days INTEGER DEFAULT -1;#默认配置查询天数
	DECLARE v_exists_result INTEGER DEFAULT 0;
	DECLARE v_sql_hash_value BIGINT;
	DECLARE v_is_count INT DEFAULT 0;
	#小时差
	declare v_query_times INT DEFAULT 0;
	
	SET @is_manager = 0;
	SET @user_type = 0;
	SET @proxy_id = 0;
	SET @is_all_proxy = 0;
	SET @is_all_enterprise = 0;
	SET @enterprise_id = 0;
	
	SET @totalStat = 0;
	SET @conditionStr = '';
	SET @conditionStatStr = '';
	SET @conditionSqlFullTextStr = '';
	SET @indexStr = '';
	#结束时间结尾加.999999
	SET v_end_order_date = CONCAT(p_end_order_date,'.999999');
	SET v_is_count = p_is_count;
	
	/*2
	SET v_inner_title = CONCAT('\r ,(SELECT dt.province_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) province_name'
				,'\r ,(SELECT dt.city_name FROM t_flow_sys_mobile_dict dt WHERE od.mobile=dt.mobile ) city_name');
	*/
	IF (p_order_type = 1) THEN 
		SET v_order_table = 't_flow_order_pre';
		SET v_channel_str = ' AND od.order_status IN (0,1)';
		SET v_back_channel_str = ' AND od.order_status IN (3,4)';
		SET v_result_days = -999;
	ELSEIF (p_order_type = 2) THEN 
		SET v_order_table = 't_flow_order';
		SET v_channel_str = ' AND od.order_status IN (2,3)';
		SET v_back_channel_str = ' AND od.order_status IN (5,6)';
	ELSEIF (p_order_type = 3) THEN 
		SET v_order_table = 't_flow_order_cache';
		SET v_result_days = -999;
	ELSEIF (p_order_type = 4) THEN 
		SET v_order_table = 't_flow_order_channel_cache';
		SET v_result_days = -999;
	ELSEIF (LENGTH(p_order_type) = 6) THEN 
		SET v_order_table = CONCAT('t_flow_order_',p_order_type);
		SET v_channel_str = ' AND od.order_status IN (2,3)';
		SET v_back_channel_str = ' AND od.order_status IN (5,6)';
	END IF; 
	#获取用户相关数据
	SELECT u.is_manager,u.user_type,u.proxy_id,u.is_all_proxy,u.is_all_enterprise,u.enterprise_id
	INTO @is_manager,@user_type,@proxy_id,@is_all_proxy,@is_all_enterprise,@enterprise_id
	FROM t_flow_sys_user u
	WHERE u.user_id = p_user_id;
	
	#创建代理商临时表
	DROP TEMPORARY TABLE IF EXISTS temp_proxy_all_list;	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_proxy_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,proxy_id BIGINT,depth INT);  
	#创建企业临时表
	DROP TEMPORARY TABLE IF EXISTS temp_enterprise_all_list;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_enterprise_all_list    
	(sno INT PRIMARY KEY AUTO_INCREMENT,enterprise_id BIGINT);  
	#创建统计临时表
	DROP TEMPORARY TABLE IF EXISTS temp_order_status_stat;  	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_status_stat    
	(sno INT PRIMARY KEY AUTO_INCREMENT
	,order_status BIGINT
	,order_days BIGINT
	,total_count BIGINT
	,discount_price_sum DECIMAL(19,3)
	,price_sum DECIMAL(19,3));  
	
	#是管理员
	IF @user_type IN (1,2) THEN
		IF @is_manager = 1 THEN 
			IF @user_type = 1 THEN #运营端
				#生成顶级代理相关代理数据
				CALL p_get_proxy_child(@proxy_id,0);
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		ELSE
			IF @user_type = 1 THEN #运营端
				#通过数据权限获取代理商数据
				IF @is_all_proxy = 1 THEN				
					CALL p_get_proxy_child(@proxy_id,0);
				ELSE
					CALL p_get_user_proxy(p_user_id);
				END IF;	
			ELSEIF @user_type = 2 THEN #代理商端
				INSERT INTO temp_proxy_all_list(proxy_id ,depth)
				SELECT px.`proxy_id` ,1 depth
				FROM t_flow_proxy px 
				WHERE px.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		#默认可以查询直属数据权限
		INSERT INTO temp_proxy_all_list(proxy_id) VALUES(@proxy_id);
		IF @user_type = 1 THEN #运营端
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id;
			ELSE
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM temp_proxy_all_list pl,t_flow_enterprise e
				WHERE pl.proxy_id = e.top_proxy_id AND pl.proxy_id <> @proxy_id;
			END IF;
		ELSEIF @user_type = 2 THEN #代理商
			#获取企业临时表数据
			IF @is_all_enterprise = 1 THEN
				INSERT INTO temp_enterprise_all_list(enterprise_id)
				SELECT e.enterprise_id
				FROM t_flow_enterprise e
				WHERE e.top_proxy_id = @proxy_id;
			END IF;
		END IF;
		
		#用户所分配的企业权限
		INSERT INTO temp_enterprise_all_list(enterprise_id)
		SELECT pe.enterprise_id
		FROM t_flow_enterprise_user pe
		WHERE pe.user_id = p_user_id;
	END IF;
	#select @is_manager,@user_type;
	#仅四个用户能查询该企业判断 2016-11-21加
	IF p_user_id NOT IN (168,173,183,383) THEN
		DELETE FROM temp_enterprise_all_list WHERE enterprise_id IN(717,718);
	END IF; 
	
	#SELECT * FROM temp_proxy_all_list; 	
	#用户名称,顶级代理,手机号,运营商,归属地,流量包名称,折后价格,通道编码,提交时间,完成时间,订单状态		
	#条件过滤
	IF p_user_name IS NOT NULL && p_user_name <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_name_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_name,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_name_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_name,'%');
		
		DELETE FROM  temp_proxy_all_list WHERE proxy_id NOT IN (v_proxy_id_name_str);
		DELETE FROM  temp_enterprise_all_list WHERE enterprise_id NOT IN (v_enterprise_id_name_str);
	END IF;	
	
	IF p_user_code IS NOT NULL && p_user_code <> '' THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_code_str
		FROM t_flow_proxy p 
		WHERE p.`proxy_name` LIKE CONCAT('%',p_user_code,'%');
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_code_str
		FROM t_flow_enterprise e
		WHERE e.`enterprise_name` LIKE CONCAT('%',p_user_code,'%');
		
		DELETE FROM  temp_proxy_all_list WHERE proxy_id NOT IN (v_proxy_id_code_str);
		DELETE FROM  temp_enterprise_all_list WHERE enterprise_id NOT IN (v_enterprise_id_code_str);
	END IF;	
	
	IF p_sale_id IS NOT NULL && p_sale_id <> -1 THEN
		SELECT IFNULL(GROUP_CONCAT(p.`proxy_id` ),'-1')
		INTO v_proxy_id_sales_str
		FROM t_flow_proxy p 
		WHERE p.`sale_id` = p_sale_id;
		
		SELECT IFNULL(GROUP_CONCAT(e.`enterprise_id`),'-1')
		INTO v_enterprise_id_sales_str
		FROM t_flow_enterprise e
		WHERE e.`sale_id` = p_sale_id;
		DELETE FROM  temp_proxy_all_list WHERE proxy_id NOT IN (v_proxy_id_sales_str);
		DELETE FROM  temp_enterprise_all_list WHERE enterprise_id NOT IN (v_enterprise_id_sales_str);
	END IF;
	
	#获取代理商ID
	 SELECT IFNULL(GROUP_CONCAT(tp.proxy_id),'-1')
	 INTO v_proxy_id_str
	 FROM 
	 (
		SELECT DISTINCT proxy_id FROM temp_proxy_all_list
	 ) tp;
	 #获取企业ID
	 SELECT IFNULL(GROUP_CONCAT(tp.enterprise_id),'-1')
	 INTO v_enterprise_id_str
	 FROM 
	 (
		SELECT DISTINCT enterprise_id FROM temp_enterprise_all_list
	 ) tp;
	
	IF @user_type IN (1,2)  THEN
		if  v_proxy_id_str = '-1' AND v_enterprise_id_str ='-1' then
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND 1<>1');
			SET v_is_count = 0;
		elseIF v_proxy_id_str = '-1' AND v_enterprise_id_str <>'-1' THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND  od.enterprise_id in (',v_enterprise_id_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND  od.enterprise_id in (',v_enterprise_id_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND  od.enterprise_id in (',v_enterprise_id_str,')');
			
		ELSEIF v_proxy_id_str <>'-1' AND v_enterprise_id_str = '-1' THEN
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,')');
		ELSE
			SET @conditionStr = CONCAT(@conditionStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
			
			SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND ( od.proxy_id in (',v_proxy_id_str,') '
			,'\r OR od.enterprise_id in (',v_enterprise_id_str,'))');
		END IF;
	 
		
	ELSEIF @user_type = 3 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.enterprise_id = ',@enterprise_id);
		
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.enterprise_id = ',@enterprise_id);
		
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.enterprise_id = ',@enterprise_id);
	
	END IF;
	
	
	IF p_order_code IS NOT NULL && p_order_code <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_code LIKE ''',p_order_code,'%''');		
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.order_code LIKE ''''',p_order_code,'%''''');
	END IF;
	
	IF p_mobile IS NOT NULL && p_mobile <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.mobile = ''',p_mobile,'''');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.mobile = ''''',p_mobile,'''''');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_channel_id IS NOT NULL && p_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND (od.channel_id IN (',p_channel_id,')',v_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	
	
	IF p_back_channel_id IS NOT NULL && p_back_channel_id <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND (od.back_channel_id IN (',p_back_channel_id,')',v_back_channel_str,')');
	ELSE
		SET v_query_null_count = v_query_null_count+1;
	END IF;
	
	IF p_operator_id IS NOT NULL && p_operator_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.operator_id = ',p_operator_id);
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.operator_id = ',p_operator_id);
	END IF;
	IF p_province_id IS NOT NULL && p_province_id <> -1 THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.province_id = ',p_province_id);
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.province_id = ',p_province_id);
	END IF;
	
	IF p_order_status IS NOT NULL && p_order_status <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.order_status IN (',p_order_status,')');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.order_status IN (',p_order_status,')');
	END IF;
	
	IF p_product_name IS NOT NULL && p_product_name <> '' THEN
		SET @conditionStr = CONCAT(@conditionStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionStatStr = CONCAT(@conditionStatStr ,'\r AND od.product_name = ''',p_product_name,'''');
		SET @conditionSqlFullTextStr = CONCAT(@conditionSqlFullTextStr ,'\r AND od.product_name = ''''',p_product_name,'''''');
	END IF;
	
	#select @conditionStr;
	#动态SQL语句
	#set @user_type = 3;
	
	SET @strsql = CONCAT('SELECT od.order_id'
	,'\r ,CASE WHEN od.proxy_id <=0 THEN od.enterprise_name ELSE od.proxy_name END proxy_name'
	,'\r ,od.enterprise_id'
	,'\r ,od.proxy_id'
	,'\r ,od.one_proxy_name top_proxy_name'
	,'\r ,od.mobile'
	,'\r ,CASE od.operator_id WHEN 1 THEN ''中国移动'' WHEN 2 THEN ''中国联通'' WHEN 3 THEN ''中国电信'' END  operator_name'
	,'\r ,od.product_province_id province_id'
	,'\r ,od.province_name'
	,'\r ,od.city_name'
	,'\r ,od.product_name'
	,'\r ,od.discount_price'
	,'\r ,od.price'
	,'\r ,od.back_channel_code bc_channel_code'
	,'\r ,od.channel_code'
	,'\r ,od.order_date'
	,'\r ,od.complete_time'
	,'\r ,od.order_status'
	,'\r ,od.refund_id'
	,'\r ,od.proxy_id'
	,'\r ,od.enterprise_id'	
	,'\r ,od.order_code' 
	,'\r ,CASE od.pay_type WHEN 1 THEN ''账户余额'' WHEN 2 THEN ''微信'' WHEN 3 THEN ''支付宝'' END pay_type' #支付类型（1：账户余额、2：微信、3：支付宝）
	,'\r ,CASE od.source_type WHEN 1 THEN ''下游接口'' WHEN 2 THEN ''平台'' WHEN 3 THEN ''网站'' WHEN 4 THEN ''移动端'' END source_type' #来源类型 1下游接口 2平台 3网站 4移动端
	,'\r ,od.orderno_id'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_discount END top_discount'
	,'\r ,CASE WHEN od.order_status IN (0,1,2) THEN od.top_rebate_discount WHEN od.order_status IN (3,4,5,6) THEN od.back_top_rebate_discount END top_rebate_discount'
	,'\r ,od.back_content'
	,'\r ,od.channel_order_code'
	,'\r FROM ',v_order_table,' AS od '
	#,v_inner_table
	,'\r WHERE od.order_date BETWEEN ''',p_start_order_date,''' AND ''',v_end_order_date,''''
	,@conditionStr
	,'\r ORDER BY od.order_date DESC '
	,'\r LIMIT ',(p_pageIndex-1)*p_pageSize,',',p_pageSize);
	
	/*
	订单类型
	0：等待提交
	1：提交成功 
	2：充值成功 
	3：充值/提交 失败，再次等待备用通道提交 
	4：备用通道提交成功
	5：备用通道充值成功  
	6：备用通道 充值/提交 失败	
	*/
	# 充值成功 '2','5'
	# 充值/提交 失败 '6','3'
	# 等待提交 '0','1','4'
	#统计数SQL;
	
	IF v_is_count = 1 THEN 	
		#判断使用索引，当仅有时间查询条件时，使用 【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		#p_user_name、p_mobile、p_sale_id、p_product_name、p_channel_id、p_back_channel_id、p_operator_id、p_province_id、p_order_status
		#为空或为-1时用【SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';】
		IF p_order_type=2 && v_query_null_count = 3 && @user_type IN (1,2) THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		IF LENGTH(p_order_type) = 6 && v_query_null_count = 3 && @user_type IN (1,2) THEN
			SET  @indexStr = ' USE INDEX (ix_t_flow_order_order_status_order_date)';
		END IF;
		
		SET v_query_days = DATEDIFF(v_end_order_date,p_start_order_date);
		set v_query_times = hour(timediff(v_end_order_date,p_start_order_date));
		SET v_index_days = 0;
		WHILE v_index_days <= v_query_days DO 
			
			IF v_query_days = 0 THEN
				SET v_start_day = p_start_order_date;
				SET v_end_day = v_end_order_date;
			ELSE
				IF v_index_days = 0 THEN
					SET v_start_day = p_start_order_date;
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);
				ELSEIF v_index_days = v_query_days THEN
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = v_end_order_date;
				ELSE
					SET v_start_day = DATE_ADD(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),INTERVAL v_index_days DAY);
					SET v_end_day = DATE_ADD(CONCAT(DATE_FORMAT(p_start_order_date,'%Y-%m-%d'),' 23:59:59.999999'),INTERVAL v_index_days DAY);				
				END IF;
			END IF;
			#默认查询多少天不进行缓存，时时查询	
				
			#IF DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S') BETWEEN CONCAT(DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y%m%d'),'000000') AND CONCAT(DATE_FORMAT(NOW(),'%Y%m%d'),'000000')
			IF v_query_times <24 or DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S') BETWEEN CONCAT(DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y%m%d'),'000000') AND DATE_FORMAT(NOW(),'%Y%m%d%H%i%S')			
			THEN
				#select v_start_day,v_end_day,DATE_FORMAT(DATE_ADD(NOW(),INTERVAL v_result_days DAY),'%Y-%m-%d') , DATE_FORMAT(NOW(),'%Y-%m-%d');
				SET @strsqlStatDays = CONCAT('INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)'
				 ,'\r SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionStatStr
				 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
				 ,'\r GROUP BY od.order_status');	
				 
				#SELECT @strsqlStatDays;
				
				PREPARE stmtsqlStatDays FROM @strsqlStatDays; 
				EXECUTE stmtsqlStatDays; 
				DEALLOCATE PREPARE stmtsqlStatDays;
			ELSE 
				SET @sql_fulltext = CONCAT('SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''''%Y%m%d'''') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionSqlFullTextStr
				 ,'\r AND od.order_date BETWEEN ''''',v_start_day,''''' AND ''''',v_end_day,''''''
				 ,'\r GROUP BY od.order_status');
				 
				 #select @sql_fulltext;
				 
				SET @sql_fulltext_exec = CONCAT('SELECT od.order_status'
				 ,'\r ,DATE_FORMAT(od.order_date,''%Y%m%d'') order_days'
				 ,'\r ,COUNT(*) total_count'
				 ,'\r ,SUM(od.discount_price) discount_price_sum'
				 ,'\r ,SUM(od.price) price_sum'
				 ,'\r FROM ',v_order_table,' od '
				 ,@indexStr
				 ,'\r WHERE  od.user_type IN (1,2,3)'
				 ,@conditionStatStr
				 ,'\r AND od.order_date BETWEEN ''',v_start_day,''' AND ''',v_end_day,''''
				 ,'\r GROUP BY od.order_status');
					 
				SET v_sql_hash_value = `f_get_hashValue`(@sql_fulltext);
				
				SELECT COUNT(*) 
				INTO v_exists_result
				FROM t_flow_order_sql_result_stat osrs 
				WHERE osrs.`sql_hash_value` = v_sql_hash_value 
				AND osrs.`result_stat_date` = DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S');
				
				#select v_exists_result;
				IF v_exists_result <= 0 THEN
					#select 20;
					 
					SET @strsqlStatDaysResult = CONCAT('INSERT INTO t_flow_order_sql_result_stat(sql_hash_value,sql_text ,sql_fulltext,result_stat_type ,result_stat_date ,sql_result_stat,user_id,user_type,cache_time )'
					 ,'\r SELECT ',v_sql_hash_value,' sql_hash_value'
					 ,'\r ,''',SUBSTR(@sql_fulltext,1,2000),''' sql_text'
					 ,'\r ,''',@sql_fulltext,''' sql_fulltext'
					 ,'\r ,os.order_status result_stat_type'
					 ,'\r ,DATE_FORMAT(os.order_days,''%Y%m%d%H%i%S'') result_stat_date'
					 ,'\r ,CONCAT(''total_count='',os.total_count,'',discount_price_sum='',os.discount_price_sum,'',price_sum='',os.price_sum) sql_result_stat'
					 ,'\r ,',p_user_id,'  user_id'
					 ,'\r ,',@user_type,'  user_type'
					 ,'\r ,''',CURRENT_TIMESTAMP(),'''  cache_time'
					 ,'\r FROM'
					 ,'\r ('
					 ,'\r ',@sql_fulltext_exec
					 ,'\r ) os'
					 ,'\r ON DUPLICATE KEY UPDATE sql_result_stat=CONCAT(''total_count='',os.total_count,'',discount_price_sum='',os.discount_price_sum,'',price_sum='',os.price_sum)');
					 
					#SELECT @strsqlStatDaysResult;
					
					PREPARE stmtsqlStatDaysResult FROM @strsqlStatDaysResult; 
					EXECUTE stmtsqlStatDaysResult; 
					DEALLOCATE PREPARE stmtsqlStatDaysResult;
				END IF;
				INSERT INTO temp_order_status_stat(order_status,order_days,total_count,discount_price_sum,price_sum)
				SELECT osrs.result_stat_type order_status
				,DATE_FORMAT(v_start_day,'%Y%m%d') order_days
				,f_str_split(`f_str_split`(sql_result_stat,',',1),'=',2) total_count
				,f_str_split(`f_str_split`(sql_result_stat,',',2),'=',2) discount_price_sum
				,f_str_split(`f_str_split`(sql_result_stat,',',3),'=',2) price_sum
				FROM t_flow_order_sql_result_stat osrs 
				WHERE osrs.`sql_hash_value` = v_sql_hash_value 
				AND osrs.`result_stat_date` = DATE_FORMAT(v_start_day,'%Y%m%d%H%i%S');
				
			END IF;			
			
			SET v_index_days = v_index_days +1; 
		END WHILE; 
		
		SET @strsqlStat = CONCAT('SELECT SUM(al.total_count) total_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.total_count ELSE 0 END ) success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.discount_price_sum ELSE 0 END ) success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.total_count ELSE 0 END ) faile_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.discount_price_sum ELSE 0 END ) faile_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.total_count ELSE 0 END ) wait_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.discount_price_sum ELSE 0 END ) wait_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.total_count ELSE 0 END ) submit_success_count'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.discount_price_sum ELSE 0 END ) submit_success_amount'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (2,5) THEN al.price_sum ELSE 0 END ) success_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (6,3) THEN al.price_sum ELSE 0 END ) faile_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (0) THEN al.price_sum ELSE 0 END ) wait_price'
		 ,'\r ,SUM(CASE WHEN al.order_status IN (1,4) THEN al.price_sum ELSE 0 END ) submit_success_price '
		 ,'\r INTO  @total_count,@success_count,@success_amount,@faile_count,@faile_amount,@wait_count,@wait_amount,@submit_success_count,@submit_success_amount ,@success_price,@faile_price,@wait_price,@submit_success_price'
		 ,'\r FROM temp_order_status_stat al');
		#SELECT @strsqlStat;
		
		#select * from temp_order_status_stat;
		
		PREPARE stmtsqlStat FROM @strsqlStat; 
		EXECUTE stmtsqlStat; 
		DEALLOCATE PREPARE stmtsqlStat;
		
		SET p_total_count =  @total_count;
		SET p_success_count = @success_count;
		SET p_success_amount =  @success_amount;
		SET p_faile_count = @faile_count;
		SET p_faile_amount = @faile_amount;
		SET p_wait_count = @wait_count;
		SET p_wait_amount = @wait_amount;
		SET p_submit_success_count = @submit_success_count;
		SET p_submit_success_amount = @submit_success_amount;
		SET p_success_price =  @success_price;
		SET p_faile_price = @faile_price;
		SET p_wait_price = @wait_price;
		SET p_submit_success_price = @submit_success_price;
	ELSE		
		SET p_total_count =  0;
		SET p_success_count = 0;
		SET p_success_amount =  0;
		SET p_faile_count = 0;
		SET p_faile_amount = 0;
		SET p_wait_count = 0;
		SET p_wait_amount = 0;
		SET p_submit_success_count = 0;
		SET p_submit_success_amount = 0;
		SET p_success_price =  0;
		SET p_faile_price = 0;
		SET p_wait_price = 0;
		SET p_submit_success_price = 0;
	END IF;
		
	SELECT @strsql;
	
	PREPARE stmtsql FROM @strsql; 
	EXECUTE stmtsql; 
	DEALLOCATE PREPARE stmtsql; 
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_query_proxy_consume
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_query_proxy_consume`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_query_proxy_consume`(
	IN p_proxy_id BIGINT
	,IN p_proxy_name VARCHAR(200)
	,IN p_start_time DATETIME
	,IN p_end_time DATETIME
	,IN p_pageIndex INT
	,IN p_pageSize INT
	,IN p_is_count INT
	,OUT p_total_count INT
)
BEGIN 
	/*
	author		cxw
	date		2016/08/08
	desc		代理流量消费统计查询
	*/
	DECLARE v_table VARCHAR(100); #统计表名
	DECLARE v_stat_column VARCHAR(4000); #统计列名
	DECLARE v_other_column VARCHAR(4000); #其它显示列名
	DECLARE v_stat_group VARCHAR(4000); #统计分组列
	DECLARE v_condition VARCHAR(4000) DEFAULT ''; #条件
	
	SET v_table = 't_flow_stat_proxy';
	SET v_stat_column = 'stat_count,stat_price,stat_refund_price';
	SET v_other_column = 'proxy_code,proxy_name';
	SET v_stat_group = 'proxy_id';
	
	IF p_proxy_id <> -1 THEN
		SET v_condition = CONCAT('\r','AND sp.proxy_id = ',p_proxy_id);
	END IF;
	IF p_proxy_name IS NOT NULL && p_proxy_name <> '' THEN		
		SET v_condition = CONCAT(v_condition,'\r','AND sp.proxy_name LIKE ''%',p_proxy_name,'%''');
	END IF;
	
	CALL p_get_stat_common(2,205,DATE_FORMAT(p_start_time,'%Y%m%d'),DATE_FORMAT(p_end_time,'%Y%m%d'),v_table,v_stat_column,v_other_column,v_stat_group,v_condition,p_is_count,p_pageIndex,p_pageSize,p_total_count);
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_callback_order
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_callback_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_callback_order`(p_callback_id BIGINT
,p_rece_content TEXT
,p_times INT
,p_status INT
,OUT p_out_flag INT)
BEGIN 
	/*
	author		cxw
	date		2016/07/01
	desc		给下游代理商或企业主动推送回调
	*/
	#输出标志，0代表执行失败，1代表执行成功
	DECLARE v_flag INT DEFAULT 0;
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#事务处理标志符
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始事务
	START TRANSACTION;
		#新增历史回调
		INSERT INTO t_flow_order_callback_his(callback_id,`order_id`,`url`,`content`,`rece_content`,`times`,`status`,`end_date`,channel_id,back_channel_id,orderno_id,order_status)
		SELECT callback_id,`order_id`,`url`,`content`,p_rece_content `rece_content`,p_times `times`, p_status`status`,NOW() `end_date`,channel_id,back_channel_id,orderno_id,order_status
		FROM t_flow_order_callback
		WHERE callback_id = p_callback_id;
		#删除主表回调信息
		DELETE FROM t_flow_order_callback WHERE callback_id = p_callback_id;			
	IF t_error = 1 THEN 
		SET p_out_flag = 0;
		ROLLBACK;  
	ELSE  
		SET p_out_flag = 1;
		COMMIT;  
	END IF;		
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_commit_order
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_commit_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_commit_order`(
IN p_order_id BIGINT
,IN p_order_status INT
,IN p_content VARCHAR(2000)
,IN p_back_content VARCHAR(2000)
,IN p_back_fail_desc VARCHAR(2000)
,IN p_remark VARCHAR(2000)
,IN p_channel_order_code VARCHAR(2000)
,OUT p_out_flag INT)
BEGIN 
	/*
	author		cxw
	date		2015/05/16
	desc		处理成功订单
			2016/09/09 用户产品折扣判断 by lk
			2016/11/01 成功订单发送短信记录 by lk
			2016/11/17 通道设置发短信，用户设置发短信判断 by lk
	*/
	#订单用户类型 1代理商 2企业
	DECLARE v_order_user_type INT;
	#用户的代理商ID
	DECLARE v_proxy_id BIGINT;
	#用户的企业ID
	DECLARE v_enterprise_id BIGINT;
	#订单折后金额
	DECLARE v_discount_price DECIMAL(11,3);
	#订单的标准价格
	DECLARE v_price DECIMAL(11,3);
	#订单的状态
	DECLARE v_order_status INT;
	#订单主通道ID
	DECLARE v_channel_id BIGINT;
	#订单备用通道ID
	DECLARE v_back_channel_id BIGINT;
	#订单主产品ID
	DECLARE v_channel_product_id BIGINT;
	#用户账户ID
	DECLARE v_account_id BIGINT;
	#订单备用产品ID
	DECLARE v_back_channel_product_id BIGINT;
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#订单日志效果类型
	DECLARE v_tran_type INT;
	#订单日期
	DECLARE v_order_date DATETIME(6);
	#手机号省份iD
	DECLARE v_province_id INT;
	#手机号市ID
	DECLARE v_city_id INT;
	DECLARE v_proxy_enterprise_id BIGINT;
	DECLARE v_is_discount INT;
	DECLARE v_operator_id INT;
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	#产品size
	DECLARE v_size INT;
	#计数
	DECLARE v_num INT;
	#监控随机数
	DECLARE v_rand_id INT;
	#通道code
	DECLARE v_channel_order_code VARCHAR(2000);
	#用户发送短信标识
	DECLARE v_user_message_flag INT DEFAULT 0;
	#通道发送短信标识
	DECLARE v_channel_message_flag INT DEFAULT 0;
	
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
		
	#开始事务
	START TRANSACTION;
		#获取订单数据
		SELECT op.user_type order_user_type,op.proxy_id,op.enterprise_id,op.discount_price,op.price,op.order_status,op.back_channel_id,op.channel_id
		,op.channel_product_id,op.back_channel_product_id,op.top_discount,op.back_top_discount,op.order_date,op.province_id,op.city_id,op.operator_id
		INTO v_order_user_type,v_proxy_id,v_enterprise_id,v_discount_price,v_price,v_order_status,v_back_channel_id,v_channel_id
		,v_channel_product_id,v_back_channel_product_id,v_top_discount,v_back_top_discount,v_order_date,v_province_id,v_city_id,v_operator_id
		FROM t_flow_order_pre op
		WHERE op.order_id = p_order_id AND order_status = p_order_status FOR UPDATE;	
		
		#判断p_channel_order_code是否为null
		IF p_channel_order_code IS NULL THEN
			SET v_channel_order_code = '';
		ELSE
			SET v_channel_order_code = p_channel_order_code;
		END IF;
			
		#如果订单数据已处理
		IF v_proxy_id IS NOT NULL THEN
			
			IF v_order_user_type = 1 THEN #代理商
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.freeze_money = pa.freeze_money - v_discount_price
				WHERE pa.proxy_id = v_proxy_id;	
				SET v_proxy_enterprise_id = v_proxy_id;	
				#获取用户发送短信标识	
				SELECT message_status INTO v_user_message_flag
				FROM t_flow_proxy p
				WHERE p.proxy_id =v_proxy_id;						
			ELSEIF v_order_user_type = 2 THEN #企业
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.freeze_money = ea.freeze_money - v_discount_price
				WHERE ea.enterprise_id = v_enterprise_id;
				SET v_proxy_enterprise_id = v_enterprise_id;
				#获取用户发送短信标识	
				SELECT message_status INTO v_user_message_flag
				FROM t_flow_enterprise e
				WHERE e.enterprise_id =v_enterprise_id;			
			END IF;	
			
			#获取产品size
			SELECT size INTO v_size FROM t_flow_channel_product WHERE product_id = v_channel_product_id;
			
			SET v_is_discount = f_get_is_discount(v_proxy_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,IFNULL(v_size,0));		
			
			#如果状态是0，1  （0：等待提交，1：提交成功 ）
			IF v_order_status IN (0,1) THEN	#主通道提交成功
				#修改订单回调内容
				UPDATE t_flow_order_callback oc
				SET oc.content = p_content,oc.order_status=2
				,final_channel_id =v_channel_id
				WHERE oc.order_id = p_order_id;
				#通道帐户金额，多个通道对应一个账户 by cxw 201607606
				#备通道金额处理
				UPDATE LOW_PRIORITY t_flow_channel_account
				SET surplus_money = surplus_money + ROUND(v_price*v_back_top_discount,3)
				WHERE account_id = (SELECT account_id FROM t_flow_channel WHERE channel_id = v_back_channel_id);
				
				IF v_is_discount IN(1,3) THEN
					#备通道市金额处理
					UPDATE LOW_PRIORITY t_flow_channel_province cpv
					SET cpv.province_money = cpv.province_money + ROUND(v_price*v_back_top_discount,3)
					WHERE EXISTS(SELECT cp.* FROM t_flow_channel_product cp 
					WHERE cpv.channel_id = cp.channel_id 
					AND cpv.province_id = cp.province_id 
					AND cpv.city_id = cp.city_id
					AND cp.product_id = v_back_channel_product_id);
				ELSE
					#备通道省份金额处理
					UPDATE LOW_PRIORITY t_flow_channel_province cpv
					SET cpv.province_money = cpv.province_money + ROUND(v_price*v_back_top_discount,3)
					WHERE EXISTS(SELECT cp.* FROM t_flow_channel_product cp 
					WHERE cpv.channel_id = cp.channel_id 
					AND cpv.province_id = cp.province_id 
					AND cp.product_id = v_back_channel_product_id)
					AND cpv.city_id = 0;
				END IF;
				#订单状态修改
				UPDATE t_flow_order_pre op
				SET op.order_status = 2
				,op.is_using = 2
				,op.back_content = p_back_content
				,op.back_fail_desc = p_back_fail_desc
				,op.channel_order_code = v_channel_order_code
				,op.success_channel_product_id = op.channel_product_id
				WHERE op.order_id = p_order_id;	
				#主通首提交成功
				SET v_tran_type = 21;
				#获取通道发短信标识
				SELECT is_message INTO v_channel_message_flag
				FROM t_flow_channel
				WHERE channel_id = v_channel_id;
			ELSE  #备用通道提交成功
				#修改订单回调内容
				UPDATE t_flow_order_callback oc
				SET oc.content = p_content,oc.order_status=5
				,final_channel_id =v_back_channel_id
				WHERE oc.order_id = p_order_id;
				#订单状态修改
				UPDATE t_flow_order_pre op
				SET op.order_status = 5
				,op.is_using = 2
				,op.back_content = p_back_content
				,op.back_fail_desc = p_back_fail_desc
				,op.channel_order_code = v_channel_order_code
				,op.success_channel_product_id = op.back_channel_product_id
				WHERE op.order_id = p_order_id;	
				#备用通道提交成功
				SET v_tran_type = 22;
				#获取通道发短信标识
				SELECT is_message INTO v_channel_message_flag
				FROM t_flow_channel
				WHERE channel_id = v_back_channel_id;
			END IF;
			#订单数据转移
			INSERT INTO t_flow_order(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
			SELECT order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,NOW() complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id
			FROM t_flow_order_pre op 
			WHERE op.order_id = p_order_id;
			
			
			#插入用户日提交订单统计表（方便发送额度短信）
			SELECT COUNT(*) INTO v_num 
			FROM  t_flow_order_user 
			WHERE user_id = v_proxy_enterprise_id 
			AND user_type = v_order_user_type
			AND complete_time = DATE_FORMAT(NOW(),'%Y%m%d');
			IF v_num <= 0 THEN
				INSERT INTO t_flow_order_user(user_id,user_type,complete_time,order_num)
				VALUES(v_proxy_enterprise_id,v_order_user_type,DATE_FORMAT(NOW(),'%Y%m%d'),1)
				ON DUPLICATE KEY UPDATE  order_num = order_num;
			END IF;
			
			#成功订单短信记录（2016-11-01加）
			#用户设置可发，通道设置可发
			IF v_user_message_flag = 1 AND v_channel_message_flag = 1 THEN
				INSERT INTO t_flow_sms_pre(order_id,mobile,price,product_name,`status`,is_using,order_date
				,complete_time,user_type,proxy_id,enterprise_id,channel_id,back_channel_id,order_status
				,create_date)
				SELECT order_id,mobile,price,product_name,0,0,order_date
				,NOW(),user_type,proxy_id,enterprise_id,channel_id,back_channel_id,order_status	
				,NOW()
				FROM t_flow_order_pre op
				WHERE op.order_id = p_order_id;
			END IF;
			
			#获取监控随机数
			SET v_rand_id = ROUND(4*RAND()+1);
			
			#订单监控(成功下单)
			IF v_tran_type = 21 THEN
				CALL p_monitor_order_channel(v_channel_id,1,v_rand_id,v_order_date,v_discount_price);
			ELSEIF v_tran_type = 22 THEN
				CALL p_monitor_order_channel(v_back_channel_id,1,v_rand_id,v_order_date,v_discount_price);
			END IF;
			#监控订单（省份）
			CALL p_monitor_order_province(v_province_id,1,v_rand_id,v_order_date,v_discount_price);
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,v_tran_type tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
			FROM t_flow_order_pre
			WHERE order_id = p_order_id;			
			#预处理订单删除
			DELETE FROM t_flow_order_pre WHERE order_id = p_order_id;			
		END IF;
		
	IF t_error = 1 THEN 
		#回滚输出标志
		SET p_out_flag = 0;
		#订单回滚			
		ROLLBACK;  
		#记录订单日志
		SET v_end_time = CURRENT_TIMESTAMP(6);
		INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
		SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,20 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
		FROM t_flow_order_pre
		WHERE order_id = p_order_id;
	ELSE  	
		#成功输出标志
		IF v_proxy_id IS NOT NULL THEN
			SET p_out_flag = 1;#未找到订单
		ELSE
			SET p_out_flag = -1;
		END IF;
		#提交
		COMMIT;  
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_commit_order_cache
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_commit_order_cache`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_commit_order_cache`(IN p_order_id BIGINT
,OUT p_out_flag INT)
label_pro:BEGIN 
	/*
	author		cxw
	date		2016/06/30
	desc		处理成功缓存订单
			2016/09/09 用户产品折扣判断 by lk
			2016/09/26 去除上游通道余额不足判断 by lk
			2016/10/24 重新提交取最新折扣 by lk
	
	parameter:		
		p_out_flag = -2; #余额不足(游企业用户的余额不足)
		p_out_flag = -5; #上游通道账户余额不足
		p_out_flag = -6; #通道额度设置余额不足
		p_out_flag = -9; #市通道额度设置余额不足
		p_out_flag = -10; #上游通道维护中
		p_out_flag = 0 #存储运行失败，回滚
		p_out_flag = 1 #执行成功
	*/
	#订单ID
	DECLARE v_order_id BIGINT;
	#订单用户类型 1代理商 2企业
	DECLARE v_order_user_type INT;
	#用户的代理商ID
	DECLARE v_proxy_id BIGINT;
	#用户的企业ID
	DECLARE v_enterprise_id BIGINT;
	#订单折后金额
	DECLARE v_discount_price DECIMAL(11,3);
	#订单的标准价格
	DECLARE v_price DECIMAL(11,3);
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#主通道ID
	DECLARE v_channel_id BIGINT;
	#备通道ID
	DECLARE v_back_channel_id BIGINT;
	#主通道帐户ID
	DECLARE v_account_id BIGINT;
	#备通道帐户ID
	DECLARE v_back_account_id BIGINT;
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(11,3);
	#回调URL
	DECLARE v_url VARCHAR(200);
	#订单NUMID
	DECLARE v_orderno_id VARCHAR(50);
	#订单的状态
	DECLARE v_order_status INT;
	#用户ID
	DECLARE v_user_id BIGINT;
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT;	
	#产品省份iD
	DECLARE v_product_province_id INT;
	#缓存订单个数
	DECLARE v_count INT;
	#缓存额度
	DECLARE v_cache_credit DECIMAL(11,3) DEFAULT NULL;
	#主通道帐户余额
	#DECLARE v_surplus_money DECIMAL(11,3) DEFAULT NULL;
	#备通道帐户余额
	#DECLARE v_back_surplus_money DECIMAL(11,3) DEFAULT NULL;
	#主通道额度设置余额不足
	DECLARE v_province_money DECIMAL(11,3) DEFAULT NULL;
	#备通道额度设置余额不足
	DECLARE v_back_province_money DECIMAL(11,3) DEFAULT NULL;
	#市主通道额度设置余额不足
	DECLARE v_city_province_money DECIMAL(11,3) DEFAULT NULL;
	#市备通道额度设置余额不足
	DECLARE v_city_back_province_money DECIMAL(11,3) DEFAULT NULL;
	DECLARE v_order_count INT;
	DECLARE v_operator_id INT;
	DECLARE v_city_id INT;
	DECLARE v_channel_product_id BIGINT;
	DECLARE v_proxy_enterprise_id BIGINT;
	DECLARE v_is_discount INT;
	#DECLARE v_operator_id INT;
	DECLARE v_mobile VARCHAR(50) DEFAULT '';
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	#产品size
	DECLARE v_size INT;
	
	
	#主折口
	DECLARE v_discount_number DECIMAL(5,3);
	#顶级代理商ID
	DECLARE v_one_proxy_id BIGINT;
	#顶级代理商折口
	DECLARE v_one_proxy_discount DECIMAL(5,3);
	#主通道返利折扣
	DECLARE v_top_rebate_discount DECIMAL(5,3);
	#备通道返利折扣
	DECLARE v_back_top_rebate_discount DECIMAL(5,3);
	#取订单折后金额（存）
	DECLARE v_discount_price_temp DECIMAL(11,3);
	#返利金额
	DECLARE v_profit_case TEXT;
	
	DECLARE v_range INT DEFAULT 0; #0代表全国 1省内
	#主通道省ID
	DECLARE v_channel_province_id BIGINT;
	#备通道省ID
	DECLARE v_back_channel_province_id BIGINT;
	#主通道市ID
	DECLARE v_channel_city_id BIGINT;
	#备通道市ID
	DECLARE v_back_channel_city_id BIGINT;
	#省ID
	DECLARE v_province_id INT;
	#通道是否维护标志
	DECLARE v_channel_cache INT DEFAULT 0;
	
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	SELECT COUNT(*)
	INTO v_count
	FROM t_flow_order_cache op
	WHERE op.order_id = p_order_id;	
	
	
	#开始事务
	START TRANSACTION;
		IF v_count > 0 THEN
			#获取订单数据
			SELECT op.user_type order_user_type,op.proxy_id,op.enterprise_id,op.discount_price,op.price
			,op.top_discount,op.back_top_discount,op.channel_id,op.back_channel_id,op.url,cp.province_id,op.operator_id,op.mobile
			,op.city_id,op.channel_product_id,op.orderno_id,op.order_status
			,op.one_proxy_id,op.range,op.province_id
			INTO v_order_user_type,v_proxy_id,v_enterprise_id,v_discount_price_temp,v_price
			,v_top_discount,v_back_top_discount,v_channel_id,v_back_channel_id,v_url,v_product_province_id,v_operator_id,v_mobile
			,v_city_id,v_channel_product_id,v_orderno_id,v_order_status
			,v_one_proxy_id,v_range,v_province_id
			FROM t_flow_order_cache op,t_flow_channel_product cp
			WHERE op.channel_product_id = cp.product_id 
			AND op.order_id = p_order_id FOR UPDATE;
			
			#获取产品size
			SELECT size INTO v_size FROM t_flow_channel_product WHERE product_id = v_channel_product_id;
			
			
			#24小时内，单个联通号码只有2次提交的机会。（避免高失败率）
			IF v_operator_id = 2 THEN 
				SELECT COUNT(*)
				INTO v_order_count
				FROM t_flow_order_pre o
				WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
				AND o.mobile = v_mobile;
				
				SELECT v_order_count + COUNT(*)
				INTO v_order_count
				FROM t_flow_order o
				WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
				AND o.mobile = v_mobile;
				
				IF v_order_count >= 5 THEN
					SET p_out_flag = -8; 
					SET t_error = 1;
					ROLLBACK; 
					LEAVE label_pro; 
				END IF;
			END IF;
			
			#获取通道帐户ID
			#获取主通道省ID、备通道省ID、主通道市ID、备通道市ID
			SELECT account_id ,province_id,city_id
			INTO v_account_id,v_channel_province_id,v_channel_city_id
			FROM t_flow_channel 
			WHERE channel_id = v_channel_id;
			
			SELECT account_id ,province_id,city_id
			INTO v_back_account_id,v_back_channel_province_id,v_back_channel_city_id
			FROM t_flow_channel 
			WHERE channel_id = v_back_channel_id;
			#select v_account_id,v_back_account_id;
			
			
			#开始---------------
			#用户缓存或通道缓存，重新计算（2016-10-24）：
			#折后价（discount_price）、返利(profit_case)、主上游折扣(top_discount)、备上游折扣(back_top_discount)、
			#一级代理折扣(one_proxy_discount)、主通道返利折扣(top_rebate_discount)、备通道返利折扣(back_top_rebate_discount)
			IF v_order_user_type = 1 THEN #代理商
				SET v_proxy_enterprise_id = v_proxy_id;
			ELSE
				SET v_proxy_enterprise_id =  v_enterprise_id;
			END IF;
			#判断折扣类型
			SET v_is_discount = f_get_is_discount(v_proxy_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,IFNULL(v_size,0));	
			#获取当前折扣
			IF v_is_discount IN (1,2,3,4,5) THEN #市折扣 #省折扣 #市产品折扣 #省产品折扣 #全国产品折扣
				SET v_discount_number = `f_get_discount2`(v_proxy_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_is_discount,IFNULL(v_size,0));
			ELSE
				SET v_discount_number = `f_get_discount`(v_proxy_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id);
			END IF;
			#计算折口价
			SET v_discount_price = ROUND(v_price * v_discount_number,3);
			
			#获取主通道折口
			SET v_top_discount = f_get_channel_discount(v_channel_id,v_operator_id,v_province_id,v_city_id,v_proxy_enterprise_id,v_order_user_type,IFNULL(v_size,0));
			#获取备通道折口
			SET v_back_top_discount = f_get_channel_discount(v_back_channel_id,v_operator_id,v_province_id,v_city_id,v_proxy_enterprise_id,v_order_user_type,IFNULL(v_size,0));
			
			#获取一级代理商折扣
			IF v_is_discount IN(1,2,3,4,5) THEN
				SET v_one_proxy_discount = f_get_discount2(v_one_proxy_id,1,v_operator_id,v_province_id,v_city_id,v_is_discount,IFNULL(v_size,0));
			ELSE
				SET v_one_proxy_discount = f_get_discount(v_one_proxy_id,1,v_operator_id,v_province_id,v_city_id);
			END IF;
			
			#主备通道返利折扣
			SET v_top_rebate_discount = f_get_channel_rebate_discount(v_channel_id,v_operator_id,v_province_id,v_city_id);
			SET v_back_top_rebate_discount = f_get_channel_rebate_discount(v_back_channel_id,v_operator_id,v_province_id,v_city_id);
			
			#返利重新计算
			IF v_order_user_type = 1 THEN #代理商
				IF v_is_discount IN (3,4,5) THEN
					SET v_profit_case = f_get_profit_case_product(v_proxy_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_price,IFNULL(v_size,0),v_is_discount);
				ELSE
					SET v_profit_case = f_get_profit_case(v_proxy_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_price,v_is_discount);
				END IF;
			ELSEIF v_order_user_type = 2 THEN #企业
				IF v_is_discount IN (3,4,5) THEN
					SET v_profit_case = f_get_profit_case_product(v_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_price,IFNULL(v_size,0),v_is_discount);
				ELSE
					SET v_profit_case = f_get_profit_case(v_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_price,v_is_discount);
				END IF;
			END IF;
		
			IF v_profit_case IS NULL THEN
				SET v_profit_case = '[]';
			END IF;
			#结束---------------
		
			
			
			
			#更新帐户余额相关金额
			IF v_order_user_type = 1 THEN #代理商	
				#获取代理商用户ID
				SELECT user_id
				INTO v_user_id 	
				FROM t_flow_sys_user 
				WHERE proxy_id = v_proxy_id 
				AND is_manager = 1 LIMIT 1;
				#获得上级代理商ID
				SELECT p.top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_proxy p
				WHERE p.proxy_id = v_proxy_id;
				#获取代理商帐户余额
				SELECT pa.account_balance,pa.`cache_credit` 
				INTO v_account_balance,v_cache_credit
				FROM t_flow_proxy_account pa
				WHERE pa.proxy_id = v_proxy_id FOR UPDATE;
				#更新代理商金额
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.freeze_money = pa.freeze_money + v_discount_price
				,pa.account_balance = pa.account_balance - v_discount_price
				,pa.cache_credit = pa.cache_credit + v_discount_price_temp
				WHERE pa.proxy_id = v_proxy_id;	
			ELSEIF v_order_user_type = 2 THEN #企业
				#获取企业用户ID
				SELECT user_id 
				INTO v_user_id 
				FROM t_flow_sys_user 
				WHERE enterprise_id = v_enterprise_id
				AND is_manager = 1 LIMIT 1;
				#获得上级代理商ID
				SELECT p.top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_enterprise p
				WHERE p.enterprise_id = v_enterprise_id;
				#获取企业余额	
				SELECT ea.account_balance,ea.`cache_credit` 
				INTO v_account_balance,v_cache_credit
				FROM t_flow_enterprise_account ea
				WHERE ea.enterprise_id = v_enterprise_id FOR UPDATE;
				#更新企业余额
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.freeze_money = ea.freeze_money + v_discount_price
				,ea.account_balance = ea.account_balance - v_discount_price
				,ea.cache_credit = ea.cache_credit + v_discount_price_temp
				WHERE ea.enterprise_id = v_enterprise_id;
			END IF;		
			
			#主通道金额(通道额度设置余额不足)		
			SET v_province_money = f_get_channel_province_money(v_channel_id,v_channel_province_id,v_channel_city_id,v_range);
			#备通道金额(通道额度设置余额不足)
			SET v_back_province_money = f_get_channel_province_money(v_back_channel_id,v_back_channel_province_id,v_back_channel_city_id,v_range);				
			
			IF v_channel_id <> v_back_channel_id THEN
				IF v_account_id = v_back_account_id THEN						
					IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)*2) 
						OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,3)*2) THEN
						SET p_out_flag = -6;
						SET t_error = 1;
						ROLLBACK; 
						LEAVE label_pro;  
					END IF;
				ELSE
					
					IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)) 
						OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,3)) THEN
						SET p_out_flag = -6; 
						SET t_error = 1;
						ROLLBACK; 
						LEAVE label_pro; 
					END IF;
				END IF;
			ELSE				
				IF v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)*2  THEN
					SET p_out_flag = -6;
					SET t_error = 1;
					ROLLBACK; 
					LEAVE label_pro; 
				END IF;
			END IF;	
				
			#余额不足
			IF v_account_balance < v_discount_price THEN
				SET p_out_flag = -2; 
				SET t_error = 1;
				ROLLBACK; 
				LEAVE label_pro; 
			END IF;	
					
			#通道帐户金额，多个通道对应一个账户 by cxw 201607606
			#扣主通道金额
			UPDATE t_flow_channel_account
			SET surplus_money = surplus_money - ROUND(v_price*v_top_discount,3)
			WHERE account_id = v_account_id;
			#扣备通道金额
			UPDATE t_flow_channel_account
			SET surplus_money = surplus_money - ROUND(v_price*v_back_top_discount,3)
			WHERE account_id = v_back_account_id;
		
			
			#主通道额度扣除 
			IF v_channel_city_id > 0 THEN
				UPDATE LOW_PRIORITY t_flow_channel_province
				SET province_money = province_money - ROUND(v_price*v_top_discount,3)
				WHERE (city_id = v_city_id AND province_id = CASE v_range WHEN 0 THEN 1 WHEN 1 THEN v_province_id WHEN 2 THEN 0 END) 
				AND channel_id = v_channel_id;
			ELSE
				#扣省份主通道金额
				UPDATE LOW_PRIORITY t_flow_channel_province
				SET province_money = province_money - ROUND(v_price*v_top_discount,3)
				WHERE province_id = v_product_province_id AND channel_id = v_channel_id;
			END IF;
			
			#备通道额度扣除
			IF v_back_channel_city_id > 0 THEN
				#扣市备用通道金额
				UPDATE LOW_PRIORITY t_flow_channel_province
				SET province_money = province_money - ROUND(v_price*v_back_top_discount,3)
				WHERE (city_id = v_city_id AND province_id = CASE v_range WHEN 0 THEN 1 WHEN 1 THEN v_province_id WHEN 2 THEN 0 END) 
				AND channel_id = v_back_channel_id;
			ELSE
				#扣省份备用通道金额
				UPDATE LOW_PRIORITY t_flow_channel_province
				SET province_money = province_money - ROUND(v_price*v_back_top_discount,3)
				WHERE province_id = v_product_province_id AND channel_id = v_back_channel_id;
			END IF;	
		
			#缓存订单数据转移			
			INSERT INTO t_flow_order_pre(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
			SELECT order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,v_discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,NOW() complete_time,v_profit_case,is_profit,is_using,v_top_discount,v_one_proxy_discount,profit,one_proxy_id,orderno_id
			,v_back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,v_top_rebate_discount,v_back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id
			FROM t_flow_order_cache op 
			WHERE op.order_id = p_order_id;	
			
			#获得订单ID 
			#SET v_order_id = @@IDENTITY;
			SET v_order_id = p_order_id;
			#订单回调
			IF v_url IS NOT NULL AND LENGTH(v_url) > 0 THEN
				INSERT INTO t_flow_order_callback(order_id,url,content,rece_content,times,`status`,end_date,channel_id,back_channel_id,orderno_id,order_status)
				VALUES(v_order_id,v_url,NULL,NULL,0,0,NULL,v_channel_id,v_back_channel_id,v_orderno_id,v_order_status);
			END IF;
			#记录流水		
			INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
			,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
			,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
			VALUES(v_discount_price,v_account_balance,v_account_balance-v_discount_price
			,1,2,NOW(6),'购买流量',v_user_id,NOW(6),v_order_user_type,IFNULL(v_proxy_id,0),IFNULL(v_enterprise_id,0)
			,1,v_top_proxy_id,0,v_order_id,NULL);	
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,51 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
			FROM t_flow_order_pre
			WHERE order_id = v_order_id;			
			#预处理订单删除
			DELETE FROM t_flow_order_cache WHERE order_id = p_order_id;
			
		END IF;	
		
	IF t_error = 1 THEN 
		#回滚输出标志
		SET p_out_flag = 0;
		#select 0;
		#订单回滚			
		ROLLBACK;  
		#记录订单日志
		SET v_end_time = CURRENT_TIMESTAMP(6);
		INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
		SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,50 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
		FROM t_flow_order_pre
		WHERE order_id = p_order_id;
	ELSE  	
		#成功输出标志
		SET p_out_flag = 1;
		#SELECT 1;
		#提交
		COMMIT;  
	END IF;	
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_commit_order_channel_cache
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_commit_order_channel_cache`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_commit_order_channel_cache`(IN p_order_id BIGINT
,OUT p_out_flag INT)
label_pro:BEGIN 
	/*
	author		lk
	date		2016/10/24
	desc		处理成功通道缓存订单
	
	parameter:		
		p_out_flag = -2; #余额不足(游企业用户的余额不足)
		p_out_flag = -5; #上游通道账户余额不足
		p_out_flag = -6; #通道额度设置余额不足
		p_out_flag = -9; #市通道额度设置余额不足
		p_out_flag = 0 #存储运行失败，回滚
		p_out_flag = 1 #执行成功
	*/
	#订单ID
	DECLARE v_order_id BIGINT;
	#订单用户类型 1代理商 2企业
	DECLARE v_order_user_type INT;
	#用户的代理商ID
	DECLARE v_proxy_id BIGINT;
	#用户的企业ID
	DECLARE v_enterprise_id BIGINT;
	#订单折后金额
	DECLARE v_discount_price DECIMAL(11,3);
	#订单的标准价格
	DECLARE v_price DECIMAL(11,3);
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#主通道ID
	DECLARE v_channel_id BIGINT;
	#备通道ID
	DECLARE v_back_channel_id BIGINT;
	#主通道帐户ID
	DECLARE v_account_id BIGINT;
	#备通道帐户ID
	DECLARE v_back_account_id BIGINT;
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(11,3);
	#回调URL
	DECLARE v_url VARCHAR(200);
	#订单NUMID
	DECLARE v_orderno_id VARCHAR(50);
	#订单的状态
	DECLARE v_order_status INT;
	#用户ID
	DECLARE v_user_id BIGINT;
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT;	
	#产品省份iD
	DECLARE v_product_province_id INT;
	#缓存订单个数
	DECLARE v_count INT;
	#缓存额度
	DECLARE v_cache_credit DECIMAL(11,3) DEFAULT NULL;
	#主通道帐户余额
	#DECLARE v_surplus_money DECIMAL(11,3) DEFAULT NULL;
	#备通道帐户余额
	#DECLARE v_back_surplus_money DECIMAL(11,3) DEFAULT NULL;
	#主通道额度设置余额不足
	DECLARE v_province_money DECIMAL(11,3) DEFAULT NULL;
	#备通道额度设置余额不足
	DECLARE v_back_province_money DECIMAL(11,3) DEFAULT NULL;
	#市主通道额度设置余额不足
	DECLARE v_city_province_money DECIMAL(11,3) DEFAULT NULL;
	#市备通道额度设置余额不足
	DECLARE v_city_back_province_money DECIMAL(11,3) DEFAULT NULL;
	DECLARE v_order_count INT;
	DECLARE v_operator_id INT;
	DECLARE v_city_id INT;
	DECLARE v_channel_product_id BIGINT;
	DECLARE v_proxy_enterprise_id BIGINT;
	DECLARE v_is_discount INT;
	#DECLARE v_operator_id INT;
	DECLARE v_mobile VARCHAR(50) DEFAULT '';
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	#产品size
	DECLARE v_size INT;
	
	#主折口
	DECLARE v_discount_number DECIMAL(5,3);
	#顶级代理商ID
	DECLARE v_one_proxy_id BIGINT;
	#顶级代理商折口
	DECLARE v_one_proxy_discount DECIMAL(5,3);
	#主通道返利折扣
	DECLARE v_top_rebate_discount DECIMAL(5,3);
	#备通道返利折扣
	DECLARE v_back_top_rebate_discount DECIMAL(5,3);
	#取订单折后金额（存）
	DECLARE v_discount_price_temp DECIMAL(11,3);
	#返利金额
	DECLARE v_profit_case TEXT;
	
	DECLARE v_range INT DEFAULT 0; #0代表全国 1省内
	#主通道省ID
	DECLARE v_channel_province_id BIGINT;
	#备通道省ID
	DECLARE v_back_channel_province_id BIGINT;
	#主通道市ID
	DECLARE v_channel_city_id BIGINT;
	#备通道市ID
	DECLARE v_back_channel_city_id BIGINT;
	#省ID
	DECLARE v_province_id INT;
	#通道是否维护标志
	DECLARE v_channel_flag INT DEFAULT 1;
	DECLARE v_back_channel_flag INT DEFAULT 1;
	
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	SELECT COUNT(*)
	INTO v_count
	FROM t_flow_order_channel_cache op
	WHERE op.order_id = p_order_id;	
	
	
	#开始事务
	START TRANSACTION;
		IF v_count > 0 THEN
			#获取订单数据
			SELECT op.user_type order_user_type,op.proxy_id,op.enterprise_id,op.discount_price,op.price
			,op.top_discount,op.back_top_discount,op.channel_id,op.back_channel_id,op.url,cp.province_id,op.operator_id,op.mobile
			,op.city_id,op.channel_product_id,op.orderno_id,op.order_status
			,op.one_proxy_id,op.range,op.province_id
			INTO v_order_user_type,v_proxy_id,v_enterprise_id,v_discount_price_temp,v_price
			,v_top_discount,v_back_top_discount,v_channel_id,v_back_channel_id,v_url,v_product_province_id,v_operator_id,v_mobile
			,v_city_id,v_channel_product_id,v_orderno_id,v_order_status
			,v_one_proxy_id,v_range,v_province_id
			FROM t_flow_order_channel_cache op,t_flow_channel_product cp
			WHERE op.channel_product_id = cp.product_id 
			AND op.order_id = p_order_id FOR UPDATE;
			
			#获取产品size
			SELECT size INTO v_size FROM t_flow_channel_product WHERE product_id = v_channel_product_id;
			
			
			#24小时内，单个联通号码只有2次提交的机会。（避免高失败率）
			IF v_operator_id = 2 THEN 
				SELECT COUNT(*)
				INTO v_order_count
				FROM t_flow_order_pre o
				WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
				AND o.mobile = v_mobile;
				
				SELECT v_order_count + COUNT(*)
				INTO v_order_count
				FROM t_flow_order o
				WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
				AND o.mobile = v_mobile;
				
				IF v_order_count >= 5 THEN
					SET p_out_flag = -8; 
					SET t_error = 1;
					ROLLBACK; 
					LEAVE label_pro; 
				END IF;
			END IF;
			
			#获取通道帐户ID
			#获取主通道省ID、备通道省ID、主通道市ID、备通道市ID
			SELECT account_id ,province_id,city_id,is_cache
			INTO v_account_id,v_channel_province_id,v_channel_city_id,v_channel_flag
			FROM t_flow_channel 
			WHERE channel_id = v_channel_id;
			
			SELECT account_id ,province_id,city_id,is_cache
			INTO v_back_account_id,v_back_channel_province_id,v_back_channel_city_id,v_back_channel_flag
			FROM t_flow_channel 
			WHERE channel_id = v_back_channel_id;
			#select v_account_id,v_back_account_id;
			
			#判断通道是否继续在维护，如果维护，提示维护中
			IF v_channel_flag = 0 OR v_back_channel_flag = 0  THEN
				SET p_out_flag = -10;
				SET t_error = 1;
				ROLLBACK; 
				LEAVE label_pro;  
			END IF;
			
			#开始---------------
			#用户缓存或通道缓存，重新计算（2016-10-24）：
			#折后价（discount_price）、返利(profit_case)、主上游折扣(top_discount)、备上游折扣(back_top_discount)、
			#一级代理折扣(one_proxy_discount)、主通道返利折扣(top_rebate_discount)、备通道返利折扣(back_top_rebate_discount)
			IF v_order_user_type = 1 THEN #代理商
				SET v_proxy_enterprise_id = v_proxy_id;
			ELSE
				SET v_proxy_enterprise_id =  v_enterprise_id;
			END IF;
			#判断折扣类型
			SET v_is_discount = f_get_is_discount(v_proxy_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,IFNULL(v_size,0));	
			#获取当前折扣
			IF v_is_discount IN (1,2,3,4,5) THEN #市折扣 #省折扣 #市产品折扣 #省产品折扣 #全国产品折扣
				SET v_discount_number = `f_get_discount2`(v_proxy_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_is_discount,IFNULL(v_size,0));
			ELSE
				SET v_discount_number = `f_get_discount`(v_proxy_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id);
			END IF;
			#计算折口价
			SET v_discount_price = ROUND(v_price * v_discount_number,3);
			
			#获取主通道折口
			SET v_top_discount = f_get_channel_discount(v_channel_id,v_operator_id,v_province_id,v_city_id,v_proxy_enterprise_id,v_order_user_type,IFNULL(v_size,0));
			#获取备通道折口
			SET v_back_top_discount = f_get_channel_discount(v_back_channel_id,v_operator_id,v_province_id,v_city_id,v_proxy_enterprise_id,v_order_user_type,IFNULL(v_size,0));
			
			#获取一级代理商折扣
			IF v_is_discount IN(1,2,3,4,5) THEN
				SET v_one_proxy_discount = f_get_discount2(v_one_proxy_id,1,v_operator_id,v_province_id,v_city_id,v_is_discount,IFNULL(v_size,0));
			ELSE
				SET v_one_proxy_discount = f_get_discount(v_one_proxy_id,1,v_operator_id,v_province_id,v_city_id);
			END IF;
			
			#主备通道返利折扣
			SET v_top_rebate_discount = f_get_channel_rebate_discount(v_channel_id,v_operator_id,v_province_id,v_city_id);
			SET v_back_top_rebate_discount = f_get_channel_rebate_discount(v_back_channel_id,v_operator_id,v_province_id,v_city_id);
			
			#返利重新计算
			IF v_order_user_type = 1 THEN #代理商
				IF v_is_discount IN (3,4,5) THEN
					SET v_profit_case = f_get_profit_case_product(v_proxy_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_price,IFNULL(v_size,0),v_is_discount);
				ELSE
					SET v_profit_case = f_get_profit_case(v_proxy_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_price,v_is_discount);
				END IF;
			ELSEIF v_order_user_type = 2 THEN #企业
				IF v_is_discount IN (3,4,5) THEN
					SET v_profit_case = f_get_profit_case_product(v_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_price,IFNULL(v_size,0),v_is_discount);
				ELSE
					SET v_profit_case = f_get_profit_case(v_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,v_price,v_is_discount);
				END IF;
			END IF;
		
			IF v_profit_case IS NULL THEN
				SET v_profit_case = '[]';
			END IF;
			
			#结束---------------
				
				
			#更新帐户余额相关金额
			IF v_order_user_type = 1 THEN #代理商	
				#获取代理商用户ID
				SELECT user_id
				INTO v_user_id 	
				FROM t_flow_sys_user 
				WHERE proxy_id = v_proxy_id 
				AND is_manager = 1 LIMIT 1;
				#获得上级代理商ID
				SELECT p.top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_proxy p
				WHERE p.proxy_id = v_proxy_id;
				#获取代理商帐户余额
				SELECT pa.account_balance
				INTO v_account_balance
				FROM t_flow_proxy_account pa
				WHERE pa.proxy_id = v_proxy_id FOR UPDATE;
				#更新代理商金额
				
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.freeze_money = pa.freeze_money + v_discount_price
				,pa.account_balance = pa.account_balance - v_discount_price
				,pa.channel_cache_credit = pa.channel_cache_credit + v_discount_price_temp
				WHERE pa.proxy_id = v_proxy_id;	
			ELSEIF v_order_user_type = 2 THEN #企业
				#获取企业用户ID
				SELECT user_id 
				INTO v_user_id 
				FROM t_flow_sys_user 
				WHERE enterprise_id = v_enterprise_id
				AND is_manager = 1 LIMIT 1;
				#获得上级代理商ID
				SELECT p.top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_enterprise p
				WHERE p.enterprise_id = v_enterprise_id;
				#获取企业余额	
				SELECT ea.account_balance
				INTO v_account_balance
				FROM t_flow_enterprise_account ea
				WHERE ea.enterprise_id = v_enterprise_id FOR UPDATE;
				#更新企业余额
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.freeze_money = ea.freeze_money + v_discount_price
				,ea.account_balance = ea.account_balance - v_discount_price
				,ea.channel_cache_credit = ea.channel_cache_credit + v_discount_price_temp
				WHERE ea.enterprise_id = v_enterprise_id;
				
				
			END IF;		
			
			#主通道金额(通道额度设置余额不足)		
			SET v_province_money = f_get_channel_province_money(v_channel_id,v_channel_province_id,v_channel_city_id,v_range);
			#备通道金额(通道额度设置余额不足)
			SET v_back_province_money = f_get_channel_province_money(v_back_channel_id,v_back_channel_province_id,v_back_channel_city_id,v_range);			
			IF v_channel_id <> v_back_channel_id THEN
				IF v_account_id = v_back_account_id THEN						
					IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)*2) 
						OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,3)*2) THEN
						SET p_out_flag = -6;
						SET t_error = 1;
						ROLLBACK; 
						LEAVE label_pro;  
					END IF;
				ELSE
					
					IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)) 
						OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,3)) THEN
						SET p_out_flag = -6; 
						SET t_error = 1;
						ROLLBACK; 
						LEAVE label_pro; 
					END IF;
				END IF;
			ELSE				
				IF v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)*2  THEN
					SET p_out_flag = -6;
					SET t_error = 1;
					ROLLBACK; 
					LEAVE label_pro; 
				END IF;
			END IF;	
				
			#余额不足
			IF v_account_balance < v_discount_price THEN
				SET p_out_flag = -2; 
				SET t_error = 1;
				ROLLBACK; 
				LEAVE label_pro; 
			END IF;	
					
			#通道帐户金额，多个通道对应一个账户 by cxw 201607606
			#扣主通道金额
			UPDATE t_flow_channel_account
			SET surplus_money = surplus_money - ROUND(v_price*v_top_discount,3)
			WHERE account_id = v_account_id;
			#扣备通道金额
			UPDATE t_flow_channel_account
			SET surplus_money = surplus_money - ROUND(v_price*v_back_top_discount,3)
			WHERE account_id = v_back_account_id;
		
			#主通道额度扣除 
			IF v_channel_city_id > 0 THEN
				UPDATE LOW_PRIORITY t_flow_channel_province
				SET province_money = province_money - ROUND(v_price*v_top_discount,3)
				WHERE (city_id = v_city_id AND province_id = CASE v_range WHEN 0 THEN 1 WHEN 1 THEN v_province_id WHEN 2 THEN 0 END) 
				AND channel_id = v_channel_id;
			ELSE
				#扣省份主通道金额
				UPDATE LOW_PRIORITY t_flow_channel_province
				SET province_money = province_money - ROUND(v_price*v_top_discount,3)
				WHERE province_id = v_product_province_id AND channel_id = v_channel_id;
			END IF;
			#备通道额度扣除
			IF v_back_channel_city_id > 0 THEN
				#扣市备用通道金额
				UPDATE LOW_PRIORITY t_flow_channel_province
				SET province_money = province_money - ROUND(v_price*v_back_top_discount,3)
				WHERE (city_id = v_city_id AND province_id = CASE v_range WHEN 0 THEN 1 WHEN 1 THEN v_province_id WHEN 2 THEN 0 END) 
				AND channel_id = v_back_channel_id;
			ELSE
				#扣省份备用通道金额
				UPDATE LOW_PRIORITY t_flow_channel_province
				SET province_money = province_money - ROUND(v_price*v_back_top_discount,3)
				WHERE province_id = v_product_province_id AND channel_id = v_back_channel_id;
			END IF;	
		
			#缓存订单数据转移			
			INSERT INTO t_flow_order_pre(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
			SELECT order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,v_discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,NOW() complete_time,v_profit_case,is_profit,is_using,v_top_discount,v_one_proxy_discount,profit,one_proxy_id,orderno_id
			,v_back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,v_top_rebate_discount,v_back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id
			FROM t_flow_order_channel_cache op 
			WHERE op.order_id = p_order_id;	
				
			
			#获得订单ID 
			#SET v_order_id = @@IDENTITY;
			SET v_order_id = p_order_id;
			SELECT v_order_id;
			#订单回调
			IF v_url IS NOT NULL AND LENGTH(v_url) > 0 THEN
				INSERT INTO t_flow_order_callback(order_id,url,content,rece_content,times,`status`,end_date,channel_id,back_channel_id,orderno_id,order_status)
				VALUES(v_order_id,v_url,NULL,NULL,0,0,NULL,v_channel_id,v_back_channel_id,v_orderno_id,v_order_status);
			END IF;
			#记录流水		
			INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
			,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
			,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
			VALUES(v_discount_price,v_account_balance,v_account_balance-v_discount_price
			,1,2,NOW(6),'购买流量',v_user_id,NOW(6),v_order_user_type,IFNULL(v_proxy_id,0),IFNULL(v_enterprise_id,0)
			,1,v_top_proxy_id,0,v_order_id,NULL);	
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,91 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
			FROM t_flow_order_pre
			WHERE order_id = v_order_id;			
			#预处理订单删除
			DELETE FROM t_flow_order_channel_cache WHERE order_id = p_order_id;
			
		END IF;	
		
	IF t_error = 1 THEN 
		#回滚输出标志
		SET p_out_flag = 0;
		#select 0;
		#订单回滚			
		ROLLBACK;  
		#记录订单日志
		SET v_end_time = CURRENT_TIMESTAMP(6);
		INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
		SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,90 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
		FROM t_flow_order_pre
		WHERE order_id = p_order_id;
	ELSE  	
		#成功输出标志
		SET p_out_flag = 1;
		#SELECT 1;
		#提交
		COMMIT;  
	END IF;	
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_commit_sms
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_commit_sms`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_commit_sms`( 
IN p_order_id BIGINT
,IN p_callback_flag INT #0发送成功 1发送失败
,IN p_content VARCHAR(500)#发送内容
,OUT p_out_flag INT)
BEGIN 
	/*
	author		lk
	date		2016/11/01
	desc		处理已发送短信记录
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#事务处理标志符
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	#开始事务
	START TRANSACTION;
		#新增短信记录
		INSERT INTO t_flow_sms(order_id,mobile,price,product_name,`status`,is_using,order_date
		,complete_time,user_type,proxy_id,enterprise_id,channel_id,back_channel_id,order_status
		,create_date,modify_date,content)
		SELECT order_id,mobile,price,product_name,p_callback_flag+2,is_using,order_date
		,complete_time,user_type,proxy_id,enterprise_id,channel_id,back_channel_id,order_status
		,create_date,NOW(),p_content
		FROM t_flow_sms_pre op
		WHERE op.order_id = p_order_id;
		#删除预处理表信息
		DELETE FROM t_flow_sms_pre WHERE order_id = p_order_id;			
	IF t_error = 1 THEN 
		SET p_out_flag = 0;
		ROLLBACK;  
	ELSE  
		SET p_out_flag = 1;
		COMMIT;  
	END IF;		
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_create_order
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_create_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_create_order`(IN p_size INT
,IN p_operator_id INT
,IN p_province_id INT 
,IN p_city_id INT
,IN p_user_type INT
,IN p_proxy_id BIGINT
,IN p_enterprise_id BIGINT
,IN p_range INT
,IN p_profit_case VARCHAR(4000)
,IN p_order_code VARCHAR(50)
,IN p_mobile VARCHAR(11)
,IN p_order_effect_date DATETIME
,IN p_source_type INT
,IN p_orderno_id VARCHAR(50)
,IN p_url VARCHAR(100)
,IN p_back_fail_desc VARCHAR(255)
,OUT p_out_flag INT)
label_pro:BEGIN 
  /*
  author    cxw
  date    2015/05/16
  desc    处理成功订单
      2016/06/22 增加主备通道返利折扣 by cxw
      2016/06/27 增加订单缓存需求，当帐户余额不足将订单缓存 by cxw
      2016/07/25 增加控制24小时内，单个联通号码只有2次提交的机会。（避免高失败率） by cxw1
      2016/09/09 用户产品折扣判断 by lk
  parameter:    
    p_out_flag = -1; #无法找到相应的产品
    p_out_flag = -2; #余额不足(游企业用户的余额不足)
    p_out_flag = -3; #号码在黑名单
    p_out_flag = -4; #下游订单号重复判断
    p_out_flag = -8; #24小时内，单个联通号码只有5次提交的机会。（避免高失败率）
    p_out_flag = 0 #存储运行失败，回滚
    p_out_flag = 1 #执行成功
    
  */
  #事务处理标志
  DECLARE t_error INTEGER DEFAULT 0; 
  #订单产品个数
  DECLARE v_order_product_count INT DEFAULT 0;
  #订单产品序号
  DECLARE v_sno INT;
  #主通道产品iD
  DECLARE v_product_id BIGINT;
  #备通道产品iD
  DECLARE v_back_product_id BIGINT;
  #主通道产品名
  DECLARE v_product_name VARCHAR(5);
  #备通道产品名称
  DECLARE v_back_product_name VARCHAR(5);
  #主通道ID
  DECLARE v_channel_id BIGINT;
  #备通道ID
  DECLARE v_back_channel_id BIGINT;
  #运营商ID
  DECLARE v_operator_id INT;
  #单价
  DECLARE v_price DECIMAL(11,3);
  #折后价
  DECLARE v_discount DECIMAL(11,3); 
  #返点数
  DECLARE v_number VARCHAR(255);
  #产品尺寸大小
  DECLARE v_size INT;
  #主通道名
  DECLARE v_channel_name VARCHAR(30);
  #备通道名
  DECLARE v_back_channel_name VARCHAR(30);
  #固定省份集合
  DECLARE v_province_id_set VARCHAR(300) DEFAULT '9,7,15,16,3,2,8,14,21,24,11,4,18,5';
  #主折口
  DECLARE v_discount_number DECIMAL(5,3);
  #备通道折口
  DECLARE v_back_discount_number DECIMAL(5,3);
  #通道产品省份ID
  DECLARE v_province_id INT;
  #通道产品市ID
  DECLARE v_city_id INT;
  #折后价
  DECLARE v_discount_price DECIMAL(11,3);
  #用户帐户余额
  DECLARE v_account_balance DECIMAL(11,3);
  #上游折口
  DECLARE v_top_discount DECIMAL(5,3);
  #备用通道折口
  DECLARE v_back_top_discount DECIMAL(5,3);
  #顶级代理商ID
  DECLARE v_one_proxy_id BIGINT;
  #顶级代理商折口
  DECLARE v_one_proxy_discount DECIMAL(5,3);
  #订单ID
  DECLARE v_order_id BIGINT;
  #用户ID
  DECLARE v_user_id BIGINT;
  #上级代理商ID
  DECLARE v_top_proxy_id BIGINT;  
  #判断数
  DECLARE v_exist_count INT DEFAULT 0;
  #返利金额
  DECLARE v_profit_case TEXT;
  #代理商名称
  DECLARE v_proxy_name VARCHAR(50);
  #企业名称
  DECLARE v_enterprise_name VARCHAR(100);
  #一级代理商名称
  DECLARE v_one_proxy_name VARCHAR(50);
  #主通道省ID
  DECLARE v_channel_province_id BIGINT;
  #备通道省ID
  DECLARE v_back_channel_province_id BIGINT;
  #主通道市ID
  DECLARE v_channel_city_id BIGINT;
  #备通道市ID
  DECLARE v_back_channel_city_id BIGINT;
  #主通道返利折扣
  DECLARE v_top_rebate_discount DECIMAL(5,3);
  #备通道返利折扣
  DECLARE v_back_top_rebate_discount DECIMAL(5,3);


  #用户对象ID
  DECLARE v_object_id BIGINT;
  #是否有省折扣
  DECLARE v_is_discount INT DEFAULT 0;
  #订单数量
  DECLARE v_order_count INT DEFAULT 0;
  #主通道代码
  DECLARE v_channel_code VARCHAR(100);
  #备用通道代码
  DECLARE v_back_channel_code VARCHAR(100);
  #手机号省份名
  DECLARE v_province_name VARCHAR(100);
  #手机号市名
  DECLARE v_city_name VARCHAR(100);
  #开始时间
  DECLARE v_start_time TIMESTAMP(6);
  #结束时间
  DECLARE v_end_time TIMESTAMP(6);

  #申明事务处理错误标志
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
  
  SET v_start_time = CURRENT_TIMESTAMP(6);
  
  #创建主通道订单产品临时表
  DROP TEMPORARY TABLE IF EXISTS temp_order_product;  
  CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_product    
  (sno INT PRIMARY KEY AUTO_INCREMENT
  ,product_id BIGINT
  ,product_name VARCHAR(5)
  ,channel_id BIGINT
  ,operator_id INT
  ,province_id INT #通道产品省份ID
  ,city_id INT #通道产品市ID
  ,price DECIMAL(10,2)
  ,discount DECIMAL(10,2)
  ,number VARCHAR(255)
  ,size INT
  ,channel_name VARCHAR(30)
  ,channel_province_id BIGINT #通道省份ID
  ,channel_code VARCHAR(50)
  ,channel_city_id BIGINT); #通道市ID


  #判断号码是否黑名单
  SELECT COUNT(*) 
  INTO v_exist_count
  FROM t_flow_mobile_blacklist mb 
  WHERE mb.mobile = p_mobile;
  
  IF v_exist_count > 0 THEN
    SET p_out_flag = -3; #号码在黑名单
    SET t_error = 1;
    ROLLBACK; 
    LEAVE label_pro; 
  END IF;
  
  #24小时内，单个联通号码只有2次提交的机会。（避免高失败率）
  IF p_operator_id = 2 THEN 
    SELECT COUNT(*)
    INTO v_order_count
    FROM t_flow_order_pre o
    WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
    AND o.mobile = p_mobile;
    
    SELECT v_order_count + COUNT(*)
    INTO v_order_count
    FROM t_flow_order o
    WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
    AND o.mobile = p_mobile;
    
    IF v_order_count >= 5 THEN
      SET p_out_flag = -8; 
      SET t_error = 1;
      ROLLBACK; 
      LEAVE label_pro; 
    END IF;
  END IF;
  

  #获取用户代理商ID或者企业ID
  IF p_user_type = 1 THEN #代理商
    SET v_object_id = p_proxy_id; 
  ELSEIF p_user_type = 2 THEN #企业     
    SET v_object_id = p_enterprise_id;
  END IF;

  #下游订单号重复判断
  SET v_exist_count = 0;
  
  #判断该订单号再预处理表中出现次数
  IF p_orderno_id IS NOT NULL AND LENGTH(p_orderno_id) > 0 THEN

    IF p_user_type = 1 THEN

      SELECT COUNT(*)
      INTO v_exist_count
      FROM t_flow_order_pre op 
      WHERE op.`orderno_id` = p_orderno_id AND op.`proxy_id` = p_proxy_id;
      
      SELECT v_exist_count + COUNT(*)
      INTO v_exist_count
      FROM t_flow_order op 
      WHERE op.`orderno_id` = p_orderno_id AND op.`proxy_id` = p_proxy_id;
    
    ELSEIF p_user_type = 2 THEN
      SELECT COUNT(*)
      INTO v_exist_count
      FROM t_flow_order_pre op 
      WHERE op.`orderno_id` = p_orderno_id AND op.`enterprise_id` = p_enterprise_id;
      
      SELECT v_exist_count + COUNT(*)
      INTO v_exist_count
      FROM t_flow_order op 
      WHERE op.`orderno_id` = p_orderno_id AND op.`enterprise_id` = p_enterprise_id;
    END IF;

    IF v_exist_count > 0 THEN
      SET p_out_flag = -4; #下游订单号重复判断
      SET t_error = 1;
      ROLLBACK; 
      LEAVE label_pro; 
    END IF;
  END IF;
  
  #开始事务
  START TRANSACTION;
    select CONCAT(p_size,',',p_operator_id,',',p_province_id,',',p_city_id,',',p_user_type,',',p_proxy_id,',',p_enterprise_id,',',p_range,',',0,',',2);
    #获取订单产品
    CALL p_get_order_product(p_size,p_operator_id,p_province_id,p_city_id,p_user_type,p_proxy_id,p_enterprise_id,p_range,0,2);

    #智能分流后，重新取订单产品个数
    SET v_order_product_count = 0;
    
    SELECT COUNT(*)
    INTO v_order_product_count
    FROM temp_order_product;
      
    IF v_order_product_count <= 0 THEN
      SET p_out_flag = -1; #无法找到相应的产品
      SET t_error = 1;
      ROLLBACK; 
      LEAVE label_pro; 

    ELSEIF v_order_product_count = 1 THEN #主通道

      #如果只有一条，主备通道取一样的值     
      #INSERT INTO temp_back_order_product(product_id ,product_name ,channel_id ,operator_id ,province_id ,price ,discount ,number,size,channel_name)
      SELECT product_id ,product_name ,channel_id ,operator_id ,province_id,city_id ,price ,discount ,number,size,channel_name
      ,channel_province_id,channel_code,channel_city_id
      INTO v_product_id ,v_product_name ,v_channel_id ,v_operator_id ,v_province_id,v_city_id ,v_price ,v_discount ,v_number,v_size,v_channel_name 
      ,v_channel_province_id,v_channel_code,v_channel_city_id
      FROM temp_order_product;
      #备通道信息和主通道复制
      SET v_back_channel_id = v_channel_id;
      SET v_back_channel_name = v_channel_name;
      SET v_back_product_id = v_product_id;
      SET v_back_product_name = v_product_name;
      SET v_back_channel_province_id = v_channel_province_id;
      SET v_back_channel_city_id = v_channel_city_id;
      SET v_back_channel_code = v_channel_code;

    ELSEIF v_order_product_count = 2 THEN #找到了主备通道  

      #主通道相关信息
      SELECT product_id ,product_name ,channel_id ,operator_id ,province_id,city_id ,price ,discount ,number,size,channel_name
      ,channel_province_id,channel_code,channel_city_id
      INTO v_product_id ,v_product_name ,v_channel_id ,v_operator_id ,v_province_id,v_city_id ,v_price ,v_discount ,v_number,v_size,v_channel_name
      ,v_channel_province_id,v_channel_code,v_channel_city_id
      FROM temp_order_product
      WHERE sno = 1;
      #备用通道相关信息
      SELECT product_id ,product_name ,channel_id ,channel_name ,channel_province_id,channel_code
      ,channel_city_id
      INTO v_back_product_id ,v_back_product_name ,v_back_channel_id ,v_back_channel_name,v_back_channel_province_id,v_back_channel_code
      ,v_back_channel_city_id
      FROM temp_order_product
      WHERE sno = 2;
    END IF; 
    
    #获取主通道单价
    SELECT price
    INTO v_price
    FROM temp_order_product
    WHERE sno = 1;

    #获取用户折扣类型
    SET v_is_discount = f_get_is_discount(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id,p_size);
    
    #获取用户折扣
    IF v_is_discount IN (1,2,3,4,5) THEN #市折扣 #省折扣 #市产品折扣 #省产品折扣 #全国产品折扣
      SET v_discount_number = `f_get_discount2`(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_is_discount,p_size);
    ELSE
      SET v_discount_number = `f_get_discount`(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id);
    END IF; 
    
    #计算折口价
    SET v_discount_price = ROUND(v_price * v_discount_number,3);

    IF p_user_type = 1 THEN #代理商  
      #获得顶级代理商ID      
      SET v_one_proxy_id = f_get_top_proxy(p_proxy_id,p_user_type); 

      #获取代理商用户ID
      SELECT user_id
      INTO v_user_id  
      FROM t_flow_sys_user 
      WHERE proxy_id = p_proxy_id 
      AND is_manager = 1 LIMIT 1;

      #获得上级代理商ID
      SELECT p.top_proxy_id
      INTO v_top_proxy_id
      FROM t_flow_proxy p
      WHERE p.proxy_id = p_proxy_id;

      #获取代理商帐户余额
      SELECT pa.account_balance
      INTO v_account_balance
      FROM t_flow_proxy_account pa
      WHERE pa.proxy_id = p_proxy_id FOR UPDATE;

    ELSEIF p_user_type = 2 THEN #企业

      #获得顶级代理商ID
      SET v_one_proxy_id = f_get_top_proxy(p_enterprise_id,p_user_type);  
    
      #获取企业用户ID
      SELECT user_id 
      INTO v_user_id 
      FROM t_flow_sys_user 
      WHERE enterprise_id = p_enterprise_id
      AND is_manager = 1 LIMIT 1;

      #获得上级代理商ID
      SELECT p.top_proxy_id
      INTO v_top_proxy_id
      FROM t_flow_enterprise p
      WHERE p.enterprise_id = p_enterprise_id;

      #获取企业余额 
      SELECT ea.account_balance
      INTO v_account_balance
      FROM t_flow_enterprise_account ea
      WHERE ea.enterprise_id = p_enterprise_id FOR UPDATE;

    END IF; 
    
    #获取一级代理商的折扣
    IF v_is_discount IN(1,2,3,4,5) THEN
      SET v_one_proxy_discount = f_get_discount2(v_one_proxy_id,1,p_operator_id,p_province_id,p_city_id,v_is_discount,p_size);
    ELSE
      SET v_one_proxy_discount = f_get_discount(v_one_proxy_id,1,p_operator_id,p_province_id,p_city_id);
    END IF;
    
    #获取主通道折口
    SET v_top_discount = f_get_channel_discount(v_channel_id,p_operator_id,p_province_id,p_city_id,v_object_id,p_user_type,p_size);

    #获取备通道折口
    SET v_back_top_discount = f_get_channel_discount(v_back_channel_id,p_operator_id,p_province_id,p_city_id,v_object_id,p_user_type,p_size);
    
    #余额不足(下游企业用户的余额不足)
    IF v_account_balance < v_discount_price THEN
      SET p_out_flag = -2; 
      SET t_error = 1;
      ROLLBACK; 
      LEAVE label_pro; 
    END IF; 

    #更新帐户余额相关金额
    IF p_user_type = 1 THEN #代理商  
      #更新代理商金额
      UPDATE LOW_PRIORITY t_flow_proxy_account pa
      SET pa.freeze_money = pa.freeze_money + v_discount_price
      ,pa.account_balance = pa.account_balance - v_discount_price
      WHERE pa.proxy_id = p_proxy_id; 
    ELSEIF p_user_type = 2 THEN #企业
      #更新企业余额
      UPDATE LOW_PRIORITY t_flow_enterprise_account ea
      SET ea.freeze_money = ea.freeze_money + v_discount_price
      ,ea.account_balance = ea.account_balance - v_discount_price
      WHERE ea.enterprise_id = p_enterprise_id;
    END IF; 
      

    IF p_user_type = 1 THEN #代理商
      IF v_is_discount IN (3,4,5) THEN
        SET v_profit_case = f_get_profit_case_product(p_proxy_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_price,p_size,v_is_discount);
      ELSE
        SET v_profit_case = f_get_profit_case(p_proxy_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_price,v_is_discount);
      END IF;

      #代理商名称
      SET v_proxy_name  = f_get_proxy_name(p_proxy_id,p_user_type);

    ELSEIF p_user_type = 2 THEN #企业
      IF v_is_discount IN (3,4,5) THEN
        SET v_profit_case = f_get_profit_case_product(p_enterprise_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_price,p_size,v_is_discount);
      ELSE
        SET v_profit_case = f_get_profit_case(p_enterprise_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_price,v_is_discount);
      END IF;
      
      #企业名称
      SET v_enterprise_name = f_get_proxy_name(p_enterprise_id,p_user_type);
    END IF;
    
    IF v_profit_case IS NULL THEN
      SET v_profit_case = '[]';
    END IF;
    
    #一级代理商名称
    SET v_one_proxy_name = f_get_proxy_name(v_one_proxy_id,1);
    
    #主备通道返利折扣
    SET v_top_rebate_discount = f_get_channel_rebate_discount(v_channel_id,p_operator_id,p_province_id,p_city_id);
    SET v_back_top_rebate_discount = f_get_channel_rebate_discount(v_back_channel_id,p_operator_id,p_province_id,p_city_id);    

    SELECT province_name 
    INTO v_province_name
    FROM t_flow_sys_province 
    WHERE province_id = p_province_id LIMIT 1;
    
    SELECT city_name 
    INTO v_city_name
    FROM `t_flow_sys_city` 
    WHERE city_id = p_city_id LIMIT 1;
    
    #获取order_id
    SET v_order_id = f_get_increment_id();

    #新增订单 为了优化监控将订单province_id改成手机号所在省份
    INSERT DELAYED INTO t_flow_order_pre(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
    ,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
    ,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
    ,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,one_proxy_id,orderno_id
    ,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
    ,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
    VALUES(v_order_id,p_order_code,'',p_user_type,IFNULL(p_proxy_id,0),IFNULL(p_enterprise_id,0),p_operator_id
    ,v_channel_id,v_back_channel_id,v_product_id,v_back_product_id,p_province_id,p_mobile,v_price,v_discount_price
    ,1,1,NOW(6),p_order_effect_date,0,0,p_source_type,NULL
    ,p_back_fail_desc,NULL,v_profit_case,0,0,v_top_discount,v_one_proxy_discount,v_one_proxy_id,p_orderno_id
    ,v_back_top_discount,v_proxy_name,v_enterprise_name,v_one_proxy_name,0,v_top_rebate_discount,v_back_top_rebate_discount
    ,NOW(6),v_channel_code,v_back_channel_code,v_province_id,v_product_name,v_province_name,v_city_name,p_city_id);
    
    #订单回调
    IF p_url IS NOT NULL AND LENGTH(p_url) > 0 THEN
      INSERT INTO t_flow_order_callback(order_id,url,content,rece_content,times,`status`,end_date,channel_id,back_channel_id,orderno_id,order_status)
      VALUES(v_order_id,p_url,NULL,NULL,0,0,NULL,v_channel_id,v_back_channel_id,p_orderno_id,0);
    END IF;

    #记录流水   
    INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
    ,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
    ,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
    VALUES(v_discount_price,v_account_balance,v_account_balance-v_discount_price
    ,1,2,NOW(6),'购买流量',v_user_id,NOW(6),p_user_type,IFNULL(p_proxy_id,0),IFNULL(p_enterprise_id,0)
    ,1,v_top_proxy_id,0,v_order_id,NULL);

    #记录订单日志
    SET v_end_time = CURRENT_TIMESTAMP(6);
    INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
    VALUES(v_order_id,p_user_type,IFNULL(p_proxy_id,0),IFNULL(p_enterprise_id,0),v_price,v_discount_price,0 ,31 ,0 ,v_start_time,v_end_time);

  
  IF t_error = 1 THEN 
    #回滚输出标志
    SET p_out_flag = 0;
    #订单回滚     
    ROLLBACK; 
    IF v_channel_cache = 0 THEN 
      #记录订单日志
      SET v_end_time = CURRENT_TIMESTAMP(6);
      INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
      VALUES(0,p_user_type,p_proxy_id,p_enterprise_id,v_price,v_discount_price,0,80,0,v_start_time,v_end_time);
    ELSEIF v_is_cache = 1 THEN
      #记录订单日志
      SET v_end_time = CURRENT_TIMESTAMP(6);
      INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
      VALUES(0,p_user_type,p_proxy_id,p_enterprise_id,v_price,v_discount_price,0,40,0,v_start_time,v_end_time);
    ELSE
      #记录订单日志
      SET v_end_time = CURRENT_TIMESTAMP(6);
      INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
      VALUES(0,p_user_type,p_proxy_id,p_enterprise_id,v_price,v_discount_price,0,30,0,v_start_time,v_end_time);
    END IF;
  ELSE    
    #成功输出标志
    SET p_out_flag = 1;
    #提交
    COMMIT;  
  END IF; 
END
;;
DELIMITER ;


-- ----------------------------
-- Procedure structure for p_tran_create_order_20160816
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_create_order_20160816`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_create_order_20160816`(
IN p_size INT
,IN p_operator_id INT
,IN p_province_id INT 
,IN p_user_type INT
,IN p_proxy_id BIGINT
,IN p_enterprise_id BIGINT
,IN p_range INT
,IN p_profit_case VARCHAR(4000)
,IN p_order_code VARCHAR(50)
,IN p_mobile VARCHAR(11)
,IN p_order_effect_date DATETIME
,IN p_source_type INT
,IN p_orderno_id VARCHAR(50)
,IN p_url VARCHAR(100)
,IN p_back_fail_desc VARCHAR(255)
,OUT p_out_flag INT)
label_pro:BEGIN 
	/*
	author		cxw
	date		2015/05/16
	desc		处理成功订单
			2016/06/22 增加主备通道返利折扣 by cxw
			2016/06/27 增加订单缓存需求，当帐户余额不足将订单缓存 by cxw
			2016/07/25 增加控制24小时内，单个联通号码只有2次提交的机会。（避免高失败率） by cxw
	parameter:		
		p_out_flag = -1; #无法找到相应的产品
		p_out_flag = -2; #余额不足(游企业用户的余额不足)
		p_out_flag = -3; #号码在黑名单
		p_out_flag = -4; #下游订单号重复判断
		p_out_flag = -5; #上游通道账户余额不足
		p_out_flag = -6; #通道额度设置余额不足
		p_out_flag = -7; #缓存余额不足
		p_out_flag = -8; #24小时内，单个联通号码只有2次提交的机会。（避免高失败率）
		p_out_flag = 0 #存储运行失败，回滚
		p_out_flag = 1 #执行成功
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#订单产品个数
	DECLARE v_order_product_count INT DEFAULT 0;
	#订单产品序号
	DECLARE v_sno INT;
	#主通道产品iD
	DECLARE v_product_id BIGINT;
	#备通道产品iD
	DECLARE v_back_product_id BIGINT;
	#主通道产品名
	DECLARE v_product_name VARCHAR(5);
	#备通道产品名称
	DECLARE v_back_product_name VARCHAR(5);
	#主通道ID
	DECLARE v_channel_id BIGINT;
	#备通道ID
	DECLARE v_back_channel_id BIGINT;
	#运营商ID
	DECLARE v_operator_id INT;
	#DECLARE v_province_id INT;
	#单价
	DECLARE v_price DECIMAL(10,2);
	#折后价
	DECLARE v_discount DECIMAL(10,2);	
	#返点数
	DECLARE v_number VARCHAR(255);
	#产品尺寸大小
	DECLARE v_size INT;
	#主通道名
	DECLARE v_channel_name VARCHAR(30);
	#备通道名
	DECLARE v_back_channel_name VARCHAR(30);
	#固定省份集合
	DECLARE v_province_id_set VARCHAR(300) DEFAULT '9,7,15,16,3,2,8,14,21,24,11,4,18,5';
	#主折口
	DECLARE v_discount_number DECIMAL(5,3);
	#备通道折口
	DECLARE v_back_discount_number DECIMAL(5,3);
	#省份iD
	DECLARE v_province_id INT;
	#折后价
	DECLARE v_discount_price DECIMAL(10,2);
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(10,2);
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#顶级代理商ID
	DECLARE v_one_proxy_id BIGINT;
	#顶级代理商折口
	DECLARE v_one_proxy_discount DECIMAL(5,3);
	#订单ID
	DECLARE v_order_id BIGINT;
	#用户ID
	DECLARE v_user_id BIGINT;
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT;	
	#判断数
	DECLARE v_exist_count INT DEFAULT 0;
	#返利金额
	DECLARE v_profit_case TEXT;
	#代理商名称
	DECLARE v_proxy_name VARCHAR(50);
	#企业名称
	DECLARE v_enterprise_name VARCHAR(100);
	#一级代理商名称
	DECLARE v_one_proxy_name VARCHAR(50);
	#主通道省ID
	DECLARE v_channel_province_id BIGINT;
	#备通道省ID
	DECLARE v_back_channel_province_id BIGINT;
	#主通道返利折扣
	DECLARE v_top_rebate_discount DECIMAL(5,3);
	#备通道返利折扣
	DECLARE v_back_top_rebate_discount DECIMAL(5,3);
	#主通道帐户ID
	DECLARE v_account_id BIGINT;
	#备通道帐户ID
	DECLARE v_back_account_id BIGINT;
	#主通道帐户余额
	DECLARE v_surplus_money DECIMAL(10,2) DEFAULT NULL;
	#备通道帐户余额
	DECLARE v_back_surplus_money DECIMAL(10,2) DEFAULT NULL;
	#主通道额度设置余额不足
	DECLARE v_province_money DECIMAL(10,2) DEFAULT NULL;
	#备通道额度设置余额不足
	DECLARE v_back_province_money DECIMAL(10,2) DEFAULT NULL;
	#用户缓存额度
	DECLARE v_cache_credit DECIMAL(10,2) DEFAULT NULL;
	#是否缓存标志
	DECLARE v_is_cache INT DEFAULT 0;
	#是否缓存提交
	DECLARE v_is_cache_commit INT DEFAULT 0;
	#用户对象ID
	DECLARE v_object_id BIGINT;
	#是否有省折扣
	DECLARE v_is_discount INT DEFAULT 0;
	#订单数量
	DECLARE v_order_count INT DEFAULT 0;
	#主通道代码
	DECLARE v_channel_code VARCHAR(100);
	#备用通道代码
	DECLARE v_back_channel_code VARCHAR(100);
	#手机号省份名
	DECLARE v_province_name VARCHAR(100);
	#手机号市名
	DECLARE v_city_name VARCHAR(100);
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	#创建主通道订单产品临时表
	DROP TEMPORARY TABLE IF EXISTS temp_order_product;	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_product    
	(sno INT PRIMARY KEY AUTO_INCREMENT
	,product_id BIGINT
	,product_name VARCHAR(5)
	,channel_id BIGINT
	,operator_id INT
	,province_id INT #通道产品省份ID
	,price DECIMAL(10,2)
	,discount DECIMAL(10,2)
	,number VARCHAR(255)
	,size INT
	,channel_name VARCHAR(30)
	,channel_province_id BIGINT #通道省份ID
	,surplus_money DECIMAL(10,2)
	,province_money DECIMAL(10,2)
	,channel_code VARCHAR(50));
	#判断号码是否黑名单
	SELECT COUNT(*) 
	INTO v_exist_count
	FROM t_flow_mobile_blacklist mb 
	WHERE mb.mobile = p_mobile;
	
	IF v_exist_count > 0 THEN
		SET p_out_flag = -3; #号码在黑名单
		SET t_error = 1;
		ROLLBACK; 
		LEAVE label_pro; 
	END IF;
	
	#24小时内，单个联通号码只有2次提交的机会。（避免高失败率）
	IF p_operator_id = 2 THEN #暂时去掉
		SELECT COUNT(*)
		INTO v_order_count
		FROM t_flow_order_pre o
		WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
		AND o.mobile = p_mobile;
		
		SELECT v_order_count + COUNT(*)
		INTO v_order_count
		FROM t_flow_order o
		WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
		AND o.mobile = p_mobile;
		
		IF v_order_count >= 2 THEN
			SET p_out_flag = -8; 
			SET t_error = 1;
			ROLLBACK; 
			LEAVE label_pro; 
		END IF;
	END IF;
	
	#用户是否需要缓存订单
	IF p_user_type = 1 THEN	#代理商
		SELECT pa.`cache_credit` 
		INTO v_cache_credit
		FROM `t_flow_proxy_account` pa 
		WHERE pa.`proxy_id` = p_proxy_id;
		
		SET v_object_id = p_proxy_id;	
	ELSEIF p_user_type = 2 THEN #企业			
		SELECT ea.`cache_credit` 
		INTO v_cache_credit
		FROM `t_flow_enterprise_account` ea 
		WHERE ea.enterprise_id = p_enterprise_id;
		
		SET v_object_id = p_enterprise_id;
	END IF;
	#判断企业是否进行订单缓存
	IF v_cache_credit IS NULL THEN
		SET v_is_cache = 0;
	ELSE 
		SET v_is_cache = 1;
	END IF;
	
	#select v_is_cache;
	#下游订单号重复判断
	SET v_exist_count = 0;
	IF p_orderno_id IS NOT NULL AND LENGTH(p_orderno_id) > 0 THEN
		IF p_user_type = 1 THEN
			SELECT COUNT(*)
			INTO v_exist_count
			FROM t_flow_order_pre op 
			WHERE op.`orderno_id` = p_orderno_id AND op.`proxy_id` = p_proxy_id;
			
			SELECT v_exist_count + COUNT(*)
			INTO v_exist_count
			FROM t_flow_order op 
			WHERE op.`orderno_id` = p_orderno_id AND op.`proxy_id` = p_proxy_id;
		ELSEIF p_user_type = 2 THEN
			SELECT COUNT(*)
			INTO v_exist_count
			FROM t_flow_order_pre op 
			WHERE op.`orderno_id` = p_orderno_id AND op.`enterprise_id` = p_enterprise_id;
			
			SELECT v_exist_count + COUNT(*)
			INTO v_exist_count
			FROM t_flow_order op 
			WHERE op.`orderno_id` = p_orderno_id AND op.`enterprise_id` = p_enterprise_id;
		END IF;
		IF v_exist_count > 0 THEN
			SET p_out_flag = -4; #下游订单号重复判断
			SET t_error = 1;
			ROLLBACK; 
			LEAVE label_pro; 
		END IF;
	END IF;
	
	#SET v_province_id = p_province_id;
	
	#开始事务
	START TRANSACTION;
		#获取订单产品
		#先按特定通道 is_filter = 1	
		CALL p_get_order_product(p_size,p_operator_id,p_province_id,p_user_type,p_proxy_id,p_enterprise_id,p_range,1,v_is_cache,1);
		
		#获得特定通道订单产品个数
		SELECT COUNT(*)
		INTO v_order_product_count
		FROM temp_order_product;
		#如果找到，就取正常通道取1，用于备用通道
		#select v_order_product_count;
		
		IF v_order_product_count > 0 THEN
			CALL p_get_order_product(p_size,p_operator_id,p_province_id,p_user_type,p_proxy_id,p_enterprise_id,p_range,0,v_is_cache,1);
		ELSE
			#取两条，用于主备通道
			CALL p_get_order_product(p_size,p_operator_id,p_province_id,p_user_type,p_proxy_id,p_enterprise_id,p_range,0,v_is_cache,2);
		END IF;
		
		#智能分流后，重新取订单产品个数
		SET v_order_product_count = 0;
		
		SELECT COUNT(*)
		INTO v_order_product_count
		FROM temp_order_product;
			
		IF v_order_product_count <= 0 THEN
			SET p_out_flag = -1; #无法找到相应的产品
			SET t_error = 1;
			ROLLBACK; 
			LEAVE label_pro; 
		ELSEIF v_order_product_count = 1 THEN #主通道
			#如果只有一条，主备通道取一样的值			
			#INSERT INTO temp_back_order_product(product_id ,product_name ,channel_id ,operator_id ,province_id ,price ,discount ,number,size,channel_name)
			SELECT product_id ,product_name ,channel_id ,operator_id ,province_id ,price ,discount ,number,size,channel_name,channel_province_id,channel_code
			INTO v_product_id ,v_product_name ,v_channel_id ,v_operator_id ,v_province_id ,v_price ,v_discount ,v_number,v_size,v_channel_name ,v_channel_province_id,v_channel_code
			FROM temp_order_product;
			#备通道信息和主通道复制
			SET v_back_channel_id = v_channel_id;
			SET v_back_channel_name = v_channel_name;
			SET v_back_product_id = v_product_id;
			SET v_back_product_name = v_product_name;
			SET v_back_channel_province_id = v_channel_province_id;
			SET v_back_channel_code = v_channel_code;
		ELSEIF v_order_product_count = 2 THEN #找到了主备通道	
			#主通道相关信息
			SELECT product_id ,product_name ,channel_id ,operator_id ,province_id ,price ,discount ,number,size,channel_name,channel_province_id,channel_code
			INTO v_product_id ,v_product_name ,v_channel_id ,v_operator_id ,v_province_id ,v_price ,v_discount ,v_number,v_size,v_channel_name,v_channel_province_id,v_channel_code
			FROM temp_order_product
			WHERE sno = 1;
			#备用通道相关信息
			SELECT product_id ,product_name ,channel_id ,channel_name ,channel_province_id,channel_code
			INTO v_back_product_id ,v_back_product_name ,v_back_channel_id ,v_back_channel_name,v_back_channel_province_id,v_back_channel_code
			FROM temp_order_product
			WHERE sno = 2;
		END IF;	
		
		#获取主通道单价
		SELECT price
		INTO v_price
		FROM temp_order_product
		WHERE sno = 1;
		#获取通道帐户ID
		SELECT account_id 
		INTO v_account_id
		FROM t_flow_channel 
		WHERE channel_id = v_channel_id;
		
		SELECT account_id 
		INTO v_back_account_id
		FROM t_flow_channel 
		WHERE channel_id = v_back_channel_id;	
		/*
		SELECT CONCAT('v_is_cache=',v_is_cache,',v_channel_id=',v_channel_id,',v_back_channel_id='
		,v_back_channel_id,',v_account_id=',v_account_id,',v_province_id=',v_province_id);
		*/
		#缓存订单处理
		#select v_channel_id,v_back_channel_id;
		IF v_is_cache = 1 THEN
			IF v_channel_id <> v_back_channel_id THEN
				#获取主通道折口
				SET v_top_discount = f_get_channel_discount(v_channel_id,p_operator_id,p_province_id);
				#获取备通道折口
				SET v_back_top_discount = f_get_channel_discount(v_back_channel_id,p_operator_id,p_province_id);			
				#上游通道账户余额(主、备通道)
				SELECT SUM(CASE account_id WHEN v_account_id THEN surplus_money ELSE NULL END)  surplus_money
				,SUM(CASE account_id WHEN v_back_account_id THEN surplus_money ELSE NULL END) back_surplus_money
				INTO v_surplus_money,v_back_surplus_money
				FROM t_flow_channel_account ca
				WHERE account_id IN(v_account_id,v_back_account_id) FOR UPDATE;
				#扣省份主备通道金额(通道额度设置余额不足)
				SELECT SUM(CASE channel_id WHEN v_channel_id THEN province_money ELSE NULL END)  province_money
				,SUM(CASE channel_id WHEN v_back_channel_id THEN province_money ELSE NULL END) back_province_money
				INTO v_province_money,v_back_province_money
				FROM t_flow_channel_province ca
				WHERE province_id = v_province_id AND channel_id IN(v_channel_id,v_back_channel_id) FOR UPDATE;
				
				#SELECT v_province_money,v_back_province_money,v_surplus_money,v_back_surplus_money,v_channel_id,v_back_channel_id,v_province_id;
				
				#如果主通道余额不足，主通道备份备用通道
				IF v_surplus_money IS NOT NULL THEN
					IF IFNULL(v_surplus_money,0) < ROUND(v_price*v_top_discount,2) THEN
						SET v_channel_id = v_back_channel_id;
						SET v_channel_name = v_back_channel_name;
						SET v_product_id = v_back_product_id;
						SET v_product_name = v_back_product_name;
						#SET v_surplus_money = v_back_surplus_money;
						SET v_account_id = v_back_account_id;
					END IF;
				END IF;
				
				IF v_back_surplus_money IS NOT NULL THEN
					#如果备通道余额不足，备通道备份主通道
					IF IFNULL(v_back_surplus_money,0) < ROUND(v_price*v_back_top_discount,2) THEN
						SET v_back_channel_id = v_channel_id;
						SET v_back_channel_name = v_channel_name;
						SET v_back_product_id = v_product_id;
						SET v_back_product_name = v_product_name;
						#SET v_back_surplus_money = v_surplus_money;
						SET v_back_account_id = v_account_id;
					END IF;
				END IF;
				#SELECT v_province_money,v_back_province_money,v_surplus_money,v_back_surplus_money,v_channel_id,v_back_channel_id,v_province_id;
							
				#如果备通道额度设置余额不足，备通道备份主通道
				IF v_province_money IS NOT NULL THEN
					IF IFNULL(v_province_money,0) < ROUND(v_price*v_top_discount,2) THEN
						SET v_channel_id = v_back_channel_id;
						SET v_channel_name = v_back_channel_name;
						SET v_product_id = v_back_product_id;
						SET v_product_name = v_back_product_name;
						#SET v_province_money = v_back_province_money;
						SET v_account_id = v_back_account_id;
					END IF;
				END IF;
				
				IF v_back_province_money IS NOT NULL THEN
					IF IFNULL(v_back_province_money,0) < ROUND(v_price*v_back_top_discount,2) THEN
						SET v_back_channel_id = v_channel_id;
						SET v_back_channel_name = v_channel_name;
						SET v_back_product_id = v_product_id;
						SET v_back_product_name = v_product_name;
						#SET v_back_province_money = v_province_money;
						SET v_back_account_id = v_account_id;
						#SELECT 5,v_province_money,v_back_province_money,v_surplus_money,v_back_surplus_money,v_channel_id,v_back_channel_id,v_province_id;		 
					END IF;	
				END IF;	
				#复制完之后，额度再重新取一次
				SELECT SUM(CASE account_id WHEN v_account_id THEN surplus_money ELSE NULL END)  surplus_money
				,SUM(CASE account_id WHEN v_back_account_id THEN surplus_money ELSE NULL END) back_surplus_money
				INTO v_surplus_money,v_back_surplus_money
				FROM t_flow_channel_account ca
				WHERE account_id IN(v_account_id,v_back_account_id);
				#扣省份主备通道金额(通道额度设置余额不足)
				SELECT SUM(CASE channel_id WHEN v_channel_id THEN province_money ELSE NULL END)  province_money
				,SUM(CASE channel_id WHEN v_back_channel_id THEN province_money ELSE NULL END) back_province_money
				INTO v_province_money,v_back_province_money
				FROM t_flow_channel_province ca
				WHERE province_id = v_province_id AND channel_id IN(v_channel_id,v_back_channel_id);
				
				#SELECT v_province_money,v_back_province_money,v_surplus_money,v_back_surplus_money,v_channel_id,v_back_channel_id,v_province_id;		
				
			ELSE		
				SELECT surplus_money
				INTO v_surplus_money
				FROM t_flow_channel_account ca
				WHERE account_id = v_account_id FOR UPDATE;
				
				SELECT province_money
				INTO v_province_money
				FROM t_flow_channel_province ca
				WHERE province_id = v_province_id AND channel_id = v_channel_id FOR UPDATE;
				
				SET v_back_account_id = v_account_id;
				SET v_back_surplus_money = v_surplus_money;
				SET v_back_province_money = v_province_money;
			END IF;
		END IF;		
		
		#SELECT v_province_money,v_back_province_money,v_surplus_money,v_back_surplus_money,v_channel_id,v_back_channel_id,v_province_id;
		
		SET v_is_discount = f_get_is_discount(v_object_id,p_user_type,p_operator_id,p_province_id);		
		
		IF v_is_discount = 1 THEN
			SET v_discount_number = `f_get_discount2`(v_object_id,p_user_type,p_operator_id,p_province_id);
		ELSE
			SET v_discount_number = `f_get_discount`(v_object_id,p_user_type,p_operator_id,p_province_id);
		END IF;	
		
		#计算折口价
		SET v_discount_price = ROUND(v_price * v_discount_number,2);
		
		#SELECT CONCAT('v_is_discount=',v_is_discount,',v_discount_number=',v_discount_number,',v_discount_price=',v_discount_price);
		
		IF p_user_type = 1 THEN #代理商	
			#获得顶级代理商ID			
			SET v_one_proxy_id = f_get_top_proxy(p_proxy_id,p_user_type);	
			#select CONCAT('v_one_proxy_id = ',v_one_proxy_id);
			#获取代理商用户ID
			SELECT user_id
			INTO v_user_id 	
			FROM t_flow_sys_user 
			WHERE proxy_id = p_proxy_id 
			AND is_manager = 1 LIMIT 1;
			#获得上级代理商ID
			SELECT p.top_proxy_id
			INTO v_top_proxy_id
			FROM t_flow_proxy p
			WHERE p.proxy_id = p_proxy_id;
			#获取代理商帐户余额
			SELECT pa.account_balance,pa.`cache_credit` 
			INTO v_account_balance,v_cache_credit
			FROM t_flow_proxy_account pa
			WHERE pa.proxy_id = p_proxy_id FOR UPDATE;
		ELSEIF p_user_type = 2 THEN #企业
			#获得顶级代理商ID
			SET v_one_proxy_id = f_get_top_proxy(p_enterprise_id,p_user_type);			
			#获取企业用户ID
			SELECT user_id 
			INTO v_user_id 
			FROM t_flow_sys_user 
			WHERE enterprise_id = p_enterprise_id
			AND is_manager = 1 LIMIT 1;
			#获得上级代理商ID
			SELECT p.top_proxy_id
			INTO v_top_proxy_id
			FROM t_flow_enterprise p
			WHERE p.enterprise_id = p_enterprise_id;
			#获取企业余额	
			SELECT ea.account_balance,ea.`cache_credit` 
			INTO v_account_balance,v_cache_credit
			FROM t_flow_enterprise_account ea
			WHERE ea.enterprise_id = p_enterprise_id FOR UPDATE;
		END IF;	
		
		#获取顶级代理商折口,固定的用户类型为1
		IF v_is_discount = 1 THEN
			SET v_one_proxy_discount = f_get_discount2(v_one_proxy_id,1,p_operator_id,p_province_id);
		ELSE
			SET v_one_proxy_discount = f_get_discount(v_one_proxy_id,1,p_operator_id,p_province_id);
		END IF;
		#获取主通道折口
		SET v_top_discount = f_get_channel_discount(v_channel_id,p_operator_id,p_province_id);
		#获取备通道折口
		SET v_back_top_discount = f_get_channel_discount(v_back_channel_id,p_operator_id,p_province_id);
		
		#相关余额判断（1、下游企业用户的余额不足。2、上游通道账户余额不足。3、通道额度设置余额不足）
		#select v_account_balance , v_discount_price
		#1、余额不足(下游企业用户的余额不足)
		IF v_account_balance < v_discount_price THEN
			IF v_is_cache = 1 THEN
				SET v_is_cache_commit = 1;
				SET p_out_flag = -2; 
			ELSE
				SET p_out_flag = -2; 
				SET t_error = 1;
				ROLLBACK; 
				LEAVE label_pro; 
			END IF;
		END IF;	
		#2、通道余额不足(上游通道账户余额不足)
		#3、通道额度设置余额不足
		IF v_is_cache = 1 THEN			
			IF v_channel_id <> v_back_channel_id THEN
				IF (v_surplus_money IS NOT NULL AND v_surplus_money < ROUND(v_price*v_top_discount,2)) 
					OR  (v_back_surplus_money IS NOT NULL AND v_back_surplus_money < ROUND(v_price*v_back_top_discount,2)) THEN
					SET v_is_cache_commit = 1;
					SET p_out_flag = -5; 
				END IF;
				
				IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,2)) 
					OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,2)) THEN
					SET v_is_cache_commit = 1;
					SET p_out_flag = -6; 
				END IF;
			ELSE				
				IF v_surplus_money IS NOT NULL AND v_surplus_money < ROUND(v_price*v_top_discount,2)*2  THEN
					SET v_is_cache_commit = 1;
					SET p_out_flag = -5; 
				END IF;
				
				IF v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,2)*2  THEN
					SET v_is_cache_commit = 1;
					SET p_out_flag = -6;
				END IF;
			END IF;	
			
			#缓存额度不足
			IF v_cache_credit < v_discount_price AND v_is_cache_commit = 1 THEN
				SET p_out_flag = -7; 
				SET t_error = 1;
				ROLLBACK; 
				LEAVE label_pro; 
			END IF;	
		END IF;
		#SELECT p_out_flag,v_is_cache,v_is_cache_commit;		
		IF v_is_cache = 0 OR (v_is_cache = 1 AND v_is_cache_commit = 0) THEN
			#更新帐户余额相关金额
			IF p_user_type = 1 THEN #代理商	
				#更新代理商金额
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.freeze_money = pa.freeze_money + v_discount_price
				,pa.account_balance = pa.account_balance - v_discount_price
				WHERE pa.proxy_id = p_proxy_id;	
			ELSEIF p_user_type = 2 THEN #企业
				#更新企业余额
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.freeze_money = ea.freeze_money + v_discount_price
				,ea.account_balance = ea.account_balance - v_discount_price
				WHERE ea.enterprise_id = p_enterprise_id;
			END IF;	
			
			#通道帐户金额，多个通道对应一个账户 by cxw 201607606
			#扣主通道金额
			UPDATE LOW_PRIORITY t_flow_channel_account
			SET surplus_money = surplus_money - ROUND(v_price*v_top_discount,2)
			WHERE account_id = v_account_id;
			#扣备通道金额
			UPDATE LOW_PRIORITY t_flow_channel_account
			SET surplus_money = surplus_money - ROUND(v_price*v_back_top_discount,2)
			WHERE account_id = v_back_account_id;
			#select CONCAT('v_account_id=',v_account_id,',v_back_account_id=',v_back_account_id);
			#扣省份主通道金额
			UPDATE LOW_PRIORITY t_flow_channel_province
			SET province_money = province_money - ROUND(v_price*v_top_discount,2)
			WHERE province_id = v_province_id AND channel_id = v_channel_id;
			#扣省份备用通道金额
			UPDATE LOW_PRIORITY t_flow_channel_province
			SET province_money = province_money - ROUND(v_price*v_back_top_discount,2)
			WHERE province_id = v_province_id AND channel_id = v_back_channel_id;
		END IF;
		
		IF p_user_type = 1 THEN #代理商
			SET v_profit_case = f_get_profit_case(p_proxy_id,p_user_type,p_operator_id,p_province_id,v_price);
			#代理商名称
			SET v_proxy_name  = f_get_proxy_name(p_proxy_id,p_user_type);
		ELSEIF p_user_type = 2 THEN #企业
			SET v_profit_case = f_get_profit_case(p_enterprise_id,p_user_type,p_operator_id,p_province_id,v_price);
			#企业名称
			SET v_enterprise_name = f_get_proxy_name(p_enterprise_id,p_user_type);
		END IF;
		
		IF v_profit_case IS NULL THEN
			SET v_profit_case = '[]';
		END IF;
		
		#一级代理商名称
		SET v_one_proxy_name = f_get_proxy_name(v_one_proxy_id,1);
		
		#主备通道返利折扣
		SET v_top_rebate_discount = f_get_channel_rebate_discount(v_channel_id,p_operator_id,p_province_id);
		SET v_back_top_rebate_discount = f_get_channel_rebate_discount(v_back_channel_id,p_operator_id,p_province_id);		
		#获取手机号省份名及市名 by cxw 2016-08-01
		SELECT md.province_name,md.city_name
		INTO v_province_name,v_city_name
		FROM `t_flow_sys_mobile_dict` md 
		WHERE md.mobile = p_mobile LIMIT 1;
		
		IF v_is_cache = 1 AND v_is_cache_commit = 1 THEN
			#扣除缓存金额			
			IF p_user_type = 1 THEN #代理商	
				#更新代理商金额
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.cache_credit = pa.cache_credit - v_discount_price
				WHERE pa.proxy_id = p_proxy_id;	
			ELSEIF p_user_type = 2 THEN #企业
				#更新企业余额
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.cache_credit = ea.cache_credit - v_discount_price
				WHERE ea.enterprise_id = p_enterprise_id;
			END IF;	
			#缓存订单
			INSERT INTO `t_flow_order_cache`(order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,url,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name)
			VALUES(p_order_code,NULL,p_user_type,IFNULL(p_proxy_id,0),IFNULL(p_enterprise_id,0),p_operator_id
			,v_channel_id,v_back_channel_id,v_product_id,v_back_product_id,p_province_id,p_mobile,v_price,v_discount_price
			,1,1,NOW(6),p_order_effect_date,0,0,p_source_type,NULL
			,p_back_fail_desc,NULL,v_profit_case,0,0,v_top_discount,v_one_proxy_discount,v_one_proxy_id,p_orderno_id
			,v_back_top_discount,v_proxy_name,v_enterprise_name,v_one_proxy_name,0,v_top_rebate_discount,v_back_top_rebate_discount
			,p_url,NOW(6),v_channel_code,v_back_channel_code,v_province_id,v_product_name,v_province_name,v_city_name);
			#select CONCAT('order_id =',v_order_id);
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			VALUES(v_order_id,p_user_type,IFNULL(p_proxy_id,0),IFNULL(p_enterprise_id,0),v_price,v_discount_price,0 ,41 ,0 ,v_start_time,v_end_time);
			#SELECT CONCAT('log_id =',@@IDENTITY);
		ELSE	
			#新增订单 为了优化监控将订单province_id改成手机号所在省份
			INSERT DELAYED INTO t_flow_order_pre(order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name)
			VALUES(p_order_code,NULL,p_user_type,IFNULL(p_proxy_id,0),IFNULL(p_enterprise_id,0),p_operator_id
			,v_channel_id,v_back_channel_id,v_product_id,v_back_product_id,p_province_id,p_mobile,v_price,v_discount_price
			,1,1,NOW(6),p_order_effect_date,0,0,p_source_type,NULL
			,p_back_fail_desc,NULL,v_profit_case,0,0,v_top_discount,v_one_proxy_discount,v_one_proxy_id,p_orderno_id
			,v_back_top_discount,v_proxy_name,v_enterprise_name,v_one_proxy_name,0,v_top_rebate_discount,v_back_top_rebate_discount
			,NOW(6),v_channel_code,v_back_channel_code,v_province_id,v_product_name,v_province_name,v_city_name);
			#获得订单自增ID
			SET v_order_id = @@IDENTITY;
			#select CONCAT('order_id =',v_order_id);
			#订单回调
			IF p_url IS NOT NULL AND LENGTH(p_url) > 0 THEN
				INSERT INTO t_flow_order_callback(order_id,url,content,rece_content,times,`status`,end_date)
				VALUES(v_order_id,p_url,NULL,NULL,0,0,NULL);
				#SELECT CONCAT('callback_id =',@@IDENTITY);
			END IF;
			#记录流水		
			INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
			,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
			,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
			VALUES(v_discount_price,v_account_balance,v_account_balance-v_discount_price
			,1,2,NOW(6),'购买流量',v_user_id,NOW(6),p_user_type,IFNULL(p_proxy_id,0),IFNULL(p_enterprise_id,0)
			,1,v_top_proxy_id,0,v_order_id,NULL);
			#SELECT CONCAT('record_id =',@@IDENTITY);
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			VALUES(v_order_id,p_user_type,IFNULL(p_proxy_id,0),IFNULL(p_enterprise_id,0),v_price,v_discount_price,0 ,31 ,0 ,v_start_time,v_end_time);
			#SELECT CONCAT('log_id =',@@IDENTITY);	
		END IF;		
		
	IF t_error = 1 THEN 
		#回滚输出标志
		SET p_out_flag = 0;
		#订单回滚			
		ROLLBACK; 
		IF v_is_cache = 1 THEN
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			VALUES(0,p_user_type,p_proxy_id,p_enterprise_id,v_price,v_discount_price,0,40,0,v_start_time,v_end_time);
		ELSE
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			VALUES(0,p_user_type,p_proxy_id,p_enterprise_id,v_price,v_discount_price,0,30,0,v_start_time,v_end_time);
		END IF;
	ELSE  	
		#成功输出标志
		SET p_out_flag = 1;
		#提交
		COMMIT;  
	END IF;	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_create_order_lk
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_create_order_lk`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_create_order_lk`(IN p_size INT
,IN p_operator_id INT
,IN p_province_id INT 
,IN p_city_id INT
,IN p_user_type INT
,IN p_proxy_id BIGINT
,IN p_enterprise_id BIGINT
,IN p_range INT
,IN p_profit_case VARCHAR(4000)
,IN p_order_code VARCHAR(50)
,IN p_mobile VARCHAR(11)
,IN p_order_effect_date DATETIME
,IN p_source_type INT
,IN p_orderno_id VARCHAR(50)
,IN p_url VARCHAR(100)
,IN p_back_fail_desc VARCHAR(255)
,OUT p_out_flag INT)
label_pro:BEGIN 
	/*
	author		cxw
	date		2015/05/16
	desc		处理成功订单
			2016/06/22 增加主备通道返利折扣 by cxw
			2016/06/27 增加订单缓存需求，当帐户余额不足将订单缓存 by cxw
			2016/07/25 增加控制24小时内，单个联通号码只有2次提交的机会。（避免高失败率） by cxw1
			2016/09/09 用户产品折扣判断 by lk
			2016/09/26 去除上游通道余额不足判断 by lk
			2016/10/20 新增通道缓存判断 by lk
	parameter:		
		p_out_flag = -1; #无法找到相应的产品
		p_out_flag = -2; #余额不足(游企业用户的余额不足)
		p_out_flag = -3; #号码在黑名单
		p_out_flag = -4; #下游订单号重复判断
		p_out_flag = -5; #上游通道账户余额不足
		p_out_flag = -6; #通道额度设置余额不足
		p_out_flag = -7; #缓存余额不足
		p_out_flag = -8; #24小时内，单个联通号码只有2次提交的机会。（避免高失败率）
		p_out_flag = -9; #市通道额度设置余额不足
		p_out_flag = -10; #上游通道维护中
		p_out_flag = -11; #用户通道缓存额度不足
		p_out_flag = 0 #存储运行失败，回滚
		p_out_flag = 1 #执行成功
		
	*/
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	#订单产品个数
	DECLARE v_order_product_count INT DEFAULT 0;
	#订单产品序号
	DECLARE v_sno INT;
	#主通道产品iD
	DECLARE v_product_id BIGINT;
	#备通道产品iD
	DECLARE v_back_product_id BIGINT;
	#主通道产品名
	DECLARE v_product_name VARCHAR(5);
	#备通道产品名称
	DECLARE v_back_product_name VARCHAR(5);
	#主通道ID
	DECLARE v_channel_id BIGINT;
	#备通道ID
	DECLARE v_back_channel_id BIGINT;
	#运营商ID
	DECLARE v_operator_id INT;
	#单价
	DECLARE v_price DECIMAL(11,3);
	#折后价
	DECLARE v_discount DECIMAL(11,3);	
	#返点数
	DECLARE v_number VARCHAR(255);
	#产品尺寸大小
	DECLARE v_size INT;
	#主通道名
	DECLARE v_channel_name VARCHAR(30);
	#备通道名
	DECLARE v_back_channel_name VARCHAR(30);
	#固定省份集合
	DECLARE v_province_id_set VARCHAR(300) DEFAULT '9,7,15,16,3,2,8,14,21,24,11,4,18,5';
	#主折口
	DECLARE v_discount_number DECIMAL(5,3);
	#备通道折口
	DECLARE v_back_discount_number DECIMAL(5,3);
	#通道产品省份ID
	DECLARE v_province_id INT;
	#通道产品市ID
	DECLARE v_city_id INT;
	#折后价
	DECLARE v_discount_price DECIMAL(11,3);
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(11,3);
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#顶级代理商ID
	DECLARE v_one_proxy_id BIGINT;
	#顶级代理商折口
	DECLARE v_one_proxy_discount DECIMAL(5,3);
	#订单ID
	DECLARE v_order_id BIGINT;
	#用户ID
	DECLARE v_user_id BIGINT;
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT;	
	#判断数
	DECLARE v_exist_count INT DEFAULT 0;
	#返利金额
	DECLARE v_profit_case TEXT;
	#代理商名称
	DECLARE v_proxy_name VARCHAR(50);
	#企业名称
	DECLARE v_enterprise_name VARCHAR(100);
	#一级代理商名称
	DECLARE v_one_proxy_name VARCHAR(50);
	#主通道省ID
	DECLARE v_channel_province_id BIGINT;
	#备通道省ID
	DECLARE v_back_channel_province_id BIGINT;
	#主通道市ID
	DECLARE v_channel_city_id BIGINT;
	#备通道市ID
	DECLARE v_back_channel_city_id BIGINT;
	#主通道返利折扣
	DECLARE v_top_rebate_discount DECIMAL(5,3);
	#备通道返利折扣
	DECLARE v_back_top_rebate_discount DECIMAL(5,3);
	#主通道帐户ID
	DECLARE v_account_id BIGINT;
	#备通道帐户ID
	DECLARE v_back_account_id BIGINT;
	#主通道帐户余额
	#DECLARE v_surplus_money DECIMAL(11,3) DEFAULT NULL;
	#备通道帐户余额
	#DECLARE v_back_surplus_money DECIMAL(11,3) DEFAULT NULL;
	#省主通道额度设置余额不足
	DECLARE v_province_money DECIMAL(11,3) DEFAULT NULL;
	#省备通道额度设置余额不足
	DECLARE v_back_province_money DECIMAL(11,3) DEFAULT NULL;
	#用户缓存额度
	DECLARE v_cache_credit DECIMAL(11,3) DEFAULT NULL;
	#是否缓存标志
	DECLARE v_is_cache INT DEFAULT 0;
	#是否缓存提交
	DECLARE v_is_cache_commit INT DEFAULT 0;
	#用户对象ID
	DECLARE v_object_id BIGINT;
	#是否有省折扣
	DECLARE v_is_discount INT DEFAULT 0;
	#订单数量
	DECLARE v_order_count INT DEFAULT 0;
	#主通道代码
	DECLARE v_channel_code VARCHAR(100);
	#备用通道代码
	DECLARE v_back_channel_code VARCHAR(100);
	#手机号省份名
	DECLARE v_province_name VARCHAR(100);
	#手机号市名
	DECLARE v_city_name VARCHAR(100);
	#开始时间
	DECLARE v_start_time TIMESTAMP(6);
	#结束时间
	DECLARE v_end_time TIMESTAMP(6);
	#是否使用主通道
	DECLARE v_is_master_channel INT;
	#是否使用备通道
	DECLARE v_is_back_channel INT;
	#用户通道缓存额度
	DECLARE v_channel_cache_credit DECIMAL(11,3) DEFAULT NULL;
	#通道是否维护标志
	DECLARE v_channel_flag INT DEFAULT 1;
	DECLARE v_back_channel_flag INT DEFAULT 1;
	DECLARE v_channel_cache INT DEFAULT 1;
	
	#用户是否通道缓存标志
	DECLARE v_is_channel_cache INT DEFAULT 0;
	
	
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	#创建主通道订单产品临时表
	DROP TEMPORARY TABLE IF EXISTS temp_order_product;	
	CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_product    
	(sno INT PRIMARY KEY AUTO_INCREMENT
	,product_id BIGINT
	,product_name VARCHAR(5)
	,channel_id BIGINT
	,operator_id INT
	,province_id INT #通道产品省份ID
	,city_id INT #通道产品市ID
	,price DECIMAL(10,2)
	,discount DECIMAL(10,2)
	,number VARCHAR(255)
	,size INT
	,channel_name VARCHAR(30)
	,channel_province_id BIGINT #通道省份ID
	,surplus_money DECIMAL(11,3)
	,province_money DECIMAL(11,3)
	,channel_code VARCHAR(50)
	,channel_city_id BIGINT); #通道市ID
	#判断号码是否黑名单
	SELECT COUNT(*) 
	INTO v_exist_count
	FROM t_flow_mobile_blacklist mb 
	WHERE mb.mobile = p_mobile;
	
	IF v_exist_count > 0 THEN
		SET p_out_flag = -3; #号码在黑名单
		SET t_error = 1;
		ROLLBACK; 
		LEAVE label_pro; 
	END IF;
	
	#24小时内，单个联通号码只有2次提交的机会。（避免高失败率）
	IF p_operator_id = 2 THEN #暂时去掉
		SELECT COUNT(*)
		INTO v_order_count
		FROM t_flow_order_pre o
		WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
		AND o.mobile = p_mobile;
		
		SELECT v_order_count + COUNT(*)
		INTO v_order_count
		FROM t_flow_order o
		WHERE o.order_date BETWEEN CURDATE() AND NOW(6)
		AND o.mobile = p_mobile;
		
		IF v_order_count >= 5 THEN
			SET p_out_flag = -8; 
			SET t_error = 1;
			ROLLBACK; 
			LEAVE label_pro; 
		END IF;
	END IF;
	
	#用户是否需要缓存订单（通道缓存）
	IF p_user_type = 1 THEN	#代理商
		SELECT pa.`cache_credit` ,pa.`channel_cache_credit`
		INTO v_cache_credit,v_channel_cache_credit
		FROM `t_flow_proxy_account` pa 
		WHERE pa.`proxy_id` = p_proxy_id;
		
		SET v_object_id = p_proxy_id;	
	
	ELSEIF p_user_type = 2 THEN #企业			
		SELECT ea.`cache_credit` ,ea.`channel_cache_credit`
		INTO v_cache_credit,v_channel_cache_credit
		FROM `t_flow_enterprise_account` ea 
		WHERE ea.enterprise_id = p_enterprise_id;
		
		SET v_object_id = p_enterprise_id;
	END IF;
	#判断企业是否进行订单缓存
	IF v_cache_credit IS NULL THEN
		SET v_is_cache = 0;
	ELSE 
		SET v_is_cache = 1;
	END IF;
	#判断企业是否进行通道缓存缓存
	IF v_channel_cache_credit IS NULL THEN
		SET v_is_channel_cache = 0;
	ELSE 
		SET v_is_channel_cache = 1;
	END IF;
	#select v_is_cache;
	#下游订单号重复判断
	SET v_exist_count = 0;
	IF p_orderno_id IS NOT NULL AND LENGTH(p_orderno_id) > 0 THEN
		IF p_user_type = 1 THEN
			SELECT COUNT(*)
			INTO v_exist_count
			FROM t_flow_order_pre op 
			WHERE op.`orderno_id` = p_orderno_id AND op.`proxy_id` = p_proxy_id;
			
			SELECT v_exist_count + COUNT(*)
			INTO v_exist_count
			FROM t_flow_order op 
			WHERE op.`orderno_id` = p_orderno_id AND op.`proxy_id` = p_proxy_id;
		ELSEIF p_user_type = 2 THEN
			SELECT COUNT(*)
			INTO v_exist_count
			FROM t_flow_order_pre op 
			WHERE op.`orderno_id` = p_orderno_id AND op.`enterprise_id` = p_enterprise_id;
			
			SELECT v_exist_count + COUNT(*)
			INTO v_exist_count
			FROM t_flow_order op 
			WHERE op.`orderno_id` = p_orderno_id AND op.`enterprise_id` = p_enterprise_id;
		END IF;
		IF v_exist_count > 0 THEN
			SET p_out_flag = -4; #下游订单号重复判断
			SET t_error = 1;
			ROLLBACK; 
			LEAVE label_pro; 
		END IF;
	END IF;
	
	#SET v_province_id = p_province_id;
	
	#开始事务
	START TRANSACTION;
		#获取订单产品
		#先按特定通道 is_filter = 1	
		CALL p_get_order_product_lk(p_size,p_operator_id,p_province_id,p_city_id,p_user_type,p_proxy_id,p_enterprise_id,p_range,1,v_is_cache,v_is_channel_cache,1);
		#获得特定通道订单产品个数
		SELECT COUNT(*)
		INTO v_order_product_count
		FROM temp_order_product;
		#如果找到，就取正常通道取1，用于备用通道
		#select v_order_product_count;
		
		IF v_order_product_count > 0 THEN
			CALL p_get_order_product_lk(p_size,p_operator_id,p_province_id,p_city_id,p_user_type,p_proxy_id,p_enterprise_id,p_range,0,v_is_cache,v_is_channel_cache,1);
		ELSE
			#取两条，用于主备通道
			CALL p_get_order_product_lk(p_size,p_operator_id,p_province_id,p_city_id,p_user_type,p_proxy_id,p_enterprise_id,p_range,0,v_is_cache,v_is_channel_cache,2);
		END IF;
		
		#智能分流后，重新取订单产品个数
		SET v_order_product_count = 0;
		
		SELECT COUNT(*)
		INTO v_order_product_count
		FROM temp_order_product;
			
		IF v_order_product_count <= 0 THEN
			SET p_out_flag = -1; #无法找到相应的产品
			SET t_error = 1;
			ROLLBACK; 
			LEAVE label_pro; 
		ELSEIF v_order_product_count = 1 THEN #主通道
			#如果只有一条，主备通道取一样的值			
			
			SELECT product_id ,product_name ,channel_id ,operator_id ,province_id,city_id ,price ,discount ,number,size,channel_name
			,channel_province_id,channel_code,channel_city_id
			INTO v_product_id ,v_product_name ,v_channel_id ,v_operator_id ,v_province_id,v_city_id ,v_price ,v_discount ,v_number,v_size,v_channel_name 
			,v_channel_province_id,v_channel_code,v_channel_city_id
			FROM temp_order_product;
			#备通道信息和主通道复制
			SET v_back_channel_id = v_channel_id;
			SET v_back_channel_name = v_channel_name;
			SET v_back_product_id = v_product_id;
			SET v_back_product_name = v_product_name;
			SET v_back_channel_province_id = v_channel_province_id;
			SET v_back_channel_city_id = v_channel_city_id;
			SET v_back_channel_code = v_channel_code;
		ELSEIF v_order_product_count = 2 THEN #找到了主备通道	
			#主通道相关信息
			SELECT product_id ,product_name ,channel_id ,operator_id ,province_id,city_id ,price ,discount ,number,size,channel_name
			,channel_province_id,channel_code,channel_city_id
			INTO v_product_id ,v_product_name ,v_channel_id ,v_operator_id ,v_province_id,v_city_id ,v_price ,v_discount ,v_number,v_size,v_channel_name
			,v_channel_province_id,v_channel_code,v_channel_city_id
			FROM temp_order_product
			WHERE sno = 1;
			#备用通道相关信息
			SELECT product_id ,product_name ,channel_id ,channel_name ,channel_province_id,channel_code
			,channel_city_id
			INTO v_back_product_id ,v_back_product_name ,v_back_channel_id ,v_back_channel_name,v_back_channel_province_id,v_back_channel_code
			,v_back_channel_city_id
			FROM temp_order_product
			WHERE sno = 2;
		END IF;	
		
		#获取主通道单价
		SELECT price
		INTO v_price
		FROM temp_order_product
		WHERE sno = 1;
		#获取通道帐户ID及通道缓存标识
		SELECT account_id ,is_cache
		INTO v_account_id,v_channel_flag
		FROM t_flow_channel 
		WHERE channel_id = v_channel_id;
		
		SELECT account_id ,is_cache
		INTO v_back_account_id,v_back_channel_flag
		FROM t_flow_channel 
		WHERE channel_id = v_back_channel_id;	
		
		#如果通道缓存，则忽略用户订单缓存
		IF  v_channel_flag=0 OR v_back_channel_flag = 0  THEN
			SET v_is_cache = 0;
			SET v_channel_cache = 0;
		END IF;
		/*
		SELECT CONCAT('v_is_cache=',v_is_cache,',v_channel_id=',v_channel_id,',v_back_channel_id='
		,v_back_channel_id,',v_account_id=',v_account_id,',v_province_id=',v_province_id);
		*/
		SET v_is_discount = f_get_is_discount(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id,p_size);
		#缓存订单处理		
		IF v_is_cache = 1 THEN
			#获取主通道折口
			SET v_top_discount = f_get_channel_discount(v_channel_id,p_operator_id,p_province_id,p_city_id,v_object_id,p_user_type,p_size);				
			#获取备通道折口
			SET v_back_top_discount = f_get_channel_discount(v_back_channel_id,p_operator_id,p_province_id,p_city_id,v_object_id,p_user_type,p_size);
			
			#主通道金额(通道额度设置余额不足)		
			SET v_province_money = f_get_channel_province_money(v_channel_id,v_channel_province_id,v_channel_city_id,p_range);
			#备通道金额(通道额度设置余额不足)
			SET v_back_province_money = f_get_channel_province_money(v_back_channel_id,v_back_channel_province_id,v_back_channel_city_id,p_range);			
				
			##########################################################################################################
			#上游通道账户余额(备注：去除通道余额限制20160926)
			/*
			IF v_surplus_money IS NOT NULL AND v_back_surplus_money IS NOT NULL THEN
				IF IFNULL(v_surplus_money,0) < ROUND(v_price*v_top_discount,3) THEN #主通道额度不够
					IF IFNULL(v_back_surplus_money,0) < ROUND(v_price*v_back_top_discount,3) THEN #备通道客户度不够							
						#主备通道额度都不够，走主通道
						SET v_is_master_channel = 1;
						
					ELSE							
						#备通道额度够，走备通道
						SET v_is_back_channel = 1;
					END IF;
				ELSE 
					IF IFNULL(v_back_surplus_money,0) < ROUND(v_price*v_back_top_discount,3) THEN 
						#主通道额度足，备通道额度不足，备复制主,走主通道
						SET v_is_master_channel = 1;
					END IF;
				END IF;
			ELSEIF v_surplus_money IS NULL AND v_back_surplus_money IS NOT NULL THEN #主通道没设置额度，备通道设置
				IF IFNULL(v_back_surplus_money,0) < ROUND(v_price*v_back_top_discount,3) THEN #备通道客户度不够
					#备通道备份主通道，走主通道
					SET v_is_master_channel = 1;
				END IF;
			ELSEIF v_surplus_money IS NOT NULL AND v_back_surplus_money IS NULL THEN #主通道设置额度，备通道没设置
				IF IFNULL(v_surplus_money,0) < ROUND(v_price*v_top_discount,3) THEN 
					#主通道额度不足，主复制备通道，走备通道
					SET v_is_back_channel = 1;
				END IF;
			END IF;
			*/
			############################################################################################################
			#通道额度设置余额
			IF v_province_money IS NOT NULL  AND v_back_province_money IS NOT NULL THEN
				IF IFNULL(v_province_money,0) < ROUND(v_price*v_top_discount,3) THEN
					IF IFNULL(v_back_province_money,0) < ROUND(v_price*v_back_top_discount,3) THEN
						#主，备通道额度不足，走主通道
						SET v_is_master_channel = 1;
					ELSE 
						#主通道额度不足，备通道足，走备通道
						SET v_is_back_channel = 1;
					END IF;
				ELSE
					IF IFNULL(v_back_province_money,0) < ROUND(v_price*v_back_top_discount,3) THEN
						#主通道足，备通道不足，走主通道
						SET v_is_master_channel = 1;
					END IF;
				END IF;
			ELSEIF v_province_money IS NULL  AND v_back_province_money IS NOT NULL THEN #主通道额度不设置，备通道设置
				IF IFNULL(v_back_province_money,0) < ROUND(v_price*v_back_top_discount,3) THEN
					#备通道额度不足，走主通道
					SET v_is_master_channel = 1;
				END IF;
			ELSEIF v_province_money IS NOT NULL  AND v_back_province_money IS NULL THEN #主通道额度设置，备通道不设置
				IF IFNULL(v_province_money,0) < ROUND(v_price*v_top_discount,3) THEN
					#主通道不足，走备通道
					SET v_is_back_channel = 1;
				END IF;
			END IF;	
				
			#走主通道
			IF v_is_master_channel = 1 THEN
				SET v_back_channel_id = v_channel_id;
				SET v_back_channel_code = v_channel_code;
				SET v_back_channel_name = v_channel_name;
				SET v_back_product_id = v_product_id;
				SET v_back_product_name = v_product_name;
				SET v_back_account_id = v_account_id;
				SET v_back_province_money = v_province_money;
				#SET v_back_surplus_money = v_surplus_money;(备注：去除通道余额限制20160926)
			END IF;
			#走备通道
			IF v_is_back_channel = 1 THEN
				SET v_channel_id = v_back_channel_id;
				SET v_channel_code = v_back_channel_code;
				SET v_channel_name = v_back_channel_name;
				SET v_product_id = v_back_product_id;
				SET v_product_name = v_back_product_name;
				SET v_account_id = v_back_account_id;
				SET v_province_money = v_back_province_money;
				#SET v_surplus_money = v_back_surplus_money;(备注：去除通道余额限制20160926)
			END IF;					
		END IF;	
		/*
		IF v_is_discount = 1 THEN #市
			SET v_discount_number = `f_get_discount2`(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id,p_size,1);
		ELSEIF v_is_discount = 2 THEN #省
			SET v_discount_number = `f_get_discount2`(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id,p_size,2);
		ELSE
			SET v_discount_number = `f_get_discount`(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id);
		END IF;	
		*/
		
		IF v_is_discount IN (1,2,3,4,5) THEN #市折扣 #省折扣 #市产品折扣 #省产品折扣 #全国产品折扣
			SET v_discount_number = `f_get_discount2`(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_is_discount,p_size);
		ELSE
			SET v_discount_number = `f_get_discount`(v_object_id,p_user_type,p_operator_id,p_province_id,p_city_id);
		END IF;	
		
		
		#计算折口价
		SET v_discount_price = ROUND(v_price * v_discount_number,3);
		#SELECT CONCAT('v_is_discount=',v_is_discount,',v_discount_number=',v_discount_number,',v_discount_price=',v_discount_price);
		
		IF p_user_type = 1 THEN #代理商	
			#获得顶级代理商ID			
			SET v_one_proxy_id = f_get_top_proxy(p_proxy_id,p_user_type);	
			#select CONCAT('v_one_proxy_id = ',v_one_proxy_id);
			#获取代理商用户ID
			SELECT user_id
			INTO v_user_id 	
			FROM t_flow_sys_user 
			WHERE proxy_id = p_proxy_id 
			AND is_manager = 1 LIMIT 1;
			#获得上级代理商ID
			SELECT p.top_proxy_id
			INTO v_top_proxy_id
			FROM t_flow_proxy p
			WHERE p.proxy_id = p_proxy_id;
			#获取代理商帐户余额
			SELECT pa.account_balance,pa.`cache_credit`,pa.`channel_cache_credit`
			INTO v_account_balance,v_cache_credit,v_channel_cache_credit
			FROM t_flow_proxy_account pa
			WHERE pa.proxy_id = p_proxy_id ;
		ELSEIF p_user_type = 2 THEN #企业
			#获得顶级代理商ID
			SET v_one_proxy_id = f_get_top_proxy(p_enterprise_id,p_user_type);			
			#获取企业用户ID
			SELECT user_id 
			INTO v_user_id 
			FROM t_flow_sys_user 
			WHERE enterprise_id = p_enterprise_id
			AND is_manager = 1 LIMIT 1;
			#获得上级代理商ID
			SELECT p.top_proxy_id
			INTO v_top_proxy_id
			FROM t_flow_enterprise p
			WHERE p.enterprise_id = p_enterprise_id;
			#获取企业余额	
			SELECT ea.account_balance,ea.`cache_credit`,ea.`channel_cache_credit`
			INTO v_account_balance,v_cache_credit,v_channel_cache_credit
			FROM t_flow_enterprise_account ea
			WHERE ea.enterprise_id = p_enterprise_id ;
		END IF;	
		
		#获取顶级代理商折口,固定的用户类型为1
		/*
		IF v_is_discount = 1 THEN
			SET v_one_proxy_discount = f_get_discount2(v_one_proxy_id,1,p_operator_id,p_province_id,p_city_id,1);
		ELSEIF v_is_discount = 2 THEN
			SET v_one_proxy_discount = f_get_discount2(v_one_proxy_id,1,p_operator_id,p_province_id,p_city_id,2);
		ELSE
			SET v_one_proxy_discount = f_get_discount(v_one_proxy_id,1,p_operator_id,p_province_id,p_city_id);
		END IF;
		*/
		IF v_is_discount IN(1,2,3,4,5) THEN
			SET v_one_proxy_discount = f_get_discount2(v_one_proxy_id,1,p_operator_id,p_province_id,p_city_id,v_is_discount,p_size);
		ELSE
			SET v_one_proxy_discount = f_get_discount(v_one_proxy_id,1,p_operator_id,p_province_id,p_city_id);
		END IF;
		
		#获取主通道折口
		#SET v_top_discount = f_get_channel_discount(v_channel_id,p_operator_id,p_province_id);
		SET v_top_discount = f_get_channel_discount(v_channel_id,p_operator_id,p_province_id,p_city_id,v_object_id,p_user_type,p_size);
		#获取备通道折口
		#SET v_back_top_discount = f_get_channel_discount(v_back_channel_id,p_operator_id,p_province_id);
		SET v_back_top_discount = f_get_channel_discount(v_back_channel_id,p_operator_id,p_province_id,p_city_id,v_object_id,p_user_type,p_size);
		
		
		#通道缓存处理
		#1、通道缓存且用户未设置通道缓存额度，直接失败订单
		IF v_channel_cache = 0 AND v_is_channel_cache = 0 THEN
			SET p_out_flag = -10; 
			SET t_error = 1;
			ROLLBACK; 
			LEAVE label_pro; 
		#2、通道缓存且用户设置通道缓存额度，通道额度不够，直接失败订单
		#3、通道缓存且用户设置通道缓存额度，用户通道缓存额度不够，直接失败订单
		ELSEIF  v_channel_cache = 0 AND v_is_channel_cache = 1 THEN
			#主通道金额(通道额度设置余额不足)		
			SET v_province_money = f_get_channel_province_money(v_channel_id,v_channel_province_id,v_channel_city_id,p_range);
			#备通道金额(通道额度设置余额不足)
			SET v_back_province_money = f_get_channel_province_money(v_back_channel_id,v_back_channel_province_id,v_back_channel_city_id,p_range);			
			IF v_channel_id <> v_back_channel_id THEN
				IF v_account_id = v_back_account_id THEN						
					IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)*2) 
						OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,3)*2) THEN
						SET p_out_flag = -6; 
						SET t_error = 1;
						ROLLBACK; 
						LEAVE label_pro; 
					END IF;
				ELSE
					
					IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)) 
						OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,3)) THEN
						SET p_out_flag = -6; 
						SET t_error = 1;
						ROLLBACK; 
						LEAVE label_pro; 
					END IF;
				END IF;
			ELSE				
				IF v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)*2  THEN
					SET p_out_flag = -6;
					SET t_error = 1;
					ROLLBACK; 
					LEAVE label_pro; 
				END IF;
			END IF;	
			#缓存额度不足
			IF v_channel_cache_credit < v_discount_price  THEN
				SET p_out_flag = -11; 
				SET t_error = 1;
				ROLLBACK; 
				LEAVE label_pro; 
			END IF;	
			
		END IF;
		
		#相关余额判断（1、下游企业用户的余额不足。2、上游通道账户余额不足。3、通道额度设置余额不足）
		#select v_account_balance , v_discount_price
		#1、余额不足(下游企业用户的余额不足)
		IF v_account_balance < v_discount_price THEN
			IF v_is_cache = 1 THEN
				SET v_is_cache_commit = 1;
				SET p_out_flag = -2;
			ELSEIF v_channel_cache = 0 THEN
				SET p_out_flag = -2;
			ELSE
				SET p_out_flag = -2; 
				SET t_error = 1;
				ROLLBACK; 
				LEAVE label_pro; 
			END IF;
		END IF;	
		#2、通道余额不足(上游通道账户余额不足)
		#3、通道额度设置余额不足
		IF v_is_cache = 1 THEN			
			IF v_channel_id <> v_back_channel_id THEN
				/*(备注：去除通道余额限制20160926)
				IF (v_surplus_money IS NOT NULL AND v_surplus_money < ROUND(v_price*v_top_discount,3)) 
					OR  (v_back_surplus_money IS NOT NULL AND v_back_surplus_money < ROUND(v_price*v_back_top_discount,3)) THEN
					SET v_is_cache_commit = 1;
					SET p_out_flag = -5; 
				END IF;
				*/
				IF v_account_id = v_back_account_id THEN						
					IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)*2) 
						OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,3)*2) THEN
						SET v_is_cache_commit = 1;
						SET p_out_flag = -6; 
					END IF;
				ELSE
					
					IF (v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)) 
						OR  (v_back_province_money IS NOT NULL AND v_back_province_money < ROUND(v_price*v_back_top_discount,3)) THEN
						SET v_is_cache_commit = 1;
						SET p_out_flag = -6; 
					END IF;
				END IF;
			ELSE				
				/*(备注：去除通道余额限制20160926)
				IF v_surplus_money IS NOT NULL AND v_surplus_money < ROUND(v_price*v_top_discount,3)*2  THEN
					SET v_is_cache_commit = 1;
					SET p_out_flag = -5; 
				END IF;
				*/
				IF v_province_money IS NOT NULL AND v_province_money < ROUND(v_price*v_top_discount,3)*2  THEN
					SET v_is_cache_commit = 1;
					SET p_out_flag = -6;
				END IF;
			END IF;	
			
			#缓存额度不足
			IF v_cache_credit < v_discount_price AND v_is_cache_commit = 1 THEN
				SET p_out_flag = -7; 
				SET t_error = 1;
				ROLLBACK; 
				LEAVE label_pro; 
			END IF;	
		END IF;
		
		
		
		IF p_user_type = 1 THEN #代理商
			IF v_is_discount IN (3,4,5) THEN
				SET v_profit_case = f_get_profit_case_product(p_proxy_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_price,p_size,v_is_discount);
			ELSE
				SET v_profit_case = f_get_profit_case(p_proxy_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_price,v_is_discount);
			END IF;
			#代理商名称
			SET v_proxy_name  = f_get_proxy_name(p_proxy_id,p_user_type);
		ELSEIF p_user_type = 2 THEN #企业
			IF v_is_discount IN (3,4,5) THEN
				SET v_profit_case = f_get_profit_case_product(p_enterprise_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_price,p_size,v_is_discount);
			ELSE
				SET v_profit_case = f_get_profit_case(p_enterprise_id,p_user_type,p_operator_id,p_province_id,p_city_id,v_price,v_is_discount);
			END IF;
			
			#企业名称
			SET v_enterprise_name = f_get_proxy_name(p_enterprise_id,p_user_type);
		END IF;
		
		IF v_profit_case IS NULL THEN
			SET v_profit_case = '[]';
		END IF;
		
		#一级代理商名称
		SET v_one_proxy_name = f_get_proxy_name(v_one_proxy_id,1);
		
		#主备通道返利折扣
		SET v_top_rebate_discount = f_get_channel_rebate_discount(v_channel_id,p_operator_id,p_province_id,p_city_id);
		SET v_back_top_rebate_discount = f_get_channel_rebate_discount(v_back_channel_id,p_operator_id,p_province_id,p_city_id);		
		#获取手机号省份名及市名 by cxw 2016-08-01
		/*
		SELECT md.province_name,md.city_name
		INTO v_province_name,v_city_name
		FROM `t_flow_sys_mobile_dict` md 
		WHERE md.mobile = p_mobile LIMIT 1;
		*/
		
		SELECT province_name 
		INTO v_province_name
		FROM t_flow_sys_province 
		WHERE province_id = p_province_id LIMIT 1;
		
		SELECT city_name 
		INTO v_city_name
		FROM `t_flow_sys_city` 
		WHERE city_id = p_city_id LIMIT 1;
		
		#获取order_id
		SET v_order_id = f_get_increment_id();
		#通道缓存
		SELECT v_channel_cache,v_is_channel_cache,v_is_cache,v_is_cache_commit;
			
	IF t_error = 1 THEN 
		#回滚输出标志
		SET p_out_flag = 0;
		#订单回滚			
		ROLLBACK; 
		
	ELSE  	
		#成功输出标志
		SET p_out_flag = 1;
		#提交
		COMMIT;  
	END IF;	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_fail_order
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_fail_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_fail_order`(
IN p_order_id BIGINT
,IN p_order_status INT
,IN p_content VARCHAR(2000)
,IN p_back_content VARCHAR(2000)
,IN p_back_fail_desc VARCHAR(2000)
,IN p_remark VARCHAR(2000)
,IN p_channel_order_code VARCHAR(2000)
,OUT p_out_flag INT
)
BEGIN  		
	/*
	author		cxw
	date		2015/05/16
	desc		处理失败订单
			2016/09/09 用户产品折扣判断 by lk
	*/
	#订单用户类型 1 代理商 2 企业
	DECLARE v_order_user_type INT;
	#代理商ID
	DECLARE v_proxy_id BIGINT;
	#企业ID
	DECLARE v_enterprise_id BIGINT;
	#折后价
	DECLARE v_discount_price DECIMAL(11,3);
	#单价
	DECLARE v_price DECIMAL(11,3);
	#订单状态
	DECLARE v_order_status INT;
	#备用通道ID
	DECLARE v_back_channel_id BIGINT;
	#主通道ID
	DECLARE v_channel_id BIGINT;
	#主通道产品ID
	DECLARE v_channel_product_id BIGINT;	
	#备用通道产品ID
	DECLARE v_back_channel_product_id BIGINT;	
	#用户帐号ID
	DECLARE v_account_id BIGINT;	
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(11,3);
	#用户ID
	DECLARE v_user_id BIGINT;
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT;	
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#订单日志效果类型
	DECLARE v_tran_type INT;
	#订单日期
	DECLARE v_order_date DATETIME(6);
	#手机号省份iD
	DECLARE v_province_id INT;
	#手机号市ID
	DECLARE v_city_id INT;
	DECLARE v_proxy_enterprise_id BIGINT;
	DECLARE v_is_discount INT;
	DECLARE v_operator_id INT;
	#事务异常处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	#产品size
	DECLARE v_size INT;
	#监控随机数
	DECLARE v_rand_id INT;
	#通道code
	DECLARE v_channel_order_code VARCHAR(2000);
	
	#初始定义事务异常初始化
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 	
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	#开始事务
	START TRANSACTION;
		#获取订单信息并锁定
		SELECT op.user_type order_user_type,op.proxy_id,op.enterprise_id,op.discount_price,op.price,op.order_status,op.back_channel_id
		,op.channel_product_id,op.back_channel_product_id,op.channel_id,op.top_discount,op.back_top_discount,op.order_date,op.province_id
		,op.city_id,op.operator_id
		INTO v_order_user_type,v_proxy_id,v_enterprise_id,v_discount_price,v_price,v_order_status,v_back_channel_id
		,v_channel_product_id,v_back_channel_product_id,v_channel_id,v_top_discount,v_back_top_discount,v_order_date,v_province_id
		,v_city_id,v_operator_id
		FROM t_flow_order_pre op
		WHERE op.order_id = p_order_id AND order_status = p_order_status FOR UPDATE;
		
		#判断p_channel_order_code是否为null
		IF p_channel_order_code IS NULL THEN
			SET v_channel_order_code = '';
		ELSE
			SET v_channel_order_code = p_channel_order_code;
		END IF;
		
		IF v_proxy_id IS NOT NULL THEN	
			IF v_order_user_type = 1 THEN #代理商	
				SET v_proxy_enterprise_id = v_proxy_id;
			ELSEIF v_order_user_type = 2 THEN #企业
				SET v_proxy_enterprise_id = v_enterprise_id;
			END IF;
			#获取产品size
			SELECT size INTO v_size FROM t_flow_channel_product WHERE product_id = v_channel_product_id;
			
			SET v_is_discount = f_get_is_discount(v_proxy_enterprise_id,v_order_user_type,v_operator_id,v_province_id,v_city_id,IFNULL(v_size,0));
			
			IF v_order_status IN (3,4) THEN	#备用通道失败	
				IF v_order_user_type = 1 THEN #代理商	
					#获取代理商用户ID
					SELECT user_id 	
					INTO v_user_id 	
					FROM t_flow_sys_user 
					WHERE proxy_id = v_proxy_id 
					AND is_manager = 1 LIMIT 1;
					#获取代理商上级代理商ID
					SELECT top_proxy_id 
					INTO v_top_proxy_id	
					FROM t_flow_proxy 
					WHERE proxy_id = v_proxy_id;
					#获取代理商帐户余额
					SELECT pa.account_balance 
					INTO v_account_balance
					FROM t_flow_proxy_account pa
					WHERE pa.proxy_id = v_proxy_id 
					FOR UPDATE;
					#更新代理商相关帐户金额
					UPDATE LOW_PRIORITY t_flow_proxy_account pa
					SET pa.freeze_money = pa.freeze_money - v_discount_price
					,pa.account_balance = pa.account_balance + v_discount_price
					WHERE pa.proxy_id = v_proxy_id;	
					SET v_proxy_enterprise_id = v_proxy_id;
				ELSEIF v_order_user_type = 2 THEN #企业
					#获取企业用户ID
					SELECT user_id 
					INTO v_user_id 
					FROM t_flow_sys_user 
					WHERE enterprise_id = v_enterprise_id
					AND is_manager = 1 LIMIT 1;	
					#获取企业所属代理商ID
					SELECT top_proxy_id
					INTO v_top_proxy_id
					FROM t_flow_enterprise
					WHERE enterprise_id = v_enterprise_id;
					#获取企业帐户余额
					SELECT ea.account_balance
					INTO v_account_balance
					FROM t_flow_enterprise_account ea
					WHERE ea.enterprise_id = v_enterprise_id
					FOR UPDATE;
					#更新企业帐户相关余额
					UPDATE LOW_PRIORITY t_flow_enterprise_account ea
					SET ea.freeze_money = ea.freeze_money - v_discount_price
					,ea.account_balance = ea.account_balance + v_discount_price
					WHERE ea.enterprise_id = v_enterprise_id;
					SET v_proxy_enterprise_id = v_enterprise_id;
				END IF;
				
				#更新预处理订单状态相关信息
				UPDATE t_flow_order_pre op
				SET op.order_status = 6
				,op.back_content = p_back_content
				,op.back_fail_desc = p_back_fail_desc
				,op.channel_order_code = v_channel_order_code
				,op.complete_time = NOW()
				,op.is_using = 2
				WHERE op.order_id = p_order_id;		
			
				#更新回调信息
				UPDATE t_flow_order_callback oc
				SET oc.content = p_content,oc.order_status = 6
				,final_channel_id = v_back_channel_id
				WHERE oc.order_id = p_order_id;
				
				#通道帐户金额，多个通道对应一个账户 by cxw 201607606
				#更新通道相关金额
				UPDATE t_flow_channel_account
				SET surplus_money = surplus_money + ROUND(v_price*v_back_top_discount,3)
				#WHERE channel_id = v_back_channel_id;
				WHERE account_id = (SELECT account_id FROM t_flow_channel WHERE channel_id = v_back_channel_id);
				#更新通道省份相关金额
				
				UPDATE t_flow_channel_province cpv
				SET cpv.province_money = cpv.province_money + ROUND(v_price*v_back_top_discount,3)
				WHERE EXISTS(SELECT cp.* FROM t_flow_channel_product cp 
				WHERE cpv.channel_id = cp.channel_id 
				AND cpv.province_id = cp.province_id 
				AND cpv.city_id = cp.city_id
				AND cp.product_id = v_back_channel_product_id);	
				
				
				#记录流水
				INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
				,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
				,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
				VALUES(v_discount_price,v_account_balance,v_account_balance+v_discount_price
				,7,1,NOW(),p_remark,v_user_id,NOW(),v_order_user_type,v_proxy_id,v_enterprise_id
				,1,v_top_proxy_id,0,p_order_id,NULL);				
				#订单处理结束并生成订单
				INSERT INTO t_flow_order(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
				,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
				,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
				,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
				,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
				,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
				SELECT order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
				,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
				,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
				,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
				,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
				,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id
				FROM t_flow_order_pre op 
				WHERE op.order_id = p_order_id;					
				#删除预处理订单
				DELETE FROM t_flow_order_pre WHERE order_id = p_order_id;
				
				SET v_tran_type = 12;
			ELSEIF v_order_status IN (0,1) THEN #主通道失败
				#更新订单状态及相关信息
				UPDATE t_flow_order_pre op
				SET op.order_status = 3
				,op.back_content = p_back_content
				,op.back_fail_desc = p_back_fail_desc
				,op.is_using = 0
				,op.channel_order_code = v_channel_order_code
				WHERE op.order_id = p_order_id;	
				#通道帐户金额，多个通道对应一个账户 by cxw 201607606
				#更新通道帐户金额
				UPDATE t_flow_channel_account
				SET surplus_money = surplus_money + ROUND(v_price*v_top_discount,3)
				#WHERE channel_id = v_channel_id;
				WHERE account_id = (SELECT account_id FROM t_flow_channel WHERE channel_id = v_channel_id);		
				#更新通道省份帐户金额
				
				UPDATE t_flow_channel_province cpv
				SET cpv.province_money = cpv.province_money + ROUND(v_price*v_top_discount,3)
				WHERE EXISTS(SELECT cp.* FROM t_flow_channel_product cp 
				WHERE cpv.channel_id = cp.channel_id 
				AND cpv.province_id = cp.province_id 
				AND cpv.city_id = cp.city_id
				AND cp.product_id = v_channel_product_id);
				
				
				SET v_tran_type = 11;
			END IF;
			
			#获取监控随机数
			SET v_rand_id = ROUND(4*RAND()+1);
			
			#订单监控(下单失败)
			IF v_tran_type = 11 THEN
				CALL p_monitor_order_channel(v_channel_id,2,v_rand_id,v_order_date,v_discount_price);
			ELSEIF v_tran_type = 12 THEN
				CALL p_monitor_order_channel(v_back_channel_id,2,v_rand_id,v_order_date,v_discount_price);
			END IF;
			CALL p_monitor_order_province(v_province_id,2,v_rand_id,v_order_date,v_discount_price);
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,v_tran_type tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
			FROM t_flow_order_pre
			WHERE order_id = p_order_id;
		END IF;
	IF t_error = 1 THEN 		
		SET p_out_flag = 0;		
		ROLLBACK; 		
		#记录订单日志
		SET v_end_time = CURRENT_TIMESTAMP(6);
		INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
		SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,10 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
		FROM t_flow_order_pre
		WHERE order_id = p_order_id;
	ELSE  	
		IF v_proxy_id IS NOT NULL THEN
			SET p_out_flag = 1;#未找到订单
		ELSE
			SET p_out_flag = -1;
		END IF;
		
		COMMIT;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_fail_order_cache
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_fail_order_cache`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_fail_order_cache`(
IN p_order_id BIGINT
,OUT p_out_flag INT)
BEGIN 
	/*
	author		cxw
	date		2016/06/30
	desc		处理失败缓存订单
	*/
	#订单ID
	DECLARE v_order_id BIGINT;
	#订单用户类型 1代理商 2企业
	DECLARE v_order_user_type INT;
	#用户的代理商ID
	DECLARE v_proxy_id BIGINT;
	#用户的企业ID
	DECLARE v_enterprise_id BIGINT;
	#订单折后金额
	DECLARE v_discount_price DECIMAL(11,3);
	#订单的标准价格
	DECLARE v_price DECIMAL(11,3);
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#主通道ID
	DECLARE v_channel_id BIGINT;
	#备通道ID
	DECLARE v_back_channel_id BIGINT;
	#主通道帐户ID
	DECLARE v_account_id BIGINT;
	#备通道帐户ID
	DECLARE v_back_account_id BIGINT;
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(11,3);
	DECLARE v_url VARCHAR(200);
	#订单NUMID
	DECLARE v_orderno_id VARCHAR(50);
	#用户ID
	DECLARE v_user_id BIGINT;
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT;	
	#省份iD
	DECLARE v_province_id INT;
	DECLARE v_content VARCHAR(255);
	DECLARE v_order_code VARCHAR(50);
	DECLARE v_mobile VARCHAR(20);
	DECLARE v_count INT;
	#订单日期
	DECLARE v_order_date DATETIME(6);
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	#监控随机数
	DECLARE v_rand_id INT;
	
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	SELECT COUNT(*)
	INTO v_count
	FROM t_flow_order_cache op
	WHERE op.order_id = p_order_id;
	#开始事务
	START TRANSACTION;
		IF v_count > 0 THEN
			#获取订单数据
			SELECT op.user_type order_user_type,op.proxy_id,op.enterprise_id,op.discount_price,op.price
			,top_discount,back_top_discount,channel_id,back_channel_id,url,province_id,order_code,mobile,order_date,
			op.orderno_id
			INTO v_order_user_type,v_proxy_id,v_enterprise_id,v_discount_price,v_price
			,v_top_discount,v_back_top_discount,v_channel_id,v_back_channel_id,v_url,v_province_id,v_order_code,v_mobile,v_order_date,
			v_orderno_id
			FROM t_flow_order_cache op
			WHERE op.order_id = p_order_id FOR UPDATE;		
					
			#SELECT v_back_channel_id,v_channel_id,2,v_order_date,v_discount_price,v_province_id;
			#获取通道帐户ID
			SELECT account_id 
			INTO v_account_id
			FROM t_flow_channel 
			WHERE channel_id = v_channel_id;
			
			SELECT account_id 
			INTO v_back_account_id
			FROM t_flow_channel 
			WHERE channel_id = v_back_channel_id;
			#select v_account_id,v_back_account_id;
			#select v_order_user_type;
			#更新帐户余额相关金额
			IF v_order_user_type = 1 THEN #代理商	
				#获取代理商用户ID
				SELECT user_id
				INTO v_user_id 	
				FROM t_flow_sys_user 
				WHERE proxy_id = v_proxy_id 
				AND is_manager = 1 LIMIT 1;
				#获得上级代理商ID
				SELECT p.top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_proxy p
				WHERE p.proxy_id = v_proxy_id;
				#获取代理商帐户余额
				SELECT pa.account_balance
				INTO v_account_balance
				FROM t_flow_proxy_account pa
				WHERE pa.proxy_id = v_proxy_id FOR UPDATE;
				#select v_proxy_id;
				#更新代理商金额
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.cache_credit = pa.cache_credit + v_discount_price
				WHERE pa.proxy_id = v_proxy_id;	
			ELSEIF v_order_user_type = 2 THEN #企业
				#获取企业用户ID
				SELECT user_id 
				INTO v_user_id 
				FROM t_flow_sys_user 
				WHERE enterprise_id = v_enterprise_id
				AND is_manager = 1 LIMIT 1;
				#获得上级代理商ID
				SELECT p.top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_enterprise p
				WHERE p.enterprise_id = v_enterprise_id;
				#获取企业余额	
				SELECT ea.account_balance
				INTO v_account_balance
				FROM t_flow_enterprise_account ea
				WHERE ea.enterprise_id = v_enterprise_id FOR UPDATE;
				#更新企业余额
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.cache_credit = ea.cache_credit + v_discount_price
				WHERE ea.enterprise_id = v_enterprise_id;
			END IF;	
			#如果订单数据已处理
			#缓存订单数据转移		
			INSERT INTO t_flow_order(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type
			,back_content,back_fail_desc
			,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
			SELECT order_id order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,6 order_status,success_channel_product_id,source_type
			,CONCAT('->充值失败:',NOW()) back_content,'充值失败' back_fail_desc
			,NOW() complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id
			FROM t_flow_order_cache op 
			WHERE op.order_id = p_order_id;	
			
			#获得订单ID 
			SET v_order_id = p_order_id;
			#select CONCAT('v_order_id =',v_order_id);
			
			#订单回调
			IF v_url IS NOT NULL AND LENGTH(v_url) > 0 THEN
				SET v_content = CONCAT('{"respCode":"0003","respMsg":"\u5145\u503c\u6210\u529f","orderID":"'
				,v_order_code,'","phoneNo":"',v_mobile,'"}');
				INSERT INTO t_flow_order_callback(order_id,url,content,rece_content,times,`status`,end_date,channel_id,back_channel_id,orderno_id,order_status,final_channel_id)
				VALUES(v_order_id,v_url,v_content,NULL,0,0,NULL,v_channel_id,v_back_channel_id,v_orderno_id,6,v_back_channel_id);
				#SELECT CONCAT('callback_id =',@@IDENTITY);
			END IF;
			#记录流水
			/*		
			INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
			,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
			,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
			VALUES(v_discount_price,v_account_balance,0
			,7,2,NOW(6),'订单失败自动退款',v_user_id,NOW(6),v_order_user_type,IFNULL(v_proxy_id,0),IFNULL(v_enterprise_id,0)
			,1,v_top_proxy_id,0,v_order_id,NULL);
			*/
			#SELECT CONCAT('record_id =',@@IDENTITY);
			#select v_back_channel_id,v_channel_id,2,v_order_date,v_discount_price,v_province_id;
			
			#获取监控随机数
			SET v_rand_id = ROUND(4*RAND()+1);
			
			#监控订单（主通失败次数）
			CALL p_monitor_order_channel(v_channel_id,2,v_rand_id,v_order_date,v_discount_price);
			#监控订单（备通道失败次数）
			CALL p_monitor_order_channel(v_back_channel_id,2,v_rand_id,v_order_date,v_discount_price);
			#监控订单（省份）
			CALL p_monitor_order_province(v_province_id,2,v_rand_id,v_order_date,v_discount_price);
			
			#SELECT v_back_channel_id,v_channel_id,2,v_order_date,v_discount_price,v_province_id;
			
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,61 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
			FROM t_flow_order_pre
			WHERE order_id = v_order_id;
			#预处理订单删除
			DELETE FROM t_flow_order_cache WHERE order_id = p_order_id;
			#select 1;
		END IF;	
		
	IF t_error = 1 THEN 
		#回滚输出标志
		SET p_out_flag = 0;
		#select 0;
		#订单回滚			
		ROLLBACK;  
		#记录订单日志
		SET v_end_time = CURRENT_TIMESTAMP(6);
		INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
		SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,60 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
		FROM t_flow_order_pre
		WHERE order_id = p_order_id;
	ELSE  	
		#成功输出标志
		SET p_out_flag = 1;
		#SELECT 1;
		#提交
		COMMIT;  
	END IF;	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_fail_order_channel_cache
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_fail_order_channel_cache`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_fail_order_channel_cache`(
IN p_order_id BIGINT
,OUT p_out_flag INT)
BEGIN 
	/*
	author		lk
	date		2016/10/24
	desc		处理失败通道缓存订单
	*/
	#订单ID
	DECLARE v_order_id BIGINT;
	#订单用户类型 1代理商 2企业
	DECLARE v_order_user_type INT;
	#用户的代理商ID
	DECLARE v_proxy_id BIGINT;
	#用户的企业ID
	DECLARE v_enterprise_id BIGINT;
	#订单折后金额
	DECLARE v_discount_price DECIMAL(11,3);
	#订单的标准价格
	DECLARE v_price DECIMAL(11,3);
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#主通道ID
	DECLARE v_channel_id BIGINT;
	#备通道ID
	DECLARE v_back_channel_id BIGINT;
	#主通道帐户ID
	DECLARE v_account_id BIGINT;
	#备通道帐户ID
	DECLARE v_back_account_id BIGINT;
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(11,3);
	DECLARE v_url VARCHAR(200);
	#订单NUMID
	DECLARE v_orderno_id VARCHAR(50);
	#用户ID
	DECLARE v_user_id BIGINT;
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT;	
	#省份iD
	DECLARE v_province_id INT;
	DECLARE v_content VARCHAR(255);
	DECLARE v_order_code VARCHAR(50);
	DECLARE v_mobile VARCHAR(20);
	DECLARE v_count INT;
	#订单日期
	DECLARE v_order_date DATETIME(6);
	#缓存类型 0：通道缓存 1：订单缓存
	DECLARE v_cache_type INT;
	
	#事务处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	#监控随机数
	DECLARE v_rand_id INT;
	
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	SELECT COUNT(*)
	INTO v_count
	FROM t_flow_order_channel_cache op
	WHERE op.order_id = p_order_id;
	#开始事务
	START TRANSACTION;
		IF v_count > 0 THEN
			#获取订单数据
			SELECT op.user_type order_user_type,op.proxy_id,op.enterprise_id,op.discount_price,op.price
			,top_discount,back_top_discount,channel_id,back_channel_id,url,province_id,order_code,mobile,order_date,
			op.orderno_id
			INTO v_order_user_type,v_proxy_id,v_enterprise_id,v_discount_price,v_price
			,v_top_discount,v_back_top_discount,v_channel_id,v_back_channel_id,v_url,v_province_id,v_order_code,v_mobile,v_order_date,
			v_orderno_id
			FROM t_flow_order_channel_cache op
			WHERE op.order_id = p_order_id FOR UPDATE;		
					
			#SELECT v_back_channel_id,v_channel_id,2,v_order_date,v_discount_price,v_province_id;
			#获取通道帐户ID
			SELECT account_id 
			INTO v_account_id
			FROM t_flow_channel 
			WHERE channel_id = v_channel_id;
			
			SELECT account_id 
			INTO v_back_account_id
			FROM t_flow_channel 
			WHERE channel_id = v_back_channel_id;
			#select v_account_id,v_back_account_id;
			#select v_order_user_type;
			#更新帐户余额相关金额
			IF v_order_user_type = 1 THEN #代理商	
				#获取代理商用户ID
				SELECT user_id
				INTO v_user_id 	
				FROM t_flow_sys_user 
				WHERE proxy_id = v_proxy_id 
				AND is_manager = 1 LIMIT 1;
				#获得上级代理商ID
				SELECT p.top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_proxy p
				WHERE p.proxy_id = v_proxy_id;
				#获取代理商帐户余额
				SELECT pa.account_balance
				INTO v_account_balance
				FROM t_flow_proxy_account pa
				WHERE pa.proxy_id = v_proxy_id FOR UPDATE;
				#select v_proxy_id;
				#更新代理商金额
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.channel_cache_credit = pa.channel_cache_credit + v_discount_price
				WHERE pa.proxy_id = v_proxy_id;	
				
			ELSEIF v_order_user_type = 2 THEN #企业
				#获取企业用户ID
				SELECT user_id 
				INTO v_user_id 
				FROM t_flow_sys_user 
				WHERE enterprise_id = v_enterprise_id
				AND is_manager = 1 LIMIT 1;
				#获得上级代理商ID
				SELECT p.top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_enterprise p
				WHERE p.enterprise_id = v_enterprise_id;
				#获取企业余额	
				SELECT ea.account_balance
				INTO v_account_balance
				FROM t_flow_enterprise_account ea
				WHERE ea.enterprise_id = v_enterprise_id FOR UPDATE;
				#更新企业余额
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.channel_cache_credit = ea.channel_cache_credit + v_discount_price
				WHERE ea.enterprise_id = v_enterprise_id;
			END IF;	
			#如果订单数据已处理
			#缓存订单数据转移		
			INSERT INTO t_flow_order(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type
			,back_content,back_fail_desc
			,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
			SELECT order_id order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,6 order_status,success_channel_product_id,source_type
			,CONCAT('->充值失败:',NOW()) back_content,'充值失败' back_fail_desc
			,NOW() complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id
			FROM t_flow_order_channel_cache op 
			WHERE op.order_id = p_order_id;	
			
			#获得订单ID 
			SET v_order_id = p_order_id;
			#select CONCAT('v_order_id =',v_order_id);
			
			#订单回调
			IF v_url IS NOT NULL AND LENGTH(v_url) > 0 THEN
				SET v_content = CONCAT('{"respCode":"0003","respMsg":"\u5145\u503c\u6210\u529f","orderID":"'
				,v_order_code,'","phoneNo":"',v_mobile,'"}');
				INSERT INTO t_flow_order_callback(order_id,url,content,rece_content,times,`status`,end_date,channel_id,back_channel_id,orderno_id,order_status,final_channel_id)
				VALUES(v_order_id,v_url,v_content,NULL,0,0,NULL,v_channel_id,v_back_channel_id,v_orderno_id,6,v_back_channel_id);
				#SELECT CONCAT('callback_id =',@@IDENTITY);
			END IF;
			#记录流水
			/*		
			INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
			,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
			,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
			VALUES(v_discount_price,v_account_balance,0
			,7,2,NOW(6),'订单失败自动退款',v_user_id,NOW(6),v_order_user_type,IFNULL(v_proxy_id,0),IFNULL(v_enterprise_id,0)
			,1,v_top_proxy_id,0,v_order_id,NULL);
			*/
			#SELECT CONCAT('record_id =',@@IDENTITY);
			#select v_back_channel_id,v_channel_id,2,v_order_date,v_discount_price,v_province_id;
			
			#获取监控随机数
			SET v_rand_id = ROUND(4*RAND()+1);
			
			#监控订单（主通失败次数）
			CALL p_monitor_order_channel(v_channel_id,2,v_rand_id,v_order_date,v_discount_price);
			#监控订单（备通道失败次数）
			CALL p_monitor_order_channel(v_back_channel_id,2,v_rand_id,v_order_date,v_discount_price);
			#监控订单（省份）
			CALL p_monitor_order_province(v_province_id,2,v_rand_id,v_order_date,v_discount_price);
			
			#SELECT v_back_channel_id,v_channel_id,2,v_order_date,v_discount_price,v_province_id;
			
			#记录订单日志
			SET v_end_time = CURRENT_TIMESTAMP(6);
			INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
			SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,81 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
			FROM t_flow_order_pre
			WHERE order_id = v_order_id;
			#预处理订单删除
			DELETE FROM t_flow_order_channel_cache WHERE order_id = p_order_id;
			#select 1;
		END IF;	
		
	IF t_error = 1 THEN 
		#回滚输出标志
		SET p_out_flag = 0;
		#select 0;
		#订单回滚			
		ROLLBACK;  
		#记录订单日志
		SET v_end_time = CURRENT_TIMESTAMP(6);
		INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
		SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,80 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
		FROM t_flow_order_pre
		WHERE order_id = p_order_id;
	ELSE  	
		#成功输出标志
		SET p_out_flag = 1;
		#SELECT 1;
		#提交
		COMMIT;  
	END IF;	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_noresponse_order
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_noresponse_order`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_noresponse_order`(
IN p_order_id BIGINT
,OUT p_out_flag INT)
BEGIN 
	/*
	author		lhh
	date		2016/09/28
	desc		处理失败订单
	*/
		#订单用户类型 1 代理商 2 企业
	DECLARE v_order_user_type INT;
	#代理商ID
	DECLARE v_proxy_id BIGINT;
	#企业ID
	DECLARE v_enterprise_id BIGINT;
	#折后价
	DECLARE v_discount_price DECIMAL(11,3);
	#单价
	DECLARE v_price DECIMAL(11,3);
	#订单状态
	DECLARE v_order_status INT;
	#备用通道ID
	DECLARE v_back_channel_id BIGINT;
	#主通道ID
	DECLARE v_channel_id BIGINT;
	#主通道产品ID
	DECLARE v_channel_product_id BIGINT;	
	#备用通道产品ID
	DECLARE v_back_channel_product_id BIGINT;	
	#用户帐号ID
	DECLARE v_account_id BIGINT;	
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(11,3);
	#用户ID
	DECLARE v_user_id BIGINT;
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT;	
	#上游折口
	DECLARE v_top_discount DECIMAL(5,3);
	#备用通道折口
	DECLARE v_back_top_discount DECIMAL(5,3);
	#订单日志效果类型
	DECLARE v_tran_type INT;
	#订单日期
	DECLARE v_order_date DATETIME(6);
	#手机号省份iD
	DECLARE v_province_id INT;
	
	DECLARE v_content VARCHAR(255);
	#手机号市ID
	DECLARE v_city_id INT;
	DECLARE v_proxy_enterprise_id BIGINT;
	DECLARE v_is_discount INT;
	DECLARE v_operator_id INT;
	#事务异常处理标志
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_start_time TIMESTAMP(6);
	DECLARE v_end_time TIMESTAMP(6);
	DECLARE v_count INTEGER DEFAULT 0; 
	DECLARE v_order_code VARCHAR(50);
	DECLARE v_mobile VARCHAR(20);
	DECLARE v_url VARCHAR(200);
	#监控随机数
	DECLARE v_rand_id INT;
	
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1; 
	
	SET v_start_time = CURRENT_TIMESTAMP(6);
	
	#开始事务
	START TRANSACTION;
	#获取订单数据
		SELECT op.user_type order_user_type,op.proxy_id,op.enterprise_id,op.discount_price,op.price,op.order_status,op.back_channel_id
		,op.channel_product_id,op.back_channel_product_id,op.channel_id,op.top_discount,op.back_top_discount,op.order_date,op.province_id
		,op.city_id,op.operator_id,op.order_code,op.mobile
		INTO v_order_user_type,v_proxy_id,v_enterprise_id,v_discount_price,v_price,v_order_status,v_back_channel_id
		,v_channel_product_id,v_back_channel_product_id,v_channel_id,v_top_discount,v_back_top_discount,v_order_date,v_province_id
		,v_city_id,v_operator_id,v_order_code,v_mobile
		FROM t_flow_order_pre op
		WHERE op.order_id = p_order_id FOR UPDATE;
		
		SELECT c.url 
		INTO v_url
		FROM t_flow_order_callback c
		WHERE c.order_id = p_order_id;
		
		IF v_order_user_type = 1 THEN #代理商	
			SET v_proxy_enterprise_id = v_proxy_id;
		ELSEIF v_order_user_type = 2 THEN #企业
			SET v_proxy_enterprise_id = v_enterprise_id;
		END IF;
		IF v_order_status IN (3,4) THEN	#备用通道失败	
			IF v_order_user_type = 1 THEN #代理商	
				#获取代理商用户ID
				SELECT user_id 	
				INTO v_user_id 	
				FROM t_flow_sys_user 
				WHERE proxy_id = v_proxy_id 
				AND is_manager = 1 LIMIT 1;
				#获取代理商上级代理商ID
				SELECT top_proxy_id 
				INTO v_top_proxy_id	
				FROM t_flow_proxy 
				WHERE proxy_id = v_proxy_id;
				#获取代理商帐户余额
				SELECT pa.account_balance 
				INTO v_account_balance
				FROM t_flow_proxy_account pa
				WHERE pa.proxy_id = v_proxy_id 
				FOR UPDATE;
				#更新代理商相关帐户金额
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.freeze_money = pa.freeze_money - v_discount_price
				,pa.account_balance = pa.account_balance + v_discount_price
				WHERE pa.proxy_id = v_proxy_id;	
				SET v_proxy_enterprise_id = v_proxy_id;
			ELSEIF v_order_user_type = 2 THEN #企业
				#获取企业用户ID
				SELECT user_id 
				INTO v_user_id 
				FROM t_flow_sys_user 
				WHERE enterprise_id = v_enterprise_id
				AND is_manager = 1 LIMIT 1;	
				#获取企业所属代理商ID
				SELECT top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_enterprise
				WHERE enterprise_id = v_enterprise_id;
				#获取企业帐户余额
				SELECT ea.account_balance
				INTO v_account_balance
				FROM t_flow_enterprise_account ea
				WHERE ea.enterprise_id = v_enterprise_id
				FOR UPDATE;
				#更新企业帐户相关余额
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.freeze_money = ea.freeze_money - v_discount_price
				,ea.account_balance = ea.account_balance + v_discount_price
				WHERE ea.enterprise_id = v_enterprise_id;
				SET v_proxy_enterprise_id = v_enterprise_id;
			END IF;
			
			#更新预处理订单状态相关信息
			
			 IF v_order_status IN (3) THEN
				UPDATE t_flow_order_pre op
				SET op.order_status = 6
				,op.back_content = CONCAT(op.back_content,'->备通道提交超时置失败')
				,op.back_fail_desc = '充值失败'
				,op.complete_time = NOW()
				,op.is_using = 2
				WHERE op.order_id = p_order_id;		
			
				#更新回调信息
				UPDATE t_flow_order_callback oc
				SET oc.content = '充值失败',oc.order_status = 6
				WHERE oc.order_id = p_order_id;
			ELSE 
				UPDATE t_flow_order_pre op
				SET op.order_status = 6
				,op.back_content = CONCAT(op.back_content,'->备通道充值超时置失败')
				,op.back_fail_desc = '充值失败'
				,op.complete_time = NOW()
				,op.is_using = 2
				WHERE op.order_id = p_order_id;		
			
				#更新回调信息
				UPDATE t_flow_order_callback oc
				SET oc.content = '充值失败',oc.order_status = 6
				WHERE oc.order_id = p_order_id;
			
			END IF ;
			#通道帐户金额，多个通道对应一个账户 by cxw 201607606
			#更新通道相关金额
			UPDATE t_flow_channel_account
			SET surplus_money = surplus_money + ROUND(v_price*v_back_top_discount,3)
			WHERE account_id IN (SELECT account_id FROM t_flow_channel WHERE channel_id = v_back_channel_id);
			#更新通道省份相关金额
			
			UPDATE t_flow_channel_province cpv
			SET cpv.province_money = cpv.province_money + ROUND(v_price*v_back_top_discount,3)
			WHERE EXISTS(SELECT 1 FROM t_flow_channel_product cp 
			WHERE cpv.channel_id = cp.channel_id 
			AND cpv.province_id = cp.province_id 
			AND cpv.city_id = cp.city_id
			AND cp.product_id = v_back_channel_product_id);	
			
			
			#记录流水
			INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
			,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
			,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
			VALUES(v_discount_price,v_account_balance,v_account_balance+v_discount_price
			,7,1,NOW(),'备充值失败',v_user_id,NOW(),v_order_user_type,v_proxy_id,v_enterprise_id
			,1,v_top_proxy_id,0,p_order_id,NULL);				
			#订单处理结束并生成订单
			INSERT INTO t_flow_order(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
			SELECT order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id
			FROM t_flow_order_pre op 
			WHERE op.order_id = p_order_id;	
							
			#订单回调
			IF v_url IS NOT NULL AND LENGTH(v_url) > 0 THEN
				SET v_content = CONCAT('{"respCode":"0003","respMsg":"\u5145\u503c\u6210\u529f","orderID":"'
				,v_order_code,'","phoneNo":"',v_mobile,'"}');				
				UPDATE t_flow_order_callback 
				SET content=v_content,order_status=6
				WHERE order_id=p_order_id;
			END IF;
			
			SET v_tran_type = 12;
		ELSEIF v_order_status IN (0,1) THEN #主通道失败
						
			IF v_order_user_type = 1 THEN #代理商	
				#获取代理商用户ID
				SELECT user_id 	
				INTO v_user_id 	
				FROM t_flow_sys_user 
				WHERE proxy_id = v_proxy_id 
				AND is_manager = 1 LIMIT 1;
				#获取代理商上级代理商ID
				SELECT top_proxy_id 
				INTO v_top_proxy_id	
				FROM t_flow_proxy 
				WHERE proxy_id = v_proxy_id;
				#获取代理商帐户余额
				SELECT pa.account_balance 
				INTO v_account_balance
				FROM t_flow_proxy_account pa
				WHERE pa.proxy_id = v_proxy_id 
				FOR UPDATE;
				#更新代理商相关帐户金额
				UPDATE LOW_PRIORITY t_flow_proxy_account pa
				SET pa.freeze_money = pa.freeze_money - v_discount_price
				,pa.account_balance = pa.account_balance + v_discount_price
				WHERE pa.proxy_id = v_proxy_id;	
				SET v_proxy_enterprise_id = v_proxy_id;
			ELSEIF v_order_user_type = 2 THEN #企业
				#获取企业用户ID
				SELECT user_id 
				INTO v_user_id 
				FROM t_flow_sys_user 
				WHERE enterprise_id = v_enterprise_id
				AND is_manager = 1 LIMIT 1;	
				#获取企业所属代理商ID
				SELECT top_proxy_id
				INTO v_top_proxy_id
				FROM t_flow_enterprise
				WHERE enterprise_id = v_enterprise_id;
				#获取企业帐户余额
				SELECT ea.account_balance
				INTO v_account_balance
				FROM t_flow_enterprise_account ea
				WHERE ea.enterprise_id = v_enterprise_id
				FOR UPDATE;
				#更新企业帐户相关余额
				UPDATE LOW_PRIORITY t_flow_enterprise_account ea
				SET ea.freeze_money = ea.freeze_money - v_discount_price
				,ea.account_balance = ea.account_balance + v_discount_price
				WHERE ea.enterprise_id = v_enterprise_id;
				SET v_proxy_enterprise_id = v_enterprise_id;
			END IF;
			
			#更新预处理订单状态相关信息
			IF v_order_status IN (0) THEN
				UPDATE t_flow_order_pre op
				SET op.order_status = 6
				-- ,op.back_content = CONCAT(op.back_content,'->主通道提交超时置失败')
				,op.back_content = '主通道提交超时置失败'
				,op.back_fail_desc = '充值失败'
				,op.complete_time = NOW()
				,op.is_using = 2
				WHERE op.order_id = p_order_id;		
			
				#更新回调信息
				UPDATE t_flow_order_callback oc
				SET oc.content = '充值失败',oc.order_status = 6
				WHERE oc.order_id = p_order_id;
			ELSE
				UPDATE t_flow_order_pre op
				SET op.order_status = 6
				,op.back_content = CONCAT(op.back_content,'->主通道充值超时置失败')
				,op.back_fail_desc = '充值失败'
				,op.complete_time = NOW()
				,op.is_using = 2
				WHERE op.order_id = p_order_id;		
			
				#更新回调信息
				UPDATE t_flow_order_callback oc
				SET oc.content = '充值失败',oc.order_status = 6
				WHERE oc.order_id = p_order_id;
			END IF;
			
			
			#通道帐户金额，多个通道对应一个账户 by cxw 201607606
			#更新通道帐户金额
			UPDATE t_flow_channel_account
			SET surplus_money = surplus_money + ROUND(v_price*v_top_discount,3)
			#WHERE channel_id = v_channel_id;
			WHERE account_id IN(SELECT account_id FROM t_flow_channel WHERE channel_id = v_channel_id);		
			#更新通道省份帐户金额
			
			UPDATE t_flow_channel_province cpv
			SET cpv.province_money = cpv.province_money + ROUND(v_price*v_top_discount,3)
			WHERE EXISTS(SELECT 1 FROM t_flow_channel_product cp 
			WHERE cpv.channel_id = cp.channel_id 
			AND cpv.province_id = cp.province_id 
			AND cpv.city_id = cp.city_id
			AND cp.product_id = v_channel_product_id);
			
			#记录流水
			INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
			,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
			,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
			VALUES(v_discount_price,v_account_balance,v_account_balance+v_discount_price
			,7,1,NOW(),'主充值失败',v_user_id,NOW(),v_order_user_type,v_proxy_id,v_enterprise_id
			,1,v_top_proxy_id,0,p_order_id,NULL);				
			#订单处理结束并生成订单
			INSERT INTO t_flow_order(order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id)
			SELECT order_id,order_code,channel_order_code,user_type,proxy_id,enterprise_id,operator_id
			,channel_id,back_channel_id,channel_product_id,back_channel_product_id,province_id,mobile,price,discount_price
			,pay_type,is_payment,order_date,order_effect_date,order_status,success_channel_product_id,source_type,back_content
			,back_fail_desc,complete_time,profit_case,is_profit,is_using,top_discount,one_proxy_discount,profit,one_proxy_id,orderno_id
			,back_top_discount,proxy_name,enterprise_name,one_proxy_name,refund_id,top_rebate_discount,back_top_rebate_discount
			,last_update_time,channel_code,back_channel_code,product_province_id,product_name,province_name,city_name,city_id
			FROM t_flow_order_pre op 
			WHERE op.order_id = p_order_id;		
	
			#订单回调
			IF v_url IS NOT NULL AND LENGTH(v_url) > 0 THEN
				SET v_content = CONCAT('{"respCode":"0003","respMsg":"\u5145\u503c\u6210\u529f","orderID":"'
				,v_order_code,'","phoneNo":"',v_mobile,'"}');
				UPDATE t_flow_order_callback 
				SET content=v_content,order_status=6
				,final_channel_id = v_back_channel_id
				WHERE order_id=p_order_id;
			END IF;
		
			SET v_tran_type = 11;
		END IF;
		#获取监控随机数
		SET v_rand_id = ROUND(4*RAND()+1);
			
		#订单监控(下单失败)
		IF v_tran_type = 11 THEN
			CALL p_monitor_order_channel(v_channel_id,2,v_rand_id,v_order_date,v_discount_price);
		ELSEIF v_tran_type = 12 THEN
			CALL p_monitor_order_channel(v_back_channel_id,2,v_rand_id,v_order_date,v_discount_price);
		END IF;
		CALL p_monitor_order_province(v_province_id,2,v_rand_id,v_order_date,v_discount_price);
		#记录订单日志
		SET v_end_time = CURRENT_TIMESTAMP(6);
		INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
		SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,71 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
		FROM t_flow_order_pre
		WHERE order_id = p_order_id;
		DELETE FROM t_flow_order_pre WHERE order_id = p_order_id;
	IF t_error = 1 THEN 		
		SET p_out_flag = 0;		
		ROLLBACK; 		
		#记录订单日志
		SET v_end_time = CURRENT_TIMESTAMP(6);
		INSERT INTO t_flow_order_tran_log(order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,tran_type,handle_status,create_date,end_date)
		SELECT order_id,user_type,proxy_id,enterprise_id,price,discount_price,order_status,70 tran_type,0 handle_status,v_start_time create_date,v_end_time end_date
		FROM t_flow_order_pre
		WHERE order_id = p_order_id;
	ELSE  	
		SET p_out_flag = 1;
		COMMIT;
	END IF;
	
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for p_tran_order_profit
-- ----------------------------
DROP PROCEDURE IF EXISTS `p_tran_order_profit`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `p_tran_order_profit`(
  IN p_start_time DATETIME
  ,IN p_end_time DATETIME
)
BEGIN  	
	/*
	author		cxw
	date		2015/05/24
	desc		订单返利		
	*/
	DECLARE v_log_id BIGINT;
	DECLARE t_error INTEGER DEFAULT 0; 
	DECLARE v_done INT DEFAULT 0; 
	DECLARE v_profit_case TEXT DEFAULT '';
	DECLARE v_profit_count INT;
	DECLARE v_profit VARCHAR(100);
	DECLARE v_profit_idx INT DEFAULT 1;
	DECLARE v_delim VARCHAR(3) DEFAULT '],[';
	DECLARE v_user_type INT;
	DECLARE v_proxy_enterprise_id BIGINT;
	#返利金额;单位:元
	DECLARE v_profit_price DECIMAL(11,3);
	#自身折扣
	DECLARE v_self_discount DECIMAL(5,3);
	#下级折扣
	DECLARE v_lower_discount DECIMAL(5,3);
	#订单ID
	DECLARE v_order_id BIGINT;
	#用户帐户余额
	DECLARE v_account_balance DECIMAL(11,3);
	#用户ID
	DECLARE v_user_id BIGINT;
	#订单代理商ID
	#DECLARE v_order_proxy_id BIGINT;
	#企业	
	#declare v_enterprise_id bigint;
	DECLARE v_obj_user_type INT;
	DECLARE v_obj_proxy_id BIGINT;
	DECLARE v_obj_enterprise_id BIGINT;
	
	
	#需要返利
	DECLARE v_order_profit_cur CURSOR FOR 
	SELECT o.order_id,o.profit_case,o.proxy_id,o.enterprise_id,o.user_type
	FROM t_flow_order o
	WHERE o.order_status IN (2,5) 
	AND o.is_profit = 0  
	AND o.profit_case <> '[]'
	AND IFNULL(o.refund_id,0) = 0 #没有退款的成功订单进行返利
	AND o.complete_time BETWEEN p_start_time AND p_end_time;
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1; 
	#申明事务处理错误标志
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET t_error=1;
	
	START TRANSACTION;
		#开始时间
		INSERT INTO t_flow_sys_event_log(log_type ,event_title,  log_desc ,log_status ,start_time,end_time )
		VALUES (2,'p_tran_order_profit','代理商返利',1,NOW(6),NULL);
		SET v_log_id = @@IDENTITY;
		#不需要返利
		UPDATE t_flow_order o
		SET is_profit = 2
		WHERE o.order_status IN (0,1,3,4,6) 
		AND o.is_profit = 0 		
		AND o.complete_time BETWEEN p_start_time AND p_end_time;
		
		UPDATE t_flow_order o
		SET is_profit = 2
		WHERE o.profit_case = '[]'
		AND o.order_status IN (2,5) 
		AND o.is_profit = 0
		AND o.complete_time BETWEEN p_start_time AND p_end_time;
		#进行返利		
		OPEN v_order_profit_cur;
		FETCH v_order_profit_cur INTO v_order_id,v_profit_case,v_obj_proxy_id,v_obj_enterprise_id,v_obj_user_type;
		WHILE v_done = 0 DO 
			#select v_profit_case;
			SET v_profit_count = f_str_split_count(v_profit_case,v_delim);
			#select v_profit_count;
			
			SET v_profit_idx=1;
			WHILE v_profit_idx <= v_profit_count DO
				#insert into t1(filed) values(i);
				SET v_profit = f_str_split(v_profit_case,v_delim,v_profit_idx);
				SET v_profit = REPLACE(REPLACE(v_profit,'[[',''),']]','');
				SET v_profit_idx = v_profit_idx+1;
				SET v_user_type = f_str_split(v_profit,',',1);
				SET v_proxy_enterprise_id = f_str_split(v_profit,',',2);
				SET v_profit_price = f_str_split(v_profit,',',3);
				SET v_self_discount = f_str_split(v_profit,',',4);
				SET v_lower_discount = f_str_split(v_profit,',',5);
				IF v_user_type = 1 THEN #更新返利，目前只有代理商
					#获取代理商用户ID
					SELECT user_id
					INTO v_user_id 	
					FROM t_flow_sys_user 
					WHERE proxy_id = v_proxy_enterprise_id 
					AND is_manager = 1 LIMIT 1;
					#获得代理商余额
					SELECT pa.account_balance
					INTO v_account_balance
					FROM t_flow_proxy_account pa					
					WHERE pa.proxy_id = v_proxy_enterprise_id;
					#更新代理商帐户余额
					UPDATE t_flow_proxy_account pa
					SET pa.account_balance = pa.account_balance + v_profit_price
					WHERE pa.proxy_id = v_proxy_enterprise_id;
				END IF;
				#新增流水记录
				INSERT INTO t_flow_account_record(operater_price,operater_before_balance,operater_after_balance
				,operate_type,balance_type,record_date,remark,user_id,operation_date,user_type,proxy_id,enterprise_id
				,obj_user_type,obj_proxy_id,obj_enterprise_id,order_id,device_name)
				VALUES(v_profit_price,v_account_balance,v_account_balance+v_profit_price
				,6,1,NOW(6),'订单分红',v_user_id,NOW(6),1,v_proxy_enterprise_id,0
				,v_obj_user_type,v_obj_proxy_id,v_obj_enterprise_id,v_order_id,NULL);
				
				SET v_obj_user_type = v_user_type;
				SET v_obj_proxy_id = v_proxy_enterprise_id;
				SET v_obj_enterprise_id = 0;
				
			END WHILE;
			#更新返利状态
			UPDATE t_flow_order o
			SET o.is_profit = 1
			WHERE o.order_id = v_order_id;	
			
			#select v_order_id,v_profit_case;
			FETCH v_order_profit_cur INTO v_order_id,v_profit_case,v_obj_proxy_id,v_obj_enterprise_id,v_obj_user_type;
		END WHILE;
		CLOSE v_order_profit_cur; 
		
	IF t_error = 1 THEN 
		#订单回滚			
		ROLLBACK; 
		#失败时间和状态
		UPDATE t_flow_sys_event_log
		SET log_status = 0
		,end_time = NOW(6)
		WHERE log_id = v_log_id;
	ELSE 
		#提交
		COMMIT;
		#更新结束时间
		UPDATE t_flow_sys_event_log
		SET end_time = NOW(6)
		WHERE log_id = v_log_id;
	END IF;		
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_channel_discount
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_channel_discount`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_channel_discount`(
p_channel_id BIGINT
,p_operator_id INT
,p_province_id INT
,p_city_id INT
,p_user_id BIGINT
,p_user_type INT
,p_size INT
) RETURNS decimal(5,3)
BEGIN 
	DECLARE v_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_nation_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_province_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_city_discount_number DECIMAL(5,3) DEFAULT 0; 
	
	DECLARE v_attribute_id INT;
	
	SELECT a.attribute_id 
	INTO v_attribute_id
	FROM t_flow_channel a	
	WHERE a.channel_id = p_channel_id ;
	
	#流量包通道1 
	IF v_attribute_id = 2 THEN
		IF p_user_type =1 THEN
			SELECT MAX(CASE WHEN a.province_id = 1 THEN discount ELSE 0 END )
			,MAX(CASE WHEN a.province_id = p_province_id AND a.city_id = 0 THEN discount ELSE 0 END )
			,MAX(CASE WHEN a.province_id = 0 AND a.city_id = p_city_id THEN discount ELSE 0 END )
			INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
			FROM t_flow_channel_product a,t_flow_channel_user b
			WHERE a.channel_id = b.channel_id  
			AND a.channel_id = p_channel_id 
			AND a.operator_id = p_operator_id 
			AND b.proxy_id = p_user_id 
			AND a.size = p_size
			AND (a.province_id IN(1,p_province_id) OR a.city_id = p_city_id);
		ELSEIF p_user_type = 2 THEN 
			SELECT MAX(CASE WHEN a.province_id = 1 THEN discount ELSE 0 END )
			,MAX(CASE WHEN a.province_id = p_province_id AND a.city_id = 0 THEN discount ELSE 0 END )
			,MAX(CASE WHEN a.province_id = 0 AND a.city_id = p_city_id THEN discount ELSE 0 END )
			INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
			FROM t_flow_channel_product a,t_flow_channel_user b
			WHERE a.channel_id = b.channel_id 
			AND a.channel_id = p_channel_id 
			AND a.operator_id = p_operator_id 
			AND b.enterprise_id = p_user_id 
			AND a.size = p_size
			AND (a.province_id IN(1,p_province_id) OR a.city_id = p_city_id);
		END IF;
	#普通通道
	ELSEIF v_attribute_id = 1 THEN 
		SELECT MAX(CASE WHEN a.province_id = 1 THEN a.discount_number ELSE 0 END )
		,MAX(CASE WHEN a.province_id = p_province_id AND a.city_id = 0 THEN a.discount_number ELSE 0 END )
		,MAX(CASE WHEN a.province_id = 0 AND a.city_id = p_city_id THEN a.discount_number ELSE 0 END )
		INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
		FROM t_flow_channel_discount a
		WHERE a.channel_id = p_channel_id 
		AND a.operator_id = p_operator_id 
		AND (a.province_id IN(1,p_province_id) OR a.city_id = p_city_id);			
	END IF;
	#先判断市是否有折扣
	SET v_discount_number = IF(v_city_discount_number>0,v_city_discount_number,IF(v_province_discount_number>0,v_province_discount_number,v_nation_discount_number));
	
	IF v_discount_number <= 0 THEN
		SET v_discount_number = 1;
	END IF;
	
	RETURN IFNULL(v_discount_number,1);
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_channel_province_money
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_channel_province_money`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_channel_province_money`(
p_channel_id BIGINT
,p_province_id INT
,p_city_id INT
,p_range INT
) RETURNS decimal(11,3)
BEGIN 
	#通道额度设置
	DECLARE v_province_money DECIMAL(11,3) DEFAULT NULL;
	DECLARE v_province_id INT;
	SET v_province_id = CASE p_range WHEN 0 THEN 1 WHEN 1 THEN p_province_id WHEN 2 THEN 0 END;
	#select p_province_id;
	IF p_city_id > 0 THEN
		#市主通道金额(通道额度设置余额不足)
		SELECT province_money
		INTO v_province_money
		FROM t_flow_channel_province ca
		WHERE (city_id = p_city_id AND province_id = v_province_id) 
		AND channel_id = p_channel_id FOR UPDATE;
	ELSE
		SELECT province_money
		INTO v_province_money
		FROM t_flow_channel_province ca
		WHERE province_id = v_province_id 
		AND city_id = 0
		AND channel_id = p_channel_id FOR UPDATE;
	END IF;
		
	RETURN v_province_money;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_channel_rebate_discount
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_channel_rebate_discount`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_channel_rebate_discount`(
p_channel_id BIGINT
,p_operator_id INT
,p_province_id INT
,p_city_id INT
) RETURNS decimal(5,3)
BEGIN 
	DECLARE v_rebate_discount DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_nation_rebate_discount DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_province_rebate_discount DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_city_rebate_discount DECIMAL(5,3) DEFAULT 0; 
	SELECT MAX(CASE WHEN a.province_id = 1 THEN a.rebate_discoun ELSE 0 END )
	,MAX(CASE WHEN a.province_id = p_province_id AND a.city_id = 0 THEN a.rebate_discoun ELSE 0 END )
	,MAX(CASE WHEN a.province_id = 0 AND a.city_id = p_city_id THEN a.rebate_discoun ELSE 0 END )
	INTO v_nation_rebate_discount,v_province_rebate_discount,v_city_rebate_discount
	FROM t_flow_channel_discount a
	WHERE a.channel_id = p_channel_id 
	AND a.operator_id = p_operator_id 
	AND (a.province_id IN(1,p_province_id) OR a.city_id = p_city_id);			
	
	SET v_rebate_discount = IF(v_city_rebate_discount>0,v_city_rebate_discount,IF(v_province_rebate_discount>0,v_province_rebate_discount,v_nation_rebate_discount));
	
	RETURN IFNULL(v_rebate_discount,0);
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_column
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_column`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_column`(
  p_column VARCHAR(255)
  ,p_delim VARCHAR(12)
  ,p_function VARCHAR(30)
) RETURNS varchar(4000) CHARSET utf8
BEGIN 
	DECLARE v_column VARCHAR(100) DEFAULT '';
	DECLARE v_column_str VARCHAR(4000) DEFAULT '';
	DECLARE v_column_len INT;
	DECLARE v_idx INT;
	
	SET v_column_len = LENGTH(p_column) - LENGTH(REPLACE(p_column,',','')) + 1;	
	SET v_idx = 1;
	WHILE v_idx <= v_column_len DO 
		SET v_column = f_str_split(p_column,',',v_idx);
		IF p_function IS NULL THEN
			SET v_column_str = CONCAT(v_column_str,',sp.',v_column,'\r');
		ELSE
			SET v_column_str = CONCAT(v_column_str,',',p_function,'(sp.',v_column,') ',v_column,'\r');
		END IF;		
		SET v_idx = v_idx +1; 
	END WHILE; 
	RETURN SUBSTRING(v_column_str,2);
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_discount
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_discount`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_discount`(
p_object_id BIGINT
,p_user_type INT
,p_operator_id INT
,p_province_id INT
,p_city_id INT
) RETURNS decimal(5,3)
BEGIN 
	DECLARE v_discount_number DECIMAL(5,3) DEFAULT 1; 
	DECLARE v_nation_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_province_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_city_discount_number DECIMAL(5,3) DEFAULT 0; 
		
	IF p_user_type = 1 THEN
		SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
		INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
		FROM t_flow_discount d
		WHERE d.user_type = p_user_type  
		AND d.proxy_id = p_object_id 
		AND d.operator_id = p_operator_id
		AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
	ELSEIF p_user_type = 2 THEN
		SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
		INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
		FROM t_flow_discount d
		WHERE d.user_type = p_user_type  
		AND d.enterprise_id = p_object_id 
		AND d.operator_id = p_operator_id
		AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
	END IF;
	
	SET v_discount_number = IF(v_city_discount_number>0,v_city_discount_number,IF(v_province_discount_number>0,v_province_discount_number,v_nation_discount_number));
	
	IF v_discount_number = 0 THEN
		SET v_discount_number = 1;
	END IF;
	RETURN IFNULL(v_discount_number,1);
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_discount2
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_discount2`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_discount2`(
p_object_id BIGINT
,p_user_type INT
,p_operator_id INT
,p_province_id INT
,p_city_id INT
,p_discount_type INT
,p_size INT
) RETURNS decimal(5,3)
BEGIN 
	DECLARE v_discount_number DECIMAL(5,3) DEFAULT 1; 
		
	IF p_user_type = 1 THEN
		#市折扣
		IF p_discount_type = 1 THEN
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount d 
			WHERE d.user_type = p_user_type  
			AND d.proxy_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.city_id = p_city_id LIMIT 1;
		#省折扣
		ELSEIF p_discount_type = 2 THEN
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount d 
			WHERE d.user_type = p_user_type  
			AND d.proxy_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = p_province_id LIMIT 1;
		#市产品折扣
		ELSEIF p_discount_type = 3 THEN 
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount_product d 
			WHERE d.user_type = p_user_type  
			AND d.proxy_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = 0
			AND d.city_id = p_city_id 
			AND d.size = p_size LIMIT 1;
		#省产品折扣 #全国产品折扣
		ELSEIF p_discount_type = 4  THEN
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount_product d 
			WHERE d.user_type = p_user_type  
			AND d.proxy_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = p_province_id 
			AND d.city_id = 0
			AND d.size = p_size LIMIT 1;
		#全国产品折扣
		ELSEIF  p_discount_type = 5 THEN
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount_product d 
			WHERE d.user_type = p_user_type  
			AND d.proxy_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = 1 
			AND d.city_id = 0
			AND d.size = p_size LIMIT 1;
		END IF;
	ELSEIF p_user_type = 2 THEN
		IF p_discount_type = 1 THEN
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount d 
			WHERE d.user_type = p_user_type  
			AND d.enterprise_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.city_id = p_city_id LIMIT 1;
		ELSEIF p_discount_type = 2 THEN
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount d 
			WHERE d.user_type = p_user_type  
			AND d.enterprise_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = p_province_id LIMIT 1;
		#市产品折扣
		ELSEIF p_discount_type = 3 THEN 
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount_product d 
			WHERE d.user_type = p_user_type  
			AND d.enterprise_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = 0
			AND d.city_id = p_city_id 
			AND d.size = p_size LIMIT 1;
		#省产品折扣 #全国产品折扣
		ELSEIF p_discount_type = 4  THEN
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount_product d 
			WHERE d.user_type = p_user_type  
			AND d.enterprise_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = p_province_id 
			AND d.city_id = 0
			AND d.size = p_size LIMIT 1;
		#全国产品折扣
		ELSEIF p_discount_type = 5  THEN
			SELECT d.discount_number
			INTO v_discount_number
			FROM t_flow_discount_product d 
			WHERE d.user_type = p_user_type  
			AND d.enterprise_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = 1 
			AND d.city_id = 0
			AND d.size = p_size LIMIT 1;
		END IF;
	END IF;
	IF v_discount_number <= 0 THEN
		SET v_discount_number = 1;
	END IF;
	RETURN IFNULL(v_discount_number,1);
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_hashValue
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_hashValue`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_hashValue`(str TEXT) RETURNS bigint(20)
BEGIN
	
	SET @pos = 1;
	SET @hashValue = 0;
	SET @szHex = HEX(MD5(str));
	SET @size = LENGTH(@szHex);
	WHILE @pos<@size+1 DO
		SET @cCh = SUBSTRING(@szHex,@pos,2);
		SET @nCh = CAST(ASCII(UNHEX(@cCh)) AS UNSIGNED);
		SET @hashValue = 3*@hashValue + @nCh;
		SET @pos = @pos + 2;
	END WHILE;
	RETURN @hashValue;
	
    END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_increment_id
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_increment_id`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_increment_id`() RETURNS bigint(18)
BEGIN 
	DECLARE NOW, twepoch, RAND, result BIGINT(18);
	SET twepoch = 1288834974657;
	SET NOW = UNIX_TIMESTAMP(NOW(3))*1000;
	SET NOW = NOW - twepoch;
	SET NOW = NOW << 22;
	SET RAND = ROUND(RAND() * 4000000);
	SET result = NOW | RAND;
	
	RETURN result;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_is_discount
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_is_discount`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_is_discount`(
p_object_id BIGINT
,p_user_type INT
,p_operator_id INT
,p_province_id INT
,p_city_id INT
,p_size INT
) RETURNS int(11)
BEGIN 
	DECLARE v_is_discount INT DEFAULT 0;	
	IF p_user_type = 1 THEN	
		#如果有市产品折扣就取市产品折扣
		SELECT COUNT(*)
		INTO v_is_discount
		FROM t_flow_discount_product d 
		WHERE d.user_type = p_user_type  
		AND d.proxy_id = p_object_id 
		AND d.operator_id = p_operator_id
		AND d.province_id = 0
		AND d.city_id = p_city_id 
		AND d.size = p_size LIMIT 1;
		
		IF v_is_discount > 0 THEN
			SET v_is_discount = 3;
		ELSE 
			#有省产品折扣取省产品折扣
			SELECT COUNT(*)
			INTO v_is_discount
			FROM t_flow_discount_product d 
			WHERE d.user_type = p_user_type  
			AND d.proxy_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = p_province_id
			AND d.city_id = 0 
			AND d.size = p_size LIMIT 1;
			IF v_is_discount > 0 THEN
				SET v_is_discount = 4;
			ELSE	
				#有全国产品折扣取全国产品折扣
				SELECT COUNT(*)
				INTO v_is_discount
				FROM t_flow_discount_product d 
				WHERE d.user_type = p_user_type  
				AND d.proxy_id = p_object_id 
				AND d.operator_id = p_operator_id
				AND d.province_id = 1
				AND d.city_id = 0 
				AND d.size = p_size LIMIT 1;
				IF v_is_discount > 0 THEN
					SET v_is_discount = 5;
				ELSE	
					#如果有市折扣就取市折扣
					SELECT COUNT(*)
					INTO v_is_discount
					FROM t_flow_discount d 
					WHERE d.user_type = p_user_type  
					AND d.proxy_id = p_object_id 
					AND d.operator_id = p_operator_id
					AND d.province_id = 0
					AND d.city_id = p_city_id LIMIT 1;		
					IF v_is_discount > 0 THEN
						SET v_is_discount = 1;
					ELSE
						#否则取省折扣
						SELECT COUNT(*)
						INTO v_is_discount
						FROM t_flow_discount d 
						WHERE d.user_type = p_user_type  
						AND d.proxy_id = p_object_id 
						AND d.operator_id = p_operator_id
						AND d.province_id = p_province_id
						AND d.city_id = 0 LIMIT 1;
						IF v_is_discount > 0 THEN
							SET v_is_discount = 2;
						ELSE 
							SET v_is_discount = 0;
						END IF;
					END IF;
					
				END IF;
				
				
			END IF; 
			
		END IF;
		
		
	ELSEIF p_user_type = 2 THEN
		#如果有市产品折扣就取市产品折扣
		SELECT COUNT(*)
		INTO v_is_discount
		FROM t_flow_discount_product d 
		WHERE d.user_type = p_user_type  
		AND d.enterprise_id = p_object_id 
		AND d.operator_id = p_operator_id
		AND d.province_id = 0
		AND d.city_id = p_city_id 
		AND d.size = p_size LIMIT 1;
		IF v_is_discount >0  THEN
			SET v_is_discount = 3;
		ELSE 
			#有省产品折扣取省产品折扣
			SELECT COUNT(*)
			INTO v_is_discount
			FROM t_flow_discount_product d 
			WHERE d.user_type = p_user_type  
			AND d.enterprise_id = p_object_id 
			AND d.operator_id = p_operator_id
			AND d.province_id = p_province_id
			AND d.city_id = 0 
			AND d.size = p_size LIMIT 1;
			IF v_is_discount >0  THEN
				SET v_is_discount = 4;
			ELSE 	
				#有全国产品折扣取全国产品折扣
				SELECT COUNT(*)
				INTO v_is_discount
				FROM t_flow_discount_product d 
				WHERE d.user_type = p_user_type  
				AND d.enterprise_id = p_object_id 
				AND d.operator_id = p_operator_id
				AND d.province_id = 1
				AND d.city_id = 0 
				AND d.size = p_size LIMIT 1;
				IF v_is_discount >0  THEN
					SET v_is_discount = 5;
				ELSE 
					#如果有市折扣就取市折扣
					SELECT COUNT(*)
					INTO v_is_discount
					FROM t_flow_discount d 
					WHERE d.user_type = p_user_type  
					AND d.enterprise_id = p_object_id 
					AND d.operator_id = p_operator_id
					AND d.province_id = 0
					AND d.city_id = p_city_id LIMIT 1;
					IF v_is_discount > 0 THEN
						SET v_is_discount = 1;
					ELSE
						SELECT COUNT(*)
						INTO v_is_discount
						FROM t_flow_discount d 
						WHERE d.user_type = p_user_type  
						AND d.enterprise_id = p_object_id 
						AND d.operator_id = p_operator_id
						AND d.province_id = p_province_id
						AND d.city_id = 0 LIMIT 1;
						IF v_is_discount > 0 THEN
							SET v_is_discount = 2;
						ELSE 
							SET v_is_discount = 0;
						END IF;
					END IF;
					
				END IF;
				
			END IF;
		END IF;
	END IF;
	RETURN v_is_discount;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_profit_case
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_profit_case`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_profit_case`(
p_proxy_id BIGINT
,p_user_type INT
,p_operator_id INT
,p_province_id INT 
,p_city_id INT
,p_size DECIMAL(11,3)
,p_is_discount INT
) RETURNS text CHARSET utf8
BEGIN 
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT; 
	#代理商ID
	DECLARE v_proxy_id BIGINT;
	#返利合并
	DECLARE v_profit_case TEXT DEFAULT '';
	#返利金额 
	DECLARE v_profit DECIMAL(11,3);
	#自身折口
	DECLARE v_discount_number DECIMAL(5,3);
	#上级折口
	DECLARE v_top_discount_number DECIMAL(5,3);
	
	DECLARE v_nation_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_province_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_city_discount_number DECIMAL(5,3) DEFAULT 0; 
	
	DECLARE v_top_nation_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_top_province_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_top_city_discount_number DECIMAL(5,3) DEFAULT 0; 
	#判断是否找到代理商
	DECLARE v_count INT DEFAULT 1;
	
	
	SET v_profit_case = '[';
	
	IF p_user_type = 1 THEN #代理商
		SET v_proxy_id = p_proxy_id;
		SET v_top_proxy_id = p_proxy_id;		
	ELSEIF p_user_type = 2 THEN #企业		
		SELECT e.top_proxy_id 
		INTO v_proxy_id
		FROM t_flow_enterprise e 
		WHERE e.enterprise_id = p_proxy_id;
		SET v_top_proxy_id = v_proxy_id;
		#return v_proxy_id;
		#判断市折扣还是省折扣
		SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
		INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
		FROM t_flow_discount d
		WHERE d.user_type = 2  
		AND d.enterprise_id = p_proxy_id 
		AND d.operator_id = p_operator_id
		AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
		
		
		SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
		INTO v_top_nation_discount_number,v_top_province_discount_number,v_top_city_discount_number
		FROM t_flow_discount d
		WHERE d.user_type = 1  
		AND d.proxy_id = v_top_proxy_id 
		AND d.operator_id = p_operator_id
		AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
		
		#SET v_discount_number = IF(v_city_discount_number>0,v_city_discount_number,IF(v_province_discount_number>0,v_province_discount_number,v_nation_discount_number));
		#SET v_top_discount_number = IF(v_top_city_discount_number>0,v_top_city_discount_number,IF(v_top_province_discount_number>0,v_top_province_discount_number,v_top_nation_discount_number));
		#有市折扣取市折扣，省折扣取省折扣，全国折扣取全国折扣
		IF p_is_discount = 1 THEN #有市折扣取市折扣
			SET v_discount_number = v_city_discount_number;
			SET v_top_discount_number = v_top_city_discount_number;
		ELSEIF p_is_discount = 2 THEN #省折扣取省折扣
			SET v_discount_number = v_province_discount_number;
			SET v_top_discount_number = v_top_province_discount_number;
		ELSE #全国折扣取全国折扣
			SET v_discount_number = v_nation_discount_number;
			SET v_top_discount_number = v_top_nation_discount_number;
		END IF;
		
		IF v_top_discount_number IS NULL THEN 
			SET v_top_discount_number = 1;
		END IF;
		
		IF v_discount_number IS NULL THEN 
			SET v_discount_number = 1;
		END IF;	
		#return v_discount_number;	
		#RETURN v_top_discount_number;
		IF v_discount_number >= v_top_discount_number THEN
			SET v_profit = (v_discount_number - v_top_discount_number)*p_size;
			SET v_profit_case = CONCAT(v_profit_case,'[','1,',v_top_proxy_id,',',v_profit,',',v_top_discount_number,',',v_discount_number,'],');
		END IF;
		#return v_top_discount_number;
	END IF;
	#初始值	
	IF p_proxy_id > 1 THEN
		WHILE v_top_proxy_id <> 1 AND v_count <> 0 DO 
			#获得上级代理商
			SELECT px.top_proxy_id,COUNT(*)
			INTO v_top_proxy_id,v_count
			FROM t_flow_proxy px      
			WHERE px.proxy_id = v_proxy_id;	
			#如果不是1就进行合并计算 
			IF v_top_proxy_id <> 1 AND v_count <> 0  THEN
				#自身折口
					
				SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
				,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
				,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
				INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
				FROM t_flow_discount d
				WHERE d.user_type = 1  
				AND d.proxy_id = v_proxy_id 
				AND d.operator_id = p_operator_id
				AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
				
				
				#自身折口
				SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
				,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
				,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
				INTO v_top_nation_discount_number,v_top_province_discount_number,v_top_city_discount_number
				FROM t_flow_discount d
				WHERE d.user_type = 1  
				AND d.proxy_id = v_top_proxy_id 
				AND d.operator_id = p_operator_id
				AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
				
				#SET v_discount_number = IF(v_city_discount_number>0,v_city_discount_number,IF(v_province_discount_number>0,v_province_discount_number,v_nation_discount_number));
				#SET v_top_discount_number = IF(v_top_city_discount_number>0,v_top_city_discount_number,IF(v_top_province_discount_number>0,v_top_province_discount_number,v_top_nation_discount_number));
				#有市折扣取市折扣，省折扣取省折扣，全国折扣取全国折扣
				IF p_is_discount = 1 THEN #有市折扣取市折扣
					SET v_discount_number = v_city_discount_number;
					SET v_top_discount_number = v_top_city_discount_number;
				ELSEIF p_is_discount = 2 THEN #省折扣取省折扣
					SET v_discount_number = v_province_discount_number;
					SET v_top_discount_number = v_top_province_discount_number;
				ELSE #全国折扣取全国折扣
					SET v_discount_number = v_nation_discount_number;
					SET v_top_discount_number = v_top_nation_discount_number;
				END IF;
				
				#return v_top_proxy_id;
				#折口价
				IF v_top_discount_number IS NULL THEN 
					SET v_top_discount_number = 1;
				END IF;
				
				IF v_discount_number IS NULL THEN 
					SET v_discount_number = 1;
				END IF;	
				
				IF v_discount_number >= v_top_discount_number THEN
					SET v_profit = (v_discount_number - v_top_discount_number)*p_size;
					#合并
					SET v_profit_case = CONCAT(v_profit_case,'[','1,',v_top_proxy_id,',',v_profit,',',v_top_discount_number,',',v_discount_number,'],');
				ELSE 
					SET v_profit_case = CONCAT(v_profit_case,'');
				END IF;
			END IF;
			
			SET v_proxy_id = v_top_proxy_id;
		END WHILE;
	END IF;
	#如果返点
	IF LENGTH(v_profit_case) > 1 THEN
		SET v_profit_case = SUBSTRING(v_profit_case,1,LENGTH(v_profit_case)-1);
	END IF;
	SET v_profit_case = CONCAT(v_profit_case,']');
	RETURN v_profit_case;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_profit_case_product
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_profit_case_product`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_profit_case_product`(
p_proxy_id BIGINT
,p_user_type INT
,p_operator_id INT
,p_province_id INT 
,p_city_id INT
,p_size DECIMAL(11,3)
,p_size_int INT
,p_is_discount INT
) RETURNS text CHARSET utf8
BEGIN 
	#上级代理商ID
	DECLARE v_top_proxy_id BIGINT; 
	#代理商ID
	DECLARE v_proxy_id BIGINT;
	#返利合并
	DECLARE v_profit_case TEXT DEFAULT '';
	#返利金额 
	DECLARE v_profit DECIMAL(11,3);
	#自身折口
	DECLARE v_discount_number DECIMAL(5,3);
	#上级折口
	DECLARE v_top_discount_number DECIMAL(5,3);
	
	DECLARE v_nation_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_province_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_city_discount_number DECIMAL(5,3) DEFAULT 0; 
	
	DECLARE v_top_nation_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_top_province_discount_number DECIMAL(5,3) DEFAULT 0; 
	DECLARE v_top_city_discount_number DECIMAL(5,3) DEFAULT 0; 
	#判断是否找到代理商
	DECLARE v_count INT DEFAULT 1;
	
	
	SET v_profit_case = '[';
	
	IF p_user_type = 1 THEN #代理商
		SET v_proxy_id = p_proxy_id;
		SET v_top_proxy_id = p_proxy_id;		
	ELSEIF p_user_type = 2 THEN #企业		
		SELECT e.top_proxy_id 
		INTO v_proxy_id
		FROM t_flow_enterprise e 
		WHERE e.enterprise_id = p_proxy_id;
		SET v_top_proxy_id = v_proxy_id;
		#return v_proxy_id;
		#判断市产品折扣还是省产品折扣
		SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
		INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
		FROM t_flow_discount_product d
		WHERE d.user_type = 2  
		AND d.enterprise_id = p_proxy_id 
		AND d.operator_id = p_operator_id
		AND d.size = p_size_int
		AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
		
		
		SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
		,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
		INTO v_top_nation_discount_number,v_top_province_discount_number,v_top_city_discount_number
		FROM t_flow_discount_product d
		WHERE d.user_type = 1  
		AND d.proxy_id = v_top_proxy_id 
		AND d.operator_id = p_operator_id
		AND d.size = p_size_int
		AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
		
		#SET v_discount_number = IF(v_city_discount_number>0,v_city_discount_number,IF(v_province_discount_number>0,v_province_discount_number,v_nation_discount_number));
		#SET v_top_discount_number = IF(v_top_city_discount_number>0,v_top_city_discount_number,IF(v_top_province_discount_number>0,v_top_province_discount_number,v_top_nation_discount_number));
		#有市折扣取市折扣，省折扣取省折扣，全国折扣取全国折扣
		IF p_is_discount = 3 THEN #有市折扣取市折扣
			SET v_discount_number = v_city_discount_number;
			SET v_top_discount_number = v_top_city_discount_number;
		ELSEIF p_is_discount = 4 THEN #省折扣取省折扣
			SET v_discount_number = v_province_discount_number;
			SET v_top_discount_number = v_top_province_discount_number;
		ELSE #全国折扣取全国折扣
			SET v_discount_number = v_nation_discount_number;
			SET v_top_discount_number = v_top_nation_discount_number;
		END IF;
		
		IF v_top_discount_number IS NULL THEN 
			SET v_top_discount_number = 1;
		END IF;
		
		IF v_discount_number IS NULL THEN 
			SET v_discount_number = 1;
		END IF;	
		#return v_discount_number;	
		#RETURN v_top_discount_number;
		IF v_discount_number >= v_top_discount_number THEN
			SET v_profit = (v_discount_number - v_top_discount_number)*p_size;
			SET v_profit_case = CONCAT(v_profit_case,'[','1,',v_top_proxy_id,',',v_profit,',',v_top_discount_number,',',v_discount_number,'],');
		END IF;
		#return v_top_discount_number;
	END IF;
	#初始值	
	IF p_proxy_id > 1 THEN
		WHILE v_top_proxy_id <> 1 AND v_count <> 0 DO 
			#获得上级代理商
			SELECT px.top_proxy_id,COUNT(*)
			INTO v_top_proxy_id,v_count
			FROM t_flow_proxy px      
			WHERE px.proxy_id = v_proxy_id;	
			#如果不是1就进行合并计算 
			IF v_top_proxy_id <> 1 AND v_count <> 0  THEN
				#自身折口
					
				SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
				,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
				,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
				INTO v_nation_discount_number,v_province_discount_number,v_city_discount_number
				FROM t_flow_discount_product d
				WHERE d.user_type = 1  
				AND d.proxy_id = v_proxy_id 
				AND d.operator_id = p_operator_id
				AND d.size = p_size_int
				AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
				
				
				#自身折口
				SELECT MAX(CASE WHEN d.province_id = 1 THEN d.discount_number ELSE 0 END )
				,MAX(CASE WHEN d.province_id = p_province_id AND d.city_id = 0 THEN d.discount_number ELSE 0 END )
				,MAX(CASE WHEN d.province_id = 0 AND d.city_id = p_city_id THEN d.discount_number ELSE 0 END )
				INTO v_top_nation_discount_number,v_top_province_discount_number,v_top_city_discount_number
				FROM t_flow_discount_product d
				WHERE d.user_type = 1  
				AND d.proxy_id = v_top_proxy_id 
				AND d.operator_id = p_operator_id
				AND d.size =p_size_int
				AND (d.province_id IN(1,p_province_id) OR d.city_id = p_city_id);
				
				#SET v_discount_number = IF(v_city_discount_number>0,v_city_discount_number,IF(v_province_discount_number>0,v_province_discount_number,v_nation_discount_number));
				#SET v_top_discount_number = IF(v_top_city_discount_number>0,v_top_city_discount_number,IF(v_top_province_discount_number>0,v_top_province_discount_number,v_top_nation_discount_number));
				#有市折扣取市折扣，省折扣取省折扣，全国折扣取全国折扣
				IF p_is_discount = 3 THEN #有市折扣取市折扣
					SET v_discount_number = v_city_discount_number;
					SET v_top_discount_number = v_top_city_discount_number;
				ELSEIF p_is_discount = 4 THEN #省折扣取省折扣
					SET v_discount_number = v_province_discount_number;
					SET v_top_discount_number = v_top_province_discount_number;
				ELSE #全国折扣取全国折扣
					SET v_discount_number = v_nation_discount_number;
					SET v_top_discount_number = v_top_nation_discount_number;
				END IF;
				
				#return v_top_proxy_id;
				#折口价
				IF v_top_discount_number IS NULL THEN 
					SET v_top_discount_number = 1;
				END IF;
				
				IF v_discount_number IS NULL THEN 
					SET v_discount_number = 1;
				END IF;	
				
				IF v_discount_number >= v_top_discount_number THEN
					SET v_profit = (v_discount_number - v_top_discount_number)*p_size;
					#合并
					SET v_profit_case = CONCAT(v_profit_case,'[','1,',v_top_proxy_id,',',v_profit,',',v_top_discount_number,',',v_discount_number,'],');
				ELSE 
					SET v_profit_case = CONCAT(v_profit_case,'');
				END IF;
			END IF;
			
			SET v_proxy_id = v_top_proxy_id;
		END WHILE;
	END IF;
	#如果返点
	IF LENGTH(v_profit_case) > 1 THEN
		SET v_profit_case = SUBSTRING(v_profit_case,1,LENGTH(v_profit_case)-1);
	END IF;
	SET v_profit_case = CONCAT(v_profit_case,']');
	RETURN v_profit_case;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_proxy_name
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_proxy_name`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_proxy_name`(p_proxy_id BIGINT,p_user_type INT) RETURNS varchar(50) CHARSET utf8
BEGIN 
	DECLARE v_return_info VARCHAR(50);
	
	IF p_user_type = 1 THEN #代理商
		SELECT px.proxy_name
		INTO v_return_info 
		FROM t_flow_proxy px 
		WHERE px.proxy_id = p_proxy_id; 
	ELSEIF p_user_type = 2 THEN #企业
		SELECT e.enterprise_name 
		INTO v_return_info
		FROM t_flow_enterprise e 
		WHERE e.enterprise_id = p_proxy_id;
	END IF;
		
	RETURN v_return_info;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_get_top_proxy
-- ----------------------------
DROP FUNCTION IF EXISTS `f_get_top_proxy`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_get_top_proxy`(p_proxy_id BIGINT,p_user_type INT) RETURNS bigint(20)
BEGIN 
	DECLARE v_top_proxy_id BIGINT; 
	DECLARE v_proxy_id BIGINT;
	DECLARE v_return_top_proxy_id BIGINT;
	DECLARE v_count INT DEFAULT 1;
	
	IF p_user_type = 1 THEN #代理商
		SET v_proxy_id = p_proxy_id;
		SET v_top_proxy_id = p_proxy_id;
		SET v_return_top_proxy_id = p_proxy_id;
	ELSEIF p_user_type = 2 THEN #企业
		SELECT e.top_proxy_id 
		INTO v_proxy_id
		FROM t_flow_enterprise e 
		WHERE e.enterprise_id = p_proxy_id;
		SET v_top_proxy_id = v_proxy_id;
		SET v_return_top_proxy_id = v_proxy_id;
	END IF;
	IF p_proxy_id > 1 THEN
		WHILE v_top_proxy_id <> 1 AND v_count <> 0 DO 
			SET v_return_top_proxy_id = v_top_proxy_id;
			SELECT px.top_proxy_id,COUNT(*)
			INTO v_top_proxy_id,v_count
			FROM t_flow_proxy px      
			WHERE px.proxy_id = v_proxy_id;
			SET v_proxy_id = v_top_proxy_id;
		END WHILE;
	END IF;
	RETURN v_return_top_proxy_id;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_str_split
-- ----------------------------
DROP FUNCTION IF EXISTS `f_str_split`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_str_split`(
  p_str VARCHAR(255)
  ,p_delim VARCHAR(12)
  ,p_pos INT
) RETURNS varchar(255) CHARSET utf8
BEGIN 
	RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(p_str, p_delim, p_pos),LENGTH(SUBSTRING_INDEX(p_str,p_delim, p_pos -1)) + 1),p_delim,'');
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for f_str_split_count
-- ----------------------------
DROP FUNCTION IF EXISTS `f_str_split_count`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `f_str_split_count`(
  p_str VARCHAR(255)
  ,p_delim VARCHAR(12)
) RETURNS int(11)
BEGIN
  RETURN 1+((LENGTH(p_str) - LENGTH(REPLACE(p_str,p_delim,'')))/LENGTH(p_delim));
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getProxyChildList
-- ----------------------------
DROP FUNCTION IF EXISTS `getProxyChildList`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `getProxyChildList`(rootIds VARCHAR(1000)) RETURNS varchar(1000) CHARSET utf8
BEGIN 
DECLARE sTemp VARCHAR(1000); 
DECLARE sTempChd VARCHAR(1000);
DECLARE rootId VARCHAR(1000);
DECLARE old_rootId VARCHAR(1000);
DECLARE i INT(10);
IF rootIds='' THEN
RETURN '';
END IF;
SET i = 1;
SET rootId = 1;
SET sTemp = '$';
WHILE rootId DO
SET rootId = SUBSTRING_INDEX(SUBSTRING_INDEX(rootIds,',',i),',',-1);
IF rootId = old_rootId THEN
	RETURN SUBSTRING(sTemp,3,LENGTH(sTemp)-2);
END IF;
SET sTempChd =CAST(rootId AS CHAR); 
WHILE sTempChd IS NOT NULL DO 
SET sTemp = CONCAT(sTemp,',',sTempChd); 
SELECT GROUP_CONCAT(proxy_id) INTO sTempChd FROM t_flow_proxy WHERE FIND_IN_SET(top_proxy_id,sTempChd)>0; 
END WHILE;
SET old_rootId = rootId;
SET i = i+1;
END WHILE;
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for e_order_profit
-- ----------------------------
DROP EVENT IF EXISTS `e_order_profit`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `e_order_profit` ON SCHEDULE EVERY 1 DAY STARTS '2016-05-31 01:00:00' ON COMPLETION PRESERVE ENABLE DO CALL p_tran_order_profit(DATE_ADD(CURDATE(), INTERVAL - 4 DAY),DATE_ADD(CURDATE(), INTERVAL -3 DAY))
;;
DELIMITER ;

-- ----------------------------
-- Event structure for e_stat_profit
-- ----------------------------
DROP EVENT IF EXISTS `e_stat_profit`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `e_stat_profit` ON SCHEDULE EVERY 1 DAY STARTS '2016-08-30 05:00:00' ON COMPLETION PRESERVE ENABLE DO CALL p_auto_stat_profit(DATE_ADD(CURDATE(), INTERVAL - 3 DAY),CURDATE())
;;
DELIMITER ;

-- ----------------------------
-- Event structure for e_stat_record
-- ----------------------------
DROP EVENT IF EXISTS `e_stat_record`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `e_stat_record` ON SCHEDULE EVERY 1 DAY STARTS '2016-08-30 00:00:00' ON COMPLETION PRESERVE ENABLE DO CALL p_auto_stat_record(DATE_ADD(CURDATE(), INTERVAL - 1 DAY),CURDATE())
;;
DELIMITER ;

-- ----------------------------
-- Event structure for e_stat_rpt
-- ----------------------------
DROP EVENT IF EXISTS `e_stat_rpt`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `e_stat_rpt` ON SCHEDULE EVERY 1 DAY STARTS '2016-05-30 02:00:00' ON COMPLETION PRESERVE ENABLE DO CALL p_auto_stat_rpt(DATE_ADD(CURDATE(), INTERVAL - 1 DAY),CURDATE())
;;
DELIMITER ;
