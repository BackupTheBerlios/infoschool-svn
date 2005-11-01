<?php
 include 'var.php';

 $output->headline[] = 'documentation';

 $v['menu'] = get_menuitem($root,convert_menu($dok_menu));

 $content = new tmpl('index.html',$v);

 $output->out($content);
?>