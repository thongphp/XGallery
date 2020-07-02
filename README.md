
XGallery

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jooservices/XGallery/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/jooservices/XGallery/?branch=develop)
![.github/workflows/ci.yml](https://github.com/jooservices/XGallery/workflows/.github/workflows/ci.yml/badge.svg?branch=develop)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/jooservices/XGallery/badges/code-intelligence.svg?b=develop)](https://scrutinizer-ci.com/code-intelligence)


Official website [XCrawler](https://xcrawler.net)


## Getting started

### Required service

 - Redis for queues ( and cache )
 - Supervisor for queues
 - MySQL & Mongodb

### Launch the starter project

*(Assuming you've [installed Laravel](https://laravel.com/docs/6.x/installation))*

Fork this repository, then clone your fork, and run this in your newly created directory:

``` bash
composer install
```

Next you need to make a copy of the `.env.example` file and rename it to `.env` inside your project root.

Run the following command

```
php artisan key:generate
```

You will need provide extra data like Google / Flickr keys

## Licence

This software is licensed under the Apache 2 license, quoted below.

Copyright 2018 SoulEvil.com (https://soulevil.com).

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this project except in compliance with the License. You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
