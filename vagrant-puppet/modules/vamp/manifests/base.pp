class vamp::base{
  Exec {
    path => [
      '/usr/local/bin',
      '/opt/local/bin',
      '/usr/bin',
      '/usr/sbin',
      '/bin',
      '/sbin'],
      logoutput => true,
  }
  exec { 'update':
    command => '/usr/bin/apt-get update && touch /tmp/apt-get_update.tstmp',
    unless  => '[ -f /tmp/apt-get_update.tstmp ]',
    creates => '/tmp/apt-get_update.tstmp',
  }
  package { 'vim':
    ensure  => present,
    require => Exec['update'],
  }
}
