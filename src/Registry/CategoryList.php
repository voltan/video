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

namespace Module\Video\Registry;

use Pi;
use Pi\Application\Registry\AbstractRegistry;

/**
 * Category list
 */
class CategoryList extends AbstractRegistry
{
    /** @var string Module name */
    protected $module = 'video';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = [])
    {
        $return = [];
        $where = ['status' => 1];
        $order = ['title ASC', 'id ASC'];
        $select = Pi::model('category', $this->module)->select()->where($where)->order($order);
        $rowset = Pi::model('category', $this->module)->selectWith($select);
        foreach ($rowset as $row) {
            $return[$row->id] = Pi::api('category', 'video')->canonizeCategory($row);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @param array
     */
    public function read()
    {
        $options = [];
        $result = $this->loadData($options);

        return $result;
    }

    /**
     * {@inheritDoc}
     * @param bool $name
     */
    public function create()
    {
        $this->clear('');
        $this->read();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setNamespace($meta = '')
    {
        return parent::setNamespace('');
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        return $this->clear('');
    }
}
