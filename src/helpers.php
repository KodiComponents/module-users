<?php

/**
 * @param string|array $action
 *
 * @return bool
 */
function acl_check($action)
{
    return BackendGate::allows($action);
}