<?php
/**
 * Design Cart pCategories – wyświetlanie wybranych kategorii na stronie
 *
 * @author    Design Cart
 * @copyright Design Cart
 * @license   AFL-3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Dc_Pcategories extends Module
{
    const CONFIG_INTRO_TITLE = 'DC_PCATEGORIES_INTRO_TITLE';
    const CONFIG_INTRO_DESC = 'DC_PCATEGORIES_INTRO_DESC';
    const CONFIG_SELECTED_CATEGORIES = 'DC_PCATEGORIES_SELECTED';
    const CONFIG_MODULE_BG = 'DC_PCATEGORIES_MODULE_BG';
    const CONFIG_INTRO_TITLE_SIZE = 'DC_PCATEGORIES_INTRO_TITLE_SIZE';
    const CONFIG_INTRO_TITLE_COLOR = 'DC_PCATEGORIES_INTRO_TITLE_COLOR';
    const CONFIG_INTRO_DESC_SIZE = 'DC_PCATEGORIES_INTRO_DESC_SIZE';
    const CONFIG_INTRO_DESC_COLOR = 'DC_PCATEGORIES_INTRO_DESC_COLOR';
    const CONFIG_INTRO_ALIGN = 'DC_PCATEGORIES_INTRO_ALIGN';
    const CONFIG_GRID_COLS = 'DC_PCATEGORIES_GRID_COLS';
    const CONFIG_TILE_BG = 'DC_PCATEGORIES_TILE_BG';
    const CONFIG_SHOW_IMAGE = 'DC_PCATEGORIES_SHOW_IMAGE';
    const CONFIG_IMAGE_WIDTH = 'DC_PCATEGORIES_IMAGE_WIDTH';
    const CONFIG_CAT_NAME_COLOR = 'DC_PCATEGORIES_CAT_NAME_COLOR';
    const CONFIG_CAT_NAME_SIZE = 'DC_PCATEGORIES_CAT_NAME_SIZE';
    const CONFIG_CAT_ALIGN = 'DC_PCATEGORIES_CAT_ALIGN';

    public function __construct()
    {
        $this->name = 'dc_pcategories';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Design Cart';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => '9.99.99'];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Design Cart pCategories', [], 'Modules.Dcpcategories.Admin');
        $this->description = $this->trans('Pozwala wybrać kategorie ze sklepu i wyświetla je na stronie w konfigurowalnym gridzie.', [], 'Modules.Dcpcategories.Admin');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHome')
            && $this->registerHook('displayWrapperTop')
            && $this->setDefaultConfig();
    }

    public function uninstall()
    {
        $keys = [
            self::CONFIG_INTRO_TITLE, self::CONFIG_INTRO_DESC, self::CONFIG_SELECTED_CATEGORIES,
            self::CONFIG_MODULE_BG, self::CONFIG_INTRO_TITLE_SIZE, self::CONFIG_INTRO_TITLE_COLOR,
            self::CONFIG_INTRO_DESC_SIZE, self::CONFIG_INTRO_DESC_COLOR, self::CONFIG_INTRO_ALIGN,
            self::CONFIG_GRID_COLS, self::CONFIG_TILE_BG, self::CONFIG_SHOW_IMAGE,
            self::CONFIG_IMAGE_WIDTH, self::CONFIG_CAT_NAME_COLOR, self::CONFIG_CAT_NAME_SIZE,
            self::CONFIG_CAT_ALIGN,
        ];
        foreach ($keys as $key) {
            Configuration::deleteByName($key);
        }
        return parent::uninstall();
    }

    protected function setDefaultConfig()
    {
        $defaults = [
            self::CONFIG_INTRO_TITLE => '',
            self::CONFIG_INTRO_DESC => '',
            self::CONFIG_SELECTED_CATEGORIES => '[]',
            self::CONFIG_MODULE_BG => '#f5f5f5',
            self::CONFIG_INTRO_TITLE_SIZE => '24',
            self::CONFIG_INTRO_TITLE_COLOR => '#333333',
            self::CONFIG_INTRO_DESC_SIZE => '14',
            self::CONFIG_INTRO_DESC_COLOR => '#666666',
            self::CONFIG_INTRO_ALIGN => 'center',
            self::CONFIG_GRID_COLS => '4',
            self::CONFIG_TILE_BG => '#ffffff',
            self::CONFIG_SHOW_IMAGE => '1',
            self::CONFIG_IMAGE_WIDTH => '100',
            self::CONFIG_CAT_NAME_COLOR => '#333333',
            self::CONFIG_CAT_NAME_SIZE => '16',
            self::CONFIG_CAT_ALIGN => 'center',
        ];
        foreach ($defaults as $key => $value) {
            Configuration::updateValue($key, $value);
        }
        return true;
    }

    public function getContent()
    {
        $output = '';
        $tab = Tools::getValue('dc_tab', 'intro');

        if (Tools::isSubmit('submitDcPcategoriesIntro')) {
            $output .= $this->processIntro();
        } elseif (Tools::isSubmit('submitDcPcategoriesCategories')) {
            $output .= $this->processCategories();
        } elseif (Tools::isSubmit('submitDcPcategoriesDesign')) {
            $output .= $this->processDesign();
        }

        $output .= $this->renderTabs($tab);
        return $output;
    }

    protected function processIntro()
    {
        $title = [];
        $desc = [];
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $title[$lang['id_lang']] = Tools::getValue('dc_intro_title_' . $lang['id_lang']) ?: '';
            $desc[$lang['id_lang']] = Tools::getValue('dc_intro_desc_' . $lang['id_lang']) ?: '';
        }
        Configuration::updateValue(self::CONFIG_INTRO_TITLE, json_encode($title));
        Configuration::updateValue(self::CONFIG_INTRO_DESC, json_encode($desc));
        return $this->displayConfirmation($this->trans('Ustawienia Intro zapisane.', [], 'Modules.Dcpcategories.Admin'));
    }

    protected function processCategories()
    {
        $selected = Tools::getValue('dc_selected_categories');
        if (!is_array($selected)) {
            $selected = [];
        }
        $selected = array_values(array_filter(array_map('intval', $selected)));
        Configuration::updateValue(self::CONFIG_SELECTED_CATEGORIES, json_encode($selected));
        return $this->displayConfirmation($this->trans('Wybrane kategorie zapisane.', [], 'Modules.Dcpcategories.Admin'));
    }

    protected function processDesign()
    {
        $moduleBg = (string) Tools::getValue('dc_module_bg', '#f5f5f5');
        $introTitleSize = (int) Tools::getValue('dc_intro_title_size', 24);
        $introTitleColor = (string) Tools::getValue('dc_intro_title_color', '#333');
        $introDescSize = (int) Tools::getValue('dc_intro_desc_size', 14);
        $introDescColor = (string) Tools::getValue('dc_intro_desc_color', '#666');
        $introAlign = in_array(Tools::getValue('dc_intro_align'), ['left', 'center']) ? Tools::getValue('dc_intro_align') : 'center';
        $gridCols = (int) Tools::getValue('dc_grid_cols', 4);
        if ($gridCols < 2) {
            $gridCols = 2;
        }
        if ($gridCols > 6) {
            $gridCols = 6;
        }
        $tileBg = (string) Tools::getValue('dc_tile_bg', '#ffffff');
        $showImage = (int) Tools::getValue('dc_show_image', 1);
        $imageWidth = (int) Tools::getValue('dc_image_width', 100);
        if ($imageWidth < 10) {
            $imageWidth = 10;
        }
        if ($imageWidth > 100) {
            $imageWidth = 100;
        }
        $catNameColor = (string) Tools::getValue('dc_cat_name_color', '#333');
        $catNameSize = (int) Tools::getValue('dc_cat_name_size', 16);
        $catAlign = in_array(Tools::getValue('dc_cat_align'), ['left', 'center']) ? Tools::getValue('dc_cat_align') : 'center';

        Configuration::updateValue(self::CONFIG_MODULE_BG, $moduleBg);
        Configuration::updateValue(self::CONFIG_INTRO_TITLE_SIZE, $introTitleSize);
        Configuration::updateValue(self::CONFIG_INTRO_TITLE_COLOR, $introTitleColor);
        Configuration::updateValue(self::CONFIG_INTRO_DESC_SIZE, $introDescSize);
        Configuration::updateValue(self::CONFIG_INTRO_DESC_COLOR, $introDescColor);
        Configuration::updateValue(self::CONFIG_INTRO_ALIGN, $introAlign);
        Configuration::updateValue(self::CONFIG_GRID_COLS, $gridCols);
        Configuration::updateValue(self::CONFIG_TILE_BG, $tileBg);
        Configuration::updateValue(self::CONFIG_SHOW_IMAGE, $showImage);
        Configuration::updateValue(self::CONFIG_IMAGE_WIDTH, $imageWidth);
        Configuration::updateValue(self::CONFIG_CAT_NAME_COLOR, $catNameColor);
        Configuration::updateValue(self::CONFIG_CAT_NAME_SIZE, $catNameSize);
        Configuration::updateValue(self::CONFIG_CAT_ALIGN, $catAlign);

        return $this->displayConfirmation($this->trans('Ustawienia Design zapisane.', [], 'Modules.Dcpcategories.Admin'));
    }

    protected function renderTabs($activeTab)
    {
        $baseUrl = $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name;
        $introUrl = $baseUrl . '&dc_tab=intro';
        $catUrl = $baseUrl . '&dc_tab=categories';
        $designUrl = $baseUrl . '&dc_tab=design';

        $html = '<div class="panel">';
        $html .= '<ul class="nav nav-tabs" role="tablist">';
        $html .= '<li class="nav-item"><a class="nav-link ' . ($activeTab === 'intro' ? 'active' : '') . '" href="' . $introUrl . '">' . $this->trans('Intro', [], 'Modules.Dcpcategories.Admin') . '</a></li>';
        $html .= '<li class="nav-item"><a class="nav-link ' . ($activeTab === 'categories' ? 'active' : '') . '" href="' . $catUrl . '">' . $this->trans('Kategorie', [], 'Modules.Dcpcategories.Admin') . '</a></li>';
        $html .= '<li class="nav-item"><a class="nav-link ' . ($activeTab === 'design' ? 'active' : '') . '" href="' . $designUrl . '">' . $this->trans('Design', [], 'Modules.Dcpcategories.Admin') . '</a></li>';
        $html .= '</ul>';
        $html .= '<div class="tab-content panel-body">';

        if ($activeTab === 'intro') {
            $html .= $this->renderIntroForm();
        } elseif ($activeTab === 'categories') {
            $html .= $this->renderCategoriesGrid();
        } else {
            $html .= $this->renderDesignForm();
        }

        $html .= '</div></div>';
        return $html;
    }

    protected function renderIntroForm()
    {
        $titleJson = Configuration::get(self::CONFIG_INTRO_TITLE);
        $descJson = Configuration::get(self::CONFIG_INTRO_DESC);
        $titles = $titleJson ? json_decode($titleJson, true) : [];
        $descs = $descJson ? json_decode($descJson, true) : [];
        $languages = Language::getLanguages(false);
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $html = '<form method="post" class="form-horizontal">';
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-lg-3">' . $this->trans('Tytuł sekcji (H2)', [], 'Modules.Dcpcategories.Admin') . '</label>';
        $html .= '<div class="col-lg-9">';
        foreach ($languages as $lang) {
            $val = isset($titles[$lang['id_lang']]) ? $titles[$lang['id_lang']] : '';
            $html .= '<div class="input-group"><span class="input-group-addon">' . $lang['iso_code'] . '</span>';
            $html .= '<input type="text" name="dc_intro_title_' . $lang['id_lang'] . '" value="' . $this->escape($val) . '" class="form-control" /></div>';
        }
        $html .= '</div></div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-lg-3">' . $this->trans('Opis', [], 'Modules.Dcpcategories.Admin') . '</label>';
        $html .= '<div class="col-lg-9">';
        foreach ($languages as $lang) {
            $val = isset($descs[$lang['id_lang']]) ? $descs[$lang['id_lang']] : '';
            $html .= '<div class="input-group"><span class="input-group-addon">' . $lang['iso_code'] . '</span>';
            $html .= '<textarea name="dc_intro_desc_' . $lang['id_lang'] . '" class="form-control" rows="4">' . $this->escape($val) . '</textarea></div>';
        }
        $html .= '</div></div>';
        $html .= '<div class="form-group"><div class="col-lg-9 col-lg-offset-3"><button type="submit" name="submitDcPcategoriesIntro" class="btn btn-primary">' . $this->trans('Zapisz', [], 'Admin.Actions') . '</button></div></div>';
        $html .= '</form>';
        return $html;
    }

    public function getCategoriesForAdmin($idLang)
    {
        $categories = Category::getSimpleCategoriesWithParentInfos($idLang);
        if (!is_array($categories)) {
            return [];
        }
        $idRoot = (int) Configuration::get('PS_ROOT_CATEGORY');
        $idHome = (int) Configuration::get('PS_HOME_CATEGORY');
        $byId = [];
        foreach ($categories as $c) {
            $byId[(int) $c['id_category']] = [
                'id_category' => (int) $c['id_category'],
                'name' => $c['name'],
                'id_parent' => (int) $c['id_parent'],
            ];
        }
        $result = [];
        foreach ($categories as $c) {
            $id = (int) $c['id_category'];
            if ($id === $idRoot || $id === $idHome) {
                continue;
            }
            $breadcrumb = $this->getCategoryBreadcrumb($id, $byId, $idRoot, $idHome);
            $result[] = [
                'id_category' => $id,
                'name' => $c['name'],
                'breadcrumb' => $breadcrumb,
            ];
        }
        return $result;
    }

    protected function getCategoryBreadcrumb($idCategory, array $byId, $idRoot, $idHome)
    {
        $parts = [];
        $id = $idCategory;
        while (isset($byId[$id]) && $id !== $idRoot && $id !== $idHome) {
            array_unshift($parts, $byId[$id]['name']);
            $id = $byId[$id]['id_parent'];
        }
        if (count($parts) <= 1) {
            return $this->trans('Kategoria główna', [], 'Modules.Dcpcategories.Admin');
        }
        array_pop($parts);
        return implode(' » ', $parts);
    }

    protected function renderCategoriesGrid()
    {
        $idLang = (int) $this->context->language->id;
        $categories = $this->getCategoriesForAdmin($idLang);

        $html = '';
        if (!isset($this->context->controller->ajax) || !$this->context->controller->ajax) {
            $html .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />';
        }
        $selectedJson = Configuration::get(self::CONFIG_SELECTED_CATEGORIES);
        $selected = $selectedJson ? json_decode($selectedJson, true) : [];
        if (!is_array($selected)) {
            $selected = [];
        }
        $selected = array_map('intval', $selected);

        $orderedCategories = [];
        foreach ($selected as $id) {
            foreach ($categories as $cat) {
                if ((int) $cat['id_category'] === $id) {
                    $orderedCategories[] = $cat;
                    break;
                }
            }
        }
        foreach ($categories as $cat) {
            if (!in_array((int) $cat['id_category'], $selected)) {
                $orderedCategories[] = $cat;
            }
        }
        $categories = $orderedCategories;

        $html .= '<form method="post" id="dc-pcategories-form">';
        $html .= '<p class="help-block">' . $this->trans('Kliknij kafelek, aby dodać lub usunąć kategorię. Przeciągnij kafelek, aby zmienić kolejność wyświetlania na stronie.', [], 'Modules.Dcpcategories.Admin') . '</p>';
        $html .= '<div id="dc-pcategories-tiles-row" class="row" style="margin-bottom:20px; margin-left:-8px; margin-right:-8px;">';
        foreach ($categories as $cat) {
            $id = (int) $cat['id_category'];
            $checked = in_array($id, $selected);
            $bgClass = $checked ? 'dc-tile-selected' : 'dc-tile-unselected';
            $iconClass = $checked ? 'fa fa-check-circle' : 'fa fa-plus-circle';
            $iconColor = $checked ? '#2e7d32' : '#757575';
            $bgColor = $checked ? '#e8f5e9' : '#e9ecef';
            $html .= '<div class="col-md-4 col-lg-3 col-xl-2 dc-admin-tile ' . $bgClass . '" data-id="' . $id . '" style="margin: 0 8px 16px 8px; cursor:pointer; padding:12px; border-radius:8px; border:1px solid #dee2e6; min-height:160px; display:flex; flex-direction:column; align-items:center; justify-content:space-between; background-color:' . $bgColor . ';">';
            $html .= '<div class="dc-tile-header" style="font-weight:600; text-align:center; width:100%; font-size:13px;">' . $this->escape($cat['name']) . '</div>';
            $html .= '<div class="dc-tile-icon" style="flex:1; display:flex; align-items:center;"><i class="' . $iconClass . ' dc-tile-fa" style="font-size:42px; color:' . $iconColor . ';" aria-hidden="true"></i></div>';
            $html .= '<div class="dc-tile-footer" style="font-size:11px; color:#666; text-align:center; width:100%;">' . $this->escape($cat['breadcrumb']) . '</div>';
            $html .= '<input type="checkbox" name="dc_selected_categories[]" value="' . $id . '" ' . ($checked ? 'checked="checked"' : '') . ' style="display:none" class="dc-tile-cb" />';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '<button type="submit" name="submitDcPcategoriesCategories" class="btn btn-primary">' . $this->trans('Zapisz wybrane kategorie', [], 'Modules.Dcpcategories.Admin') . '</button>';
        $html .= '</form>';

        $html .= '<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>';
        $html .= '<script>
            (function(){
            var form = document.getElementById("dc-pcategories-form");
            if (!form) return;
            var row = document.getElementById("dc-pcategories-tiles-row");
            if (row && typeof Sortable !== "undefined") {
            new Sortable(row, {
                animation: 150,
                handle: ".dc-admin-tile",
                ghostClass: "dc-tile-ghost",
                chosenClass: "dc-tile-chosen"
            });
            }
            var tiles = form.querySelectorAll(".dc-admin-tile");
            tiles.forEach(function(tile) {
            tile.addEventListener("click", function(e) {
                if (e.target.tagName === "INPUT") return;
                var cb = tile.querySelector(".dc-tile-cb");
                var icon = tile.querySelector(".dc-tile-icon .dc-tile-fa");
                if (!icon) return;
                cb.checked = !cb.checked;
                tile.classList.toggle("dc-tile-unselected", !cb.checked);
                tile.classList.toggle("dc-tile-selected", cb.checked);
                tile.style.backgroundColor = cb.checked ? "#e8f5e9" : "#e9ecef";
                icon.className = (cb.checked ? "fa fa-check-circle" : "fa fa-plus-circle") + " dc-tile-fa";
                icon.style.color = cb.checked ? "#2e7d32" : "#757575";
            });
            });
            })();
            </script>';
        $html .= '<style>.dc-tile-ghost { opacity: 0.4; } .dc-tile-chosen { opacity: 0.9; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }</style>';
        return $html;
    }

    protected function renderDesignForm()
    {
        $moduleBg = Configuration::get(self::CONFIG_MODULE_BG);
        $introTitleSize = (int) Configuration::get(self::CONFIG_INTRO_TITLE_SIZE);
        $introTitleColor = Configuration::get(self::CONFIG_INTRO_TITLE_COLOR);
        $introDescSize = (int) Configuration::get(self::CONFIG_INTRO_DESC_SIZE);
        $introDescColor = Configuration::get(self::CONFIG_INTRO_DESC_COLOR);
        $introAlign = Configuration::get(self::CONFIG_INTRO_ALIGN);
        $gridCols = (int) Configuration::get(self::CONFIG_GRID_COLS);
        $tileBg = Configuration::get(self::CONFIG_TILE_BG);
        $showImage = (int) Configuration::get(self::CONFIG_SHOW_IMAGE);
        $imageWidth = (int) Configuration::get(self::CONFIG_IMAGE_WIDTH);
        $catNameColor = Configuration::get(self::CONFIG_CAT_NAME_COLOR);
        $catNameSize = (int) Configuration::get(self::CONFIG_CAT_NAME_SIZE);
        $catAlign = Configuration::get(self::CONFIG_CAT_ALIGN);

        $html = '<form method="post" class="form-horizontal">';
        $html .= '<h4>' . $this->trans('Ogólnie moduł', [], 'Modules.Dcpcategories.Admin') . '</h4>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Tło całego modułu', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-4"><input type="color" name="dc_module_bg" value="' . $this->escape($moduleBg) . '" class="form-control" style="height:38px" /></div><div class="col-lg-2"><input type="text" name="dc_module_bg_txt" value="' . $this->escape($moduleBg) . '" class="form-control dc-color-txt" data-for="dc_module_bg" /></div></div>';

        $html .= '<hr><h4>' . $this->trans('Wygląd Intro', [], 'Modules.Dcpcategories.Admin') . '</h4>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Rozmiar czcionki tytułu (px)', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-2"><input type="number" name="dc_intro_title_size" value="' . $introTitleSize . '" min="12" max="72" class="form-control" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Kolor tytułu', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-4"><input type="color" name="dc_intro_title_color" value="' . $this->escape($introTitleColor) . '" class="form-control" style="height:38px" /></div><div class="col-lg-2"><input type="text" value="' . $this->escape($introTitleColor) . '" class="form-control dc-color-txt" data-for="dc_intro_title_color" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Rozmiar czcionki opisu (px)', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-2"><input type="number" name="dc_intro_desc_size" value="' . $introDescSize . '" min="11" max="24" class="form-control" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Kolor opisu', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-4"><input type="color" name="dc_intro_desc_color" value="' . $this->escape($introDescColor) . '" class="form-control" style="height:38px" /></div><div class="col-lg-2"><input type="text" value="' . $this->escape($introDescColor) . '" class="form-control dc-color-txt" data-for="dc_intro_desc_color" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Wyrównanie intro', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-4"><select name="dc_intro_align" class="form-control"><option value="center"' . ($introAlign === 'center' ? ' selected="selected"' : '') . '>' . $this->trans('Wyśrodkowane', [], 'Modules.Dcpcategories.Admin') . '</option><option value="left"' . ($introAlign === 'left' ? ' selected="selected"' : '') . '>' . $this->trans('Do lewej', [], 'Modules.Dcpcategories.Admin') . '</option></select></div></div>';

        $html .= '<hr><h4>' . $this->trans('Wygląd grid kategorii', [], 'Modules.Dcpcategories.Admin') . '</h4>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Kategorii w rzędzie', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-2"><input type="number" name="dc_grid_cols" value="' . $gridCols . '" min="2" max="6" class="form-control" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Tło pojedynczego kafelka', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-4"><input type="color" name="dc_tile_bg" value="' . $this->escape($tileBg) . '" class="form-control" style="height:38px" /></div><div class="col-lg-2"><input type="text" value="' . $this->escape($tileBg) . '" class="form-control dc-color-txt" data-for="dc_tile_bg" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Pokazuj zdjęcie kategorii', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-4"><select name="dc_show_image" class="form-control"><option value="1"' . ($showImage ? ' selected="selected"' : '') . '>' . $this->trans('Tak', [], 'Admin.Global') . '</option><option value="0"' . (!$showImage ? ' selected="selected"' : '') . '>' . $this->trans('Nie', [], 'Admin.Global') . '</option></select></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Szerokość zdjęcia (%)', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-2"><input type="number" name="dc_image_width" value="' . $imageWidth . '" min="10" max="100" class="form-control" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Kolor nazwy kategorii', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-4"><input type="color" name="dc_cat_name_color" value="' . $this->escape($catNameColor) . '" class="form-control" style="height:38px" /></div><div class="col-lg-2"><input type="text" value="' . $this->escape($catNameColor) . '" class="form-control dc-color-txt" data-for="dc_cat_name_color" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Rozmiar nazwy kategorii (px)', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-2"><input type="number" name="dc_cat_name_size" value="' . $catNameSize . '" min="12" max="28" class="form-control" /></div></div>';
        $html .= '<div class="form-group"><label class="control-label col-lg-3">' . $this->trans('Wyrównanie nazwy kategorii', [], 'Modules.Dcpcategories.Admin') . '</label><div class="col-lg-4"><select name="dc_cat_align" class="form-control"><option value="center"' . ($catAlign === 'center' ? ' selected="selected"' : '') . '>' . $this->trans('Wyśrodkowane', [], 'Modules.Dcpcategories.Admin') . '</option><option value="left"' . ($catAlign === 'left' ? ' selected="selected"' : '') . '>' . $this->trans('Do lewej', [], 'Modules.Dcpcategories.Admin') . '</option></select></div></div>';

        $html .= '<div class="form-group"><div class="col-lg-9 col-lg-offset-3"><button type="submit" name="submitDcPcategoriesDesign" class="btn btn-primary">' . $this->trans('Zapisz', [], 'Admin.Actions') . '</button></div></div>';
        $html .= '</form>';

        $html .= '<script>
(function(){
  document.querySelectorAll(".dc-color-txt").forEach(function(txt) {
    var forId = txt.getAttribute("data-for");
    var colorInp = document.querySelector("[name=\"" + forId + "\"]");
    if (!colorInp) return;
    function syncToTxt() { txt.value = colorInp.value; }
    function syncToColor() { colorInp.value = txt.value; }
    colorInp.addEventListener("input", syncToTxt);
    txt.addEventListener("input", syncToColor);
  });
})();
</script>';
        return $html;
    }

    protected function escape($s)
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }

    public function hookDisplayHome($params)
    {
        $selectedJson = Configuration::get(self::CONFIG_SELECTED_CATEGORIES);
        $selected = $selectedJson ? json_decode($selectedJson, true) : [];
        if (!is_array($selected) || empty($selected)) {
            return '';
        }

        $idLang = (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;
        $categories = [];
        $link = $this->context->link;

        foreach ($selected as $idCategory) {
            $idCategory = (int) $idCategory;
            $category = new Category($idCategory, $idLang, $idShop);
            if (!Validate::isLoadedObject($category)) {
                continue;
            }
            $url = $link->getCategoryLink($category);
            $name = $category->name;
            if (is_array($name)) {
                $name = $name[$idLang] ?? reset($name);
            }
            $imageUrl = '';
            $thumbPath = _PS_CAT_IMG_DIR_ . $idCategory . '_thumb.jpg';
            if (file_exists($thumbPath)) {
                $base = rtrim($link->getBaseLink($idShop), '/');
                $imageUrl = $base . '/img/c/' . $idCategory . '_thumb.jpg';
            }
            $categories[] = [
                'id_category' => $idCategory,
                'name' => $name,
                'url' => $url,
                'image' => $imageUrl,
            ];
        }

        $introTitleJson = Configuration::get(self::CONFIG_INTRO_TITLE);
        $introDescJson = Configuration::get(self::CONFIG_INTRO_DESC);
        $titles = $introTitleJson ? json_decode($introTitleJson, true) : [];
        $descs = $introDescJson ? json_decode($introDescJson, true) : [];
        $introTitle = isset($titles[$idLang]) ? $titles[$idLang] : (isset($titles[Configuration::get('PS_LANG_DEFAULT')]) ? $titles[Configuration::get('PS_LANG_DEFAULT')] : '');
        $introDesc = isset($descs[$idLang]) ? $descs[$idLang] : (isset($descs[Configuration::get('PS_LANG_DEFAULT')]) ? $descs[Configuration::get('PS_LANG_DEFAULT')] : '');

        $gridCols = (int) Configuration::get(self::CONFIG_GRID_COLS);
        if ($gridCols < 2) {
            $gridCols = 2;
        }
        if ($gridCols > 6) {
            $gridCols = 6;
        }
        $gapRem = 1.5 * ($gridCols - 1);
        $tileFlexBasis = 'calc((100% - ' . $gapRem . 'rem) / ' . $gridCols . ')';

        $this->context->smarty->assign([
            'dc_categories' => $categories,
            'dc_intro_title' => $introTitle,
            'dc_intro_desc' => $introDesc,
            'dc_module_bg' => Configuration::get(self::CONFIG_MODULE_BG),
            'dc_intro_title_size' => (int) Configuration::get(self::CONFIG_INTRO_TITLE_SIZE),
            'dc_intro_title_color' => Configuration::get(self::CONFIG_INTRO_TITLE_COLOR),
            'dc_intro_desc_size' => (int) Configuration::get(self::CONFIG_INTRO_DESC_SIZE),
            'dc_intro_desc_color' => Configuration::get(self::CONFIG_INTRO_DESC_COLOR),
            'dc_intro_align' => Configuration::get(self::CONFIG_INTRO_ALIGN),
            'dc_grid_cols' => $gridCols,
            'dc_tile_flex_basis' => $tileFlexBasis,
            'dc_tile_bg' => Configuration::get(self::CONFIG_TILE_BG),
            'dc_show_image' => (int) Configuration::get(self::CONFIG_SHOW_IMAGE),
            'dc_image_width' => (int) Configuration::get(self::CONFIG_IMAGE_WIDTH),
            'dc_cat_name_color' => Configuration::get(self::CONFIG_CAT_NAME_COLOR),
            'dc_cat_name_size' => (int) Configuration::get(self::CONFIG_CAT_NAME_SIZE),
            'dc_cat_align' => Configuration::get(self::CONFIG_CAT_ALIGN),
        ]);

        return $this->fetch('module:dc_pcategories/views/templates/hook/dc_pcategories.tpl');
    }

    public function hookDisplayWrapperTop($params)
    {
        return $this->hookDisplayHome($params);
    }
}
