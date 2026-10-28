# Laravel Playground Starter
```shell
git clone --depth=1 --branch=main https://github.com/jonaaix/laravel-playground-starter <yourname>
    
cd <yourname>

# Initialize Claude-Auto Repo
rm -rf ./.git \
&& git init -b main \
&& git add . \
&& git commit -m "Initial commit" \
&& git switch -c dev \
&& git switch -c dev_ai
```

```shell
cp .env.example .env

# Salt for media paths — set once, never change after files are stored
sed -i.bak "s/^MEDIA_HASH_SALT=.*/MEDIA_HASH_SALT=$(openssl rand -hex 32)/" .env && rm .env.bak

for key in DB_PASSWORD DB_ROOT_PASSWORD REDIS_PASSWORD MEILISEARCH_KEY; do
  sed -i "s|^${key}=.*|${key}=$(randpw)|" .env
done

cp compose.prod.yaml compose.yaml
```

```shell
php artisan db:seed --class="Aaix\\LaravelCountries\\Database\\Seeders\\DatabaseSeeder"
```
