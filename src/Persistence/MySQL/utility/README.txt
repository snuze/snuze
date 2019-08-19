If you tried to run Snuze and got an exception prompting you to read this file,
that means the expected version of the MySQL database schema wasn't found. This
is normal when:

    a) You're setting up the MySQL storage provider for the first time, or

    b) You've just upgraded to a new version of Snuze

The MySQL storage provider for Snuze comes with a utility script for installing
and updating the database schema, as needed. That script is named "utility.php" 
and is in the same directory as this README.txt file.

If you're setting up the MySQL storage provider for the first time: first create
a MySQL database and user for Snuze, and grant the user full privileges on the
database.

Please open a command prompt, `cd` to this directory, and run the command:

    php utility.php

You will be prompted to enter the MySQL information corresponding to your
Snuze database, so please have the following handy:

    Server hostname or IP address
    MySQL username
    MySQL password
    Database name

The utility.php script will then either upgrade the database tables or create 
them from scratch if needed.

Sorry for the inconvenience, someday this might all happen automagically!
