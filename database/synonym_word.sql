/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50711
Source Host           : 127.0.0.1:3306
Source Database       : shop_search

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2019-01-15 00:13:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `synonym_word`
-- ----------------------------
DROP TABLE IF EXISTS `synonym_word`;
CREATE TABLE `synonym_word` (
  `synonym_id` int(11) NOT NULL AUTO_INCREMENT,
  `primary_word` varchar(16) DEFAULT NULL COMMENT '主同义词',
  `second_word` varchar(16) DEFAULT NULL COMMENT '副同义词',
  PRIMARY KEY (`synonym_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='同义词表';

-- ----------------------------
-- Records of synonym_word
-- ----------------------------
INSERT INTO `synonym_word` VALUES ('1', '女人', '女式');
INSERT INTO `synonym_word` VALUES ('2', '女式', '女人');
INSERT INTO `synonym_word` VALUES ('3', '女人', '女');
INSERT INTO `synonym_word` VALUES ('4', '女式', '女');
