<?if(count($content['msg'])>0):?>
    <h5><?=$content['title']?></h5>
<ul class="alt">
    <?foreach($content['msg'] as $item):?>
        <li><?=$item;?></li>
    <?endforeach;?>
    </ul>
<?endif;?>