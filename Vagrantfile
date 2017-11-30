# -*- mode: ruby -*-
# vi: set ft=ruby :

github_access_token = File.read(File.expand_path('~') + '/.config/composer/auth.json').match(/"([a-z0-9]{40})"/)[0].to_s

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.box_version = "=20160320.0.0"
  config.vm.box_check_update = false
 
  config.vm.provider "virtualbox" do |v|
  	v.memory = 4000
  end
 
  config.vm.provision :puppet do |puppet|
    puppet.module_path = "vagrant-puppet/modules"
    puppet.manifests_path = "vagrant-puppet/manifests"
    puppet.manifest_file = "site.pp"
    puppet.facter = {
      "logroot" => "/var/log",
	  "github_access_token" => github_access_token, 
	  "php" => "7", # 7 or 5 
    }
  end
end
