# Usage

The tool is build to be used with Laravel Nova GUI.

For command line, execute the artisan file to list all the available commands:
```
php artisan
```

# Import vulnerabilities

In order to identify vulnerable Products in Websites, Hosts and Repositories, is necessary to run the import modules, that will fetch and download vulnerabilities from public (and comercial) sources (check the modules documentation for more details).

```
php artisan import:vulnerabilities
```

# Add a user (Laravel Nova only)

In order to use the GUI, you can create a user with the following command:

```
php nova:user
```