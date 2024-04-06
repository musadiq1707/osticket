<?php
/*********************************************************************
profile.php

Manage client profile. This will allow a logged-in user to manage
his/her own public (non-internal) information

Peter Rotich <peter@osticket.com>
Jared Hancock <jared@osticket.com>
Copyright (c)  2006-2013 osTicket
http://www.osticket.com

Released under the GNU General Public License WITHOUT ANY WARRANTY.
See LICENSE.TXT for details.

vim: expandtab sw=4 ts=4 sts=4:
$Id: $
 **********************************************************************/
require 'secure.inc.php';

require_once 'class.user.php';
require_once 'config.php';

$user = User::lookup($thisclient->getId());

if ($user && $_POST) {
    $errors = array();

    if (!isset($_POST['plan'])) {
        $errors['err'] = __('Please select plan');
    } else {
        $planId = $_POST['plan'];

        $selectedPlan = null;
        foreach ($plans as $key => $value) {
            if ($value['id'] == $planId) {
                $selectedPlan = $value;
                break;
            }
        }
    }
}

$inc = 'subscription.inc.php';

include(CLIENTINC_DIR.'header.inc.php');
include(CLIENTINC_DIR.$inc);
include(CLIENTINC_DIR.'footer.inc.php');
