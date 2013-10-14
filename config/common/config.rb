set :exec_path, File.expand_path(File.dirname(__FILE__) + "/..")
set :scm, :git
set :git_enable_submodules, 1
set :copy_compression, :gzip
set :deploy_via, :remote_cache
set :runner, :user
set :use_sudo, false
set(:run_method) { use_sudo ? :sudo : :run }
set :stages, %w( production )
set :keep_releases, 10


default_run_options[:pty] = true

role :web, "#{domainname}"
role :app, "#{domainname}"

ssh_options[:user]          = "web1"
ssh_options[:compression]   = false
ssh_options[:forward_agent] = true

#dont remove gitfiles, so we can resync from server
#set :copy_exclude, [ '.git' ]
