<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$uri = JUri::getInstance();
$return = base64_encode($uri);
$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$modMenuId = (int) $this->get('ModMenuId');

JFactory::getDocument()->setBuffer($this->sidebar, 'modules', 'submenu');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task != 'menus.delete' || confirm('<?php echo JText::_('COM_MENUS_MENU_CONFIRM_DELETE', true);?>'))
		{
			Joomla.submitform(task);
		}
	}
</script>

<!-- Form -->
<form class="k-list-layout -koowa-grid" action="<?php echo JRoute::_('index.php?option=com_menus&view=menus');?>" method="get" name="adminForm" id="adminForm">

    <!-- Scopebar -->
    <div class="k-scopebar" id="filter-bar">

        <!-- Filters -->
        <div class="k-scopebar__item k-scopebar__item--fluid">

            <!-- Search toggle button -->
            <button type="button" class="k-toggle-search"><span class="k-icon-magnifying-glass"></span><span class="visually-hidden">Search</span></button>

        </div><!-- .k-scopebar__item--fluid -->

        <!-- Search -->
        <div class="k-scopebar__item k-scopebar__search">
            <div class="k-search__container k-search__container--has-both-buttons">
                <input class="k-search__field" type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
                <button type="submit" class="k-search__button-search" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
                    <span class="k-icon-magnifying-glass"></span>
                </button>
                <button type="button" class="k-search__button-empty" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();">
                    <span>X</span>
                </button>
            </div>
        </div><!-- .k-scopebar__search -->

    </div><!-- .k-scopebar -->

    <!-- Table -->
    <div class="k-table-container">
        <div class="k-table">
            <table class="table--fixed footable select-rows">
                <thead>
                    <tr>
                        <th width="1%">
                            <?php echo JHtml::_('grid.checkall'); ?>
                        </th>
                        <th>
                            <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                        <th>
                            <?php echo JText::_('COM_MENUS_HEADING_LINKED_MODULES'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($this->items as $i => $item) :
                    $canCreate = $user->authorise('core.create',     'com_menus');
                    $canEdit   = $user->authorise('core.edit',       'com_menus');
                    $canChange = $user->authorise('core.edit.state', 'com_menus');
                ?>
                    <tr>
                        <td>
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_menus&view=items&menutype='.$item->menutype) ?> ">
                                <?php echo $this->escape($item->title); ?></a>
                            <p class="small">(<span><?php echo JText::_('COM_MENUS_MENU_MENUTYPE_LABEL') ?></span>
                                <?php if ($canEdit) : ?>
                                    <?php echo '<a href="'.JRoute::_('index.php?option=com_menus&task=menu.edit&id='.$item->id).' title='.$this->escape($item->description).'">'.
                                    $this->escape($item->menutype).'</a>'; ?>)
                                <?php else : ?>
                                    <?php echo $this->escape($item->menutype)?>)
                                <?php endif; ?>
                            </p>
                        </td>
                        <td class="left">
                            <?php if (isset($this->modules[$item->menutype])) : ?>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-small dropdown-toggle" data-toggle="dropdown">
                                        <?php echo JText::_('COM_MENUS_MODULES') ?>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($this->modules[$item->menutype] as &$module) : ?>
                                            <li>
                                                <?php if ($canEdit) : ?>
                                                    <a class="small modal" href="<?php echo JRoute::_('index.php?option=com_modules&task=module.edit&id='.$module->id.'&return='.$return.'&tmpl=component&layout=modal');?>" rel="{handler: 'iframe', size: {x: 1024, y: 450}, onClose: function() {window.location.reload()}}" title="<?php echo JText::_('COM_MENUS_EDIT_MODULE_SETTINGS');?>">
                                                    <?php echo JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position)); ?></a>
                                                <?php else :?>
                                                    <?php echo JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position)); ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                 </div>
                            <?php elseif ($modMenuId) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_modules&task=module.add&eid=' . $modMenuId . '&params[menutype]='.$item->menutype); ?>">
                                <?php echo JText::_('COM_MENUS_ADD_MENU_MODULE'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div><!-- .k-table -->

        <!-- Pagination -->
        <div class="k-table-pagination">
            <?php echo $this->pagination->getListFooter(); ?>
        </div><!-- .k-table-pagination -->

    </div><!-- .k-table-container -->

</form><!-- .k-list-layout -->
