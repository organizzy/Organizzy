<?php
/**
 * Organizzy
 * Copyright (C) 2014 Organizzy Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class OTabView extends CWidget {

    public $id = '';

    public $activePage = null;

    private $pages = [];
    private $currentPage = null;
    private $tabCount = 0;


    public function init() {
        if ($this->activePage == null && isset($_GET['tab'])) {
            $this->activePage = $_GET['tab'];
        }
    }

    /**
     * begin tab page
     *
     * @param string $title
     * @param array $htmlOptions
     * @param null|string $pageId
     */
    public function beginPage($title, $htmlOptions = [], $pageId = null) {
        if ($pageId == null) {
            $pageId = $this->id . '-tab' . $this->tabCount;
        }

        $this->currentPage = [
            'title' => $title,
            'id' => $pageId,
            'pageOptions' => $htmlOptions,
        ];

        ob_start();
    }

    /**
     * end tab page
     */
    public function endPage() {
        $currentPage = $this->currentPage;
        $currentPage['content'] = ob_get_clean();
        $currentPage['active'] = ($this->activePage && $this->activePage == $currentPage['id']) ||
            ($this->activePage == null && $this->tabCount == 0);
        $this->pages[] = $currentPage;
        $this->tabCount++;
    }

    /**
     * write tab pages
     * called on Controller::endWidget
     */
    public function run() {
        if ($this->tabCount == 1) {
            $this->pages[0]['active'] = true;
        }

        else {
            echo '<div class="tab-view"><div class="selector" id="', $this->id, '">';
            foreach ($this->pages as $p) {
                $options = ['id' => $p['id'] . '-selector'];
                if ($p['active']) $options['class'] = 'active';
                echo CHtml::link($p['title'], '#' . $p['id'], $options);
            }
            echo '</div>';

        }


        foreach ($this->pages as $p) {
            $pageOptions = is_array($p['pageOptions']) ? $p['pageOptions'] : [];
            $pageOptions['id'] = $p['id'];
            isset($pageOptions['class']) || $pageOptions['class'] = '';
            $pageOptions['class'] .= ' tab-page';
            ($p['active']) && $pageOptions['class'] .= ' active';

            echo CHtml::tag('div', $pageOptions, $p['content']);
        }
    }

} 