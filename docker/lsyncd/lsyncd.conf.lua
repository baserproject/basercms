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
        exclude={"node_modules", "app/tmp", ".git", ".idea", ".DS_Store", ".vagrant", "docker"}
}
sync {
        default.rsync,
        source = "/var/www/html/",
        target = "/var/www/shared/",
        delay = 1,
        delete="running",
        exclude={"node_modules", "app/tmp", ".git", ".idea", ".DS_Store", ".vagrant", "docker"}
}
