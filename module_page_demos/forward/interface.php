<?php
    /**
     * @author Anastasia Sidak <m0st1ce.nastya@gmail.com>
     *
     * @link https://steamcommunity.com/profiles/76561198038416053
     * @link https://github.com/M0st1ce
     *
     * @license GNU General Public License Version 3
     */

    switch ( get_section( 'section', 'table' ) ) {
    case 'table':
        require MODULES . 'module_page_demos' . '/includes/table.php';
        break;
    case 'match':
        require MODULES . 'module_page_demos' . '/includes/match.php';
        break;
}?>