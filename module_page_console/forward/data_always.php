<?php
isset( $_SESSION['steamid32'] ) && isset( $_SESSION['user_admin'] ) && $Modules->set_sidebar_select('module_page_console', ["href" =>"?page=console", "open_new_tab" =>"0", "icon_group" =>"zmdi", "icon_category" =>"", "icon" =>"laptop", "name" =>"_Console", "sidebar_directory" =>""]);
