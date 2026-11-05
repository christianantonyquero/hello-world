# Lana Del Rey - - Spumoni Virtual Pop-Up Shop

This is the Lana Del Rey pop-up shop code base

Laravel and Vue SPA for creating "Virtual" Pop-Up Shops based around a unqiue artists or event promotion experience.

# Install Deps
> composer install
> npm install
`php artisan storage:link`

# Test user
Add an admin user: `php artisan db:seed` (username: `test@few.io`, password: `admin123`)

# Testing
* `php artisan dusk` (to run with browser visible, comment out `--headless` option in `DuskTestCase`)
  * To run with separate DB, use `php artisan serve --env=dusk.local` w/ `APP_URL` set to `http://127.0.0.1:8000`
    * `php artisan migrate --env=dusk.local`
    * use `sqlite_testing` db so it can be shared between tests and `serve`
* `phpunit` (not using this yet but maybe for some unit testing stuff later)
* PHP Unit testing is not up to date. 
* No Vue testing on the frontend

# Frontend
Frontend is built using Laravel's MIX (webpack).
https://laravel.com/docs/7.x/mix
https://laravel.com/docs/7.x/frontend

For development run
> npm run watch

> npm run dev

*Compiled CSS and JS assets are not stored in the repo. They are built on the server during deployment.
Please don't check in compiled CSS / JS*

Images are compiled from `public/img` to `public/images`

## Frontend Libraries
- Bulma for CSS forms and modals, etc. The Boring Stuff
- JQuery for now
- Bootbox - helper for easy dynamic bootstrap modals

# Library Notes
* Flash messages - https://github.com/laracasts/flash


# Merging
- Make Feature branches off develop. Merge or create PR in Gitlab
- stage is our release branch. Develop gets merged to stage and deployed to staging server on Forge
- master is the production branch. Stage gets merged to master and autodeployed to production on Digital Ocean


# Author
Christian Antony Quero
