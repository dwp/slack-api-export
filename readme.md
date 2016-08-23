# Slack API Export

This application is a Slack App which facilitates the following:

1. OAuth2 handshake which allows the tool to be added as an App in Slack.  This is the means though which an API authentication token can be obtained.
2. A "sync" CLI tool which pulls down Team, User, Message and Channel data from the Slack API for storage in a MongoDb database.
3. An "export" CLI tool which exports all data within MongoDB as JSON, with an export file created per team. 

This application has been prepared with Docker - you will need a current version of docker installed on your machine to run this code, please obtain this from the [Docker website](https://www.docker.com/products/overview).

### Components

This application is made up of the following Docker containers:

1. A Symfony PHP application which contains the project code, this code is runnable in three different containers.
   1. http - this container runs a web server on port 8000.
   2. sync - this container runs a command which synchronises data for any team which has added the app in Slack.
   3. export - this container dumps all data within MongoDB to JSON files.
2. A localtunnel.me container which provides a tunnel to make the code accessible for the Slack OAuth exchange.
3. A MongoDB database container.

### Setting up

In order to get the tool working you need to setup your own App in slack - you can create you app [here](https://api.slack.com/apps/new).

Once your app is created you need to copy *.env.dist* to *.env* and update it with your credentials:

1. **TUNNEL_HOSTNAME** - This is the name of the localtunnel which is going to be used to facilitate a public oauth exchange.  The final tunnel name will be ```${TUNNEL_HOSTNAME}.localtunnel.me```.
2. **SLACK_CLIENT_ID** - This is the oauth client ID which you obtain from Slack when setting up your app.
3. **SLACK_CLIENT_SECRET** - This is the oauth client secret which you obtain from Slack when setting up your app.  
4. **SLACK_CLIENT_REDIRECT_URI** - This is the URL which Slack will redirect back to after completing the oauth process - it is normally of the format ```${TUNNEL_HOSTNAME}.localtunnel.me/oauth```.

Once you have this *.env* file setup and configured you should be ready to start your application.

### Adding as an "App"

Run ```docker-compose up``` from the root of the project - this will build and then launch all containers.  It has been noted that the localtunnel implementation has some issues when running via the ```up``` command.

As such once all systems have been build and run for the first time it is recommended that they are shut down and then restarted via ```docker-compose start``` which should cause them all to come online successfully.

At that point you can then visit https://yourtunnel.localtunnel.me/oauth and the application will request an authentication with Slack.

### Synchronising

Run the sync container via ```docker-compose run sync``` which will cause the slack:sync command to run interactively.  This will suck down all data in to the MongoDB instance.

### Exporting

Run the sync container via ```docker-compose run export``` which will cause the slack:sync command to run interactively.  This will export all data in MongoDB to the /export directory within the project, with one JSON file being created for each team.
