<?php
/**
 * SiteFixture
 *
 */
class SiteFixture extends BaserTestFixture {

/**
 * Name of the object
 *
 * @var string
 */
  public $name = 'Site';
  
/**
 * Records
 *
 * @var array
 */
  public $records = array(
    array(
      'id' => 1,
      'main_site_id' => 0,
      'name' => 'mobile',
      'display_name' => 'モバイル',
      'title' => 'baserCMS inc. [デモ]',
      'alias' => 'm',
      'theme' => '',
      'status' => 1,
      'use_subdomain' => 0,
      'relate_main_site' => 0,
      'created' => '2017-07-25 13:30:05',
      'modified' => '2017-07-25 13:30:05',
    ),
    array(
      'id' => 2,
      'main_site_id' => 0,
      'name' => 'smartphone',
      'display_name' => 'スマートフォン',
      'title' => 'baserCMS inc. [デモ]',
      'alias' => 's',
      'theme' => '',
      'status' => 1,
      'use_subdomain' => 0,
      'relate_main_site' => 0,
      'created' => '2017-07-25 13:30:05',
      'modified' => '2017-07-25 13:30:05',
    ),
  );

}
