cap [stage] deploy:setup  - prepares the web directory to be used with capistrano
cap [stage] deploy        - deploys a branch to the server


cap [stage] db:backup     - dumps the database to databases/backups/#{stage}
cap [stage] db:export     - exports the database to databases/dump.sql and converts it to all stages
cap [stage] db:import     - imports the databases/dump_#{stage}.sql to the server
cap [stage] db:convert    - converts the databases/dump.sql to all stages
