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

cp compose.prod.yaml compose.yaml
```
