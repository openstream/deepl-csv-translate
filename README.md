Generate a Secret. DO NOT COMMIT or expose publicaly:

```
APP_RUNTIME_ENV=prod php bin/console secrets:generate-keys
```

Save your DeepL Api Key:

```
php bin/console secrets:set DEEPL_KEY --local
```
(you get prompted to insert the key)


