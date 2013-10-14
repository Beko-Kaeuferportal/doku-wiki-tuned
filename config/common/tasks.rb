namespace :deploy do
  task :restart, :roles => :app do
  end

  task :start, :roles => :app do
  end

  task :stop, :roles => :app do
  end

  task :finalize_update, :roles => :app do
  end

  task :symlink_shared_folders, :roles => :app do
    run <<-CMD
      rm -Rf #{release_path}/conf && ln -s #{shared_path}/conf #{release_path}/conf && ln -s #{shared_path}/data #{release_path}/data && ln -s #{shared_path}/tools #{release_path}/tools
    CMD
  end

  task :clean_cache, :roles => :app do
    run <<-CMD
      rm -Rf #{release_path}/data/cache/*
    CMD
  end
end


after "deploy", "deploy:cleanup"
after "deploy:create_symlink", "deploy:symlink_shared_folders"
after "deploy:create_symlink", "deploy:clean_cache"
