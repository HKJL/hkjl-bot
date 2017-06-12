# hkjl-bot
'Official' IRC Bot for hackenkunjeleren.nl

# Installation

Requirements:
- MySQL/MariaDB
- Webserver capable of serving PHP
- Python 2.7+

Steps:
- `git clone https://github.com/HKJL/hkjl-bot` somewhere in the documenroot of your webserver, so bot.php is reachable and it can include all it's modules.
- Create a database for the bot
- Create a user and give it SELECT/UPDATE/INSERT/DELETE permissions on the freshly created database
- Run the SQL from the `install.sql` file on the database to create all required tables
- Create a file called `sqlconfig.php` in the hkjl-bot folder with the following contents, and replace values:
    ```
    <?php
    $db = "databasename";
    $user = "databaseuser";
    $pass = "databasepass";
    ```
- Run `python bot.py --help` and adjust parameters as needed, for example: `python bot.py --backend "http://127.0.0.1/bot.php" --channel "#ILikeTesting" --nickname "MyTestingBot"`

Optional:
- For increased security, make sure bot.php can only be called by your `boy.py` script running on localhost. Example for Apache HTTP Server:
  ```
  <Location "/bot.php">
	  Require expr %{REMOTE_ADDR} =~ /(127\.0\.0\.1|::1)/
  </Location>
  ```
- For using mod_discourse, create 'mod_discourse_config.php' with '$discourse_api_key' for a discourse forum instance
- For using mod_weather, create 'mod_weather_config.php' with '$weather_api_key' for api.openweathermap.org
- For using mod_wolfram, create 'mod_wolfram_config.php' with '$wolfram_api_key' for api.wolframalpha.com
- For using mod_youtube, create 'mod_youtube_config.php' with '$youtube_api_key' for the Google Developer key. See https://github.com/youtube/api-samples/ for more info.
