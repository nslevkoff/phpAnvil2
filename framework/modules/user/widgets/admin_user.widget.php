<?php

/**
* @file
* Admin User Widget
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Admin_User_Widget phpAnvil_Widgets
*
*/

require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.widget.php');

//require_once(PHPANVIL_TOOLS_PATH . 'atList.class.php');
//require_once(PHPANVIL_TOOLS_PATH . 'atLink.class.php');
/**
* Admin User Widget for phpAnvil.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Admin_User_Widget phpAnvil_Widgets
*/

class AdminUserWidget extends BaseWidget {

    public $user;
    
    function __construct() {
        global $phpWebCore;
        parent::__construct();
        
        $this->addProperty('disabled', false);
        
        $this->user = new AdminUserModel($phpWebCore->db);
    }

    public function renderContent() {

        $return = '<div class="adminUserWidget">';
        
        $return = '<div class="adminUserWidget';
        if ($this->disabled) {
            $return .= ' disabled';
        }
        $return .= '">';
        
//        $return .= '<input type="hidden" value="' . $this->user->id .'" name="adminUserWidget' . $this->user->id .'"/>';
        $return .= '<div class="image"><a href="' . $phpAnvil->site->webPath . 'Security/User?i=' . $this->user->id .'"><img src="' . $phpAnvil->site->webPath . 'themes/default/images/iAdminUser.png" alt="iAdminUser.png" /></a></div>';
//        $return .= '<div class="info">';
        $return .= '<div class="title"><a href="' . $phpAnvil->site->webPath . 'Security/User?i=' . $this->user->id .'">' . $this->user->name . '</a></div>';
        
        if ($this->user->role != '') {
            $return .= '<div class="info">Role: ' . $this->user->role . '</div>';
        }
        if ($this->user->company != '') {
            $return .= '<div class="info">Company: ' . $this->user->company . '</div>';
        }
//        $return .= '<div class="email"><a href="mailto:' . $this->user->email . '"><img src="' . $phpAnvil->site->webPath . 'themes/default/images/bEmail.png" alt="bEmail.png" /></a></div>';
        $return .= '<div class="info">';
        
        if ($this->user->email != '') {
            $return .= '<a href="mailto:' . $this->user->email . '"><img src="' . $phpAnvil->site->webPath . 'themes/default/images/bEmail.png" alt="bEmail.png" /></a>';
        }if ($this->user->phone != '') {
            $return .= '<span class="bulletPhone">' . $this->user->phone . '</span>';
        }
        $return .= '</div>';
//        $return .= '</div>';
        $return .= '</div>';

        return $return;
    }


}

?>