<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltfield.php';

class CFormFieldUpload extends CFormField
{

	public function __construct($field, $default)
	{
		$root      = JPath::clean(JComponentHelper::getParams('com_cobalt')->get('general_upload'));
		$url       = str_replace(JPATH_ROOT, '', $root);
		$url       = str_replace("\\", '/', $url);
		$url       = preg_replace('#^\/#iU', '', $url);
		$this->url = JURI::root(TRUE) . '/' . str_replace("//", "/", $url);

		$this->fieldname = NULL;

		parent::__construct($field, $default);

		$this->subscriptions = array();
		settype($this->value, 'array');
		if(isset($this->value['subscriptions']) && !empty($this->value['subscriptions']))
		{
			$this->subscriptions = $this->value['subscriptions'];
			unset($this->value['subscriptions']);
		}
	}

	public function onJSValidate()
	{
		$js = "";//remove jQuery('input[id^=\"" . $this->tmpname . "_tbxFile\"]').remove(); to resolve the issue json files cannot be loaded once error/warnings is displayed on page
		return $js;
	}

	protected function getFileUrl($file)
	{
		$out = $this->url . "/{$file->subfolder}/" . $file->fullpath;

		return $out;
	}

	public function getInput()
	{
		$user = JFactory::getUser();
		settype($this->value, 'array');
		$default = array();
		if(isset($this->value[0]))
		{
			if(is_array($this->value[0]))
			{
				$default = $this->value;
			}
			else
			{
				$default = $this->getFiles($this->record);
			}
		}
		$this->options['autostart']               = $this->params->get('params.autostart');
		$this->options['can_delete']               = $this->_getDeleteAccess();
		$this->tmpname = $this->options['tmpname'] = substr(md5(time() . rand(1, 1000000)), 0, 5);

		$html = JHtml::_('mrelements.mooupload', "jform[fields][{$this->id}]" . $this->fieldname, $default, $this->options, $this->id);

		if($this->params->get('params.subscription', 0) && in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels()))
		{
			$html .= JHtml::_('emerald.plans', "jform[fields][{$this->id}][subscriptions][]", $this->params->get('params.subscription', array()), $this->subscriptions, 'CRESTRICTIONPLANSDESCR');
		}

		return $html;
	}

	private function _getDeleteAccess()
	{
		$user              = JFactory::getUser();
		$author_can_delete = $this->params->get('params.delete_access', 1);
		$params            = JComponentHelper::getParams('com_cobalt');
		$type              = ItemsStore::getType($this->type_id);
		$app               = JFactory::getApplication();

		$record_id = $app->input->getInt('id', 0);

		if($author_can_delete && (!$record_id || $user->get('id') == ItemsStore::getRecord($record_id)->user_id))
		{
			return 1;
		}
		else
		{
			if($params->get('moderator') == $user->get('id'))
			{
				return 1;
			}

			if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels()))
			{
				return 1;
			}

			if(MECAccess::allowUserModerate($user, ItemsStore::getSection($app->input->getInt('section_id')), 'allow_delete'))
			{
				return 1;
			}
		}

		return 0;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$subscr = FALSE;

		if(isset($value['subscriptions']))
		{
			$subscr = $value['subscriptions'];
			unset($value['subscriptions']);
		}

		$result = $this->_getPrepared($value);

		if($subscr)
		{
			$result['subscriptions'] = $subscr;
		}

		return $result;
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		$files = $this->_getPrepared($value);

		$out = array();
		settype($files, 'array');
		foreach($files as $file)
		{
			$out[] = $file->realname;
		}

		return implode(', ', $out);
	}

	public function onStoreValues($validData, $record)
	{
		settype($this->value, 'array');
		$out = $saved = array();
		foreach($this->value as $key => $file)
		{
			if(!JString::strcmp($key, 'subscriptions'))
			{
				continue;
			}
			$out[]   = $file['realname'];
			$saved[] = $file['id'];
		}

		$files = JTable::getInstance('Files', 'CobaltTable');
		$files->markSaved($saved, $validData, $this->id);

		return $out;
	}

	protected function _getPrepared($array)
	{
		static $data = array();

		if(empty($array))
		{
			return NULL;
		}

		settype($array, 'array');

		$key = md5(implode(',', $array));

		if(isset($data[$key]))
		{
			return $data[$key];
		}

		$files      = JTable::getInstance('Files', 'CobaltTable');
		$array      = $files->prepareSave($array);

		$data[$key] = json_decode($array, TRUE);
		foreach($data[$key] AS &$file)
		{
			unset($file['params']);
		}

		return $data[$key];

	}

	public function onBeforeDownload($record, $file_index, $file_id, $return = TRUE)
	{
		$user = JFactory::getUser();
		if(!in_array($this->params->get('params.allow_download', 1), $user->getAuthorisedViewLevels()))
		{
			$this->setError(JText::_("CNORIGHTSDOWNLOAD"));

			return FALSE;
		}

		if($this->_ajast_subscr($record))
		{
			$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
			if(!JFile::exists($em_api))
			{
				return TRUE;
			}

			if(in_array($this->params->get('params.subscr_skip', 3), $user->getAuthorisedViewLevels()))
			{
				return TRUE;
			}

			if($this->params->get('params.subscr_skip_author', 1) && $record->user_id && ($record->user_id == $user->id))
			{
				return TRUE;
			}
			$section = ItemsStore::getSection($record->section_id);
			if($this->params->get('params.subscr_skip_moderator', 1) && MECAccess::allowRestricted($user, $section))
			{
				return TRUE;
			}

			include_once($em_api);

			if($this->_is_subscribed($this->_ajast_subscr($record), false))
			{
				return TRUE;
			}

			$result = JText::_($this->params->get('params.subscription_msg'));
			$result .= sprintf('<br><small><a href="%s">%s</a></small>',
				EmeraldApi::getLink('list', true, $this->_ajast_subscr($record)),
				JText::_('CSUBSCRIBENOW')
			);

			$this->setError($result);

			return FALSE;
		}

		return $return;
	}

	public function _is_subscribed($plans, $redirect)
	{
		require_once JPATH_ROOT . '/components/com_emerald/api.php';

		return EmeraldApi::hasSubscription(
			$plans,
			$this->params->get('params.subscription_msg'),
			null,
			$this->params->get('params.subscription_count'),
			$redirect);
	}

	public function _ajast_subscr($record)
	{
		if(!$record->user_id)
		{
			return;
		}

		$user = JFactory::getUser($record->user_id);

		if(in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels()) &&
			$this->params->get('params.subscription')
		)
		{
			$subscr = $this->subscriptions;
		}
		else
		{
			$subscr = $this->params->get('params.subscription');
		}

		ArrayHelper::clean_r($subscr);

		return $subscr;
	}

	public function onCopy($value, $record, $type, $section, $field)
	{
		if(!empty($value))
		{
			foreach($value AS $key => $file)
			{
				$value[$key] = $this->copyFile($file, $field);
			}
		}

		return $value;
	}

	protected function getFiles($record, $show_hits = FALSE)
	{
		$list = $this->value;

		$subfolder = $this->params->get('params.subfolder', FALSE);

		if(!$list)
		{
			return array();
		}

		if(is_string($list))
		{
			$list = json_decode($list);
		}

		$files = JTable::getInstance('Files', 'CobaltTable');

		if(!is_array(@$list[0]))
		{
			$list      = $files->getFiles($list, 'filename');
			$show_hits = FALSE;
		}

		if($show_hits)
		{
			$in = array();
			foreach($list as $attach)
			{
				settype($attach, 'array');
				$in[] = $attach['id'];
			}

			if($in)
			{
				$list = $files->getFiles($in);
			}
		}
		foreach($list as $idx => &$file)
		{
			if(is_array($file))
			{
				$file = JArrayHelper::toObject($file);
			}
			if($this->params->get('params.show_in_browser', 0) == 0)
			{
				$file->url = $this->getDownloadUrl($record, $file, $idx);
			}
			else
			{
				$file->url = JURI::root(TRUE) . '/' . JComponentHelper::getParams('com_cobalt')->get('general_upload') . '/' . $subfolder . '/' . str_replace('\\', '/', $file->fullpath);
			}
			$file->subfolder = $subfolder ? $subfolder : $file->ext;
		}

		$sort = $this->params->get('params.sort', 0);

		$parts = explode(' ', $sort);
		if(!isset($parts[0]))
		{
			$parts[0] = 0;
		}

		if(!isset($parts[1]))
		{
			$parts[1] = 'ASC';
		}
		$sortArray = array();
		switch($parts[0])
		{
			case 0:
				$title = $this->params->get('params.allow_edit_title', 0);
				foreach($list as $val)
				{
					$sortArray[] = strtolower($title && $val->title ? $val->title : $val->realname);
				}
				natcasesort($sortArray);
				array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
				break;

			case 1:
				foreach($list as $val)
				{
					$sortArray[] = $val->size;
				}
				array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
				break;

			case 2:
				foreach($list as $val)
				{
					$sortArray[] = $val->hits;
				}
				array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
				break;
			case 3:
				foreach($list as $val)
				{
					$sortArray[] = $val->id;
				}
				array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
				break;
		}


		return $list;
	}

	protected function getDownloadUrl($record, $file, $idx)
	{
		if(empty($record))
		{
			return;
		}
		$url = JURI::root(TRUE) . '/index.php?option=com_cobalt&task=files.download&tmpl=component';
		$url .= '&id=' . $file->id;
		$url .= '&fid=' . $this->id;
		$url .= '&fidx=' . $idx;
		$url .= '&rid=' . $record->id;
		$url .= '&return=' . Url::back();

		return $url;
	}

	/**
	 *
	 * @param string $filename Value from column 'filename' in table #__js_res_files
	 *
	 * @return string Filename of copied file
	 */

	protected function copyFile($filename, $field)
	{
		$params      = JComponentHelper::getParams('com_cobalt');
		$files_table = JTable::getInstance('Files', 'CobaltTable');
		if($files_table->load(array('filename' => $filename)))
		{
			$time = time();
			//$date = date('Y-m', $time);
			$date      = date($params->get('folder_format', 'Y-m'), $time);
			$ext       = strtolower(JFile::getExt($filename));
			$subfolder = $field->params->get('params.subfolder', $ext);

			$dest  = JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR;
			$index = '<html><body></body></html>';
			if(!JFolder::exists($dest))
			{
				JFolder::create($dest, 0755);
				JFile::write($dest . DIRECTORY_SEPARATOR . 'index.html', $index);
			}
			$dest .= $date . DIRECTORY_SEPARATOR;
			if(!JFolder::exists($dest))
			{

				JFolder::create($dest, 0755);
				JFile::write($dest . DIRECTORY_SEPARATOR . 'index.html', $index);
			}

			$files_table->id       = NULL;
			$parts                 = explode('_', $filename);
			$files_table->filename = $time . '_' . $parts[1];

			$copied = JFile::copy(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $files_table->fullpath, $dest . $files_table->filename);

			$files_table->fullpath = JPath::clean($date . DIRECTORY_SEPARATOR . $files_table->filename, '/');
			$files_table->saved    = 0;

			if(!$copied)
			{
				return FALSE;
			}
			if(!$files_table->store())
			{
				return FALSE;
			}

			return $files_table->filename;
		}

		return FALSE;
	}

	public function onSaveTitle()
	{
		$app = JFactory::getApplication();

		$id        = $app->input->getInt('id', 0);
		$text      = CensorHelper::cleanText($app->input->getString('text'));
		$record_id = $app->input->getInt('record_id', 0);
		$field_id  = $app->input->getInt('field_id', 0);
		if($record_id && $field_id)
		{
			$record_table = JTable::getInstance('Record', 'CobaltTable');
			$record_table->load($record_id);
			$fields = json_decode($record_table->fields, TRUE);

			if(isset($fields[$field_id]))
			{
				$files = & $fields[$field_id];
				if(isset($fields[$field_id]['files']))
				{
					$files = & $fields[$field_id]['files'];
				}

				foreach($files as &$file)
				{
					if($file['id'] == $id)
					{
						$file['title'] = $text;
						break;
					}
				}
				$record_table->fields = json_encode($fields);
				$record_table->store();
			}

		}
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__js_res_files SET title = '{$text}' WHERE id = {$id}");

		if(!$db->execute())
		{
			AjaxHelper::error('DB save error');
		}

		return $text;
	}

	public function onSaveDescr()
	{
		$app       = JFactory::getApplication();
		$id        = $app->input->getInt('id', 0);
		$text      = CensorHelper::cleanText($app->input->getString('text', $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$record_id = $app->input->getInt('record_id', 0);
		$field_id  = $app->input->getInt('field_id', 0);
		if($record_id && $field_id)
		{
			$record_table = JTable::getInstance('Record', 'CobaltTable');
			$record_table->load($record_id);
			$fields = json_decode($record_table->fields, TRUE);

			if(isset($fields[$field_id]))
			{
				$files = & $fields[$field_id];
				if(isset($fields[$field_id]['files']))
				{
					$files = & $fields[$field_id]['files'];
				}

				foreach($files as &$file)
				{
					if($file['id'] == $id)
					{
						$file['description'] = $text;
						break;
					}
				}
				$record_table->fields = json_encode($fields);
				$record_table->store();
			}

		}
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__js_res_files SET description = '{$text}' WHERE id = {$id}");

		if(!$db->execute())
		{
			AjaxHelper::error('DB save error');
		}

		return $text;
	}

	public function onImportData($row, $params)
	{
		return $row->get($params->get('field.' . $this->id . '.fname'));
	}

	public function onImport($value, $params, $record = null)
	{
		$values = explode($params->get('field.' . $this->id . '.separator', ','), $value);
		ArrayHelper::clean_r($values);

		$files = array();
		include_once JPATH_ROOT . '/components/com_cobalt/controllers/files.php';
		$controller = new CobaltControllerFiles();

		$default = $this->value;
		if(array_key_exists('files', $default))
		{
			$default = $default['files'];
		}
		settype($default, 'array');

		foreach($values AS $file)
		{

			$exists = FALSE;
			foreach($default AS $f)
			{
				if(basename($file) == $f['realname'])
				{
					$files[] = $f['filename'];
					$exists = TRUE;
				}
			}

			if($exists)
			{
				continue;
			}

			$ext        = JString::strtolower(JFile::getExt($file));
			$new_name   = JFactory::getDate($record->ctime)->toUnix() . '_' . md5($file) . '.' . $ext;

			$file = $this->_find_import_file($params->get('field.' . $this->id . '.path'), $file);
			if(!$file)
			{
				continue;
			}

			$sub_folder = $this->params->get('params.subfolder', $this->field_type);

			if(!$controller->savefile(basename($file), $new_name, $sub_folder, $file, $record->id, $record->section_id, $record->type_id, $this->id))
			{
				continue;
			}

			$files[] = $new_name;
		}


		if(empty($files))
		{
			return;
		}

		$return = $this->_getPrepared($files);

		if($this->type == 'paytodownaload' || $this->type == 'video')
		{
			$out['files'] = $return;
		}
		else
		{
			$out = $return;
		}

		return $out;
	}

	public function onImportForm($heads, $defaults)
	{
		$out = $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.fname'), 'fname');
		$out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][separator]" value="%s" class="span2" >',
			JText::_('CMULTIVALFIELDSEPARATOR'), $this->id, $defaults->get('field.' . $this->id . '.separator', ','));
		$out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][path]" value="%s" class="span12" >',
			JText::_('CFILESPATH'), $this->id, $defaults->get('field.' . $this->id . '.path', 'files'));

		return $out;
	}

	public function validate($value, $record, $type, $section)
	{
		$jform = $this->request->get('jform', array(), 'array');
		if($this->required && !isset($jform['fields'][$this->id]))
		{
			$jform['fields'][$this->id] = '';
			$this->request->set('jform', $jform);
		}
		$jform = $this->request->get('jform', array(), 'array');

		parent::validate($value, $record, $type, $section);
	}
}
