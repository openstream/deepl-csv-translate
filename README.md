This as a tool based on Symfony to translate csv files with the DeepL Api.

Checkout repo in a Webspace or use symfony binary.
Set Root dir to PROJECT_ROOT/puplic

Install composer dependencies:
````composer install````

Generate a Secret. DO NOT COMMIT or expose to the public:

```
APP_RUNTIME_ENV=prod php bin/console secrets:generate-keys
```

Save your DeepL Api Key:

```
php bin/console secrets:set DEEPL_KEY --local
```
(you get prompted to insert the key)

Upload the csv files to ROOT/files with filename: input.csv
(Create dir "files" if it doesn't exist).

In src/Controller/IndexController.php change array keys accordingly to your CSV-Header.
(Add or remove new ones)

In your browser acces the project. CSV gets translated immediatley!




