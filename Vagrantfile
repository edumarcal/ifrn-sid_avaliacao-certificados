# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "renatobiohazard/PDS"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  config.vm.network "forwarded_port", guest: 80, host: 8080
  
  # Forward mailcacther to host
  config.vm.network "forwarded_port", guest: 1080, host: 1080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network "private_network", ip: "192.168.33.10"

  # Sync current directory with the Vagrant box's Apache root
  # http://jeremykendall.net/2013/08/09/vagrant-synced-folders-permissions/
  config.vm.synced_folder ".", "/var/www/html",
    owner: "vagrant",
    group: "www-data",
    mount_options: ["dmode=775,fmode=664"]

  # Vagrant box configurations 
  config.vm.provider "virtualbox" do |v|
    # Increase VM Memory to resolve bug associated with MySQL 5.6 install
    # See https://github.com/fideloper/Vaprobash/issues/335#issuecomment-44913379
    v.memory = 256
    # Use host VPN 
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
  end

  # Basic LAMP server
#  config.vm.provision :shell, path: "bootstrap.sh"

end
