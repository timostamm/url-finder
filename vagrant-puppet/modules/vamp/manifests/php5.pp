class vamp::php5 {

	  package { 'php':
	    name =>'php5', 
	    require => Exec['update'],
	    ensure => present,
	  }
	
	  file { 
		  '/var/log/php_error.log':
			   ensure => 'present',
			   mode => '644',
			   require => Package['php'];
	  }
	  
	  package { 'php5-cli':
	    require => Exec['update'],
	    ensure => present,
	  }
	  
  
    package { 'php5-xdebug':
      ensure => present,
      require => [Package['php', 'php5-cli'],Exec['update']]
    }
    
	  
	  package { 'php5-gd':
	    ensure => present,
	    require => [Package['php'],Exec['update']]
	  }
	  package { 'php5-curl':
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
