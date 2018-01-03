<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     HDVinnie
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Decoda\Decoda;

/**
 * Model responsible for managing pages (as on Wordpress)
 *
 *
 */
class Page extends Model
{
    /**
     * Parse content and return valid HTML
     *
     */
    public function getContentHtml()
    {
        $code = new Decoda($this->content);
        $code->defaults();
        $code->removeHook('Censor');
        $code->setXhtml(false);
        $code->setStrict(false);
        $code->setLineBreaks(true);
        return $code->parse();
    }
}
