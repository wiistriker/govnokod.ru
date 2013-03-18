# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "precise32"
  config.vm.box_url = "http://files.vagrantup.com/precise32.box"
  
  # packages installation and other stuff
  config.vm.provision :shell, :path => "vm_init.sh"
  
  # http://localhost:8080
  config.vm.network :forwarded_port, guest: 80, host: 8080
end
