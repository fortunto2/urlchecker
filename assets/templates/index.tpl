<!DOCTYPE html>
<html><head>
<title>Инструментарий инфобизнесмена</title>
<meta charset="UTF-8">
<meta name="description" content="" />
<base href="{~URL:"0"~}" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script type="text/javascript" src="{~URL:"0"~}assets/js/prettify.js"></script>                                   <!-- PRETTIFY -->
<script type="text/javascript" src="{~URL:"0"~}assets/js/kickstart.js"></script>                                  <!-- KICKSTART -->
<link rel="stylesheet" type="text/css" href="{~URL:"0"~}assets/css/kickstart.css" media="all" />                  <!-- KICKSTART -->
<link rel="stylesheet" type="text/css" href="{~URL:"0"~}assets/css/style.css" media="all" />                          <!-- CUSTOM STYLES -->
</head><body><a id="top-of-page"></a><div id="wrap" class="clearfix">
<!-- ===================================== END HEADER ===================================== -->
    <div class="col_12">
        <div class="col_4">
           {BLOCK:"MenuAction"}
           {BLOCK:"DebugOut"}
           <h3>Информация</h3>
           <ul class="alt">
               <li>Всего сайтов в базе: {BLOCK:"SiteInDB"}</li>
               <li>Версия парсера: {BLOCK:"VersionScript"}</li>
               <li>Автор парсера: <a href="{BLOCK:"AuthorURL"}" target="_blank">{BLOCK:"AuthorScript"}</a></li>
               </ul>

            <h3>TODO LIST</h3>
            <ul class="alt">
               <li>Загрузка файла со списокм URL</li>
               <li>Форма для отправки списка URLов</li>
               <li>Подключить CND для кириллических доменов</li>
               <li>Ajax'овый скрипт для отправки запроса на проверку параметров</li>
               <li>Фильтрафия сайтов в базе</li>
               <li>Настройка параметров по которым будут фильтроватся сайты</li>
            </ul>

        </div>
        <div class="col_8 content">
            {BLOCK:"Pagetitle"}
            {BLOCK:"PageContent"}
        </div>

    </div>

<!-- ===================================== START FOOTER ===================================== -->


</div><!-- END WRAP -->
</body></html>
