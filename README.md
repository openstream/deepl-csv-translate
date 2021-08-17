This as a tool based on Symfony to translate csv files with the DeepL Api.

Checkout repo in a Webspace or use [symfony binary](https://symfony.com/doc/current/setup.html).

If you use a Webspace:
Set Root dir to PROJECT_ROOT/public

Install composer dependencies:
````composer install````

Generate a Secret. DO NOT COMMIT or expose to the public:

For DEV envoirnment:
```
php bin/console secrets:generate-keys
```
Or for PROD envoirnment:
```
APP_RUNTIME_ENV=prod php bin/console secrets:generate-keys
```

Save your DeepL Api Key:

```
php bin/console secrets:set DEEPL_KEY --local
```
(you get prompted to insert the key)

Upload the csv files to PROJECT_ROOT/files with filename: input.csv
(Create dir "files" if it doesn't exist).

In config/packages/deepl_translate_csv.yaml change the **translate** array to the columns you want to get translated.
Skip the columns that don't need translation.


In your browser acces the project. CSV gets translated immediatley!




