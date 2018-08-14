<p align="center">
    <img src="https://i.imgur.com/q4awiMm.png" alt="UNIT3D Logo">
</p>
<p align="center">
    <b>A Special Thanks To All Our <a href="https://github.com/UNIT3D/UNIT3D/graphs/contributors">Contributors</a></b>
</p>
<hr>

<p align="center">
<a href="http://laravel.com"><img src="https://img.shields.io/badge/Laravel-5.6.33-f4645f.svg" /></a> <a href="https://github.com/UNIT3D/UNIT3D/blob/master/LICENSE"><img src="https://img.shields.io/aur/license/yaourt.svg" /></a>
<a href="https://trello.com/b/tUDsQ0Gs/unit3d"><img src="https://img.shields.io/badge/Trello-HERE-green.svg" /></a>
<a class="badge-align" href="https://www.codacy.com/app/HDVinnie/UNIT3D?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=UNIT3D/UNIT3D&amp;utm_campaign=Badge_Grade"><img src="https://api.codacy.com/project/badge/Grade/6c6c6c940aec420e987ea82adea272ee"/></a> <a href="https://discord.gg/Yk4NBUU"><img alt="Discord chat" src="https://cdn.rawgit.com/Hyleus/237f9903320939eb4f7615633a8fb221/raw/dca104edf34eabaca1016e633f06a52a346a7700/chat-Discord-blue.svg" /></a>
<a href="https://www.patreon.com/UNIT3D"><img src="https://img.shields.io/badge/patreon-Support%20UNIT3D-orange.svg"></a>	
</p>


## Table of Contents

1. [Introduction](#introduction)
2. [Some Features](#features)
3. [Requirements](#requirements)
4. [Installation](#installation)
5. [Packages](#packages)
6. [Security](#security)
7. [Contributing](#contributing)
8. [License](#license)
9. [Homestead (For local developement)](#homestead)
10. [Demo](#demo)
11. [Donate](#donate)


## <a name="introduction"></a> Introduction

I have been developing a Nex-Gen Torrent Tracker Software called "UNIT3D." This is a PHP software based off the lovely Laravel Framework -- currently Laravel Framework 5.6.33, MySQL Strict Mode Compliant and PHP 7.1 Ready. The code is well-designed and follows the PSR-2 coding style. It uses a MVC Architecture to ensure clarity between logic and presentation. As a hashing algorithm of Bcrypt or Argon2 is used, to ensure a safe and proper way to store the passwords for the users. A lightweight Blade Templating Engine. Caching System Supporting: "apc,” "array,” "database,” "file," "memcached," and "redis" methods. Eloquent and much more!

## <a name="features"></a> Some Features

UNIT3D currently offers the following features:
  - Internal Forums System
  - Staff Dashboard
  - Faceted Ajax Torrent Search System
  - BON Store
  - Torrent Request Section with BON Bounties
  - Freeleech System
  - Double Upload System
  - Featured Torrents System
  - Polls System
  - Extra-Stats
  - PM System
  - Multilingual Support
  - TwoStep Auth System
  - DB + Files Backup Manager
  - and MUCH MORE!

## <a name="requirements"></a> Requirements

- A Web server (NGINX is recommended)
- PHP 7.1.3 + is required (7.2 for Argon2 Support)
- Dependencies for PHP,
  -   php-curl -> This is specifically needed for the various APIs we have running.
  -   php-zip -> This is required for the Backup Manager.
- Crontab access
- A Redis server
- MySQL 5.7 + or MariaDB 10.2 +
- TheMovieDB API Key: https://www.themoviedb.org/documentation/api
- TheTVDB API Key: https://api.thetvdb.com/swagger
- OMDB API Key: http://www.omdbapi.com/
- A decent dedicated server. Dont try running this on some crappy server!
<pre>
Processor: Intel  Xeon E3-1245v2 -
Cores/Threads: 4c/8t
Frequency: 3.4GHz /3.8GHz
RAM: 32GB DDR3 1333 MHz
Disks: SoftRaid  2x240 GB   SSD
Bandwidth: 250 Mbps
Traffic: Unlimited
<b>Is Under $50 A Month</b>
</pre>

## <a name="installation"></a> Installation

**A UNIT3D Installer has been released by Poppabear.**

Check it out here: https://github.com/ServNX/UNIT3D-INSTALLER

If you rather setup UNIT3D manually you can follow the instructions here: https://github.com/HDInnovations/UNIT3D/wiki/Manual-Install
 
## <a name="packages"></a> Packages
Here are some packages that are built for UNIT3D.
- [An artisan package to import a XBTIT database into UNIT3D](https://github.com/pxgamer/xbtit-to-unit3d).
- [An artisan package to import a Gazelle database into UNIT3D](https://github.com/pxgamer/gazelle-to-unit3d).
- [An artisan package to import a U-232 database into UNIT3D](https://github.com/pxgamer/u232-to-unit3d).

## <a name="security"></a> Security

If you discover any security related issues, please email unit3d@protonmail.com instead of using the issue tracker.

## <a name="contributing"></a> Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## <a name="license"></a> License

UNIT3D is open-sourced software licensed under the [GNU General Public License v3.0](https://github.com/HDVinnie/UNIT3D/blob/master/LICENSE).

<b> As per license do not remove the license from sourcecode or from footer in /resources/views/partials/footer.blade.php</b>

## <a name="homestead"></a> Homestead (for local developement)

For instructions on how to use Homestead for running UNIT3D in a local development environment check here: https://github.com/HDInnovations/UNIT3D/wiki/Homestead

## <a name="demo"></a> Demo

URL: https://unit3d.org

Username: UNIT3D

Password: UNIT3D

## <a name="donate"></a> Support UNIT3D

You can support my work if you are enjoying UNIT3D, this really keeps me up for fixing problems and adding new features. Also helps pay for the demo server + domain. Plus some beer to keep me sane. Some folks have asked me if it's possible to do a one-time donation, or if I accept cryptocurrency. Yes, and yes!

	
Patreon:<a href="https://www.patreon.com/UNIT3D"><img src="https://img.shields.io/badge/patreon-Support%20UNIT3D-orange.svg"></a>

Bitcoin (BTC) - 3HUVkv3Q8b5nbxa9DtXG1dm4RdTJaTFRfc

Bitcoin Cash (BCH) - qp3wgpnwzpj4v9sq90wflsca8p5s75glrvga9tweu2

Ether (ETH) - 0x5eFF42F65234aD9c6A0CA5B9495f3c6D205bBC27

Litecoin (LTC) - MDLKyHzupt1mchuo8mrjW9mihkKp1LD4nG
