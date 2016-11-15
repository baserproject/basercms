# The default recipe takes over yum_globalconfig[/etc/yum.conf]
# Test to make sure the package manager still works.

@test "install a package" {
  yum -y install emacs-nox
}
