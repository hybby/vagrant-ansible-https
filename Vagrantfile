# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # webserver01
  config.vm.define "webserver01" do |server|
    server.vm.box = "spantree/Centos-6.5_x86-64"
    server.vm.network :private_network, ip: "192.168.10.11"
    server.vm.network "forwarded_port", guest: 10443, host: 8441
    server.vm.hostname = "webserver01.hybby.com"
    server.vm.post_up_message = "hey!  webserver01 is up!  check out https://localhost:8441"
  end

  # webserver02
  config.vm.define "webserver02" do |server|
    server.vm.box = "spantree/Centos-6.5_x86-64"
    server.vm.network :private_network, ip: "192.168.10.12"
    server.vm.network "forwarded_port", guest: 10443, host: 8442
    server.vm.hostname = "webserver02.hybby.com"
    server.vm.post_up_message = "hey!  webserver02 is up!  check out https://localhost:8442"
  end

  # provision
  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "site.yml"
    ansible.inventory_path = "hosts" 
    ansible.extra_vars = { https_port: 10443 }
    # ansible.extra_vars = { https_port: 10443,
    #                        webmaster: "your@email.here" }
    # ansible.verbose = "vvv"
  end
end
