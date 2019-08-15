/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:13:14
*/

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_wxs_replymoreimg
-- ----------------------------
