<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Form\Element;

use Pi;
use Zend\Form\Element\Select;

class Service extends Select
{
    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            // Get topic list
            $columns = ['id', 'title'];
            $where = ['status' => 1];
            $order = ['time_create DESC', 'title ASC'];
            $limit = 250;
            $list = ['' => ''];
            $select = Pi::model('video', 'video')->select()->columns($columns)->where($where)->order($order)->limit($limit);
            $rowset = Pi::model('video', 'video')->selectWith($select);
            foreach ($rowset as $row) {
                $list[$row->id] = $row->title;
            }
            $this->valueOptions = $list;
        }
        return $this->valueOptions;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $this->Attributes = [
            'size'     => 1,
            'multiple' => 0,
            'class'    => 'form-control',
        ];
        // check form size
        if (isset($this->attributes['size'])) {
            $this->Attributes['size'] = $this->attributes['size'];
        }
        // check form multiple
        if (isset($this->attributes['multiple'])) {
            $this->Attributes['multiple'] = $this->attributes['multiple'];
        }
        return $this->Attributes;
    }
}