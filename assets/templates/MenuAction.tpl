<ul class="menu vertical">
	<li <?if($content=='' || $content==array()):?>class="current"<?endif;?>><a href="{~URL:"0"~}" class="active current">Анализ сайтов</a></li>
    <li <?if(isset($content['mode']) && $content['mode']=='find'):?>class="current"<?endif;?>><a href="{~URL:"mode=find"~}">Поиск сайтов в базе</a></li>
    <li <?if(isset($content['mode']) && $content['mode']=='options'):?>class="current"<?endif;?>><a href="{~URL:"mode=options"~}">Настройки фильтрации</a></li>
</ul>
<br />