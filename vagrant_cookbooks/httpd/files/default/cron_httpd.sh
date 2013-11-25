#!/bin/sh

httpd=`ps ax | grep '^.* /usr/sbin/httpd$'`
if [ -z "`ps ax | grep '^.* /usr/sbin/httpd$'`" ]; then
  /sbin/service httpd start
fi
