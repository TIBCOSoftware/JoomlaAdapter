<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$fid = $this->id;
$schema_id_map = array(202=>216, 203=>216);
?>

<?php echo $this->inputvalue;?>

<?php if(count($this->value)!=0){ ?>
    <input type="hidden" name="orig_content_<?php echo $fid; ?>" value="<?php echo json_decode(TibcoTibco::getFields($this->value[0])->fields,true)[$schema_id_map[$fid]]; ?>"/>
<?php  } ?>

<script>
    jQuery('[name="orig_content_<?php echo $fid; ?>"]').val(DeveloperPortal.java7DecodeURIComponent(jQuery('[name="orig_content_<?php echo $fid; ?>"]').val()));
</script>
