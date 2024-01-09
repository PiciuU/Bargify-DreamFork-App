<p align="center"><a href="https://dreamfork.dream-speak.pl" target="_blank"><img src="https://raw.githubusercontent.com/PiciuU/DreamFork-PHP-Framework/af10e176afad1dab39f368689a5448069a3532f7/resources/icons/logo.svg" width="200" alt="Dreamfork Logo"></a></p>

# Bargify Server

Bargify utilizes the [Dreamfork](https://github.com/PiciuU/DreamFork-PHP-Framework) framework as its backend API. The framework is specifically designed to handle API requests, as reflected in the `RouteServiceProvider` file.

### Configuration

Apart from the default framework settings in the `env` file, the following additional configurations are defined:

- **SCRAPER_SECRET_KEY**: Required key for accessing the scraper service. The scraper fetches products registered in the application from their original websites.

- **SEND_WEB_PUSH_NOTIFICATIONS**: Defines whether web push notifications should be sent to application users.

- **VAPID_PUBLIC_KEY** and **VAPID_PRIVATE_KEY**: Public and private keys for VAPID, used by the web push notification service. Ensure the frontend configuration in the `client` folder uses the same public key as the API configuration.


> If you're unable to generate VAPID keys manually, you can do so [here](https://www.attheminute.com/vapid-key-generator).

#### General Purpose of the API

The application relies on a properly connected API for frontend functionality. Determine the API's accessible address and provide it in the client's configuration in the `client` folder. Assuming the server was launched using the command `php -S localhost:8000 -t public/`, the address would be http://localhost:8000/.

The application is designed to trigger the scraper every 5 minutes to update information about registered products. To automate this process, defining a CRON job is recommended, considering the previously set secret key for the scraper. An example CRON job might look like this:

```bash
*/5 * * * * curl --silent http://localhost:8000/scrap?secret=defined_key_in_configuration > /dev/null
```