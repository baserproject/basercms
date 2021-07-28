# $Id$
#
# Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
#								1785 E. Sahara Avenue, Suite 490-204
#								Las Vegas, Nevada 89104
#
# Licensed under The MIT License
# For full copyright and license information, please see the LICENSE.txt
# Redistributions of files must retain the above copyright notice.
# MIT License (http://www.opensource.org/licenses/mit-license.php)

# CUSTOMIZE MODIFY 2021/07/14
# >>>

# CREATE TABLE cake_sessions (
#   id varchar(255) NOT NULL default '',
#   data text,
#   expires int(11) default NULL,
#   PRIMARY KEY  (id)
# );

# ---

# ---------------------------------------------------
# MySQLの例
# ※ prefixがmysite_以外の時は書き換えてください
# ---------------------------------------------------
DROP TABLE IF EXISTS mysite_cake_sessions;
CREATE TABLE mysite_cake_sessions (
  id varchar(255) NOT NULL default '',
  data longtext,
  expires int(11) default NULL,
  PRIMARY KEY  (id)
);

# ---------------------------------------------------
# PostgreSQLの例
# ※ prefixがmysite_以外の時は書き換えてください
# ---------------------------------------------------
# DROP TABLE IF EXISTS "mysite_cake_sessions";
# CREATE TABLE "mysite_cake_sessions" (
#     "id" character varying(255) DEFAULT '' NOT NULL,
#     "data" text,
#     "expires" integer,
#     PRIMARY KEY ("id")
# );

# ---------------------------------------------------
# SQLiteの例
# ---------------------------------------------------
# DROP TABLE IF EXISTS cake_sessions;
# CREATE TABLE cake_sessions (
#   id varchar(255) NOT NULL default '',
#   data text,
#   expires int(11) default NULL,
#   PRIMARY KEY  (id)
# );

# <<<
