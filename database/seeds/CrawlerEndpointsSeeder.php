<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class CrawlerEndpoints
 */
class CrawlerEndpointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crawler_endpoints')->insert(
            [
                [
                    'crawler' => 'Batdongsan',
                    'url' => 'https://batdongsan.com.vn/nha-dat-ban',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'Onejav',
                    'url' => 'https://onejav.com/new?page=',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'R18',
                    'url' => 'https://www.r18.com/videos/vod/movies/list/pagesize=30/price=all/sort=new/type=all',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'Truyenchon',
                    'url' => 'http://truyenchon.com/',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%81%82',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%81%8B',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%81%95',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%81%9F',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%81%AA',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%81%AF',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%81%BE',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%82%84',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%82%89',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityProfile',
                    'url' => 'https://xxx.xcity.jp/idol/?kana=%E3%82%8F',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'XCityVideo',
                    'url' => 'https://xxx.xcity.jp/avod/list/?style=simple&page=',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'Xiuren',
                    'url' => 'http://www.xiuren.org/',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'Phodacbiet',
                    'url' => 'https://phodacbiet.info/forums/anh-hotgirl-nguoi-mau.43/',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'crawler' => 'Kissgoddess',
                    'url' => 'https://kissgoddess.com/gallery/',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
            ]
        );
    }
}
