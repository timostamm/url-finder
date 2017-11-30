class vamp::php7 {
  
	  
	  exec { 'add_php7_repository':
	     command => '/usr/bin/add-apt-repository ppa:ondrej/php'
	  }
	  
	  package { 'php':
	    require => [Exec['add_php7_repository'], Exec['update']],
	    ensure => present,
	  }
	  
	  package { 'php7.1-cli':
	    require => [Package['php']],
	    ensure => present,
	  }
	  
	  
	  file { 
		  '/var/log/php_error.log':
			    ensure => 'present',
			    mode => '644',
			    require => Package['php'];
	  }
	  
	  
    package { 'php7.1-xdebug':
      ensure => present,
      require => [Package['php', 'php7.1-cli'],Exec['update']]
    }
	     
    package { 'php7.1-mbstring':
      ensure => present,
      require => [Package['php', 'php7.1-cli'],Exec['update']]
    }
	    
	  
	  package { 'php7.1-gd':
	    ensure => present,
	    require => [Package['php'],Exec['update']]
	  }
	  package { 'php7.1-curl':
	    ensure => present,
	    require => [Package['php'],Exec['update']]
	  }
	  
  	
  	
	  package{ 'php-pear':
	    ensure => present,
	    require => [Exec['update'],Package['php']],
	  }
	  exec{ 
	    'pear update':
	      require => Package['php-pear'],
	      command => "/usr/bin/pear update-channels \
	                  && touch /tmp/pear_update-channels.tstmp",
	      unless => '/usr/bin/test -f /tmp/pear_update-channels.tstmp';
	    'pear auto_discover':
	      require => Package ['php-pear'],
	      command => "/usr/bin/pear config-set auto_discover 1";
	    'pear upgrade':
	      require => Exec['pear update'],
	      command => '/usr/bin/pear upgrade-all',
	      returns => [0,'',' '];
	  }
	  
  
  
}
