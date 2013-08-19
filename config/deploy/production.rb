set :deploy_to,       "/var/www/it-wiki/"
set :default_branch,  "master"


set :branch, ENV['BRANCH'] || proc {
  dont_ask_me = ['assets:import', 'assets:export', 'db:convert', 'db:export', 'db:import', 'dont:ask_for_branch']

  if ARGV.all?{|i| not dont_ask_me.include?(i)}
    Capistrano::CLI.ui.ask("Which branch would you like to deploy to live? [#{default_branch}] ")
  else
    ''
  end
}

set(:branch, default_branch) if branch.empty?


