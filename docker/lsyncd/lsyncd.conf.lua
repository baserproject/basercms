settings {
        logfile    = "/tmp/lsyncd.log",
        statusFile = "/tmp/lsyncd.status",
        nodaemon = false
}
sync {
        default.rsync,
        source = "/var/www/shared/",
        target = "/var/www/html/",
        delay = 0,
        delete="running",
        exclude={"node_modules", "tmp", ".git", ".idea", ".DS_Store", "docker"}
}
sync {
        default.rsync,
        source = "/var/www/html/tmp",
        target = "/var/www/shared/tmp",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/shared/tmp",
        target = "/var/www/html/tmp",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/html/logs",
        target = "/var/www/shared/logs",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/shared/logs",
        target = "/var/www/html/logs",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/html/webroot/files",
        target = "/var/www/shared/webroot/files",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/shared/webroot/files",
        target = "/var/www/html/webroot/files",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/html/vendor",
        target = "/var/www/shared/vendor",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/shared/vendor",
        target = "/var/www/html/vendor",
        delay = 0,
        delete="running",
}
