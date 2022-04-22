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
        exclude={"node_modules", "app/tmp", "app/View/Pages", "app/webroot/files", ".git", ".idea", ".DS_Store", ".vagrant", "docker"}
}
sync {
        default.rsync,
        source = "/var/www/shared/docker/bin/",
        target = "/var/www/html/docker/bin/",
        delay = 0,
        delete="running"
}
sync {
        default.rsync,
        source = "/var/www/html/",
        target = "/var/www/shared/",
        delay = 1,
        delete="running",
        exclude={"node_modules", "app/tmp", "app/View/Pages", "app/webroot/files", ".git", ".idea", ".DS_Store", ".vagrant", "docker"}
}
sync {
        default.rsync,
        source = "/var/www/html/app/tmp",
        target = "/var/www/shared/app/tmp",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/html/app/View/Pages",
        target = "/var/www/shared/app/View/Pages",
        delay = 0,
        delete="running",
}
sync {
        default.rsync,
        source = "/var/www/html/app/webroot/files",
        target = "/var/www/shared/app/webroot/files",
        delay = 0,
        delete="running",
}

