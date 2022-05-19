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
        exclude={"node_modules", ".git", ".idea", ".DS_Store", ".vagrant", "docker"}
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
        exclude={"node_modules", ".git", ".idea", ".DS_Store", ".vagrant", "docker"}
}
