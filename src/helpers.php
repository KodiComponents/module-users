<?php

/**
 * @param string|array $action
 *
 * @return bool
 */
function acl_check($action)
{
    return Gate::allows($action);
}
