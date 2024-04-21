## About this project

`personal-canvas` is a personal blog project powred by [Laravel](https://laravel.com/) framework and [Laravel Canvas](https://github.com/austintoddj/canvas)

## Getting Started

1. **Choose Your Local Development Tool:**

   Select your preferred local development tool, such as [Laragon](https://github.com/leokhoa/laragon),[Laravel Herd](https://herd.laravel.com), XAMPP, WAMP, or any other tool that suits your needs.

   ### Version Requirments ###
   - Node 16+
   - PHP version 8.2+
   - MYSQL version 8.0+


2. **Configure Your Environment:**

   Update your `.env` file with the correct database credentials.

   *Copy .env.example to .env:*

   Before proceeding, copy the .env.example file to .env to set up your environment variables:

   ```bash
   cp .env.example .env
   ```


3. **Install Dependencies:**

   To install local development packages, including Husky and other Laravel-specific packages, run the following commands:

   ```bash
   npm install
   npm run dev 
   ```

   Run the following command to install the required dependencies using Composer:

   ```bash
   composer install
   ```

4. **Migrate and Seed the Database:**
    Initialize and seed the database with default data using:
    ```bash 
    php artisan migrate
    ```
5. **Initialize The canvas:**
    Initialize and Publish the assets and primary configuration file using the canvas:install Artisan command:
    ```bash
    php artisan canvas:install
    ```

    Now, your project is ready for use. You can use the postman collection provided in the repo to start playing with the API. If you've run the seed command, log in with the provided credentials. Customize and expand your application as needed.
