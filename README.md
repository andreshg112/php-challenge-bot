<p align="center"><img height="188" width="198" src="https://botman.io/img/botman.png"></p>
<h1 align="center">MoneyBot (Powered by BotMan)</h1>

# Installation instructions

## Install and start Docker

[Get Docker](https://docs.docker.com/get-docker).

## Run the next commands in your terminal

Before doing that, please make sure that ports 80 (Apache), 3306 (MySQL), and 6379 (Redis) are available.

```bash
git clone https://github.com/andreshg112/php-challenge-bot.git

cd php-challenge-bot

# Install and run
bash ./install.sh

# Run tests
./vessel test

# Stop the environment when you have finished
./vessel stop
```

## Add environment variables

Please, add the provided values for `MAIL_PASSWORD`, `AMDOREN_API_KEY`, and `SENTRY_LARAVEL_DSN`. The last one is optional, but it allows me to check and fix bugs.

## Open your browswer

Go to the [localhost:80](http://localhost) and click on the mail icon in the inferior right corner of the screen.

# Chatbot commands

1.  `convert X USD to COP` to convert X United States Dollars to Colombian Pesos. You can use any currency code listed [here](https://www.amdoren.com/currency-list).

2.  `signup` to register your information. I suggest you use a Gmail based account and check Spam if needed. Outlook is not receiving the password email.

3.  `login` to enter and start registering transactions.

4.  `deposit X USD` to put X amount of money in your account. The currency code is optional. If specified, the amount will be converted to your default currency specified when signing up.

5.  `withdraw X USD` to extract X amount of money from your account. The currency code is optional. If specified, the amount will be converted to your default currency specified when signing up.

6.  `balance` to see your current account balance.

7.  `logout` to forget the user from the session.
