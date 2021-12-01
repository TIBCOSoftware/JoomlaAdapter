<?php
/* Portions copyright © 2013, TIBCO Software Inc.
 * All rights reserved.
 */
?>
<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

require_once JPATH_BASE . "/includes/api.php";
$item = $this->item;
$api_ids = array();

if(isset($item->fields[7])) {
    $api_ids = $item->fields[7];
}
?>

<?php
    $apis = array();
    foreach($api_ids as $api_id):
        $api_a = DeveloperPortalApi::getRecordById($api_id);
//        echo "<code>API: ".var_dump($api_a)."<br/></code>";
//        echo "API Title:".$api->title."<br/>";
        $apis[] = $api_a;
    endforeach;
?>
<div id="record_atticle_tab_doc_content">
	<div class="inline-documents">
    <?php
            echo "<div class=\"inline-doc active\">";
            echo '<h2><a>'.$item->title.'</a></h2>';
            echo '<div class="inline-doc-content">'.$item->fields[117].'</div>';
            echo "</div>";
			foreach($apis as $key => $api):
                if($api->published == 1){
                    echo "<div class=\"inline-doc\">";
                    echo '<h2><a>'.$api->title.'</a></h2>';
                    $tmp = json_decode($api->fields,true);
                    echo '<div class="inline-doc-content">'.$tmp[44].'</div>';
                    echo "</div>";
                }
            endforeach
    ?>
    </div>
    <div class="download-documents">
        <h1>Downloadable Documents</h1>
        <?php
            echo "<div class=\"download-doc active\">";
            echo '<h2><a>'.$item->title.'</a></h2>';
            echo '<div>';
            $attachments = $item->fields[118];
            foreach($attachments as $k => $attach):
                $filename = $attach['title'];
                if(!isset($filename)) {
                    $filename = $attach['realname'];
                }
                $doctype = DeveloperPortalApi::getDocType($filename);
                echo '<a class="download-link" target="blank" href="uploads/apiDocumentation/'.$attach['fullpath'].'"><div><div class="doctype-'.$doctype.'"></div><span class="doc-filename">'.$filename.'</span></div></a>';
                echo "<span class=\"download-desc\">".$attach['description']."</span>";
            endforeach;
            echo "</div>";
            echo "</div>";
			foreach($apis as $key => $api):
                if($api->published == 1) {
                    echo "<div class=\"download-doc\">";
                    $tmp = json_decode($api->fields, true);
                    echo '<h2><a>' . $api->title . '</a></h2>';
                    echo "<div>";
                }
        // Attached documentation file goes here.
				$attachments = $tmp[24];
				foreach($attachments as $k => $attach):
                    $filename = $attach['title'];
                    if(!isset($filename)) {
                        $filename = $attach['realname'];
                    }
                    $doctype = DeveloperPortalApi::getDocType($filename);
					echo '<a class="download-link" target="blank" href="uploads/apiDocumentation/'.$attach['fullpath'].'"><div><div class="doctype-'.$doctype.'"></div><span class="doc-filename">'.$filename.'</span></div></a>';
                    echo "<span class=\"download-desc\">".$attach['description']."</span>";
				endforeach;
        //REST spec goes here
				// $restspec = $tmp[23];
				// foreach($restspec as $k => $attach):
                    // $filename = $attach['title'];
                    // if(!isset($filename)) {
                        // $filename = $attach['realname'];
                    // }
                    // $doctype = DeveloperPortalApi::getDocType($filename);
					// echo '<a class="download-link" target="blank" href="uploads/apiDocumentation/'.$attach['fullpath'].'"><div><div class="doctype-'.$doctype.'"></div><span class="doc-filename">'.$filename.'</span></div></a>';
                    // echo "<span class=\"download-desc\">".$attach['description']."</span>";
				// endforeach;
                echo "</div>";
                echo "</div>";
			endforeach;
        $wsdlspec = $tmp[127];
        if(!empty($wsdlspec)) {
        echo "<div class=\"download-doc\">";
        $tmp = json_decode($api->fields,true);
        echo '<h2><a>WSDL files</a></h2>';
        echo "<div>";
        //WSDL spec goes here
				foreach($wsdlspec as $k => $attach):
                    $filename = $attach['title'];
                    if(!isset($filename)) {
                        $filename = $attach['realname'];
                    }
                    $doctype = DeveloperPortalApi::getDocType($filename);
					echo '<a class="download-link" href="index.php?option=com_cobalt&task=ajaxmore.getFacadeWSDL&file_id='. $attach['id'] .'"><div><div class="doctype-'.$doctype.'"></div><span class="doc-filename">'.$filename.'</span></div></a>';
                    echo "<span class=\"download-desc\">".$attach['description']."</span>";
				endforeach;
        }
		?>
    </div>
    <div class="clearfix"></div>
    <script type="text/javascript">
        (function ($) {
            $('.inline-doc h2').click(function (e) {
                if($(this).parent().hasClass("active")) {
                    $(this).parent().removeClass("active");
                }
                else {
                    $(this).parent().addClass("active");
                }
            });
            $('.download-doc h2').click(function (e) {
                if($(this).parent().hasClass("active")) {
                    $(this).parent().removeClass("active");
                }
                else {
                    $(this).parent().addClass("active");
                }
            });
        })(jQuery);

    </script>
</div>

