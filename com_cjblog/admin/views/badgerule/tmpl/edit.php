<?php
/**
 * @package     corejoomla.administrator
 * @subpackage  com_cjblog
 *
 * @copyright   Copyright (C) 2009 - 2016 corejoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JFactory::getLanguage()->load($this->item->asset_name);

JHtml::_('behavior.formvalidator');
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

// This checks if the config options have ever been saved. If they haven't they
// will fall back to the original settings.
$params = json_decode($params);
$editoroptions = isset($params->show_publishing_options);

if (! $editoroptions)
{
	$params->show_publishing_options = '1';
}

// Check if the point uses configuration settings besides global. If so, use
// them.
if (isset($this->item->attribs['show_publishing_options']) && $this->item->attribs['show_publishing_options'] != '')
{
	$params->show_publishing_options = $this->item->attribs['show_publishing_options'];
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'point.cancel' || document.formvalidator.isValid(document.getElementById('item-form')))
		{
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_cjblog&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="item-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_CJBLOG_FIELD_DESCRIPTION_LABEL', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('description'); ?>
				</fieldset>
			</div>
			<div class="span3">
				<fieldset class="form-vertical">
					<?php foreach ($this->item->badgeRule->rules as $rule):?>
					<div class="control-group">
						<div class="control-label">
							<?php echo JText::_($rule->label, true);?>:
						</div>
						<div class="controls">
							<?php 
							switch ($rule->type)
							{
								case 'text':
									echo '<input type="text" name="'.$rule->name.'" value="'.$rule->value.'">';
									break;
									
								case 'list':
									echo '<select name="'.$rule->name.'" size="1">';
									foreach ($rule->options as $rule_option)
									{
										echo '<option value="'.$rule_option['value'].'"'.
											($rule->value == $rule_option['value'] ? ' selected="selected"' : '').'>'.((string)$rule_option).'</option>';
									}
									echo '</select>';
									
									break;
									
								case 'checkbox':
									foreach($rule->options as $rule_option)
									{
										echo '<label><input type="checkbox" name="'.$rule->name.'[]" value="'.$rule_option['value'].'"'.
											(in_array($rule_option['value'], $rule->value) ? ' checked="checked"' : '').'> '.((string)$rule_option).'</label>';
									}
									break;
							}
							?>
						</div>
					</div>
					<?php endforeach;?>
					
					<?php foreach ($this->form->getFieldset('options') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
					<?php endforeach; ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php // Do not show the publishing options if the edit form is configured not to. ?>
		<?php if ($params->show_publishing_options == 1) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_CJBLOG_FIELDSET_PUBLISHING', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
