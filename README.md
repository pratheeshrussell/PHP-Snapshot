# PHP SNAPSHOT
This is a simple program to be run in cli. It just records the sha1 hashes of files and stores them in json format.   

I wrote this program to check which wordpress files are modified after a hack.

## Using it
### Generate snapshots
* Copy snapshotter.php to your wordpress installation and run `php snapshotter.php`. It will generate a json file. store it someplace safe
* After modifying files run the same command again to generate new snapshot.
* you may edit the variables *$blackListedFolders* and *$blackListedFiles* as per yout needs

### Comparing the snapshots
* Copy your snapshots to the folder with the **comparer.php** file
* Edit **comparer.php** and change variables *$snapshotFile1* and *$snapshotFile2* to the name of your snapshots.
* Run command `php comparer.php`(Time will be in utc).
* by default the results will be saved to a json file. You can change that by modifying the variable *$saveTofile*