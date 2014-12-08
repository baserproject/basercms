execute 'resolv.conf' do
    command "echo 'options single-request-reopen' >> /etc/resolv.conf"
    not_if "cat /etc/resolv.conf | grep 'options single-request-reopen'"
end
