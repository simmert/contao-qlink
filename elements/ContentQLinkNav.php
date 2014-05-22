<?php

namespace QLink;


/**
 * Class ContentQLinkNav
 */
class ContentQLinkNav extends \Contao\ContentElement
{
    protected $strTemplate  = 'ce_qlink_nav',
              $pages        = array(),
              $itemKey      = 'item',
              $keepQuery    = false;


    public function generate()
    {
        $this->pages = $this->fetchPages();
        $this->itemKey = trim($this->qlink_item_key);
        $this->keepQuery = $this->qlink_keep_query ? true : false;
        
        if (TL_MODE == 'BE') {
            $template = new \BackendTemplate('be_wildcard');

            $template->wildcard = '';
        
            foreach ($this->pages as &$page) {
                $template->wildcard .= '– ' . $page['label'] . '<br>';
            }
            
            $template->title    = $this->headline;
            $template->id       = $this->id;
            $template->link     = $this->name;
            $template->href     = 'contao/main.php?do=article&amp;table=tl_content&amp;act=edit&amp;id=' . $this->id;

            return $template->parse();
        }
        
        if ($this->pages === null) {
            return '';
        }

        return parent::generate();
    }


    public function compile()
    {
        global $objPage;
        
        $this->Template->navigationMarkup = $this->generateNavigationMarkup($this->pages, $objPage->id);
    }
    
    
    protected function fetchPages()
    {
        $pageIds = deserialize($this->qlink_pages);
        
        if (!is_array($pageIds) || count($pageIds) == 0) {
            return array();
        }
        
        // Prevent SQL injections
        foreach ($pageIds as &$id) {
            $id = intval($id);
        }
        
        $cols = array('id IN (' . implode(',', $pageIds) . ')');

        // Fetch only visible pages if not admin
        if (!BE_USER_LOGGED_IN) {
            $time = time();
            $cols[] = "(start='' OR start<$time) AND (stop='' OR stop>$time) AND published=1";
        }
        
        return $this->parsePages(\PageModel::findBy($cols, null, array('order' => 'pid, sorting')));
    }
    
    
    protected function parsePages(\Contao\Model\Collection $pageCollection=null)
    {
        $pages = array();

        if ($pageCollection === null) {
            return $pages;
        }
        
        while ($pageCollection->next()) {
            $page = $pageCollection->current();

            $pages[] = array(
                'model' => $page,
                'id'    => intval($page->id),
                'label' => $page->title,
            );
        }
        
        // TODO: Assign children
        
        return $pages;
    }
    
    
    protected function generateNavigationMarkup(array $pages, $activeId=null, $level=1)
    {
        $pageCount = count($pages);

        if ($pageCount == 0) {
            return '';
        }

        $markup = '<ul class="level_' . $level . '">';

        for ($i=0; $i<$pageCount; $i++) {
            $page = &$pages[$i];
            $classes = array();

            if ($page['id'] == $activeId) {
                $classes[] = 'active';
            }

            if (isset($page['children'])) {
                $classes[] = 'submenu';
            }
            
            if (self::childIsActive($page, $activeId)) {
                $classes[] = 'trail';
            }

            if ($i == 0) {
                $classes[] = 'first';
            }
            
            if ($i == $itemCount-1) {
                $classes[] = 'last';
            }
            
            $classString = implode(' ', $classes);

            $markup .= '<li class="' . $classString . '">';
            
            if ($page['id'] == $activeId) {
                $markup .= '<span class="' . $classString . '">' . $page['label'] . '</span>';
            } else {
                $markup .= '<a class="' . $classString . '" href="' . $this->buildUrl($page['model']) . '" title="' . $page['label'] . '">' . $page['label'] . '</a>';
            }

            if (isset($page['children'])) {
                $markup .= $this->generateNavigationMarkup($page['children'], $activeId, $level+1);
            }

            $markup .= '</li>';
        }

        $markup .= '</ul>';

        return $markup;
    }
    
    
    protected function childIsActive(array &$page, $activeId)
    {
        if (!isset($page['children']) || !is_array($page['children'])) {
            return false;
        }
        
        foreach ($page['children'] as &$child) {
            if ($child['id'] == $activeId || self::childIsActive($child, $activeId)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    protected function buildUrl(\Contao\PageModel $page=null)
    {
        global $objPage;

        if ($page === null) {
            $page = $objPage;
        }
        
        $queryPath = '';
        
        if ($GLOBALS['TL_CONFIG']['useAutoItem'] && \Input::get('auto_item')) {
            $queryPath = '/' . \Input::get('auto_item');
        } else if ($GLOBALS['TL_CONFIG']['rewriteURL'] && \Input::get($this->itemKey)) {
            $queryPath = '/' . $this->itemKey . '/' . \Input::get($this->itemKey);
        }

        return ampersand($this->generateFrontendUrl($page->row(), $queryPath) . ($this->keepQuery ? '?' . $_SERVER['QUERY_STRING'] : ''));
    }
}
