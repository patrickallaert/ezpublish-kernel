<?php
/**
 * MoveUserGroupSignal class
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\SignalSlot\Signal\UserService;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * MoveUserGroupSignal class
 * @package eZ\Publish\Core\SignalSlot\Signal\UserService
 */
class MoveUserGroupSignal extends Signal
{
    /**
     * User Group
     *
     * @var \eZ\Publish\API\Repository\Values\User\UserGroup
     */
    public $userGroup;

    /**
     * NewParentId
     *
     * @var mixed
     */
    public $newParentId;
}
