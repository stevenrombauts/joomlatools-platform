<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$this->hiddenFieldsets = array();
$this->hiddenFieldsets[0] = 'basic-limited';
$this->configFieldsets = array();
$this->configFieldsets[0] = 'editorConfig';

// Create shortcut to parameters.
$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;
$assoc = JLanguageAssociations::isEnabled();

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$params = json_decode($params);
$editoroptions = isset($params->show_publishing_options);

if (!$editoroptions)
{
    $params->show_publishing_options = '1';
    $params->show_article_options = '1';
    $params->show_urls_images_backend = '0';
    $params->show_urls_images_frontend = '0';
}

// Check if the article uses configuration settings besides global. If so, use them.
if (isset($this->item->attribs['show_publishing_options']) && $this->item->attribs['show_publishing_options'] != '')
{
    $params->show_publishing_options = $this->item->attribs['show_publishing_options'];
}

if (isset($this->item->attribs['show_article_options']) && $this->item->attribs['show_article_options'] != '')
{
    $params->show_article_options = $this->item->attribs['show_article_options'];
}

if (isset($this->item->attribs['show_urls_images_frontend']) && $this->item->attribs['show_urls_images_frontend'] != '')
{
    $params->show_urls_images_frontend = $this->item->attribs['show_urls_images_frontend'];
}

if (isset($this->item->attribs['show_urls_images_backend']) && $this->item->attribs['show_urls_images_backend'] != '')
{
    $params->show_urls_images_backend = $this->item->attribs['show_urls_images_backend'];
}

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'article.cancel' || document.formvalidator.isValid(document.id('item-form')))
        {
            <?php echo $this->form->getField('articletext')->save(); ?>
            Joomla.submitform(task, document.getElementById('item-form'));
        }
    }
</script>

<form class="k-form-layout -koowa-form k-form-flexbox" action="<?php echo JRoute::_('index.php?option=com_content&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

    <!-- Container -->
    <div class="k-container">

        <!-- Main information -->
        <div class="k-container__main">
            <fieldset>
                <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
            </fieldset>

            <div class="k-container__flex">
                <div class="tab-container">
                    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_CONTENT_ARTICLE_CONTENT', true)); ?>
                    <?php echo $this->form->getInput('articletext'); ?>
                    <?php echo JHtml::_('bootstrap.endTab'); ?>

                    <?php // Do not show the publishing options if the edit form is configured not to. ?>
                    <?php if ($params->show_publishing_options == 1) : ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_CONTENT_FIELDSET_PUBLISHING', true)); ?>
                            <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
                            <?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                    <?php endif; ?>

                    <?php // Do not show the images and links options if the edit form is configured not to. ?>
                    <?php if ($params->show_urls_images_backend == 1) : ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('COM_CONTENT_FIELDSET_URLS_AND_IMAGES', true)); ?>
                        <div class="row-fluid form-horizontal-desktop">
                            <div class="span6">
                                <?php echo $this->form->getControlGroup('images'); ?>
                                <?php foreach ($this->form->getGroup('images') as $field) : ?>
                                    <?php echo $field->getControlGroup(); ?>
                                <?php endforeach; ?>
                            </div>
                            <div class="span6">
                                <?php foreach ($this->form->getGroup('urls') as $field) : ?>
                                    <?php echo $field->getControlGroup(); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                    <?php endif; ?>

                    <?php if ($assoc) : ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
                        <?php echo $this->loadTemplate('associations'); ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                    <?php endif; ?>

                    <?php $this->show_options = $params->show_article_options; ?>
                    <?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

                    <?php if ($this->canDo->get('core.admin')) : ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'editor', JText::_('COM_CONTENT_SLIDER_EDITOR_CONFIG', true)); ?>
                        <?php echo $this->form->renderFieldset('editorConfig'); ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                    <?php endif; ?>

                    <?php if ($this->canDo->get('core.admin')) : ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_CONTENT_FIELDSET_RULES', true)); ?>
                        <?php echo $this->form->getInput('rules'); ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                    <?php endif; ?>

                    <?php echo JHtml::_('bootstrap.endTabSet'); ?>
                </div>
            </div><!-- .k-container__flex -->

        </div><!-- .k-container__main -->

        <!-- Other information -->
        <div class="k-container__sub">
            <fieldset class="k-form-block">
                <div class="k-form-block__header"><?php echo JText::_('DETAILS'); ?></div>
                <div class="k-form-block__content">
                    <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
                </div>
            </fieldset>
        </div><!-- .k-container__sub -->

    </div><!-- .k-container -->

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
    <?php echo JHtml::_('form.token'); ?>

</form><!-- .k-form-layout -->
