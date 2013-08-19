namespace :deploy do
  task :restart, :roles => :app do
  end

  task :start, :roles => :app do
  end

  task :stop, :roles => :app do
  end

  task :finalize_update, :roles => :app do
  end

  task :symlink_config_folder, :roles => :app do
    run <<-CMD
      rm -Rf #{release_path}/conf && ln -s #{shared_path}/conf #{release_path}/conf && ln -s #{shared_path}/data #{release_path}/data
    CMD
  end
end


after "deploy",         "deploy:cleanup"
after "deploy:create_symlink",         "deploy:symlink_config_folder"
