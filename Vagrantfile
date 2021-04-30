# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "centos7-base"
  config.vm.network "forwarded_port", guest: 80, host: 8882
  config.vm.network "private_network", ip:"192.168.22.18", auto_config: true
  config.vm.hostname = "centos7.2-x64"
  config.vm.provider "virtualbox" do |vb|
    vb.name = "centos7.2-docker"
    vb.cpus = 2
    vb.memory ="8192"
    # unless File.exist?('./docker.vdi')
    #   vb.customize ['createhd', '--filename', './docker.vdi', '--variant', 'Fixed', '--size', 50 * 1024]
    # end
    # unless File.exist?('./thirdDisk.vdi')
    #   vb.customize ['createhd', '--filename', './thirdDisk.vdi', '--variant', 'Fixed', '--size', 10 * 1024]
    # end
    # vb.customize ['storageattach', :id,  '--storagectl', 'SATA Controller', '--port', 1, '--device', 0, '--type', 'hdd', '--medium', './docker.vdi']
    # vb.customize ['storageattach', :id,  '--storagectl', 'SATA', '--port', 2, '--device', 0, '--type', 'hdd', '--medium', './thirdDisk.vdi']
  end
  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  config.vm.provision "shell", inline: <<-SHELL
    sudo yum -y update
    sudo yum remove docker docker-common docker-selinux  docker-engine
    sudo yum install -y yum-utils device-mapper-persistent-data lvm2
    sudo yum-config-manager -y --add-repo http://mirrors.aliyun.com/docker-ce/linux/centos/docker-ce.repo
    sudo yum install -y docker-ce
    sudo yum install -y vim
    sudo systemctl start docker
    sudo systemctl enable docker
    sudo groupadd docker
    sudo gpasswd -a vagrant docker
    sudo wget https://github.com/docker/compose/releases/download/1.24.1/docker-compose-`uname -s`-`uname -m` -O /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    echo 'All have done!'
  SHELL
  #config.vm.provision "shell", path:"lnmp.sh"
end
