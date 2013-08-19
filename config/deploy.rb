#require 'capistrano/recipes/syntax_checking'
require 'capistrano/ext/multistage'
require 'capistrano_colors'

capistrano_color_matchers = [
  { :match => /command finished/,       :color => :hide,      :prio => 10 },
  { :match => /executing command/,      :color => :blue,      :prio => 10, :attribute => :underscore },
  { :match => /^transaction: commit$/,  :color => :magenta,   :prio => 10, :attribute => :blink },
  { :match => /git/,                    :color => :white,     :prio => 20, :attribute => :reverse },
]

colorize( capistrano_color_matchers )


# configuration
set :application, "it-wiki"
set :domainname,  "kb.ib.io"
set :repository,  "git@github.com:Beko-Kaeuferportal/doku-wiki-tuned.git"
set :branch,      "master"



# shared
#set :shared_directories, %w( wp-content/uploads wp-content/themes/kuechenportal/uploads )
#set :cache_directories, %w( wp-content/cache wp-content/themes/kuechenportal/cache wp-content/themes/kuechenportal/temp )
#set :shared_files, %w( wp-config.php sitemap.xml.xsl )



# nothing to configure below (normally)
load 'config/common/config'
load 'config/common/tasks'
