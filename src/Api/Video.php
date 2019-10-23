<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use Zend\Db\Sql\Predicate\Expression;

/*
 * Pi::api('video', 'video')->getVideo($parameter, $type);
 * Pi::api('video', 'video')->getVideoLight($parameter, $type);
 * Pi::api('video', 'video')->getVideoJson($parameter, $type);
 * Pi::api('video', 'video')->getList($where, $limit, $order);
 * Pi::api('video', 'video')->getListFromId($id);
 * Pi::api('video', 'video')->getListFromIdLight($id);
 * Pi::api('video', 'video')->attributeCount($id);
 * Pi::api('video', 'video')->getDuration($secs);
 * Pi::api('video', 'video')->getAccess($video);
 * Pi::api('video', 'video')->getPayUrl($video);
 * Pi::api('video', 'video')->setAccess($video, $uid);
 * Pi::api('video', 'video')->canonizeVideo($video, $categoryList, $serverList);
 * Pi::api('video', 'video')->canonizeVideoLight($video);
 * Pi::api('video', 'video')->canonizeVideoJson($video, $categoryList, $serverList);
 * Pi::api('video', 'video')->canonizeVideoFilter($video, $categoryList, $serverList);
 * Pi::api('video', 'video')->sitemap();
 */

class Video extends AbstractApi
{
    public function getVideo($parameter, $type = 'id')
    {
        // Get video
        $video = Pi::model('video', $this->getModule())->find($parameter, $type);
        $video = $this->canonizeVideo($video);
        return $video;
    }

    public function getVideoLight($parameter, $type = 'id')
    {
        // Get video
        $video = Pi::model('video', $this->getModule())->find($parameter, $type);
        $video = $this->canonizeVideoLight($video);
        return $video;
    }

    public function getVideoJson($parameter, $type = 'id')
    {
        // Get video
        $video = Pi::model('video', $this->getModule())->find($parameter, $type);
        $video = $this->canonizeVideoJson($video);
        return $video;
    }

    public function getList($where, $limit = '', $order = [])
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Set limit
        $limit = empty($limit) ? $config['view_perpage'] : $limit;

        // Set order
        $order = empty($order) ? $order = ['time_create DESC', 'id DESC'] : $order;

        $columns = ['video' => new Expression('DISTINCT video'), '*'];

        // Get info from link table
        $select = Pi::model('link', $this->getModule())->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowSet = Pi::model('link', $this->getModule())->selectWith($select)->toArray();

        // Make list
        foreach ($rowSet as $id) {
            $videoId[] = $id['video'];
        }

        // Check not empty
        if (!empty($videoId)) {
            $videos = $this->getListFromId($videoId);
        }

        return $videos;
    }

    public function getListFromId($id)
    {
        $list   = [];
        $where  = ['id' => $id, 'status' => 1];
        $select = Pi::model('video', $this->getModule())->select()->where($where);
        $rowSet = Pi::model('video', $this->getModule())->selectWith($select);
        foreach ($rowSet as $row) {
            $list[$row->id] = $this->canonizeVideo($row);
        }
        return $list;
    }

    public function getListFromIdLight($id)
    {
        $list   = [];
        $where  = ['id' => $id, 'status' => 1];
        $select = Pi::model('video', $this->getModule())->select()->where($where);
        $rowSet = Pi::model('video', $this->getModule())->selectWith($select);
        foreach ($rowSet as $row) {
            $list[$row->id] = $this->canonizeVideoLight($row);
        }
        return $list;
    }

    public function attributeCount($id)
    {
        // Get attach count
        $columns = ['count' => new Expression('count(*)')];
        $select  = Pi::model('field_data', $this->getModule())->select()->columns($columns);
        $count   = Pi::model('field_data', $this->getModule())->selectWith($select)->current()->count;

        // Set attach count
        Pi::model('video', $this->getModule())->update(['attribute' => $count], ['id' => $id]);
    }

    public function getDuration($secs)
    {
        $time = '';
        if (!empty($secs)) {
            if ($secs < 3600) {
                $times = [60, 1];
                $time  = '';
                for ($i = 0; $i < 2; $i++) {
                    $tmp = floor($secs / $times[$i]);
                    if ($tmp < 1) {
                        $tmp = '00';
                    } elseif ($tmp < 10) {
                        $tmp = '0' . $tmp;
                    }
                    $time .= $tmp;
                    if ($i < 1) {
                        $time .= ':';
                    }
                    $secs = $secs % $times[$i];
                }
            } else {
                $times = [3600, 60, 1];
                $time  = '';
                for ($i = 0; $i < 3; $i++) {
                    $tmp = floor($secs / $times[$i]);
                    if ($tmp < 1) {
                        $tmp = '00';
                    } elseif ($tmp < 10) {
                        $tmp = '0' . $tmp;
                    }
                    $time .= $tmp;
                    if ($i < 2) {
                        $time .= ':';
                    }
                    $secs = $secs % $times[$i];
                }
            }
        }
        return $time;
    }

    public function getAccess($video)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Get user
        $uid = Pi::user()->getId();

        // Set access
        $access = false;

        // Check system sale
        switch ($config['sale_video']) {
            case 'free':
                $access = true;
                break;

            case 'package':
                if (Pi::service('authentication')->hasIdentity()) {
                    $packages = Pi::registry('planModuleList', 'plans')->read();
                    foreach ($packages as $package) {
                        if ($package['module'] == 'video') {
                            $key = sprintf('video-package-%s-%s', $package['id'], $uid);
                            if (Pi::api('access', 'order')->hasAccess($key, true)) {
                                $access = true;
                                break;
                            }
                        }
                    }
                }
                break;

            case 'single':
                if ($video['sale_price'] > 0) {
                    if (Pi::service('authentication')->hasIdentity()) {
                        $key = sprintf('video-single-%s-%s', $video['id'], $uid);
                        if (Pi::api('access', 'order')->hasAccess($key, true)) {
                            $access = true;
                        }
                    }
                } else {
                    $access = true;
                }
                break;
        }
        return $access;
    }

    public function getPayUrl($video)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Set url
        $url = '#';

        // Check system sale
        switch ($config['sale_video']) {
            case 'package':
                if (Pi::service('module')->isActive('plans')) {
                    $url = Pi::url(
                        Pi::service('url')->assemble(
                            'plans', [
                                'module'     => 'plans',
                                'controller' => 'index',
                                'action'     => 'index',
                            ]
                        )
                    );
                }
                break;

            case 'single':
                if ($video['sale_price'] > 0) {
                    switch ($config['sale_video_single']) {
                        case 'buy':
                            // ToDo
                            break;

                        case 'credit':
                            // ToDo
                            break;

                        case 'mobile':
                            $url = Pi::url(
                                Pi::service('url')->assemble(
                                    'video', [
                                        'module'     => 'video',
                                        'controller' => 'order',
                                        'action'     => 'index',
                                        'video'      => $video['id'],
                                    ]
                                )
                            );
                            break;
                    }
                }
                break;
        }

        return $url;
    }

    public function setAccess($video, $uid = '')
    {
        $uid = !empty($uid) ? $uid : Pi::user()->getId();

        $access = [
            'item_key' => sprintf('video-single-%s-%s', $video['id'], $uid),
        ];

        return Pi::api('access', 'order')->setAccess($access);
    }

    public function canonizeVideo($video, $categoryList = [], $serverList = [])
    {
        // Check
        if (empty($video)) {
            return '';
        }

        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Get category list
        $categoryList = (empty($categoryList)) ? Pi::registry('categoryList', 'video')->read() : $categoryList;

        // Get server list
        $serverList = (empty($serverList)) ? Pi::registry('serverList', 'video')->read() : $serverList;

        // object to array
        $video = $video->toArray();

        // Check title
        if (empty($video['title'])) {
            $video['title'] = sprintf(__('Submitted video on %s'), _date($video['time_create']));
        }

        // Set server information
        $video['server'] = $serverList[$video['video_server']];

        // Make setting
        $video['setting'] = json_decode($video['setting'], true);

        // Set text_summary
        $video['text_summary'] = Pi::service('markup')->render($video['text_summary'], 'html', 'html');

        // Set text_description
        $video['text_description'] = Pi::service('markup')->render($video['text_description'], 'html', 'html');

        // Set times
        $video['time_create_view'] = _date($video['time_create']);
        $video['time_update_view'] = _date($video['time_update']);

        // Set video url
        $video['videoUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'video', [
                    'module'     => $this->getModule(),
                    'controller' => 'watch',
                    'slug'       => $video['slug'],
                ]
            )
        );

        // Set channel url
        $video['channelUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'video', [
                    'module'     => $this->getModule(),
                    'controller' => 'channel',
                    'id'         => $video['uid'],
                ]
            )
        );

        // Set video play url
        if (empty($video['video_url']) && !empty($video['video_file'])) {
            $video['video_url'] = Pi::api('serverService', 'video')->getUrl(
                $video['server']['type'],
                [
                    'serverUrl'         => $video['server']['url'],
                    'serverApplication' => $video['server']['application'],
                    'streamPath'        => $video['video_path'],
                    'streamName'        => $video['video_file'],
                ]
            );
        }

        // Set video duration
        $video['video_duration_view'] = $this->getDuration($video['video_duration']);

        // Set category information
        $video['category'] = json_decode($video['category']);
        foreach ($video['category'] as $category) {
            if (!empty($categoryList[$category]['title'])) {
                $video['categories'][$category]['title'] = $categoryList[$category]['title'];
                $video['categories'][$category]['url']   = Pi::url(
                    Pi::service('url')->assemble(
                        'video', [
                            'module'     => $this->getModule(),
                            'controller' => 'category',
                            'slug'       => $categoryList[$category]['slug'],
                        ]
                    )
                );
            }
        }

        // Set image url
        if ($video['image']) {
            // Set image original url
            $video['originalUrl'] = Pi::url(
                sprintf(
                    'upload/%s/original/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image large url
            $video['largeUrl'] = Pi::url(
                sprintf(
                    'upload/%s/large/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image medium url
            $video['mediumUrl'] = Pi::url(
                sprintf(
                    'upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image thumb url
            $video['thumbUrl'] = Pi::url(
                sprintf(
                    'upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
        } else {
            $video['originalUrl'] = '';
            $video['largeUrl']    = '';
            $video['mediumUrl']   = '';
            $video['thumbUrl']    = '';
        }

        // Set player
        $video['player'] = [
            'type'     => $video['server']['type'] == 'file' ? 'mp4' : 'hls',
            'mimetype' => 'application/x-mpegURL',
            'source'   => [
                [
                    'url'   => $video['video_url'],
                    'title' => 'Normal Quality',
                ],
            ],
            'layout'   => [
                'title'       => $video['title'],
                'posterImage' => $video['largeUrl'],
            ],
        ];

        // return video
        return $video;
    }

    public function canonizeVideoLight($video)
    {
        // Check
        if (empty($video)) {
            return '';
        }

        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Get server list
        $serverList = (empty($serverList)) ? Pi::registry('serverList', 'video')->read() : $serverList;

        // Set server information
        $video['server'] = $serverList[$video['video_server']];

        // object to array
        $video = $video->toArray();

        // Set times
        $video['time_create_view'] = _date($video['time_create']);
        $video['time_update_view'] = _date($video['time_update']);

        // Set video url
        $video['videoUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'video', [
                    'module'     => $this->getModule(),
                    'controller' => 'watch',
                    'slug'       => $video['slug'],
                ]
            )
        );

        // Set channel url
        $video['channelUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'video', [
                    'module'     => $this->getModule(),
                    'controller' => 'channel',
                    'id'         => $video['uid'],
                ]
            )
        );

        // Set image url
        if ($video['image']) {
            // Set image thumb url
            $video['thumbUrl'] = Pi::url(
                sprintf(
                    'upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
        } else {
            $video['thumbUrl'] = '';
        }

        // unset
        unset($video['text_summary']);
        unset($video['text_description']);
        unset($video['seo_title']);
        unset($video['seo_keywords']);
        unset($video['seo_description']);
        unset($video['comment']);
        unset($video['point']);
        unset($video['count']);
        unset($video['favorite']);
        unset($video['attribute']);

        // return video
        return $video;
    }

    public function canonizeVideoJson($video, $categoryList = [], $serverList = [])
    {
        // Check
        if (empty($video)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Get category list
        $categoryList = (empty($categoryList)) ? Pi::registry('categoryList', 'video')->read() : $categoryList;

        // Get server list
        $serverList = (empty($serverList)) ? Pi::registry('serverList', 'video')->read() : $serverList;

        // Set server information
        $video['server'] = $serverList[$video['video_server']];

        // object to array
        $video = $video->toArray();

        // Make setting
        $video['setting'] = json_decode($video['setting'], true);

        // Set text_summary
        $video['text_summary'] = Pi::service('markup')->render($video['text_summary'], 'html', 'html');

        // Set text_description
        $video['text_description'] = Pi::service('markup')->render($video['text_description'], 'html', 'html');

        // Set times
        $video['time_create_view'] = _date($video['time_create']);
        $video['time_update_view'] = _date($video['time_update']);

        // Set video url
        $video['videoUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'video', [
                    'module'     => $this->getModule(),
                    'controller' => 'watch',
                    'slug'       => $video['slug'],
                ]
            )
        );

        // Set channel url
        $video['channelUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'video', [
                    'module'     => $this->getModule(),
                    'controller' => 'channel',
                    'id'         => $video['uid'],
                ]
            )
        );

        // Set video play url
        if (empty($video['video_url']) && !empty($video['video_file'])) {
            $video['video_url'] = Pi::api('serverService', 'video')->getUrl(
                $video['server']['type'],
                [
                    'serverUrl'         => $video['server']['url'],
                    'serverApplication' => $video['server']['application'],
                    'streamPath'        => $video['video_path'],
                    'streamName'        => $video['video_file'],
                ]
            );
        }

        // Set video duration
        $video['video_duration_view'] = $this->getDuration($video['video_duration']);

        // Set category information
        $video['category'] = json_decode($video['category']);
        foreach ($video['category'] as $category) {
            if (!empty($categoryList[$category]['title'])) {
                $video['categories'][$category]['title'] = $categoryList[$category]['title'];
                $video['categories'][$category]['url']   = Pi::url(
                    Pi::service('url')->assemble(
                        'video', [
                            'module'     => $this->getModule(),
                            'controller' => 'category',
                            'slug'       => $categoryList[$category]['slug'],
                        ]
                    )
                );
            }
        }

        // Set image url
        if ($video['image']) {
            // Set image original url
            $video['originalUrl'] = Pi::url(
                sprintf(
                    'upload/%s/original/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image large url
            $video['largeUrl'] = Pi::url(
                sprintf(
                    'upload/%s/large/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image medium url
            $video['mediumUrl'] = Pi::url(
                sprintf(
                    'upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image thumb url
            $video['thumbUrl'] = Pi::url(
                sprintf(
                    'upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
        } else {
            $video['originalUrl'] = '';
            $video['largeUrl']    = '';
            $video['mediumUrl']   = '';
            $video['thumbUrl']    = '';
        }

        // Set category_main information
        $video['categoryMainTitle'] = $categoryList[$video['category_main']]['title'];

        // Set attribute
        if ($video['attribute'] && $config['view_attribute']) {
            $attributes = Pi::api('attribute', 'video')->Video($video['id'], $video['category_main']);
            //$videoSingle['attributes'] = $attributes['all'];
            foreach ($attributes['all'] as $attribute) {
                $video['attribute-' . $attribute['id']] = $attribute['data'];
            }
        }

        // return video
        return $video;
    }

    public function canonizeVideoFilter($video, $categoryList = [], $filterList = [])
    {
        // Check
        if (empty($video)) {
            return '';
        }

        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        // Get category list
        $categoryList = (empty($categoryList)) ? Pi::registry('categoryList', 'video')->read() : $categoryList;

        // Get server list
        $serverList = (empty($serverList)) ? Pi::registry('serverList', 'video')->read() : $serverList;

        // Set server information
        $video['server'] = $serverList[$video['video_server']];

        // object to array
        $video = $video->toArray();

        // Set times
        $video['time_create_view'] = _date($video['time_create']);
        $video['time_update_view'] = _date($video['time_update']);

        // Set video url
        $video['videoUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'video', [
                    'module'     => $this->getModule(),
                    'controller' => 'watch',
                    'slug'       => $video['slug'],
                ]
            )
        );

        // Set channel url
        $video['channelUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'video', [
                    'module'     => $this->getModule(),
                    'controller' => 'channel',
                    'id'         => $video['uid'],
                ]
            )
        );

        // Set category information
        if (isset($video['category']) && !empty($video['category'])) {
            $video['category'] = json_decode($video['category']);
            foreach ($video['category'] as $category) {
                $video['categories'][$category]['id']    = $categoryList[$category]['id'];
                $video['categories'][$category]['title'] = $categoryList[$category]['title'];
                $video['categories'][$category]['url']   = Pi::url(
                    Pi::service('url')->assemble(
                        'video', [
                            'module'     => $this->getModule(),
                            'controller' => 'category',
                            'slug'       => $categoryList[$category]['slug'],
                        ]
                    )
                );
            }
        }

        // Set image url
        if ($video['image']) {
            // Set image original url
            $video['originalUrl'] = Pi::url(
                sprintf(
                    'upload/%s/original/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image large url
            $video['largeUrl'] = Pi::url(
                sprintf(
                    'upload/%s/large/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image medium url
            $video['mediumUrl'] = Pi::url(
                sprintf(
                    'upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
            // Set image thumb url
            $video['thumbUrl'] = Pi::url(
                sprintf(
                    'upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $video['path'],
                    $video['image']
                )
            );
        } else {
            $video['originalUrl'] = '';
            $video['largeUrl']    = '';
            $video['mediumUrl']   = '';
            $video['thumbUrl']    = '';
        }

        // Set attribute
        $filterList = isset($filterList) ? $filterList : Pi::api('attribute', 'video')->filterList();
        $attribute  = Pi::api('attribute', 'video')->filterData($video['id'], $filterList);
        $video      = array_merge($video, $attribute);

        // unset
        unset($video['text_summary']);
        unset($video['text_description']);
        unset($video['seo_title']);
        unset($video['seo_keywords']);
        unset($video['seo_description']);
        unset($video['point']);
        unset($video['count']);
        unset($video['attribute']);
        unset($video['recommended']);
        unset($video['uid']);
        unset($video['setting']);

        // return video
        return $video;
    }

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {

            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll($this->getModule(), 'video');

            // find and import
            $columns = ['id', 'slug', 'status'];
            $select  = Pi::model('video', $this->getModule())->select()->columns($columns);
            $rowSet  = Pi::model('video', $this->getModule())->selectWith($select);
            foreach ($rowSet as $row) {

                // Make url
                $loc = Pi::url(
                    Pi::service('url')->assemble(
                        'video', [
                            'module'     => $this->getModule(),
                            'controller' => 'watch',
                            'slug'       => $row->slug,
                        ]
                    )
                );

                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, $this->getModule(), 'video', $row->id);
            }
        }
    }
}