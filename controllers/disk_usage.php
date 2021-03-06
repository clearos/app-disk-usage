<?php

/**
 * Disk Usage controller.
 *
 * @category   apps
 * @package    disk-usage
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/disk_usage/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\disk_usage\Disk_Usage as Disk_Usage_Class;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Disk Usage controller.
 *
 * @category   apps
 * @package    disk-usage
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/disk_usage/
 */

class Disk_Usage extends ClearOS_Controller
{
    /**
     * Disk Usage default controller
     *
     * @return view
     */

    function index()
    {
        // Load dependencies
        //------------------

        $this->lang->load('disk_usage');
        $this->load->library('disk_usage/Duc');

        // Load view data
        //---------------

        try {
            $data['initialized'] = $this->duc->is_initialized();

            if ($data['initialized']) 
                $data['image'] = $this->duc->get_image($_SERVER['QUERY_STRING']);
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Update database if not initialized
        //-----------------------------------

        if (! $data['initialized'])
            $this->duc->update_database();

        // Load view
        //----------

        $this->page->view_form('disk_usage', $data, lang('disk_usage_app_name'));
    }

    /**
     * Returns image for given path.
     *
     * @param string $encoded_path encoded path
     *
     * @return PNG image
     */

    function get_image($encoded_path)
    {
        $this->load->library('disk_usage/Duc');

        $real_path = base64_decode(strtr($encoded_path, '-_.', '+/='));

        // Validation
        //-----------

        if ($this->duc->validate_path($real_path))
            return;

        echo $this->duc->get_image($real_path);
    }

    /**
     * Gets state of database.
     *
     * @return state
     */

    function get_state()
    {
        // Load dependencies
        //------------------

        $this->load->library('disk_usage/Duc');

        // Run synchronize
        //----------------

        try {
            $data['error_code'] = 0;
            $data['state'] = $this->duc->is_initialized();
        } catch (Exception $e) {
            $data['error_code'] = clearos_exception_code($e);
            $data['error_message'] = clearos_exception_message($e);
        }

        // Return status message
        //----------------------

        $this->output->set_header("Content-Type: application/json");
        $this->output->set_output(json_encode($data));
    }
}
